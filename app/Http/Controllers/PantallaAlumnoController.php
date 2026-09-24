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

use App\Models\Alumno;
use App\Models\AlumnoAsistencia;
use App\Models\AlumnoPuntaje;
use App\Models\AlumnoNota;
use App\Models\Matriculacion;
use App\Models\Inscripcion;
use App\Models\Materia;
use App\Models\PagoMatriculacion;
use App\Models\Semestre;
use App\Models\SemestreMalla;
use App\Models\SemestreMallaMateria;
use App\Models\SemestreMallaMateriaHorario;
use App\Models\Evaluacion;
use App\Models\Malla;
use App\Models\MallaDetalle;
use App\Models\MallaEspejoDetalle;
use App\Models\ActaEvaluacion;
use App\Models\ActaEvaluacionAlumno;
use App\Models\ExtensionUniversitariaDetalle;
use App\Models\RequerimientoExtensionUniversitaria;
use App\Models\TipoExtensionUniversitaria;
use App\Models\ClaseMateria;
use App\Models\DiaSemana;
use App\Models\NoticiaAviso;
use App\Models\TipoSolicitud;
use App\Models\Solicitud;
use App\Models\MateriaSuficiencia;
use App\Models\Correlatividad;
use App\Models\Encuesta;
use App\Models\TipoTesis;
use App\Models\AreaTesis;
use App\Models\LineaTesis;
use App\Models\Docente;
use App\Models\InscripcionTemaTesis;
use App\Models\AnteproyectoTesis;
use App\Models\ProyectoTesis;
use App\Models\Tutoria;
use App\Models\TutoriaPrecio;
use App\Models\ExamenSuficiencia;
use App\Models\ExamenSuficienciaFechaSolicitud;
use App\Models\FechaDesmatriculacion;
use App\Traits\ResuelveCarreraSemestreAlumno;

class PantallaAlumnoController extends Controller
{
    use ResuelveCarreraSemestreAlumno;

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

    public function index (Request $request, $id)
    {
        $this->authorize('ver_dashboard_alumnos_pantalla');

        try {
            $hoy = Carbon::today();
            $semestre = Semestre::whereDate('fecha_inicio', '<=', $hoy)
                            ->whereDate('fecha_fin', '>=', $hoy)
                            ->where('estado', 'AC')
                            ->orderBy('id', 'asc')
                            ->first();
            $periodo_activo = $semestre ? $semestre->id : null;
            $alumno = Alumno::with(['alumnoNotas' => function ($query) use ($periodo_activo) {
                $query->whereIn('evaluacion', ['ORDINARIO', 'COMPLEMENTARIO', 'EXTRAORDINARIO'])
                    ->where('semestre_id', $periodo_activo)
                    ->orderBy('created_at', 'desc');
            }])->where('usuario_id', Auth::id())->first();
            foreach ($alumno->alumnoNotas as $nota) {
                $malla = Malla::with('mallaDetalles')->where('carrera_id', $nota->carrera_id)->first();
                foreach ($malla->mallaDetalles as $detalle) {
                    if ($nota->materia_id == $detalle->materia_id) {
                        $nota->semestre_materia = $detalle->semestre;
                    }
                }
            }
            $alumno->alumnoNotas = $alumno->alumnoNotas->sortBy('semestre_materia');

			$asistencias = AlumnoAsistencia::where('alumno_id', $alumno->id)
				->where('semestre_id', $periodo_activo)
				->distinct('materia_id')
				->get();

			$horas_asistidas = 0;
			$total_asistido = 0;

			foreach ($asistencias as $asistencia) {
				$asistencia->horas_desarrollo = ClaseMateria::where('materia_id', $asistencia->materia_id)
					->where('semestre_id', $asistencia->semestre_id)
					->sum('horas_desarrollo');

				$asistencia->total_clases = ClaseMateria::where('materia_id', $asistencia->materia_id)
					->where('semestre_id', $asistencia->semestre_id)
					->count();

				$asistencias_all = AlumnoAsistencia::where('alumno_id', $alumno->id)
					->where('semestre_id', $periodo_activo)
					->where('materia_id', $asistencia->materia_id)
					->get();

				foreach ($asistencias_all as $a) {
					if ($a->estado == 'PR' || $a->estado == 'AJ') {
						$horas_asistidas += $a->horas_desarrollo;
						$total_asistido += 1;
					}
				}

				if ($asistencia->horas_desarrollo > 0) {
					$asistencia->porcentaje = number_format(($horas_asistidas / $asistencia->horas_desarrollo) * 100, 0, ',', '.');
				} else {
					$asistencia->porcentaje = 0;
				}

				$asistencia->horas_asistidas = $horas_asistidas;
				$asistencia->total_asistido = $total_asistido;

				$horas_asistidas = 0;
				$total_asistido = 0;
			}

            $dias_semana = DiaSemana::get();
            $horarios_clases = collect();
            $horarios = collect();
            $semestre_malla_materias_ambos = collect();

            $solicitudes = Solicitud::where('alumno_id', $alumno->id)->where('semestre_id', $periodo_activo)->orderBy('fecha_solicitud', 'desc')->limit(5)->get();

            $matriculacion = Matriculacion::where('alumno_id', $alumno->id)->orderBy('id', 'desc')->first();
            if ($matriculacion) {
                $inscripciones = Inscripcion::where('matriculacion_id', $matriculacion->id)->whereHas('matriculacion', function ($query) use ($periodo_activo) {
                    $query->semeste_id = $periodo_activo;
                })/*whereIn('estado', ['MA', 'EC', ])*/->get();
                $malla = Malla::where('carrera_id', $matriculacion->carrera_id)->first();
                $semestre_malla = SemestreMalla::where('semestre_id', $matriculacion->semestre_id)->where('malla_id', $malla->id)->first();
                $semestre_malla_materias = SemestreMallaMateria::where('semestre_malla_id', $semestre_malla->id)->get();
                foreach ($semestre_malla_materias as $smm) {
                    $semestre_malla_materias_ambos->push($smm);
                }

                if ($matriculacion->carrera_siu_id) {
                    $malla_siu = Malla::where('carrera_id', $matriculacion->carrera_siu_id)->first();
                    $semestre_malla_siu = SemestreMalla::where('semestre_id', $matriculacion->semestre_id)->where('malla_id', $malla_siu->id)->first();
                    if ($semestre_malla_siu) {
                        $semestre_malla_materias_siu = SemestreMallaMateria::where('semestre_malla_id', $semestre_malla_siu->id)->get();
                    } else {
                        $semestre_malla_materias_siu = collect();
                    }

                    foreach ($semestre_malla_materias_siu as $smms) {
                        $semestre_malla_materias_ambos->push($smms);
                    }
                }

                if ($matriculacion->programa_id == 1) {
                    $horarios_clases->push(Carbon::createFromTime(8, 0),
                                            Carbon::createFromTime(9, 0),
                                            Carbon::createFromTime(10, 0),
                                            Carbon::createFromTime(11, 0),
                                            Carbon::createFromTime(12, 0),
                                            Carbon::createFromTime(13, 0),
                                            Carbon::createFromTime(14, 0),
                                            );
                } elseif ($matriculacion->programa_id == 2) {
                    $horarios_clases->push(Carbon::createFromTime(18, 0),
                                            Carbon::createFromTime(20, 0),
                                            Carbon::createFromTime(22, 0)
                                        );
                }

                if ($matriculacion->programa_id == 1 || $matriculacion->programa_id == 2) {
                    foreach ($inscripciones as $inscripcion) {
                        foreach ($semestre_malla_materias_ambos as $semestre_malla_materia) {
                            if ($inscripcion->materia_id == $semestre_malla_materia->materia_id) {
                                $dias_horarios = SemestreMallaMateriaHorario::where('semestre_malla_materia_id', $semestre_malla_materia->id)->get();
                                foreach ($dias_horarios as $dia_horario) {
                                    if ($dia_horario->dia_semana_id) {
                                        $datos_horarios = ['materia_id' => $semestre_malla_materia->materia_id,
                                                    'materia' => $semestre_malla_materia->materia->nombre_fantasia,
                                                    'dia' => $dia_horario->diaSemana->nombre,
                                                    'hora_inicio' => $dia_horario->hora_inicio,
                                                    'hora_fin' => $dia_horario->hora_fin,
                                                    'aula' => $semestre_malla_materia->aula];
                                        $horarios->push($datos_horarios);
                                    }
                                }
                            }
                        }
                    }
                    $horarios = $horarios->sortBy(function($horario) {
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
                }
            } else {
                $horarios_clases->push(Carbon::createFromTime(8, 0),
                                            Carbon::createFromTime(9, 0),
                                            Carbon::createFromTime(10, 0),
                                            Carbon::createFromTime(11, 0),
                                            Carbon::createFromTime(12, 0),
                                            Carbon::createFromTime(13, 0),
                                            Carbon::createFromTime(14, 0),);
            }

            $tutorias = Tutoria::with('horarios')->whereHas('alumnos', function ($query) use ($alumno) {
                $query->where('alumno_id', $alumno->id);
            })->where('semestre_id', $periodo_activo)->where('estado', '!=', 'FI')->get();

            $noticias = NoticiaAviso::where('estado', 'PU')->where('tipo', 'NO')->orderBy('destacado', 'desc')->limit(5)->get();
            $avisos = NoticiaAviso::where('estado', 'PU')->where('tipo', 'AV')->orderBy('destacado', 'desc')->limit(3)->get();

            $matriculacion = Matriculacion::where('alumno_id', $alumno->id)->where('semestre_id', $periodo_activo)->where('estado', 'AC')->orderBy('id', 'desc')->first();
            if ($matriculacion) {
                $pago_pendiente = PagoMatriculacion::where('matriculacion_id', $matriculacion->id)->whereIn('estado', ['PE', 'PA'])->orderBy('id', 'asc')->first();
                if ($pago_pendiente) {
                    $pago_pendiente->fecha_vencimiento = Carbon::parse($pago_pendiente->fecha_vencimiento)->format('d/m/Y');
                    $pago_pendiente->saldo = number_format($pago_pendiente->saldo, 0, ',', '.');
                    $pago_pendiente->monto = number_format($pago_pendiente->monto, 0, ',', '.');
                }
				$alumno->programa_id = $matriculacion->programa_id;
            } else {
                $pago_pendiente = null;
            }

            $fecha_examen_suficiencia = null;
            if ($semestre && $matriculacion) {
                if ($semestre->fecha_inicio <= Carbon::today() && $semestre->fecha_fin >= Carbon::today()) {
                    $fecha_examen_suficiencia = ExamenSuficienciaFechaSolicitud::where('semestre_id', $periodo_activo)
                    ->where('programa_id', $matriculacion->programa_id)
                    ->whereDate('fecha_inicio', '<=', Carbon::today())
                    ->whereDate('fecha_fin', '>=', Carbon::today())
                    ->where('estado', 'AC')->first();

                    if ($fecha_examen_suficiencia) {
                        $fecha_examen_suficiencia->fecha_inicio = Carbon::parse($fecha_examen_suficiencia->fecha_inicio)->format('d/m/Y');
                        $fecha_examen_suficiencia->fecha_fin = Carbon::parse($fecha_examen_suficiencia->fecha_fin)->format('d/m/Y');
                    }   
                }
            }

            return view('pantallas_alumnos/index')->with(compact('alumno', 'asistencias', 'solicitudes', 'dias_semana', 'horarios_clases', 'horarios', 'tutorias', 'noticias', 'avisos', 'pago_pendiente', 'fecha_examen_suficiencia'));
        } catch (\Exception $e) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login')->with('error-message', 'Ocurrió un error durante el inicio de sesión, vuelva a intentarlo. Si el error persiste contactar con Soporte IT');
        }
    }

    public function materias($id)
    {
        $this->authorize('ver_materias_alumnos_pantalla');

        try {
            $alumno = Alumno::where('usuario_id', $id)->first();
            $hoy = Carbon::today();
            $semestre = Semestre::whereDate('fecha_inicio', '<=', $hoy)
                            ->whereDate('fecha_fin', '>=', $hoy)
                            ->where('estado', 'AC')
                            ->orderBy('id', 'asc')
                            ->first();
            $periodo_activo = $semestre ? $semestre->id : null;

            if ($periodo_activo) {
                $matriculacion = Matriculacion::where('alumno_id', $alumno->id)->where('semestre_id', $periodo_activo)->first();
                if (!$matriculacion) {
                    return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', 'Debe estar matriculado e inscripto en el semestre para ver sus materias.');
                }

                $inscripciones = Inscripcion::where('matriculacion_id', $matriculacion->id)->get();
                if ($inscripciones) {
                    foreach ($inscripciones as $inscripcion) {
                        $semestre_malla_materia = SemestreMallaMateria::where('materia_id', $inscripcion->materia_id)->whereHas('semestreMalla', function ($query) use ($periodo_activo) {
                            $query->where('semestre_id', $periodo_activo);
                        })->first();
                        $inscripcion->url_plan_clase = $semestre_malla_materia ? $semestre_malla_materia->url_plan_clase : null;
                        $inscripcion->url_programa_clase = $semestre_malla_materia ? $semestre_malla_materia->url_programa_clase : null;


                        $inscripcion->puntaje_proceso = null;
                        $inscripcion->puntaje_parcial = null;
                        $inscripcion->puntaje_recuperatorio = null;
                        $inscripcion->puntaje_ordinario = null;
                        $inscripcion->puntaje_complementario = null;
                        $inscripcion->puntaje_extraordinario = null;

                        $puntajes = AlumnoPuntaje::where('alumno_id', $alumno->id)->where('materia_id', $inscripcion->materia_id)->where('semestre_id', $periodo_activo)->get();
                        if ($puntajes->count() > 0) {
                            foreach ($puntajes as $puntaje) {
                                switch ($puntaje->evaluacion_id) {
                                    case 1:
                                        $inscripcion->puntaje_proceso = $puntaje->puntos_obtenidos;
                                        break;
                                    case 2:
                                        $inscripcion->puntaje_parcial = $puntaje->puntos_obtenidos;
                                        break;
                                    case 3:
                                        $inscripcion->puntaje_recuperatorio = $puntaje->puntos_obtenidos;
                                        break;
                                    case 4:
                                        $inscripcion->puntaje_ordinario = $puntaje->puntos_obtenidos;
                                        break;
                                    case 5:
                                        $inscripcion->puntaje_complementario = $puntaje->puntos_obtenidos;
                                        break;
                                    case 6:
                                        $inscripcion->puntaje_extraordinario = $puntaje->puntos_obtenidos;
                                        break;
                                    default:
                                        break;
                                }
                            }
                        }

                        $inscripcion->nota_ordinario = null;
                        $inscripcion->nota_complementario = null;
                        $inscripcion->nota_extraordinario = null;

                        $notas = AlumnoNota::where('alumno_id', $alumno->id)->where('materia_id', $inscripcion->materia_id)->where('semestre_id', $periodo_activo)->get();
                        if ($notas->count() > 0) {
                            foreach ($notas as $nota) {
                                switch ($nota->evaluacion) {
                                    case 'ORDINARIO':
                                        $inscripcion->nota_ordinario = $nota->calificacion;
                                        break;
                                    case 'COMPLEMENTARIO':
                                        $inscripcion->nota_complementario = $nota->calificacion;
                                        break;
                                    case 'EXTRAORDINARIO':
                                        $inscripcion->nota_extraordinario = $nota->calificacion;
                                        break;
                                    default:
                                        break;
                                }
                            }
                        }

                        $horas_asistidas = 0;
                        $total_asistido = 0;
                        $inscripcion->porcentaje_asistencia = null;

                        $asistencias = AlumnoAsistencia::where('alumno_id', $alumno->id)->where('semestre_id', $periodo_activo)->where('materia_id', $inscripcion->materia_id)->get();
                        foreach ($asistencias as $asistencia) {
                            $asistencia->horas_desarrollo = ClaseMateria::where('materia_id', $asistencia->materia_id)
                                ->where('semestre_id', $asistencia->semestre_id)
                                ->sum('horas_desarrollo');

                            $asistencia->total_clases = ClaseMateria::where('materia_id', $asistencia->materia_id)
                                ->where('semestre_id', $asistencia->semestre_id)
                                ->count();

                            $asistencias_all = AlumnoAsistencia::where('alumno_id', $alumno->id)
                                ->where('semestre_id', $periodo_activo)
                                ->where('materia_id', $asistencia->materia_id)
                                ->get();

                            foreach ($asistencias_all as $a) {
                                if ($a->estado == 'PR' || $a->estado == 'AJ') {
                                    $horas_asistidas += $a->horas_desarrollo;
                                    $total_asistido += 1;
                                }
                            }

                            if ($asistencia->horas_desarrollo > 0) {
                                $inscripcion->porcentaje_asistencia = number_format(($horas_asistidas / $asistencia->horas_desarrollo) * 100, 0, ',', '.');
                            }

                            $horas_asistidas = 0;
                            $total_asistido = 0;
                        }
                    }
                }
            }

            return view('pantallas_alumnos/materias')->with(compact('alumno', 'inscripciones'));

        } catch (\Exception $e) {
            return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function fechas_examenes (Request $request, $id)
    {
        $this->authorize('ver_fechas_examenes_alumnos_pantalla');

        try {
            $hoy = Carbon::today();
            $semestre = Semestre::whereDate('fecha_inicio', '<=', $hoy)
                            ->whereDate('fecha_fin', '>=', $hoy)
                            ->where('estado', 'AC')
                            ->orderBy('id', 'asc')
                            ->first();
            $periodo_activo = $semestre ? $semestre->id : null;
            $alumno = Alumno::where('usuario_id', Auth::id())->first();

            $fechas_examenes = collect();

            if ($periodo_activo) {
                $matriculacion = Matriculacion::where('alumno_id', $alumno->id)->where('semestre_id', $periodo_activo)->first();
                if (!$matriculacion) {
                    return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', 'Debe estar matriculado e inscripto en el semestre para ver sus materias.');
                }

                $inscripciones = Inscripcion::where('matriculacion_id', $matriculacion->id)->get();
                if ($inscripciones) {
                    foreach ($inscripciones as $inscripcion) {
                        $semestre_malla_materia = SemestreMallaMateria::where('materia_id', $inscripcion->materia_id)->whereHas('semestreMalla', function ($query) use ($periodo_activo) {
                            $query->where('semestre_id', $periodo_activo);
                        })->first();

                        $fechas_examenes->push($semestre_malla_materia);
                    }
                }
            }


            return view('pantallas_alumnos/fechas_examenes')->with(compact('alumno', 'fechas_examenes'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function puntajes($id)
    {
        $this->authorize('ver_puntajes_alumnos_pantalla');

        try {
            $alumno = Alumno::where('usuario_id', $id)->first();

            $puntajes_nacionales = AlumnoPuntaje::where('alumno_id', $alumno->id)
                                            ->whereHas('carrera', function ($query) {
                                                $query->whereIn('programa_id', [1, 2, 3, 8]);
                                            })
                                            ->whereIn('evaluacion_id', [1, 2, 3])
                                            ->orderBy('created_at', 'desc')
                                            ->get();

            $puntajes_siu = AlumnoPuntaje::where('alumno_id', $alumno->id)
                                            ->whereHas('carrera', function ($query) {
                                                $query->where('programa_id', 4);
                                            })
                                            ->whereIn('evaluacion_id', [1, 2, 3])
                                            ->orderBy('created_at', 'desc')
                                            ->get();


            $hoy = Carbon::today();
            $semestre = Semestre::whereDate('fecha_inicio', '<=', $hoy)
                            ->whereDate('fecha_fin', '>=', $hoy)
                            ->where('estado', 'AC')
                            ->orderBy('id', 'asc')
                            ->first();
            $periodo_activo = $semestre ? $semestre->id : null;
            $evaluaciones = Evaluacion::whereIn('id', [1, 2, 3])->where('estado', 'AC')->get();

            return view('pantallas_alumnos/puntajes')->with(compact('alumno', 'puntajes_nacionales', 'puntajes_siu', 'periodo_activo', 'evaluaciones'));

        } catch (\Exception $e) {
            return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function calificaciones($id)
    {
        $this->authorize('ver_calificaciones_alumnos_pantalla');

        try {
            $alumno = Alumno::where('usuario_id', Auth::id())->first();
			$total_notas_nacional = 0;
            $total_materias_nacional = 0;
			$promedio_nacional = 0;
            $total_notas_siu = 0;
            $total_materias_siu = 0;
			$promedio_siu = 0;

            $matriculacion = Matriculacion::where('alumno_id', $alumno->id)->orderBy('id', 'desc')->first();
            $alumno->carrera_paraguay = $matriculacion->carrera->nombre_fantasia;
            $alumno->carrera_siu = $matriculacion->carreraSiu->nombre_fantasia;
            $alumno->programa_id = $matriculacion->programa_id;

            $alumno_notas_nacional = AlumnoNota::where('alumno_id', $alumno->id)
                                ->whereIn('evaluacion', ['ORDINARIO', 'COMPLEMENTARIO', 'EXTRAORDINARIO'])
                                ->whereHas('carrera', function ($query) {
                                        $query->whereIn('programa_id', [1, 2, 3, 8]);
                                    })
                                ->get();

            $alumno_notas_siu = AlumnoNota::where('alumno_id', $alumno->id)
                                ->whereIn('evaluacion', ['ORDINARIO', 'COMPLEMENTARIO', 'EXTRAORDINARIO'])
                                ->whereHas('carrera', function ($query) {
                                        $query->where('programa_id', 4);
                                    })
                                ->get();

            foreach ($alumno_notas_nacional as $nota) {
                $tipo = Str::substr($nota->evaluacion, 0, 1);
                $acta = ActaEvaluacion::whereHas('alumnos', function ($query) use ($nota) {
                    $query->where('alumno_id', $nota->alumno_id);
                })->where('tipo', $tipo)->where('carrera_id', $nota->carrera_id)->where('materia_id', $nota->materia_id)->where('semestre_id', $nota->semestre_id)->first();
                if ($acta) {
                    $nota->semestre_materia = $acta->materia_semestre;
                    $nota->numero_acta = $acta->numero_acta;
                    $nota->fecha_evaluacion = Carbon::parse($acta->fecha_evaluacion)->format('d/m/Y');
                    $acta_detalle = ActaEvaluacionAlumno::where('acta_evaluacion_id', $acta->id)->where('alumno_id', $nota->alumno_id)->first();
                    if ($acta_detalle) {
                        $nota->puntos_examen = $acta_detalle->puntos_examen;
                    }

                    if (is_numeric(trim($nota->calificacion))) {
                        $total_notas_nacional += $nota->calificacion;
                        $total_materias_nacional += 1;
                    }
                }
            }

            if ($total_notas_nacional > 0 && $total_materias_nacional > 0) {
				$promedio_nacional = number_format($total_notas_nacional / $total_materias_nacional, 2, '.', ',');
			}

            $alumno_notas_nacional = $alumno_notas_nacional->sortBy('semestre_materia');

            foreach ($alumno_notas_siu as $nota) {
                $tipo = Str::substr($nota->evaluacion, 0, 1);
                $acta = ActaEvaluacion::whereHas('alumnos', function ($query) use ($nota) {
                    $query->where('alumno_id', $nota->alumno_id);
                })->where('tipo', $tipo)->where('carrera_id', $nota->carrera_id)->where('materia_id', $nota->materia_id)->where('semestre_id', $nota->semestre_id)->first();
                if ($acta) {
                    $nota->semestre_materia = $acta->materia_semestre;
                    $nota->numero_acta = $acta->numero_acta;
                    $nota->fecha_evaluacion = Carbon::parse($acta->fecha_evaluacion)->format('d/m/Y');
                    $acta_detalle = ActaEvaluacionAlumno::where('acta_evaluacion_id', $acta->id)->where('alumno_id', $nota->alumno_id)->first();
                    if ($acta_detalle) {
                        $nota->puntos_examen = $acta_detalle->puntos_examen;
                    }

                    if (is_numeric(trim($nota->calificacion))) {
                        $total_notas_siu += $nota->calificacion;
                        $total_materias_siu += 1;
                    }
                }
            }

            $alumno_notas_siu = $alumno_notas_siu->sortBy('semestre_materia');

            $matriculacion = Matriculacion::where('alumno_id', $alumno->id)->orderBy('id', 'desc')->first();
            if (!$matriculacion->carrera_siu_id) {
                return back()->with('error-message', 'El alumno no se encuentra inscripto en SIU.');
            }

            $alumno->carrera_paraguay = $matriculacion->carrera->nombre_fantasia;
            $alumno->carrera_siu = $matriculacion->carreraSiu->nombre_fantasia;

            $malla_paraguay = Malla::with(['mallaDetalles' => function ($query) {
                $query->orderBy('semestre', 'asc')
                    ->with(['materia' => function ($q) {
                        $q->orderBy('nombre_fantasia', 'asc');
                    }]);
            }])->where('carrera_id', $matriculacion->carrera_id)->where('estado', 'AC')->orderBy('id', 'asc')->first();
            $malla_siu = Malla::with(['mallaDetalles' => function ($query) {
                $query->orderBy('semestre', 'asc')
                    ->with(['materia' => function ($q) {
                        $q->orderBy('nombre_fantasia', 'asc');
                    }]);
            }])->where('carrera_id', $matriculacion->carrera_siu_id)->where('estado', 'AC')->orderBy('id', 'asc')->first();

            $cantidad_materias_paraguay = $malla_paraguay->mallaDetalles->count();
            $cantidad_materias_siu = $malla_siu->mallaDetalles->count();

            $espejos_siu_ids = [];
            foreach ($malla_paraguay->mallaDetalles as $malla_paraguay_detalle) {
                $espejo = MallaEspejoDetalle::whereHas('mallaEspejo', function ($query) use ($malla_paraguay, $malla_siu) {
                    $query->where('malla_paraguay_id', $malla_paraguay->id)
                        ->where('malla_siu_id', $malla_siu->id)
                        ->where('estado', 'AC');
                })->where('materia_paraguay_id', $malla_paraguay_detalle->materia_id)->first();

                if ($espejo) {
                    $malla_paraguay_detalle->semestre_materia_siu = $espejo->mallaEspejo->mallaSiu->mallaDetalles()->where('materia_id', $espejo->materia_siu_id)->first()->semestre;
                    $malla_paraguay_detalle->materia_siu_id = $espejo->materia_siu_id;
                    $malla_paraguay_detalle->materia_siu = $espejo->materiaSiu->nombre_fantasia;
                    $malla_paraguay_detalle->espejo = true;
                    $espejos_siu_ids[] = $espejo->materia_siu_id;
                }
            }

            $equivalencias = collect();
            $paraguay_sin_espejo = collect();
            $siu_sin_espejo = collect();

            foreach ($malla_paraguay->mallaDetalles as $malla_detalle) {
                $nota_paraguay = $alumno->alumnoNotas->where('materia_id', $malla_detalle->materia_id)->first();
                $nota_siu = $alumno->alumnoNotas->where('materia_id', $malla_detalle->materia_siu_id)->first();

                $periodo = '---';
                if ($nota_paraguay) {
                    $periodo = $nota_paraguay->semestre->nombre;
                }
                if ($nota_siu) {
                    $periodo = $nota_siu->semestre->nombre;
                }

                $semestre_orden = $malla_detalle->semestre ? $malla_detalle->semestre : ($malla_detalle->semestre_materia_siu ? $malla_detalle->semestre_materia_siu : 999);

                $datos = [
                    'semestre_materia_paraguay' => $malla_detalle->semestre ? $malla_detalle->semestre : '---',
                    'materia_paraguay' => $malla_detalle->materia->nombre_real ? $malla_detalle->materia->nombre_real : '---',
                    'nota_paraguay' => $nota_paraguay ? $nota_paraguay->calificacion : '---',
                    'semestre_materia_siu' => $malla_detalle->semestre_materia_siu ? $malla_detalle->semestre_materia_siu : '---',
                    'materia_siu' => $malla_detalle->materia_siu ? $malla_detalle->materia_siu : '---',
                    'nota_siu' => $nota_siu ? $nota_siu->calificacion : '---',
                    'periodo' => $periodo,
                    'estado' => $malla_detalle->espejo,
                    'semestre_orden' => $semestre_orden
                ];

                if ($malla_detalle->espejo) {
                    $equivalencias->push($datos);
                } else {
                    $paraguay_sin_espejo->push($datos);
                }
            }

            foreach ($malla_siu->mallaDetalles as $malla_siu_detalle) {
                if (!in_array($malla_siu_detalle->materia_id, $espejos_siu_ids)) {
                    $nota_siu = $alumno->alumnoNotas->where('materia_id', $malla_siu_detalle->materia_id)->first();
                    $periodo = $nota_siu ? $nota_siu->semestre->nombre : '---';
                    $semestre_orden = $malla_siu_detalle->semestre ? $malla_siu_detalle->semestre : 999;

                    $datos = [
                        'semestre_materia_paraguay' => '---',
                        'materia_paraguay' => '---',
                        'nota_paraguay' => '---',
                        'semestre_materia_siu' => $malla_siu_detalle->semestre ? $malla_siu_detalle->semestre : '---',
                        'materia_siu' => $malla_siu_detalle->materia->nombre_fantasia ? $malla_siu_detalle->materia->nombre_fantasia : '---',
                        'nota_siu' => $nota_siu ? $nota_siu->calificacion : '---',
                        'periodo' => $periodo,
                        'estado' => false,
                        'semestre_orden' => $semestre_orden
                    ];

                    $siu_sin_espejo->push($datos);
                }
            }

            // Combinamos equivalencias primero
            $espejos = $equivalencias;

            // Emparejamos Paraguay sin espejo con SIU sin espejo
            $max_sin_espejo = max($paraguay_sin_espejo->count(), $siu_sin_espejo->count());
            for ($i = 0; $i < $max_sin_espejo; $i++) {
                $p = $paraguay_sin_espejo->get($i, [
                    'semestre_materia_paraguay' => '---',
                    'materia_paraguay' => '---',
                    'nota_paraguay' => '---',
                    'semestre_orden' => 999
                ]);
                $s = $siu_sin_espejo->get($i, [
                    'semestre_materia_siu' => '---',
                    'materia_siu' => '---',
                    'nota_siu' => '---',
                    'periodo' => '---',
                    'semestre_orden' => 999
                ]);

                $periodo_combinado = $p['periodo'] ?? '---';
                if ($s['periodo'] !== '---') {
                    $periodo_combinado = $s['periodo'];
                }

                $datos = [
                    'semestre_materia_paraguay' => $p['semestre_materia_paraguay'],
                    'materia_paraguay' => $p['materia_paraguay'],
                    'nota_paraguay' => $p['nota_paraguay'],
                    'semestre_materia_siu' => $s['semestre_materia_siu'],
                    'materia_siu' => $s['materia_siu'],
                    'nota_siu' => $s['nota_siu'],
                    'periodo' => $periodo_combinado,
                    'estado' => false,
                    'semestre_orden' => min($p['semestre_orden'], $s['semestre_orden'])
                ];

                $espejos->push($datos);
            }

            //Ordenamos por semestre
            $espejos = $espejos->sortBy('semestre_orden')->values();

            $hoy = Carbon::today();
            $semestre = Semestre::whereDate('fecha_inicio', '<=', $hoy)
                            ->whereDate('fecha_fin', '>=', $hoy)
                            ->where('estado', 'AC')
                            ->orderBy('id', 'asc')
                            ->first();
            $periodo_activo = $semestre ? $semestre->id : null;

            return view('pantallas_alumnos/calificaciones')->with(compact('alumno', 'alumno_notas_nacional', 'alumno_notas_siu', 'espejos', 'cantidad_materias_paraguay', 'cantidad_materias_siu', 'periodo_activo', 'promedio_nacional'));

        } catch (\Exception $e) {
            return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function extensiones_universitarias($id)
    {
        $this->authorize('ver_extensiones_alumnos_pantalla');

        try {
            $alumno = Alumno::where('usuario_id', Auth::id())->firstOrFail();
            // El progreso de horas/actividades solo cuenta postulaciones ya
            // aceptadas; las pendientes/rechazadas se muestran aparte en
            // "Mis Postulaciones" (variable $postulaciones más abajo).
            $extensiones = ExtensionUniversitariaDetalle::with('extensionUniversitaria.tipoExtension')->where('alumno_id', $alumno->id)->where('estado', 'AC')->get();
            $postulaciones = ExtensionUniversitariaDetalle::with('extensionUniversitaria.tipoExtension')->where('alumno_id', $alumno->id)->orderByDesc('fecha_postulacion')->get();

            // El requerimiento de graduación puede estar definido por carrera;
            // si la carrera del alumno (según su matriculación más reciente) no
            // tiene uno propio, se usa el general (carrera_id null) como
            // respaldo.
            $matriculacion = Matriculacion::where('alumno_id', $alumno->id)->orderByDesc('fecha')->first();
            $requerimiento = null;
            if ($matriculacion) {
                $requerimiento = RequerimientoExtensionUniversitaria::where('carrera_id', $matriculacion->carrera_id)->first();
            }
            if (!$requerimiento) {
                $requerimiento = RequerimientoExtensionUniversitaria::whereNull('carrera_id')->first();
            }
            $horas_requeridas = $requerimiento->horas_requeridas ?? 0;
            $actividades_requeridas = $requerimiento->actividades_requeridas ?? 0;
            $tipos_actividades = TipoExtensionUniversitaria::where('estado', 'AC')->get();

            $cantidad_realizada_1 = 0;
            $cantidad_realizada_2 = 0;
            $cantidad_realizada_3 = 0;
            $cantidad_realizada_4 = 0;
            $horas_realizadas_1 = 0;
            $horas_realizadas_2 = 0;
            $horas_realizadas_3 = 0;
            $horas_realizadas_4 = 0;
            $horas_acreditadas = 0;
            $horas_acreditadas_1 = 0;
            $horas_acreditadas_2 = 0;
            $horas_acreditadas_3 = 0;
            $horas_acreditadas_4 = 0;

            foreach ($extensiones as $extension) {
                $fecha = Carbon::parse($extension->extensionUniversitaria->fecha_inicio);
                $anho = $fecha->year;
                if ($fecha->month <= 7) {
                    $extension->periodo = $anho . '-1';
                } else {
                    $extension->periodo = $anho . '-2';
                }

                if ($extension->extensionUniversitaria->estado == 'FI') {
                    $tipo_extension_id = $extension->extensionUniversitaria->tipo_extension_id;
                    $maxima_cantidad_horas = $extension->extensionUniversitaria->tipoExtension->maxima_cantidad_horas;

                    // Dependiendo del tipo de extensión, incrementar las horas y cantidades
                    switch ($tipo_extension_id) {
                        case 1:
                            $cantidad_realizada_1++;
                            $horas_realizadas_1 += $extension->cantidad_horas;
                            if ($horas_realizadas_1 > $maxima_cantidad_horas) {
                                $horas_acreditadas_1 = $maxima_cantidad_horas;
                            } else {
                                $horas_acreditadas_1 = $horas_realizadas_1;
                            }
                            break;

                        case 2:
                            $cantidad_realizada_2++;
                            $horas_realizadas_2 += $extension->cantidad_horas;
                            if ($horas_realizadas_2 > $maxima_cantidad_horas) {
                                $horas_acreditadas_2 = $maxima_cantidad_horas;
                            } else {
                                $horas_acreditadas_2 = $horas_realizadas_2;
                            }
                            break;

                        case 3:
                            $cantidad_realizada_3++;
                            $horas_realizadas_3 += $extension->cantidad_horas;
                            if ($horas_realizadas_3 > $maxima_cantidad_horas) {
                                $horas_acreditadas_3 = $maxima_cantidad_horas;
                            } else {
                                $horas_acreditadas_3 = $horas_realizadas_3;
                            }
                            break;

                        case 4:
                            $cantidad_realizada_4++;
                            $horas_realizadas_4 += $extension->cantidad_horas;
                            if ($horas_realizadas_4 > $maxima_cantidad_horas) {
                                $horas_acreditadas_4 = $maxima_cantidad_horas;
                            } else {
                                $horas_acreditadas_4 = $horas_realizadas_4;
                            }
                            break;
                    }
                }
            }

            $horas_acreditadas = $horas_acreditadas_1 + $horas_acreditadas_2 + $horas_acreditadas_3 + $horas_acreditadas_4;
            $actividades_realizadas = $cantidad_realizada_1 + $cantidad_realizada_2 + $cantidad_realizada_3 + $cantidad_realizada_4;

            return view('pantallas_alumnos/extensiones_universitarias')->with(compact('alumno', 'extensiones', 'postulaciones', 'horas_requeridas', 'horas_acreditadas', 'actividades_requeridas', 'tipos_actividades', 'horas_realizadas_1', 'horas_realizadas_2', 'horas_realizadas_3', 'horas_realizadas_4', 'horas_acreditadas_1', 'horas_acreditadas_2', 'horas_acreditadas_3', 'horas_acreditadas_4', 'cantidad_realizada_1', 'cantidad_realizada_2', 'cantidad_realizada_3', 'cantidad_realizada_4', 'actividades_realizadas'));

        } catch (\Exception $e) {
            return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function adjuntar_certificado_extensiones_universitarias(Request $request, $id)
    {
        $this->authorize('ver_extensiones_alumnos_pantalla');

        $request->validate([
            'adjunto' => ['required', 'file', 'extensions:jpg,png,pdf']
        ]);

        DB::beginTransaction();

        try {
            $extension = ExtensionUniversitariaDetalle::findOrFail($id);

            //cargar archivo
            $archivo = $request->adjunto;
            $extension_archivo = $archivo->getClientOriginalExtension();
            $directorio = 'storage/extensiones_universitarias/alumnos_certificados';
            if ($extension_archivo == 'jpg' || $extension_archivo == 'jpeg' || $extension_archivo == 'png') {
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($archivo);
                $image = $image->encode(new AutoEncoder(quality: 50));
                $nombre = $extension->extension_universitaria_id . '_' . Str::lower($extension->alumno->numero_documento);
                $nombre_archivo = $nombre . '.' . $extension_archivo;
                $image->save($directorio . '/' . $nombre_archivo);
            } else {
                $directorio_storage = 'public/extensiones_universitarias/alumnos_certificados';
                $nombre = $extension->extension_universitaria_id . '_' . Str::lower($extension->alumno->numero_documento);
                $nombre_archivo = $nombre . '.' . $extension_archivo;
                Storage::putFileAs($directorio_storage . '/', $archivo, $nombre_archivo);
            }
            $extension->url_certificado = $directorio . '/' . $nombre_archivo;
            $extension->save();

            DB::commit();

            return response()->json([
                'message' => 'El certificado de la extensión universitaria ' . $extension->extensionUniversitaria->nombre . ' fue adjuntado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function eliminar_certificado_extensiones_universitarias($id)
    {
        $this->authorize('ver_extensiones_alumnos_pantalla');

        DB::beginTransaction();

        try {
            $extension = ExtensionUniversitariaDetalle::findOrFail($id);
            Storage::delete($extension->url_certificado);
            $extension->url_certificado = null;
            $extension->save();

            DB::commit();

            return redirect()->route('pantallas_alumnos.extensiones_universitarias', Auth::id())->with('error-message','El certificado de la extensión universitaria ' . $extension->extensionUniversitaria->nombre . ' fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    /**
     * Catálogo de proyectos de extensión abiertos a postulación: aprobados,
     * dentro de la fecha, abiertos a la carrera del alumno (o sin
     * restricción de carrera) y a los que el alumno todavía no se postuló.
     */
    public function catalogo_extensiones_universitarias($id)
    {
        $this->authorize('ver_catalogo_extensiones_alumnos_pantalla');

        try {
            $alumno = Alumno::where('usuario_id', Auth::id())->firstOrFail();
            $matriculacion = Matriculacion::where('alumno_id', $alumno->id)->orderByDesc('fecha')->first();
            $carreraAlumnoId = $alumno->carrera_id ?: ($matriculacion->carrera_id ?? null);

            $yaPostuladoIds = ExtensionUniversitariaDetalle::where('alumno_id', $alumno->id)->pluck('extension_universitaria_id');

            $proyectos = \App\Models\ExtensionUniversitaria::with(['tipoExtension', 'docente', 'carreras'])
                ->where('estado', 'AP')
                ->where(function ($query) {
                    $query->whereNull('fecha_fin')->orWhereDate('fecha_fin', '>=', now()->toDateString());
                })
                ->whereNotIn('id', $yaPostuladoIds)
                ->get()
                ->filter(function ($proyecto) use ($carreraAlumnoId) {
                    if ($proyecto->carreras->isEmpty()) {
                        return true; // sin restricción de carrera = abierto a todas
                    }
                    return $carreraAlumnoId && $proyecto->carreras->contains('id', $carreraAlumnoId);
                })
                ->values();

            return view('pantallas_alumnos/catalogo_extensiones_universitarias')->with(compact('alumno', 'proyectos'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function postular_extension_universitaria($idUsuario, $idExtension)
    {
        $this->authorize('postular_extensiones_alumnos_pantalla');

        DB::beginTransaction();

        try {
            $alumno = Alumno::where('usuario_id', Auth::id())->firstOrFail();
            $extension = \App\Models\ExtensionUniversitaria::with('carreras')->findOrFail($idExtension);

            if (!$alumno->tieneDatosParaExtension()) {
                throw new \Exception('Tu perfil todavía no tiene la carrera y el año de ingreso cargados. Pedile al encargado de Extensión que los complete para poder postularte.');
            }

            if (!$extension->estaAbiertaParaPostulacion()) {
                throw new \Exception('Este proyecto ya no está abierto a postulaciones.');
            }

            $yaPostulado = ExtensionUniversitariaDetalle::where('extension_universitaria_id', $extension->id)->where('alumno_id', $alumno->id)->exists();
            if ($yaPostulado) {
                throw new \Exception('Ya te postulaste a este proyecto.');
            }

            $cupos = $extension->cuposDisponibles();
            if ($cupos !== null && $cupos <= 0) {
                throw new \Exception('No quedan cupos disponibles en este proyecto.');
            }

            $matriculacion = Matriculacion::where('alumno_id', $alumno->id)->orderByDesc('fecha')->first();
            $carreraPropia = $alumno->carrera_id ?: ($matriculacion->carrera_id ?? null);
            if (!$extension->carreras->isEmpty() && (!$carreraPropia || !$extension->carreras->contains("id", $carreraPropia))) {
                throw new \Exception('Este proyecto no está habilitado para tu carrera.');
            }

            $carreraSemestre = $this->resolverCarreraSemestreAlumno($alumno->id, $extension->fecha_inicio);

            $detalle = new ExtensionUniversitariaDetalle();
            $detalle->extension_universitaria_id = $extension->id;
            $detalle->alumno_id = $alumno->id;
            $detalle->carrera_id = $carreraSemestre['carrera_id'];
            $detalle->semestre_id = $carreraSemestre['semestre_id'];
            $detalle->estado = 'PE';
            $detalle->fecha_postulacion = now();
            $detalle->save();

            DB::commit();

            return redirect()->route('pantallas_alumnos.extensiones_universitarias', Auth::id())->with('success-message', 'Tu postulación a ' . $extension->nombre . ' fue enviada. Vas a ver el resultado en "Mis Postulaciones".');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('pantallas_alumnos.catalogo_extensiones_universitarias', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function cancelar_postulacion_extension_universitaria($idUsuario, $idDetalle)
    {
        $this->authorize('postular_extensiones_alumnos_pantalla');

        DB::beginTransaction();

        try {
            $alumno = Alumno::where('usuario_id', Auth::id())->firstOrFail();
            $detalle = ExtensionUniversitariaDetalle::where('alumno_id', $alumno->id)->findOrFail($idDetalle);

            if ($detalle->estado !== 'PE') {
                throw new \Exception('Solo se puede cancelar una postulación mientras está pendiente de revisión.');
            }

            $detalle->delete();

            DB::commit();

            return redirect()->route('pantallas_alumnos.extensiones_universitarias', Auth::id())->with('success-message', 'La postulación fue cancelada.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('pantallas_alumnos.extensiones_universitarias', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function create_inscripciones_tesis($id)
    {
        $this->authorize('crear_inscripciones_tesis_alumnos_pantalla');

        try {
            $alumno = Alumno::where('usuario_id', $id)->first();
            $matriculacion = Matriculacion::where('alumno_id', $alumno->id)->orderBy('id', 'desc')->first();
            if (!$matriculacion) {
                return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', 'Debe estar matriculado en la Universidad para inscribir un tema de trabajo final de grado.');
            }

            $alumno_notas = AlumnoNota::where('alumno_id', $alumno->id)->where('carrera_id', $matriculacion->carrera_id)->where('calificacion', '>', '1')->get();

            $malla = Malla::where('carrera_id', $matriculacion->carrera_id)->first();
            $malla_detalles = MallaDetalle::where('malla_id', $malla->id)->get();
            $cantidad_materias = 0;

            foreach ($malla_detalles as $detalle) {
                if (!str_contains($detalle->materia->nombre_real, 'TRABAJO FINAL DE GRADO') || !str_contains($detalle->materia->nombre_fantasia, 'TRABAJO FINAL DE GRADO')) {
                    $cantidad_materias = $cantidad_materias + 1;
                }
            }

            if ($cantidad_materias != $alumno_notas->count()) { //si la carrera se encuentra terminada hace lo siguiente
                return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', 'Debe aprobar todas las materias para inscribir un tema de trabajo final de grado.');
            }

            $inscripcion_tesis = InscripcionTemaTesis::where('alumno_id', $alumno->id)->whereIn('estado', ['PE', 'AT', 'AC'])->first();

            if ($inscripcion_tesis) {
                return redirect()->route('pantallas_alumnos.inscripciones_tesis', Auth::id())->with('error-message', 'Ya cuenta con una inscripción de tema de trabajo final de grado activa. Aquí te la mostramos.');
            }

            $matriculaciones = Matriculacion::where('alumno_id', $alumno->id)->where('estado', 'AC')->orderBy('id', 'desc')->get();
            $carreras = collect();
            foreach ($matriculaciones as $matriculacion) {
                $carreras->push($matriculacion->carrera);
            }
            $tipos = TipoTesis::where('estado', 'AC')->orderBy('nombre', 'asc')->get();
            $areas = AreaTesis::where('estado', 'AC')->orderBy('nombre', 'asc')->get();
            $lineas = LineaTesis::where('estado', 'AC')->orderBy('nombre', 'asc')->get();
            $tutores = Docente::where('tutor_tesis', true)->where('estado', 'AC')->get();
            return view('pantallas_alumnos/tesis/create')->with(compact('alumno', 'carreras', 'tipos', 'areas', 'lineas', 'tutores'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function inscripciones_tesis($id)
    {
        $this->authorize('ver_inscripciones_tesis_alumnos_pantalla');

        try {
            $alumno = Alumno::where('usuario_id', $id)->first();
            $inscripciones = InscripcionTemaTesis::where('alumno_id', $alumno->id)->orderBy('id', 'desc')->get();
            return view('pantallas_alumnos/tesis/index')->with(compact('alumno', 'inscripciones'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function show_inscripciones_tesis($id) {
        $this->authorize('ver_inscripciones_tesis_alumnos_pantalla');

        try {
            $inscripcion = InscripcionTemaTesis::findOrFail($id);
            $alumno = Alumno::findOrFail($inscripcion->alumno_id);

            return view('pantallas_alumnos/tesis/show')->with(compact('inscripcion', 'alumno'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function anteproyectos_tesis($id)
    {
        $this->authorize('ver_anteproyectos_tesis_alumnos_pantalla');

        try {
            $alumno = Alumno::where('usuario_id', $id)->first();
            $temas = InscripcionTemaTesis::whereHas('anteproyectos')->where('alumno_id', $alumno->id)->get();
            return view('pantallas_alumnos/tesis/anteproyectos/index')->with(compact('alumno', 'temas'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function show_anteproyectos_tesis($id)
    {
        $this->authorize('ver_anteproyectos_tesis_alumnos_pantalla');

        try {
            $tema = InscripcionTemaTesis::with(['anteproyectos' => function ($query) {
                $query->orderBy('numero_bloque', 'asc');
            }])->findOrFail($id);
            $alumno = Alumno::findOrFail($tema->alumno_id);
            return view('pantallas_alumnos/tesis/anteproyectos/show')->with(compact('tema', 'alumno'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function show_entregas_anteproyectos_tesis($id)
    {
        $this->authorize('ver_entregas_anteproyectos_tesis_alumnos_pantalla');

        try {
            $anteproyecto = AnteproyectoTesis::with(['entregas' => function ($query) {
                $query->orderBy('numero', 'desc');
            }])->findOrFail($id);
            $alumno = Alumno::findOrFail($anteproyecto->inscripcion->alumno_id);

            return view('pantallas_alumnos/tesis/anteproyectos/show_entregas')->with(compact('anteproyecto', 'alumno'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function proyectos_tesis($id)
    {
        $this->authorize('ver_proyectos_tesis_alumnos_pantalla');

        try {
            $alumno = Alumno::where('usuario_id', $id)->first();
            $temas = InscripcionTemaTesis::whereHas('proyectos')->where('alumno_id', $alumno->id)->get();
            return view('pantallas_alumnos/tesis/proyectos/index')->with(compact('alumno', 'temas'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function show_proyectos_tesis($id)
    {
        $this->authorize('ver_proyectos_tesis_alumnos_pantalla');

        try {
            $tema = InscripcionTemaTesis::with(['proyectos' => function ($query) {
                $query->orderBy('numero_bloque', 'asc');
            }])->findOrFail($id);
            $alumno = Alumno::findOrFail($tema->alumno_id);
            return view('pantallas_alumnos/tesis/proyectos/show')->with(compact('tema', 'alumno'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function show_entregas_proyectos_tesis($id)
    {
        $this->authorize('ver_entregas_proyectos_tesis_alumnos_pantalla');

        try {
            $proyecto = ProyectoTesis::with(['entregas' => function ($query) {
                $query->orderBy('numero', 'desc');
            }])->findOrFail($id);
            $alumno = Alumno::findOrFail($proyecto->inscripcion->alumno_id);

            return view('pantallas_alumnos/tesis/proyectos/show_entregas')->with(compact('proyecto', 'alumno'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function borradores_tesis($id)
    {
        $this->authorize('ver_borradores_tesis_alumnos_pantalla');

        try {
            $alumno = Alumno::where('usuario_id', $id)->first();
            $temas = InscripcionTemaTesis::whereHas('borradores')->where('alumno_id', $alumno->id)->get();
            return view('pantallas_alumnos/tesis/borradores/index')->with(compact('alumno', 'temas'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function show_borradores_tesis($id)
    {
        $this->authorize('ver_borradores_tesis_alumnos_pantalla');

        try {
            $tema = InscripcionTemaTesis::with(['borradores' => function ($query) {
                $query->orderBy('numero_bloque', 'asc');
            }])->findOrFail($id);
            $alumno = Alumno::findOrFail($tema->alumno_id);
            return view('pantallas_alumnos/tesis/borradores/show')->with(compact('tema', 'alumno'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function show_entregas_borradores_tesis($id)
    {
        $this->authorize('ver_entregas_borradores_tesis_alumnos_pantalla');

        try {
            $proyecto = BorradorTesis::with(['entregas' => function ($query) {
                $query->orderBy('numero', 'desc');
            }])->findOrFail($id);
            $alumno = Alumno::findOrFail($proyecto->inscripcion->alumno_id);

            return view('pantallas_alumnos/tesis/borradores/show_entregas')->with(compact('proyecto', 'alumno'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function noticias($id)
    {
        $this->authorize('ver_noticias_avisos_alumnos_pantalla');

        try {
            $alumno = Alumno::where('usuario_id', Auth::id())->first();
            $noticias = NoticiaAviso::where('estado', 'PU')->where('tipo', 'NO')->orderBy('destacado', 'desc')->get();
            return view('pantallas_alumnos/noticias')->with(compact('alumno', 'noticias'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function show_noticia($id)
    {
        $this->authorize('ver_noticias_avisos_alumnos_pantalla');

        try {
            $alumno = Alumno::where('usuario_id', Auth::id())->first();
            $noticia = NoticiaAviso::findOrFail($id);
            return view('pantallas_alumnos/show_noticia')->with(compact('alumno', 'noticia'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function avisos($id)
    {
        $this->authorize('ver_noticias_avisos_alumnos_pantalla');

        try {
            $alumno = Alumno::where('usuario_id', Auth::id())->first();
            $avisos = NoticiaAviso::where('estado', 'PU')->where('tipo', 'AV')->orderBy('destacado', 'desc')->get();
            return view('pantallas_alumnos/avisos')->with(compact('alumno', 'avisos'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function show_aviso($id)
    {
        $this->authorize('ver_noticias_avisos_alumnos_pantalla');

        try {
            $alumno = Alumno::where('usuario_id', Auth::id())->first();
            $aviso = NoticiaAviso::findOrFail($id);
            return view('pantallas_alumnos/show_aviso')->with(compact('alumno', 'aviso'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function solicitudes($id)
    {
        $this->authorize('ver_solicitudes_alumnos_pantalla');

        try {
            $alumno = Alumno::where('usuario_id', Auth::id())->first();
            $solicitudes = Solicitud::where('alumno_id', $alumno->id)->get();
			$examen_suficiencia;
			foreach ($solicitudes as $solicitud) {
				$examen_suficiencia = ExamenSuficiencia::where('solicitud_id', $solicitud->id)->first();
				if ($examen_suficiencia) {
					$solicitud->fecha_examen = $examen_suficiencia->fecha_examen;
					$solicitud->aula_examen = $examen_suficiencia->aula_examen;
				}
			}

            return view('pantallas_alumnos/solicitudes')->with(compact('alumno', 'solicitudes'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function create_solicitudes($id)
    {
        $this->authorize('crear_solicitudes_alumnos_pantalla');

        try {
            $alumno = Alumno::where('usuario_id', Auth::id())->first();
            $hoy = Carbon::today();
            $semestre = Semestre::whereDate('fecha_inicio', '<=', $hoy)
                            ->whereDate('fecha_fin', '>=', $hoy)
                            ->where('estado', 'AC')
                            ->orderBy('id', 'asc')
                            ->first();

            if (!$semestre) {
                return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', 'No hay un semestre activo para realizar una solicitud.');
            }
            
            $matriculacion = Matriculacion::where('semestre_id', $semestre->id)->where('alumno_id', $alumno->id)->orderBy('id', 'desc')->first();

            if (!$matriculacion) {
                $tipos_solicitudes = TipoSolicitud::whereIn('id', [1])->where('estado', 'AC')->orderBy('id', 'asc')->get();

                $materias = collect();
                $materias_suficiencias = collect();
                $examen_suficiencia_fecha_solicitud = null;
                $cantidad_inasistencias = null;
                $materias_tutorias = collect();
                $modalidades_tutorias = collect();
                $inscripciones = collect();
                $fechas_desmatriculacion = null;

                return view('pantallas_alumnos/create_solicitudes')->with(compact('alumno', 'tipos_solicitudes', 'materias', 'materias_suficiencias', 'examen_suficiencia_fecha_solicitud', 'cantidad_inasistencias', 'materias_tutorias', 'modalidades_tutorias', 'inscripciones', 'fechas_desmatriculacion'));
            } else {
                $array_tipos_solicitudes = [1, 2, 3, 4, 5, 6];

                //inicio ausencias
                $cantidad_inasistencias = Solicitud::where('alumno_id', $alumno->id)->where('tipo_solicitud_id', 3)->where('semestre_id', $semestre->id)->where('estado', 'EN')->count();
                $materias = collect();

                $malla = Malla::where('carrera_id', $matriculacion->carrera_id)->first();
                $semestre_malla = SemestreMalla::where('semestre_id', $matriculacion->semestre_id)->where('malla_id', $malla->id)->first();
                $semestre_malla_materias = SemestreMallaMateria::where('semestre_malla_id', $semestre_malla->id)->get();
                foreach ($semestre_malla_materias as $smm) {
                    $materias->push($smm->materia);
                }

                if ($matriculacion->carrera_siu_id) {
                    $malla_siu = Malla::where('carrera_id', $matriculacion->carrera_siu_id)->first();
                    if ($malla_siu) {
                        $semestre_malla_siu = SemestreMalla::where('semestre_id', $matriculacion->semestre_id)->where('malla_id', $malla_siu->id)->first();
                        if ($semestre_malla_siu) {
                            $semestre_malla_materias_siu = SemestreMallaMateria::where('semestre_malla_id', $semestre_malla_siu->id)->get();
                            if ($semestre_malla_materias_siu) {
                                foreach ($semestre_malla_materias_siu as $smms) {
                                    $materias->push($smms->materia);
                                }
                            }
                        }
                    }
                }
                $materias = $materias->sortBy('nombre_fantasia');
                //fin ausencias

                //inicio examenes de suficiciencia
                $materias_suf = MateriaSuficiencia::where('estado', 'AC')->get();
                $correlatividades = Correlatividad::get();

                $malla = Malla::where('carrera_id', $matriculacion->carrera_id)->first();
                $semestre_malla = SemestreMalla::where('semestre_id', $matriculacion->semestre_id)->where('malla_id', $malla->id)->first();
                $semestre_malla_materias = SemestreMallaMateria::where('semestre_malla_id', $semestre_malla->id)->pluck('materia_id')->toArray();

                $inscripciones = Inscripcion::where('alumno_id', $alumno->id)->whereIn('estado', ['AP', 'CO', 'ES', 'SU'])->pluck('materia_id')->toArray();
                $examenes_suficiencia = ExamenSuficiencia::where('alumno_id', $alumno->id)->pluck('materia_id')->toArray();

                $materias_suficiencias = collect();

                foreach ($materias_suf as $ms) {
                    if (in_array($ms->materia_id, $inscripciones)) {
                        continue;
                    }

                    if (!in_array($ms->materia_id, $semestre_malla_materias)) {
                        continue;
                    }

                    if (in_array($ms->materia_id, $examenes_suficiencia)) {
                        continue;
                    }

                    $correlativas = $correlatividades->where('materia_id', $ms->materia_id)->pluck('correlativa_id')->toArray();

                    $correlativas_aprobadas = true;
                    foreach ($correlativas as $correlativa_id) {
                        if (!in_array($correlativa_id, $inscripciones)) {
                            $correlativas_aprobadas = false;
                            break;
                        }
                    }

                    if ($correlativas_aprobadas) {
                        $materias_suficiencias->push($ms);
                    }
                }

                $materias_suficiencias = $materias_suficiencias->unique('materia_id');

                $materias_suficiencias = $materias_suficiencias->values()->all();


                $examen_suficiencia_fecha_solicitud = ExamenSuficienciaFechaSolicitud::where('semestre_id', $semestre->id)
                    ->where('programa_id', $matriculacion->programa_id)
                    ->whereDate('fecha_inicio', '<=', Carbon::today())
                    ->whereDate('fecha_fin', '>=', Carbon::today())
                    ->where('estado', 'AC')->first();

                if (!$examen_suficiencia_fecha_solicitud) {
                    $array_tipos_solicitudes = array_diff($array_tipos_solicitudes, [2]);
                }
                //fin examenes de suficiencia

                //inicio tutorias
                $materias_tut = Materia::where('estado', 'AC')->get();
                $inscripciones = Inscripcion::where('alumno_id', $alumno->id)->whereIn('estado', ['EC', 'RE'])->get();

                $materias_tutorias = collect();

                foreach ($materias_tut as $materia) {
                    foreach ($inscripciones as $inscripcion) {
                        if ($materia->id == $inscripcion->materia_id) {
                            $materias_tutorias->push($materia);
                        }
                    }
                }

                $modalidades_tutorias = TutoriaPrecio::get();
                //fin tutorias

                //inicio desmatriculacion
                $inscripciones = Inscripcion::whereHas('matriculacion', function ($query) use ($semestre, $alumno) {
                    $query->where('semestre_id', $semestre->id)
                            ->where('alumno_id', $alumno->id)
                            ->where('estado', 'AC');
                })->whereIn('estado', ['MA', 'EC'])
                ->get();

                $fechas_desmatriculacion = FechaDesmatriculacion::where('semestre_id', $semestre->id)
                    ->where('programa_id', $matriculacion->programa_id)
                    ->whereDate('fecha_inicio', '<=', Carbon::today())
                    ->whereDate('fecha_fin', '>=', Carbon::today())
                    ->where('estado', 'AC')->first();

                if (!$fechas_desmatriculacion) {
                    $array_tipos_solicitudes = array_diff($array_tipos_solicitudes, [6]);
                }

                $tipos_solicitudes = TipoSolicitud::whereIn('id', $array_tipos_solicitudes)->where('estado', 'AC')->orderBy('id', 'asc')->get();

                //fin desmatriculacion

                return view('pantallas_alumnos/create_solicitudes')->with(compact('alumno', 'tipos_solicitudes', 'materias', 'materias_suficiencias', 'examen_suficiencia_fecha_solicitud', 'cantidad_inasistencias', 'materias_tutorias', 'modalidades_tutorias', 'inscripciones', 'fechas_desmatriculacion'));
            }
        } catch (\Exception $e) {
            return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function encuestas ($id)
    {
        $this->authorize('ver_encuestas_alumnos_pantalla');

        try {
            $alumno = Alumno::where('usuario_id', $id)->first();
            $encuestas = Encuesta::where('tipo', 'AL')->where('estado', 'PU')->get();

            return view('pantallas_alumnos/encuestas')->with(compact('alumno', 'encuestas'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function estado_cuenta($id)
    {
        $this->authorize('ver_estado_cuenta_alumnos_pantalla');

        try {
            $fecha_hoy = Carbon::now();
            $alumno = Alumno::where('usuario_id', $id)->first();
            $matriculacion = Matriculacion::where('alumno_id', $alumno->id)->where('estado', 'AC')->orderBy('id', 'desc')->first();
            if ($matriculacion) {
                $pagos = collect();
                $pagos_matriculaciones = PagoMatriculacion::where('matriculacion_id', $matriculacion->id)->get();
                foreach ($pagos_matriculaciones as $pago) {
                    $datos_pagos = ['concepto' => $pago->descripcion,
                                    'vencimiento' => $pago->fecha_vencimiento,
                                    'monto' => $pago->monto,
                                    'saldo' => $pago->saldo,
                                    'estado' => $pago->estado];
                    $pagos->push($datos_pagos);
                }


                //REVISAR ESTA PARTE
                $solicitudes = Solicitud::with('pagoSolicitud')->where('alumno_id', $alumno->id)->where('estado', 'AP')->get();
                if ($solicitudes) {
                    foreach ($solicitudes as $solicitud) {
                        if ($solicitud->tipo_solicitud_id != 5) {
                            $datos_pagos = ['concepto' => $solicitud->pagoSolicitud->descripcion,
                                        'vencimiento' => $solicitud->pagoSolicitud->vencimiento,
                                        'monto' => $solicitud->pagoSolicitud->monto,
                                        'saldo' => $solicitud->pagoSolicitud->saldo,
                                        'estado' => $solicitud->pagoSolicitud->estado];
                            $pagos->push($datos_pagos);
                        }
                    }
                }
                //HASTA ACA

                return view('pantallas_alumnos/estado_cuenta')->with(compact('fecha_hoy', 'alumno', 'matriculacion', 'pagos'));
            } else {
                return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', 'Actualmente no cuentas con ninguna matriculación activa.');
            }
        } catch (\Exception $e) {
           return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }
}
