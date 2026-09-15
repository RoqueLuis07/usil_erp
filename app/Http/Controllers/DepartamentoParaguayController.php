<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

use App\Models\DepartamentoParaguay;


class DepartamentoParaguayController extends Controller
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
        $this->authorize('ver_departamentos_paraguay');

        return view('departamentos_paraguay.index');
    }

    public function index_ajax()
    {
        $this->authorize('ver_departamentos_paraguay');

        try {
            $departamentos_paraguay = DepartamentoParaguay::orderBy('nombre', 'asc')->get();
            return response()->json([
                'departamentos_paraguay' => $departamentos_paraguay,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('departamentos_paraguay.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_departamentos_paraguay');

        try {
            $departamento_paraguay = DepartamentoParaguay::findOrFail($id);
            return response()->json([
                'departamento_paraguay' => $departamento_paraguay,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('departamentos_paraguay.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_departamentos_paraguay');

        DB::beginTransaction();

            $request->validate([
                'nombre' => ['required', Rule::unique('departamentos_paraguay')],
                'capital' => 'required'
            ]);

            try {
                $departamento_paraguay = new DepartamentoParaguay();
                $departamento_paraguay->nombre = removeAccents(Str::upper($request->nombre));
                $departamento_paraguay->capital = removeAccents(Str::upper($request->capital));
                $departamento_paraguay->save();

                DB::commit();

                $departamentos_paraguay = DepartamentoParaguay::get();
                return response()->json([
                    'message' => 'El departamento ' . $departamento_paraguay->nombre . ' fue creado exitosamente.',
                    'departamentos_paraguay' => $departamentos_paraguay,
                    'selected' => $departamento_paraguay,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('departamentos_paraguay.index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_departamentos_paraguay');

        try {
            $departamento_paraguay = DepartamentoParaguay::findOrFail($id);
            return response()->json([
                'departamento_paraguay' => $departamento_paraguay,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('departamentos_paraguay.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_departamentos_paraguay');

        DB::beginTransaction();

        $request->validate([
            'nombre' => ['required', Rule::unique('departamentos_paraguay')->ignore($id)],
            'capital' => 'required'
        ]);

        try {
            $departamento_paraguay = DepartamentoParaguay::findOrFail($id);
            $departamento_paraguay->nombre = removeAccents(Str::upper($request->nombre));
            $departamento_paraguay->capital = removeAccents(Str::upper($request->capital));
            $departamento_paraguay->save();

            DB::commit();

            $departamentos_paraguay = DepartamentoParaguay::get();
            return response()->json([
                'message' => 'El departamento ' . $departamento_paraguay->nombre . ' fue actualizado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('departamentos_paraguay.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_departamentos_paraguay');

        try {
            $departamento_paraguay = DepartamentoParaguay::findOrFail($id);
            return response()->json([
                'departamento_paraguay' => $departamento_paraguay,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('departamentos_paraguay.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_departamentos_paraguay');

        DB::beginTransaction();

        try {
            $departamento_paraguay = DepartamentoParaguay::findOrFail($id);
            $departamento_paraguay->delete();

            DB::commit();

            return response()->json([
                'message' => 'El departamento ' . $departamento_paraguay->nombre . ' fue eliminado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('departamentos_paraguay.index')->with('error-message', 'El departamento ' . $departamento_paraguay->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('departamentos_paraguay.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
