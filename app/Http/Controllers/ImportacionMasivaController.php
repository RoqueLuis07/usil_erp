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
use App\Models\Nacionalidad;
use App\Models\Sexo;
use App\Models\User;

/**
 * Carga masiva de alumnos y docentes desde un CSV (Excel lo guarda como
 * "CSV UTF-8"). Primero se puede "Validar" (no guarda nada) y después
 * "Importar". Las filas con error se informan con su número y motivo y no
 * frenan al resto.
 */
class ImportacionMasivaController extends Controller
{
    private const MAX_FILAS = 2000;

    private const COLUMNAS_ALUMNO = [
        'documento', 'primer_nombre', 'segundo_nombre', 'tercer_nombre', 'primer_apellido', 'segundo_apellido',
        'sexo', 'fecha_nacimiento', 'nacionalidad', 'telefono', 'celular', 'correo', 'direccion',
        'departamento', 'ciudad', 'barrio', 'facultad', 'carrera', 'anho_ingreso', 'semestre_ingreso',
    ];

    private const COLUMNAS_DOCENTE = [
        'documento', 'primer_nombre', 'segundo_nombre', 'tercer_nombre', 'primer_apellido', 'segundo_apellido',
        'sexo', 'fecha_nacimiento', 'nacionalidad', 'telefono', 'celular', 'correo', 'direccion',
        'departamento', 'ciudad', 'barrio', 'nivel_academico', 'area_conocimiento', 'capacitacion_didactica',
        'tutor', 'facultad', 'carreras',
    ];

    private array $cache = [];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        abort_unless(Auth::user()->can('crear_alumnos') || Auth::user()->can('crear_docentes'), 403);

        return view('importaciones.index', [
            'carreras' => Carrera::with('Facultad')->where('estado', 'AC')->orderBy('nombre_fantasia')->get(),
            'departamentos' => DepartamentoParaguay::orderBy('nombre')->get(),
            'niveles' => DocenteNivelAcademico::where('estado', 'AC')->orderBy('nombre')->get(),
            'nacionalidades' => Nacionalidad::orderBy('nombre')->get(),
            'resultado' => session('resultado'),
        ]);
    }

    public function plantilla($tipo)
    {
        $this->autorizarTipo($tipo);

        $columnas = $tipo === 'alumnos' ? self::COLUMNAS_ALUMNO : self::COLUMNAS_DOCENTE;
        $ejemplo = $tipo === 'alumnos'
            ? ['1234567', 'Maria', '', '', 'Gonzalez', 'Benitez', 'Femenino', '15/03/2004', 'Paraguaya', '', '0981123456', 'maria@correo.com', 'Calle 1 y Av. 2', 'Central', 'Luque', 'Centro', 'Facultad de Ciencias', 'Ingenieria en Informatica', '2023', '1']
            : ['7654321', 'Juan', '', '', 'Perez', 'Lopez', 'Masculino', '02/11/1985', 'Paraguaya', '', '0971654321', 'juan@correo.com', 'Calle 3', 'Central', 'San Lorenzo', '', 'Maestria', 'Tecnologia e Ingenieria', 'SI', 'NO', 'Facultad de Ciencias', 'Ingenieria en Informatica|Otra Carrera'];

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
            'archivo' => ['required', 'file', 'max:5120', 'mimes:csv,txt'],
            'modo' => ['required', 'in:validar,importar'],
        ], [
            'archivo.required' => 'Elegí un archivo CSV.',
            'archivo.mimes' => 'El archivo tiene que ser CSV (en Excel: Guardar como > CSV UTF-8).',
        ]);

        $persistir = $request->modo === 'importar';
        $columnasEsperadas = $tipo === 'alumnos' ? self::COLUMNAS_ALUMNO : self::COLUMNAS_DOCENTE;

        try {
            [$cabecera, $filas] = $this->leerCsv($request->file('archivo'));
        } catch (\RuntimeException $e) {
            return back()->with('error-message', $e->getMessage());
        }

        $faltantes = array_diff(['documento', 'primer_nombre', 'primer_apellido'], $cabecera);
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
                    $accion = $tipo === 'alumnos' ? $this->procesarAlumno($datos) : $this->procesarDocente($datos);
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

    // ------------------------------------------------------------------ alumnos

    private function procesarAlumno(array $d): string
    {
        $this->exigir($d, ['documento', 'primer_nombre', 'primer_apellido']);
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
        if ($alumno) {
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
        $alumno->usuario_id = $this->crearUsuario($d, $documento, 'ALUMNO', 'alumnos.local')->id;
        $alumno->save();
        $this->sincronizarNacionalidades($alumno, $d['nacionalidad'] ?? '');

        return 'creado';
    }

    // ----------------------------------------------------------------- docentes

    private function procesarDocente(array $d): string
    {
        $this->exigir($d, ['documento', 'primer_nombre', 'primer_apellido']);
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
        $persona->primer_nombre = removeAccents(Str::upper($d['primer_nombre']));
        $persona->segundo_nombre = removeAccents(Str::upper($d['segundo_nombre'] ?? '')) ?: null;
        $persona->tercer_nombre = removeAccents(Str::upper($d['tercer_nombre'] ?? '')) ?: null;
        $persona->primer_apellido = removeAccents(Str::upper($d['primer_apellido']));
        $persona->segundo_apellido = removeAccents(Str::upper($d['segundo_apellido'] ?? '')) ?: null;
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
        $usuario->name = removeAccents(Str::upper($d['primer_nombre'])) . ' ' . removeAccents(Str::upper($d['primer_apellido']));
        $usuario->email = $correo;
        $usuario->password = Hash::make($documento . '-' . Str::substr(removeAccents(Str::upper($d['primer_nombre'])), 0, 1) . Str::substr(removeAccents(Str::lower($d['primer_apellido'])), 0, 1));
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

    private function autorizarTipo($tipo): void
    {
        abort_unless(in_array($tipo, ['alumnos', 'docentes'], true), 404);
        abort_unless(Auth::user()->can($tipo === 'alumnos' ? 'crear_alumnos' : 'crear_docentes'), 403);
    }
}
