<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use App\Models\BloqueBorradorTesisUbs;

class BloqueBorradorTesisUbsController extends Controller
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
        $this->authorize('ver_bloques_borradores_tesis_ubs');

        return view('ubs.tesis.bloques_borradores.index');
    }

    public function index_ajax()
    {
        $this->authorize('ver_bloques_borradores_tesis_ubs');

        try {
            $bloques = BloqueBorradorTesisUbs::orderBy('id', 'asc')->get();
            return response()->json([
                'bloques' => $bloques,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tesis_parametros_ubs.bloques_borradores_index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_bloques_borradores_tesis_ubs');

        try {
            $bloque = BloqueBorradorTesisUbs::findOrFail($id);
            return response()->json([
                'bloque' => $bloque,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tesis_parametros_ubs.bloques_borradores_index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_bloques_borradores_tesis_ubs');

        DB::beginTransaction();

            $request->validate([
                'numero' => ['required', 'numeric', 'min:1', 'max:10', Rule::unique('bloques_borradores_tesis_ubs')],
                'nombre' => ['required', Rule::unique('bloques_borradores_tesis_ubs')]
            ]);

            try {
                $bloque = new BloqueBorradorTesisUbs();
                $bloque->numero = $request->numero;
                $bloque->nombre = removeAccents(Str::upper($request->nombre));
                $bloque->cargado_por_id = Auth::id();
                $bloque->save();

                DB::commit();

                $bloques = BloqueBorradorTesisUbs::get();
                return response()->json([
                    'message' => 'El bloque de anteproyecto ' . $bloque->nombre . ' fue creado exitosamente.',
                    'bloques' => $bloques,
                    'selected' => $bloque,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('tesis_parametros_ubs.bloques_borradores_index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_bloques_borradores_tesis_ubs');

        try {
            $bloque = BloqueBorradorTesisUbs::findOrFail($id);
            return response()->json([
                'bloque' => $bloque,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tesis_parametros_ubs.bloques_borradores_index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_bloques_borradores_tesis_ubs');

        DB::beginTransaction();

        $request->validate([
            'numero' => ['required', 'numeric', 'min:1', 'max:10', Rule::unique('bloques_borradores_tesis_ubs')->ignore($id)],
            'nombre' => ['required', Rule::unique('bloques_borradores_tesis_ubs')->ignore($id)]
        ]);

        try {
            $bloque = BloqueBorradorTesisUbs::findOrFail($id);
            $bloque->numero = $request->numero;
            $bloque->nombre = removeAccents(Str::upper($request->nombre));
            $bloque->actualizado_por_id = Auth::id();
            $bloque->save();

            DB::commit();

            $bloques = BloqueBorradorTesisUbs::get();
            return response()->json([
                'message' => 'El bloque de anteproyecto ' . $bloque->nombre . ' fue actualizado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tesis_parametros_ubs.bloques_borradores_index')->with('error-message', $e->getMessage());
        }
    }

    public function get_unactivate($id)
    {
        $this->authorize('inactivar_bloques_borradores_tesis_ubs');

        try {
            $bloque = BloqueBorradorTesisUbs::findOrFail($id);
            return response()->json([
                'bloque' => $bloque,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tesis_parametros_ubs.bloques_borradores_index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_bloques_borradores_tesis_ubs');

        DB::beginTransaction();

        try {
            $bloque = BloqueBorradorTesisUbs::findOrFail($id);
            $bloque->actualizado_por_id = Auth::id();
            $bloque->estado = 'IN';
            $bloque->save();

            DB::commit();

            return response()->json([
                'message' => 'El bloque de anteproyecto ' . $bloque->nombre . ' fue inactivado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tesis_parametros_ubs.bloques_borradores_index')->with('error-message', $e->getMessage());
        }
    }

    public function get_activate($id)
    {
        $this->authorize('activar_bloques_borradores_tesis_ubs');

        try {
            $bloque = BloqueBorradorTesisUbs::findOrFail($id);
            return response()->json([
                'bloque' => $bloque,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tesis_parametros_ubs.bloques_borradores_index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_bloques_borradores_tesis_ubs');

        DB::beginTransaction();

        try {
            $bloque = BloqueBorradorTesisUbs::findOrFail($id);
            $bloque->actualizado_por_id = Auth::id();
            $bloque->estado = 'AC';
            $bloque->save();

            DB::commit();

            return response()->json([
                'message' => 'El bloque de anteproyecto ' . $bloque->nombre . ' fue activado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tesis_parametros_ubs.bloques_borradores_index')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_bloques_borradores_tesis_ubs');

        try {
            $bloque = BloqueBorradorTesisUbs::findOrFail($id);
            return response()->json([
                'bloque' => $bloque,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tesis_parametros_ubs.bloques_borradores_index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_bloques_borradores_tesis_ubs');

        DB::beginTransaction();

        try {
            $bloque = BloqueBorradorTesisUbs::findOrFail($id);
            $bloque->delete();

            DB::commit();

            return response()->json([
                'message' => 'El bloque de anteproyecto ' . $bloque->nombre . ' fue eliminado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('tesis_parametros_ubs.bloques_borradores_index')->with('error-message', 'El bloque de anteproyecto ' . $bloque->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('tesis_parametros_ubs.bloques_borradores_index')->with('error-message', $e->getMessage());
            }
        }
    }
}
