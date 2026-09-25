<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Carbon\Carbon;

use App\Models\Carrera;
use App\Models\Docente;
use App\Models\DocenteNivelAcademico;
use App\Models\ExtensionUniversitaria;
use App\Models\Facultad;
use App\Models\Sexo;
use App\Models\TipoExtensionUniversitaria;

/**
 * Reportes de gestión del módulo: extensiones (por mes/año, carrera, facultad,
 * docente y tipo) y docentes (perfil, nivel académico, demografía y extensión
 * realizada). Cada reporte se ve en pantalla y se puede exportar a CSV.
 */
class ReporteExtensionController extends Controller
{
    private const MESES = [1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'];

    private const ESTADOS = ['PE' => 'Pendiente', 'AP' => 'Aprobado', 'IN' => 'Informado', 'CO' => 'Completo', 'FI' => 'Finalizado', 'RE' => 'Rechazado'];

    public function __construct()
    {
        $this->middleware('auth');
    }

    // =============================================================== EXTENSIONES

    public function extensiones(Request $request)
    {
        $this->authorize('generar_reportes_extensiones_universitarias');

        $filtros = [
            'anio' => $request->anio, 'mes' => $request->mes, 'carrera' => $request->carrera, 'facultad' => $request->facultad,
            'docente' => $request->docente, 'tipo' => $request->tipo, 'estado' => $request->estado,
        ];

        $filas = $this->filasExtensiones($filtros);

        if ($request->exportar === 'csv') {
            return $this->csv('reporte_extensiones.csv', [
                'ID', 'Proyecto', 'Tipo de actividad', 'Estado', 'Fecha inicio', 'Fecha fin', 'Docente responsable', 'Horas asignadas',
                'Estudiantes', 'Carreras', 'Facultades', 'Con certificado', 'Certificados cargados',
            ], $filas->map(fn ($f) => [
                $f->id, $f->nombre, $f->tipo, $f->estado_texto, $f->fecha_inicio, $f->fecha_fin, $f->docente, $f->horas,
                $f->estudiantes, $f->carreras->implode(' | '), $f->facultades->implode(' | '), $f->tiene_certificado ? 'SI' : 'NO', $f->certificados_cargados,
            ])->all());
        }

        $resumen = [
            'extensiones' => $filas->count(),
            'estudiantes' => $filas->sum('estudiantes'),
            'horas' => $filas->sum('horas'),
            'con_certificado' => $filas->where('tiene_certificado', true)->count(),
            'certificados_cargados' => $filas->sum('certificados_cargados'),
        ];

        $porTipo = $this->contar($filas, fn ($f) => [$f->tipo]);
        $porCarrera = $this->contar($filas, fn ($f) => $f->carreras->all());
        $porFacultad = $this->contar($filas, fn ($f) => $f->facultades->all());
        $porDocente = $this->contar($filas, fn ($f) => [$f->docente]);
        $porMes = $this->contar($filas, fn ($f) => [$f->mes_texto], false);
        $porSemestre = $this->contarEstudiantes($filas, 'semestres');
        $estudiantesPorCarrera = $this->contarEstudiantes($filas, 'carreras_estudiantes');
        $conCertificado = $filas->where('tiene_certificado', true)->values();

        return view('reportes_extension.extensiones', [
            'filas' => $filas, 'resumen' => $resumen, 'filtros' => $filtros,
            'porTipo' => $porTipo, 'porCarrera' => $porCarrera, 'porFacultad' => $porFacultad, 'porDocente' => $porDocente,
            'porMes' => $porMes, 'porSemestre' => $porSemestre, 'estudiantesPorCarrera' => $estudiantesPorCarrera,
            'conCertificado' => $conCertificado,
            'anios' => ExtensionUniversitaria::selectRaw('YEAR(fecha_inicio) as anio')->whereNotNull('fecha_inicio')->distinct()->orderByDesc('anio')->pluck('anio'),
            'meses' => self::MESES, 'estados' => self::ESTADOS,
            'carreras' => Carrera::orderBy('nombre_fantasia')->get(), 'facultades' => Facultad::orderBy('nombre')->get(),
            'docentes' => Docente::orderBy('primer_nombre')->get(), 'tipos' => TipoExtensionUniversitaria::orderBy('nombre')->get(),
        ]);
    }

    /** Una fila por extensión, ya con sus estudiantes aceptados resumidos. */
    private function filasExtensiones(array $f): Collection
    {
        $query = ExtensionUniversitaria::with([
            'TipoExtension', 'Docente',
            'ExtensionUniversitariaDetalles' => fn ($q) => $q->where('estado', 'AC'),
            'ExtensionUniversitariaDetalles.Alumno.Carrera.Facultad',
            'ExtensionUniversitariaDetalles.Carrera.Facultad',
            'ExtensionUniversitariaDetalles.Semestre',
        ]);

        if (!blank($f['anio'])) {
            $query->whereYear('fecha_inicio', $f['anio']);
        }
        if (!blank($f['mes'])) {
            $query->whereMonth('fecha_inicio', $f['mes']);
        }
        if (!blank($f['tipo'])) {
            $query->where('tipo_extension_id', $f['tipo']);
        }
        if (!blank($f['docente'])) {
            $query->where('docente_id', $f['docente']);
        }
        if (!blank($f['estado'])) {
            $query->where('estado', $f['estado']);
        } else {
            $query->where('estado', '!=', 'RE');
        }

        return $query->orderByDesc('fecha_inicio')->get()->map(function ($e) {
            $detalles = $e->ExtensionUniversitariaDetalles;

            // La carrera sale del snapshot de la inscripción y, si no hay, de la carrera propia del alumno.
            $carreraDe = fn ($d) => $d->Carrera ?: optional($d->Alumno)->Carrera;

            $carrerasEst = $detalles->map($carreraDe)->filter();
            $fila = (object) [
                'id' => $e->id,
                'nombre' => $e->nombre,
                'tipo' => optional($e->TipoExtension)->nombre ?? '-',
                'estado_texto' => self::ESTADOS[$e->estado] ?? $e->estado,
                'fecha_inicio' => $e->fecha_inicio ? Carbon::parse($e->fecha_inicio)->format('d/m/Y') : '',
                'fecha_fin' => $e->fecha_fin ? Carbon::parse($e->fecha_fin)->format('d/m/Y') : '',
                'mes_texto' => $e->fecha_inicio ? Carbon::parse($e->fecha_inicio)->format('Y-m') : 'Sin fecha',
                'docente' => $e->Docente ? $e->Docente->primer_nombre . ' ' . $e->Docente->primer_apellido : 'Sin responsable',
                'docente_id' => $e->docente_id,
                'horas' => (float) $e->cantidad_horas,
                'estudiantes' => $detalles->count(),
                'carreras' => $carrerasEst->pluck('nombre_fantasia')->unique()->values(),
                'facultades' => $carrerasEst->map(fn ($c) => optional($c->Facultad)->nombre)->filter()->unique()->values(),
                'carreras_estudiantes' => $carrerasEst->pluck('nombre_fantasia')->values(),
                'semestres' => $detalles->map(function ($d) use ($e) {
                    $nivel = optional($d->Alumno)->semestreEn($e->fecha_inicio);
                    return $nivel ? $nivel . '.º semestre' : (optional($d->Semestre)->nombre ?: 'Sin dato');
                })->values(),
                'tiene_certificado' => (bool) $e->tiene_certificado,
                'certificados_cargados' => $detalles->whereNotNull('url_certificado')->count(),
            ];
            return $fila;
        })->filter(function ($fila) use ($f) {
            if (!blank($f['carrera']) && !$this->coincide($fila->carreras, $f['carrera'], Carrera::class, 'nombre_fantasia')) {
                return false;
            }
            if (!blank($f['facultad']) && !$this->coincide($fila->facultades, $f['facultad'], Facultad::class, 'nombre')) {
                return false;
            }
            return true;
        })->values();
    }

    private function coincide(Collection $nombres, $id, string $modelo, string $columna): bool
    {
        $nombre = optional($modelo::find($id))->{$columna};
        return $nombre && $nombres->contains($nombre);
    }

    /** Ranking [etiqueta => cantidad de extensiones], de mayor a menor (o cronológico). */
    private function contar(Collection $filas, callable $clasificador, bool $ordenarPorCantidad = true): array
    {
        $cuenta = [];
        foreach ($filas as $fila) {
            foreach (array_unique($clasificador($fila)) as $etiqueta) {
                $cuenta[$etiqueta] = ($cuenta[$etiqueta] ?? 0) + 1;
            }
        }
        $ordenarPorCantidad ? arsort($cuenta) : ksort($cuenta);
        return $cuenta;
    }

    /** Ranking [etiqueta => cantidad de estudiantes] a partir de una lista por estudiante. */
    private function contarEstudiantes(Collection $filas, string $campo): array
    {
        $cuenta = [];
        foreach ($filas as $fila) {
            foreach ($fila->{$campo} as $etiqueta) {
                $cuenta[$etiqueta] = ($cuenta[$etiqueta] ?? 0) + 1;
            }
        }
        arsort($cuenta);
        return $cuenta;
    }

    // ================================================================= DOCENTES

    public function docentes(Request $request)
    {
        $this->authorize('ver_docentes');

        $filtros = [
            'carrera' => $request->carrera, 'facultad' => $request->facultad, 'nivel' => $request->nivel, 'sexo' => $request->sexo,
            'anio' => $request->anio, 'tutor' => $request->tutor, 'didactica' => $request->didactica,
        ];

        $query = Docente::with(['Carreras.Facultad', 'Sexo', 'Nacionalidades', 'Departamento', 'Ciudad', 'NivelAcademico']);
        if (!blank($filtros['carrera'])) {
            $query->whereHas('Carreras', fn ($q) => $q->where('carreras.id', $filtros['carrera']));
        }
        if (!blank($filtros['facultad'])) {
            $query->whereHas('Carreras', fn ($q) => $q->where('carreras.facultad_id', $filtros['facultad']));
        }
        if (!blank($filtros['nivel'])) {
            $query->where('nivel_academico_id', $filtros['nivel']);
        }
        if (!blank($filtros['sexo'])) {
            $query->where('sexo_id', $filtros['sexo']);
        }
        if (!blank($filtros['anio'])) {
            $query->whereYear('created_at', $filtros['anio']);
        }
        if ($filtros['tutor'] === 'SI' || $filtros['tutor'] === 'NO') {
            $query->where('tutor_tesis', $filtros['tutor'] === 'SI');
        }
        if ($filtros['didactica'] === 'SI' || $filtros['didactica'] === 'NO') {
            $query->where('capacitacion_didactica', $filtros['didactica'] === 'SI');
        }
        $docentes = $query->where('estado', 'AC')->orderBy('primer_apellido')->get();

        // Extensión realizada por cada docente (como responsable), sin las rechazadas.
        $extensiones = $this->filasExtensiones(['anio' => null, 'mes' => null, 'carrera' => null, 'facultad' => null, 'docente' => null, 'tipo' => null, 'estado' => null])
            ->groupBy('docente_id');

        $filas = $docentes->map(function ($d) use ($extensiones) {
            $ext = $extensiones->get($d->id, collect());
            return (object) [
                'id' => $d->id,
                'nombre' => $d->primer_nombre . ' ' . $d->primer_apellido,
                'documento' => $d->numero_documento,
                'sexo' => optional($d->Sexo)->nombre ?? 'Sin dato',
                'nivel' => optional($d->NivelAcademico)->nombre ?? 'Sin dato',
                'nacionalidades' => $d->Nacionalidades->pluck('nombre')->values(),
                'departamento' => optional($d->Departamento)->nombre ?? 'Sin dato',
                'ciudad' => optional($d->Ciudad)->nombre ?? 'Sin dato',
                'anio' => $d->created_at ? $d->created_at->year : null,
                'edad' => $d->fecha_nacimiento ? Carbon::parse($d->fecha_nacimiento)->age : null,
                'carreras' => $d->Carreras->pluck('nombre_fantasia')->values(),
                'facultades' => $d->Carreras->map(fn ($c) => optional($c->Facultad)->nombre)->filter()->unique()->values(),
                'tutor' => (bool) $d->tutor_tesis,
                'didactica' => (bool) $d->capacitacion_didactica,
                'extensiones' => $ext->count(),
                'ext_estudiantes' => $ext->sum('estudiantes'),
                'ext_horas' => $ext->sum('horas'),
                'ext_carreras' => $ext->flatMap(fn ($e) => $e->carreras)->unique()->values(),
                'ext_semestres' => $ext->flatMap(fn ($e) => $e->semestres)->unique()->values(),
            ];
        });

        if ($request->exportar === 'csv') {
            return $this->csv('reporte_docentes.csv', [
                'Documento', 'Docente', 'Sexo', 'Edad', 'Nivel académico', 'Nacionalidad', 'Departamento', 'Ciudad', 'Año de alta',
                'Carreras', 'Facultades', 'Tutor', 'Capacitación didáctica', 'Extensiones realizadas', 'Estudiantes en sus extensiones', 'Horas de extensión',
            ], $filas->map(fn ($f) => [
                $f->documento, $f->nombre, $f->sexo, $f->edad, $f->nivel, $f->nacionalidades->implode(' | '), $f->departamento, $f->ciudad, $f->anio,
                $f->carreras->implode(' | '), $f->facultades->implode(' | '), $f->tutor ? 'SI' : 'NO', $f->didactica ? 'SI' : 'NO',
                $f->extensiones, $f->ext_estudiantes, $f->ext_horas,
            ])->all());
        }

        $conteo = fn (callable $c) => $this->contar($filas, $c);
        $rangos = fn ($f) => [$f->edad === null ? 'Sin dato' : ($f->edad < 30 ? 'Menos de 30' : ($f->edad < 40 ? '30 a 39' : ($f->edad < 50 ? '40 a 49' : '50 o más')))];

        // Extensión de estos docentes por carrera y semestre (de los estudiantes que participaron).
        $extDeEstosDocentes = $extensiones->only($filas->pluck('id')->all())->flatten(1);

        return view('reportes_extension.docentes', [
            'filas' => $filas, 'filtros' => $filtros,
            'resumen' => [
                'docentes' => $filas->count(), 'tutores' => $filas->where('tutor', true)->count(), 'didactica' => $filas->where('didactica', true)->count(),
                'extensiones' => $filas->sum('extensiones'), 'horas' => $filas->sum('ext_horas'),
            ],
            'porFacultad' => $conteo(fn ($f) => $f->facultades->isEmpty() ? ['Sin facultad'] : $f->facultades->all()),
            'porCarrera' => $conteo(fn ($f) => $f->carreras->isEmpty() ? ['Sin carrera'] : $f->carreras->all()),
            'porNivel' => $conteo(fn ($f) => [$f->nivel]),
            'porSexo' => $conteo(fn ($f) => [$f->sexo]),
            'porNacionalidad' => $conteo(fn ($f) => $f->nacionalidades->isEmpty() ? ['Sin dato'] : $f->nacionalidades->all()),
            'porDepartamento' => $conteo(fn ($f) => [$f->departamento]),
            'porCiudad' => $conteo(fn ($f) => [$f->ciudad]),
            'porAnio' => $this->contar($filas, fn ($f) => [(string) ($f->anio ?? 'Sin dato')], false),
            'porEdad' => $conteo($rangos),
            'extPorCarrera' => $this->contar($extDeEstosDocentes, fn ($e) => $e->carreras->isEmpty() ? ['Sin carrera'] : $e->carreras->all()),
            'extPorSemestre' => $this->contarEstudiantes($extDeEstosDocentes, 'semestres'),
            'anios' => Docente::selectRaw('YEAR(created_at) as anio')->distinct()->orderByDesc('anio')->pluck('anio'),
            'carreras' => Carrera::orderBy('nombre_fantasia')->get(), 'facultades' => Facultad::orderBy('nombre')->get(),
            'niveles' => DocenteNivelAcademico::orderBy('nombre')->get(), 'sexos' => Sexo::orderBy('nombre')->get(),
        ]);
    }

    // ------------------------------------------------------------------- CSV

    private function csv(string $nombre, array $cabecera, array $filas)
    {
        $limpiar = fn ($v) => '"' . str_replace('"', '""', (string) $v) . '"';
        $salida = "\xEF\xBB\xBF" . implode(';', array_map($limpiar, $cabecera)) . "\r\n";
        foreach ($filas as $fila) {
            $salida .= implode(';', array_map($limpiar, $fila)) . "\r\n";
        }
        return response($salida, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $nombre . '"',
        ]);
    }
}
