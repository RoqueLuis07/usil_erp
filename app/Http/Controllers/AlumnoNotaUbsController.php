<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\Models\AlumnoNotaUbs;
use App\Models\AlumnoPuntajeUbs;
use App\Models\Curso;
use App\Models\CursoModulo;
use App\Models\Modulo;
use App\Models\Modalidad;
use App\Models\Alumno;
use App\Models\EvaluacionUbs;
use App\Models\ActaEvaluacionUbs;
use App\Models\ActaEvaluacionAlumnoUbs;
use App\Models\Escala;
use App\Models\InscripcionModulo;

class AlumnoNotaUbsController extends Controller
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

    public function show($curso_id)
    {
        $this->authorize('ver_notas_maestrias_ubs');

        try {
            $maestria = Curso::with(['puntajes', 'notas', 'modulos'])->findOrFail($curso_id);

            $puntajes = $maestria->puntajes->groupBy('alumno_id');
            $notas = $maestria->notas->groupBy('alumno_id');

            return view('ubs/maestrias/alumnos_notas/show')->with(compact('maestria', 'puntajes', 'notas'));
        } catch (\Exception $e) {
            return redirect()->route('maestrias.index')->with('error-message', $e->getMessage());
        }
    }

    public function create($curso_id, $modulo_id, $evaluacion_id)
    {
        $this->authorize('crear_notas_maestrias_ubs');

        try {
            $maestria = CursoModulo::where('curso_id', $curso_id)->where('modulo_id', $modulo_id)->first();
            $modalidades = Modalidad::where('estado', 'AC')->get();
            $evaluacion = EvaluacionUbs::findOrFail($evaluacion_id);

            if ($evaluacion_id == 1) {
                $alumnos = Alumno::select(['alumnos.*'])
                ->whereHas('inscripcionesModulos', function ($query) use ($curso_id, $modulo_id) {
                    $query->where('curso_id', $curso_id)
                        ->where('modulo_id', $modulo_id)
                        ->whereIn('estado', ['MA', 'EC']);
                })
                ->groupBy('alumnos.id')
                ->orderBy('primer_apellido', 'asc')
                ->get();
            } else if ($evaluacion_id == 2) {
                $alumnos = Alumno::select(['alumnos.*'])
                ->whereHas('actaEvaluacionAlumnoUbs', function ($query) use ($curso_id, $modulo_id) {
                    $query->whereHas('acta', function ($query) use ($curso_id, $modulo_id) {
                        $query->where('tipo', 'O')
                            ->where('curso_id', $curso_id)
                            ->where('modulo_id', $modulo_id);
                    });
                })
                ->groupBy('alumnos.id')
                ->orderBy('primer_apellido', 'asc')
                ->get();
            } else if ($evaluacion_id == 3) {
                $alumnos = Alumno::select(['alumnos.*'])
                ->whereHas('actaEvaluacionAlumnoUbs', function ($query) use ($curso_id, $modulo_id) {
                    $query->whereHas('acta', function ($query) use ($curso_id, $modulo_id) {
                        $query->where('tipo', 'C')
                            ->where('curso_id', $curso_id)
                            ->where('modulo_id', $modulo_id);
                    });
                })
                ->groupBy('alumnos.id')
                ->orderBy('primer_apellido', 'asc')
                ->get();
            } else if ($evaluacion_id == 4) {
                $alumnos = Alumno::select(['alumnos.*'])
                ->whereHas('actaEvaluacionAlumnoUbs', function ($query) use ($curso_id, $modulo_id) {
                    $query->whereHas('acta', function ($query) use ($curso_id, $modulo_id) {
                        $query->where('tipo', 'E')
                            ->where('curso_id', $curso_id)
                            ->where('modulo_id', $modulo_id);
                    });
                })
                ->groupBy('alumnos.id')
                ->orderBy('primer_apellido', 'asc')
                ->get();
            }

            return view('ubs/maestrias/alumnos_notas/create')->with(compact('maestria', 'alumnos', 'evaluacion'));
        } catch (\Exception $e) {
            return redirect()->route('maestrias.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request, $curso_id, $modulo_id, $evaluacion_id)
    {
        $this->authorize('crear_notas_maestrias_ubs');

        if ($request->evaluacion == 1) {
            $requerido = 'nullable';
            $puntos_req = 'required_if:detalles.*.estado,AUSENTE';
        } else {
            $requerido = 'required';
            $puntos_req = 'required_if:detalles.*.estado,PRESENTE';
        }

        $request->validate([
            'fecha' => ['required', 'date'],

            'detalles' => ['required', 'array'],
            'detalles.*.alumno' => ['required', 'numeric'],
            'detalles.*.estado' => $requerido,
            'detalles.*.puntos' => ['nullable', $puntos_req, 'min:0', 'max:100'],
        ]);

        DB::beginTransaction();

        try {
            $cargado = AlumnoPuntajeUbs::where('curso_id', $curso_id)->where('modulo_id', $modulo_id)->where('evaluacion_id', $evaluacion_id)->exists();
            if ($cargado) {
                return back()->with('error-message', 'No es posible cargar los puntajes de los alumnos, ya fue cargado previamente.');
            }

            $maestria = Curso::findOrFail($curso_id);

            foreach ($request->detalles as $detalle) {
                $bandera = 0;
                if ($detalle['puntos'] == null && $detalle['estado'] == null) {
                    $bandera = 1;
                }

                if ($bandera == 0) {
                    $alumno_puntaje = new AlumnoPuntajeUbs();
                    $alumno_puntaje->alumno_id = $detalle['alumno'];
                    $alumno_puntaje->curso_id = $curso_id;
                    $alumno_puntaje->modulo_id = $modulo_id;
                    $alumno_puntaje->evaluacion_id = $evaluacion_id;
                    if ($alumno_puntaje->evaluacion_id != 1) {
                        $alumno_puntaje->observaciones = removeAccents(Str::upper($detalle['estado']));
                    }
                    if ($alumno_puntaje->observaciones == 'AUSENTE') {
                        $alumno_puntaje->puntos_obtenidos = 0;
                        $nota_ausente = 'AUSENTE';
                    } else {
                        $alumno_puntaje->puntos_obtenidos = $detalle['puntos'];
                        $nota_ausente = '';
                    }
                    $alumno_puntaje->cargado_por_id = Auth::id();
                    $alumno_puntaje->save();

                    if ($alumno_puntaje->puntos_obtenidos >= 0) {
                        $evaluacion = EvaluacionUbs::findOrFail($alumno_puntaje->evaluacion_id);
                        if ($evaluacion->tipo_evaluacion_id == 2 || $evaluacion->tipo_evaluacion_id == 3 || $evaluacion->tipo_evaluacion_id == 4) {
                            $procesos = AlumnoPuntajeUbs::where('alumno_id', $alumno_puntaje->alumno_id)->where('curso_id', $alumno_puntaje->curso_id)->where('modulo_id', $alumno_puntaje->modulo_id)->where('evaluacion_id', 1)->get();
                            $total_puntos_proceso = 0;
                            foreach ($procesos as $proceso) {
                                $puntos_proceso = $proceso->puntos_obtenidos * ($proceso->evaluacion->valor_porcentual / 100);
                                $total_puntos_proceso = $total_puntos_proceso + $puntos_proceso;
                            }

                            if ($evaluacion->tipo_evaluacion_id == 2) {
                                $puntaje_obtenido = $alumno_puntaje->puntos_obtenidos;
                                $total_puntos_final = $puntaje_obtenido * ($evaluacion->valor_porcentual / 100);
                                $nombre_evaluacion = 'ORDINARIO';
                                $acta = ActaEvaluacionUbs::with('alumnos')->where('tipo', 'O')->where('curso_id', $alumno_puntaje->curso_id)->where('modulo_id', $alumno_puntaje->modulo_id)->first();
                            }

                            if ($evaluacion->tipo_evaluacion_id == 3) {
                                $puntaje_obtenido = $alumno_puntaje->puntos_obtenidos;
                                $total_puntos_final = $puntaje_obtenido * ($evaluacion->valor_porcentual / 100);
                                $nombre_evaluacion = 'COMPLEMENTARIO';
                                $acta = ActaEvaluacionUbs::with('alumnos')->where('tipo', 'C')->where('curso_id', $alumno_puntaje->curso_id)->where('modulo_id', $alumno_puntaje->modulo_id)->first();
                            }

                            if ($evaluacion->tipo_evaluacion_id == 4) {
                                $puntaje_obtenido = $alumno_puntaje->puntos_obtenidos;
                                $total_puntos_final = $puntaje_obtenido * ($evaluacion->valor_porcentual / 100);
                                $nombre_evaluacion = 'EXTRAORDINARIO';
                                $acta = ActaEvaluacionUbs::with('alumnos')->where('tipo', 'E')->where('curso_id', $alumno_puntaje->curso_id)->where('modulo_id', $alumno_puntaje->modulo_id)->first();
                            }

                            $total_puntos_alumno = $total_puntos_proceso + $total_puntos_final;

                            $curso = Curso::findOrFail($alumno_puntaje->curso_id);
                            $escala = Escala::with('escalaDetalles')->where('programa_id', $curso->programa_id)->first();

                            foreach ($escala->escalaDetalles as $detalle) {
                                if ($total_puntos_alumno >= $detalle->punto_minimo && $total_puntos_alumno <= $detalle->punto_maximo) {
                                    $nota = $detalle->nota;
                                }
                            }

                            if ($nota_ausente == 'AUSENTE') {
                                if ($evaluacion->tipo_evaluacion_id == 4) {
                                    $nota = 1;
                                } else {
                                    $nota = $nota_ausente;
                                }
                            }

                            $old_alumno_nota = AlumnoNotaUbs::where('alumno_id', $alumno_puntaje->alumno_id)->where('curso_id', $alumno_puntaje->curso_id)->where('modulo_id', $alumno_puntaje->modulo_id)->first();
                            if (!$old_alumno_nota) {
                                $alumno_nota = new AlumnoNotaUbs();
                                $alumno_nota->alumno_id = $alumno_puntaje->alumno_id;
                                $alumno_nota->curso_id = $alumno_puntaje->curso_id;
                                $alumno_nota->modulo_id = $alumno_puntaje->modulo_id;
                                $alumno_nota->evaluacion_id = $alumno_puntaje->evaluacion_id;
                                $alumno_nota->calificacion = $nota;
                                $alumno_nota->evaluacion = $nombre_evaluacion;
                                $alumno_nota->save();
                            } else {
                                $old_alumno_nota->calificacion = $nota;
                                $old_alumno_nota->save();
                            }

                            $acta_detalle = ActaEvaluacionAlumnoUbs::where('acta_evaluacion_id', $acta->id)->where('alumno_id', $alumno_puntaje->alumno_id)->first();
                            if ($acta_detalle) {
                                $acta_detalle->puntos_examen = $total_puntos_final;
                                $acta_detalle->calificacion = $nota;
                                $acta_detalle->save();
                            }

                            $curso = Curso::findOrFail($alumno_puntaje->curso_id);
                            $inscripcion = InscripcionModulo::where('alumno_id', $alumno_puntaje->alumno_id)->where('curso_id', $alumno_puntaje->curso_id)->where('modulo_id', $alumno_puntaje->modulo_id)->first();
                            if ($nota != 'AUSENTE') {
                                if ($nota != 1) {
                                    $inscripcion->estado = 'AP';
                                    $inscripcion->save();

                                    $orden_actual = CursoModulo::where('curso_id', $alumno_puntaje->curso_id)->where('modulo_id', $alumno_puntaje->modulo_id)->orderBy('id', 'asc')->first()->orden;
                                    $orden_nuevo = $orden_actual + 1;

                                    $curso_modulo = CursoModulo::where('curso_id', $alumno_puntaje->curso_id)->where('orden', $orden_nuevo)->orderBy('id', 'asc')->first();
                                    if ($curso_modulo) {
                                        $nueva_inscripcion = new InscripcionModulo();
                                        $nueva_inscripcion->alumno_id = $alumno_puntaje->alumno_id;
                                        $nueva_inscripcion->curso_id = $alumno_puntaje->curso_id;
                                        $nueva_inscripcion->modulo_id = $curso_modulo->modulo_id;
                                        $nueva_inscripcion->save();
                                    }

                                } else {
                                    if ($evaluacion->tipo_evaluacion_id == 4) {
                                        $inscripcion->estado = 'RE';
                                        $inscripcion->save();
                                    }
                                }
                            } else {
                                if ($evaluacion->tipo_evaluacion_id == 4) {
                                    $inscripcion->estado = 'RE';
                                    $inscripcion->save();
                                }
                            }
                        }
                    }
                }
            }

            $evaluacion = EvaluacionUbs::findOrFail($alumno_puntaje->evaluacion_id);
            $curso = Curso::findOrFail($curso_id);
            $modulo = Modulo::findOrFail($modulo_id);

            DB::commit();

            if ($request->generado_docente == 'SI') {
                return redirect()->route('pantallas_docentes.index', Auth::id())->with('success-message', 'La evaluación ' . $evaluacion->nombre . ' del módulo ' . $modulo->nombre_fantasia . ' fue cargada exitosamente.');
            } else {
                return redirect()->route('alumnos_notas_ubs.show', $curso->id)->with('success-message', 'La evaluación ' . $evaluacion->nombre . ' del módulo ' . $modulo->nombre_fantasia . ' fue cargada exitosamente.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('maestrias.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_evaluaciones($curso_id, $modulo_id) {

        $this->authorize('crear_notas_maestrias_ubs');

        try {
            $ids = collect(); //coleccion para los id de evaluaciones que tenemos que mostrar
            $ids->push(1); //agregamos proceso

            if (AlumnoPuntajeUbs::where('modulo_id', $modulo_id)->where('curso_id', $curso_id)->where('evaluacion_id', 1)->exists()) {
                $ids->forget(0);
            }

            if (ActaEvaluacionUbs::with('alumnos')->where('tipo', 'O')->where('curso_id', $curso_id)->where('modulo_id', $modulo_id)->first()) {
                if (!AlumnoPuntajeUbs::where('modulo_id', $modulo_id)->where('curso_id', $curso_id)->where('evaluacion_id', 2)->exists()) {
                    $ids->push(2);
                }
            }
            if (ActaEvaluacionUbs::with('alumnos')->where('tipo', 'C')->where('curso_id', $curso_id)->where('modulo_id', $modulo_id)->first()) {
                if (!AlumnoPuntajeUbs::where('modulo_id', $modulo_id)->where('curso_id', $curso_id)->where('evaluacion_id', 3)->exists()) {
                    $ids->push(3);
                }
            }
            if (ActaEvaluacionUbs::with('alumnos')->where('tipo', 'E')->where('curso_id', $curso_id)->where('modulo_id', $modulo_id)->first()) {
                if (!AlumnoPuntajeUbs::where('modulo_id', $modulo_id)->where('curso_id', $curso_id)->where('evaluacion_id', 4)->exists()) {
                    $ids->push(4);
                }
            }

            $todos = ActaEvaluacionUbs::whereHas('alumnos', function ($query) {
                $query->where('puntos_examen', '!=', null)
                    ->where('calificacion', '!=', null);
            })->where('tipo', 'E')->where('curso_id', $curso_id)->where('modulo_id', $modulo_id)->exists();
            if ($todos) {
                return response()->json([
                    'message' => 'Todas las actas de evaluaciones ya fueron generadas en el módulo seleccionado.',
                ]);
            }

            if ($ids->isEmpty()) {
                return response()->json([
                    'message' => 'El acta de evaluación debe ser generado previamente para agregar las puntuaciones de los exámenes.',
                ]);
            }

            $evaluaciones = EvaluacionUbs::whereIn('id', $ids)->where('estado', 'AC')->orderBy('id', 'asc')->get();

            return response()->json([
                'evaluaciones' => $evaluaciones,
            ]);

        } catch (\Exception $e) {
            return redirect()->route('maestrias.index')->with('error-message', $e->getMessage());
        }
    }
}
