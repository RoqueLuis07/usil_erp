<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\Models\Recibo;
use App\Models\ReciboDetalle;
use App\Models\Venta;
use App\Models\Caja;
use App\Models\UsuarioCaja;
use App\Models\Cliente;
use App\Models\FormaPago;
use App\Models\Articulo;
use App\Models\Banco;
use App\Models\CuentaBancaria;
use App\Models\Cobro;
use App\Models\MovimientoCaja;
use App\Models\MovimientoBanco;
use App\Models\AsientoContable;
use App\Models\AsientoContableDetalle;
use App\Models\CuentaContable;
use App\Models\SaldoCuentaContable;
use App\Models\SaldoCuentaContableDetalle;
use App\Models\Cotizacion;
use App\Models\UnidadNegocioContable;
use App\Models\CentroCostoContable;
use App\Models\SubunidadNegocioContable;
use App\Models\SubcentroCostoContable;

class ReciboController extends Controller
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
        $this->authorize('ver_recibos');

        try {
            $recibos = Recibo::orderBy('id', 'desc')->get();
            return view('recibos/index')->with(compact('recibos'));
        } catch (\Exception $e) {
            return redirect()->route('recibos.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_recibos');

        try {
            $recibo = Recibo::with('detalles')->findOrFail($id);
            $recibo->fecha = Carbon::parse($recibo->fecha)->format('d/m/Y H:i');
            $recibo->numero = str_pad($recibo->numero, 7, '0', STR_PAD_LEFT);
            return view('recibos/show')->with(compact('recibo'));
        } catch (\Exception $e) {
            return redirect()->route('recibos.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_recibos');

        try {
            $fecha_hoy = Carbon::today()->format('d/m/Y');

            $ultimo_recibo = Recibo::orderBy('id', 'desc')->first();
            if ($ultimo_recibo) {
                $numero = $ultimo_recibo->numero;
                $numero_recibo = str_pad($ultimo_recibo->numero, 7, '0', STR_PAD_LEFT);
            } else {
                $numero = 1;
                $numero_recibo = '0000001';
            }

            $caja = UsuarioCaja::where('usuario_id', Auth::id())->first();
            if (!$caja) {
                return redirect()->route('recibos.index')->with('error-message', 'Usted no cuenta con una caja registrada a su nombre para realizar el recibo.');
            }

            $clientes = Cliente::where('estado', 'AC')->get();
            $formas_pagos = FormaPago::where('id', '!=', 6)->where('estado', 'AC')->orderBy('id', 'asc')->get();
            $bancos = Banco::where('estado', 'AC')->get();
            $cuentas_bancarias = CuentaBancaria::where('estado', 'AC')->get();

            return view('recibos/create')->with(compact('fecha_hoy', 'numero', 'numero_recibo', 'caja', 'clientes', 'formas_pagos', 'bancos', 'cuentas_bancarias'));
        } catch (\Exception $e) {
            return redirect()->route('recibos.index')->with('error-message', $e->getMessage());
        }
    }

    public function create_unique($id)
    {
        $this->authorize('crear_recibos');

        try {
            $fecha_hoy = Carbon::today()->format('d/m/Y');

            $ultimo_recibo = Recibo::orderBy('id', 'desc')->first();
            if ($ultimo_recibo) {
                $numero = $ultimo_recibo->numero;
                $numero_recibo = str_pad($ultimo_recibo->numero, 7, '0', STR_PAD_LEFT);
            } else {
                $numero = 1;
                $numero_recibo = '0000001';
            }

            $caja = UsuarioCaja::where('usuario_id', Auth::id())->first();
            if (!$caja) {
                return redirect()->route('recibos.index')->with('error-message', 'Usted no cuenta con una caja registrada a su nombre para realizar el recibo.');
            }

            $venta = Venta::findOrFail($id);
            $formas_pagos = FormaPago::where('estado', 'AC')->get();
            $bancos = Banco::where('estado', 'AC')->get();
            $cuentas_bancarias = CuentaBancaria::where('estado', 'AC')->get();

            return view('recibos/create_unique')->with(compact('fecha_hoy', 'numero', 'numero_recibo', 'caja', 'venta', 'formas_pagos', 'bancos', 'cuentas_bancarias'));
        } catch (\Exception $e) {
            return redirect()->route('recibos.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_recibos');

        $request->validate([
            'numero' => ['required', 'numeric'],
            'cliente' => ['required', 'numeric'],
            'forma_pago' => 'required',
            'banco_debito' => ['nullable', 'numeric', 'required_if:forma_pago,2'],
            'numero_transaccion_debito' => ['nullable', 'required_if:forma_pago,2'],
            'fecha_transaccion_debito' => ['nullable', 'date', 'required_if:forma_pago,2'],
            'banco_credito' => ['nullable', 'numeric', 'required_if:forma_pago,3'],
            'numero_transaccion_credito' => ['nullable', 'required_if:forma_pago,3'],
            'fecha_transaccion_credito' => ['nullable', 'date', 'required_if:forma_pago,3'],
            'banco_transferencia' => ['nullable', 'numeric', 'required_if:forma_pago,4'],
            'numero_transaccion_transferencia' => ['nullable', 'required_if:forma_pago,4'],
            'fecha_transaccion_transferencia' => ['nullable', 'date', 'required_if:forma_pago,4'],
            'cuenta_bancaria_transferencia' => ['nullable', 'numeric', 'required_if:forma_pago,4'],
            'numero_transaccion_deposito' => ['nullable', 'required_if:forma_pago,5'],
            'fecha_transaccion_deposito' => ['nullable', 'date', 'required_if:forma_pago,5'],
            'cuenta_bancaria_deposito' => ['nullable', 'numeric', 'required_if:forma_pago,5'],

            'detalles' => ['required', 'array'],
            'detalles.*.venta' => ['required', 'numeric'],
            'detalles.*.monto' => 'required'
        ]);

        DB::beginTransaction();

        try {
            $caja = UsuarioCaja::where('usuario_id', Auth::id())->first();
            if (!$caja) {
                return redirect()->route('recibos.index')->with('error-message', 'Usted no cuenta con una caja registrada a su nombre para realizar la venta.');
            }

            $monto_total = 0;
            $numeros_facturas_array = [];
            foreach ($request->detalles as $detalle) {
                $monto = str_replace('.', '', $detalle['monto']);
                $monto_total = $monto_total + $monto;
                $venta = Venta::where('id', $detalle['venta'])->first();
                if (!$venta) {
                    return redirect()->route('recibos.create')->with('error-message', 'El recibo no puede ser generado. La factura de venta no fue encontrada.');
                } else {
                    $numeros_facturas_array[] = $venta->numero_factura;
                }
            }
            $numeros_facturas = implode(' / ', $numeros_facturas_array);

            $recibo = new Recibo();
            $recibo->numero = $request->numero;
            $recibo->fecha = Carbon::now();
            $recibo->cliente_id = $request->cliente;
            $recibo->concepto = 'PAGO DE FACTURA/S ' . $numeros_facturas;
            $recibo->monto_total = $monto_total;
            $recibo->forma_pago_id = $request->forma_pago;
            $recibo->cargado_por_id = Auth::id();
            $recibo->save();

            foreach ($request->detalles as $detalle) {
                $recibo_detalle = new ReciboDetalle();
                $recibo_detalle->recibo_id = $recibo->id;
                $recibo_detalle->venta_id = $detalle['venta'];
                $recibo_detalle->monto = str_replace('.', '', $detalle['monto']);
                $recibo_detalle->save();

                $cobro = new Cobro();
                $cobro->venta_id = $recibo_detalle->venta_id;
                $cobro->forma_pago_id = $recibo->forma_pago_id;
                $cobro->monto = $recibo_detalle->monto;

                switch ($recibo->forma_pago_id) {
                    case 1: //efectivo
                        $cobro->caja_id = $caja->caja_id;
                        $cobro->save();

                        $movimiento_caja = new MovimientoCaja();
                        $movimiento_caja->fecha = Carbon::now();
                        $movimiento_caja->caja_destino_id = $caja->caja_id;
                        $movimiento_caja->sentido = 'D';
                        $movimiento_caja->monto = $cobro->monto;
                        $movimiento_caja->tipo_movimiento_id = 12; //aca va el ID del movimiento de caja del tipo Recibo
                        $movimiento_caja->motivo = 'RECIBO N° ' . str_pad($recibo->numero, 7, '0', STR_PAD_LEFT) . ' COBRADO CON EFECTIVO';
                        $movimiento_caja->venta_id = $recibo_detalle->venta_id;
                        $movimiento_caja->estado = 'AP';
                        $movimiento_caja->creado_por_id = Auth::id();
                        $movimiento_caja->aprobado_por_id = Auth::id();
                        $movimiento_caja->save();

                        $caja = Caja::findOrFail($caja->caja_id);
                        $caja->monto = $caja->monto + $cobro->monto;
                        $caja->save();

                        $cuenta_contable = CuentaContable::where('id', $caja->cuenta_ingreso_id)->first();
                        break;
                    case 2: //tarjeta de debito
                        $caja = UsuarioCaja::where('usuario_id', Auth::id())->first();
                        $cuenta_bancaria = CuentaBancaria::where('acredita_tarjeta', true)->first();
                        $cuenta_contable = CuentaContable::where('id', $cuenta_bancaria->cuenta_ingreso_id)->first();
                        if (!$cuenta_bancaria) {
                            return back()->with('error-message', 'No existe una cuenta bancaria habilitada para cobros con tarjetas.');
                        }

                        $cobro->caja_id = $caja->caja_id;
                        if (isset($request->banco_debito)) {
                            $cobro->banco_id = $request->banco_debito;
                        }
                        if (isset($request->numero_transaccion_debito)) {
                            $cobro->numero_transaccion = $request->numero_transaccion_debito;
                        }
                        if (isset($request->fecha_transaccion_debito)) {
                            $cobro->fecha_transaccion = $request->fecha_transaccion_debito;
                        }
                        $cobro->cuenta_bancaria_id = $cuenta_bancaria->id;
                        $cobro->save();

                        $movimiento_caja = new MovimientoCaja();
                        $movimiento_caja->fecha = Carbon::now();
                        $movimiento_caja->caja_destino_id = $caja->caja_id;
                        $movimiento_caja->sentido = 'D';
                        $movimiento_caja->monto = $cobro->monto;
                        $movimiento_caja->tipo_movimiento_id = 12; //aca va el ID del movimiento de caja del tipo Recibo
                        $movimiento_caja->motivo = 'RECIBO N° ' . str_pad($recibo->numero, 7, '0', STR_PAD_LEFT) . ' COBRADO CON TARJETA DE DEBITO DEL BANCO ' . $cobro->banco->nombre . '. N° Y FECHA DE TRANSACCION: ' . $cobro->numero_transaccion . ' - ' . $cobro->fecha_transaccion;
                        $movimiento_caja->venta_id = $recibo_detalle->venta_id;
                        $movimiento_caja->estado = 'AP';
                        $movimiento_caja->creado_por_id = Auth::id();
                        $movimiento_caja->aprobado_por_id = Auth::id();
                        $movimiento_caja->save();

                        if ($cuenta_bancaria->moneda_id == 1) {
                            $monto = $cobro->monto;
                        } else {
                            $cotizacion = Cotizacion::orderBy('id', 'desc')->precio_venta;
                            $monto = $cobro->monto / $cotizacion;
                        }

                        $movimiento_banco = new MovimientoBanco();
                        $movimiento_banco->fecha = Carbon::now();
                        $movimiento_banco->cuenta_bancaria_destino_id = $cuenta_bancaria->id;
                        $movimiento_banco->sentido = 'D';
                        $movimiento_banco->monto = $monto;
                        $movimiento_banco->tipo_movimiento_id = 14; //aca va el ID del movimiento de banco del tipo Recibo
                        $movimiento_banco->motivo = 'RECIBO N° ' . str_pad($recibo->numero, 7, '0', STR_PAD_LEFT) . ' COBRADO CON TARJETA DE DEBITO DEL BANCO ' . $cobro->banco->nombre . '. N° Y FECHA DE TRANSACCION: ' . $cobro->numero_transaccion . ' - ' . $cobro->fecha_transaccion;
                        $movimiento_banco->venta_id = $recibo_detalle->venta_id;
                        $movimiento_banco->estado = 'PE';
                        $movimiento_banco->creado_por_id = Auth::id();
                        $movimiento_banco->aprobado_por_id = Auth::id();
                        $movimiento_banco->save();

                        //VERIFICAR LUEGO PARA HACER LOS CALCULOS DE % DE BANCARD
                        // $cuenta_bancaria->monto = $cuenta_bancaria->monto + $monto;
                        // $cuenta_bancaria->save();
                        break;
                    case 3: //tarjeta de credito
                        $caja = UsuarioCaja::where('usuario_id', Auth::id())->first();
                        $cuenta_bancaria = CuentaBancaria::where('acredita_tarjeta', true)->first();
                        $cuenta_contable = CuentaContable::where('id', $cuenta_bancaria->cuenta_ingreso_id)->first();
                        if (!$cuenta_bancaria) {
                            return back()->with('error-message', 'No existe una cuenta bancaria habilitada para cobros con tarjetas.');
                        }

                        $cobro->caja_id = $caja->caja_id;
                        if (isset($request->banco_credito)) {
                            $cobro->banco_id = $request->banco_credito;
                        }
                        if (isset($request->numero_transaccion_credito)) {
                            $cobro->numero_transaccion = $request->numero_transaccion_credito;
                        }
                        if (isset($request->fecha_transaccion_credito)) {
                            $cobro->fecha_transaccion = $request->fecha_transaccion_credito;
                        }
                        $cobro->cuenta_bancaria_id = $cuenta_bancaria->id;
                        $cobro->save();

                        $movimiento_caja = new MovimientoCaja();
                        $movimiento_caja->fecha = Carbon::now();
                        $movimiento_caja->caja_destino_id = $caja->caja_id;
                        $movimiento_caja->sentido = 'D';
                        $movimiento_caja->monto = $cobro->monto;
                        $movimiento_caja->tipo_movimiento_id = 12; //aca va el ID del movimiento de caja del tipo Recibo
                        $movimiento_caja->motivo = 'RECIBO N° ' . str_pad($recibo->numero, 7, '0', STR_PAD_LEFT) . ' COBRADO CON TARJETA DE CREDITO DEL BANCO ' . $cobro->banco->nombre . '. N° Y FECHA DE TRANSACCION: ' . $cobro->numero_transaccion . ' - ' . $cobro->fecha_transaccion;
                        $movimiento_caja->venta_id = $venta->id;
                        $movimiento_caja->estado = 'PE';
                        $movimiento_caja->creado_por_id = Auth::id();
                        $movimiento_caja->aprobado_por_id = Auth::id();
                        $movimiento_caja->save();

                        if ($cuenta_bancaria->moneda_id == 1) {
                            $monto = $cobro->monto;
                        } else {
                            $cotizacion = Cotizacion::orderBy('id', 'desc')->precio_venta;
                            $monto = $cobro->monto / $cotizacion;
                        }

                        $movimiento_banco = new MovimientoBanco();
                        $movimiento_banco->fecha = Carbon::now();
                        $movimiento_banco->cuenta_bancaria_destino_id = $cuenta_bancaria->id;
                        $movimiento_banco->sentido = 'D';
                        $movimiento_banco->monto = $monto;
                        $movimiento_banco->tipo_movimiento_id = 14; //aca va el ID del movimiento de banco del tipo Recibo
                        $movimiento_banco->motivo = 'RECIBO N° ' . str_pad($recibo->numero, 7, '0', STR_PAD_LEFT) . ' COBRADO CON TARJETA DE CREDITO DEL BANCO ' . $cobro->banco->nombre . '. N° Y FECHA DE TRANSACCION: ' . $cobro->numero_transaccion . ' - ' . $cobro->fecha_transaccion;
                        $movimiento_banco->venta_id = $recibo_detalle->venta_id;
                        $movimiento_banco->estado = 'AP';
                        $movimiento_banco->creado_por_id = Auth::id();
                        $movimiento_banco->aprobado_por_id = Auth::id();
                        $movimiento_banco->save();

                        //VERIFICAR LUEGO PARA HACER LOS CALCULOS DE % DE BANCARD
                        // $cuenta_bancaria->monto = $cuenta_bancaria->monto + $monto;
                        // $cuenta_bancaria->save();
                        break;
                    case 4: //transferencia
                        $caja = UsuarioCaja::where('usuario_id', Auth::id())->first();

                        $cobro->caja_id = $caja->caja_id;
                        if (isset($request->banco_transferencia)) {
                            $cobro->banco_id = $request->banco_transferencia;
                        }
                        if (isset($request->numero_transaccion_transferencia)) {
                            $cobro->numero_transaccion = $request->numero_transaccion_transferencia;
                        }
                        if (isset($request->fecha_transaccion_transferencia)) {
                            $cobro->fecha_transaccion = $request->fecha_transaccion_transferencia;
                        }
                        if (isset($request->cuenta_bancaria_transferencia)) {
                            $cobro->cuenta_bancaria_id = $request->cuenta_bancaria_transferencia;
                        }
                        $cobro->save();

                        $movimiento_caja = new MovimientoCaja();
                        $movimiento_caja->fecha = Carbon::now();
                        $movimiento_caja->caja_destino_id = $caja->caja_id;
                        $movimiento_caja->sentido = 'D';
                        $movimiento_caja->monto = $cobro->monto;
                        $movimiento_caja->tipo_movimiento_id = 12; //aca va el ID del movimiento de caja del tipo Recibo
                        $movimiento_caja->motivo = 'RECIBO N° ' . str_pad($recibo->numero, 7, '0', STR_PAD_LEFT) . ' COBRADO CON TRANSFERENCIA DESDE EL BANCO ' . $cobro->banco->nombre . 'A LA CUENTA BANCARIA ' . $cobro->cuentaBancaria->banco->nombre . ' - ' . $cobro->cuentaBancaria->numero_cuenta . '. N° Y FECHA DE TRANSACCION: ' . $cobro->numero_transaccion . ' - ' . $cobro->fecha_transaccion;
                        $movimiento_caja->venta_id = $venta->id;
                        $movimiento_caja->estado = 'AP';
                        $movimiento_caja->creado_por_id = Auth::id();
                        $movimiento_caja->aprobado_por_id = Auth::id();
                        $movimiento_caja->save();

                        $cuenta_bancaria = CuentaBancaria::findOrFail($cobro->cuenta_bancaria_id);
                        $cuenta_contable = CuentaContable::where('id', $cuenta_bancaria->cuenta_ingreso_id)->first();

                        $movimiento_banco = new MovimientoBanco();
                        $movimiento_banco->fecha = Carbon::now();
                        $movimiento_banco->cuenta_bancaria_destino_id = $cuenta_bancaria->id;
                        $movimiento_banco->sentido = 'D';
                        $movimiento_banco->monto = $cobro->monto;
                        $movimiento_banco->tipo_movimiento_id = 14; //aca va el ID del movimiento de banco del tipo Recibo
                        $movimiento_banco->motivo = 'RECIBO N° ' . str_pad($recibo->numero, 7, '0', STR_PAD_LEFT) . ' COBRADO CON TRANSFERENCIA DESDE EL BANCO ' . $cobro->banco->nombre . 'A LA CUENTA BANCARIA ' . $cobro->cuentaBancaria->banco->nombre . ' - ' . $cobro->cuentaBancaria->numero_cuenta . '. N° Y FECHA DE TRANSACCION: ' . $cobro->numero_transaccion . ' - ' . $cobro->fecha_transaccion;
                        $movimiento_banco->venta_id = $recibo_detalle->venta_id;
                        $movimiento_banco->estado = 'PE';
                        $movimiento_banco->creado_por_id = Auth::id();
                        $movimiento_banco->save();
                        break;
                    case 5: //deposito
                        $caja = UsuarioCaja::where('usuario_id', Auth::id())->first();

                        $cobro->caja_id = $caja->caja_id;
                        if (isset($request->numero_transaccion_deposito)) {
                            $cobro->numero_transaccion = $request->numero_transaccion_deposito;
                        }
                        if (isset($request->fecha_transaccion_deposito)) {
                            $cobro->fecha_transaccion = $request->fecha_transaccion_deposito;
                        }
                        if (isset($request->cuenta_bancaria_deposito)) {
                            $cobro->cuenta_bancaria_id = $request->cuenta_bancaria_deposito;
                        }
                        $cobro->save();

                        $movimiento_caja = new MovimientoCaja();
                        $movimiento_caja->fecha = Carbon::now();
                        $movimiento_caja->caja_destino_id = $caja->caja_id;
                        $movimiento_caja->sentido = 'D';
                        $movimiento_caja->monto = $cobro->monto;
                        $movimiento_caja->tipo_movimiento_id = 12; //aca va el ID del movimiento de caja del tipo Recibo
                        $movimiento_caja->motivo = 'RECIBO N° ' . str_pad($recibo->numero, 7, '0', STR_PAD_LEFT) . ' COBRADO CON DEPOSITO BANCARIO A LA CUENTA BANCARIA ' . $cobro->cuentaBancaria->banco->nombre . ' - ' . $cobro->cuentaBancaria->numero_cuenta . '. N° Y FECHA DE TRANSACCION: ' . $cobro->numero_transaccion . ' - ' . $cobro->fecha_transaccion;
                        $movimiento_caja->venta_id = $venta->id;
                        $movimiento_caja->estado = 'AP';
                        $movimiento_caja->creado_por_id = Auth::id();
                        $movimiento_caja->aprobado_por_id = Auth::id();
                        $movimiento_caja->save();

                        $cuenta_bancaria = CuentaBancaria::findOrFail($cobro->cuenta_bancaria_id);
                        $cuenta_contable = CuentaContable::where('id', $cuenta_bancaria->cuenta_ingreso_id)->first();

                        $movimiento_banco = new MovimientoBanco();
                        $movimiento_banco->fecha = Carbon::now();
                        $movimiento_banco->cuenta_bancaria_destino_id = $cuenta_bancaria->id;
                        $movimiento_banco->sentido = 'D';
                        $movimiento_banco->monto = $cobro->monto;
                        $movimiento_banco->tipo_movimiento_id = 14; //aca va el ID del movimiento de banco del tipo Recibo
                        $movimiento_banco->motivo = 'RECIBO N° ' . str_pad($recibo->numero, 7, '0', STR_PAD_LEFT) . ' COBRADO CON DEPOSITO BANCARIO A LA CUENTA BANCARIA ' . $cobro->cuentaBancaria->banco->nombre . ' - ' . $cobro->cuentaBancaria->numero_cuenta . '. N° Y FECHA DE TRANSACCION: ' . $cobro->numero_transaccion . ' - ' . $cobro->fecha_transaccion;
                        $movimiento_banco->venta_id = $recibo_detalle->venta_id;
                        $movimiento_banco->estado = 'PE';
                        $movimiento_banco->creado_por_id = Auth::id();
                        $movimiento_banco->save();
                        break;
                    default:
                        break;
                }
                $cobro->save();

                $venta = Venta::where('id', $detalle['venta'])->first();
                $venta->saldo = $venta->saldo - $recibo_detalle->monto;
                if ($venta->saldo == 0) {
                    $venta->estado = 'CO';
                }
                $venta->save();
            }

            $old_asiento_contable = AsientoContable::orderBy('id', 'desc')->first();
            if ($old_asiento_contable) {
                $numero_asiento = $old_asiento_contable->numero + 1;
            } else {
                $numero_asiento = 1;
            }

            $asiento_contable = new AsientoContable();
            $asiento_contable->numero = $numero_asiento;
            $asiento_contable->fecha = Carbon::now();
            $asiento_contable->origen = 'RECIBO';
            $asiento_contable->moneda_id = 1;
            $asiento_contable->cotizacion_id = null;
            $asiento_contable->unidad_negocio_id = 1; //aca va la unidad de negocio NINGUNO
            $asiento_contable->subunidad_negocio_id = 1; //aca va la subunidad de negocio NINGUNO
            $asiento_contable->save();

            $asiento_contable_detalle = new AsientoContableDetalle();
            $asiento_contable_detalle->asiento_id = $asiento_contable->id;
            $asiento_contable_detalle->cuenta_contable_id = 18; //aca va la cuenta contable CUENTAS A COBRAR
            $asiento_contable_detalle->descripcion = 'RECIBO N° ' . str_pad($recibo->numero, 7, '0', STR_PAD_LEFT) . ' - CLIENTE: ' . $recibo->cliente->nombre;
            $asiento_contable_detalle->haber = $recibo->monto_total;
            $asiento_contable_detalle->centro_costo_id = 1; //aca va el centro de costo NINGUNO
            $asiento_contable_detalle->subcentro_costo_id = 1; //aca va el subcentro de costo NINGUNO
            $asiento_contable_detalle->save();

            $asiento_contable_cobro = new AsientoContableDetalle();
            $asiento_contable_cobro->asiento_id = $asiento_contable->id;
            $asiento_contable_cobro->cuenta_contable_id = $cuenta_contable->id;
            $asiento_contable_cobro->descripcion = 'COBRO SOBRE RECIBO N° ' . str_pad($recibo->numero, 7, '0', STR_PAD_LEFT) . ' - CLIENTE: ' . $recibo->cliente->nombre;
            $asiento_contable_cobro->debe = $recibo->monto_total;
            $asiento_contable_cobro->centro_costo_id = 1; //aca va el centro de costo NINGUNO
            $asiento_contable_cobro->subcentro_costo_id = 1; //aca va el subcentro de costo NINGUNO
            $asiento_contable_cobro->save();

            $saldo_cuenta_contable_recibo = SaldoCuentaContable::where('id', $asiento_contable_detalle->cuenta_contable_id)->first();
            if ($saldo_cuenta_contable_recibo) {
                $saldo_cuenta_contable_recibo->saldo = $saldo_cuenta_contable_recibo->saldo + $asiento_contable_detalle->haber;
                $saldo_cuenta_contable_recibo->save();
            }

            $saldo_cuenta_contable_recibo_detalle = new SaldoCuentaContableDetalle();
            $saldo_cuenta_contable_recibo_detalle->saldo_cuenta_contable_id = $saldo_cuenta_contable_recibo->id;
            $saldo_cuenta_contable_recibo_detalle->mes = Carbon::now()->format('m');
            $saldo_cuenta_contable_recibo_detalle->anho = Carbon::now()->format('Y');
            $saldo_cuenta_contable_recibo_detalle->haber = $asiento_contable_detalle->haber;
            $saldo_cuenta_contable_recibo_detalle->save();

            $saldo_cuenta_contable_cobro = SaldoCuentaContable::where('id', $asiento_contable_detalle->cuenta_contable_id)->first();
            if ($saldo_cuenta_contable_cobro) {
                $saldo_cuenta_contable_cobro->saldo = $saldo_cuenta_contable_cobro->saldo + $asiento_contable_detalle->haber;
                $saldo_cuenta_contable_cobro->save();
            }

            $saldo_cuenta_contable_cobro_detalle = new SaldoCuentaContableDetalle();
            $saldo_cuenta_contable_cobro_detalle->saldo_cuenta_contable_id = $saldo_cuenta_contable_cobro->id;
            $saldo_cuenta_contable_cobro_detalle->mes = Carbon::now()->format('m');
            $saldo_cuenta_contable_cobro_detalle->anho = Carbon::now()->format('Y');
            $saldo_cuenta_contable_cobro_detalle->debe = $asiento_contable_cobro->debe;
            $saldo_cuenta_contable_cobro_detalle->save();

            DB::commit();

            return redirect()->route('recibos.index')->with('success-message', 'La venta N° ' . $venta->numero_factura . ' fue creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('recibos.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('anular_recibos');

        DB::beginTransaction();

        try {
            $recibo = Recibo::with('detalles')->findOrFail($id);
            if (!Carbon::parse($recibo->fecha)->isSameMonth(Carbon::now())) {
                return back()->with('error-message', 'No se puede anular la venta porque no pertenece al mes y año actual.');
            }

            $recibo->anulado_por_id = Auth::id();
            $recibo->estado = 'IN';
            $recibo->save();

            foreach ($recibo->detalles as $detalle) {
                $venta = Venta::where('id', $detalle->venta_id)->first();
                foreach ($venta->cobros as $cobro) {
                    if ($cobro->forma_pago_id == 1) {
                        $caja = Caja::findOrFail($cobro->caja_id);
                        $caja->monto = $caja->monto - $cobro->monto;
                        $caja->save();
                    } else {
                        $cuenta_bancaria = CuentaBancaria::findOrFail($cobro->cuenta_bancaria_id);
                        if ($cuenta_bancaria->moneda_id == 1) {
                            $monto = $cobro->monto;
                        } else {
                            $cotizacion = Cotizacion::orderBy('id', 'desc')->first()->precio_venta;
                            $monto = $cobro->monto / $cotizacion;
                        }
                        $cuenta_bancaria->monto = $cuenta_bancaria->monto - $monto;
                        $cuenta_bancaria->save();
                    }
                    $cobro->estado = 'IN';
                    $cobro->save();
                }
                $venta->saldo = $venta->saldo + $detalle->monto;
                if ($venta->saldo == $venta->monto_total) {
                    $venta->estado = 'PE';
                }
                $venta->save();

                foreach ($venta->ventaDetalles as $detalle) {
                    $movimientos_cajas = MovimientoCaja::where('venta_id', $detalle->venta_id)->get();
                    if ($movimientos_cajas->count() > 0) {
                        foreach ($movimientos_cajas as $movimiento) {
                            $movimiento->estado = 'IN';
                            $movimiento->save();
                        }
                    }

                    $movimientos_bancos = MovimientoBanco::where('venta_id', $detalle->venta_id)->get();
                    if ($movimientos_bancos->count() > 0) {
                        foreach ($movimientos_bancos as $movimiento) {
                            $movimiento->estado = 'IN';
                            $movimiento->save();
                        }
                    }
                }
            }

            $asiento_contable = AsientoContable::whereHas('detalles', function ($query) use ($recibo) {
                $query->where('descripcion', 'like', '%RECIBO N° ' . str_pad($recibo->numero, 7, '0', STR_PAD_LEFT) . '%');
            })->first();

            foreach ($asiento_contable->detalles as $asiento_detalle) {
                $cuenta_contable = CuentaContable::find($asiento_detalle->cuenta_contable_id)->first();
                if ($cuenta_contable) {
                    $saldo_cuenta_contable = SaldoCuentaContable::where('cuenta_contable_id', $cuenta_contable->id)->first();
                    if ($saldo_cuenta_contable) {
                        if ($asiento_detalle->debe) {
                            $saldo_cuenta_contable->saldo = $saldo_cuenta_contable->saldo + $asiento_detalle->debe;
                        } elseif ($asiento_detalle->haber) {
                            $saldo_cuenta_contable->saldo = $saldo_cuenta_contable->saldo - $asiento_detalle->haber;
                        }
                        $saldo_cuenta_contable->saldo = $saldo_cuenta_contable->saldo - $asiento_detalle->haber;
                        $saldo_cuenta_contable->save();

                        SaldoCuentaContableDetalle::where('saldo_cuenta_contable_id', $saldo_cuenta_contable->id)->where('venta_id', $venta->id)->delete();
                    } else {
                        return back()->with('error-message', 'Hubo un problema al anular la venta. No se encontró la cuenta contable del asiento.');
                    }
                } else {
                    return back()->with('error-message', 'Hubo un problema al anular la venta. No se encontró la cuenta contable del asiento.');
                }
            }
            $asiento_contable->estado = 'IN';
            $asiento_contable->actualizado_por_id = Auth::id();
            $asiento_contable->save();

            DB::commit();

            return redirect()->route('recibos.index')->with('error-message','El recibo N° ' . str_pad($recibo->numero, 7, '0', STR_PAD_LEFT) . ' fue anulado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('recibos.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_recibos');

        DB::beginTransaction();

        try {
            $recibo = Recibo::with('detalles')->findOrFail($id);

            if ($recibo->estado == 'AC') {
                return back()->with('error-message', 'La venta no se puede eliminar. Su estado actualmente es facturado.');
            }

            foreach ($recibo->detalles as $detalle) {
                $cobros = Cobro::where('venta_id', $detalle->venta_id)->delete();
                $detalle->delete();
                $movimientos_cajas = MovimientoCaja::where('venta_id', $detalle->venta_id)->delete();
                $movimientos_bancos = MovimientoBanco::where('venta_id', $detalle->venta_id)->delete();
            }
            $asiento_contable = AsientoContable::whereHas('detalles', function ($query) use ($recibo) {
                $query->where('descripcion', 'like', '%RECIBO N° ' . str_pad($recibo->numero, 7, '0', STR_PAD_LEFT) . '%');
            })->first();
            $asientos_contables_detalles = AsientoContableDetalle::where('asiento_id', $asiento_contable->id)->delete();
            $asiento_contable->delete();

            $recibo->delete();

            DB::commit();

            return redirect()->route('recibos.index')->with('success-message','El recibo N° ' . str_pad($recibo->numero, 7, '0', STR_PAD_LEFT) . ' fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('recibos.index')->with('error-message', 'La venta N° ' . $venta->numero_factura . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('recibos.index')->with('error-message', $e->getMessage());
            }
        }
    }

    public function get_ventas($id)
    {
        $this->authorize('crear_recibos');

        DB::beginTransaction();

        try {
            $ventas = Venta::where('cliente_id', $id)->where('forma_pago', 'CR')->where('estado', 'PE')->get();

            return response()->json([
                'ventas' => $ventas,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('recibos.index')->with('error-message', $e->getMessage());
        }
    }
}
