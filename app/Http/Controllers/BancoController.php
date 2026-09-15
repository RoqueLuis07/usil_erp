<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use App\Models\Banco;

class BancoController extends Controller
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
        $this->authorize('ver_bancos');

        return view('bancos.index');
    }

    public function index_ajax()
    {
        $this->authorize('ver_bancos');

        try {
            $bancos = Banco::orderBy('id', 'asc')->get();
            return response()->json([
                'bancos' => $bancos,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('bancos.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_bancos');

        try {
            $banco = Banco::findOrFail($id);
            return response()->json([
                'banco' => $banco,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('bancos.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_bancos');

        DB::beginTransaction();

            $request->validate([
                'nombre' => ['required', Rule::unique('bancos')],
            ]);

            try {
                $banco = new Banco();
                $banco->nombre = removeAccents(Str::upper($request->nombre));
                $banco->save();

                DB::commit();

                $bancos = Banco::get();
                return response()->json([
                    'message' => 'El banco ' . $banco->nombre . ' fue creado exitosamente.',
                    'bancos' => $bancos,
                    'selected' => $banco,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('bancos.index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_bancos');

        try {
            $banco = Banco::findOrFail($id);
            return response()->json([
                'banco' => $banco,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('bancos.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_bancos');

        DB::beginTransaction();

        $request->validate([
            'nombre' => ['required', Rule::unique('bancos')->ignore($id)],
        ]);

        try {
            $banco = Banco::findOrFail($id);
            $banco->nombre = removeAccents(Str::upper($request->nombre));
            $banco->save();

            DB::commit();

            $bancos = Banco::get();
            return response()->json([
                'message' => 'El banco ' . $banco->nombre . ' fue actualizado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('bancos.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_unactivate($id)
    {
        $this->authorize('inactivar_bancos');

        try {
            $banco = Banco::findOrFail($id);
            return response()->json([
                'banco' => $banco,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('bancos.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_bancos');

        DB::beginTransaction();

        try {
            $banco = Banco::findOrFail($id);
            $banco->estado = 'IN';
            $banco->save();

            DB::commit();

            return response()->json([
                'message' => 'El banco ' . $banco->nombre . ' fue inactivado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('bancos.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_activate($id)
    {
        $this->authorize('activar_bancos');

        try {
            $banco = Banco::findOrFail($id);
            return response()->json([
                'banco' => $banco,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('bancos.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_bancos');

        DB::beginTransaction();

        try {
            $banco = Banco::findOrFail($id);
            $banco->estado = 'AC';
            $banco->save();

            DB::commit();

            return response()->json([
                'message' => 'El banco ' . $banco->nombre . ' fue activado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('bancos.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_bancos');

        try {
            $banco = Banco::findOrFail($id);
            return response()->json([
                'banco' => $banco,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('bancos.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_bancos');

        DB::beginTransaction();

        try {
            $banco = Banco::findOrFail($id);
            $banco->delete();

            DB::commit();

            return response()->json([
                'message' => 'El banco ' . $banco->nombre . ' fue eliminado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('bancos.index')->with('error-message', 'El banco ' . $banco->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('bancos.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
