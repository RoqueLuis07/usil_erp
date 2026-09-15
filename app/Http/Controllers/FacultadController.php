<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use App\Models\Facultad;


class FacultadController extends Controller
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
        $this->authorize('ver_facultades');

        return view('facultades.index');
    }

    public function index_ajax()
    {
        $this->authorize('ver_facultades');

        try {
            $facultades = Facultad::orderBy('nombre', 'asc')->get();
            return response()->json([
                'facultades' => $facultades,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('facultades.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_facultades');

        try {
            $facultad = Facultad::findOrFail($id);
            return response()->json([
                'facultad' => $facultad,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('facultades.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_facultades');

        DB::beginTransaction();

            $request->validate([
                'nombre' => ['required', Rule::unique('facultades')],
            ]);

            try {
                $facultad = new Facultad();
                $facultad->nombre = removeAccents(Str::upper($request->nombre));
                $facultad->cargado_por_id = Auth::id();
                $facultad->save();

                DB::commit();

                $facultades = Facultad::get();
                return response()->json([
                    'message' => 'La facultad ' . $facultad->nombre . ' fue creada exitosamente.',
                    'facultades' => $facultades,
                    'selected' => $facultad,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('facultades.index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_facultades');

        try {
            $facultad = Facultad::findOrFail($id);
            return response()->json([
                'facultad' => $facultad,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('facultades.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_facultades');

        DB::beginTransaction();

        $request->validate([
            'nombre' => ['required', Rule::unique('facultades')->ignore($id)],
        ]);

        try {
            $facultad = Facultad::findOrFail($id);
            $facultad->nombre = removeAccents(Str::upper($request->nombre));
            $facultad->actualizado_por_id = Auth::id();
            $facultad->save();

            DB::commit();

            $facultades = Facultad::get();
            return response()->json([
                'message' => 'La facultad ' . $facultad->nombre . ' fue actualizada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('facultades.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_facultades');

        try {
            $facultad = Facultad::findOrFail($id);
            return response()->json([
                'facultad' => $facultad,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('facultades.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_facultades');

        DB::beginTransaction();

        try {
            $facultad = Facultad::findOrFail($id);
            $facultad->delete();

            DB::commit();

            return response()->json([
                'message' => 'La facultad ' . $facultad->nombre . ' fue eliminada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('facultades.index')->with('error-message', 'La facultad ' . $facultad->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('facultades.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
