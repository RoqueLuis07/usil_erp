<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

use App\Models\Barrio;
use App\Models\Ciudad;


class BarrioController extends Controller
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
        $this->authorize('ver_barrios');

        $ciudades = Ciudad::orderBy('nombre', 'asc')->get();

        return view('barrios.index')->with(compact('ciudades'));
    }

    public function index_ajax()
    {
        $this->authorize('ver_barrios');

        try {
            $barrios = Barrio::with('ciudad')->orderBy('nombre', 'asc')->get();
            return response()->json([
                'barrios' => $barrios,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('barrios.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('crear_barrios');

        try {
            $barrio = Barrio::with('ciudad')->findOrFail($id);
            return response()->json([
                'barrio' => $barrio,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('barrios.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_barrios');

        DB::beginTransaction();

            $request->validate([
                'nombre' => ['required', Rule::unique('barrios')],
                'ciudad' => ['required', 'numeric']
            ]);

            try {
                $barrio = new Barrio();
                $barrio->nombre = removeAccents(Str::upper($request->nombre));
                $barrio->ciudad_id = $request->ciudad;
                $barrio->save();

                DB::commit();

                $barrios = Barrio::with('ciudad')->get();
                return response()->json([
                    'message' => 'El barrio ' . $barrio->nombre . ' fue creado exitosamente.',
                    'barrios' => $barrios,
                    'selected' => $barrio,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('barrios.index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_barrios');

        try {
            $barrio = Barrio::with('ciudad')->findOrFail($id);
            $ciudades = Ciudad::orderBy('nombre', 'asc')->get();
            return response()->json([
                'barrio' => $barrio,
                'ciudades' => $ciudades,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('barrios.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_barrios');

        DB::beginTransaction();

        $request->validate([
            'nombre' => ['required', Rule::unique('barrios')->ignore($id)],
            'ciudad' => ['required', 'numeric']
        ]);

        try {
            $barrio = Barrio::with('ciudad')->findOrFail($id);
            $barrio->nombre = removeAccents(Str::upper($request->nombre));
            $barrio->ciudad_id = $request->ciudad;
            $barrio->save();

            DB::commit();

            $barrios = Barrio::with('ciudad')->get();
            return response()->json([
                'message' => 'El barrio ' . $barrio->nombre . ' fue actualizado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('barrios.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_barrios');

        try {
            $barrio = Barrio::findOrFail($id);
            return response()->json([
                'barrio' => $barrio,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('barrios.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_barrios');

        DB::beginTransaction();

        try {
            $barrio = Barrio::findOrFail($id);
            $barrio->delete();

            DB::commit();

            return response()->json([
                'message' => 'El barrio ' . $barrio->nombre . ' fue eliminado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('barrios.index')->with('error-message', 'El barrio ' . $barrio->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('barrios.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
