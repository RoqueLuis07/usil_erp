<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\Models\MovimientoBanco;
use App\Models\CuentaBancaria;
use App\Models\TipoMovimiento;
use App\Models\Cotizacion;
use App\Models\AsientoContable;
use App\Models\AsientoContableDetalle;
use App\Models\SaldoCuentaContable;
use App\Models\SaldoCuentaContableDetalle;

class MovimientoBancoController extends Controller
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
        $this->authorize('ver_movimientos_cuentas');

        try {
            $movimientos_bancos = MovimientoBanco::orderBy('fecha', 'desc')->get();
            return view('movimientos_bancos/index')->with(compact('movimientos_bancos'));
        } catch (\Exception $e) {
            return redirect()->route('movimientos_bancos.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_movimientos_cuentas');

        try {
            $cuentas_bancarias = CuentaBancaria::with('banco', 'moneda')->where('estado', 'AC')->get();
            $tipos_movimientos = TipoMovimiento::where('tipo', 'BA')->where('estado', 'AC')->orderBy('id', 'asc')->get();
            return view('movimientos_bancos/create')->with(compact('cuentas_bancarias', 'tipos_movimientos'));
        } catch (\Exception $e) {
            return redirect()->route('movimientos_bancos.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_movimientos_cuentas');

        if ($request->cuenta_bancaria_origen) {
            $monto_cuenta_bancaria_origen = CuentaBancaria::findOrFail($request->cuenta_bancaria_origen)->monto;
        } else {
            $monto_cuenta_bancaria_origen = 99999999999;
        }

        $request->validate([
            'tipo_movimiento' => ['required', 'numeric'],
            'cuenta_bancaria_origen' => ['nullable', 'numeric', 'required_if:tipo_movimiento,5', 'required_if:tipo_movimiento,6'],
            'cuenta_bancaria_destino' => ['nullable', 'numeric', 'required_if:tipo_movimiento,4', 'required_if:tipo_movimiento,6'],
            'monto' => ['required', 'numeric', 'max:' . $monto_cuenta_bancaria_origen],
            'motivo' => 'required'
        ]);

        DB::beginTransaction();

        try {
            $movimiento = new MovimientoBanco();
            $movimiento->fecha = Carbon::now();
            $movimiento->cuenta_bancaria_origen_id = $request->cuenta_bancaria_origen;
            $movimiento->cuenta_bancaria_destino_id = $request->cuenta_bancaria_destino;
            if ($request->tipo_movimiento == 5) {
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

            return redirect()->route('movimientos_bancos.index')->with('success-message', 'El movimiento de banco fue creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('movimientos_bancos.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_movimientos_cuentas');

        try {
            $movimiento = MovimientoBanco::findOrFail($id);
            $cuentas_bancarias = CuentaBancaria::with('banco', 'moneda')->where('estado', 'AC')->get();
            $tipos_movimientos = TipoMovimiento::where('tipo', 'BA')->where('estado', 'AC')->orderBy('id', 'asc')->get();
            return view('movimientos_bancos/edit')->with(compact('movimiento', 'cuentas_bancarias', 'tipos_movimientos'));
        } catch (\Exception $e) {
            return redirect()->route('movimientos_bancos.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_movimientos_cuentas');

        if ($request->cuenta_bancaria_origen) {
            $monto_cuenta_bancaria_origen = CuentaBancaria::findOrFail($request->cuenta_bancaria_origen)->monto;
        } else {
            $monto_cuenta_bancaria_origen = 99999999999;
        }

        $request->validate([
            'tipo_movimiento' => ['required', 'numeric'],
            'cuenta_bancaria_origen' => ['nullable', 'numeric', 'required_if:tipo_movimiento,5', 'required_if:tipo_movimiento,6'],
            'cuenta_bancaria_destino' => ['nullable', 'numeric', 'required_if:tipo_movimiento,4', 'required_if:tipo_movimiento,6'],
            'monto' => ['required', 'numeric', 'max:' . $monto_cuenta_bancaria_origen],
            'motivo' => 'required'
        ]);

        DB::beginTransaction();

        try {
            $movimiento = MovimientoBanco::findOrFail($id);
            $movimiento->cuenta_bancaria_origen_id = $request->cuenta_bancaria_origen;
            $movimiento->cuenta_bancaria_destino_id = $request->cuenta_bancaria_destino;
            if ($request->tipo_movimiento == 5) {
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

            return redirect()->route('movimientos_bancos.index')->with('success-message', 'El movimiento de banco fue actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('movimientos_bancos.index')->with('error-message', $e->getMessage());
        }
    }

    public function approve($id)
    {
        $this->authorize('aprobar_movimientos_cuentas');

        DB::beginTransaction();

        try {
            $movimiento = MovimientoBanco::findOrFail($id);
            $movimiento->aprobado_por_id = Auth::id();
            $movimiento->estado = 'AP';
            $movimiento->save();

            $old_asiento_contable = AsientoContable::orderBy('id', 'desc')->first();
            if ($old_asiento_contable) {
                $numero_asiento = $old_asiento_contable->numero + 1;
            } else {
                $numero_asiento = 1;
            }

            if ($movimiento->tipo_movimiento_id == 4) {
                $cuenta_bancaria = CuentaBancaria::findOrFail($movimiento->cuenta_bancaria_destino_id);
                $cuenta_bancaria->monto = $cuenta_bancaria->monto + $movimiento->monto;
                $cuenta_bancaria->save();

                $asiento = new AsientoContable();
                $asiento->numero = $numero_asiento;
                $asiento->fecha = Carbon::now();
                $asiento->origen = 'MOV. CTA. BANCARIA';
                $asiento->moneda_id = 1;
                $asiento->movimiento_banco_id = $movimiento->id;
                if ($cuenta_bancaria->moneda_id == 2) {
                    $cotizacion = Cotizacion::orderBy('id', 'desc')->first();
                    if (!$cotizacion) {
                        return back()->with('error-message', 'El movimiento no puede ser aprobado. No existe una cotización cargada.');
                    } else {
                        $asiento->cotizacion_id = $cotizacion->id;
                        $monto = $movimiento->monto * $cotizacion->precio_venta;
                    }
                } else {
                    $monto = $movimiento->monto;
                }
                $asiento->unidad_negocio_id = 1; //aca va la unidad de negocio NINGUNO
                $asiento->subunidad_negocio_id = 1; //aca va la subunidad de negocio NINGUNO
                $asiento->save();

                $asiento_detalle_origen = new AsientoContableDetalle();
                $asiento_detalle_origen->asiento_id = $asiento->id;
                $asiento_detalle_origen->cuenta_contable_id = 263; //verificar luego para saber de donde entra el monto del movimiento
                $asiento_detalle_origen->descripcion = 'MOV. CTA. BANCARIA DE ENTRADA N°: ' . $movimiento->id;
                $asiento_detalle_origen->haber = $monto;
                $asiento_detalle_origen->centro_costo_id = 1; //aca va el centro de costo NINGUNO
                $asiento_detalle_origen->subcentro_costo_id = 1; //aca va el subcentro de costo NINGUNO
                $asiento_detalle_origen->save();

                $asiento_detalle_destino = new AsientoContableDetalle();
                $asiento_detalle_destino->asiento_id = $asiento->id;
                $asiento_detalle_destino->cuenta_contable_id = $cuenta_bancaria->cuenta_egreso_id;
                $asiento_detalle_destino->descripcion = 'MOV. CTA. BANCARIA DE ENTRADA N°: ' . $movimiento->id;
                $asiento_detalle_destino->debe = $monto;
                $asiento_detalle_destino->centro_costo_id = 1; //aca va el centro de costo NINGUNO
                $asiento_detalle_destino->subcentro_costo_id = 1; //aca va el subcentro de costo NINGUNO
                $asiento_detalle_destino->save();


                $saldo_cuenta_origen = SaldoCuentaContable::where('cuenta_contable_id', 120)->first(); //verificar luego para saber de donde entra el monto del movimiento
                $saldo_cuenta_destino = SaldoCuentaContable::where('cuenta_contable_id', $cuenta_bancaria->cuenta_egreso_id)->first();
                if (!$saldo_cuenta_origen || !$saldo_cuenta_destino) {
                    return back()->with('error-message', 'Hubo un problema al aprobar el movimiento. La cuenta contable de la cuenta bancaria no se encuentra seleccionada.');
                } else {
                    $saldo_cuenta_origen->saldo = $saldo_cuenta_origen->saldo - $monto;
                    $saldo_cuenta_origen->save();

                    $saldo_cuenta_origen_detalle = new SaldoCuentaContableDetalle();
                    $saldo_cuenta_origen_detalle->saldo_cuenta_contable_id = $saldo_cuenta_origen->id;
                    $saldo_cuenta_origen_detalle->mes = Carbon::today()->format('m');
                    $saldo_cuenta_origen_detalle->anho = Carbon::today()->format('Y');
                    $saldo_cuenta_origen_detalle->haber = $monto;
                    $saldo_cuenta_origen_detalle->movimiento_banco_id = $movimiento->id;
                    $saldo_cuenta_origen_detalle->save();

                    $saldo_cuenta_destino->saldo = $saldo_cuenta_destino->saldo + $monto;
                    $saldo_cuenta_destino->save();

                    $saldo_cuenta_destino_detalle = new SaldoCuentaContableDetalle();
                    $saldo_cuenta_destino_detalle->saldo_cuenta_contable_id = $saldo_cuenta_destino->id;
                    $saldo_cuenta_destino_detalle->mes = Carbon::today()->format('m');
                    $saldo_cuenta_destino_detalle->anho = Carbon::today()->format('Y');
                    $saldo_cuenta_destino_detalle->debe = $monto;
                    $saldo_cuenta_destino_detalle->movimiento_banco_id = $movimiento->id;
                    $saldo_cuenta_destino_detalle->save();
                }

            } else if ($movimiento->tipo_movimiento_id == 5) {
                $cuenta_bancaria = CuentaBancaria::findOrFail($movimiento->cuenta_bancaria_origen_id);
                $cuenta_bancaria->monto = $cuenta_bancaria->monto - $movimiento->monto;
                $cuenta_bancaria->save();

                $asiento = new AsientoContable();
                $asiento->numero = $numero_asiento;
                $asiento->fecha = Carbon::now();
                $asiento->origen = 'MOV. CTA. BANCARIA';
                $asiento->moneda_id = 1;
                $asiento->movimiento_banco_id = $movimiento->id;
                if ($cuenta_bancaria->moneda_id == 2) {
                    $cotizacion = Cotizacion::orderBy('id', 'desc')->first();
                    if (!$cotizacion) {
                        return back()->with('error-message', 'El movimiento no puede ser aprobado. No existe una cotización cargada.');
                    } else {
                        $asiento->cotizacion_id = $cotizacion->id;
                        $monto = $movimiento->monto * $cotizacion->precio_venta;
                    }
                } else {
                    $monto = $movimiento->monto;
                }
                $asiento->unidad_negocio_id = 1; //aca va la unidad de negocio NINGUNO
                $asiento->subunidad_negocio_id = 1; //aca va la subunidad de negocio NINGUNO
                $asiento->save();

                $asiento_detalle_origen = new AsientoContableDetalle();
                $asiento_detalle_origen->asiento_id = $asiento->id;
                $asiento_detalle_origen->cuenta_contable_id = $cuenta_bancaria->cuenta_egreso_id;
                $asiento_detalle_origen->descripcion = 'MOV. CTA. BANCARIA DE SALIDA N°: ' . $movimiento->id;
                $asiento_detalle_origen->haber = $monto;
                $asiento_detalle_origen->centro_costo_id = 1; //aca va el centro de costo NINGUNO
                $asiento_detalle_origen->subcentro_costo_id = 1; //aca va el subcentro de costo NINGUNO
                $asiento_detalle_origen->save();

                $asiento_detalle_destino = new AsientoContableDetalle();
                $asiento_detalle_destino->asiento_id = $asiento->id;
                $asiento_detalle_destino->cuenta_contable_id = 91; //verificar luego para saber de donde entra el monto del movimiento
                $asiento_detalle_destino->descripcion = 'MOV. CTA. BANCARIA DE ENTRADA N°: ' . $movimiento->id;
                $asiento_detalle_destino->debe = $monto;
                $asiento_detalle_destino->centro_costo_id = 1; //aca va el centro de costo NINGUNO
                $asiento_detalle_destino->subcentro_costo_id = 1; //aca va el subcentro de costo NINGUNO
                $asiento_detalle_destino->save();

                $saldo_cuenta_origen = SaldoCuentaContable::where('cuenta_contable_id', $cuenta_bancaria->cuenta_egreso_id)->first();
                $saldo_cuenta_destino = SaldoCuentaContable::where('cuenta_contable_id', 70)->first(); //verificar luego para saber de donde entra el monto del movimiento
                if (!$saldo_cuenta_origen || !$saldo_cuenta_destino) {
                    return back()->with('error-message', 'Hubo un problema al aprobar el movimiento. La cuenta contable de la cuenta bancaria no se encuentra seleccionada.');
                } else {
                    $saldo_cuenta_origen->saldo = $saldo_cuenta_origen->saldo - $monto;
                    $saldo_cuenta_origen->save();

                    $saldo_cuenta_origen_detalle = new SaldoCuentaContableDetalle();
                    $saldo_cuenta_origen_detalle->saldo_cuenta_contable_id = $saldo_cuenta_origen->id;
                    $saldo_cuenta_origen_detalle->mes = Carbon::today()->format('m');
                    $saldo_cuenta_origen_detalle->anho = Carbon::today()->format('Y');
                    $saldo_cuenta_origen_detalle->haber = $monto;
                    $saldo_cuenta_origen_detalle->movimiento_banco_id = $movimiento->id;
                    $saldo_cuenta_origen_detalle->save();

                    $saldo_cuenta_destino_detalle = new SaldoCuentaContableDetalle();
                    $saldo_cuenta_destino_detalle->saldo_cuenta_contable_id = $saldo_cuenta_destino->id;
                    $saldo_cuenta_destino_detalle->mes = Carbon::today()->format('m');
                    $saldo_cuenta_destino_detalle->anho = Carbon::today()->format('Y');
                    $saldo_cuenta_destino_detalle->debe = $monto;
                    $saldo_cuenta_destino_detalle->movimiento_banco_id = $movimiento->id;
                    $saldo_cuenta_destino_detalle->save();
                }

            } else if ($movimiento->tipo_movimiento_id == 6) {
                $cuenta_bancaria_origen = CuentaBancaria::findOrFail($movimiento->cuenta_bancaria_origen_id);
                $cuenta_bancaria_destino = CuentaBancaria::findOrFail($movimiento->cuenta_bancaria_destino_id);

                if ($cuenta_bancaria_origen->moneda_id == $cuenta_bancaria_destino->moneda_id) {
                    $cuenta_bancaria_origen->monto = $cuenta_bancaria_origen->monto - $movimiento->monto;
                    $cuenta_bancaria_destino->monto = $cuenta_bancaria_destino->monto + $movimiento->monto;

                    $asiento = new AsientoContable();
                    $asiento->numero = $numero_asiento;
                    $asiento->fecha = Carbon::now();
                    $asiento->origen = 'MOV. CTA. BANCARIA';
                    $asiento->moneda_id = 1;
                    $asiento->movimiento_banco_id = $movimiento->id;
                    $asiento->unidad_negocio_id = 1; //aca va la unidad de negocio NINGUNO
                    $asiento->subunidad_negocio_id = 1; //aca va la subunidad de negocio NINGUNO
                    $asiento->save();

                    $asiento_detalle_origen = new AsientoContableDetalle();
                    $asiento_detalle_origen->asiento_id = $asiento->id;
                    $asiento_detalle_origen->cuenta_contable_id = $cuenta_bancaria_origen->cuenta_egreso_id;
                    $asiento_detalle_origen->descripcion = 'MOV. CTA. BANCARIA ENTRE CUENTAS N°: ' . $movimiento->id;
                    $asiento_detalle_origen->haber = $movimiento->monto;
                    $asiento_detalle_origen->centro_costo_id = 1; //aca va el centro de costo NINGUNO
                    $asiento_detalle_origen->subcentro_costo_id = 1; //aca va el subcentro de costo NINGUNO
                    $asiento_detalle_origen->save();

                    $asiento_detalle_destino = new AsientoContableDetalle();
                    $asiento_detalle_destino->asiento_id = $asiento->id;
                    $asiento_detalle_destino->cuenta_contable_id = $cuenta_bancaria_destino->cuenta_egreso_id;
                    $asiento_detalle_destino->descripcion = 'MOV. CTA. BANCARIA ENTRE CUENTAS N°: ' . $movimiento->id;
                    $asiento_detalle_destino->debe = $movimiento->monto;
                    $asiento_detalle_destino->centro_costo_id = 1; //aca va el centro de costo NINGUNO
                    $asiento_detalle_destino->subcentro_costo_id = 1; //aca va el subcentro de costo NINGUNO
                    $asiento_detalle_destino->save();

                    $saldo_cuenta_origen = SaldoCuentaContable::where('cuenta_contable_id', $cuenta_bancaria_origen->cuenta_egreso_id)->first();
                    $saldo_cuenta_destino = SaldoCuentaContable::where('cuenta_contable_id', $cuenta_bancaria_destino->cuenta_egreso_id)->first();
                    if (!$saldo_cuenta_origen || !$saldo_cuenta_destino) {
                        return back()->with('error-message', 'Hubo un problema al aprobar el movimiento. La cuenta contable de la cuenta bancaria no se encuentra seleccionada.');
                    } else {
                        $saldo_cuenta_origen->saldo = $saldo_cuenta_origen->saldo - $movimiento->monto;
                        $saldo_cuenta_origen->save();

                        $saldo_cuenta_origen_detalle = new SaldoCuentaContableDetalle();
                        $saldo_cuenta_origen_detalle->saldo_cuenta_contable_id = $saldo_cuenta_origen->id;
                        $saldo_cuenta_origen_detalle->mes = Carbon::today()->format('m');
                        $saldo_cuenta_origen_detalle->anho = Carbon::today()->format('Y');
                        $saldo_cuenta_origen_detalle->haber = $movimiento->monto;
                        $saldo_cuenta_origen_detalle->movimiento_banco_id = $movimiento->id;
                        $saldo_cuenta_origen_detalle->save();

                        $saldo_cuenta_destino->saldo = $saldo_cuenta_destino->saldo + $movimiento->monto;
                        $saldo_cuenta_destino->save();

                        $saldo_cuenta_destino_detalle = new SaldoCuentaContableDetalle();
                        $saldo_cuenta_destino_detalle->saldo_cuenta_contable_id = $saldo_cuenta_destino->id;
                        $saldo_cuenta_destino_detalle->mes = Carbon::today()->format('m');
                        $saldo_cuenta_destino_detalle->anho = Carbon::today()->format('Y');
                        $saldo_cuenta_destino_detalle->debe = $movimiento->monto;
                        $saldo_cuenta_destino_detalle->movimiento_banco_id = $movimiento->id;
                        $saldo_cuenta_destino_detalle->save();
                    }

                } else if ($cuenta_bancaria_origen->moneda_id == 1 && $cuenta_bancaria_destino->moneda_id == 2) {
                    $cotizacion = Cotizacion::orderBy('id', 'desc')->first();
                    if (!$cotizacion) {
                        return back()->with('error-message', 'El movimiento no puede ser aprobado. No existe una cotización cargada.');
                    } else {
                        $cuenta_bancaria_origen->monto = $cuenta_bancaria_origen->monto - $movimiento->monto;
                        $monto = $movimiento->monto / $cotizacion->precio_venta;
                        $cuenta_bancaria_destino->monto = $cuenta_bancaria_destino->monto + $monto;

                        $asiento = new AsientoContable();
                        $asiento->numero = $numero_asiento;
                        $asiento->fecha = Carbon::now();
                        $asiento->origen = 'MOV. CTA. BANCARIA';
                        $asiento->moneda_id = 1;
                        $asiento->cotizacion_id = $cotizacion->id;
                        $asiento->movimiento_banco_id = $movimiento->id;
                        $asiento->unidad_negocio_id = 1; //aca va la unidad de negocio NINGUNO
                        $asiento->subunidad_negocio_id = 1; //aca va la subunidad de negocio NINGUNO
                        $asiento->save();

                        $asiento_detalle_origen = new AsientoContableDetalle();
                        $asiento_detalle_origen->asiento_id = $asiento->id;
                        $asiento_detalle_origen->cuenta_contable_id = $cuenta_bancaria_origen->cuenta_egreso_id;
                        $asiento_detalle_origen->descripcion = 'MOV. CTA. BANCARIA ENTRE CUENTAS N°: ' . $movimiento->id;
                        $asiento_detalle_origen->haber = $monto;
                        $asiento_detalle_origen->centro_costo_id = 1; //aca va el centro de costo NINGUNO
                        $asiento_detalle_origen->subcentro_costo_id = 1; //aca va el subcentro de costo NINGUNO
                        $asiento_detalle_origen->save();

                        $asiento_detalle_destino = new AsientoContableDetalle();
                        $asiento_detalle_destino->asiento_id = $asiento->id;
                        $asiento_detalle_destino->cuenta_contable_id = $cuenta_bancaria_destino->cuenta_egreso_id;
                        $asiento_detalle_destino->descripcion = 'MOV. CTA. BANCARIA ENTRE CUENTAS N°: ' . $movimiento->id;
                        $asiento_detalle_destino->debe = $monto;
                        $asiento_detalle_destino->centro_costo_id = 1; //aca va el centro de costo NINGUNO
                        $asiento_detalle_destino->subcentro_costo_id = 1; //aca va el subcentro de costo NINGUNO
                        $asiento_detalle_destino->save();

                        $saldo_cuenta_origen = SaldoCuentaContable::where('cuenta_contable_id', $cuenta_bancaria_origen->cuenta_egreso_id)->first();
                        $saldo_cuenta_destino = SaldoCuentaContable::where('cuenta_contable_id', $cuenta_bancaria_destino->cuenta_egreso_id)->first();
                        if (!$saldo_cuenta_origen || !$saldo_cuenta_destino) {
                            return back()->with('error-message', 'Hubo un problema al aprobar el movimiento. La cuenta contable de la cuenta bancaria no se encuentra seleccionada.');
                        } else {
                            $saldo_cuenta_origen->saldo = $saldo_cuenta_origen->saldo - $monto;
                            $saldo_cuenta_origen->save();

                            $saldo_cuenta_origen_detalle = new SaldoCuentaContableDetalle();
                            $saldo_cuenta_origen_detalle->saldo_cuenta_contable_id = $saldo_cuenta_origen->id;
                            $saldo_cuenta_origen_detalle->mes = Carbon::today()->format('m');
                            $saldo_cuenta_origen_detalle->anho = Carbon::today()->format('Y');
                            $saldo_cuenta_origen_detalle->haber = $monto;
                            $saldo_cuenta_origen_detalle->movimiento_banco_id = $movimiento->id;
                            $saldo_cuenta_origen_detalle->save();

                            $saldo_cuenta_destino->saldo = $saldo_cuenta_destino->saldo + $monto;
                            $saldo_cuenta_destino->save();

                            $saldo_cuenta_destino_detalle = new SaldoCuentaContableDetalle();
                            $saldo_cuenta_destino_detalle->saldo_cuenta_contable_id = $saldo_cuenta_destino->id;
                            $saldo_cuenta_destino_detalle->mes = Carbon::today()->format('m');
                            $saldo_cuenta_destino_detalle->anho = Carbon::today()->format('Y');
                            $saldo_cuenta_destino_detalle->debe = $monto;
                            $saldo_cuenta_destino_detalle->movimiento_banco_id = $movimiento->id;
                            $saldo_cuenta_destino_detalle->save();
                        }
                    }

                } else if ($cuenta_bancaria_origen->moneda_id == 2 && $cuenta_bancaria_destino->moneda_id == 1) {
                    $cotizacion = Cotizacion::orderBy('id', 'desc')->first();
                    if (!$cotizacion) {
                        return back()->with('error-message', 'El movimiento no puede ser aprobado. No existe una cotización cargada.');
                    } else {
                        $cuenta_bancaria_origen->monto = $cuenta_bancaria_origen->monto - $movimiento->monto;
                        $monto = $movimiento->monto * $cotizacion->precio_venta;
                        $cuenta_bancaria_destino->monto = $cuenta_bancaria_destino->monto + $monto;

                        $asiento = new AsientoContable();
                        $asiento->numero = $numero_asiento;
                        $asiento->fecha = Carbon::now();
                        $asiento->origen = 'MOV. CTA. BANCARIA';
                        $asiento->moneda_id = 1;
                        $asiento->cotizacion_id = $cotizacion->id;
                        $asiento->movimiento_banco_id = $movimiento->id;
                        $asiento->unidad_negocio_id = 1; //aca va la unidad de negocio NINGUNO
                        $asiento->subunidad_negocio_id = 1; //aca va la subunidad de negocio NINGUNO
                        $asiento->save();

                        $asiento_detalle_origen = new AsientoContableDetalle();
                        $asiento_detalle_origen->asiento_id = $asiento->id;
                        $asiento_detalle_origen->cuenta_contable_id = $cuenta_bancaria_origen->cuenta_egreso_id;
                        $asiento_detalle_origen->descripcion = 'MOV. CTA. BANCARIA ENTRE CUENTAS N°: ' . $movimiento->id;
                        $asiento_detalle_origen->haber = $monto;
                        $asiento_detalle_origen->centro_costo_id = 1; //aca va el centro de costo NINGUNO
                        $asiento_detalle_origen->subcentro_costo_id = 1; //aca va el subcentro de costo NINGUNO
                        $asiento_detalle_origen->save();

                        $asiento_detalle_destino = new AsientoContableDetalle();
                        $asiento_detalle_destino->asiento_id = $asiento->id;
                        $asiento_detalle_destino->cuenta_contable_id = $cuenta_bancaria_destino->cuenta_egreso_id;
                        $asiento_detalle_destino->descripcion = 'MOV. CTA. BANCARIA ENTRE CUENTAS N°: ' . $movimiento->id;
                        $asiento_detalle_destino->debe = $monto;
                        $asiento_detalle_destino->centro_costo_id = 1; //aca va el centro de costo NINGUNO
                        $asiento_detalle_destino->subcentro_costo_id = 1; //aca va el subcentro de costo NINGUNO
                        $asiento_detalle_destino->save();

                        $saldo_cuenta_origen = SaldoCuentaContable::where('cuenta_contable_id', $cuenta_bancaria_origen->cuenta_egreso_id)->first();
                        $saldo_cuenta_destino = SaldoCuentaContable::where('cuenta_contable_id', $cuenta_bancaria_destino->cuenta_egreso_id)->first();
                        if (!$saldo_cuenta_origen || !$saldo_cuenta_destino) {
                            return back()->with('error-message', 'Hubo un problema al aprobar el movimiento. La cuenta contable de la cuenta bancaria no se encuentra seleccionada.');
                        } else {
                            $saldo_cuenta_origen->saldo = $saldo_cuenta_origen->saldo - $monto;
                            $saldo_cuenta_origen->save();

                            $saldo_cuenta_origen_detalle = new SaldoCuentaContableDetalle();
                            $saldo_cuenta_origen_detalle->saldo_cuenta_contable_id = $saldo_cuenta_origen->id;
                            $saldo_cuenta_origen_detalle->mes = Carbon::today()->format('m');
                            $saldo_cuenta_origen_detalle->anho = Carbon::today()->format('Y');
                            $saldo_cuenta_origen_detalle->haber = $monto;
                            $saldo_cuenta_origen_detalle->movimiento_banco_id = $movimiento->id;
                            $saldo_cuenta_origen_detalle->save();

                            $saldo_cuenta_destino->saldo = $saldo_cuenta_destino->saldo + $monto;
                            $saldo_cuenta_destino->save();

                            $saldo_cuenta_destino_detalle = new SaldoCuentaContableDetalle();
                            $saldo_cuenta_destino_detalle->saldo_cuenta_contable_id = $saldo_cuenta_destino->id;
                            $saldo_cuenta_destino_detalle->mes = Carbon::today()->format('m');
                            $saldo_cuenta_destino_detalle->anho = Carbon::today()->format('Y');
                            $saldo_cuenta_destino_detalle->debe = $monto;
                            $saldo_cuenta_destino_detalle->movimiento_banco_id = $movimiento->id;
                            $saldo_cuenta_destino_detalle->save();
                        }
                    }
                }
                $cuenta_bancaria_origen->save();
                $cuenta_bancaria_destino->save();

            } else if ($movimiento->tipo_movimiento_id == 14) {
                $cuenta_bancaria_destino = CuentaBancaria::findOrFail($movimiento->cuenta_bancaria_destino_id);
                $cuenta_bancaria_destino->monto = $cuenta_bancaria_destino->monto + $movimiento->monto;
                $cuenta_bancaria_destino->save();

                $asiento = new AsientoContable();
                $asiento->numero = $numero_asiento;
                $asiento->fecha = Carbon::now();
                $asiento->origen = 'MOV. CTA. BANCARIA';
                $asiento->moneda_id = 1;
                $asiento->movimiento_banco_id = $movimiento->id;
                $asiento->unidad_negocio_id = 1; //aca va la unidad de negocio NINGUNO
                $asiento->subunidad_negocio_id = 1; //aca va la subunidad de negocio NINGUNO
                $asiento->save();

                $asiento_detalle_origen = new AsientoContableDetalle();
                $asiento_detalle_origen->asiento_id = $asiento->id;
                $asiento_detalle_origen->cuenta_contable_id = $cuenta_bancaria_destino->cuenta_ingreso_id;
                $asiento_detalle_origen->descripcion = 'MOV. CTA. BANCARIA DE VENTA N°: ' . $movimiento->id;
                $asiento_detalle_origen->haber = $movimiento->monto;
                $asiento_detalle_origen->centro_costo_id = 1; //aca va el centro de costo NINGUNO
                $asiento_detalle_origen->subcentro_costo_id = 1; //aca va el subcentro de costo NINGUNO
                $asiento_detalle_origen->save();

                $asiento_detalle_destino = new AsientoContableDetalle();
                $asiento_detalle_destino->asiento_id = $asiento->id;
                $asiento_detalle_destino->cuenta_contable_id = $cuenta_bancaria_destino->cuenta_egreso_id;
                $asiento_detalle_destino->descripcion = 'MOV. CTA. BANCARIA DE VENTA N°: ' . $movimiento->id;
                $asiento_detalle_destino->debe = $movimiento->monto;
                $asiento_detalle_destino->centro_costo_id = 1; //aca va el centro de costo NINGUNO
                $asiento_detalle_destino->subcentro_costo_id = 1; //aca va el subcentro de costo NINGUNO
                $asiento_detalle_destino->save();

                $saldo_cuenta_origen = SaldoCuentaContable::where('cuenta_contable_id', $cuenta_bancaria_destino->cuenta_ingreso_id)->first();
                $saldo_cuenta_destino = SaldoCuentaContable::where('cuenta_contable_id', $cuenta_bancaria_destino->cuenta_egreso_id)->first();
                if (!$saldo_cuenta_origen || !$saldo_cuenta_destino) {
                    return back()->with('error-message', 'Hubo un problema al aprobar el movimiento. La cuenta contable de la cuenta bancaria no se encuentra seleccionada.');
                } else {
                    $saldo_cuenta_origen->saldo = $saldo_cuenta_origen->saldo - $movimiento->monto;
                    $saldo_cuenta_origen->save();

                    $saldo_cuenta_origen_detalle = new SaldoCuentaContableDetalle();
                    $saldo_cuenta_origen_detalle->saldo_cuenta_contable_id = $saldo_cuenta_origen->id;
                    $saldo_cuenta_origen_detalle->mes = Carbon::today()->format('m');
                    $saldo_cuenta_origen_detalle->anho = Carbon::today()->format('Y');
                    $saldo_cuenta_origen_detalle->haber = $movimiento->monto;
                    $saldo_cuenta_origen_detalle->movimiento_banco_id = $movimiento->id;
                    $saldo_cuenta_origen_detalle->save();

                    $saldo_cuenta_destino->saldo = $saldo_cuenta_destino->saldo + $movimiento->monto;
                    $saldo_cuenta_destino->save();

                    $saldo_cuenta_destino_detalle = new SaldoCuentaContableDetalle();
                    $saldo_cuenta_destino_detalle->saldo_cuenta_contable_id = $saldo_cuenta_destino->id;
                    $saldo_cuenta_destino_detalle->mes = Carbon::today()->format('m');
                    $saldo_cuenta_destino_detalle->anho = Carbon::today()->format('Y');
                    $saldo_cuenta_destino_detalle->debe = $movimiento->monto;
                    $saldo_cuenta_destino_detalle->movimiento_banco_id = $movimiento->id;
                    $saldo_cuenta_destino_detalle->save();
                }
            }

            DB::commit();

            return redirect()->route('movimientos_bancos.index')->with('success-message','El movimiento de banco fue aprobado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('movimientos_bancos.index')->with('error-message', $e->getMessage());
        }
    }

    public function unapprove($id)
    {
        $this->authorize('anular_aprobacion_movimientos_cuentas');

        DB::beginTransaction();

        try {
            $movimiento = MovimientoBanco::findOrFail($id);
            $movimiento->aprobado_por_id = null;
            $movimiento->estado = 'PE';
            $movimiento->save();

            if ($movimiento->tipo_movimiento_id == 4) {
                $cuenta_bancaria = CuentaBancaria::findOrFail($movimiento->cuenta_bancaria_destino_id);
                $cuenta_bancaria->monto = $cuenta_bancaria->monto - $movimiento->monto;
                $cuenta_bancaria->save();

                $asiento = AsientoContable::where('movimiento_banco_id', $movimiento->id)->first();
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

            } else if ($movimiento->tipo_movimiento_id == 5) {
                $cuenta_bancaria = CuentaBancaria::findOrFail($movimiento->cuenta_bancaria_origen_id);
                $cuenta_bancaria->monto = $cuenta_bancaria->monto + $movimiento->monto;
                $cuenta_bancaria->save();

                $asiento = AsientoContable::where('movimiento_banco_id', $movimiento->id)->first();
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

            } else if ($movimiento->tipo_movimiento_id == 6) {
                $cuenta_bancaria_origen = CuentaBancaria::findOrFail($movimiento->cuenta_bancaria_origen_id);
                $cuenta_bancaria_destino = CuentaBancaria::findOrFail($movimiento->cuenta_bancaria_destino_id);
                $cotizacion = Cotizacion::orderBy('id', 'desc')->first();

                if ($cuenta_bancaria_origen->moneda_id == $cuenta_bancaria_destino->moneda_id) {
                    $cuenta_bancaria_origen->monto = $cuenta_bancaria_origen->monto + $movimiento->monto;
                    $cuenta_bancaria_destino->monto = $cuenta_bancaria_destino->monto - $movimiento->monto;
                } else if ($cuenta_bancaria_origen->moneda_id == 1 && $cuenta_bancaria_destino->moneda_id == 2) {
                    $cuenta_bancaria_origen->monto = $cuenta_bancaria_origen->monto + $movimiento->monto;
                    $monto = $movimiento->monto / $cotizacion->precio_venta;
                    $cuenta_bancaria_destino->monto = $cuenta_bancaria_destino->monto - $monto;
                } else if ($cuenta_bancaria_origen->moneda_id == 2 && $cuenta_bancaria_destino->moneda_id == 1) {
                    $cuenta_bancaria_origen->monto = $cuenta_bancaria_origen->monto + $movimiento->monto;
                    $monto = $movimiento->monto * $cotizacion->precio_venta;
                    $cuenta_bancaria_destino->monto = $cuenta_bancaria_destino->monto - $monto;
                }
                $cuenta_bancaria_origen->save();
                $cuenta_bancaria_destino->save();

                $asiento = AsientoContable::where('movimiento_banco_id', $movimiento->id)->first();
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

            } else if ($movimiento->tipo_movimiento_id == 14) {
                $cuenta_bancaria_destino = CuentaBancaria::findOrFail($movimiento->cuenta_bancaria_destino_id);
                $cuenta_bancaria_destino->monto = $cuenta_bancaria_destino->monto - $movimiento->monto;
                $cuenta_bancaria_destino->save();

                $asiento = AsientoContable::where('movimiento_banco_id', $movimiento->id)->first();
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
            }

            DB::commit();

            return redirect()->route('movimientos_bancos.index')->with('error-message','La aprobación del movimiento de banco fue anulado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('movimientos_bancos.index')->with('error-message', $e->getMessage());
        }
    }

    public function reject($id)
    {
        $this->authorize('rechazar_movimientos_cuentas');

        DB::beginTransaction();

        try {
            $movimiento = MovimientoBanco::findOrFail($id);
            $movimiento->rechazado_por_id = Auth::id();
            $movimiento->estado = 'RE';
            $movimiento->save();

            DB::commit();

            return redirect()->route('movimientos_bancos.index')->with('error-message','El movimiento de banco fue rechazado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('movimientos_bancos.index')->with('error-message', $e->getMessage());
        }
    }

    public function unreject($id)
    {
        $this->authorize('anular_rechazo_movimientos_cuentas');

        DB::beginTransaction();

        try {
            $movimiento = MovimientoBanco::findOrFail($id);
            $movimiento->rechazado_por_id = null;
            $movimiento->estado = 'PE';
            $movimiento->save();

            DB::commit();

            return redirect()->route('movimientos_bancos.index')->with('success-message','El rechazo del movimiento de banco fue anulado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('movimientos_bancos.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_movimientos_cuentas');

        DB::beginTransaction();

        try {
            $movimiento = MovimientoBanco::findOrFail($id);
            if ($movimiento->estado != 'PE') {
                return back()->with('error-message', 'El movimiento de banco no se puede eliminar. Su estado es diferente a pendiente.');
            }
            if ($movimiento->tipo_movimiento_id == 14) {
                return back()->with('error-message', 'El movimiento de banco no se puede eliminar. Éste proviene de una venta/compra.');
            }

            $movimiento->delete();

            DB::commit();

            return redirect()->route('movimientos_bancos.index')->with('success-message','El movimiento de banco fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('movimientos_bancos.index')->with('error-message', 'El movimiento de banco no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('movimientos_bancos.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
