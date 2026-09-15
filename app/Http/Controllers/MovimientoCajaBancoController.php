<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\Models\MovimientoCajaBanco;
use App\Models\Caja;
use App\Models\CuentaBancaria;
use App\Models\TipoMovimiento;
use App\Models\Cotizacion;
use App\Models\AsientoContable;
use App\Models\AsientoContableDetalle;
use App\Models\SaldoCuentaContable;
use App\Models\SaldoCuentaContableDetalle;

class MovimientoCajaBancoController extends Controller
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
        $this->authorize('ver_cajas_cuentas_movimientos');

        try {
            $movimientos_cajas_bancos = MovimientoCajaBanco::orderBy('fecha', 'desc')->get();
            return view('movimientos_cajas_bancos/index')->with(compact('movimientos_cajas_bancos'));
        } catch (\Exception $e) {
            return redirect()->route('movimientos_cajas_bancos.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_cajas_cuentas_movimientos');

        try {
            $cajas = Caja::where('estado', 'AC')->get();
            $cuentas_bancarias = CuentaBancaria::with('banco', 'moneda')->where('estado', 'AC')->get();
            $tipos_movimientos = TipoMovimiento::where('tipo', 'CB')->where('estado', 'AC')->orderBy('id', 'asc')->get();
            return view('movimientos_cajas_bancos/create')->with(compact('cajas', 'cuentas_bancarias', 'tipos_movimientos'));
        } catch (\Exception $e) {
            return redirect()->route('movimientos_cajas_bancos.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_cajas_cuentas_movimientos');

        if ($request->tipo_movimiento == 7) {
            if ($request->caja) {
                $monto = Caja::findOrFail($request->caja)->monto;
            } else {
                $monto = 99999999999;
            }
        } else if ($request->tipo_movimiento == 8){
            if ($request->cuenta_bancaria) {
                $monto = CuentaBancaria::findOrFail($request->cuenta_bancaria)->monto;
            } else {
                $monto = 99999999999;
            }
        }


        $request->validate([
            'tipo_movimiento' => ['required', 'numeric'],
            'caja' => ['required', 'numeric'],
            'moneda' => 'nullable',
            'sentido' => 'nullable',
            'cuenta_bancaria' => ['required', 'numeric'],
            'monto' => ['required', 'numeric', 'max:' . $monto],
            'motivo' => 'required'
        ]);

        DB::beginTransaction();

        try {
            $movimiento = new MovimientoCajaBanco();
            $movimiento->fecha = Carbon::now();
            $movimiento->caja_id = $request->caja;
            $movimiento->cuenta_bancaria_id = $request->cuenta_bancaria;
            if ($request->tipo_movimiento == 8) {
                $movimiento->sentido = 'I';
            } else {
                $movimiento->sentido = 'D';
            }
            $movimiento->monto = $request->monto;
            $movimiento->tipo_movimiento_id = $request->tipo_movimiento;
            $movimiento->motivo = removeAccents(Str::upper($request->motivo));
            $movimiento->creado_por_id = Auth::id();
            $movimiento->save();

            DB::commit();

            return redirect()->route('movimientos_cajas_bancos.index')->with('success-message', 'El movimiento entre caja y banco fue creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('movimientos_cajas_bancos.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_cajas_cuentas_movimientos');

        try {
            $movimiento = MovimientoCajaBanco::findOrFail($id);
            $cajas = Caja::where('estado', 'AC')->get();
            $cuentas_bancarias = CuentaBancaria::with('banco')->where('estado', 'AC')->get();
            $tipos_movimientos = TipoMovimiento::where('tipo', 'BA')->where('estado', 'AC')->orderBy('id', 'asc')->get();
            return view('movimientos_cajas_bancos/edit')->with(compact('movimiento', 'cajas', 'cuentas_bancarias', 'tipos_movimientos'));
        } catch (\Exception $e) {
            return redirect()->route('movimientos_cajas_bancos.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_cajas_cuentas_movimientos');

        if ($request->tipo_movimiento == 7) {
            $monto = Caja::findOrFail($request->caja)->monto;
        } else if ($request->tipo_movimiento == 8){
            $monto = CuentaBancaria::findOrFail($request->cuenta_bancaria)->monto;
        }

        $request->validate([
            'tipo_movimiento' => ['required', 'numeric'],
            'caja' => ['required', 'numeric'],
            'cuenta_bancaria' => ['required', 'numeric'],
            'monto' => ['required', 'numeric', 'max:' . $monto],
            'motivo' => 'required'
        ]);

        DB::beginTransaction();

        try {
            $movimiento = MovimientoCajaBanco::findOrFail($id);
            $movimiento->caja_id = $request->caja;
            $movimiento->cuenta_bancaria_id = $request->cuenta_bancaria;
            if ($request->tipo_movimiento == 8) {
                $movimiento->sentido = 'I';
            } else {
                $movimiento->sentido = 'D';
            }
            $movimiento->monto = $request->monto;
            $movimiento->tipo_movimiento_id = $request->tipo_movimiento;
            $movimiento->motivo = removeAccents(Str::upper($request->motivo));
            $movimiento->actualizado_por_id = Auth::id();
            $movimiento->save();

            DB::commit();

            return redirect()->route('movimientos_cajas_bancos.index')->with('success-message', 'El movimiento entre caja y banco fue actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('movimientos_cajas_bancos.index')->with('error-message', $e->getMessage());
        }
    }

    public function approve($id)
    {
        $this->authorize('aprobar_cajas_cuentas_movimientos');

        DB::beginTransaction();

        try {
            $movimiento = MovimientoCajaBanco::findOrFail($id);
            $movimiento->aprobado_por_id = Auth::id();
            $movimiento->estado = 'AP';
            $movimiento->save();

            $caja = Caja::findOrFail($movimiento->caja_id);
            $cuenta_bancaria = CuentaBancaria::findOrFail($movimiento->cuenta_bancaria_id);

            $old_asiento_contable = AsientoContable::orderBy('id', 'desc')->first();
            if ($old_asiento_contable) {
                $numero_asiento = $old_asiento_contable->numero + 1;
            } else {
                $numero_asiento = 1;
            }

            if ($movimiento->tipo_movimiento_id == 7) {
                $caja->monto = $caja->monto - $movimiento->monto;

                if ($cuenta_bancaria->moneda_id == 1) {
                    $cuenta_bancaria->monto = $cuenta_bancaria->monto + $movimiento->monto;
                } else {
                    $cotizacion = Cotizacion::orderBy('id', 'desc')->first();
                    if (!$cotizacion) {
                        return back()->with('error-message', 'El movimiento no puede ser aprobado. No existe una cotización cargada.');
                    } else {
                        $monto = $movimiento->monto / $cotizacion->precio_venta;
                        $cuenta_bancaria->monto = $cuenta_bancaria->monto + $monto;
                    }

                    $asiento = new AsientoContable();
                    $asiento->numero = $numero_asiento;
                    $asiento->fecha = Carbon::now();
                    $asiento->origen = 'MOV. CTA. CAJA/BANCO';
                    $asiento->moneda_id = 1;
                    $asiento->movimiento_caja_banco_id = $movimiento->id;
                    $asiento->unidad_negocio_id = 1; //aca va la unidad de negocio NINGUNO
                    $asiento->subunidad_negocio_id = 1; //aca va la subunidad de negocio NINGUNO
                    $asiento->save();

                    $asiento_detalle_origen = new AsientoContableDetalle();
                    $asiento_detalle_origen->asiento_id = $asiento->id;
                    $asiento_detalle_origen->cuenta_contable_id = $caja->cuenta_egreso_id;
                    $asiento_detalle_origen->descripcion = 'MOV. CTA. CAJA/BANCO N°: ' . $movimiento->id;
                    $asiento_detalle_origen->haber = $movimiento->monto;
                    $asiento_detalle_origen->centro_costo_id = 1; //aca va el centro de costo NINGUNO
                    $asiento_detalle_origen->subcentro_costo_id = 1; //aca va el subcentro de costo NINGUNO
                    $asiento_detalle_origen->save();

                    $asiento_detalle_destino = new AsientoContableDetalle();
                    $asiento_detalle_destino->asiento_id = $asiento->id;
                    $asiento_detalle_destino->cuenta_contable_id = $cuenta_bancaria->cuenta_egreso_id;
                    $asiento_detalle_destino->descripcion = 'MOV. CTA. CAJA/BANCO N°: ' . $movimiento->id;
                    $asiento_detalle_destino->debe = $movimiento->monto;
                    $asiento_detalle_destino->centro_costo_id = 1; //aca va el centro de costo NINGUNO
                    $asiento_detalle_destino->subcentro_costo_id = 1; //aca va el subcentro de costo NINGUNO
                    $asiento_detalle_destino->save();

                    $saldo_cuenta_origen = SaldoCuentaContable::where('cuenta_contable_id', $caja->cuenta_egreso_id)->first();
                    $saldo_cuenta_desitno = SaldoCuentaContable::where('cuenta_contable_id', $cuenta_bancaria->cuenta_egreso_id)->first();
                    if (!$saldo_cuenta_origen || !$saldo_cuenta_destino) {
                        return back()->with('Hubo un problema al aprobar el movimiento. La cuenta contable de la caja/cuenta bancaria no se encuentra seleccionada.');
                    } else {
                        $saldo_cuenta_origen->saldo = $saldo_cuenta_origen->saldo - $movimiento->monto;
                        $saldo_cuenta_origen->save();

                        $saldo_cuenta_origen_detalle = new SaldoCuentaContableDetalle();
                        $saldo_cuenta_origen_detalle->saldo_cuenta_contable_id = $saldo_cuenta_origen->id;
                        $saldo_cuenta_origen_detalle->mes = Carbon::today()->format('m');
                        $saldo_cuenta_origen_detalle->anho = Carbon::today()->format('Y');
                        $saldo_cuenta_origen_detalle->haber = $movimiento->monto;
                        $saldo_cuenta_origen_detalle->movimiento_caja_banco_id = $movimiento->id;
                        $saldo_cuenta_origen_detalle->save();

                        $saldo_cuenta_destino->saldo = $saldo_cuenta_destino->saldo + $movimiento->monto;
                        $saldo_cuenta_destino->save();

                        $saldo_cuenta_destino_detalle = new SaldoCuentaContableDetalle();
                        $saldo_cuenta_destino_detalle->saldo_cuenta_contable_id = $saldo_cuenta_destino->id;
                        $saldo_cuenta_destino_detalle->mes = Carbon::today()->format('m');
                        $saldo_cuenta_destino_detalle->anho = Carbon::today()->format('Y');
                        $saldo_cuenta_destino_detalle->debe = $movimiento->monto;
                        $saldo_cuenta_destino_detalle->movimiento_caja_banco_id = $movimiento->id;
                        $saldo_cuenta_destino_detalle->save();
                    }
                }

            } else {
                $cuenta_bancaria->monto = $cuenta_bancaria->monto - $movimiento->monto;

                if ($cuenta_bancaria->moneda_id == 1) {
                    $caja->monto = $caja->monto + $movimiento->monto;

                    $asiento = new AsientoContable();
                    $asiento->numero = $numero_asiento;
                    $asiento->fecha = Carbon::now();
                    $asiento->origen = 'MOV. CTA. BANCO/CAJA';
                    $asiento->moneda_id = 1;
                    $asiento->movimiento_caja_banco_id = $movimiento->id;
                    $asiento->unidad_negocio_id = 1; //aca va la unidad de negocio NINGUNO
                    $asiento->subunidad_negocio_id = 1; //aca va la subunidad de negocio NINGUNO
                    $asiento->save();

                    $asiento_detalle_origen = new AsientoContableDetalle();
                    $asiento_detalle_origen->asiento_id = $asiento->id;
                    $asiento_detalle_origen->cuenta_contable_id = $cuenta_bancaria->cuenta_egreso_id;
                    $asiento_detalle_origen->descripcion = 'MOV. CTA. BANCO/CAJA N°: ' . $movimiento->id;
                    $asiento_detalle_origen->haber = $movimiento->monto;
                    $asiento_detalle_origen->centro_costo_id = 1; //aca va el centro de costo NINGUNO
                    $asiento_detalle_origen->subcentro_costo_id = 1; //aca va el subcentro de costo NINGUNO
                    $asiento_detalle_origen->save();

                    $asiento_detalle_destino = new AsientoContableDetalle();
                    $asiento_detalle_destino->asiento_id = $asiento->id;
                    $asiento_detalle_destino->cuenta_contable_id = $caja->cuenta_egreso_id;
                    $asiento_detalle_destino->descripcion = 'MOV. CTA. BANCO/CAJA N°: ' . $movimiento->id;
                    $asiento_detalle_destino->debe = $movimiento->monto;
                    $asiento_detalle_destino->centro_costo_id = 1; //aca va el centro de costo NINGUNO
                    $asiento_detalle_destino->subcentro_costo_id = 1; //aca va el subcentro de costo NINGUNO
                    $asiento_detalle_destino->save();

                    $saldo_cuenta_origen = SaldoCuentaContable::where('cuenta_contable_id', $cuenta_bancaria->cuenta_egreso_id)->first();
                    $saldo_cuenta_desitno = SaldoCuentaContable::where('cuenta_contable_id', $caja->cuenta_egreso_id)->first();
                    if (!$saldo_cuenta_origen || !$saldo_cuenta_destino) {
                        return back()->with('Hubo un problema al aprobar el movimiento. La cuenta contable de la caja/cuenta bancaria no se encuentra seleccionada.');
                    } else {
                        $saldo_cuenta_origen->saldo = $saldo_cuenta_origen->saldo - $movimiento->monto;
                        $saldo_cuenta_origen->save();

                        $saldo_cuenta_origen_detalle = new SaldoCuentaContableDetalle();
                        $saldo_cuenta_origen_detalle->saldo_cuenta_contable_id = $saldo_cuenta_origen->id;
                        $saldo_cuenta_origen_detalle->mes = Carbon::today()->format('m');
                        $saldo_cuenta_origen_detalle->anho = Carbon::today()->format('Y');
                        $saldo_cuenta_origen_detalle->haber = $movimiento->monto;
                        $saldo_cuenta_origen_detalle->movimiento_caja_banco_id = $movimiento->id;
                        $saldo_cuenta_origen_detalle->save();

                        $saldo_cuenta_destino->saldo = $saldo_cuenta_destino->saldo + $movimiento->monto;
                        $saldo_cuenta_destino->save();

                        $saldo_cuenta_destino_detalle = new SaldoCuentaContableDetalle();
                        $saldo_cuenta_destino_detalle->saldo_cuenta_contable_id = $saldo_cuenta_destino->id;
                        $saldo_cuenta_destino_detalle->mes = Carbon::today()->format('m');
                        $saldo_cuenta_destino_detalle->anho = Carbon::today()->format('Y');
                        $saldo_cuenta_destino_detalle->debe = $movimiento->monto;
                        $saldo_cuenta_destino_detalle->movimiento_caja_banco_id = $movimiento->id;
                        $saldo_cuenta_destino_detalle->save();
                    }

                } else {
                    $cotizacion = Cotizacion::orderBy('id', 'desc')->first();
                    if (!$cotizacion) {
                        return back()->with('error-message', 'El movimiento no puede ser aprobado. No existe una cotización cargada.');
                    } else {
                        $monto = $movimiento->monto * $cotizacion->precio_venta;
                        $caja->monto = $caja->monto + $monto;

                        $asiento = new AsientoContable();
                        $asiento->numero = $numero_asiento;
                        $asiento->fecha = Carbon::now();
                        $asiento->origen = 'MOV. CTA. BANCO/CAJA';
                        $asiento->moneda_id = 1;
                        $asiento->movimiento_caja_banco_id = $movimiento->id;
                        $asiento->unidad_negocio_id = 1; //aca va la unidad de negocio NINGUNO
                        $asiento->subunidad_negocio_id = 1; //aca va la subunidad de negocio NINGUNO
                        $asiento->save();

                        $asiento_detalle_origen = new AsientoContableDetalle();
                        $asiento_detalle_origen->asiento_id = $asiento->id;
                        $asiento_detalle_origen->cuenta_contable_id = $cuenta_bancaria->cuenta_egreso_id;
                        $asiento_detalle_origen->descripcion = 'MOV. CTA. BANCO/CAJA N°: ' . $movimiento->id;
                        $asiento_detalle_origen->haber = $monto;
                        $asiento_detalle_origen->centro_costo_id = 1; //aca va el centro de costo NINGUNO
                        $asiento_detalle_origen->subcentro_costo_id = 1; //aca va el subcentro de costo NINGUNO
                        $asiento_detalle_origen->save();

                        $asiento_detalle_destino = new AsientoContableDetalle();
                        $asiento_detalle_destino->asiento_id = $asiento->id;
                        $asiento_detalle_destino->cuenta_contable_id = $caja->cuenta_egreso_id;
                        $asiento_detalle_destino->descripcion = 'MOV. CTA. BANCO/CAJA N°: ' . $movimiento->id;
                        $asiento_detalle_destino->debe = $monto;
                        $asiento_detalle_destino->centro_costo_id = 1; //aca va el centro de costo NINGUNO
                        $asiento_detalle_destino->subcentro_costo_id = 1; //aca va el subcentro de costo NINGUNO
                        $asiento_detalle_destino->save();

                        $saldo_cuenta_origen = SaldoCuentaContable::where('cuenta_contable_id', $cuenta_bancaria->cuenta_egreso_id)->first();
                        $saldo_cuenta_desitno = SaldoCuentaContable::where('cuenta_contable_id', $caja->cuenta_egreso_id)->first();
                        if (!$saldo_cuenta_origen || !$saldo_cuenta_destino) {
                            return back()->with('Hubo un problema al aprobar el movimiento. La cuenta contable de la cuenta bancaria/caja no se encuentra seleccionada.');
                        } else {
                            $saldo_cuenta_origen->saldo = $saldo_cuenta_origen->saldo - $monto;
                            $saldo_cuenta_origen->save();

                            $saldo_cuenta_origen_detalle = new SaldoCuentaContableDetalle();
                            $saldo_cuenta_origen_detalle->saldo_cuenta_contable_id = $saldo_cuenta_origen->id;
                            $saldo_cuenta_origen_detalle->mes = Carbon::today()->format('m');
                            $saldo_cuenta_origen_detalle->anho = Carbon::today()->format('Y');
                            $saldo_cuenta_origen_detalle->haber = $monto;
                            $saldo_cuenta_origen_detalle->movimiento_caja_banco_id = $movimiento->id;
                            $saldo_cuenta_origen_detalle->save();

                            $saldo_cuenta_destino->saldo = $saldo_cuenta_destino->saldo + $monto;
                            $saldo_cuenta_destino->save();

                            $saldo_cuenta_destino_detalle = new SaldoCuentaContableDetalle();
                            $saldo_cuenta_destino_detalle->saldo_cuenta_contable_id = $saldo_cuenta_destino->id;
                            $saldo_cuenta_destino_detalle->mes = Carbon::today()->format('m');
                            $saldo_cuenta_destino_detalle->anho = Carbon::today()->format('Y');
                            $saldo_cuenta_destino_detalle->debe = $monto;
                            $saldo_cuenta_destino_detalle->movimiento_caja_banco_id = $movimiento->id;
                            $saldo_cuenta_destino_detalle->save();
                        }
                    }
                }
            }
            $caja->save();
            $cuenta_bancaria->save();

            DB::commit();

            return redirect()->route('movimientos_cajas_bancos.index')->with('success-message','El movimiento entre caja y banco fue aprobado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('movimientos_cajas_bancos.index')->with('error-message', $e->getMessage());
        }
    }

    public function unapprove($id)
    {
        $this->authorize('anular_aprobacion_cajas_cuentas_movimientos');

        DB::beginTransaction();

        try {
            $movimiento = MovimientoCajaBanco::findOrFail($id);
            $movimiento->aprobado_por_id = null;
            $movimiento->estado = 'PE';
            $movimiento->save();

            $caja = Caja::findOrFail($movimiento->caja_id);
            $cuenta_bancaria = CuentaBancaria::findOrFail($movimiento->cuenta_bancaria_id);
            $cotizacion = Cotizacion::orderBy('id', 'desc')->first();

            if ($movimiento->tipo_movimiento_id == 7) {
                $caja->monto = $caja->monto + $movimiento->monto;
                if ($cuenta_bancaria->moneda_id == 1) {
                    $cuenta_bancaria->monto = $cuenta_bancaria->monto - $movimiento->monto;
                } else {
                    $monto = $movimiento->monto / $cotizacion->precio_venta;
                    $cuenta_bancaria->monto = $cuenta_bancaria->monto - $monto;
                }
            } else {
                $cuenta_bancaria->monto = $cuenta_bancaria->monto + $movimiento->monto;
                if ($cuenta_bancaria->moneda_id == 1) {
                    $caja->monto = $caja->monto - $movimiento->monto;
                } else {
                    $monto = $movimiento->monto * $cotizacion->precio_venta;
                    $caja->monto = $caja->monto - $monto;
                }
            }
            $caja->save();
            $cuenta_bancaria->save();

            $asiento = AsientoContable::where('movimiento_caja_banco_id', $movimiento->id)->first();
            foreach ($asiento->detalles as $asiento_detalle) {
                $saldo_cuenta = SaldoCuentaContable::where('cuenta_contable_id', $asiento_detalle->cuenta_contable_id)->first();

                if ($asiento_detalle->debe) {
                    $saldo_cuenta->saldo = $saldo_cuenta - $asiento_detalle->debe;
                } else {
                    $saldo_cuenta->saldo = $saldo_cuenta + $asiento_detalle->haber;
                }
                $saldo_cuenta->save();
            }

            $saldo_cuenta_detalles = SaldoCuentaContableDetalle::where('movimiento_banco_id', $movimiento->id)->delete();

            $asiento->estado = 'IN';
            $asiento->actualizado_por_id = Auth::id();
            $asiento->save();

            DB::commit();

            return redirect()->route('movimientos_cajas_bancos.index')->with('error-message','La aprobación del movimiento de banco fue anulado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('movimientos_cajas_bancos.index')->with('error-message', $e->getMessage());
        }
    }

    public function reject($id)
    {
        $this->authorize('rechazar_cajas_cuentas_movimientos');

        DB::beginTransaction();

        try {
            $movimiento = MovimientoCajaBanco::findOrFail($id);
            $movimiento->rechazado_por_id = Auth::id();
            $movimiento->estado = 'RE';
            $movimiento->save();

            DB::commit();

            return redirect()->route('movimientos_cajas_bancos.index')->with('error-message','El movimiento entre caja y banco fue rechazado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('movimientos_cajas_bancos.index')->with('error-message', $e->getMessage());
        }
    }

    public function unreject($id)
    {
        $this->authorize('anular_rechazo_cajas_cuentas_movimientos');

        DB::beginTransaction();

        try {
            $movimiento = MovimientoCajaBanco::findOrFail($id);
            $movimiento->rechazado_por_id = null;
            $movimiento->estado = 'PE';
            $movimiento->save();

            DB::commit();

            return redirect()->route('movimientos_cajas_bancos.index')->with('success-message','El rechazo del movimiento de banco fue anulado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('movimientos_cajas_bancos.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_cajas_cuentas_movimientos');

        DB::beginTransaction();

        try {
            $movimiento = MovimientoCajaBanco::findOrFail($id);
            if ($movimiento->estado != 'PE') {
                return back()->with('error-message', 'El movimiento entre caja y banco no se puede eliminar. Su estado es diferente a pendiente.');
            }
            $movimiento->delete();

            DB::commit();

            return redirect()->route('movimientos_cajas_bancos.index')->with('success-message','El movimiento entre caja y banco fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('movimientos_cajas_bancos.index')->with('error-message', 'El movimiento entre caja y banco no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('movimientos_cajas_bancos.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
