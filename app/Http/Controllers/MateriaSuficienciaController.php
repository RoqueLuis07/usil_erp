<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use App\Models\MateriaSuficiencia;
use App\Models\Materia;


class MateriaSuficienciaController extends Controller
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
        $this->authorize('ver_materias_suficiencias');

        $materias = Materia::where('estado', 'AC')->orderBy('nombre_real', 'asc')->get();

        return view('materias/suficiencias/index')->with(compact('materias'));
    }

    public function index_ajax()
    {
        $this->authorize('ver_materias_suficiencias');

        try {
            $materias = MateriaSuficiencia::with('materia')->orderBy('id', 'asc')->get();
            return response()->json([
                'materias' => $materias,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('materias_suficiencias.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_materias_suficiencias');

        try {
            $materia = MateriaSuficiencia::with('materia')->findOrFail($id);
            return response()->json([
                'materia' => $materia,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('materias_suficiencias.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_materias_suficiencias');

        DB::beginTransaction();

            $request->validate([
                'materia' => ['required', Rule::unique('materias_suficiencias', 'materia_id')],
            ]);

            try {
                $materia = new MateriaSuficiencia();
                $materia->materia_id = $request->materia;
                $materia->save();

                DB::commit();

                $materias = MateriaSuficiencia::get();
                return response()->json([
                    'message' => 'La materia de suficiencia ' . $materia->materia->nombre_real . ' fue agregada exitosamente.',
                    'materias' => $materias,
                    'selected' => $materia,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('materias_suficiencias.index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_materias_suficiencias');

        try {
            $materia = MateriaSuficiencia::with('materia')->findOrFail($id);
            $materias = Materia::where('estado', 'AC')->orderBy('nombre_real', 'asc')->get();
            return response()->json([
                'materia' => $materia,
                'materias' => $materias,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('materias_suficiencias.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_materias_suficiencias');

        DB::beginTransaction();

        $request->validate([
            'materia' => ['required', Rule::unique('materias_suficiencias', 'materia_id')->ignore($id)],
        ]);

        try {
            $materia = MateriaSuficiencia::with('materia')->findOrFail($id);
            $materia->materia_id = $request->materia;
            $materia->save();

            DB::commit();

            $materias = MateriaSuficiencia::get();
            return response()->json([
                'message' => 'La materia de suficiencia ' . $materia->materia->nombre_real . ' fue actualizada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('materias_suficiencias.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_unactivate($id)
    {
        $this->authorize('inactivar_materias_suficiencias');

        try {
            $materia = MateriaSuficiencia::with('materia')->findOrFail($id);
            return response()->json([
                'materia' => $materia,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('materias_suficiencias.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_materias_suficiencias');

        DB::beginTransaction();

        try {
            $materia = MateriaSuficiencia::with('materia')->findOrFail($id);
            $materia->estado = 'IN';
            $materia->save();

            DB::commit();

            return response()->json([
                'message' => 'La materia de suficiencia ' . $materia->materia->nombre_rela . ' fue inactivada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('materias_suficiencias.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_activate($id)
    {
        $this->authorize('activar_materias_suficiencias');

        try {
            $materia = MateriaSuficiencia::with('materia')->findOrFail($id);
            return response()->json([
                'materia' => $materia,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('materias_suficiencias.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_materias_suficiencias');

        DB::beginTransaction();

        try {
            $materia = MateriaSuficiencia::with('materia')->findOrFail($id);
            $materia->estado = 'AC';
            $materia->save();

            DB::commit();

            return response()->json([
                'message' => 'La materia de suficiencia ' . $materia->materia->nombre_real . ' fue activada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('materias_suficiencias.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_materias_suficiencias');

        try {
            $materia = MateriaSuficiencia::with('materia')->findOrFail($id);
            return response()->json([
                'materia' => $materia,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('materias_suficiencias.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_materias_suficiencias');

        DB::beginTransaction();

        try {
            $materia = MateriaSuficiencia::with('materia')->findOrFail($id);
            $materia->delete();

            DB::commit();

            return response()->json([
                'message' => 'La materia de suficiencia ' . $materia->materia->nombre_real . ' fue eliminada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('materias_suficiencias.index')->with('error-message', 'La materia de suficiencia ' . $materia->materia->nombre_real . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('materias_suficiencias.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
