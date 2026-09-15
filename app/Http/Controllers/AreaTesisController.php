<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use App\Models\AreaTesis;


class AreaTesisController extends Controller
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
        $this->authorize('ver_areas_tesis');

        return view('tesis.areas.index');
    }

    public function index_ajax()
    {
        $this->authorize('ver_areas_tesis');

        try {
            $areas_tesis = AreaTesis::orderBy('id', 'asc')->get();
            return response()->json([
                'areas_tesis' => $areas_tesis,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('areas_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_areas_tesis');

        try {
            $area_tesis = AreaTesis::findOrFail($id);
            return response()->json([
                'area_tesis' => $area_tesis,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('areas_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_areas_tesis');

        DB::beginTransaction();

            $request->validate([
                'nombre' => ['required', Rule::unique('areas_tesis')]
            ]);

            try {
                $area_tesis = new AreaTesis();
                $area_tesis->nombre = removeAccents(Str::upper($request->nombre));
                $area_tesis->cargado_por_id = Auth::id();
                $area_tesis->save();

                DB::commit();

                $areas_tesis = AreaTesis::get();
                return response()->json([
                    'message' => 'El área de trabajo final de grado ' . $area_tesis->nombre . ' fue creado exitosamente.',
                    'areas_tesis' => $areas_tesis,
                    'selected' => $area_tesis,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('areas_tesis.index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_areas_tesis');

        try {
            $area_tesis = AreaTesis::findOrFail($id);
            return response()->json([
                'area_tesis' => $area_tesis,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('areas_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_areas_tesis');

        DB::beginTransaction();

        $request->validate([
            'nombre' => ['required', Rule::unique('areas_tesis')->ignore($id)]
        ]);

        try {
            $area_tesis = AreaTesis::findOrFail($id);
            $area_tesis->nombre = removeAccents(Str::upper($request->nombre));
            $area_tesis->actualizado_por_id = Auth::id();
            $area_tesis->save();

            DB::commit();

            $areas_tesis = AreaTesis::get();
            return response()->json([
                'message' => 'El área de trabajo final de grado ' . $area_tesis->nombre . ' fue actualizado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('areas_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_unactivate($id)
    {
        $this->authorize('inactivar_areas_tesis');

        try {
            $area_tesis = AreaTesis::findOrFail($id);
            return response()->json([
                'area_tesis' => $area_tesis,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('areas_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_areas_tesis');

        DB::beginTransaction();

        try {
            $area_tesis = AreaTesis::findOrFail($id);
            $area_tesis->actualizado_por_id = Auth::id();
            $area_tesis->estado = 'IN';
            $area_tesis->save();

            DB::commit();

            return response()->json([
                'message' => 'El área de trabajo final de grado ' . $area_tesis->nombre . ' fue inactivado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('areas_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_activate($id)
    {
        $this->authorize('activar_areas_tesis');

        try {
            $area_tesis = AreaTesis::findOrFail($id);
            return response()->json([
                'area_tesis' => $area_tesis,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('areas_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_areas_tesis');

        DB::beginTransaction();

        try {
            $area_tesis = AreaTesis::findOrFail($id);
            $area_tesis->actualizado_por_id = Auth::id();
            $area_tesis->estado = 'AC';
            $area_tesis->save();

            DB::commit();

            return response()->json([
                'message' => 'El área de trabajo final de grado ' . $area_tesis->nombre . ' fue activado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('areas_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_areas_tesis');

        try {
            $area_tesis = AreaTesis::findOrFail($id);
            return response()->json([
                'area_tesis' => $area_tesis,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('areas_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_areas_tesis');

        DB::beginTransaction();

        try {
            $area_tesis = AreaTesis::findOrFail($id);
            $area_tesis->delete();

            DB::commit();

            return response()->json([
                'message' => 'El área de trabajo final de grado ' . $area_tesis->nombre . ' fue eliminado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('areas_tesis.index')->with('error-message', 'El área de trabajo final de grado ' . $area_tesis->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('areas_tesis.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
