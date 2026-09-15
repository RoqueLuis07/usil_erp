<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

use App\Models\AreaConocimiento;


class AreaConocimientoController extends Controller
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
        $this->authorize('ver_areas_conocimientos');

        return view('areas_conocimientos.index');
    }

    public function index_ajax()
    {
        $this->authorize('ver_areas_conocimientos');

        try {
            $areas = AreaConocimiento::orderBy('nombre', 'asc')->get();
            return response()->json([
                'areas' => $areas,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('areas_conocimientos.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_areas_conocimientos');

        try {
            $area = AreaConocimiento::findOrFail($id);
            return response()->json([
                'area' => $area,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('areas_conocimientos.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_areas_conocimientos');

        DB::beginTransaction();

            $request->validate([
                'nombre' => ['required', Rule::unique('areas_conocimientos')],
                'abreviatura' => ['required', Rule::unique('areas_conocimientos')],
            ]);

            try {
                $area = new AreaConocimiento();
                $area->nombre = removeAccents(Str::upper($request->nombre));
                $area->abreviatura = removeAccents(Str::upper($request->abreviatura));
                $area->save();

                DB::commit();

                $areas = AreaConocimiento::get();
                return response()->json([
                    'message' => 'El área de conocimiento ' . $area->nombre . ' fue creado exitosamente.',
                    'areas' => $areas,
                    'selected' => $area,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('areas_conocimientos.index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_areas_conocimientos');

        try {
            $area = AreaConocimiento::findOrFail($id);
            return response()->json([
                'area' => $area,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('areas_conocimientos.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_areas_conocimientos');

        DB::beginTransaction();

        $request->validate([
            'nombre' => ['required', Rule::unique('areas_conocimientos')->ignore($id)],
            'abreviatura' => ['required', Rule::unique('areas_conocimientos')->ignore($id)],
        ]);

        try {
            $area = AreaConocimiento::findOrFail($id);
            $area->nombre = removeAccents(Str::upper($request->nombre));
            $area->abreviatura = removeAccents(Str::upper($request->abreviatura));
            $area->save();

            DB::commit();

            $areas = AreaConocimiento::get();
            return response()->json([
                'message' => 'El área de conocimiento ' . $area->nombre . ' fue actualizado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('areas_conocimientos.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_areas_conocimientos');

        try {
            $area = AreaConocimiento::findOrFail($id);
            return response()->json([
                'area' => $area,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('areas_conocimientos.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_areas_conocimientos');

        DB::beginTransaction();

        try {
            $area = AreaConocimiento::findOrFail($id);
            $area->delete();

            DB::commit();

            return response()->json([
                'message' => 'El área de conocimiento ' . $area->nombre . ' fue eliminado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('areas_conocimientos.index')->with('error-message', 'El área de conocimiento no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('areas_conocimientos.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
