<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\Models\Docente;
use App\Models\Semestre;
use App\Models\SemestreMalla;
use App\Models\SemestreMallaMateria;
use App\Models\SemestreMallaMateriaHorario;
use App\Models\DiaSemana;
use App\Models\Materia;
use App\Models\Modalidad;
use App\Models\ClaseMateria;
use App\Models\AlumnoAsistencia;
use App\Models\AlumnoPuntaje;
use App\Models\ActaEvaluacion;
use App\Models\ActaEvaluacionAlumno;
use App\Models\Alumno;
use App\Models\Carrera;
use App\Models\Evaluacion;
use App\Models\ExtensionUniversitaria;
use App\Models\TipoExtensionUniversitaria;
use App\Models\NoticiaAviso;
use App\Models\Encuesta;
use App\Models\DocenteSalario;

class PantallaDocenteController extends Controller
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

    public function index(Request $request, $id)
    {
        $this->authorize('ver_dashboard_docentes_pantalla');

        try {
            $hoy = Carbon::today();
            $semestre = Semestre::whereDate('fecha_inicio', '<=', $hoy)
                            ->whereDate('fecha_fin', '>=', $hoy)
                            ->where('estado', 'AC')
                            ->orderBy('id', 'asc')
                            ->first();
            $periodo_activo = $semestre ? $semestre->id : null;
            $docente = Docente::where('usuario_id', Auth::id())->first();

            $dias_semana = DiaSemana::get();
            $horarios_clases = collect();
            $horarios_clases_cpel = collect();
            $horarios = collect();
            $materias = collect();
            $carreras = collect();
            $cpel = 'NO';
            $ga = 'NO';

            $semestre_mallas = SemestreMalla::with(['semestreMallaMaterias' => function ($query) use ($docente) {
                $query->where('docente_id', $docente->id);
            }])
                ->where('semestre_id', $periodo_activo)
                ->get();

            foreach ($semestre_mallas as $sm) {
                if ($sm->semestreMallaMaterias->count() > 0) {
                    $carreras->push($sm->malla->carrera);
                    if ($sm->malla->carrera->programa_id == 2) {
                        $cpel = 'SI';
                    } else if ($sm->malla->carrera->programa_id == 1) {
                        $ga = 'SI';
                    }
                    foreach ($sm->semestreMallaMaterias as $smm) {
                        if (!str_contains($smm->materia->nombre_real, 'TRABAJO FINAL DE GRADO') || !str_contains($smm->materia->nombre_fantasia, 'TRABAJO FINAL DE GRADO')) {
                            $materias->push($smm);
                        }
                    }
                }
            }

            $carreras = $carreras->unique('id');

            $horarios_clases->push(
                Carbon::createFromTime(8, 0),
                Carbon::createFromTime(9, 0),
                Carbon::createFromTime(10, 0),
                Carbon::createFromTime(11, 0),
                Carbon::createFromTime(12, 0),
                Carbon::createFromTime(13, 0),
                Carbon::createFromTime(14, 0),
            );

            $horarios_clases_cpel->push(
                Carbon::createFromTime(18, 0),
                Carbon::createFromTime(20, 0),
                Carbon::createFromTime(22, 0)
            );

            $materias = $materias->unique('materia_id');

            foreach ($materias as $materia) {
                $dias_horarios = SemestreMallaMateriaHorario::where('semestre_malla_materia_id', $materia->id)->get();
                foreach ($dias_horarios as $dia_horario) {
                    if ($dia_horario->dia_semana_id) {
                        $datos_horario = [
                            'materia_id' => $materia->materia_id,
                            'materia' => $materia->materia->nombre_fantasia,
                            'dia' => $dia_horario->diaSemana->nombre,
                            'hora_inicio' => $dia_horario->hora_inicio,
                            'hora_fin' => $dia_horario->hora_fin,
                            'aula' => $materia->aula
                        ];
                        $horarios->push($datos_horario);
                    }
                }
            }

            $horarios = $horarios->sortBy(function ($horario) {
                $dias_orden = [
                    'LUNES' => 1,
                    'MARTES' => 2,
                    'MIERCOLES' => 3,
                    'JUEVES' => 4,
                    'VIERNES' => 5,
                    'SABADO' => 6,
                    'DOMINGO' => 7,
                ];
                return $dias_orden[$horario['dia']];
            });
            $horarios = $horarios->values();

            $noticias = NoticiaAviso::where('estado', 'PU')->where('tipo', 'NO')->orderBy('destacado', 'desc')->limit(5)->get();

            return view('pantallas_docentes/index')->with(compact('docente', 'periodo_activo', 'materias', 'dias_semana', 'horarios_clases', 'horarios_clases_cpel', 'horarios', 'cpel', 'ga', 'carreras', 'noticias'));
        } catch (\Exception $e) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login')->with('error-message', 'Ocurrió un error durante el inicio de sesión, vuelva a intentarlo. Si el error persiste contactar con el Dpto. Académico');
        }
    }

    public function generate_clases($usuario, $materia, $semestre)
    {
        $this->authorize('crear_clases_docentes_pantalla');

        try {
            $docente = Docente::where('usuario_id', $usuario)->first();
            $materia = Materia::findOrFail($materia);
            $semestre = Semestre::findOrFail($semestre);
            $modalidades = Modalidad::where('estado', 'AC')->get();

            $clase = ClaseMateria::whereDate('fecha_hora', Carbon::today())
                                    ->where('docente_id', $docente->id)
                                    ->where('materia_id', $materia->id)
                                    ->where('semestre_id', $semestre->id)
                                    ->first();
            if ($clase) {
                return redirect()->route('pantallas_docentes.index', Auth::id())->with('error-message', 'No es posible generar más de un registro de cátedra por día en la misma materia.');
            }

            if (ActaEvaluacion::where('materia_id', $materia->id)->where('semestre_id', $semestre->id)->exists()) {
                return redirect()->route('pantallas_docentes.index', Auth::id())->with('error-message', 'No se puede generar registros de cátedra, ya existe un acta de evaluación generado.');
            }

            Carbon::setLocale('es');
            $dia_hoy = removeAccents(Str::upper(Carbon::now()->isoFormat('dddd')));

            $semestre_mallas = SemestreMalla::with(['semestreMallaMaterias' => function ($query) use ($docente, $materia) {
                $query->where('docente_id', $docente->id)
                    ->where('materia_id', $materia->id);
            }])
                ->where('semestre_id', $semestre->id)
                ->get();

			$fecha_hoy = Carbon::now()->format('Y-m-d');
			$fecha_default = Carbon::now()->format('Y-m-d H:i:s');
            $cantidad_horas = 1;
            foreach ($semestre_mallas as $sm) {
                if ($sm->semestreMallaMaterias->count() > 0) {
                    $modalidad_encontrada = $sm->malla->carrera->modalidad_id;
                    foreach ($sm->semestreMallaMaterias as $smm) {
                        if ($materia->id == $smm->materia_id) {
                            $dias_horarios = SemestreMallaMateriaHorario::where('semestre_malla_materia_id', $smm->id)->get();
                            foreach ($dias_horarios as $dia_horario) {
                                if ($dia_horario->dia_semana_id) {
                                    if ($dia_horario->diaSemana->nombre == $dia_hoy) {
                                        $hora_inicio = Carbon::createFromFormat('H:i:s', $dia_horario->hora_inicio);
                                        $hora_fin = Carbon::createFromFormat('H:i:s', $dia_horario->hora_fin);
                                        $cantidad_horas = $hora_inicio->diffInHours($hora_fin);
										$fecha_default = Carbon::createFromFormat('Y-m-d H:i:s', $fecha_hoy . ' ' . $dia_horario->hora_inicio)->format('Y-m-d H:i:s');
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $clase = ClaseMateria::where('materia_id', $materia->id)->where('semestre_id', $semestre->id)->orderBy('id', 'desc')->first();
            if ($clase) {
                if ($clase->alumnoAsistencias->count() == 0) {
                    return back()->with('error-message', 'No se puede crear una clase, debe completar la asistencia de la anterior para continuar.');
                }
            }

            return view('pantallas_docentes/clases/create')->with(compact('materia', 'semestre', 'modalidades', 'docente', 'cantidad_horas', 'fecha_default', 'modalidad_encontrada'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_docentes.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function charge_asistencias($usuario, $materia, $semestre)
    {
        $this->authorize('crear_asistencias_docentes_pantalla');

        try {
            $docente = Docente::where('usuario_id', $usuario)->first();
            $materia = Materia::findOrFail($materia);
            $semestre = Semestre::findOrFail($semestre);

            $clase = ClaseMateria::where('materia_id', $materia->id)->where('semestre_id', $semestre->id)->orderBy('id', 'desc')->first();
            $asistencia_cargada = AlumnoAsistencia::where('clase_materia_id', $clase->id)->exists();

            if (ActaEvaluacion::where('materia_id', $materia->id)->where('semestre_id', $semestre->id)->exists()) {
                return redirect()->route('pantallas_docentes.index', Auth::id())->with('error-message', 'No se puede generar asistencias de clase, ya existe un acta de evaluación generado.');
            }

            if ($clase && !$asistencia_cargada) {
                $alumnos = Alumno::select(['alumnos.*'])
                    ->whereHas('matriculaciones', function ($query) use ($semestre, $materia) {
                        $query->where('semestre_id', $semestre->id)
                            ->whereHas('inscripciones', function ($query) use ($materia) {
                                $query->where('materia_id', $materia->id)
                                    ->whereIn('estado', ['MA', 'EC']);
                            });
                    })
                    ->groupBy('alumnos.id')
                    ->get();

                return view('pantallas_docentes/asistencias/create')->with(compact('docente', 'materia', 'semestre', 'alumnos', 'clase'));
            } else {
                return back()->with('error-message', 'No se puede cargar las asistencias, debe generar una nueva clase para continuar.');
            }
        } catch (\Exception $e) {
            return redirect()->route('pantallas_docentes.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function charge_evaluaciones($usuario, $materia, $carrera, $semestre, $tipo)
    {
        $this->authorize('crear_evaluaciones_docentes_pantalla');

        try {
            $docente = Docente::where('usuario_id', $usuario)->first();
            $materia = Materia::findOrFail($materia);
            $carrera = Carrera::findOrFail($carrera);
            $semestre = Semestre::findOrFail($semestre);
            $evaluacion = Evaluacion::where('tipo_evaluacion_id', $tipo)->first();

            $puntaje_minimo_requerido = $evaluacion->puntaje_minimo_requerido;

            if ($tipo < 3) {
                $alumnos_obtenidos = Alumno::select([
                    'alumnos.*'
                ])
                    ->whereHas('matriculaciones', function ($query) use ($semestre, $materia, $carrera) {
                        $query->where('semestre_id', $semestre->id)
                            ->where('carrera_id', $carrera->id)
                            ->whereHas('inscripciones', function ($query) use ($materia) {
                                $query->where('materia_id', $materia->id)
                                    ->where('estado', 'EC');
                            });
                    })
                    ->groupBy('alumnos.id')
                    ->get();
            } else {
                if ($tipo != 3) {
                    $subquery = DB::table('alumnos_puntajes as ap')
                        ->select('ap.alumno_id')
                        ->join('evaluaciones as e', 'e.id', '=', 'ap.evaluacion_id')
                        ->groupBy('ap.alumno_id')
                        ->havingRaw('SUM(CASE
						WHEN ap.evaluacion_id = 1 THEN ap.puntos_obtenidos * e.valor_porcentual / 100
						WHEN ap.evaluacion_id = 3 AND ap.puntos_obtenidos > 0 THEN ap.puntos_obtenidos * e.valor_porcentual / 100
						WHEN ap.evaluacion_id = 2 AND NOT EXISTS (
							SELECT 1
							FROM alumnos_puntajes as ap2
							WHERE ap2.alumno_id = ap.alumno_id
							AND ap2.evaluacion_id = 3
							AND ap2.puntos_obtenidos > 0
						) THEN ap.puntos_obtenidos * e.valor_porcentual / 100
						ELSE 0
						END) >=?', [$puntaje_minimo_requerido]);

                    $alumnos_obtenidos = Alumno::select([
                        'alumnos.*',
                        DB::raw('SUM(CASE
							WHEN ap.evaluacion_id = 1 THEN ap.puntos_obtenidos * e.valor_porcentual / 100
							WHEN ap.evaluacion_id = 3 AND ap.puntos_obtenidos > 0 THEN ap.puntos_obtenidos * e.valor_porcentual / 100
							WHEN ap.evaluacion_id = 2 AND NOT EXISTS (
								SELECT 1
								FROM alumnos_puntajes as ap2
								WHERE ap2.alumno_id = ap.alumno_id
								AND ap2.evaluacion_id = 3
								AND ap2.puntos_obtenidos > 0
							) THEN ap.puntos_obtenidos * e.valor_porcentual / 100
							ELSE 0
							END) AS puntos_obtenidos')
                    ])
                        ->join('alumnos_puntajes as ap', 'alumnos.id', '=', 'ap.alumno_id')
                        ->join('evaluaciones as e', 'ap.evaluacion_id', '=', 'e.id')
                        ->whereHas('matriculaciones', function ($query) use ($semestre, $materia, $carrera) {
                            $query->where('semestre_id', $semestre->id)
                                ->where('carrera_id', $carrera->id)
                                ->whereHas('inscripciones', function ($query) use ($materia) {
                                    $query->where('materia_id', $materia->id)
                                        ->where('estado', 'EC');
                                });
                        })
                        ->whereIn('alumnos.id', $subquery)
                        ->groupBy('alumnos.id')
                        ->get();
                } else {
                    $subquery = DB::table('alumnos_puntajes as ap')
                        ->select('ap.alumno_id')
                        ->join('evaluaciones as e', 'e.id', '=', 'ap.evaluacion_id')
                        ->groupBy('ap.alumno_id')
                        ->havingRaw('SUM(CASE
						WHEN ap.evaluacion_id = 1 THEN ap.puntos_obtenidos * e.valor_porcentual / 100
						WHEN ap.evaluacion_id = 3 AND ap.puntos_obtenidos > 0 THEN ap.puntos_obtenidos * e.valor_porcentual / 100
						WHEN ap.evaluacion_id = 2 AND NOT EXISTS (
							SELECT 1
							FROM alumnos_puntajes as ap2
							WHERE ap2.alumno_id = ap.alumno_id
							AND ap2.evaluacion_id = 3
							AND ap2.puntos_obtenidos > 0
						) THEN ap.puntos_obtenidos * e.valor_porcentual / 100
						ELSE 0
						END) >= ?', [$puntaje_minimo_requerido]);

                    $alumnos_obtenidos = Alumno::select([
                        'alumnos.*',
                        DB::raw('SUM(CASE
							WHEN ap.evaluacion_id = 1 THEN ap.puntos_obtenidos * e.valor_porcentual / 100
							WHEN ap.evaluacion_id = 3 AND ap.puntos_obtenidos > 0 THEN ap.puntos_obtenidos * e.valor_porcentual / 100
							WHEN ap.evaluacion_id = 2 AND NOT EXISTS (
								SELECT 1
								FROM alumnos_puntajes as ap2
								WHERE ap2.alumno_id = ap.alumno_id
								AND ap2.evaluacion_id = 3
								AND ap2.puntos_obtenidos > 0
							) THEN ap.puntos_obtenidos * e.valor_porcentual / 100
							ELSE 0
							END) AS puntos_obtenidos')
                    ])
                        ->join('alumnos_puntajes as ap', 'alumnos.id', '=', 'ap.alumno_id')
                        ->join('evaluaciones as e', 'ap.evaluacion_id', '=', 'e.id')
                        ->whereHas('matriculaciones', function ($query) use ($semestre, $materia, $carrera) {
                            $query->where('semestre_id', $semestre->id)
                                ->where('carrera_id', $carrera->id)
                                ->whereHas('inscripciones', function ($query) use ($materia) {
                                    $query->where('materia_id', $materia->id)
                                        ->where('estado', 'EC');
                                });
                        })
                        // Excluimos a los alumnos que ya tienen un puntaje en la evaluación 2
                        ->whereDoesntHave('alumnoPuntajes', function ($query) use ($materia, $carrera, $semestre) {
                            $query->where('materia_id', $materia->id)
                                ->where('carrera_id', $carrera->id)
                                ->where('semestre_id', $semestre->id)
                                ->where('evaluacion_id', 2); // Excluir a los alumnos con puntaje en la evaluación 2
                        })
                        ->whereIn('alumnos.id', $subquery)
                        ->groupBy('alumnos.id')
                        ->get();
                }
            }

            $acta = ActaEvaluacion::with('alumnos')->where('semestre_id', $semestre->id)->where('carrera_id', $carrera->id)->where('materia_id', $materia->id);
            $cantidad_actas = $acta->count();

            $alumnos = collect(); //creamos coleccion para insertar los alumnos que deberian aparecer

            if ($cantidad_actas > 0) {
                if ($cantidad_actas != 1) {
                    foreach ($acta->orderBy('id', 'desc')->first()->alumnos->sortBy('alumno_id') as $acta_alumno) {
                        foreach ($alumnos_obtenidos as $alumno) {
                            if ($alumno->id == $acta_alumno->alumno_id) {
                                $alumnos->push($alumno);
                            }
                        }
                    }
                } else {
                    foreach ($acta->first()->alumnos->sortBy('alumno_id') as $acta_alumno) {
                        foreach ($alumnos_obtenidos as $alumno) {
                            if ($alumno->id == $acta_alumno->alumno_id) {
                                $alumnos->push($alumno);
                            }
                        }
                    }
                }
            } else {
                foreach ($alumnos_obtenidos as $alumno) {
                    $alumnos->push($alumno);
                }
            }

            if ($alumnos->count() != 0) {
                return view('pantallas_docentes/evaluaciones/create')->with(compact('docente', 'materia', 'carrera', 'semestre', 'evaluacion', 'alumnos'));
            } else {
                return back()->with('error-message', 'No hay alumnos inscriptos en la materia y carrera seleccionada.');
            }
        } catch (\Exception $e) {
            return redirect()->route('pantallas_docentes.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function obtener_evaluaciones($usuario, $materia, $carrera, $semestre)
    {
        $this->authorize('crear_evaluaciones_docentes_pantalla');

        $docente = Docente::where('usuario_id', $usuario)->first();
        $materia = Materia::findOrFail($materia);
        $carrera = Carrera::findOrFail($carrera);
        $semestre = Semestre::findOrFail($semestre);

        $ids = collect(); //coleccion para los id de evaluaciones que tenemos que mostrar
        $ids->push(1, 2); //agregamos proceso, parcial

        if (AlumnoPuntaje::where('materia_id', $materia->id)->where('carrera_id', $carrera->id)->where('semestre_id', $semestre->id)->where('evaluacion_id', 1)->exists()) {
            $ids->forget(0);
        }

        if (AlumnoPuntaje::where('materia_id', $materia->id)->where('carrera_id', $carrera->id)->where('semestre_id', $semestre->id)->where('evaluacion_id', 2)->exists()) {
            $ids->forget(1);
            $ids->push(3);
        }

        if (AlumnoPuntaje::where('materia_id', $materia->id)->where('carrera_id', $carrera->id)->where('semestre_id', $semestre->id)->where('evaluacion_id', 3)->exists()) {
            $ids->forget(2);
        }

        if (ActaEvaluacion::with('alumnos')->where('tipo', 'O')->where('carrera_id', $carrera->id)->where('materia_id', $materia->id)->where('semestre_id', $semestre->id)->first()) {
            $ids->forget([0, 1, 2]);
            if (!AlumnoPuntaje::where('materia_id', $materia->id)->where('carrera_id', $carrera->id)->where('semestre_id', $semestre->id)->where('evaluacion_id', 4)->exists()) {
                $ids->push(4);
            }
        }
        if (ActaEvaluacion::with('alumnos')->where('tipo', 'C')->where('carrera_id', $carrera->id)->where('materia_id', $materia->id)->where('semestre_id', $semestre->id)->first()) {
            $ids->forget([0, 1, 2, 3]);
            if (!AlumnoPuntaje::where('materia_id', $materia->id)->where('carrera_id', $carrera->id)->where('semestre_id', $semestre->id)->where('evaluacion_id', 5)->exists()) {
                $ids->push(5);
            }
        }
        if (ActaEvaluacion::with('alumnos')->where('tipo', 'E')->where('carrera_id', $carrera->id)->where('materia_id', $materia->id)->where('semestre_id', $semestre->id)->first()) {
            $ids->forget([0, 1, 2, 3, 4]);
            if (!AlumnoPuntaje::where('materia_id', $materia->id)->where('carrera_id', $carrera->id)->where('semestre_id', $semestre->id)->where('evaluacion_id', 6)->exists()) {
                $ids->push(6);
            }
        }

        if ($ids->isEmpty()) {
            return redirect()->route('pantallas_docentes.index', Auth::id())->with('error-message', 'El acta de evaluación debe ser generado previamente para agregar las puntuaciones de los exámenes.');
        }

        $evaluaciones = Evaluacion::whereIn('id', $ids)->where('estado', 'AC')->orderBy('id', 'asc')->get();

        return response()->json([
            'evaluaciones' => $evaluaciones,
        ]);
    }

    public function clases($id)
    {
        $this->authorize('ver_clases_docentes_pantalla');

        try {
            $hoy = Carbon::today();
            $semestre = Semestre::whereDate('fecha_inicio', '<=', $hoy)
                            ->whereDate('fecha_fin', '>=', $hoy)
                            ->where('estado', 'AC')
                            ->orderBy('id', 'asc')
                            ->first();
            $periodo_activo = $semestre ? $semestre->id : null;
            $docente = Docente::where('usuario_id', $id)->first();

            $clases = ClaseMateria::select('clases_materias.*')
                ->join('materias', 'clases_materias.materia_id', '=', 'materias.id')
                ->orderBy('clases_materias.fecha_hora', 'desc')
                ->orderBy('materias.nombre_fantasia', 'asc')
                ->with('materia')
                ->where('docente_id', $docente->id)
                ->where('semestre_id', $periodo_activo)
                ->get();

            $materias = collect();
            $modalidades = collect();

            foreach ($clases as $clase) {
                $materias->push($clase->materia->nombre_fantasia);
                $modalidades->push($clase->modalidad->nombre);
            }

            $materias = $materias->unique();
            $modalidades = $modalidades->unique();

            return view('pantallas_docentes/clases/index')->with(compact('docente', 'clases', 'materias', 'modalidades'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_docentes.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function show_clases($id)
    {
        $this->authorize('ver_clases_docentes_pantalla');

        try {
            $clase = ClaseMateria::with('alumnoAsistencias')->findOrFail($id);
            $docente = Docente::findOrFail($clase->docente_id);

            return view('pantallas_docentes/clases/show')->with(compact('clase', 'docente'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_docentes.clases.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function fechas_examenes($id)
    {
        $this->authorize('ver_fechas_examenes_docentes_pantalla');

        try {
            $hoy = Carbon::today();
            $semestre = Semestre::whereDate('fecha_inicio', '<=', $hoy)
                            ->whereDate('fecha_fin', '>=', $hoy)
                            ->where('estado', 'AC')
                            ->orderBy('id', 'asc')
                            ->first();
            $periodo_activo = $semestre ? $semestre->id : null;
            $docente = Docente::where('usuario_id', $id)->first();

            $fechas_examenes = SemestreMallaMateria::whereHas('semestreMalla', function ($query) use ($periodo_activo) {
                $query->where('semestre_id', $periodo_activo);
            })->where('docente_id', $docente->id)->where('estado', 'AC')->get();

            return view('pantallas_docentes/fechas_examenes')->with(compact('docente', 'fechas_examenes'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_docentes.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function asistencias_alumnos($id)
    {
        $this->authorize('crear_asistencias_docentes_pantalla');

        try {
            $hoy = Carbon::today();
            $semestre = Semestre::whereDate('fecha_inicio', '<=', $hoy)
                            ->whereDate('fecha_fin', '>=', $hoy)
                            ->where('estado', 'AC')
                            ->orderBy('id', 'asc')
                            ->first();
            $periodo_activo = $semestre ? $semestre->id : null;
            $docente = Docente::where('usuario_id', $id)->first();

            $materias = collect();

            $semestre_mallas = SemestreMalla::whereHas('semestreMallaMaterias', function ($query) use ($docente) {
                $query->where('docente_id', $docente->id);
            })->where('semestre_id', $periodo_activo)->get();

            foreach ($semestre_mallas as $semestre_malla) {
                $smms = SemestreMallaMateria::where('docente_id', $docente->id)->get();
            }

            foreach ($smms as $smm) {
                $materias->push($smm->materia);
            }

            foreach ($materias as $materia) {
                $alumnos = Alumno::select(['alumnos.*'])
                    ->whereHas('matriculaciones', function ($query) use ($periodo_activo, $materia) {
                        $query->where('semestre_id', $periodo_activo)
                            ->whereHas('inscripciones', function ($query) use ($materia) {
                                $query->where('materia_id', $materia->id);
                            });
                    })
                    ->groupBy('alumnos.id')
                    ->get();

                $materia->alumnos = $alumnos;
            }

            return view('pantallas_docentes/asistencias/index')->with(compact('semestre', 'docente', 'materias'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_docentes.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function show_asistencias_alumnos($id)
    {
        $this->authorize('crear_asistencias_docentes_pantalla');

        try {
            $hoy = Carbon::today();
            $semestre = Semestre::whereDate('fecha_inicio', '<=', $hoy)
                            ->whereDate('fecha_fin', '>=', $hoy)
                            ->where('estado', 'AC')
                            ->orderBy('id', 'asc')
                            ->first();
            $docente = Docente::where('usuario_id', Auth::id())->first();
            $materia = Materia::findOrFail($id);

            $materia->horas_desarrollo = ClaseMateria::where('materia_id', $materia->id)->where('semestre_id', $semestre->id)->sum('horas_desarrollo');
            $materia->total_clases = ClaseMateria::where('materia_id', $materia->id)->where('semestre_id', $semestre->id)->count();

            $asistencias = AlumnoAsistencia::where('materia_id', $materia->id)->where('semestre_id', $semestre->id)->get()->groupBy('alumno_id');
            foreach ($asistencias as $alumno_id => $asistencias_alumno) {
                $horas_asistidas = 0;
                $total_asistido = 0;
                foreach ($asistencias_alumno as $asistencia) {
                    if ($asistencia->estado == 'PR' || $asistencia->estado == 'AJ') {
                        $horas_asistidas += $asistencia->horas_desarrollo;
                        $total_asistido += 1;
                    }
                }

                $asistencia = $asistencias_alumno->first(); // Tomamos la primera instancia para actualizar
                $asistencia->horas_asistidas = $horas_asistidas;
                $asistencia->total_asistido = $total_asistido;
                $asistencia->porcentaje = number_format(($horas_asistidas / $materia->horas_desarrollo) * 100, 0, ',', '.');
            }


            return view('pantallas_docentes/asistencias/show')->with(compact('semestre', 'docente', 'materia', 'asistencias'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_docentes.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function puntajes_alumnos($id)
    {
        $this->authorize('crear_evaluaciones_docentes_pantalla');

        try {
            $hoy = Carbon::today();
            $semestre = Semestre::whereDate('fecha_inicio', '<=', $hoy)
                            ->whereDate('fecha_fin', '>=', $hoy)
                            ->where('estado', 'AC')
                            ->orderBy('id', 'asc')
                            ->first();
            $periodo_activo = $semestre ? $semestre->id : null;
            $docente = Docente::where('usuario_id', $id)->first();

            $materias = collect();

            $semestre_mallas = SemestreMalla::whereHas('semestreMallaMaterias', function ($query) use ($docente) {
                $query->where('docente_id', $docente->id);
            })->where('semestre_id', $periodo_activo)->get();

            foreach ($semestre_mallas as $semestre_malla) {
                $smms = SemestreMallaMateria::where('docente_id', $docente->id)->get();
            }

            foreach ($smms as $smm) {
                $materias->push($smm->materia);
            }

            foreach ($materias as $materia) {
                $alumnos = Alumno::select(['alumnos.*'])
                    ->whereHas('matriculaciones', function ($query) use ($periodo_activo, $materia) {
                        $query->where('semestre_id', $periodo_activo)
                            ->whereHas('inscripciones', function ($query) use ($materia) {
                                $query->where('materia_id', $materia->id);
                            });
                    })
                    ->groupBy('alumnos.id')
                    ->get();

                $materia->alumnos = $alumnos;
            }

            return view('pantallas_docentes/evaluaciones/index')->with(compact('semestre', 'docente', 'materias'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_docentes.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function show_puntajes_alumnos($id)
    {
        $this->authorize('crear_evaluaciones_docentes_pantalla');

        try {
            $hoy = Carbon::today();
            $semestre = Semestre::whereDate('fecha_inicio', '<=', $hoy)
                            ->whereDate('fecha_fin', '>=', $hoy)
                            ->where('estado', 'AC')
                            ->orderBy('id', 'asc')
                            ->first();
            $docente = Docente::where('usuario_id', Auth::id())->first();
            $materia = Materia::findOrFail($id);

            $puntajes_crudo = AlumnoPuntaje::where('materia_id', $materia->id)->where('semestre_id', $semestre->id)->orderBy('alumno_id')->get();
            $evaluaciones = Evaluacion::select('id', 'nombre')->where('estado', 'AC')->get();

            foreach ($puntajes_crudo as $puntaje) {
                $evaluacion_bandera = Evaluacion::findOrFail($puntaje->evaluacion_id);
                $puntaje->puntos_obtenidos = (int) round(($puntaje->puntos_obtenidos * $evaluacion_bandera->valor_porcentual) / $evaluacion_bandera->puntos);

                switch ($puntaje->evaluacion_id) {
                    case 4:
                        $evaluacion = 'ORDINARIO';
                        break;
                    case 5:
                        $evaluacion = 'COMPLEMENTARIO';
                        break;
                    case 6:
                        $evaluacion = 'EXTRAORDINARIO';
                        break;
                    default:
                        $evaluacion = 'NO';
                        break;
                }

                if ($evaluacion != 'NO') {
                    $acta = ActaEvaluacion::where('materia_id', $puntaje->materia_id)->where('semestre_id', $puntaje->semestre_id)->where('docente_id', $docente->id)->first();
                    $alumno_nota = ActaEvaluacionAlumno::where('acta_evaluacion_id', $acta->id)->where('alumno_id', $puntaje->alumno_id)->first();
                    if (!$alumno_nota->calificacion) {
                        $puntaje->puntos_obtenidos = 'A';
                        $puntaje->calificacion = 'A';
                    } else {
                        $puntaje->calificacion = $alumno_nota->calificacion;
                    }
                }
            }

            $puntajes = $puntajes_crudo->groupBy('alumno_id');

            return view('pantallas_docentes/evaluaciones/show')->with(compact('semestre', 'docente', 'materia', 'puntajes', 'evaluaciones'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_docentes.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function extensiones_universitarias($id)
    {
        $this->authorize('ver_extensiones_docentes_pantalla');

        try {
            $docente = Docente::where('usuario_id', $id)->firstOrFail();
            $extensiones = ExtensionUniversitaria::where('docente_id', $docente->id)->get();

            return view('pantallas_docentes/extensiones/index')->with(compact('docente', 'extensiones'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_docentes.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function create_extensiones_universitarias($id)
    {
        $this->authorize('crear_extensiones_docentes_pantalla');

        try {
            $docente = Docente::where('usuario_id', $id)->first();
            $tipos_extensiones = TipoExtensionUniversitaria::where('estado', 'AC')->get();
            $alumnos = Alumno::where('estado', 'AC')->orderBy('primer_nombre', 'asc')->get();
            return view('pantallas_docentes/extensiones/create')->with(compact('docente', 'tipos_extensiones', 'alumnos'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_docentes.extensiones.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function show_extensiones_universitarias($id)
    {
        $this->authorize('ver_extensiones_docentes_pantalla');

        try {
            $extension = ExtensionUniversitaria::with('extensionUniversitariaDetalles')->findOrFail($id);
            $docente = Docente::findOrFail($extension->docente_id);
            return view('pantallas_docentes/extensiones/show')->with(compact('extension', 'docente'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_docentes.extensiones.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function encuestas($id)
    {
        $this->authorize('ver_encuestas_docentes_pantalla');

        try {
            $docente = Docente::where('usuario_id', $id)->first();
            $encuestas = Encuesta::where('tipo', 'DO')->where('estado', 'PU')->get();

            return view('pantallas_docentes/encuestas')->with(compact('docente', 'encuestas'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_docentes.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function cobros($id)
    {
        $this->authorize('ver_cobros_docentes_pantalla');

        try {
            $docente = Docente::where('usuario_id', $id)->first();
            $cobros = DocenteSalario::where('docente_id', $docente->id)->where('estado', 'AP')->get();

            return view('pantallas_docentes/cobros')->with(compact('docente', 'cobros'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_docentes.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }
}
