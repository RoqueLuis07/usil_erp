<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use App\Models\CuentaContable;
use App\Models\SaldoCuentaContable;

class CuentaContableController extends Controller
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
        $this->authorize('ver_cuentas_contables');

        try {
            $cuentas_contables = CuentaContable::orderBy('cuenta', 'asc')->get();

            return view('cuentas_contables/index')->with(compact('cuentas_contables'));
        } catch (\Exception $e) {
            return redirect()->route('cuentas_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_cuentas_contables');

        try {
            $cuenta_contable = CuentaContable::with('hijos', 'padre')->findOrFail($id);
            $padres = $cuenta_contable->obtenerPadres();
            return view('cuentas_contables/show')->with(compact('cuenta_contable', 'padres'));
        } catch (\Exception $e) {
            return redirect()->route('cuentas_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_cuentas_contables');

        try {
            $cuentas_contables = CuentaContable::where('estado', 'AC')->get();
            return view('cuentas_contables/create')->with(compact('cuentas_contables'));
        } catch (\Exception $e) {
            return redirect()->route('cuentas_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_cuentas_contables');

        $request->validate([
            'cuenta' => ['required', Rule::unique('cuentas_contables')],
            'nombre' => ['required', Rule::unique('cuentas_contables')],
            'padre' => ['nullable', 'numeric'],
            'imputable' => 'required'
        ]);

        DB::beginTransaction();

        try {
            $cuenta_contable = new CuentaContable();
            $cuenta_contable->cuenta = $request->cuenta;
            $cuenta_contable->nombre = removeAccents(Str::upper($request->nombre));
            $cuenta_contable->tipo = substr($cuenta_contable->cuenta, 0, 1);
            $cuenta_contable->padre_id = $request->padre;

            if ($cuenta_contable->padre_id) {
                $padre = CuentaContable::findOrFail($cuenta_contable->padre_id);
                $cuenta_contable->nivel = $padre->nivel + 1;
            } else {
                $cuenta_contable->nivel = 1;
            }
            $cuenta_contable->imputable = $request->imputable;
            $cuenta_contable->cargado_por_id = Auth::id();
            $cuenta_contable->save();

            $saldo_cuenta = new SaldoCuentaContable();
            $saldo_cuenta->cuenta_contable_id = $cuenta_contable->id;
            $saldo_cuenta->saldo = 0;
            $saldo_cuenta->save();

            DB::commit();

            return redirect()->route('cuentas_contables.index')->with('success-message', 'La cuenta contable ' . $cuenta_contable->nombre . ' fue creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('cuentas_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_cuentas_contables');

        try {
            $cuenta_contable = CuentaContable::findOrFail($id);
            $cuentas_contables = CuentaContable::where('estado', 'AC')->where('id', '!=', $id)->get();
            return view('cuentas_contables/edit')->with(compact('cuenta_contable', 'cuentas_contables'));
        } catch (\Exception $e) {
            return redirect()->route('cuentas_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_cuentas_contables');

        $request->validate([
            'cuenta' => ['required', Rule::unique('cuentas_contables')->ignore($id)],
            'nombre' => ['required', Rule::unique('cuentas_contables')->ignore($id)],
            'padre' => ['nullable', 'numeric'],
            'imputable' => 'required'
        ]);

        DB::beginTransaction();

        try {
           $cuenta_contable = CuentaContable::findOrFail($id);
           $cuenta_contable->cuenta = $request->cuenta;
           $cuenta_contable->nombre = removeAccents(Str::upper($request->nombre));
           if ($cuenta_contable->padre_id) {
                if ($request->padre != $cuenta_contable->padre_id) {
                    $cuenta_contable->padre_id = $request->padre;
                }
                $padre = CuentaContable::findOrFail($cuenta_contable->padre_id);
                $cuenta_contable->nivel = $padre->nivel + 1;
            }

            $cuenta_cambiada = false;
            if (substr($cuenta_contable->cuenta, 0, 1) != $cuenta_contable->tipo) {
                $cuenta_contable->tipo = substr($cuenta_contable->cuenta, 0, 1);
                $cuenta_cambiada = true;
            }

            $cuenta_contable->imputable = $request->imputable;
            $cuenta_contable->actualizado_por_id = Auth::id();
            $cuenta_contable->save();

            if ($cuenta_contable->hijos->count() > 0 && $cuenta_cambiada == true) {
                $cuenta_contable->actualizarHijosRecursivos();
            }


            DB::commit();

            return redirect()->route('cuentas_contables.index')->with('success-message', 'La cuenta contable ' . $cuenta_contable->nombre . ' fue actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('cuentas_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_cuentas_contables');

        DB::beginTransaction();

        try {
            $cuenta_contable = CuentaContable::findOrFail($id);
            $cuenta_contable->actualizado_por_id = Auth::id();
            $cuenta_contable->estado = 'IN';
            $cuenta_contable->save();

            DB::commit();

            return redirect()->route('cuentas_contables.index')->with('error-message','La cuenta contable ' . $cuenta_contable->nombre . ' fue inactivada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('cuentas_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_cuentas_contables');

        DB::beginTransaction();

        try {
            $alumno = CuentaContable::findOrFail($id);
            $alumno->actualizado_por_id = Auth::id();
            $alumno->estado = 'AC';
            $alumno->save();

            DB::commit();

            return redirect()->route('clientes.index')->with('success-message','La cuenta contable ' . $cliente->nombre . ' fue activada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('clientes.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_cuentas_contables');

        DB::beginTransaction();

        try {
            $cuenta_contable = CuentaContable::findOrFail($id);
            $cuenta_contable->delete();

            DB::commit();

            return redirect()->route('cuentas_contables.index')->with('success-message','La cuenta contable ' . $cuenta_contable->nombre . ' fue eliminada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('cuentas_contables.index')->with('error-message', 'La cuenta contable ' . $cuenta_contable->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('cuentas_contables.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
