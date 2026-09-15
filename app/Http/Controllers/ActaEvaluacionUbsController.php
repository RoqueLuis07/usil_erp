<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\AutoEncoder;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\Empresa;
use App\Models\ActaEvaluacionUbs;
use App\Models\ActaEvaluacionAlumnoUbs;
use App\Models\AlumnoNotaUbs;
use App\Models\IncripcionUbs;
use App\Models\InscripcionModulo;
use App\Models\Curso;
use App\Models\CursoModulo;
use App\Models\Modulo;
use App\Models\EvaluacionUbs;
use App\Models\Alumno;
use App\Models\Docente;
use App\Models\ClaseMaestria;
use App\Models\AlumnoAsistenciaUbs;
use App\Models\AlumnoPuntajeUbs;


class ActaEvaluacionUbsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index($curso_id)
    {
        $this->authorize('ver_actas_maestrias_ubs');

        try {
            $curso = Curso::with(['modulos' => function ($query) {
                $query->orderBy('id', 'asc');
            }])->findOrFail($curso_id);
            $actas_evaluaciones = ActaEvaluacionUbs::where('curso_id', $curso_id)->orderBy('numero_acta', 'desc')->get();
            return view('ubs/maestrias/actas_evaluaciones/index')->with(compact('curso', 'actas_evaluaciones'));
        } catch (\Exception $e) {
            return redirect()->route('maestrias.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_actas_maestrias_ubs');

        try {
            $acta_evaluacion = ActaEvaluacionUbs::with('alumnos')->findOrFail($id);
            return view('ubs/maestrias/actas_evaluaciones/show')->with(compact('acta_evaluacion'));
        } catch (\Exception $e) {
            return redirect()->route('actas_evaluaciones_ubs.index', $acta_evaluacion->curso_id)->with('error-message', $e->getMessage());
        }
    }

    public function create($curso_id, $modulo_id)
    {
        $this->authorize('crear_actas_maestrias_ubs');

        DB::beginTransaction();

        try {
            $curso = Curso::findOrFail($curso_id);
            $modulo = Modulo::findOrFail($modulo_id);
            $curso_modulo = CursoModulo::where('curso_id', $curso_id)->where('modulo_id', $modulo_id)->first();
            $docente = Docente::findOrFail($curso_modulo->docente_id);

            $cantidad_actas = ActaEvaluacionUbs::where('curso_id', $curso_id)->where('modulo_id', $modulo_id)->count();
            if ($cantidad_actas > 0) {
                if ($cantidad_actas != 1) {
                    $existe = ActaEvaluacionUbs::where('curso_id', $curso_id)->where('modulo_id', $modulo_id)->where('tipo', 'E')->exists();
                    if ($existe) {
                        return back()->with('error-message', 'Todas las actas de evaluaciones ya fueron generadas en el módulo seleccionado.');
                    }
                    $tipo = 4;
                    $tipo_examen = 'Extraordinario';
                } else {
                    $existe = ActaEvaluacionUbs::whereHas('alumnos', function ($query) {
                        $query->where('puntos_examen', '!=', null)
                            ->where('calificacion', '!=', null);
                    })->where('curso_id', $curso_id)->where('modulo_id', $modulo_id)->where('tipo', 'O')->exists();
                    if (!$existe) {
                        return back()->with('error-message', 'Se deben cargar los puntajes del acta de evaluación ordinaria antes de generar un nuevo acta.');
                    }
                    $tipo = 3;
                    $tipo_examen = 'Complementario';
                }
            } else {
                $existe = AlumnoPuntajeUbs::where('curso_id', $curso_id)->where('modulo_id', $modulo_id)->where('evaluacion_id', 1)->exists();
                if (!$existe) {
                    return back()->with('error-message', 'Se deben cargar los puntajes de proceso antes de generar un acta.');
                }
                $tipo = 2;
                $tipo_examen = 'Ordinario';
            }

            $evaluacion = EvaluacionUbs::where('tipo_evaluacion_id', $tipo)->first();
            $puntaje_minimo_requerido = $evaluacion->puntaje_minimo_requerido;
            $porcentaje_minimo_asistencia = 70;

            $alumnos = Alumno::select([
                'alumnos.*',
                DB::raw('SUM(DISTINCT CASE
                    WHEN ap.evaluacion_id = 1 AND ap.modulo_id = cm.modulo_id THEN ap.puntos_obtenidos * e.valor_porcentual / 100
                    ELSE 0
                    END) AS puntos_obtenidos'),
                DB::raw("(COUNT( DISTINCT CASE WHEN aa.estado IN ('PR', 'AJ') THEN 1 END) / COUNT(DISTINCT cm.id)) * 100 AS porcentaje_asistencia"),
            ])
            ->join('alumnos_puntajes_ubs as ap', 'alumnos.id', '=', 'ap.alumno_id')
            ->join('evaluaciones_ubs as e', 'ap.evaluacion_id', '=', 'e.id')
            ->join('alumnos_asistencias_ubs as aa', 'alumnos.id', '=', 'aa.alumno_id')
            ->join('clases_maestrias as cm', function ($join) use ($curso_id, $modulo_id) {
                $join->on('cm.id', '=', 'cm.id') // Relaciona las clases del curso y módulo
                    ->where('cm.curso_id', $curso_id)
                    ->where('cm.modulo_id', $modulo_id);
            })
            ->join('inscripciones_ubs as i', function ($join) use ($curso_id) {
                $join->on('alumnos.id', '=', 'i.alumno_id') //Relaciona el alumno con la inscripción
                     ->where('i.curso_id', '=', $curso_id); //Asegura que sea del curso correcto
            })
            ->join('pagos_inscripciones_ubs as pi', 'i.id', '=', 'pi.inscripcion_id')
            ->whereHas('inscripcionesModulos', function ($query) use ($curso_id, $modulo_id) {
                $query->where('curso_id', $curso_id)
                    ->where('modulo_id', $modulo_id)
                    ->where('estado', 'EC');
            })
            ->groupBy('alumnos.id')
            ->having(DB::raw("(COUNT(CASE WHEN aa.estado IN ('PR', 'AJ') THEN 1 END) / COUNT(cm.id)) * 100"), '>=', $porcentaje_minimo_asistencia)
            ->havingRaw('SUM(CASE
                WHEN ap.evaluacion_id = 1 THEN ap.puntos_obtenidos * e.valor_porcentual / 100
                ELSE 0
                END) >= ?', [$puntaje_minimo_requerido])
            ->having(DB::raw("COALESCE(
                (SELECT
                    CASE
                        WHEN pi.estado = 'CA' THEN 1
                        WHEN pi.estado != 'CA' AND pi.fecha_vencimiento >= CURRENT_DATE THEN 1
                        ELSE 0
                    END
                FROM pagos_inscripciones_ubs pi
                WHERE pi.alumno_id = alumnos.id
                ORDER BY CASE
                    WHEN pi.estado != 'CA' AND pi.fecha_vencimiento <= CURRENT_DATE THEN 0
                    ELSE 1
                END, pi.fecha_vencimiento
                LIMIT 1
            ), 0)"), '>=', '1')
            ->orderBy('primer_apellido')
            ->get();

            $acta = ActaEvaluacionUbs::with('alumnos')->where('curso_id', $curso_id)->where('modulo_id', $modulo_id);
            $cantidad_actas = $acta->count();

            return view('ubs/maestrias/actas_evaluaciones/create')->with(compact('curso', 'modulo', 'docente', 'alumnos', 'cantidad_actas', 'tipo_examen'));

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('actas_evaluaciones_ubs.index', $curso_id)->with('error-message', $e->getMessage());
        }
    }

    public function generar_actas(Request $request, $curso_id, $modulo_id, $tipo)
    {
        $this->authorize('generar_actas_maestrias_ubs');

        $request->validate([
            'fecha' => ['required', 'date']
        ]);

        DB::beginTransaction();

        try {
            $curso = Curso::findOrFail($curso_id);
            $modulo = Modulo::findOrFail($modulo_id);
            $curso_modulo = CursoModulo::where('curso_id', $curso_id)->where('modulo_id', $modulo_id)->first();
            $docente = Docente::findOrFail($curso_modulo->docente_id);

            $evaluacion = EvaluacionUbs::findOrFail($tipo);
            $puntaje_minimo_requerido = $evaluacion->puntaje_minimo_requerido;
            $porcentaje_minimo_asistencia = 70;

            switch ($tipo) {
                case 2:
                    $nombre_examen = 'ORDINARIO';
                    $tipo_acta = 'O';
                    $oportunidad = 'PRIMERA';
                    break;
                case 3:
                    $nombre_examen = 'COMPLEMENTARIO';
                    $tipo_acta = 'C';
                    $oportunidad = 'SEGUNDA';
                    break;
                case 4:
                    $nombre_examen = 'EXTRAORDINARIO';
                    $tipo_acta = 'E';
                    $oportunidad = 'TERCERA';
                    break;
                default:
                    return redirect()->route('actas_evaluaciones_ubs.index', $curso_id)->with('error-message', 'Ocurrió un error en la generación. Si el problema persiste, contacte con el Administrador.');
                    break;
            }

            $acta = ActaEvaluacionUbs::with('alumnos')->where('curso_id', $curso_id)->where('modulo_id', $modulo_id);
            $cantidad_actas = $acta->count();

            $existe_acta = ActaEvaluacionUbs::where('curso_id', $curso_id)
                ->where('modulo_id', $modulo_id)
                ->where('tipo', $tipo_acta)
                ->where('docente_id', $docente->id)->first();

            if ($existe_acta) {
                return redirect()->route('actas_evaluaciones_ubs.show', $existe_acta->id)->with('error-message', 'El acta ya existe, no se puede volver a generar, aquí te lo mostramos.');
            }

            $alumnos = Alumno::select([
                'alumnos.*',
                DB::raw('SUM(DISTINCT CASE
                    WHEN ap.evaluacion_id = 1 AND ap.modulo_id = cm.modulo_id THEN ap.puntos_obtenidos * e.valor_porcentual / 100
                    ELSE 0
                    END) AS puntos_obtenidos'),
                DB::raw("(COUNT( DISTINCT CASE WHEN aa.estado IN ('PR', 'AJ') THEN 1 END) / COUNT(DISTINCT cm.id)) * 100 AS porcentaje_asistencia"),
            ])
            ->join('alumnos_puntajes_ubs as ap', 'alumnos.id', '=', 'ap.alumno_id')
            ->join('evaluaciones_ubs as e', 'ap.evaluacion_id', '=', 'e.id')
            ->join('alumnos_asistencias_ubs as aa', 'alumnos.id', '=', 'aa.alumno_id')
            ->join('clases_maestrias as cm', function ($join) use ($curso_id, $modulo_id) {
                $join->on('cm.id', '=', 'cm.id') // Relaciona las clases del curso y módulo
                    ->where('cm.curso_id', $curso_id)
                    ->where('cm.modulo_id', $modulo_id);
            })
            ->join('inscripciones_ubs as i', function ($join) use ($curso_id) {
                $join->on('alumnos.id', '=', 'i.alumno_id') //Relaciona el alumno con la inscripción
                     ->where('i.curso_id', '=', $curso_id); //Asegura que sea del curso correcto
            })
            ->join('pagos_inscripciones_ubs as pi', 'i.id', '=', 'pi.inscripcion_id')
            ->whereHas('inscripcionesModulos', function ($query) use ($curso_id, $modulo_id) {
                $query->where('curso_id', $curso_id)
                    ->where('modulo_id', $modulo_id)
                    ->where('estado', 'EC');
            })
            ->groupBy('alumnos.id')
            ->having(DB::raw("(COUNT(CASE WHEN aa.estado IN ('PR', 'AJ') THEN 1 END) / COUNT(cm.id)) * 100"), '>=', $porcentaje_minimo_asistencia)
            ->havingRaw('SUM(CASE
                WHEN ap.evaluacion_id = 1 THEN ap.puntos_obtenidos * e.valor_porcentual / 100
                ELSE 0
                END) >= ?', [$puntaje_minimo_requerido])
            ->having(DB::raw("COALESCE(
                (SELECT
                    CASE
                        WHEN pi.estado = 'CA' THEN 1
                        WHEN pi.estado != 'CA' AND pi.fecha_vencimiento >= CURRENT_DATE THEN 1
                        ELSE 0
                    END
                FROM pagos_inscripciones_ubs pi
                WHERE pi.alumno_id = alumnos.id
                ORDER BY CASE
                    WHEN pi.estado != 'CA' AND pi.fecha_vencimiento <= CURRENT_DATE THEN 0
                    ELSE 1
                END, pi.fecha_vencimiento
                LIMIT 1
            ), 0)"), '>=', '1')
            ->orderBy('primer_apellido')
            ->get();

            $old_acta = ActaEvaluacionUbs::orderBy('id', 'desc')->where('curso_id', $curso_id)->first();
            $anho_actual = Carbon::now()->format('Y');
            $curso = Curso::findOrFail($curso_id);
            if ($old_acta) {
                $numero_acta = substr($old_acta->numero_acta, 3, 191); //el primer numero es cuantos espacios queres eliminar y el segundo numero cuanto queres dejar
                $partes_numero = explode('/', $numero_acta);
                $anho_old_acta = $partes_numero[1];
                $partes_numero = explode('.', $partes_numero[0]);
                if ($anho_actual == $anho_old_acta) {
                    $numero_nuevo = intval($partes_numero[4]) + 1;
                    $numero = $curso->codigo . str_pad($numero_nuevo, 3,'0', STR_PAD_LEFT) . '/' . $anho_old_acta;
                } else {
                    $numero = $curso->codigo . str_pad(1, 3, '0', STR_PAD_LEFT) . '/'. $anho_actual;
                }
            } else {
                $numero = $curso->codigo . str_pad(1, 3, '0', STR_PAD_LEFT) . '/'. $anho_actual;
            }

            $fecha_evaluacion = $request->fecha;

            $acta = new ActaEvaluacionUbs();
            $acta->numero_acta = $numero;
            $acta->tipo = $tipo_acta;
            $acta->curso_id = $curso_id;
            $acta->modulo_id = $modulo_id;
            $acta->docente_id = $docente->id;
            $acta->fecha_evaluacion = $fecha_evaluacion;
            $acta->seccion = $curso->programa->turno;
            $acta->generado_por_id = Auth::id();
            $acta->save();

            foreach ($alumnos as $alumno) {
                $acta_alumnos = new ActaEvaluacionAlumnoUbs();
                $acta_alumnos->acta_evaluacion_id = $acta->id;
                $acta_alumnos->alumno_id = $alumno->id;
                $acta_alumnos->puntos_obtenidos = $alumno->puntos_obtenidos;
                $acta_alumnos->save();
            }

            DB::commit();

            $empresa = Empresa::first();

            $titulo = 'ACTA DE EVALUACIÓN FINAL - ' . $nombre_examen;

            switch ($docente->nivel_academico_id) {
                case 1:
                    $prenombre_docente = 'LIC.';
                    break;

                case 2:
                    $prenombre_docente = 'MGTR.';
                    break;
                case 3:
                    $prenombre_docente = 'DR.';
                    break;
                case 4:
                    $prenombre_docente = 'PHD.';
                    break;
                default:
                    $prenombre_docente = '';
                    break;
            }

            $fecha_hoy = Carbon::now();
            $pdf = Pdf::loadView('ubs/maestrias/actas_evaluaciones/pdf', compact('empresa', 'titulo', 'oportunidad', 'numero', 'curso', 'modulo', 'alumnos', 'fecha_evaluacion', 'docente', 'prenombre_docente', 'fecha_hoy'));
            $pdf->setPaper('A4', 'landscape');
            $contenido_pdf = $pdf->output();

            $numero = str_replace('.', '_', $numero);
            $numero = str_replace('/', '-', $numero);

            $nombre_archivo = 'acta_' . Str::lower($numero) . '.pdf';
            $contenido_pdf = $pdf->output();
            $ubicacion_archivo = 'public/maestrias/actas_evaluaciones/generados/' . $nombre_archivo;
            Storage::put($ubicacion_archivo, $contenido_pdf);
            $acta->ubicacion_acta = str_replace('public', 'storage', $ubicacion_archivo);
            $acta->save();

            return $pdf->stream('acta_' . Str::lower(str_replace('/', '_', $numero)) . '.pdf');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('actas_evaluaciones_ubs.index', $curso->id)->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_actas_maestrias_ubs');

        DB::beginTransaction();

        try {
            $acta_evaluacion = ActaEvaluacionUbs::findOrFail($id);
            $ubicacion_acta = $acta_evaluacion->ubicacion_acta;
            $ubicacion_acta = str_replace('storage', 'public', $ubicacion_acta);
            Storage::delete($ubicacion_acta);
            $ubicacion_archivo = $acta_evaluacion->ubicacion_adjunto;
            $ubicacion_archivo = str_replace('storage', 'public', $ubicacion_archivo);
            Storage::delete($ubicacion_archivo);

            $actas_evaluaciones_alumnos = ActaEvaluacionAlumnoUbs::where('acta_evaluacion_id', $id)->get();

            foreach ($actas_evaluaciones_alumnos as $lista_alumno) {
                $alumno_nota = AlumnoNotaUbs::where('alumno_id', $lista_alumno->alumno_id)
                    ->where('curso_id', $acta_evaluacion->curso_id)
                    ->where('modulo_id', $acta_evaluacion->modulo_id)
                    ->delete();

                    $inscripcion = InscripcionModulo::where('alumno_id', $lista_alumno->alumno_id)
                        ->where('curso_id', $acta_evaluacion->curso_id)
                        ->where('modulo_id', $acta_evaluacion->modulo_id)
                        ->first();

                    if ($inscripcion->estado == 'AP' || $inscripcion->estado == 'RE') {
                        $inscripcion->estado = 'EC';
                        $inscripcion->save();
                    }
            }

            $actas_evaluaciones_alumnos = ActaEvaluacionAlumnoUbs::where('acta_evaluacion_id', $id)->delete();
            $acta_evaluacion->delete();

            DB::commit();

            return redirect()->route('actas_evaluaciones_ubs.index', $acta_evaluacion->curso_id)->with('success-message','El acta N° ' . $acta_evaluacion->numero_acta . ' de ' . $acta_evaluacion->curso->nombre_fantasia . ' del módulo ' . $acta_evaluacion->modulo->nombre_fantasia . ' fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('actas_evaluaciones_ubs.index', $acta_evaluacion->curso_id)->with('error-message', 'El acta N° ' . $acta_evaluacion->numero_acta . ' de ' . $acta_evaluacion->curso->nombre_fantasia . ' del módulo ' . $acta_evaluacion->modulo->nombre_fantasia . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('actas_evaluaciones_ubs.index', $acta_evaluacion->curso_id)->with('error-message', $e->getMessage());
            }
        }
    }

    public function subir_acta(Request $request, $id)
    {
        $this->authorize('subir_adjunto_actas_maestrias_ubs');

        $request->validate([
            'acta' => ['required', 'file', 'extensions:jpg,png,pdf']
        ]);

        DB::beginTransaction();

        try {
            $acta_evaluacion = ActaEvaluacionUbs::findOrFail($id);

            //cargar archivo
            $archivo = $request->acta;
            $extension = $archivo->getClientOriginalExtension();
            $directorio = 'storage/maestrias/actas_evaluaciones/firmados';
            if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png') {
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($archivo);
                $image = $image->encode(new AutoEncoder(quality: 50));
                $numero_acta = Str::lower($acta_evaluacion->numero_acta);
                $numero_acta = str_replace('.', '_', $numero_acta);
                $numero_acta = str_replace('/', '-', $numero_acta);
                $nombre = $numero_acta . '_' . Str::lower($acta_evaluacion->tipo) . '_' . Str::lower(str_replace(' ', '_', $acta_evaluacion->curso->nombre_fantasia)) . '_' . Str::lower(str_replace(' ', '_', $acta_evaluacion->modulo->nombre_fantasia));
                $nombre_archivo = $nombre . '.' . $extension;
                $image->save($directorio . '/' . $nombre_archivo);
            } else {
                $directorio_storage = 'public/maestrias/actas_evaluaciones/firmados';
                $numero_acta = Str::lower($acta_evaluacion->numero_acta);
                $numero_acta = str_replace('.', '_', $numero_acta);
                $numero_acta = str_replace('/', '-', $numero_acta);
                $nombre = $numero_acta . '_' . Str::lower($acta_evaluacion->tipo) . '_' . Str::lower(str_replace(' ', '_', $acta_evaluacion->curso->nombre_fantasia)) . '_' . Str::lower(str_replace(' ', '_', $acta_evaluacion->modulo->nombre_fantasia));
                $nombre_archivo = $nombre . '.' . $extension;
                Storage::putFileAs($directorio_storage . '/', $archivo, $nombre_archivo);
            }
            $acta_evaluacion->ubicacion_adjunto = $directorio . '/' . $nombre_archivo;
            $acta_evaluacion->save();

            DB::commit();

            return response()->json([
                'message' => 'El adjunto del acta N° ' . $acta_evaluacion->numero_acta . ' de ' . $acta_evaluacion->curso->nombre_fantasia . ' del módulo ' . $acta_evaluacion->modulo->nombre_fantasia . ' fue subido exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('actas_evaluaciones_ubs.index', $acta_evaluacion->curso_id)->with('error-message', $e->getMessage());
        }
    }

    public function eliminar_acta($id)
    {
        $this->authorize('eliminar_adjunto_actas_maestrias_ubs');

        DB::beginTransaction();

        try {
            $acta_evaluacion = ActaEvaluacionUbs::findOrFail($id);

            $ubicacion_archivo = $acta_evaluacion->ubicacion_adjunto;
            $ubicacion_archivo = str_replace('storage', 'public', $ubicacion_archivo);
            Storage::delete($ubicacion_archivo);

            $acta_evaluacion->ubicacion_adjunto = null;
            $acta_evaluacion->save();


            DB::commit();

            return redirect()->route('actas_evaluaciones_ubs.show', $acta_evaluacion->id)->with('error-message','El adjunto del acta N° ' . $acta_evaluacion->numero_acta . ' de ' . $acta_evaluacion->curso->nombre_fantasia . ' del módulo ' . $acta_evaluacion->modulo->nombre_fantasia . ' fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('actas_evaluaciones_ubs.index', $acta_evaluacion->curso_id)->with('error-message', $e->getMessage());
        }
    }
}
