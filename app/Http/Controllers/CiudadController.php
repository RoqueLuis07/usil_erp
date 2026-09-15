<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

use App\Models\Ciudad;
use App\Models\DepartamentoParaguay;


class CiudadController extends Controller
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
        $this->authorize('ver_ciudades');

        $departamentos = DepartamentoParaguay::orderBy('nombre', 'asc')->get();

        return view('ciudades.index')->with(compact('departamentos'));
    }

    public function index_ajax()
    {
        $this->authorize('ver_ciudades');

        try {
            $ciudades = Ciudad::with('departamento')->orderBy('nombre', 'asc')->get();
            return response()->json([
                'ciudades' => $ciudades,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('ciudades.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_ciudades');

        try {
            $ciudad = Ciudad::with('departamento')->findOrFail($id);
            return response()->json([
                'ciudad' => $ciudad,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('ciudades.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_ciudades');

        DB::beginTransaction();

            $request->validate([
                'nombre' => ['required', Rule::unique('ciudades')],
                'departamento' => ['required', 'numeric']
            ]);

            try {
                $ciudad = new Ciudad();
                $ciudad->nombre = removeAccents(Str::upper($request->nombre));
                $ciudad->departamento_id = $request->departamento;
                $ciudad->save();

                DB::commit();

                $ciudades = Ciudad::with('departamento')->get();
                return response()->json([
                    'message' => 'La ciudad ' . $ciudad->nombre . ' fue creada exitosamente.',
                    'ciudades' => $ciudades,
                    'selected' => $ciudad,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('ciudades.index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_ciudades');

        try {
            $ciudad = Ciudad::with('departamento')->findOrFail($id);
            $departamentos = DepartamentoParaguay::orderBy('nombre', 'asc')->get();
            return response()->json([
                'ciudad' => $ciudad,
                'departamentos' => $departamentos,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('ciudades.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_ciudades');

        DB::beginTransaction();

        $request->validate([
            'nombre' => ['required', Rule::unique('ciudades')->ignore($id)],
            'departamento' => ['required', 'numeric']
        ]);

        try {
            $ciudad = Ciudad::with('departamento')->findOrFail($id);
            $ciudad->nombre = removeAccents(Str::upper($request->nombre));
            $ciudad->departamento_id = $request->departamento;
            $ciudad->save();

            DB::commit();

            $ciudades = Ciudad::with('departamento')->get();
            return response()->json([
                'message' => 'La ciudad ' . $ciudad->nombre . ' fue actualizada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('ciudades.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_ciudades');

        try {
            $ciudad = Ciudad::findOrFail($id);
            return response()->json([
                'ciudad' => $ciudad,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('ciudades.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_ciudades');

        DB::beginTransaction();

        try {
            $ciudad = Ciudad::findOrFail($id);
            $ciudad->delete();

            DB::commit();

            return response()->json([
                'message' => 'La ciudad ' . $ciudad->nombre . ' fue eliminada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('ciudades.index')->with('error-message', 'La ciudad ' . $ciudad->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('ciudades.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
