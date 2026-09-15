<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use App\Models\ClaseMaestria;
use App\Models\Curso;
use App\Models\Modulo;
use App\Models\CursoModulo;
use App\Models\Modalidad;
use App\Models\AlumnoAsistenciaUbs;
use App\Models\Docente;
use App\Models\ActaEvaluacionUbs;
use App\Models\InscripcionUbs;
use App\Models\InscripcionModulo;


class ClaseMaestriaController extends Controller
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
        $this->authorize('ver_clases_maestrias_ubs');

        try {
            $clases_maestrias = ClaseMaestria::where('curso_id', $curso_id)->orderBy('fecha_hora', 'desc')->get();
            $curso = Curso::with(['modulos' => function ($query) {
                $query->orderBy('id', 'asc');
            }])->findOrFail($curso_id);
            $docentes = Docente::where('ubs', true)->where('estado', 'AC')->orderBy('primer_nombre', 'asc')->get();

            return view('ubs/maestrias/clases/index')->with(compact('clases_maestrias', 'curso', 'docentes'));
        } catch (\Exception $e) {
            return redirect()->route('maestrias.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_clases_maestrias_ubs');

        try {
            $clase = ClaseMaestria::findOrFail($id);
            $asistencias = AlumnoAsistenciaUbs::where('curso_id', $clase->curso_id)->where('modulo_id', $clase->modulo_id)->get();

            return view('ubs/maestrias/clases/show')->with(compact('clase', 'asistencias'));
        } catch (\Exception $e) {
            return redirect()->route('clases_maestrias.index', $clase->curso_id)->with('error-message', $e->getMessage());
        }
    }

    public function create($curso_id, $modulo_id)
    {
        $this->authorize('crear_clases_maestrias_ubs');

        try {
            // if (ActaEvaluacionUbs::where('curso_id', $curso_id)->where('modulo_id', $modulo_id)->exists()) {
            //     return redirect()->route('clases_maestrias.index', $curso_id)->with('error-message', 'No se puede generar una clase, ya existe un acta de evaluación generado.');
            // }

            $curso = Curso::findOrFail($curso_id);
            $modulo = Modulo::findOrFail($modulo_id);
            $modalidades = Modalidad::where('estado', 'AC')->get();
            $curso_modulo = CursoModulo::where('curso_id', $curso_id)->where('modulo_id', $modulo_id)->first();
            $docente = Docente::findOrFail($curso_modulo->docente_id);

            return view('ubs/maestrias/clases/create')->with(compact('curso', 'modulo', 'modalidades', 'docente'));
        } catch (\Exception $e) {
            return redirect()->route('clases_maestrias.index', $curso_id)->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request, $curso_id, $modulo_id)
    {
        $this->authorize('crear_clases_maestrias_ubs');

        $request->validate([
            'fecha_hora' => ['required', 'date'],
            'tema_desarrollado' => 'required',
            'horas_desarrollo' => ['required', 'numeric'],
            'modalidad' => ['required', 'numeric'],
            'observaciones' => 'nullable'
        ]);

        DB::beginTransaction();

        try {
            $clase_maestria = new ClaseMaestria();
            $clase_maestria->docente_id = $request->docente;
            $clase_maestria->curso_id = $curso_id;
            $clase_maestria->modulo_id = $modulo_id;
            $clase_maestria->fecha_hora = $request->fecha_hora;
            $clase_maestria->tema_desarrollado = removeAccents(Str::upper($request->tema_desarrollado));
            $clase_maestria->horas_desarrollo = $request->horas_desarrollo;
            $clase_maestria->modalidad_id = $request->modalidad;
            $clase_maestria->observaciones = $request->observaciones;
            $clase_maestria->save();

            $inscripciones = InscripcionModulo::where('curso_id', $clase_maestria->curso_id)->where('modulo_id', $clase_maestria->modulo_id)->get();
            foreach ($inscripciones as $inscripcion) {
                if ($inscripcion->estado == 'MA') {
                    $inscripcion->estado = 'EC';
                    $inscripcion->save();
                }
            }

            DB::commit();

            if ($request->generado_docente == 'SI') {
                return redirect()->route('pantallas_docentes.index', Auth::id())->with('success-message', 'La clase de ' . $clase_maestria->curso->nombre_fantasia . ' del módulo ' . $clase_maestria->modulo->nombre_fantasia . ' fue creada existosamente.');
            } else {
                return redirect()->route('clases_maestrias.index', $curso_id)->with('success-message', 'La clase de ' . $clase_maestria->curso->nombre_fantasia . ' del módulo ' . $clase_maestria->modulo->nombre_fantasia . ' fue creada existosamente.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('clases_maestrias.index', $curso_id)->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_clases_maestrias_ubs');

        DB::beginTransaction();

        try {
            $clase = ClaseMaestria::findOrFail($id);
            $asistencia = AlumnoAsistenciaUbs::where('curso_id', $clase->curso_id)->where('modulo_id', $clase->modulo_id)->delete();
            $clase->delete();

            DB::commit();

            return redirect()->route('clases_maestrias.index', $clase->curso_id)->with('success-message', 'La clase y asistencia de ' . $clase->curso->nombre_fantasia . ' fue eliminada existosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                $clase = ClaseMaestria::findOrFail($id);
                return redirect()->route('clases_maestrias.index', $clase->curso_id)->with('error-message', 'La clase de ' . $clase->curso->nombre_fantasia . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('clases_maestrias.index', $clase->curso_id)->with('error-message', $e->getMessage());
            }
        }
    }
}
