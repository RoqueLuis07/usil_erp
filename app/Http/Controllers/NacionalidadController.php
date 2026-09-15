<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

use App\Models\Nacionalidad;


class NacionalidadController extends Controller
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
        $this->authorize('ver_nacionalidades');

        return view('nacionalidades.index');
    }

    public function index_ajax()
    {
        $this->authorize('ver_nacionalidades');

        try {
            $nacionalidades = Nacionalidad::orderBy('nombre')->get();
            return response()->json([
                'nacionalidades' => $nacionalidades,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('nacionalidades.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_nacionalidades');

        try {
            $nacionalidad = Nacionalidad::findOrFail($id);
            return response()->json([
                'nacionalidad' => $nacionalidad,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('nacionalidades.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_nacionalidades');

        DB::beginTransaction();

            $request->validate([
                'nombre' => ['required', Rule::unique('nacionalidades')],
            ]);

            try {
                $nacionalidad = new Nacionalidad();
                $nacionalidad->nombre = removeAccents(Str::upper($request->nombre));
                $nacionalidad->save();

                DB::commit();

                $nacionalidades = Nacionalidad::get();
                return response()->json([
                    'message' => 'La nacionalidad ' . $nacionalidad->nombre . ' fue creada exitosamente.',
                    'nacionalidades' => $nacionalidades,
                    'selected' => $nacionalidad,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('nacionalidades.index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_nacionalidades');

        try {
            $nacionalidad = Nacionalidad::findOrFail($id);
            return response()->json([
                'nacionalidad' => $nacionalidad,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('nacionalidades.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_nacionalidades');

        DB::beginTransaction();

        $request->validate([
            'nombre' => ['required', Rule::unique('nacionalidades')->ignore($id)],
        ]);

        try {
            $nacionalidad = Nacionalidad::findOrFail($id);
            $nacionalidad->nombre = removeAccents(Str::upper($request->nombre));
            $nacionalidad->save();

            DB::commit();

            $nacionalidades = Nacionalidad::get();
            return response()->json([
                'message' => 'La nacionalidad ' . $nacionalidad->nombre . ' fue actualizada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('nacionalidades.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_nacionalidades');

        try {
            $nacionalidad = Nacionalidad::findOrFail($id);
            return response()->json([
                'nacionalidad' => $nacionalidad,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('nacionalidades.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_nacionalidades');

        DB::beginTransaction();

        try {
            $nacionalidad = Nacionalidad::findOrFail($id);
            $nacionalidad->delete();

            DB::commit();

            return response()->json([
                'message' => 'La nacionalidad ' . $nacionalidad->nombre . ' fue eliminada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('nacionalidades.index')->with('error-message', 'La nacionalidad ' . $nacionalidad->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('nacionalidades.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
