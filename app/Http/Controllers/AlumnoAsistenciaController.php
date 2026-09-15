<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use App\Models\AlumnoAsistencia;
use App\Models\ClaseMateria;
use App\Models\Materia;
use App\Models\Semestre;
use App\Models\Carrera;
use App\Models\Malla;
use App\Models\SemestreMalla;
use App\Models\SemestreMallaMateria;
use App\Models\Modalidad;
use App\Models\Alumno;
use App\Models\Docente;
use App\Models\ActaEvaluacion;


class AlumnoAsistenciaController extends Controller
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


    public function create($materia_id, $semestre_id, $docente_id)
    {
        $this->authorize('crear_alumnos_asistencias_materias_semestres');

        try {
            // if (ActaEvaluacion::where('materia_id', $materia_id)->where('semestre_id', $semestre_id)->where('carrera_id', $carrera_id)->exists()) {
            //     return redirect()->route('materias_semestres.index')->with('error-message', 'No se puede generar asistencias de clase, ya existe un acta de evaluación generado.');
            // }

            $materia = Materia::findOrFail($materia_id);
            $semestre = Semestre::findOrFail($semestre_id);
            $carrera = collect();
            $modalidades = Modalidad::where('estado', 'AC')->get();
            $docente = Docente::findOrFail($docente_id);

            $clase = ClaseMateria::where('materia_id', $materia_id)->where('semestre_id', $semestre_id)->orderBy('id', 'desc')->first();
            if ($clase) {
                if ($clase->alumnoAsistencias->count() != 0) {
                    return back()->with('error-message', 'No se pueden agregar las asistencias porque ya fue cargada anteriormente.');
                }
            } else {
                return back()->with('error-message', 'No se pueden agregar las asistencias, debe generar una clase previamente.');
            }

            $alumnos = Alumno::select(['alumnos.*'])
            ->whereHas('matriculaciones', function ($query) use ($semestre_id, $materia_id) {
                $query->where('semestre_id', $semestre_id)
                    ->whereHas('inscripciones', function ($query) use ($materia_id) {
                        $query->where('materia_id', $materia_id)
                            ->whereIn('estado', ['MA', 'EC']);
                });
            })
            ->groupBy('alumnos.id')
            ->get();

            return view('alumnos_asistencias/create')->with(compact('materia', 'semestre', 'carrera', 'modalidades', 'docente', 'alumnos', 'clase'));
        } catch (\Exception $e) {
            return redirect()->route('clases_materias.index')->with('error-message', $e->getMessage());
        }
    }

    public function create_materia($materia_id, $semestre_id, $carrera_id)
    {
        $this->authorize('crear_alumnos_asistencias_materias_semestres');

        try {
            if (ActaEvaluacion::where('materia_id', $materia_id)->where('semestre_id', $semestre_id)->where('carrera_id', $carrera_id)->exists()) {
                return redirect()->route('materias_semestres.index')->with('error-message', 'No se puede generar asistencias de clase, ya existe un acta de evaluación generado.');
            }

            $materia = Materia::findOrFail($materia_id);
            $semestre = Semestre::findOrFail($semestre_id);
            if ($semestre->estado == 'IN') {
                return back()->with('error-message', 'Las asistencias no pueden guardarse. El semestre se encuentra cerrado.');
            }
            $carrera = collect();
            $modalidades = Modalidad::where('estado', 'AC')->get();

            $malla = Malla::where('carrera_id', $carrera_id)->first();
            $semestre_malla = SemestreMalla::where('semestre_id', $semestre_id)->where('malla_id', $malla->id)->first();
            $semestre_malla_materia = SemestreMallaMateria::with('semestreMalla.malla.mallaDetalles')->where('materia_id', $materia_id)->where('semestre_malla_id', $semestre_malla->id)->first();
            foreach ($semestre_malla_materia->semestreMalla->malla->mallaDetalles as $detalle) {
                if ($detalle->materia_id == $materia->id) {
                    $semestre_materia = $detalle->semestre;
                }
            }
            $docente = $semestre_malla_materia->docente;
            if (!$docente) {
                return redirect()->route('materias_semestres.index')->with('error-message', 'No se puede generar una asistencia a clase sin seleccionar previamente un docente para la materia.');
            }

            $clase = ClaseMateria::where('materia_id', $materia_id)->where('semestre_id', $semestre_id)->orderBy('id', 'desc')->first();
            if ($clase) {
                if ($clase->alumnoAsistencias->count() != 0) {
                    return back()->with('error-message', 'No se pueden agregar las asistencias porque ya fue cargada anteriormente.');
                }
            } else {
                return back()->with('error-message', 'No se pueden agregar las asistencias, debe generar una clase previamente.');
            }

            $alumnos = Alumno::select(['alumnos.*'])
            ->whereHas('matriculaciones', function ($query) use ($semestre_id, $materia_id) {
                $query->where('semestre_id', $semestre_id)
                    ->whereHas('inscripciones', function ($query) use ($materia_id) {
                        $query->where('materia_id', $materia_id)
                            ->whereIn('estado', ['MA', 'EC']);
                });
            })
            ->groupBy('alumnos.id')
            ->get();

            return view('alumnos_asistencias/create')->with(compact('materia', 'semestre', 'carrera', 'modalidades', 'docente', 'alumnos', 'clase'));
        } catch (\Exception $e) {
            return redirect()->route('materias_semestres.index')->with('error-message', $e->getMessage());
        }
    }

    public function create_carrera($materia_id, $semestre_id, $carrera_id)
    {
        $this->authorize('crear_alumnos_asistencias_materias_semestres');

        try {
            if (ActaEvaluacion::where('materia_id', $materia_id)->where('semestre_id', $semestre_id)->where('carrera_id', $carrera_id)->exists()) {
                return redirect()->route('materias_semestres.index')->with('error-message', 'No se puede generar asistencias de clase, ya existe un acta de evaluación generado.');
            }

            $materia = Materia::findOrFail($materia_id);
            $semestre = Semestre::findOrFail($semestre_id);
            if ($semestre->estado == 'IN') {
                return back()->with('error-message', 'El acta de evaluación no puede ser generado. El semestre se encuentra cerrado.');
            }
            $carrera = Carrera::findOrFail($carrera_id);
            $modalidades = Modalidad::where('estado', 'AC')->get();

            $malla = Malla::where('carrera_id', $carrera_id)->first();
            $semestre_malla = SemestreMalla::where('semestre_id', $semestre_id)->where('malla_id', $malla->id)->first();
            $semestre_malla_materia = SemestreMallaMateria::with('semestreMalla.malla.mallaDetalles')->where('materia_id', $materia_id)->where('semestre_malla_id', $semestre_malla->id)->first();
            foreach ($semestre_malla_materia->semestreMalla->malla->mallaDetalles as $detalle) {
                if ($detalle->materia_id == $materia->id) {
                    $semestre_materia = $detalle->semestre;
                }
            }
            $docente = $semestre_malla_materia->docente;
            if (!$docente) {
                return redirect()->route('materias_semestres.index')->with('error-message', 'No se puede generar una asistencia sin seleccionar previamente un docente para la materia.');
            }

            $clase = ClaseMateria::where('materia_id', $materia_id)->where('semestre_id', $semestre_id)->orderBy('id', 'desc')->first();
            if ($clase) {
                if ($clase->alumnoAsistencias->count() != 0) {
                    return back()->with('error-message', 'No se pueden agregar las asistencias porque ya fue cargada anteriormente.');
                }
            } else {
                return back()->with('error-message', 'No se pueden agregar las asistencias, debe generar una clase previamente.');
            }

            $alumnos = Alumno::select(['alumnos.*'])
        ->whereHas('matriculaciones', function ($query) use ($semestre_id, $materia_id, $carrera_id) {
            $query->where('semestre_id', $semestre_id)
                ->where('carrera_id', $carrera_id)
                ->whereHas('inscripciones', function ($query) use ($materia_id) {
                    $query->where('materia_id', $materia_id)
                        ->whereIn('estado', ['MA', 'EC']);
            });
        })
        ->groupBy('alumnos.id')
        ->get();

            return view('alumnos_asistencias/create')->with(compact('materia', 'semestre', 'carrera' ,'modalidades', 'docente', 'alumnos', 'clase'));
        } catch (\Exception $e) {
            return redirect()->route('materias_semestres.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_alumnos_asistencias_materias_semestres');

        $request->validate([
            'detalles' => ['required', 'array'],
            'detalles.*.alumno' => ['required', 'numeric'],
            'detalles.*.estado' => ['required', 'size:2'],
            'detalles.*.observaciones' => 'nullable'
        ]);

        DB::beginTransaction();

        try {
            $clase = ClaseMateria::where('materia_id', $request->materia)->where('semestre_id', $request->semestre)->orderBy('id', 'desc')->first();
            if ($clase->alumnoAsistencias->count() != 0) {
                return back()->with('error-message', 'No se puede agregar la asistencia porque ya fue cargada anteriormente');
            }

            foreach ($request->detalles as $detalle) {
                $alumno_asistencia = new AlumnoAsistencia();
                $alumno_asistencia->clase_materia_id = $clase->id;
                $alumno_asistencia->materia_id = $request->materia;
                $alumno_asistencia->semestre_id = $request->semestre;
                $alumno_asistencia->alumno_id = $detalle['alumno'];
                $alumno_asistencia->horas_desarrollo = $clase->horas_desarrollo;
                $alumno_asistencia->estado = $detalle['estado'];
                $alumno_asistencia->observaciones = removeAccents(Str::upper($detalle['observaciones']));
                $alumno_asistencia->save();
            }

            DB::commit();

            if ($request->generado_docente == 'SI') {
                return redirect()->route('pantallas_docentes.index', Auth::id())->with('success-message', 'La asistencia de los alumnos en la clase de la materia ' . $clase->materia->nombre_fantasia . ' fue creada existosamente.');
            } else {
                return redirect()->route('clases_materias.index')->with('success-message', 'La asistencia de los alumnos en la clase de la materia ' . $clase->materia->nombre_fantasia . ' fue creada existosamente.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('clases_materias.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_alumnos_asistencias_materias_clases');

        DB::beginTransaction();

        try {
            $asistencia = AlumnoAsistencia::findOrFail($id);
            if ($asistencia->semestre->estado == 'IN') {
                return back()->with('error-message', 'La asistencia no se puede actualizar. El semestre se encuentra cerrado.');
            }
            $asistencia->estado = $request->estado;
            $asistencia->save();

            DB::commit();

            $clase = ClaseMateria::findOrFail($asistencia->clase_materia_id);

            return response()->json([
                'message' => 'La asistencia del alumno ' . $asistencia->alumno->primer_nombre . ' ' . $asistencia->alumno->primer_apellido . ' fue actualizada correctamente.',
            ]);
        } catch (\Exception $e) {
            return redirect()->route('clases_materias.index')->with('error-message', $e->getMessage());
        }
    }

    public function update_observacion(Request $request, $id)
    {
        $this->authorize('editar_alumnos_asistencias_materias_clases');

        DB::beginTransaction();

        try {
            $asistencia = AlumnoAsistencia::findOrFail($id);
            $asistencia->observaciones = removeAccents(Str::upper($request->observaciones));
            $asistencia->save();

            DB::commit();

            $clase = ClaseMateria::findOrFail($asistencia->clase_materia_id);

            return response()->json([
                'message' => 'La observación de la asistencia del alumno ' . $asistencia->alumno->primer_nombre . ' ' . $asistencia->alumno->primer_apellido . ' fue actualizada correctamente.',
            ]);
        } catch (\Exception $e) {
            return redirect()->route('clases_materias.index')->with('error-message', $e->getMessage());
        }
    }
}
