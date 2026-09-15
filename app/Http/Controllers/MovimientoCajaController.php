<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\Models\MovimientoCaja;
use App\Models\Caja;
use App\Models\TipoMovimiento;
use App\Models\AsientoContable;
use App\Models\AsientoContableDetalle;
use App\Models\SaldoCuentaContable;
use App\Models\SaldoCuentaContableDetalle;

class MovimientoCajaController extends Controller
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
        $this->authorize('ver_cajas_movimientos');

        try {
            $movimientos_cajas = MovimientoCaja::orderBy('fecha', 'desc')->get();
            return view('movimientos_cajas/index')->with(compact('movimientos_cajas'));
        } catch (\Exception $e) {
            return redirect()->route('movimientos_cajas.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_cajas_movimientos');

        try {
            $cajas = Caja::where('estado', 'AC')->get();
            $tipos_movimientos = TipoMovimiento::where('tipo', 'CA')->where('estado', 'AC')->orderBy('id', 'asc')->get();
            return view('movimientos_cajas/create')->with(compact('cajas', 'tipos_movimientos'));
        } catch (\Exception $e) {
            return redirect()->route('movimientos_cajas.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_cajas_movimientos');

        if ($request->caja_origen) {
            $monto_caja_origen = Caja::findOrFail($request->caja_origen)->monto;
        } else {
            $monto_caja_origen = 99999999999;
        }

        $request->validate([
            'tipo_movimiento' => ['required', 'numeric'],
            'caja_origen' => ['nullable', 'numeric', 'required_if:tipo_movimiento,2', 'required_if:tipo_movimiento,3'],
            'caja_destino' => ['nullable', 'numeric', 'required_if:tipo_movimiento,1', 'required_if:tipo_movimiento,3'],
            'monto' => ['required', 'numeric', 'max:' . $monto_caja_origen],
            'motivo' => 'required'
        ]);

        DB::beginTransaction();

        try {
            $movimiento = new MovimientoCaja();
            $movimiento->fecha = Carbon::now();
            $movimiento->caja_origen_id = $request->caja_origen;
            $movimiento->caja_destino_id = $request->caja_destino;
            if ($request->tipo_movimiento == 2) {
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

            return redirect()->route('movimientos_cajas.index')->with('success-message', 'El movimiento de caja fue creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('movimientos_cajas.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_cajas_movimientos');

        try {
            $movimiento = MovimientoCaja::findOrFail($id);
            $cajas = Caja::where('estado', 'AC')->get();
            $tipos_movimientos = TipoMovimiento::where('tipo', 'CA')->where('estado', 'AC')->orderBy('id', 'asc')->get();
            return view('movimientos_cajas/edit')->with(compact('movimiento', 'cajas', 'tipos_movimientos'));
        } catch (\Exception $e) {
            return redirect()->route('movimientos_cajas.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_cajas_movimientos');

        if ($request->caja_origen) {
            $monto_caja_origen = Caja::findOrFail($request->caja_origen)->monto;
        } else {
            $monto_caja_origen = 99999999999;
        }

        $request->validate([
            'tipo_movimiento' => ['required', 'numeric'],
            'caja_origen' => ['nullable', 'numeric', 'required_if:tipo_movimiento,2', 'required_if:tipo_movimiento,3'],
            'caja_destino' => ['nullable', 'numeric', 'required_if:tipo_movimiento,1', 'required_if:tipo_movimiento,3'],
            'monto' => ['required', 'numeric', 'max:' . $monto_caja_origen],
            'motivo' => 'required'
        ]);

        DB::beginTransaction();

        try {
            $movimiento = MovimientoCaja::findOrFail($id);
            $movimiento->caja_origen_id = $request->caja_origen;
            $movimiento->caja_destino_id = $request->caja_destino;
            if ($request->tipo_movimiento == 2) {
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

            return redirect()->route('movimientos_cajas.index')->with('success-message', 'El movimiento de caja fue actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('movimientos_cajas.index')->with('error-message', $e->getMessage());
        }
    }

    public function approve($id)
    {
        $this->authorize('aprobar_cajas_movimientos');

        DB::beginTransaction();

        try {
            $movimiento = MovimientoCaja::findOrFail($id);
            $movimiento->aprobado_por_id = Auth::id();
            $movimiento->estado = 'AP';
            $movimiento->save();

            $old_asiento_contable = AsientoContable::orderBy('id', 'desc')->first();
            if ($old_asiento_contable) {
                $numero_asiento = $old_asiento_contable->numero + 1;
            } else {
                $numero_asiento = 1;
            }

            if ($movimiento->tipo_movimiento_id == 1) {
                $caja = Caja::findOrFail($movimiento->caja_destino_id);
                $caja->monto = $caja->monto + $movimiento->monto;
                $caja->save();

                $asiento = new AsientoContable();
                $asiento->numero = $numero_asiento;
                $asiento->fecha = Carbon::now();
                $asiento->origen = 'MOV. CAJA';
                $asiento->moneda_id = 1;
                $asiento->movimiento_caja_id = $movimiento->id;
                $asiento->unidad_negocio_id = 1; //aca va la unidad de negocio NINGUNO
                $asiento->subunidad_negocio_id = 1; //aca va la subunidad de negocio NINGUNO
                $asiento->save();

                $asiento_detalle_origen = new AsientoContableDetalle();
                $asiento_detalle_origen->asiento_id = $asiento->id;
                $asiento_detalle_origen->cuenta_contable_id = 263; //verificar luego para saber de donde entra el monto del movimiento
                $asiento_detalle_origen->descripcion = 'MOV. CAJA DE ENTRADA N°: ' . $movimiento->id;
                $asiento_detalle_origen->haber = $movimiento->monto;
                $asiento_detalle_origen->centro_costo_id = 1; //aca va el centro de costo NINGUNO
                $asiento_detalle_origen->subcentro_costo_id = 1; //aca va el subcentro de costo NINGUNO
                $asiento_detalle_origen->save();

                $asiento_detalle_destino = new AsientoContableDetalle();
                $asiento_detalle_destino->asiento_id = $asiento->id;
                $asiento_detalle_destino->cuenta_contable_id = $caja->cuenta_egreso_id;
                $asiento_detalle_destino->descripcion = 'MOV. CAJA DE ENTRADA N°: ' . $movimiento->id;
                $asiento_detalle_destino->debe = $movimiento->monto;
                $asiento_detalle_destino->centro_costo_id = 1; //aca va el centro de costo NINGUNO
                $asiento_detalle_destino->subcentro_costo_id = 1; //aca va el subcentro de costo NINGUNO
                $asiento_detalle_destino->save();

                $saldo_cuenta_origen = SaldoCuentaContable::where('cuenta_contable_id', 120)->first(); //verificar luego para saber de donde entra el monto del movimiento
                $saldo_cuenta_destino = SaldoCuentaContable::where('cuenta_contable_id', $caja->cuenta_egreso_id)->first();
                if (!$saldo_cuenta_origen || !$saldo_cuenta_destino) {
                    return back()->with('error-message', 'Hubo un problema al aprobar el movimiento. La cuenta contable de la caja no se encuentra seleccionada.');
                } else {
                    $saldo_cuenta_origen->saldo = $saldo_cuenta_origen->saldo - $movimiento->monto;
                    $saldo_cuenta_origen->save();

                    $saldo_cuenta_origen_detalle = new SaldoCuentaContableDetalle();
                    $saldo_cuenta_origen_detalle->saldo_cuenta_contable_id = $saldo_cuenta_origen->id;
                    $saldo_cuenta_origen_detalle->mes = Carbon::today()->format('m');
                    $saldo_cuenta_origen_detalle->anho = Carbon::today()->format('Y');
                    $saldo_cuenta_origen_detalle->haber = $movimiento->monto;
                    $saldo_cuenta_origen_detalle->movimiento_caja_id = $movimiento->id;
                    $saldo_cuenta_origen_detalle->save();

                    $saldo_cuenta_destino->saldo = $saldo_cuenta_destino->saldo + $movimiento->monto;
                    $saldo_cuenta_destino->save();

                    $saldo_cuenta_destino_detalle = new SaldoCuentaContableDetalle();
                    $saldo_cuenta_destino_detalle->saldo_cuenta_contable_id = $saldo_cuenta_destino->id;
                    $saldo_cuenta_destino_detalle->mes = Carbon::today()->format('m');
                    $saldo_cuenta_destino_detalle->anho = Carbon::today()->format('Y');
                    $saldo_cuenta_destino_detalle->debe = $movimiento->monto;
                    $saldo_cuenta_destino_detalle->movimiento_caja_id = $movimiento->id;
                    $saldo_cuenta_destino_detalle->save();
                }

            } else if ($movimiento->tipo_movimiento_id == 2) {
                $caja = Caja::findOrFail($movimiento->caja_origen_id);
                $caja->monto = $caja->monto - $movimiento->monto;
                $caja->save();

                $asiento = new AsientoContable();
                $asiento->numero = $numero_asiento;
                $asiento->fecha = Carbon::now();
                $asiento->origen = 'MOV. CAJA';
                $asiento->moneda_id = 1;
                $asiento->movimiento_caja_id = $movimiento->id;
                $asiento->unidad_negocio_id = 1; //aca va la unidad de negocio NINGUNO
                $asiento->subunidad_negocio_id = 1; //aca va la subunidad de negocio NINGUNO
                $asiento->save();

                $asiento_detalle_origen = new AsientoContableDetalle();
                $asiento_detalle_origen->asiento_id = $asiento->id;
                $asiento_detalle_origen->cuenta_contable_id = $caja->cuenta_egreso_id;
                $asiento_detalle_origen->descripcion = 'MOV. CAJA DE SALIDA N°: ' . $movimiento->id;
                $asiento_detalle_origen->haber = $movimiento->monto;
                $asiento_detalle_origen->centro_costo_id = 1; //aca va el centro de costo NINGUNO
                $asiento_detalle_origen->subcentro_costo_id = 1; //aca va el subcentro de costo NINGUNO
                $asiento_detalle_origen->save();

                $asiento_detalle_destino = new AsientoContableDetalle();
                $asiento_detalle_destino->asiento_id = $asiento->id;
                $asiento_detalle_destino->cuenta_contable_id = 91; //verificar luego para saber de donde entra el monto del movimiento
                $asiento_detalle_destino->descripcion = 'MOV. CAJA DE SALIDA N°: ' . $movimiento->id;
                $asiento_detalle_destino->haber = $movimiento->monto;
                $asiento_detalle_destino->centro_costo_id = 1; //aca va el centro de costo NINGUNO
                $asiento_detalle_destino->subcentro_costo_id = 1; //aca va el subcentro de costo NINGUNO
                $asiento_detalle_destino->save();

                $saldo_cuenta_origen = SaldoCuentaContable::where('cuenta_contable_id', $caja->cuenta_egreso_id)->first();
                $saldo_cuenta_destino = SaldoCuentaContable::where('cuenta_contable_id', 70)->first(); //verificar luego para saber de donde entra el monto del movimiento
                if (!$saldo_cuenta_origen || !$saldo_cuenta_destino) {
                    return back()->with('error-message', 'Hubo un problema al aprobar el movimiento. La cuenta contable de la caja no se encuentra seleccionada.');
                } else {
                    $saldo_cuenta_origen->saldo = $saldo_cuenta_origen->saldo - $movimiento->monto;
                    $saldo_cuenta_origen->save();

                    $saldo_cuenta_origen_detalle = new SaldoCuentaContableDetalle();
                    $saldo_cuenta_origen_detalle->saldo_cuenta_contable_id = $saldo_cuenta_origen->id;
                    $saldo_cuenta_origen_detalle->mes = Carbon::today()->format('m');
                    $saldo_cuenta_origen_detalle->anho = Carbon::today()->format('Y');
                    $saldo_cuenta_origen_detalle->haber = $movimiento->monto;
                    $saldo_cuenta_origen_detalle->movimiento_caja_id = $movimiento->id;
                    $saldo_cuenta_origen_detalle->save();

                    $saldo_cuenta_destino->saldo = $saldo_cuenta_destino->saldo + $movimiento->monto;
                    $saldo_cuenta_destino->save();

                    $saldo_cuenta_destino_detalle = new SaldoCuentaContableDetalle();
                    $saldo_cuenta_destino_detalle->saldo_cuenta_contable_id = $saldo_cuenta_destino->id;
                    $saldo_cuenta_destino_detalle->mes = Carbon::today()->format('m');
                    $saldo_cuenta_destino_detalle->anho = Carbon::today()->format('Y');
                    $saldo_cuenta_destino_detalle->debe = $movimiento->monto;
                    $saldo_cuenta_destino_detalle->movimiento_caja_id = $movimiento->id;
                    $saldo_cuenta_destino_detalle->save();
                }

            } else if ($movimiento->tipo_movimiento_id == 3) {
                $caja_origen = Caja::findOrFail($movimiento->caja_origen_id);
                $caja_origen->monto = $caja_origen->monto - $movimiento->monto;
                $caja_origen->save();

                $caja_destino = Caja::findOrFail($movimiento->caja_destino_id);
                $caja_destino->monto = $caja_destino->monto + $movimiento->monto;
                $caja_destino->save();

                $asiento = new AsientoContable();
                $asiento->numero = $numero_asiento;
                $asiento->fecha = Carbon::now();
                $asiento->origen = 'MOV. CAJA';
                $asiento->moneda_id = 1;
                $asiento->movimiento_caja_id = $movimiento->id;
                $asiento->unidad_negocio_id = 1; //aca va la unidad de negocio NINGUNO
                $asiento->subunidad_negocio_id = 1; //aca va la subunidad de negocio NINGUNO
                $asiento->save();

                $asiento_detalle_origen = new AsientoContableDetalle();
                $asiento_detalle_origen->asiento_id = $asiento->id;
                $asiento_detalle_origen->cuenta_contable_id = $caja_origen->cuenta_egreso_id;
                $asiento_detalle_origen->descripcion = 'MOV. CAJA ENTRE CUENTAS N°: ' . $movimiento->id;
                $asiento_detalle_origen->haber = $movimiento->monto;
                $asiento_detalle_origen->centro_costo_id = 1; //aca va el centro de costo NINGUNO
                $asiento_detalle_origen->subcentro_costo_id = 1; //aca va el subcentro de costo NINGUNO
                $asiento_detalle_origen->save();

                $asiento_detalle_destino = new AsientoContableDetalle();
                $asiento_detalle_destino->asiento_id = $asiento->id;
                $asiento_detalle_destino->cuenta_contable_id = $caja_destino->cuenta_egreso_id;
                $asiento_detalle_destino->descripcion = 'MOV. CAJA ENTRE CUENTAS N°: ' . $movimiento->id;
                $asiento_detalle_destino->debe = $movimiento->monto;
                $asiento_detalle_destino->centro_costo_id = 1; //aca va el centro de costo NINGUNO
                $asiento_detalle_destino->subcentro_costo_id = 1; //aca va el subcentro de costo NINGUNO
                $asiento_detalle_destino->save();

                $saldo_cuenta_origen = SaldoCuentaContable::where('cuenta_contable_id', $caja_origen->cuenta_egreso_id)->first();
                $saldo_cuenta_destino = SaldoCuentaContable::where('cuenta_contable_id', $caja_destino->cuenta_egreso_id)->first();
                if (!$saldo_cuenta_origen || !$saldo_cuenta_destino) {
                    return back()->with('error-message', 'Hubo un problema al aprobar el movimiento. La cuenta contable de la caja no se encuentra seleccionada.');
                } else {
                    $saldo_cuenta_origen->saldo = $saldo_cuenta_origen->saldo - $movimiento->monto;
                    $saldo_cuenta_origen->save();

                    $saldo_cuenta_origen_detalle = new SaldoCuentaContableDetalle();
                    $saldo_cuenta_origen_detalle->saldo_cuenta_contable_id = $saldo_cuenta_origen->id;
                    $saldo_cuenta_origen_detalle->mes = Carbon::today()->format('m');
                    $saldo_cuenta_origen_detalle->anho = Carbon::today()->format('Y');
                    $saldo_cuenta_origen_detalle->haber = $movimiento->monto;
                    $saldo_cuenta_origen_detalle->movimiento_caja_id = $movimiento->id;
                    $saldo_cuenta_origen_detalle->save();

                    $saldo_cuenta_destino->saldo = $saldo_cuenta_destino->saldo + $movimiento->monto;
                    $saldo_cuenta_destino->save();

                    $saldo_cuenta_destino_detalle = new SaldoCuentaContableDetalle();
                    $saldo_cuenta_destino_detalle->saldo_cuenta_contable_id = $saldo_cuenta_destino->id;
                    $saldo_cuenta_destino_detalle->mes = Carbon::today()->format('m');
                    $saldo_cuenta_destino_detalle->anho = Carbon::today()->format('Y');
                    $saldo_cuenta_destino_detalle->debe = $movimiento->monto;
                    $saldo_cuenta_destino_detalle->movimiento_caja_id = $movimiento->id;
                    $saldo_cuenta_destino_detalle->save();
                }
            }

            DB::commit();

            return redirect()->route('movimientos_cajas.index')->with('success-message','El movimiento de caja fue aprobado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('movimientos_cajas.index')->with('error-message', $e->getMessage());
        }
    }

    public function unapprove($id)
    {
        $this->authorize('anular_aprobacion_cajas_movimientos');

        DB::beginTransaction();

        try {
            $movimiento = MovimientoCaja::findOrFail($id);

            if ($movimiento->tipo_movimiento_id == 12) {
                return back()->with('error-message', 'Debe anular la factura para que el movimiento sea anulado automáticamente.');
            }


            $movimiento->aprobado_por_id = null;
            $movimiento->estado = 'PE';
            $movimiento->save();

            if ($movimiento->tipo_movimiento_id == 1) {
                $caja = Caja::findOrFail($movimiento->caja_destino_id);
                $caja->monto = $caja->monto - $movimiento->monto;
                $caja->save();

                $asiento = AsientoContable::where('movimiento_caja_id', $movimiento->id)->first();
                foreach ($asiento->detalles as $asiento_detalle) {
                    $saldo_cuenta = SaldoCuentaContable::where('cuenta_contable_id', $asiento_detalle->cuenta_contable_id)->first();

                    if ($asiento_detalle->debe) {
                        $saldo_cuenta->saldo = $saldo_cuenta - $asiento_detalle->debe;
                    } else {
                        $saldo_cuenta->saldo = $saldo_cuenta + $asiento_detalle->haber;
                    }
                    $saldo_cuenta->save();
                }

                $saldo_cuenta_detalles = SaldoCuentaContableDetalle::where('movimiento_caja_id', $movimiento->id)->delete();

                $asiento->estado = 'IN';
                $asiento->save();

            } else if ($movimiento->tipo_movimiento_id == 2) {
                $caja = Caja::findOrFail($movimiento->caja_origen_id);
                $caja->monto = $caja->monto + $movimiento->monto;
                $caja->save();

                $asiento = AsientoContable::where('movimiento_caja_id', $movimiento->id)->first();
                foreach ($asiento->detalles as $asiento_detalle) {
                    $saldo_cuenta = SaldoCuentaContable::where('cuenta_contable_id', $asiento_detalle->cuenta_contable_id)->first();

                    if ($asiento_detalle->debe) {
                        $saldo_cuenta->saldo = $saldo_cuenta - $asiento_detalle->debe;
                    } else {
                        $saldo_cuenta->saldo = $saldo_cuenta + $asiento_detalle->haber;
                    }
                    $saldo_cuenta->save();
                }

                $saldo_cuenta_detalles = SaldoCuentaContableDetalle::where('movimiento_caja_id', $movimiento->id)->delete();

                $asiento->estado = 'IN';
                $asiento->save();

            } else if ($movimiento->tipo_movimiento_id == 3) {
                $caja_origen = Caja::findOrFail($movimiento->caja_origen_id);
                $caja_origen->monto = $caja_origen->monto + $movimiento->monto;
                $caja_origen->save();

                $caja_destino = Caja::findOrFail($movimiento->caja_destino_id);
                $caja_destino->monto = $caja_destino->monto - $movimiento->monto;
                $caja_destino->save();

                $asiento = AsientoContable::where('movimiento_caja_id', $movimiento->id)->first();
                foreach ($asiento->detalles as $asiento_detalle) {
                    $saldo_cuenta = SaldoCuentaContable::where('cuenta_contable_id', $asiento_detalle->cuenta_contable_id)->first();

                    if ($asiento_detalle->debe) {
                        $saldo_cuenta->saldo = $saldo_cuenta - $asiento_detalle->debe;
                    } else {
                        $saldo_cuenta->saldo = $saldo_cuenta + $asiento_detalle->haber;
                    }
                    $saldo_cuenta->save();
                }

                $saldo_cuenta_detalles = SaldoCuentaContableDetalle::where('movimiento_caja_id', $movimiento->id)->delete();

                $asiento->estado = 'IN';
                $asiento->actualizado_por_id = Auth::id();
                $asiento->save();
            }

            DB::commit();

            return redirect()->route('movimientos_cajas.index')->with('error-message','La aprobación del movimiento de caja fue anulado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('movimientos_cajas.index')->with('error-message', $e->getMessage());
        }
    }

    public function reject($id)
    {
        $this->authorize('rechazar_cajas_movimientos');

        DB::beginTransaction();

        try {
            $movimiento = MovimientoCaja::findOrFail($id);
            $movimiento->rechazado_por_id = Auth::id();
            $movimiento->estado = 'RE';
            $movimiento->save();

            DB::commit();

            return redirect()->route('movimientos_cajas.index')->with('error-message','El movimiento de caja fue rechazado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('movimientos_cajas.index')->with('error-message', $e->getMessage());
        }
    }

    public function unreject($id)
    {
        $this->authorize('anular_rechazo_cajas_movimientos');

        DB::beginTransaction();

        try {
            $movimiento = MovimientoCaja::findOrFail($id);
            $movimiento->rechazado_por_id = null;
            $movimiento->estado = 'PE';
            $movimiento->save();

            DB::commit();

            return redirect()->route('movimientos_cajas.index')->with('success-message','El rechazo del movimiento de caja fue anulado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('movimientos_cajas.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_cajas_movimientos');

        DB::beginTransaction();

        try {
            $movimiento = MovimientoCaja::findOrFail($id);
            if ($movimiento->estado != 'PE') {
                return back()->with('error-message', 'El movimiento de caja no se puede eliminar. Su estado es diferente a pendiente.');
            }
            if ($movimiento->tipo_movimiento_id == 14) {
                return back()->with('error-message', 'El movimiento de banco no se puede eliminar. Éste proviene de una venta/compra.');
            }
            $movimiento->delete();

            DB::commit();

            return redirect()->route('movimientos_cajas.index')->with('success-message','El movimiento de caja fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('movimientos_cajas.index')->with('error-message', 'El movimiento de caja no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('movimientos_cajas.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
