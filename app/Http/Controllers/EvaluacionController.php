<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use App\Models\Evaluacion;
use App\Models\TipoEvaluacion;


class EvaluacionController extends Controller
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

    public function index()
    {
        $this->authorize('ver_evaluaciones');

        try {
            $evaluaciones = Evaluacion::orderBy('id', 'asc')->get();
            return view('evaluaciones/index')->with(compact('evaluaciones'));
        } catch (\Exception $e) {
            return redirect()->route('evaluaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_evaluaciones');

        try {
            $evaluacion = Evaluacion::findOrFail($id);
            return view('evaluaciones/show')->with(compact('evaluacion'));
        } catch (\Exception $e) {
            return redirect()->route('evaluaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_evaluaciones');

        try {
            $tipos_evaluaciones = TipoEvaluacion::where('estado', 'AC')->get();
            return view('evaluaciones/create')->with(compact('tipos_evaluaciones'));
        } catch (\Exception $e) {
            return redirect()->route('evaluaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_evaluaciones');

        $request->validate([
            'nombre_evaluacion' => ['required', Rule::unique('evaluaciones', 'nombre')],
            'tipo_evaluacion' => 'required',
            'puntos' => ['required', 'numeric', 'min:1', 'max:100'],
            'valor_porcentual' => ['required', 'numeric', 'min:1', 'max:100'],
            'puntaje_minimo_requerido' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        DB::beginTransaction();

        try {
            $evaluacion = new Evaluacion();
            $evaluacion->nombre = removeAccents(Str::upper($request->nombre_evaluacion));
            $evaluacion->tipo_evaluacion_id = $request->tipo_evaluacion;
            $evaluacion->puntos = $request->puntos;
            $evaluacion->valor_porcentual = $request->valor_porcentual;
            $evaluacion->puntaje_minimo_requerido = $request->puntaje_minimo_requerido;
            $evaluacion->cargado_por_id = Auth::id();
            $evaluacion->save();

            DB::commit();

            return redirect()->route('evaluaciones.index')->with('success-message', 'La evaluación ' . $evaluacion->nombre . ' fue creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('evaluaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_evaluaciones');

        try {
            $evaluacion = Evaluacion::findOrFail($id);
            $tipos_evaluaciones = TipoEvaluacion::where('estado', 'AC')->get();
            return view('evaluaciones/edit')->with(compact('evaluacion', 'tipos_evaluaciones'));
        } catch (\Exception $e) {
            return redirect()->route('evaluaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_evaluaciones');

        $request->validate([
            'nombre_evaluacion' => ['required', Rule::unique('evaluaciones', 'nombre')->ignore($id)],
            'tipo_evaluacion' => 'required',
            'puntos' => ['required', 'numeric', 'min:1', 'max:100'],
            'valor_porcentual' => ['required', 'numeric', 'min:1', 'max:100'],
            'puntaje_minimo_requerido' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        DB::beginTransaction();

        try {
            $evaluacion = Evaluacion::findOrFail($id);
            $evaluacion->nombre = removeAccents(Str::upper($request->nombre_evaluacion));
            $evaluacion->tipo_evaluacion_id = $request->tipo_evaluacion;
            $evaluacion->puntos = $request->puntos;
            $evaluacion->valor_porcentual = $request->valor_porcentual;
            $evaluacion->puntaje_minimo_requerido = $request->puntaje_minimo_requerido;
            $evaluacion->actualizado_por_id = Auth::id();
            $evaluacion->save();

            DB::commit();

            return redirect()->route('evaluaciones.index')->with('success-message', 'La evaluación ' . $evaluacion->nombre . ' fue actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('evaluaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_evaluaciones');

        DB::beginTransaction();

        try {
            $evaluacion = Evaluacion::findOrFail($id);
            $evaluacion->actualizado_por_id = Auth::id();
            $evaluacion->estado = 'IN';
            $evaluacion->save();

            DB::commit();

            return redirect()->route('evaluaciones.index')->with('error-message','La evaluación ' . $evaluacion->nombre . ' fue inactivada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('evaluaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_evaluaciones');

        DB::beginTransaction();

        try {
            $evaluacion = Evaluacion::findOrFail($id);
            $evaluacion->actualizado_por_id = Auth::id();
            $evaluacion->estado = 'AC';
            $evaluacion->save();

            DB::commit();

            return redirect()->route('evaluaciones.index')->with('success-message','La evaluación ' . $evaluacion->nombre . ' fue activada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('evaluaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_evaluaciones');

        DB::beginTransaction();

        try {
            $evaluacion = Evaluacion::findOrFail($id);
            $evaluacion->delete();

            DB::commit();

            return redirect()->route('evaluaciones.index')->with('success-message','La evaluación ' . $evaluacion->nombre . ' fue eliminada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('evaluaciones.index')->with('error-message', 'La evaluación ' . $evaluacion->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('evaluaciones.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
