<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use App\Models\CuentaBancaria;
use App\Models\Banco;
use App\Models\Moneda;
use App\Models\CuentaContable;

class CuentaBancariaController extends Controller
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
        $this->authorize('ver_cuentas_bancarias');

        $bancos = Banco::orderBy('id', 'asc')->get();
        $monedas = Moneda::orderBy('id', 'asc')->get();
        $cuentas_contables = CuentaContable::where('imputable', true)->where('estado', 'AC')->get();

        return view('cuentas_bancarias.index')->with(compact('bancos', 'monedas', 'cuentas_contables'));
    }

    public function index_ajax()
    {
        $this->authorize('ver_cuentas_bancarias');

        try {
            $cuentas_bancarias = CuentaBancaria::with('banco', 'moneda')->orderBy('id', 'asc')->get();

            return response()->json([
                'cuentas_bancarias' => $cuentas_bancarias,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('cuentas_bancarias.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_cuentas_bancarias');

        try {
            $cuenta_bancaria = CuentaBancaria::with('banco', 'moneda', 'cuentaIngreso', 'cuentaEgreso')->findOrFail($id);
            return response()->json([
                'cuenta_bancaria' => $cuenta_bancaria,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('cuentas_bancarias.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_cuentas_bancarias');

        DB::beginTransaction();

        $request->validate([
            'banco' => ['required', 'numeric'],
            'numero_cuenta' => ['required', Rule::unique('cuentas_bancarias')->where(fn ($query) => $query->where('numero_cuenta', $request->numero_cuenta)->where('banco_id', $request->banco))],
            //El de arriba verifica que el conjunto de banco_id y numero_cuenta no existan
            'tipo_cuenta' => 'required',
            'moneda' => ['required', 'numeric'],
            'acredita_tarjeta' => 'required',
            'titular' => 'required',
            'documento_titular' => 'required',
            'cuenta_ingreso' => ['required', 'numeric'],
            'cuenta_egreso' => ['required', 'numeric'],
        ]);

        try {
            $cuenta_bancaria = new CuentaBancaria();
            $cuenta_bancaria->banco_id = $request->banco;
            $cuenta_bancaria->numero_cuenta = removeAccents(Str::upper($request->numero_cuenta));
            $cuenta_bancaria->tipo_cuenta = $request->tipo_cuenta;
            $cuenta_bancaria->moneda_id = $request->moneda;
            $cuenta_bancaria->acredita_tarjeta = $request->acredita_tarjeta;
            $cuenta_bancaria->titular = removeAccents(Str::upper($request->titular));
            $cuenta_bancaria->documento_titular = removeAccents(Str::upper($request->documento_titular));
            $cuenta_bancaria->cuenta_ingreso_id = $request->cuenta_ingreso;
            $cuenta_bancaria->cuenta_egreso_id = $request->cuenta_egreso;
            $cuenta_bancaria->cargado_por_id = Auth::id();
            $cuenta_bancaria->save();

            DB::commit();

            $cuentas_bancarias = CuentaBancaria::get();
            return response()->json([
                'message' => 'La cuenta bancaria fue creada exitosamente.',
                'cuentas_bancarias' => $cuentas_bancarias,
                'selected' => $cuenta_bancaria,
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('cuentas_bancarias.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_cuentas_bancarias');

        try {
            $cuenta_bancaria = CuentaBancaria::with('banco', 'moneda', 'cuentaIngreso', 'cuentaEgreso')->findOrFail($id);
            $bancos = Banco::orderBy('id', 'asc')->get();
            $monedas = Moneda::orderBy('id', 'asc')->get();
            $cuentas_contables = CuentaContable::where('imputable', true)->where('estado', 'AC')->get();

            return response()->json([
                'cuenta_bancaria' => $cuenta_bancaria,
                'bancos' => $bancos,
                'monedas' => $monedas,
                'cuentas_contables' => $cuentas_contables,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('cuentas_bancarias.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_cuentas_bancarias');

        DB::beginTransaction();

        $request->validate([
            'banco' => ['required', 'numeric'],
            'numero_cuenta' => ['required', Rule::unique('cuentas_bancarias')->where(fn ($query) => $query->where('numero_cuenta', $request->numero_cuenta)->where('banco_id', $request->banco))->ignore($id)],
            //El de arriba verifica que el conjunto de banco_id y numero_cuenta no existan
            'tipo_cuenta' => 'required',
            'moneda' => ['required', 'numeric'],
            'acredita_tarjeta' => 'required',
            'titular' => 'required',
            'documento_titular' => 'required',
            'cuenta_ingreso' => ['required', 'numeric'],
            'cuenta_egreso' => ['required', 'numeric'],
        ]);

        try {
            $cuenta_bancaria = CuentaBancaria::findOrFail($id);
            $cuenta_bancaria->banco_id = $request->banco;
            $cuenta_bancaria->numero_cuenta = removeAccents(Str::upper($request->numero_cuenta));
            $cuenta_bancaria->tipo_cuenta = $request->tipo_cuenta;
            $cuenta_bancaria->acredita_tarjeta = $request->acredita_tarjeta;
            $cuenta_bancaria->moneda_id = $request->moneda;
            $cuenta_bancaria->titular = removeAccents(Str::upper($request->titular));
            $cuenta_bancaria->documento_titular = removeAccents(Str::upper($request->documento_titular));
            $cuenta_bancaria->cuenta_ingreso_id = $request->cuenta_ingreso;
            $cuenta_bancaria->cuenta_egreso_id = $request->cuenta_egreso;
            $cuenta_bancaria->actualizado_por_id = Auth::id();
            $cuenta_bancaria->save();

            DB::commit();

            $cuentas_bancarias = CuentaBancaria::get();
            return response()->json([
                'message' => 'La cuenta bancaria fue actualizada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('cuentas_bancarias.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_unactivate($id)
    {
        $this->authorize('inactivar_cuentas_bancarias');

        try {
            $cuenta_bancaria = CuentaBancaria::findOrFail($id);
            return response()->json([
                'cuenta_bancaria' => $cuenta_bancaria,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('cuentas_bancarias.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_cuentas_bancarias');

        DB::beginTransaction();

        try {
            $cuenta_bancaria = CuentaBancaria::findOrFail($id);
            $cuenta_bancaria->actualizado_por_id = Auth::id();
            $cuenta_bancaria->estado = 'IN';
            $cuenta_bancaria->save();

            DB::commit();

            return response()->json([
                'message' => 'La cuenta bancaria fue inactivada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('cuentas_bancarias.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_activate($id)
    {
        $this->authorize('activar_cuentas_bancarias');

        try {
            $cuenta_bancaria = CuentaBancaria::findOrFail($id);
            return response()->json([
                'cuenta_bancaria' => $cuenta_bancaria,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('cuentas_bancarias.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_cuentas_bancarias');

        DB::beginTransaction();

        try {
            $cuenta_bancaria = CuentaBancaria::findOrFail($id);
            $cuenta_bancaria->actualizado_por_id = Auth::id();
            $cuenta_bancaria->estado = 'AC';
            $cuenta_bancaria->save();

            DB::commit();

            return response()->json([
                'message' => 'La cuenta bancaria fue activada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('cuentas_bancarias.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_cuentas_bancarias');

        try {
            $cuenta_bancaria = CuentaBancaria::findOrFail($id);
            return response()->json([
                'cuenta_bancaria' => $cuenta_bancaria,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('cuentas_bancarias.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_cuentas_bancarias');

        DB::beginTransaction();

        try {
            $cuenta_bancaria = CuentaBancaria::findOrFail($id);
            $cuenta_bancaria->delete();

            DB::commit();

            return response()->json([
                'message' => 'La cuenta bancaria fue eliminada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('cuentas_bancarias.index')->with('error-message', 'La cuenta bancaria no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('cuentas_bancarias.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
