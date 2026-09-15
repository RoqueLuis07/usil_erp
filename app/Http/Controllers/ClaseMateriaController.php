<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use App\Models\ClaseMateria;
use App\Models\Materia;
use App\Models\Semestre;
use App\Models\Carrera;
use App\Models\Malla;
use App\Models\SemestreMalla;
use App\Models\SemestreMallaMateria;
use App\Models\Modalidad;
use App\Models\AlumnoAsistencia;
use App\Models\Docente;
use App\Models\ActaEvaluacion;
use App\Models\Matriculacion;
use App\Models\Inscripcion;


class ClaseMateriaController extends Controller
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

    public function index(Request $request)
    {
        $this->authorize('ver_materias_clases');

        try {
            $buscar = Str::upper($request->buscar);
            $filtro_fecha = $request->filtro_fecha;
            $filtro_materia = $request->filtro_materia;
            $filtro_carrera = $request->filtro_carrera;
            $filtro_docente = $request->filtro_docente;
            $filtro_periodo = $request->filtro_periodo;
            $filtro_inscriptos = $request->filtro_inscriptos;

            $query = ClaseMateria::select('clases_materias.*')
                        ->join('materias', 'clases_materias.materia_id', '=', 'materias.id')
                        ->join('alumnos_asistencias', 'clases_materias.id', '=', 'alumnos_asistencias.clase_materia_id')
                        ->orderBy('clases_materias.fecha_hora', 'desc')
                        ->orderBy('materias.nombre_fantasia', 'asc')
                        ->with(['materia', 'docente', 'semestre', 'modalidad', 'alumnoAsistencias']);

            if (!(blank($filtro_fecha))) {
                $query->where('clases_materias.fecha_hora', $filtro_fecha);
            }

            if (!(blank($filtro_materia))) {
                $query->where('clases_materias.materia_id', $filtro_materia);
            }

            if (!(blank($filtro_carrera))) {
                if ($filtro_carrera == 'GENERADO POR MATERIA') {
                    $query->whereNull('clases_materias.carrera_id');
                } else {
                    $query->whereHas('carrera', function ($sub) use ($filtro_carrera) {
                        $sub->where('nombre_fantasia', $filtro_carrera);
                    });
                }
            }

            if (!(blank($filtro_docente))) {
                $query->where('clases_materias.docente_id', $filtro_docente);
            }

            if (!(blank($filtro_periodo))) {
                $query->where('clases_materias.semestre_id', $filtro_periodo);
            }

            if (!(blank($filtro_inscriptos))) {
                switch ($filtro_inscriptos) {
                    case '0-10':
                        $query->has('alumnoAsistencias', '<=', 10);
                        break;
                    case '11-20':
                        $query->has('alumnoAsistencias', '>=', 11)->has('alumnoAsistencias', '<=', 20);
                        break;
                    case '21-30':
                        $query->has('alumnoAsistencias', '>=', 21)->has('alumnoAsistencias', '<=', 30);
                        break;
                    case '31-40':
                        $query->has('alumnoAsistencias', '>=', 31)->has('alumnoAsistencias', '<=', 40);
                        break;
                    case '41-50':
                        $query->has('alumnoAsistencias', '>=', 41)->has('alumnoAsistencias', '<=', 50);
                        break;
                    case '51':
                        $query->has('alumnoAsistencias', '>=', 51);
                        break;
                }
            }

            if (!(blank($buscar))) {
				$query->where(function($q) use ($buscar) {
                    $q->where('clases_materias.id', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%')
                      ->orWhere('clases_materias.fecha_hora', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%')
                      ->orWhereHas('materia', function ($sub) use ($buscar) {
                          $sub->where('nombre_fantasia', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%')
                              ->orWhere('codigo', $buscar);
                      })
                      ->orWhereHas('docente', function ($sub) use ($buscar) {
                          $sub->where('primer_nombre', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%')
                              ->orWhere('primer_apellido', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%')
                              ->orWhere('segundo_apellido', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%')
                              ->orWhere('numero_documento', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%');
                      })
                      ->orWhereHas('semestre', function ($sub) use ($buscar) {
                          $sub->where('nombre', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%');
                      })
                      ->orWhereHas('modalidad', function ($sub) use ($buscar) {
                          $sub->where('nombre', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%');
                      });
                });
			}

            $clases_materias = $query->paginate(50);

            $semestres = Semestre::orderBy('id', 'desc')->get();
            $materias = Materia::where('estado', 'AC')->orderBy('nombre_fantasia', 'asc')->get();
            $carreras = Carrera::where('estado', 'AC')->orderBy('nombre_fantasia', 'asc')->get();
            $docentes = Docente::where('estado', 'AC')->orderBy('primer_nombre', 'asc')->get();
            $modalidades = Modalidad::where('estado', 'AC')->get();

            return view('clases_materias/index')->with(compact('clases_materias', 'semestres', 'materias', 'carreras', 'docentes', 'modalidades', 'buscar', 'filtro_fecha', 'filtro_materia', 'filtro_carrera', 'filtro_docente', 'filtro_periodo', 'filtro_inscriptos'));
        } catch (\Exception $e) {
            return redirect()->route('clases_materias.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_materias_clases');

        try {
            $clase = ClaseMateria::with('alumnoAsistencias')->findOrFail($id);

            return view('clases_materias/show')->with(compact('clase'));
        } catch (\Exception $e) {
            return redirect()->route('clases_materias.index')->with('error-message', $e->getMessage());
        }
    }

    public function create_materia($materia_id, $semestre_id, $carrera_id)
    {
        $this->authorize('crear_clases_materias_semestres');

        try {
            if (ActaEvaluacion::where('materia_id', $materia_id)->where('semestre_id', $semestre_id)->where('carrera_id', $carrera_id)->exists()) {
                return redirect()->route('materias_semestres.index')->with('error-message', 'No se puede generar una clase, ya existe un acta de evaluación generado.');
            }

            $materia = Materia::findOrFail($materia_id);
            $semestre = Semestre::findOrFail($semestre_id);
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
                return redirect()->route('materias_semestres.index')->with('error-message', 'No se puede generar una clase sin seleccionar previamente un docente para la materia.');
            }

            $clase = ClaseMateria::where('materia_id', $materia_id)->where('semestre_id', $semestre_id)->orderBy('id', 'desc')->first();
            if ($clase) {
                if ($clase->alumnoAsistencias->count() == 0 && $semestre_malla->malla->carrera->programa_id != 3) {
                    return back()->with('error-message', 'No se puede crear una clase, debe completar la asistencia de la anterior para continuar.');
                }
            }

            return view('clases_materias/create')->with(compact('materia', 'semestre', 'carrera', 'modalidades', 'docente'));
        } catch (\Exception $e) {
            return redirect()->route('materias_semestres.index')->with('error-message', $e->getMessage());
        }
    }

    public function create_carrera($materia_id, $semestre_id, $carrera_id)
    {
        $this->authorize('crear_clases_materias_semestres');

        try {
            if (ActaEvaluacion::where('materia_id', $materia_id)->where('semestre_id', $semestre_id)->where('carrera_id', $carrera_id)->exists()) {
                return redirect()->route('materias_semestres.index')->with('error-message', 'No se puede generar una clase, ya existe un acta de evaluación generado.');
            }

            $materia = Materia::findOrFail($materia_id);
            $semestre = Semestre::findOrFail($semestre_id);
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
                return redirect()->route('materias_semestres.index')->with('error-message', 'No se puede generar una clase sin seleccionar previamente un docente para la materia.');
            }

            $clase = ClaseMateria::where('materia_id', $materia_id)->where('semestre_id', $semestre_id)->orderBy('id', 'desc')->first();
            if ($clase) {
                if ($clase->alumnoAsistencias->count() == 0) {
                    return back()->with('error-message', 'No se puede crear una clase, debe completar la asistencia de la anterior para continuar.');
                }
            }

            return view('clases_materias/create')->with(compact('materia', 'semestre', 'carrera' ,'modalidades', 'docente'));
        } catch (\Exception $e) {
            return redirect()->route('materias_semestres.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_clases_materias_semestres');

        $request->validate([
            'fecha_hora' => ['required', 'date'],
            'tema_desarrollado' => 'required',
            'horas_desarrollo' => ['required', 'numeric'],
            'modalidad' => ['required', 'numeric'],
            'observaciones' => 'nullable'
        ]);

        DB::beginTransaction();

        try {
            $clase_materia = new ClaseMateria();
            $clase_materia->docente_id = $request->docente;
            if ($request->carrera) {
                $clase_materia->carrera_id = $request->carrera;
            }
            $clase_materia->materia_id = $request->materia;
            $clase_materia->semestre_id = $request->semestre;
            $clase_materia->fecha_hora = $request->fecha_hora;
            $clase_materia->tema_desarrollado = removeAccents(Str::upper($request->tema_desarrollado));
            $clase_materia->horas_desarrollo = $request->horas_desarrollo;
            $clase_materia->modalidad_id = $request->modalidad;
            $clase_materia->observaciones = $request->observaciones;
            $clase_materia->save();

            $matriculaciones = Matriculacion::where('semestre_id', $clase_materia->semestre_id)->get();
            foreach ($matriculaciones as $matriculacion) {
                $inscripciones = Inscripcion::where('matriculacion_id', $matriculacion->id)->where('materia_id', $clase_materia->materia_id)->get();
                foreach ($inscripciones as $inscripcion) {
                    if ($inscripcion->estado == 'MA') {
                        $inscripcion->estado = 'EC';
                        $inscripcion->save();
                    }
                }
            }

            DB::commit();

            if ($request->generado_docente == 'SI') {
                return redirect()->route('pantallas_docentes.index', Auth::id())->with('success-message', 'La clase de la materia ' . $clase_materia->materia->nombre_fantasia . ' fue creada existosamente.');
            } else {
                return redirect()->route('clases_materias.index')->with('success-message', 'La clase de la materia ' . $clase_materia->materia->nombre_fantasia . ' fue creada existosamente.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('materias_semestres.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_materias_clases');

        DB::beginTransaction();

        try {
            $clase = ClaseMateria::findOrFail($id);
            if ($clase->semestre->estado == 'IN') {
                return back()->with('error-message', 'La clase no se puede eliminar. El semestre se encuentra cerrado.');
            }
            $asistencia = AlumnoAsistencia::where('clase_materia_id', $clase->id)->delete();
            $clase->delete();

            DB::commit();

            return redirect()->route('clases_materias.index')->with('success-message', 'La clase y asistencia de la materia ' . $clase->materia->nombre_fantasia . ' fue eliminada existosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('clases_materias.index')->with('error-message', 'La clase de la materia ' . $clase->materia->nombre_fantasia . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('clases_materias.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
