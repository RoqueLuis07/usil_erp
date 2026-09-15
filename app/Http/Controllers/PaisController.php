<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

use App\Models\Pais;


class PaisController extends Controller
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
        $this->authorize('ver_paises');

        return view('paises.index');
    }

    public function index_ajax()
    {
        $this->authorize('ver_paises');

        try {
            $paises = Pais::orderBy('nombre', 'asc')->get();
            return response()->json([
                'paises' => $paises,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('paises.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_paises');

        try {
            $pais = Pais::findOrFail($id);
            return response()->json([
                'pais' => $pais,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('paises.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_paises');

        DB::beginTransaction();

            $request->validate([
                'nombre' => ['required', Rule::unique('paises')],
            ]);

            try {
                $pais = new Pais();
                $pais->nombre = removeAccents(Str::upper($request->nombre));
                $pais->save();

                DB::commit();

                $paises = Pais::get();
                return response()->json([
                    'message' => 'El país ' . $pais->nombre . ' fue creado exitosamente.',
                    'paises' => $paises,
                    'selected' => $pais,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('paises.index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_paises');

        try {
            $pais = Pais::findOrFail($id);
            return response()->json([
                'pais' => $pais,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('paises.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_paises');

        DB::beginTransaction();

        $request->validate([
            'nombre' => ['required', Rule::unique('paises')->ignore($id)],
        ]);

        try {
            $pais = Pais::findOrFail($id);
            $pais->nombre = removeAccents(Str::upper($request->nombre));
            $pais->save();

            DB::commit();

            $paises = Pais::get();
            return response()->json([
                'message' => 'El país ' . $pais->nombre . ' fue actualizado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('paises.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_paises');

        try {
            $pais = Pais::findOrFail($id);
            return response()->json([
                'pais' => $pais,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('paises.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_paises');

        DB::beginTransaction();

        try {
            $pais = Pais::findOrFail($id);
            $pais->delete();

            DB::commit();

            return response()->json([
                'message' => 'El país ' . $pais->nombre . ' fue eliminado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('paises.index')->with('error-message', 'El país no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('paises.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
