<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use App\Models\TipoEvaluacion;


class TipoEvaluacionController extends Controller
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
        $this->authorize('ver_tipos_evaluaciones');

        return view('tipos_evaluaciones.index');
    }

    public function index_ajax()
    {
        $this->authorize('ver_tipos_evaluaciones');

        try {
            $tipos_evaluaciones = TipoEvaluacion::orderBy('id', 'asc')->get();
            return response()->json([
                'tipos_evaluaciones' => $tipos_evaluaciones,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_evaluaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_tipos_evaluaciones');

        try {
            $tipo_evaluacion = TipoEvaluacion::findOrFail($id);
            return response()->json([
                'tipo_evaluacion' => $tipo_evaluacion,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_evaluaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_tipos_evaluaciones');

        DB::beginTransaction();

            $request->validate([
                'nombre' => ['required', Rule::unique('tipos_evaluaciones')],
            ]);

            try {
                $tipo_evaluacion = new TipoEvaluacion();
                $tipo_evaluacion->nombre = removeAccents(Str::upper($request->nombre));
                $tipo_evaluacion->save();

                DB::commit();

                $tipos_evaluaciones = TipoEvaluacion::get();
                return response()->json([
                    'message' => 'El tipo de evaluación ' . $tipo_evaluacion->nombre . ' fue creado exitosamente.',
                    'tipos_evaluaciones' => $tipos_evaluaciones,
                    'selected' => $tipo_evaluacion,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('tipos_evaluaciones.index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_tipos_evaluaciones');

        try {
            $tipo_evaluacion = TipoEvaluacion::findOrFail($id);
            return response()->json([
                'tipo_evaluacion' => $tipo_evaluacion,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_evaluaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_tipos_evaluaciones');

        DB::beginTransaction();

        $request->validate([
            'nombre' => ['required', Rule::unique('tipos_evaluaciones')->ignore($id)],
        ]);

        try {
            $tipo_evaluacion = TipoEvaluacion::findOrFail($id);
            $tipo_evaluacion->nombre = removeAccents(Str::upper($request->nombre));
            $tipo_evaluacion->save();

            DB::commit();

            $tipos_evaluaciones = TipoEvaluacion::get();
            return response()->json([
                'message' => 'El tipo de evaluación ' . $tipo_evaluacion->nombre . ' fue actualizado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tipos_evaluaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_unactivate($id)
    {
        $this->authorize('inactivar_tipos_evaluaciones');

        try {
            $tipo_evaluacion = TipoEvaluacion::findOrFail($id);
            return response()->json([
                'tipo_evaluacion' => $tipo_evaluacion,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_evaluaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_tipos_evaluaciones');

        DB::beginTransaction();

        try {
            $tipo_evaluacion = TipoEvaluacion::findOrFail($id);
            $tipo_evaluacion->estado = 'IN';
            $tipo_evaluacion->save();

            DB::commit();

            return response()->json([
                'message' => 'El tipo de evaluación ' . $tipo_evaluacion->nombre . ' fue inactivado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tipos_evaluaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_activate($id)
    {
        $this->authorize('activar_tipos_evaluaciones');

        try {
            $tipo_evaluacion = TipoEvaluacion::findOrFail($id);
            return response()->json([
                'tipo_evaluacion' => $tipo_evaluacion,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_evaluaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_tipos_evaluaciones');

        DB::beginTransaction();

        try {
            $tipo_evaluacion = TipoEvaluacion::findOrFail($id);
            $tipo_evaluacion->estado = 'AC';
            $tipo_evaluacion->save();

            DB::commit();

            return response()->json([
                'message' => 'El tipo de evaluación ' . $tipo_evaluacion->nombre . ' fue activado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tipos_evaluaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_tipos_evaluaciones');

        try {
            $tipo_evaluacion = TipoEvaluacion::findOrFail($id);
            return response()->json([
                'tipo_evaluacion' => $tipo_evaluacion,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_evaluaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_tipos_evaluaciones');

        DB::beginTransaction();

        try {
            $tipo_evaluacion = TipoEvaluacion::findOrFail($id);
            $tipo_evaluacion->delete();

            DB::commit();

            return response()->json([
                'message' => 'El tipo de evaluación ' . $tipo_evaluacion->nombre . ' fue eliminado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('tipos_evaluaciones.index')->with('error-message', 'El tipo de evaluación ' . $tipo_evaluacion->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('tipos_evaluaciones.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
