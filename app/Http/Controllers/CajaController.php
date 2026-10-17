<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use App\Models\Caja;
use App\Models\User;
use App\Models\UsuarioCaja;
use App\Models\CuentaContable;

class CajaController extends Controller
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
        $this->authorize('ver_cajas');

        $usuarios = User::whereDoesntHave('usuariosCajas')
                        ->whereDoesntHave('roles', function ($query) {
                            $query->whereIn('name', ['ALUMNO', 'DOCENTE', 'ENCARGADO_DOCENTE']);
                        })
                        ->get();
        $cuentas_contables = CuentaContable::where('imputable', true)->where('estado', 'AC')->get();

        return view('cajas.index')->with(compact('usuarios', 'cuentas_contables'));
    }

    public function index_ajax()
    {
        $this->authorize('ver_cajas');

        try {
            $cajas = Caja::with('usuario')->orderBy('id', 'asc')->get();

            return response()->json([
                'cajas' => $cajas,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('cajas.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_cajas');

        try {
            $caja = Caja::with('usuario', 'cuentaIngreso', 'cuentaEgreso')->findOrFail($id);
            return response()->json([
                'caja' => $caja,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('cajas.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_cajas');

        DB::beginTransaction();

            $request->validate([
                'nombre' => ['required', Rule::unique('cajas')],
                'usuario' => ['required', 'numeric'],
                'cuenta_ingreso' => ['required', 'numeric'],
                'cuenta_egreso' => ['required', 'numeric']
            ]);

            try {
                $caja = new Caja();
                $caja->nombre = removeAccents(Str::upper($request->nombre));
                $caja->cuenta_ingreso_id = $request->cuenta_ingreso;
                $caja->cuenta_egreso_id = $request->cuenta_egreso;
                $caja->cargado_por_id = Auth::id();
                $caja->save();

                if (UsuarioCaja::where('usuario_id', $request->usuario)->exists()) {
                    return response()->json([
                        'message' => 'El usuario seleccionado ya cuenta con una caja asignada.',
                    ]);
                } else {
                    $usuario_caja = new UsuarioCaja();
                    $usuario_caja->usuario_id = $request->usuario;
                    $usuario_caja->caja_id = $caja->id;
                    $usuario_caja->save();
                }

                DB::commit();

                $cajas = Caja::with('usuario')->get();
                return response()->json([
                    'message' => 'La caja ' . $caja->nombre . ' fue creada exitosamente.',
                    'cajas' => $cajas,
                    'selected' => $caja,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('cajas.index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_cajas');

        try {
            $caja = Caja::with('usuario', 'cuentaIngreso', 'cuentaEgreso')->findOrFail($id);
            $usuarios = User::whereDoesntHave('roles', function ($query) {
                            $query->whereIn('name', ['ALUMNO', 'DOCENTE', 'ENCARGADO_DOCENTE']);
                        })
                        ->get();
            $cuentas_contables = CuentaContable::where('imputable', true)->where('estado', 'AC')->get();

            return response()->json([
                'caja' => $caja,
                'usuarios' => $usuarios,
                'cuentas_contables' => $cuentas_contables,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('cajas.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_cajas');

        DB::beginTransaction();

        $request->validate([
            'nombre' => ['required', Rule::unique('cajas')->ignore($id)],
            'usuario' => ['required', 'numeric'],
            'cuenta_ingreso' => ['required', 'numeric'],
            'cuenta_egreso' => ['required', 'numeric']
        ]);

        try {
            $caja = Caja::findOrFail($id);
            $caja->nombre = removeAccents(Str::upper($request->nombre));
            $caja->cuenta_ingreso_id = $request->cuenta_ingreso;
            $caja->cuenta_egreso_id = $request->cuenta_egreso;
            $caja->actualizado_por_id = Auth::id();
            $caja->save();

            if (UsuarioCaja::where('usuario_id', $request->usuario)->where('caja_id', '!=', $id)->first()) {
                return response()->json([
                    'message' => 'El usuario seleccionado ya cuenta con una caja asignada.',
                ]);
            } else {
                UsuarioCaja::where('caja_id', $id)->delete();
                $usuario_caja = new UsuarioCaja();
                $usuario_caja->usuario_id = $request->usuario;
                $usuario_caja->caja_id = $caja->id;
                $usuario_caja->save();
            }

            DB::commit();

            $cajas = Caja::get();
            return response()->json([
                'message' => 'La caja ' . $caja->nombre . ' fue actualizada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('cajas.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_unactivate($id)
    {
        $this->authorize('inactivar_cajas');

        try {
            $caja = Caja::findOrFail($id);
            return response()->json([
                'caja' => $caja,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('cajas.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_cajas');

        DB::beginTransaction();

        try {
            $caja = Caja::findOrFail($id);
            $caja->actualizado_por_id = Auth::id();
            $caja->estado = 'IN';
            $caja->save();

            DB::commit();

            return response()->json([
                'message' => 'La caja ' . $caja->nombre . ' fue inactivada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('cajas.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_activate($id)
    {
        $this->authorize('activar_cajas');

        try {
            $caja = Caja::findOrFail($id);
            return response()->json([
                'caja' => $caja,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('cajas.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_cajas');

        DB::beginTransaction();

        try {
            $caja = Caja::findOrFail($id);
            $caja->actualizado_por_id = Auth::id();
            $caja->estado = 'AC';
            $caja->save();

            DB::commit();

            return response()->json([
                'message' => 'La caja ' . $caja->nombre . ' fue activada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('cajas.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_cajas');

        try {
            $caja = Caja::findOrFail($id);
            return response()->json([
                'caja' => $caja,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('cajas.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_cajas');

        DB::beginTransaction();

        try {
            $caja = Caja::findOrFail($id);
            UsuarioCaja::where('caja_id', $id)->delete();
            if ($caja->monto > 0) {
                return response()->json([
                    'message' => 'La caja ' . $caja->nombre . ' no se puede eliminar. El monto en ella es diferente a 0.',
                ]);
            }

            $caja->delete();

            DB::commit();

            return response()->json([
                'message' => 'La caja ' . $caja->nombre . ' fue eliminada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('cajas.index')->with('error-message', 'La caja ' . $caja->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('cajas.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
