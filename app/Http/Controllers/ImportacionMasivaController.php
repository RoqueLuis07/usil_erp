<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\Alumno;
use App\Models\AreaConocimiento;
use App\Models\Barrio;
use App\Models\Carrera;
use App\Models\Ciudad;
use App\Models\Configuracion;
use App\Models\DepartamentoParaguay;
use App\Models\Docente;
use App\Models\DocenteNivelAcademico;
use App\Models\ExtensionUniversitaria;
use App\Models\ExtensionUniversitariaDetalle;
use App\Models\Nacionalidad;
use App\Models\Sexo;
use App\Models\TipoExtensionUniversitaria;
use App\Models\User;
use App\Traits\ResuelveCarreraSemestreAlumno;
use OwenIt\Auditing\Models\Audit;

/**
 * Carga masiva de alumnos, docentes y actividades de extensión (con sus
 * participantes) desde un CSV (Excel lo guarda como
 * "CSV UTF-8"). Primero se puede "Validar" (no guarda nada) y después
 * "Importar". Las filas con error se informan con su número y motivo y no
 * frenan al resto.
 */
class ImportacionMasivaController extends Controller
{
    use ResuelveCarreraSemestreAlumno;

    private const MAX_FILAS = 2000;

    private const COLUMNAS_ALUMNO = [
        'documento', 'nombres', 'apellidos',
        'sexo', 'fecha_nacimiento', 'nacionalidad', 'telefono', 'celular', 'correo', 'direccion',
        'departamento', 'ciudad', 'barrio', 'facultad', 'carrera', 'anho_ingreso', 'semestre_ingreso',
        'crear_usuario',
    ];

    private const COLUMNAS_DOCENTE = [
        'documento', 'nombres', 'apellidos',
        'sexo', 'fecha_nacimiento', 'nacionalidad', 'telefono', 'celular', 'correo', 'direccion',
        'departamento', 'ciudad', 'barrio', 'nivel_academico', 'area_conocimiento', 'capacitacion_didactica',
        'tutor', 'facultad', 'carreras',
    ];

    /** Una fila = un alumno participando de una actividad; las filas de la misma actividad (nombre + fecha) la arman. */
    private const COLUMNAS_EXTENSION = [
        'actividad', 'fecha', 'horas', 'documento', 'carrera',
        'tipo_actividad', 'responsable', 'presupuesto', 'beneficiados', 'estado',
    ];

    private const TIPO_POR_DEFECTO = 'Extensión académica';

    private array $cache = [];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        abort_unless(Auth::user()->can('crear_alumnos') || Auth::user()->can('crear_docentes') || Auth::user()->can('crear_extensiones_universitarias'), 403);

        return view('importaciones.index', [
            'carreras' => Carrera::with('Facultad')->where('estado', 'AC')->orderBy('nombre_fantasia')->get(),
            'departamentos' => DepartamentoParaguay::orderBy('nombre')->get(),
            'niveles' => DocenteNivelAcademico::where('estado', 'AC')->orderBy('nombre')->get(),
            'nacionalidades' => Nacionalidad::orderBy('nombre')->get(),
            'tipos' => TipoExtensionUniversitaria::where('estado', 'AC')->orderBy('nombre')->get(),
            'resultado' => session('resultado'),
            'reversion' => session('reversion'),
            'lotes' => Auth::user()->can('revertir_importaciones') ? $this->lotesImportacion() : [],
            'usuariosLotes' => User::pluck('name', 'id'),
        ]);
    }

    public function plantilla($tipo)
    {
        $this->autorizarTipo($tipo);

        $columnas = $this->columnasDe($tipo);
        $ejemplo = $tipo === 'extensiones'
            ? ['Minga ambiental en la costanera', '15/06/2025', '5', '1234567', 'Ingenieria en Informatica', 'Voluntariado', '', '350000', '40', '']
            : ($tipo === 'alumnos'
            ? ['1234567', 'Maria', 'Gonzalez Benitez', 'Femenino', '15/03/2004', 'Paraguaya', '', '0981123456', 'maria@correo.com', 'Calle 1 y Av. 2', 'Central', 'Luque', 'Centro', 'Facultad de Ciencias', 'Ingenieria en Informatica', '2023', '1', 'SI']
            : ['7654321', 'Juan', 'Perez Lopez', 'Masculino', '02/11/1985', 'Paraguaya', '', '0971654321', 'juan@correo.com', 'Calle 3', 'Central', 'San Lorenzo', '', 'Maestria', 'Tecnologia e Ingenieria', 'SI', 'NO', 'Facultad de Ciencias', 'Ingenieria en Informatica|Otra Carrera']);

        $salida = "\xEF\xBB\xBF" . implode(';', $columnas) . "\r\n" . implode(';', array_map(fn ($v) => '"' . str_replace('"', '""', $v) . '"', $ejemplo)) . "\r\n";

        return response($salida, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="plantilla_' . $tipo . '.csv"',
        ]);
    }

    public function procesar(Request $request, $tipo)
    {
        set_time_limit(0); // cada alta genera un usuario con contraseña cifrada: 1.000 filas tardan más de 30 s
        $this->autorizarTipo($tipo);

        $request->validate([
            'archivo' => ['required', 'file', 'max:5120', 'mimes:csv,txt,xlsx'],
            'parte' => ['nullable', 'integer', 'min:1', 'max:50'],
            'modo' => ['required', 'in:validar,importar'],
        ], [
            'archivo.required' => 'Elegí un archivo CSV.',
            'archivo.mimes' => 'El archivo tiene que ser Excel (.xlsx) o CSV (en Excel: Guardar como > CSV UTF-8).',
        ]);

        $persistir = $request->modo === 'importar';
        $columnasEsperadas = $this->columnasDe($tipo);

        try {
            $archivo = $request->file('archivo');
            [$cabecera, $filas] = Str::lower($archivo->getClientOriginalExtension()) === 'xlsx'
                ? $this->leerXlsx($archivo, $tipo, (int) ($request->parte ?: 1))
                : $this->leerCsv($archivo);
        } catch (\RuntimeException $e) {
            return back()->with('error-message', $e->getMessage());
        }

        $faltantes = array_diff($tipo === 'extensiones' ? ['actividad', 'fecha', 'horas', 'documento'] : ['documento', 'nombres', 'apellidos'], $cabecera);
        if ($faltantes) {
            return back()->with('error-message', 'Al archivo le faltan columnas obligatorias: ' . implode(', ', $faltantes) . '. Descargá la plantilla y respetá los títulos.');
        }
        $desconocidas = array_diff($cabecera, $columnasEsperadas);

        $resultado = ['tipo' => $tipo, 'modo' => $request->modo, 'total' => count($filas), 'creados' => 0, 'actualizados' => 0, 'errores' => [], 'ignoradas' => $desconocidas];

        DB::beginTransaction();
        try {
            foreach ($filas as $indice => $fila) {
                $numeroFila = $indice + 2; // 1 = cabecera
                $datos = array_combine($cabecera, array_pad(array_slice($fila, 0, count($cabecera)), count($cabecera), ''));
                $datos = array_map(fn ($v) => trim((string) $v), $datos);

                DB::beginTransaction();
                try {
                    $accion = match ($tipo) {
                        'alumnos' => $this->procesarAlumno($datos),
                        'docentes' => $this->procesarDocente($datos),
                        'extensiones' => $this->procesarExtension($datos),
                    };
                    DB::commit();
                    $accion === 'creado' ? $resultado['creados']++ : $resultado['actualizados']++;
                } catch (\InvalidArgumentException $e) {
                    DB::rollBack();
                    $resultado['errores'][] = ['fila' => $numeroFila, 'documento' => $datos['documento'] ?? '', 'mensaje' => $e->getMessage()];
                } catch (\Exception $e) {
                    DB::rollBack();
                    $resultado['errores'][] = ['fila' => $numeroFila, 'documento' => $datos['documento'] ?? '', 'mensaje' => 'Error inesperado: ' . $e->getMessage()];
                }
            }

            $persistir ? DB::commit() : DB::rollBack();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error-message', $e->getMessage());
        }

        return redirect()->route('importaciones.index')->with('resultado', $resultado);
    }

    // -------------------------------------------------------- historial y reversión

    /**
     * Cada importación deja su rastro en la auditoría (alta y cambio de cada registro, con la URL de la
     * carga). Un "lote" es una tanda continua de esos registros del mismo usuario y tipo. Las validaciones
     * ("Solo validar") no aparecen porque se deshacen y no auditan nada.
     */
    private function lotesImportacion(): array
    {
        $filas = Audit::query()
            ->where('url', 'like', '%/importaciones/%')
            ->where('url', 'not like', '%/importaciones/revertir%')
            ->whereIn('event', ['created', 'updated'])
            ->where(function ($consulta) {
                $consulta->whereNull('tags')->orWhere('tags', '!=', 'revertido');
            })
            ->orderBy('id')
            ->get(['id', 'user_id', 'url', 'event', 'auditable_type', 'created_at']);

        $lotes = [];
        $actual = null;
        foreach ($filas as $fila) {
            if (!preg_match('#/importaciones/(alumnos|docentes|extensiones)#', $fila->url, $m)) {
                continue;
            }
            $tipo = $m[1];
            $nuevo = !$actual
                || $actual['user_id'] !== $fila->user_id
                || $actual['tipo'] !== $tipo
                || $fila->created_at->diffInMinutes($actual['fin']) > 20;
            if ($nuevo) {
                if ($actual) {
                    $lotes[] = $actual;
                }
                $actual = ['desde' => $fila->id, 'hasta' => $fila->id, 'user_id' => $fila->user_id, 'tipo' => $tipo,
                    'inicio' => $fila->created_at, 'fin' => $fila->created_at, 'creados' => 0, 'actualizados' => 0, 'modelos' => []];
            }
            $actual['hasta'] = $fila->id;
            $actual['fin'] = $fila->created_at;
            $fila->event === 'created' ? $actual['creados']++ : $actual['actualizados']++;
            $modelo = class_basename($fila->auditable_type);
            $actual['modelos'][$modelo][$fila->event] = ($actual['modelos'][$modelo][$fila->event] ?? 0) + 1;
        }
        if ($actual) {
            $lotes[] = $actual;
        }

        return array_reverse($lotes); // los más recientes primero
    }

    /**
     * Deshace un lote: lo que la importación creó se elimina y lo que modificó vuelve a su valor anterior
     * (de más nuevo a más viejo, para respetar las dependencias). "Simular" hace lo mismo y lo cancela.
     * Un registro que otros datos posteriores ya usan no se puede eliminar: queda y se informa.
     */
    public function revertir(Request $request)
    {
        abort_unless(Auth::user()->can('revertir_importaciones'), 403);
        set_time_limit(0);

        $request->validate([
            'desde' => ['required', 'integer'],
            'hasta' => ['required', 'integer'],
            'modo' => ['required', 'in:simular,revertir'],
        ]);

        $lote = collect($this->lotesImportacion())->first(fn ($l) => $l['desde'] == $request->desde && $l['hasta'] == $request->hasta);
        if (!$lote) {
            return back()->with('error-message', 'Ese lote ya no existe (puede que ya se haya revertido).');
        }

        $auditorias = Audit::where('user_id', $lote['user_id'])
            ->where('url', 'like', '%/importaciones/' . $lote['tipo'] . '%')
            ->whereBetween('id', [$lote['desde'], $lote['hasta']])
            ->whereIn('event', ['created', 'updated'])
            ->orderByDesc('id')
            ->get();

        $resultado = ['modo' => $request->modo, 'tipo' => $lote['tipo'], 'eliminados' => 0, 'restaurados' => 0, 'ya_no_existian' => 0, 'no_revertidos' => []];
        $procesadas = [];

        DB::beginTransaction();
        try {
            foreach ($auditorias as $auditoria) {
                DB::beginTransaction();
                try {
                    $clase = $auditoria->auditable_type;
                    $modelo = class_exists($clase) ? $clase::find($auditoria->auditable_id) : null;
                    if (!$modelo) {
                        $resultado['ya_no_existian']++;
                    } elseif ($auditoria->event === 'created') {
                        $this->eliminarCreado($modelo);
                        $resultado['eliminados']++;
                    } else {
                        $antes = $auditoria->old_values ?? [];
                        unset($antes['id'], $antes['created_at'], $antes['updated_at']);
                        if ($antes) {
                            $modelo->forceFill($antes)->save();
                        }
                        $resultado['restaurados']++;
                    }
                    DB::commit();
                    $procesadas[] = $auditoria->id;
                } catch (\Exception $e) {
                    DB::rollBack();
                    $resultado['no_revertidos'][] = [
                        'registro' => class_basename($auditoria->auditable_type) . ' #' . $auditoria->auditable_id,
                        'motivo' => 'Tiene datos posteriores que lo usan o no se pudo deshacer.',
                    ];
                }
            }
            if ($request->modo === 'revertir') {
                // El rastro de la carga no se borra: se marca para que el lote deje de aparecer en la lista.
                if ($procesadas) {
                    Audit::whereIn('id', $procesadas)->update(['tags' => 'revertido']);
                }
                DB::commit();
            } else {
                DB::rollBack();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error-message', $e->getMessage());
        }

        return redirect()->route('importaciones.index')->with('reversion', $resultado);
    }

    /** Elimina un registro creado por una importación, limpiando antes lo que cuelga de él sin modelo propio. */
    private function eliminarCreado($modelo): void
    {
        if ($modelo instanceof User) {
            DB::table('configuraciones')->where('user_id', $modelo->id)->delete();
            $modelo->syncRoles([]);
        }
        if ($modelo instanceof Alumno || $modelo instanceof Docente) {
            $modelo->nacionalidades()->detach();
        }
        if ($modelo instanceof Docente) {
            $modelo->Carreras()->detach();
        }
        $modelo->delete();
    }

    // ------------------------------------------------------------------ alumnos

    private function procesarAlumno(array $d): string
    {
        $this->exigir($d, ['documento', 'nombres', 'apellidos']);
        $documento = removeAccents(Str::upper($d['documento']));

        $carrera = $this->resolverCarrera($d['carrera'] ?? '', $d['facultad'] ?? '');
        if (!$carrera) {
            throw new \InvalidArgumentException('Falta la carrera (obligatoria para participar de extensión).');
        }
        $anho = $this->resolverAnhoIngreso($d['anho_ingreso'] ?? '');
        if (!$anho) {
            throw new \InvalidArgumentException('Falta el año de ingreso (obligatorio para calcular el semestre).');
        }
        $semestreIngreso = ($d['semestre_ingreso'] ?? '') === '2' ? 2 : 1;

        $alumno = Alumno::where('numero_documento', $documento)->first();
        $conciliado = false;
        if (!$alumno) {
            // Los alumnos que vinieron de la planilla vieja tienen un documento provisorio (EXT-IMP-n):
            // si el nombre coincide con uno solo, se completa con la cédula real en vez de duplicarlo.
            $alumno = $this->buscarImportadoSinCedula($d);
            $conciliado = (bool) $alumno;
        }

        if ($alumno) {
            if ($conciliado) {
                $previo = ['celular' => $alumno->celular, 'email' => $alumno->email_personal, 'direccion' => $alumno->direccion];
                $this->completarPersona($alumno, $d, $documento);
                $alumno->celular = ($d['celular'] ?? '') !== '' ? $alumno->celular : $previo['celular'];
                $alumno->email_personal = ($d['correo'] ?? '') !== '' ? $alumno->email_personal : $previo['email'];
                $alumno->direccion = ($d['direccion'] ?? '') !== '' ? $alumno->direccion : $previo['direccion'];
            }
            $alumno->carrera_id = $carrera->id;
            $alumno->anho_ingreso = $anho;
            $alumno->semestre_ingreso = $semestreIngreso;
            $alumno->actualizado_por_id = Auth::id();
            $alumno->save();
            return 'actualizado';
        }

        $alumno = new Alumno();
        $this->completarPersona($alumno, $d, $documento);
        $alumno->carrera_id = $carrera->id;
        $alumno->anho_ingreso = $anho;
        $alumno->semestre_ingreso = $semestreIngreso;
        $alumno->cargado_por_id = Auth::id();
        // crear_usuario = NO: el alumno queda cargado (puede participar y figurar en reportes) pero sin cuenta de acceso.
        if (($d['crear_usuario'] ?? '') === '' || $this->esSi($d['crear_usuario'])) {
            $alumno->usuario_id = $this->crearUsuario($d, $documento, 'ALUMNO', 'alumnos.local')->id;
        }
        $alumno->save();
        $this->sincronizarNacionalidades($alumno, $d['nacionalidad'] ?? '');

        return 'creado';
    }

    /** Alumno con documento provisorio (EXT-IMP-n) cuyo nombre y apellido coinciden con la fila, si es uno solo. */
    private function buscarImportadoSinCedula(array $d): ?Alumno
    {
        if (!isset($this->cache['provisorios'])) {
            $this->cache['provisorios'] = Alumno::where('numero_documento', 'like', 'EXT-IMP-%')->get();
        }
        $nombres = $this->normalizar($d['nombres']);
        $apellidos = $this->normalizar($d['apellidos']);
        $coinciden = $this->cache['provisorios']->filter(function ($a) use ($nombres, $apellidos) {
            return $this->normalizar($a->primer_nombre) === $nombres && $this->normalizar($a->primer_apellido) === $apellidos;
        });
        if ($coinciden->count() !== 1) {
            return null;
        }
        $alumno = $coinciden->first();
        $this->cache['provisorios'] = $this->cache['provisorios']->reject(fn ($a) => $a->id === $alumno->id);
        return $alumno;
    }

    // ------------------------------------------------------------- actividades

    /**
     * Una fila = un alumno en una actividad. La actividad se identifica por nombre + fecha:
     * si ya existe se reutiliza (no se duplica) y se le suma o actualiza el participante.
     */
    private function procesarExtension(array $d): string
    {
        $this->exigir($d, ['actividad', 'fecha', 'horas', 'documento']);

        $nombre = mb_substr(removeAccents(Str::upper(trim(preg_replace('/\s+/', ' ', $d['actividad'])))), 0, 255);
        $fecha = $this->resolverFechaActividad($d['fecha']);
        $horas = $this->resolverNumero($d['horas'], 'horas', 0.01);

        $documento = removeAccents(Str::upper($d['documento']));
        $alumno = Alumno::where('numero_documento', $documento)->first();
        if (!$alumno) {
            throw new \InvalidArgumentException('No existe el alumno con documento ' . $documento . '. Cargalo primero en Alumnos.');
        }

        $carrera = ($d['carrera'] ?? '') !== '' ? $this->resolverCarrera($d['carrera'], '') : null;

        $extension = $this->buscarActividad($nombre, $fecha);
        if (!$extension) {
            $tipo = TipoExtensionUniversitaria::find($this->buscarPorNombre('tipos_extension', TipoExtensionUniversitaria::where('estado', 'AC')->get(), ($d['tipo_actividad'] ?? '') ?: self::TIPO_POR_DEFECTO, 'Tipo de actividad'));

            $estado = Str::upper($d['estado'] ?? '');
            if ($estado === '') {
                $estado = $fecha <= now()->toDateString() ? 'FI' : 'AP';
            }
            if (!in_array($estado, ['FI', 'AP', 'PE', 'IN', 'CO'], true)) {
                throw new \InvalidArgumentException('El estado "' . $estado . '" no es válido (FI, AP, PE, IN o CO).');
            }

            $extension = new ExtensionUniversitaria();
            $extension->nombre = $nombre;
            $extension->tipo_extension_id = $tipo->id;
            $extension->problematica = $tipo->linea_numero ?: null;
            $extension->cantidad_horas = $horas;
            if (($d['responsable'] ?? '') !== '') {
                $docente = \App\Models\Docente::where('numero_documento', removeAccents(Str::upper($d['responsable'])))->first();
                if (!$docente) {
                    throw new \InvalidArgumentException('No existe el docente responsable con documento ' . $d['responsable'] . '.');
                }
                $extension->docente_id = $docente->id;
            }
            if (($d['presupuesto'] ?? '') !== '') {
                $extension->presupuesto = $this->resolverNumero($d['presupuesto'], 'presupuesto', 0);
            }
            if (($d['beneficiados'] ?? '') !== '') {
                $extension->cantidad_beneficiados = (int) $this->resolverNumero($d['beneficiados'], 'beneficiados', 0);
            }
            $extension->fecha_inicio = $fecha;
            $extension->fecha_fin = $fecha;
            $extension->ubicacion_proyecto = 'importado-sin-archivo';
            $extension->extension_proyecto = '';
            $extension->tiene_certificado = false;
            $extension->estado = $estado;
            $extension->cargado_por_id = Auth::id();
            $extension->save();
        } elseif ($horas > (float) $extension->cantidad_horas) {
            // Cada alumno puede tener horas distintas: la actividad guarda la mayor.
            $extension->cantidad_horas = $horas;
            $extension->actualizado_por_id = Auth::id();
            $extension->save();
        }

        $cs = $this->resolverCarreraSemestreAlumno($alumno->id, $extension->fecha_inicio);

        $detalle = ExtensionUniversitariaDetalle::where('extension_universitaria_id', $extension->id)->where('alumno_id', $alumno->id)->first();
        $existia = (bool) $detalle;
        if (!$detalle) {
            $detalle = new ExtensionUniversitariaDetalle();
            $detalle->extension_universitaria_id = $extension->id;
            $detalle->alumno_id = $alumno->id;
        }
        $detalle->carrera_id = optional($carrera)->id ?: $cs['carrera_id'];
        $detalle->semestre_id = $cs['semestre_id'];
        $detalle->estado = 'AC';
        $detalle->cantidad_horas = $horas;
        $detalle->revisado_por_id = Auth::id();
        $detalle->fecha_revision = now();
        $detalle->save();

        return $existia ? 'actualizado' : 'creado';
    }

    /** Actividad con la misma fecha y el mismo nombre (sin tildes, signos ni diferencias de espacios). */
    private function buscarActividad(string $nombre, string $fecha): ?ExtensionUniversitaria
    {
        $clave = fn (string $t) => preg_replace('/[^A-Z0-9]/', '', removeAccents(Str::upper($t)));
        $buscada = $clave($nombre);

        return ExtensionUniversitaria::whereDate('fecha_inicio', $fecha)->get()->first(fn ($e) => $clave($e->nombre) === $buscada);
    }

    private function resolverFechaActividad(string $valor): string
    {
        foreach (['d/m/Y', 'd-m-Y', 'Y-m-d', 'Y/m/d'] as $formato) {
            try {
                $fecha = Carbon::createFromFormat('!' . $formato, $valor);
                if ($fecha && $fecha->format($formato) === $valor && $fecha->year >= 2000 && $fecha->lte(now()->addYears(2))) {
                    return $fecha->format('Y-m-d');
                }
            } catch (\Exception $e) {
                // probar el siguiente formato
            }
        }
        throw new \InvalidArgumentException('La fecha "' . $valor . '" no es válida (usá dd/mm/aaaa, entre 2000 y dentro de 2 años).');
    }

    private function resolverNumero(string $valor, string $etiqueta, float $minimo): float
    {
        $limpio = trim($valor);
        if (str_contains($limpio, ',')) {
            // formato "1.234,56": los puntos son miles y la coma es el decimal
            $limpio = str_replace(',', '.', str_replace('.', '', $limpio));
        }
        if (!is_numeric($limpio) || (float) $limpio < $minimo) {
            throw new \InvalidArgumentException('El valor de ' . $etiqueta . ' ("' . $valor . '") no es válido.');
        }
        return (float) $limpio;
    }

    // ----------------------------------------------------------------- docentes

    private function procesarDocente(array $d): string
    {
        $this->exigir($d, ['documento', 'nombres', 'apellidos']);
        $documento = removeAccents(Str::upper($d['documento']));

        $carreras = [];
        foreach (preg_split('/[|]/', $d['carreras'] ?? '') as $nombre) {
            if (trim($nombre) === '') {
                continue;
            }
            $carrera = $this->resolverCarrera($nombre, $d['facultad'] ?? '');
            if (!$carrera) {
                throw new \InvalidArgumentException('La carrera "' . trim($nombre) . '" no existe (o es ambigua; indicá la facultad).');
            }
            $carreras[] = $carrera->id;
        }

        $docente = Docente::where('numero_documento', $documento)->first();
        $existia = (bool) $docente;
        if (!$docente) {
            $docente = new Docente();
            $this->completarPersona($docente, $d, $documento);
            $docente->cargado_por_id = Auth::id();
            $docente->usuario_id = $this->crearUsuario($d, $documento, 'ENCARGADO_DOCENTE', 'docentes.local')->id;
        } else {
            $docente->actualizado_por_id = Auth::id();
        }

        if (($d['nivel_academico'] ?? '') !== '') {
            $docente->nivel_academico_id = $this->buscarPorNombre('niveles', DocenteNivelAcademico::where('estado', 'AC')->get(), $d['nivel_academico'], 'Nivel académico');
        }
        if (($d['area_conocimiento'] ?? '') !== '') {
            $docente->area_conocimiento_id = $this->buscarPorNombre('areas', AreaConocimiento::get(), $d['area_conocimiento'], 'Área de conocimiento');
        }
        if (($d['capacitacion_didactica'] ?? '') !== '') {
            $docente->capacitacion_didactica = $this->esSi($d['capacitacion_didactica']);
        }
        if (($d['tutor'] ?? '') !== '') {
            $docente->tutor_tesis = $this->esSi($d['tutor']);
        }
        $docente->save();

        if (!$existia) {
            $this->sincronizarNacionalidades($docente, $d['nacionalidad'] ?? '');
        }
        if ($carreras) {
            $docente->Carreras()->syncWithoutDetaching($carreras);
        }

        return $existia ? 'actualizado' : 'creado';
    }

    // ------------------------------------------------------------------ comunes

    /** Datos personales comunes de Alumno y Docente (mismas columnas). */
    private function completarPersona($persona, array $d, string $documento): void
    {
        $persona->primer_nombre = removeAccents(Str::upper(trim($d['nombres'])));
        $persona->segundo_nombre = null;
        $persona->tercer_nombre = null;
        $persona->primer_apellido = removeAccents(Str::upper(trim($d['apellidos'])));
        $persona->segundo_apellido = null;
        $persona->numero_documento = $documento;
        $persona->telefono = ($d['telefono'] ?? '') ?: null;
        $persona->celular = $d['celular'] ?? '';
        $persona->email_personal = removeAccents(Str::lower($d['correo'] ?? ''));
        $persona->direccion = removeAccents(Str::upper($d['direccion'] ?? '')) ?: null;

        if (($d['sexo'] ?? '') !== '') {
            $persona->sexo_id = $this->resolverSexo($d['sexo']);
        }
        if (($d['fecha_nacimiento'] ?? '') !== '') {
            $persona->fecha_nacimiento = $this->resolverFecha($d['fecha_nacimiento']);
        }

        if (($d['departamento'] ?? '') !== '') {
            $departamento = $this->buscarPorNombre('departamentos', DepartamentoParaguay::get(), $d['departamento'], 'Departamento', true);
            $persona->departamento_id = $departamento;
            if (($d['ciudad'] ?? '') !== '') {
                $ciudad = $this->buscarOCrearCiudad($departamento, $d['ciudad']);
                $persona->ciudad_id = $ciudad->id;
                if (($d['barrio'] ?? '') !== '') {
                    $persona->barrio_id = $this->buscarOCrearBarrio($ciudad->id, $d['barrio'])->id;
                }
            }
        } elseif (($d['ciudad'] ?? '') !== '') {
            throw new \InvalidArgumentException('Si indicás la ciudad tenés que indicar también el departamento.');
        }
    }

    private function crearUsuario(array $d, string $documento, string $rol, string $dominioPorDefecto): User
    {
        $correo = removeAccents(Str::lower(($d['correo'] ?? '') ?: (Str::lower($documento) . '@' . $dominioPorDefecto)));
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('El correo "' . $correo . '" no es válido.');
        }
        if (User::where('email', $correo)->exists()) {
            throw new \InvalidArgumentException('El correo ' . $correo . ' ya lo usa otro usuario.');
        }

        $usuario = new User();
        $usuario->name = removeAccents(Str::upper(trim($d['nombres']))) . ' ' . removeAccents(Str::upper(trim($d['apellidos'])));
        $usuario->email = $correo;
        $usuario->password = Hash::make($documento . '-' . Str::substr(removeAccents(Str::upper(trim($d['nombres']))), 0, 1) . Str::substr(removeAccents(Str::lower(trim($d['apellidos']))), 0, 1));
        $usuario->avatar = 'no_image.jpg';
        $usuario->portada = 'no_portada.jpg';
        $rolModelo = \Spatie\Permission\Models\Role::where('name', $rol)->where('guard_name', 'web')->first();
        $usuario->role_id = optional($rolModelo)->id;
        $usuario->save();
        if ($rolModelo) {
            $usuario->assignRole($rolModelo);
        }

        $configuracion = new Configuracion();
        $configuracion->user_id = $usuario->id;
        $configuracion->lang = 'sp';
        $configuracion->data_layout = 'vertical';
        $configuracion->data_sidebar = 'dark';
        $configuracion->data_sidebar_size = 'lg';
        $configuracion->card_layout = null;
        $configuracion->data_bs_theme = 'light';
        $configuracion->data_layout_width = 'fluid';
        $configuracion->data_sidebar_image = 'none';
        $configuracion->data_layout_position = 'fixed';
        $configuracion->data_layout_style = 'default';
        $configuracion->data_topbar = 'warning';
        $configuracion->data_preloader = 'disable';
        $configuracion->save();

        return $usuario;
    }

    private function sincronizarNacionalidades($persona, string $texto): void
    {
        $ids = [];
        foreach (preg_split('/[|]/', $texto) as $nombre) {
            if (trim($nombre) !== '') {
                $ids[] = $this->buscarPorNombre('nacionalidades', Nacionalidad::get(), $nombre, 'Nacionalidad');
            }
        }
        if ($ids) {
            $persona->nacionalidades()->sync($ids);
        }
    }

    private function exigir(array $d, array $campos): void
    {
        foreach ($campos as $campo) {
            if (($d[$campo] ?? '') === '') {
                throw new \InvalidArgumentException('Falta el dato obligatorio "' . $campo . '".');
            }
        }
    }

    private function normalizar(string $texto): string
    {
        return trim(preg_replace('/\s+/', ' ', removeAccents(Str::upper($texto))));
    }

    /** Busca por nombre normalizado en un catálogo (cacheado); devuelve el id. */
    private function buscarPorNombre(string $clave, $coleccion, string $valor, string $etiqueta, bool $soloId = false): int
    {
        if (!isset($this->cache[$clave])) {
            $this->cache[$clave] = $coleccion->mapWithKeys(fn ($m) => [$this->normalizar($m->nombre) => $m->id])->all();
        }
        $id = $this->cache[$clave][$this->normalizar($valor)] ?? null;
        if (!$id) {
            $validos = implode(', ', array_slice(array_keys($this->cache[$clave]), 0, 8));
            throw new \InvalidArgumentException($etiqueta . ' "' . $valor . '" no existe en el catálogo (ej.: ' . $validos . ').');
        }
        return $id;
    }

    private function resolverSexo(string $valor): int
    {
        $mapa = ['M' => 'MASCULINO', 'H' => 'MASCULINO', 'HOMBRE' => 'MASCULINO', 'F' => 'FEMENINO', 'MUJER' => 'FEMENINO'];
        $normal = $this->normalizar($valor);
        return $this->buscarPorNombre('sexos', Sexo::get(), $mapa[$normal] ?? $normal, 'Sexo');
    }

    private function resolverFecha(string $valor): string
    {
        foreach (['d/m/Y', 'd-m-Y', 'Y-m-d', 'Y/m/d'] as $formato) {
            try {
                $fecha = Carbon::createFromFormat('!' . $formato, $valor);
                if ($fecha && $fecha->format($formato) === $valor && $fecha->year > 1900 && $fecha->lte(now())) {
                    return $fecha->format('Y-m-d');
                }
            } catch (\Exception $e) {
                // probar el siguiente formato
            }
        }
        throw new \InvalidArgumentException('La fecha "' . $valor . '" no es válida (usá dd/mm/aaaa).');
    }

    private function resolverAnhoIngreso(string $valor): ?int
    {
        if ($valor === '') {
            return null;
        }
        if (!ctype_digit($valor) || (int) $valor < 1990 || (int) $valor > now()->year) {
            throw new \InvalidArgumentException('El año de ingreso "' . $valor . '" no es válido.');
        }
        return (int) $valor;
    }

    private function resolverCarrera(string $nombre, string $facultad): ?Carrera
    {
        if ($nombre === '') {
            return null;
        }
        if (!isset($this->cache['carreras'])) {
            $this->cache['carreras'] = Carrera::with('Facultad')->where('estado', 'AC')->get();
        }
        $buscado = $this->normalizar($nombre);
        $candidatas = $this->cache['carreras']->filter(function ($c) use ($buscado) {
            return in_array($buscado, [$this->normalizar($c->nombre_fantasia), $this->normalizar($c->nombre_real), $this->normalizar($c->abreviatura)], true);
        });
        if ($facultad !== '') {
            $candidatas = $candidatas->filter(fn ($c) => $this->normalizar(optional($c->Facultad)->nombre ?? '') === $this->normalizar($facultad));
        }
        if ($candidatas->count() > 1) {
            throw new \InvalidArgumentException('La carrera "' . $nombre . '" existe en más de una facultad; indicá la facultad.');
        }
        if ($candidatas->isEmpty()) {
            throw new \InvalidArgumentException('La carrera "' . $nombre . '" no existe. Cargala primero en Parámetros > Facultades y Carreras.');
        }
        return $candidatas->first();
    }

    private function buscarOCrearCiudad(int $departamentoId, string $nombre): Ciudad
    {
        $normal = $this->normalizar($nombre);
        $ciudad = Ciudad::where('departamento_id', $departamentoId)->get()->first(fn ($c) => $this->normalizar($c->nombre) === $normal);
        if (!$ciudad) {
            $ciudad = new Ciudad();
            $ciudad->nombre = $normal;
            $ciudad->departamento_id = $departamentoId;
            $ciudad->save();
        }
        return $ciudad;
    }

    private function buscarOCrearBarrio(int $ciudadId, string $nombre): Barrio
    {
        $normal = $this->normalizar($nombre);
        $barrio = Barrio::where('ciudad_id', $ciudadId)->get()->first(fn ($b) => $this->normalizar($b->nombre) === $normal);
        if (!$barrio) {
            $barrio = new Barrio();
            $barrio->nombre = $normal;
            $barrio->ciudad_id = $ciudadId;
            $barrio->save();
        }
        return $barrio;
    }

    private function esSi(string $valor): bool
    {
        return in_array($this->normalizar($valor), ['SI', 'S', '1', 'TRUE', 'VERDADERO', 'YES'], true);
    }

    // -------------------------------------------------------------------- Excel (.xlsx)

    /**
     * Lee una hoja de un .xlsx sin librerías externas (un .xlsx es un zip de XML). Se busca la hoja
     * "Alumnos N", "Docentes N" o "Actividades N" (N = parte); si el libro tiene una sola hoja se usa esa.
     * Las fórmulas se leen por el valor que Excel dejó guardado: hay que abrir y guardar el archivo en Excel.
     */
    private function leerXlsx(UploadedFile $archivo, string $tipo, int $parte): array
    {
        $zip = new \ZipArchive();
        if ($zip->open($archivo->getRealPath()) !== true) {
            throw new \RuntimeException('No se pudo abrir el Excel (¿está dañado o es un .xls antiguo? Guardalo como .xlsx).');
        }

        $nsRel = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';
        $libro = simplexml_load_string((string) $zip->getFromName('xl/workbook.xml'));
        $rels = simplexml_load_string((string) $zip->getFromName('xl/_rels/workbook.xml.rels'));
        if (!$libro || !$rels) {
            throw new \RuntimeException('El Excel no tiene el formato esperado.');
        }

        $destinos = [];
        foreach ($rels->Relationship as $rel) {
            $destinos[(string) $rel['Id']] = (string) $rel['Target'];
        }
        $hojas = [];
        foreach ($libro->sheets->sheet as $hoja) {
            $destino = $destinos[(string) $hoja->attributes($nsRel)['id']] ?? null;
            if ($destino) {
                $hojas[(string) $hoja['name']] = str_starts_with($destino, '/') ? ltrim($destino, '/') : 'xl/' . $destino;
            }
        }
        $ruta = $this->elegirHojaXlsx($hojas, $tipo, $parte);

        $compartidas = [];
        $xmlCompartidas = $zip->getFromName('xl/sharedStrings.xml');
        if ($xmlCompartidas !== false) {
            $ss = simplexml_load_string($xmlCompartidas);
            foreach ($ss->si as $si) {
                $texto = '';
                if (isset($si->t)) {
                    $texto = (string) $si->t;
                } else {
                    foreach ($si->r as $trozo) {
                        $texto .= (string) $trozo->t;
                    }
                }
                $compartidas[] = $texto;
            }
        }

        $hojaXml = simplexml_load_string((string) $zip->getFromName($ruta));
        if (!$hojaXml) {
            throw new \RuntimeException('No se pudo leer la hoja del Excel.');
        }

        $filas = [];
        foreach ($hojaXml->sheetData->row as $fila) {
            $celdas = [];
            foreach ($fila->c as $celda) {
                $celdas[$this->indiceColumnaXlsx((string) $celda['r'])] = $this->valorCeldaXlsx($celda, $compartidas);
            }
            if ($celdas) {
                $completa = [];
                for ($k = 0; $k <= max(array_keys($celdas)); $k++) {
                    $completa[$k] = $celdas[$k] ?? '';
                }
                if (trim(implode('', $completa)) !== '') {
                    $filas[] = $completa;
                }
            }
        }

        if (count($filas) < 2) {
            throw new \RuntimeException('La hoja no tiene filas para importar.');
        }
        if (count($filas) - 1 > self::MAX_FILAS) {
            throw new \RuntimeException('La hoja supera las ' . self::MAX_FILAS . ' filas; usá una hoja más corta.');
        }

        $cabecera = array_map(fn ($c) => trim(Str::snake(str_replace(['.', '-', ' '], '_', Str::lower(removeAccents(trim($c))))), '_'), array_shift($filas));

        // Las hojas de actividades toman nombre, fecha y tipo por fórmula: si nadie las calculó (el archivo no
        // pasó por Excel) llegan vacías. Se avisa en vez de marcar todas las filas como incorrectas.
        $posActividad = array_search('actividad', $cabecera, true);
        if ($tipo === 'extensiones' && in_array('id_actividad', $cabecera, true) && $posActividad !== false
            && trim(implode('', array_column($filas, $posActividad))) === '') {
            throw new \RuntimeException('Las fórmulas del Excel no tienen valor guardado. Abrí el archivo en Excel, guardalo (Ctrl+G) y volvé a subirlo.');
        }

        // Excel guarda las fechas escritas a mano como número de serie: se pasan a dd/mm/aaaa.
        $columnasFecha = array_keys(array_filter($cabecera, fn ($c) => in_array($c, ['fecha', 'fecha_nacimiento'], true)));
        if ($columnasFecha) {
            foreach ($filas as &$fila) {
                foreach ($columnasFecha as $i) {
                    if (isset($fila[$i]) && preg_match('/^\d{5}(\.\d+)?$/', $fila[$i])) {
                        $fila[$i] = Carbon::create(1899, 12, 30)->addDays((int) $fila[$i])->format('d/m/Y');
                    }
                }
            }
            unset($fila);
        }

        return [$cabecera, $filas];
    }

    private function elegirHojaXlsx(array $hojas, string $tipo, int $parte): string
    {
        $etiqueta = ['alumnos' => 'Alumnos', 'docentes' => 'Docentes', 'extensiones' => 'Actividades'][$tipo];

        foreach ($hojas as $nombre => $ruta) {
            if ($this->normalizar($nombre) === $this->normalizar($etiqueta . ' ' . $parte)) {
                return $ruta;
            }
        }
        if ($parte === 1) {
            foreach ($hojas as $nombre => $ruta) {
                if ($this->normalizar($nombre) === $this->normalizar($etiqueta)) {
                    return $ruta;
                }
            }
        }
        if (count($hojas) === 1) {
            return array_values($hojas)[0];
        }
        throw new \RuntimeException('No encuentro la hoja "' . $etiqueta . ' ' . $parte . '". Hojas del archivo: ' . implode(', ', array_keys($hojas)) . '.');
    }

    private function indiceColumnaXlsx(string $referencia): int
    {
        preg_match('/^[A-Z]+/', $referencia, $m);
        $n = 0;
        foreach (str_split($m[0] ?? 'A') as $letra) {
            $n = $n * 26 + (ord($letra) - 64);
        }
        return $n - 1;
    }

    private function valorCeldaXlsx(\SimpleXMLElement $celda, array $compartidas): string
    {
        switch ((string) $celda['t']) {
            case 's':
                return trim($compartidas[(int) $celda->v] ?? '');
            case 'inlineStr':
                $texto = isset($celda->is->t) ? (string) $celda->is->t : '';
                if ($texto === '' && isset($celda->is->r)) {
                    foreach ($celda->is->r as $trozo) {
                        $texto .= (string) $trozo->t;
                    }
                }
                return trim($texto);
            case 'str':
                return trim((string) $celda->v);
            case 'b':
                return (string) $celda->v === '1' ? 'SI' : 'NO';
            case 'e':
                return '';
            default:
                $valor = trim((string) $celda->v);
                if ($valor !== '' && is_numeric($valor) && stripos($valor, 'E') !== false) {
                    $valor = rtrim(rtrim(number_format((float) $valor, 10, '.', ''), '0'), '.');
                }
                return $valor;
        }
    }

    // --------------------------------------------------------------------- CSV

    private function leerCsv(UploadedFile $archivo): array
    {
        $contenido = file_get_contents($archivo->getRealPath());
        $contenido = preg_replace('/^\xEF\xBB\xBF/', '', $contenido);
        if (!mb_check_encoding($contenido, 'UTF-8')) {
            $contenido = mb_convert_encoding($contenido, 'UTF-8', 'Windows-1252');
        }

        $lineas = array_values(array_filter(preg_split('/\r\n|\n|\r/', $contenido), fn ($l) => trim($l, " \t;,") !== ''));
        if (count($lineas) < 2) {
            throw new \RuntimeException('El archivo no tiene filas para importar.');
        }
        if (count($lineas) - 1 > self::MAX_FILAS) {
            throw new \RuntimeException('El archivo supera las ' . self::MAX_FILAS . ' filas; dividilo en partes.');
        }

        $delimitador = substr_count($lineas[0], ';') >= substr_count($lineas[0], ',') ? ';' : ',';
        $cabecera = array_map(fn ($c) => Str::snake(str_replace(['.', '-', ' '], '_', Str::lower(removeAccents(trim($c))))), str_getcsv(array_shift($lineas), $delimitador));
        $cabecera = array_map(fn ($c) => trim($c, '_'), $cabecera);

        $filas = array_map(fn ($l) => str_getcsv($l, $delimitador), $lineas);
        return [$cabecera, $filas];
    }

    private function columnasDe($tipo): array
    {
        return match ($tipo) {
            'alumnos' => self::COLUMNAS_ALUMNO,
            'extensiones' => self::COLUMNAS_EXTENSION,
            default => self::COLUMNAS_DOCENTE,
        };
    }

    private function autorizarTipo($tipo): void
    {
        $permisos = ['alumnos' => 'crear_alumnos', 'docentes' => 'crear_docentes', 'extensiones' => 'crear_extensiones_universitarias'];
        abort_unless(isset($permisos[$tipo]), 404);
        abort_unless(Auth::user()->can($permisos[$tipo]), 403);
    }
}
