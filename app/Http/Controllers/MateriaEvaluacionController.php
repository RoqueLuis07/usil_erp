<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\Models\Alumno;
use App\Models\AlumnoPuntaje;
use App\Models\AlumnoNota;
use App\Models\Escala;
use App\Models\Carrera;
use App\Models\Materia;
use App\Models\Malla;
use App\Models\MallaDetalle;
use App\Models\Semestre;
use App\Models\SemestreMalla;
use App\Models\SemestreMallaMateria;
use App\Models\Evaluacion;
use App\Models\ActaEvaluacion;
use App\Models\ActaEvaluacionAlumno;
use App\Models\Matriculacion;
use App\Models\Inscripcion;
use App\Models\MallaEspejo;
use App\Models\MallaEspejoDetalle;


class MateriaEvaluacionController extends Controller
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

    public function show($materia_id, $semestre_id, $carrera_id)
    {
        $this->authorize('ver_evaluaciones_materias_semestres');

        try {
            $materia = Materia::findOrFail($materia_id);
            $semestre = Semestre::findOrFail($semestre_id);
            $carrera = Carrera::findOrFail($carrera_id);

            $alumnos = Alumno::whereHas('matriculaciones', function ($query) use ($semestre_id, $materia_id, $carrera_id) {
                $query->where('semestre_id', $semestre_id)
                    ->where('carrera_id', $carrera_id)
                    ->whereHas('inscripciones', function ($query) use ($materia_id) {
                        $query->where('materia_id', $materia_id);
                    });
            })
                ->with(['alumnoPuntajes' => function ($query) use ($materia_id) {
                    $query->where('materia_id', $materia_id)
                        ->orderBy('id');
                }])
                ->get();



            $semestre_malla_materia = SemestreMallaMateria::where('materia_id', $materia_id)
                ->whereHas('semestreMalla', function ($query) use ($semestre_id) {
                    $query->where('semestre_id', $semestre_id);
                })->first();

            return view('materias_semestres/evaluaciones/show')->with(compact('materia', 'semestre', 'carrera', 'alumnos', 'semestre_malla_materia'));
        } catch (\Exception $e) {
            return redirect()->route('materias_semestres.index')->with('error-message', $e->getMessage());
        }
    }

    public function create($materia_id, $semestre_id, $carrera_id)
    {
        $this->authorize('crear_evaluaciones_materias_semestres');

        try {
            $materia = Materia::findOrFail($materia_id);
            $semestre = Semestre::findOrFail($semestre_id);
            if ($semestre->estado == 'IN') {
                return back()->with('error-message', 'La carga de puntajes no puede realizarse. El semestre se encuentra cerrado.');
            }
            $carrera = Carrera::findOrFail($carrera_id);

            $alumnos = Alumno::whereHas('matriculaciones', function ($query) use ($semestre_id, $materia_id) {
                $query->where('semestre_id', $semestre_id)
                    ->whereHas('inscripciones', function ($query) use ($materia_id) {
                        $query->where('materia_id', $materia_id)
                            ->where('estado', 'EC');
                    });
            })
                ->get();

            $semestre_malla_materia = SemestreMallaMateria::where('materia_id', $materia_id)
                ->whereHas('semestreMalla', function ($query) use ($semestre_id) {
                    $query->where('semestre_id', $semestre_id);
                })->first();

            $ids = collect(); //coleccion para los id de evaluaciones que tenemos que mostrar
            $ids->push(1, 2); //agregamos proceso, parcial

            if (AlumnoPuntaje::where('materia_id', $materia_id)->where('carrera_id', $carrera_id)->where('semestre_id', $semestre_id)->where('evaluacion_id', 1)->exists()) {
                $ids->forget(0);
            }

            if (AlumnoPuntaje::where('materia_id', $materia_id)->where('carrera_id', $carrera_id)->where('semestre_id', $semestre_id)->where('evaluacion_id', 2)->exists()) {
                $ids->forget(1);
                $ids->push(3);
            }

            if (AlumnoPuntaje::where('materia_id', $materia_id)->where('carrera_id', $carrera_id)->where('semestre_id', $semestre_id)->where('evaluacion_id', 3)->exists()) {
                $ids->forget(2);

                $alumnos = Alumno::whereHas('matriculaciones', function ($query) use ($semestre_id, $materia_id) {
                    $query->where('semestre_id', $semestre_id)
                        ->whereHas('inscripciones', function ($query) use ($materia_id) {
                            $query->where('materia_id', $materia_id)
                                ->where('estado', 'EC');
                        });
                })
                    ->whereDoesntHave('puntajes', function ($query) use ($materia_id, $carrera_id, $semestre_id) {
                        $query->where('materia_id', $materia_id)
                            ->where('carrera_id', $carrera_id)
                            ->where('semestre_id', $semestre_id)
                            ->where('evaluacion_id', 2);
                    })
                    ->get();
            }

            if (ActaEvaluacion::with('alumnos')->where('tipo', 'O')->where('carrera_id', $carrera_id)->where('materia_id', $materia_id)->where('semestre_id', $semestre_id)->first()) {
                $ids->forget([0, 1, 2]);
                if (!AlumnoPuntaje::where('materia_id', $materia_id)->where('carrera_id', $carrera_id)->where('semestre_id', $semestre_id)->where('evaluacion_id', 4)->exists()) {
                    $ids->push(4);
                }
            }
            if (ActaEvaluacion::with('alumnos')->where('tipo', 'C')->where('carrera_id', $carrera_id)->where('materia_id', $materia_id)->where('semestre_id', $semestre_id)->first()) {
                $ids->forget([0, 1, 2, 3]);
                if (!AlumnoPuntaje::where('materia_id', $materia_id)->where('carrera_id', $carrera_id)->where('semestre_id', $semestre_id)->where('evaluacion_id', 5)->exists()) {
                    $ids->push(5);
                }
            }
            if (ActaEvaluacion::with('alumnos')->where('tipo', 'E')->where('carrera_id', $carrera_id)->where('materia_id', $materia_id)->where('semestre_id', $semestre_id)->first()) {
                $ids->forget([0, 1, 2, 3, 4]);
                if (!AlumnoPuntaje::where('materia_id', $materia_id)->where('carrera_id', $carrera_id)->where('semestre_id', $semestre_id)->where('evaluacion_id', 6)->exists()) {
                    $ids->push(6);
                }
            }

            if ($ids->isEmpty()) {
                return redirect()->route('materias_evaluaciones.show', ['materia' => $materia_id, 'semestre' => $semestre_id, 'carrera' => $carrera_id])->with('error-message', 'El acta de evaluación debe ser generado previamente para agregar las puntuaciones de los exámenes.');
            }

            $evaluaciones = Evaluacion::whereIn('id', $ids)->where('estado', 'AC')->orderBy('id', 'asc')->get();

            return view('materias_semestres/evaluaciones/create')->with(compact('materia', 'semestre', 'carrera', 'alumnos', 'semestre_malla_materia', 'evaluaciones'));
        } catch (\Exception $e) {
            return redirect()->route('materias_evaluaciones.show')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_evaluaciones_materias_semestres');
		
		$puntaje_maximo = 100;
		if ($request->evaluacion == 1 || $request->evaluacion == 2 || $request->evaluacion == 3) {
			$puntaje_maximo = 30;
		} else {
			$puntaje_maximo = 40;
		}
		

        $request->validate([
            'materia' => ['required', 'numeric'],
            'evaluacion' => ['required', 'numeric'],
            'detalles' => ['required', 'array'],
            'detalles.*.alumno' => ['required', 'numeric'],
            'detalles.*.puntaje_obtenido' => ['nullable', 'required_if:evaluacion,1', 'numeric', 'max:'.$puntaje_maximo],
            'detalles.*.observacion' => 'nullable'
        ]);

        DB::beginTransaction();

        try {
            foreach ($request->detalles as $detalle) {
                $bandera = 0;
                if ($detalle['puntaje_obtenido'] == null && $detalle['observacion'] == null) {
                    $bandera = 1;
                }

                if ($bandera == 0) {
                    $alumno_puntaje = new AlumnoPuntaje();
                    $alumno_puntaje->alumno_id = $detalle['alumno'];
                    $alumno_puntaje->materia_id = $request->materia;
                    $alumno_puntaje->carrera_id = $request->carrera;
                    $alumno_puntaje->semestre_id = $request->semestre;
                    $alumno_puntaje->evaluacion_id = $request->evaluacion;
                    $alumno_puntaje->observacion = removeAccents(Str::upper($detalle['observacion']));
                    if ($alumno_puntaje->observacion == 'AUSENTE') {
                        $alumno_puntaje->puntos_obtenidos = 0;
                        $nota_ausente = 'AUSENTE';
                    } else {
                        $alumno_puntaje->puntos_obtenidos = $detalle['puntaje_obtenido'];
                        $nota_ausente = '';
                    }
                    $alumno_puntaje->cargado_por_id = Auth::id();
                    $alumno_puntaje->save();

                    if ($alumno_puntaje->puntos_obtenidos >= 0) {
                        $evaluacion = Evaluacion::findOrFail($alumno_puntaje->evaluacion_id);
                        if ($evaluacion->tipo_evaluacion_id == 4 || $evaluacion->tipo_evaluacion_id == 5 || $evaluacion->tipo_evaluacion_id == 6) {
                            $procesos = AlumnoPuntaje::where('alumno_id', $alumno_puntaje->alumno_id)->where('materia_id', $alumno_puntaje->materia_id)->where('evaluacion_id', 1)->get();
                            $total_puntos_proceso = 0;
                            foreach ($procesos as $proceso) {
                                $puntos_proceso = $proceso->puntos_obtenidos * ($proceso->evaluacion->valor_porcentual / 100);
                                $total_puntos_proceso = $total_puntos_proceso + $puntos_proceso;
                            }

                            $parciales = AlumnoPuntaje::where('alumno_id', $alumno_puntaje->alumno_id)->where('materia_id', $alumno_puntaje->materia_id)->where('evaluacion_id', 2)->get();
                            $total_puntos_parcial = 0;
                            foreach ($parciales as $parcial) {
                                $puntos_parcial = $parcial->puntos_obtenidos * ($parcial->evaluacion->valor_porcentual / 100);
                                $total_puntos_parcial = $total_puntos_parcial + $puntos_parcial;
                            }

                            $recuperatorios = AlumnoPuntaje::where('alumno_id', $alumno_puntaje->alumno_id)->where('materia_id', $alumno_puntaje->materia_id)->where('evaluacion_id', 3)->get();
                            $total_puntos_recuperatorio = 0;
                            foreach ($recuperatorios as $recuperatorio) {
                                $puntos_recuperatorio = $recuperatorio->puntos_obtenidos * ($recuperatorio->evaluacion->valor_porcentual / 100);
                                $total_puntos_recuperatorio = $total_puntos_recuperatorio + $puntos_recuperatorio;
                            }

                            if ($total_puntos_recuperatorio != 0) {
                                $total_puntos_parcial = (int) round($total_puntos_recuperatorio);
                            }

                            if ($evaluacion->tipo_evaluacion_id == 4) {
                                $puntaje_obtenido = $alumno_puntaje->puntos_obtenidos;
                                $total_puntos_final = (int) round($puntaje_obtenido * ($evaluacion->valor_porcentual / 100));
                                $nombre_evaluacion = 'ORDINARIO';
                                $acta = ActaEvaluacion::with('alumnos')->where('tipo', 'O')->where('carrera_id', $request->carrera)->where('materia_id', $alumno_puntaje->materia_id)->where('semestre_id', $request->semestre)->first();
                            }

                            if ($evaluacion->tipo_evaluacion_id == 5) {
                                $puntaje_obtenido = $alumno_puntaje->puntos_obtenidos;
                                $total_puntos_final = (int) round($puntaje_obtenido * ($evaluacion->valor_porcentual / 100));
                                $nombre_evaluacion = 'COMPLEMENTARIO';
                                $acta = ActaEvaluacion::with('alumnos')->where('tipo', 'C')->where('carrera_id', $request->carrera)->where('materia_id', $alumno_puntaje->materia_id)->where('semestre_id', $request->semestre)->first();
                            }

                            if ($evaluacion->tipo_evaluacion_id == 6) {
                                $puntaje_obtenido = $alumno_puntaje->puntos_obtenidos;
                                $total_puntos_final = (int) round($puntaje_obtenido * ($evaluacion->valor_porcentual / 100));
                                $nombre_evaluacion = 'EXTRAORDINARIO';
                                $acta = ActaEvaluacion::where('tipo', 'E')->where('carrera_id', $request->carrera)->where('materia_id', $alumno_puntaje->materia_id)->where('semestre_id', $request->semestre)->first();
                            }

                            $total_puntos_alumno = (int) round($total_puntos_proceso + $total_puntos_parcial + $total_puntos_final);

                            $carrera = Carrera::findOrFail($request->carrera);
                            $escala = Escala::with('escalaDetalles')->where('programa_id', $carrera->programa_id)->first();
                            $malla = Malla::where('carrera_id', $carrera->id)->first();
                            $malla_espejo = MallaEspejo::where('malla_paraguay_id', $malla->id)->first();

                            foreach ($escala->escalaDetalles as $detalle) {
                                if ($total_puntos_alumno >= $detalle->punto_minimo && $total_puntos_alumno <= $detalle->punto_maximo) {
                                    $nota = $detalle->nota;
                                }
                            }

                            if ($nota_ausente == 'AUSENTE') {
                                if ($evaluacion->tipo_evaluacion_id == 6) {
                                    $nota = 1;
                                } else {
                                    $nota = $nota_ausente;
                                }
                            }

                            $old_alumno_nota = AlumnoNota::where('alumno_id', $alumno_puntaje->alumno_id)->where('materia_id', $alumno_puntaje->materia_id)->where('carrera_id', $request->carrera)->where('semestre_id', $request->semestre)->first();
                            if (!$old_alumno_nota) {
                                $alumno_nota = new AlumnoNota();
                                $alumno_nota->alumno_id = $alumno_puntaje->alumno_id;
                                $alumno_nota->materia_id = $alumno_puntaje->materia_id;
                                $alumno_nota->carrera_id = $request->carrera;
                                $alumno_nota->semestre_id = $request->semestre;
                                $alumno_nota->evaluacion = $nombre_evaluacion;
                                $alumno_nota->calificacion = $nota;
                                $alumno_nota->save();

                                if ($malla_espejo && $nota != 'AUSENTE') {
                                    $doble_grado = MallaDetalle::where('malla_id', $malla->id)->where('materia_id', $alumno_puntaje->materia_id)->where('doble_grado', 'true')->count();
                                    if ($doble_grado == 1) {
                                        $escala_siu = Escala::where('programa_id', $malla_espejo->mallaSiu->carrera->programa_id)->first();
                                        $malla_espejo_detalles = MallaEspejoDetalle::where('malla_espejo_id', $malla_espejo->id)->get();
                                        foreach ($malla_espejo_detalles as $detalle) {
                                            if ($detalle->materia_paraguay_id == $alumno_puntaje->materia_id) {
                                                foreach ($escala_siu->escalaDetalles as $esiu) {
                                                    if ($total_puntos_alumno >= $esiu->punto_minimo && $total_puntos_alumno <= $esiu->punto_maximo) {
                                                        $nota_siu = $esiu->nota;
                                                    }
                                                }

                                                $alumno_nota_siu = new AlumnoNota();
                                                $alumno_nota_siu->alumno_id = $alumno_puntaje->alumno_id;
                                                $alumno_nota_siu->materia_id = $detalle->materia_siu_id;
                                                $alumno_nota_siu->carrera_id = $malla_espejo->mallaSiu->carrera_id;
                                                $alumno_nota_siu->semestre_id = $request->semestre;
                                                $alumno_nota_siu->evaluacion = 'ESPEJO DE ' . $alumno_puntaje->materia->nombre_fantasia;
                                                $alumno_nota_siu->calificacion = $nota_siu;
                                                $alumno_nota_siu->save();

                                                $matriculacion = Matriculacion::where('alumno_id', $alumno_puntaje->alumno_id)->where('semestre_id', $request->semestre)->where('carrera_id', $request->carrera)->first();
                                                $old_inscripcion = Inscripcion::where('matriculacion_id', $matriculacion->id)->where('materia_id', $alumno_puntaje->materia_id)->first();
                                                $inscripcion = new Inscripcion();
                                                $inscripcion->fecha = $old_inscripcion->fecha;
                                                $inscripcion->matriculacion_id = $matriculacion->id;
                                                $inscripcion->materia_id = $detalle->materia_siu_id;
                                                $inscripcion->estado = 'ES';
                                                $inscripcion->cargado_por_id = $old_inscripcion->cargado_por_id;
                                                $inscripcion->alumno_id = $alumno_puntaje->alumno_id;
                                                $inscripcion->docente_id = $old_inscripcion->docente_id;
                                                $inscripcion->save();
                                            }
                                        }
                                    }
                                }
                            } else {
                                $old_alumno_nota->calificacion = $nota;
                                $old_alumno_nota->save();

                                if ($malla_espejo && $nota != 'AUSENTE') {
                                    $doble_grado = MallaDetalle::where('malla_id', $malla->id)->where('materia_id', $alumno_puntaje->materia_id)->where('doble_grado', 'true')->count();
                                    if ($doble_grado == 1) {
                                        $escala_siu = Escala::where('programa_id', $malla_espejo->mallaSiu->carrera->programa_id)->first();
                                        $malla_espejo_detalles = MallaEspejoDetalle::where('malla_espejo_id', $malla_espejo->id)->get();
                                        foreach ($malla_espejo_detalles as $detalle) {
                                            if ($detalle->materia_paraguay_id == $alumno_puntaje->materia_id) {
                                                $old_alumno_nota_siu = AlumnoNota::where('alumno_id', $alumno_puntaje->alumno_id)->where('materia_id', $detalle->materia_siu_id)->where('carrera_id', $malla_espejo->mallaSiu->carrera_id)->where('semestre_id', $request->semestre)->first();
                                                foreach ($escala_siu->escalaDetalles as $esiu) {
                                                    if ($total_puntos_alumno >= $esiu->punto_minimo && $total_puntos_alumno <= $esiu->punto_maximo) {
                                                        $nota_siu = $esiu->nota;
                                                    }
                                                }

                                                $old_alumno_nota_siu->calificacion = $nota_siu;
                                                $old_alumno_nota_siu->save();

                                                $matriculacion = Matriculacion::where('alumno_id', $alumno_puntaje->alumno_id)->where('semestre_id', $request->semestre)->where('carrera_id', $request->carrera)->first();
                                                $old_inscripcion = Inscripcion::where('matriculacion_id', $matriculacion->id)->where('materia_id', $alumno_puntaje->materia_id)->first();
                                                $inscripcion = new Inscripcion();
                                                $inscripcion->fecha = $old_inscripcion->fecha;
                                                $inscripcion->matriculacion_id = $matriculacion->id;
                                                $inscripcion->materia_id = $detalle->materia_siu_id;
                                                $inscripcion->estado = 'ES';
                                                $inscripcion->cargado_por_id = $old_inscripcion->cargado_por_id;
                                                $inscripcion->alumno_id = $alumno_puntaje->alumno_id;
                                                $inscripcion->docente_id = $old_inscripcion->docente_id;
                                                $inscripcion->save();
                                            }
                                        }
                                    }
                                }
                            }

                            $acta_detalle = ActaEvaluacionAlumno::where('acta_evaluacion_id', $acta->id)->where('alumno_id', $alumno_puntaje->alumno_id)->first();
                            if ($acta_detalle) {
                                $acta_detalle->puntos_examen = $total_puntos_final;
                                $acta_detalle->calificacion = $nota;
                                $acta_detalle->save();
                            }

                            $semestre = Semestre::findOrFail($request->semestre);
                            $carrera = Carrera::findOrFail($request->carrera);
                            $matriculacion = Matriculacion::where('alumno_id', $alumno_puntaje->alumno_id)->where('semestre_id', $semestre->id)->where('programa_id', $carrera->programa_id)->where('carrera_id', $carrera->id)->first();
                            $inscripcion = Inscripcion::where('matriculacion_id', $matriculacion->id)->where('materia_id', $alumno_puntaje->materia_id)->where('alumno_id', $alumno_puntaje->alumno_id)->first();
                            if ($nota != 'AUSENTE') {
                                if ($nota != 1) { //Agregar el 1 de SIU
                                    $inscripcion->estado = 'AP';
                                    $inscripcion->save();
                                } else {
                                    if ($evaluacion->tipo_evaluacion_id == 6) {
                                        $inscripcion->estado = 'RE';
                                        $inscripcion->save();
                                    }
                                }
                            } else {
                                if ($evaluacion->tipo_evaluacion_id == 6) {
                                    $inscripcion->estado = 'RE';
                                    $inscripcion->save();
                                }
                            }
                        }
                    }
                }
            }
            $evaluacion = Evaluacion::findOrFail($alumno_puntaje->evaluacion_id);
            $materia = Materia::findOrFail($request->materia);

            DB::commit();

            if ($request->generado_docente == 'SI') {
                return redirect()->route('pantallas_docentes.index', Auth::id())->with('success-message', 'La evaluación ' . $evaluacion->nombre . ' de la materia ' . $materia->nombre_fantasia . ' fue cargada exitosamente.');
            } else {
                return response()->json([
                    'message' => 'La evaluación ' . $evaluacion->nombre . ' de la materia ' . $materia->nombre_fantasia . ' fue cargada exitosamente.',
                ]);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('materias_semestres.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_puntajes_evaluaciones_materias_semestres');

        $request->validate([
            'puntos_posibles' => ['required', 'numeric'],
            'puntos_obtenidos' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        DB::beginTransaction();

        try {

            $alumno_puntaje = AlumnoPuntaje::findOrFail($id);
            if ($alumno_puntaje->semestre->estado == 'IN') {
                return back()->with('error-message', 'El puntaje del alumno no puede actualizarse. El semestre se encuentra cerrado.');
            }
            $alumno_puntaje->puntos_obtenidos = $request->puntos_obtenidos;
            $alumno_puntaje->actualizado_por_id = Auth::id();
            $alumno_puntaje->save();

            $alumno = Alumno::findOrFail($alumno_puntaje->alumno_id);
            $evaluacion = Evaluacion::findOrFail($alumno_puntaje->evaluacion_id);
            $materia = Materia::findOrFail($alumno_puntaje->materia_id);

            DB::commit();

            return response()->json([
                'message' => 'La evaluación ' . $evaluacion->nombre . ' del alumno ' . $alumno->primer_nombre . ' ' . $alumno->primer_apellido . ' en la materia ' . $materia->nombre_fantasia . ' fue actualizada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('materias_semestres.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_create_ajax($tipo_id, $materia_id, $semestre_id, $carrera_id)
    {
        $this->authorize('crear_evaluaciones_materias_semestres');

        DB::beginTransaction();

        try {
            $materia = Materia::findOrFail($materia_id);
            $semestre = Semestre::findOrFail($semestre_id);
            $carrera = Carrera::findOrFail($carrera_id);

            $evaluacion = Evaluacion::where('tipo_evaluacion_id', $tipo_id)->first();
            $puntaje_minimo_requerido = $evaluacion->puntaje_minimo_requerido;

            if ($tipo_id == 1 || $tipo_id == 2 || $tipo_id == 3) {
                $alumnos = Alumno::select(['alumnos.*'])
                    ->whereHas('matriculaciones', function ($query) use ($semestre_id, $carrera_id, $materia_id) {
                        $query->where('semestre_id', $semestre_id)
                            ->where('carrera_id', $carrera_id)
                            ->whereHas('inscripciones', function ($query) use ($materia_id) {
                                $query->where('materia_id', $materia_id)
                                    ->where('estado', 'EC');
                            });
                    })
                    ->groupBy('alumnos.id')
                    ->orderBy('primer_apellido', 'asc')
                    ->get();
            } else if ($tipo_id == 4) {
                $alumnos = Alumno::select(['alumnos.*'])
                    ->whereHas('actaEvaluacionAlumno', function ($query) use ($semestre_id, $carrera_id, $materia_id) {
                        $query->whereHas('actaEvaluacion', function ($query) use ($semestre_id, $carrera_id, $materia_id) {
                            $query->where('tipo', 'O')
                                ->where('semestre_id', $semestre_id)
                                ->where('carrera_id', $carrera_id)
                                ->where('materia_id', $materia_id);
                        });
                    })
                    ->groupBy('alumnos.id')
                    ->orderBy('primer_apellido', 'asc')
                    ->get();
            } else if ($tipo_id == 5) {
                $alumnos = Alumno::select(['alumnos.*'])
                    ->whereHas('actaEvaluacionAlumno', function ($query) use ($semestre_id, $carrera_id, $materia_id) {
                        $query->whereHas('actaEvaluacion', function ($query) use ($semestre_id, $carrera_id, $materia_id) {
                            $query->where('tipo', 'C')
                                ->where('semestre_id', $semestre_id)
                                ->where('carrera_id', $carrera_id)
                                ->where('materia_id', $materia_id);
                        });
                    })
                    ->groupBy('alumnos.id')
                    ->orderBy('primer_apellido', 'asc')
                    ->get();
            } else if ($tipo_id == 6) {
                $alumnos = Alumno::select(['alumnos.*'])
                    ->whereHas('actaEvaluacionAlumno', function ($query) use ($semestre_id, $carrera_id, $materia_id) {
                        $query->whereHas('actaEvaluacion', function ($query) use ($semestre_id, $carrera_id, $materia_id) {
                            $query->where('tipo', 'E')
                                ->where('semestre_id', $semestre_id)
                                ->where('carrera_id', $carrera_id)
                                ->where('materia_id', $materia_id);
                        });
                    })
                    ->groupBy('alumnos.id')
                    ->orderBy('primer_apellido', 'asc')
                    ->get();
            }

            $acta = ActaEvaluacion::with('alumnos')->where('semestre_id', $semestre_id)->where('carrera_id', $carrera_id)->where('materia_id', $materia_id);
            $cantidad_actas = $acta->count();

            return response()->json([
                'alumnos' => $alumnos,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('materias_semestres.index')->with('error-message', $e->getMessage());
        }
    }
}
