<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\PuntoImpresion;
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
use App\Models\PagoMatriculacion;
use App\Models\PagoSolicitud;
use App\Models\PagoTesis;
use App\Models\PagoTutoria;
use App\Models\PagoInscripcionUbs;
use App\Models\PagoTesisUbs;
use App\Models\Recibo;
use App\Models\NotaCredito;
use App\Models\Alumno;
use App\Models\Carrera;
use App\Models\Curso;
use App\Models\Semestre;
use App\Models\Matriculacion;
use App\Models\InscripcionUbs;

class VentaController extends Controller
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
        $this->authorize('ver_ventas');

        try {
            $ventas = Venta::orderBy('id', 'desc')->get();
            return view('ventas/index')->with(compact('ventas'));
        } catch (\Exception $e) {
            return redirect()->route('ventas.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_ventas');

        try {
            $venta = Venta::with('ventaDetalles', 'cobros', 'asientoContable')->findOrFail($id);
            $recibos = Recibo::whereHas('detalles', function ($query) use ($venta) {
                $query->where('venta_id', $venta->id);
            })->get();
            return view('ventas/show')->with(compact('venta', 'recibos'));
        } catch (\Exception $e) {
            return redirect()->route('ventas.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_ventas');

        try {
            $fecha_hoy = Carbon::today()->format('d/m/Y');

            $punto_impresion = PuntoImpresion::where('tipo_documento_id', 1)->where('estado', 'AC')->orderBy('id', 'desc')->first();
            if (!$punto_impresion) {
                return redirect()->route('ventas.index')->with('error-message', 'No se encuentra registrado ningún punto de impresión activo en el sistema para realizar la venta.');
            }
            $ultima_venta = Venta::orderBy('id', 'desc')->first();
            if ($ultima_venta) {
                $numero = $ultima_venta->numero_factura;
                $partes_numero = explode('-', $numero);
                $numero = $partes_numero[2];
                $numero_sin_ceros = intval(ltrim($numero, '0'));
                $numero = $numero_sin_ceros + 1;
                $numero = str_pad($numero, 7, '0', STR_PAD_LEFT);
            } else {
                $numero = '0000001';
            }

            $numero_int = intval($numero);
            $punto_impresion_desde_int = intval($punto_impresion->numero_desde);
            $punto_impresion_hasta_int = intval($punto_impresion->numero_hasta);
            if ($numero_int >= $punto_impresion_desde_int && $numero_int <= $punto_impresion_hasta_int) {
                $numero_factura = $punto_impresion->codigo . $numero;
            } else {
                return redirect()->route('ventas.index')->with('error-message', 'El punto de impresión activo no se encuentra dentro del rango para realizar la factura ' . $numero_factura . '.');
            }

            $caja = UsuarioCaja::where('usuario_id', Auth::id())->first();
            if (!$caja) {
                return redirect()->route('ventas.index')->with('error-message', 'Usted no cuenta con una caja registrada a su nombre para realizar la venta.');
            }

            $clientes = Cliente::where('estado', 'AC')->get();
            $formas_pagos = FormaPago::where('estado', 'AC')->get();
            $formas_pagos_dos = FormaPago::where('id', '!=', 6)->where('estado', 'AC')->get();
            $articulos = Articulo::with('detalle')->where('estado', 'AC')->where('compra_venta', 'VENTA')->get();
            $bancos = Banco::where('estado', 'AC')->get();
            $cuentas_bancarias = CuentaBancaria::where('estado', 'AC')->get();
            $unidades_negocios = UnidadNegocioContable::where('estado', 'AC')->get();
            $alumnos = Alumno::where('estado', 'AC')->get();
            $centros_costos = CentroCostoContable::where('estado', 'AC')->get();

            return view('ventas/create')->with(compact('fecha_hoy', 'numero_factura', 'caja', 'clientes', 'formas_pagos', 'formas_pagos_dos', 'articulos', 'bancos', 'cuentas_bancarias', 'unidades_negocios', 'alumnos', 'centros_costos'));
        } catch (\Exception $e) {
            return redirect()->route('ventas.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_ventas');

        $request->validate([
            'numero_factura' => 'required',
            'cliente' => ['required', 'numeric'],
            'forma_pago' => 'required',
            'metodo_pago' => ['nullable', 'numeric', 'required_if:forma_pago,CO'],
            'nota_credito'=> ['nullable', 'numeric', 'required_if:metodo_pago,6'],
            'monto_nota_credito' => ['nullable', 'required_if:metodo_pago,6'],
            'credito_a' => ['nullable', 'numeric', 'required_if:forma_pago,CR'],
            'banco_debito' => ['nullable', 'numeric', 'required_if:metodo_pago,2'],
            'numero_transaccion_debito' => ['nullable', 'required_if:metodo_pago,2'],
            'fecha_transaccion_debito' => ['nullable', 'date', 'required_if:metodo_pago,2'],
            'banco_credito' => ['nullable', 'numeric', 'required_if:metodo_pago,3'],
            'numero_transaccion_credito' => ['nullable', 'required_if:metodo_pago,3'],
            'fecha_transaccion_credito' => ['nullable', 'date', 'required_if:metodo_pago,3'],
            'banco_transferencia' => ['nullable', 'numeric', 'required_if:metodo_pago,4'],
            'numero_transaccion_transferencia' => ['nullable', 'required_if:metodo_pago,4'],
            'fecha_transaccion_transferencia' => ['nullable', 'date', 'required_if:metodo_pago,4'],
            'cuenta_bancaria_transferencia' => ['nullable', 'numeric', 'required_if:metodo_pago,4'],
            'numero_transaccion_deposito' => ['nullable', 'required_if:metodo_pago,5'],
            'fecha_transaccion_deposito' => ['nullable', 'date', 'required_if:metodo_pago,5'],
            'cuenta_bancaria_deposito' => ['nullable', 'numeric', 'required_if:metodo_pago,5'],
            'unidad_negocio' => ['required', 'numeric'],
            'subunidad_negocio' => ['required', 'numeric'],


            'detalles' => ['required', 'array'],
            'detalles.*.alumno' => ['required', 'numeric'],
            'detalles.*.articulo' => ['required', 'numeric'],
            'detalles.*.centro_costo' => ['required', 'numeric'],
            'detalles.*.subcentro_costo' => ['required', 'numeric'],
            'detalles.*.precio' => ['required']
        ]);

        DB::beginTransaction();

        try {
            $punto_impresion = PuntoImpresion::where('tipo_documento_id', 1)->where('estado', 'AC')->orderBy('id', 'desc')->first();
            if (!$punto_impresion) {
                return back()->with('error-message', 'No se encuentra registrado ningún punto de impresión activo en el sistema para realizar la venta.');
            }

            $caja = UsuarioCaja::where('usuario_id', Auth::id())->first();
            if (!$caja) {
                return redirect()->route('ventas.index')->with('error-message', 'Usted no cuenta con una caja registrada a su nombre para realizar la venta.');
            }

            $numero_factura = $request->numero_factura;

            $venta = new Venta();
            $venta->fecha = Carbon::now();
            $venta->cliente_id = $request->cliente;
            $venta->punto_impresion_id = $punto_impresion->id;
            $venta->numero_factura = $numero_factura;
            $venta->forma_pago = $request->forma_pago;
            $venta->cargado_por_id = Auth::id();

            $monto_total = 0;
            foreach ($request->detalles as $detalle) {
                $monto_total += str_replace('.', '', $detalle['precio']);
            }
            $venta->monto_total = $monto_total;
            if ($request->forma_pago == 'CR') {
                $venta->credito_a = $request->credito_a;
                $venta->saldo = $monto_total;
                $venta->estado = 'PE';
            }
            $venta->save();

            $cuentas_contables_ventas = collect();

            foreach ($request->detalles as $key => $detalle) {
                $articulo = Articulo::where('id', $detalle['articulo'])->first();
                if (!$articulo) {
                    return back()->with('error-message', 'Hubo un problema al realizar la venta. No es posible obtener el artículo seleccionado.');
                }

                $carrera = Carrera::where('articulo_id', $articulo->id)->first();
                if (!$carrera) {
                    $curso = Curso::where('articulo_id', $articulo->id)->first();
                    if ($curso) {
                        $ultima_inscripcion = InscripcionUbs::where('curso_id', $request->curso)->orderBy('id', 'desc')->first();
                        if ($ultima_inscripcion) {
                            $numero_inscripcion = $ultima_inscripcion->numero_inscripcion + 1;
                        } else {
                            $numero_inscripcion = 1;
                        }

                        $inscripcion = new InscripcionUbs();
                        $inscripcion->fecha = Carbon::now();
                        $inscripcion->alumno_id = $detalle['alumno'];
                        $inscripcion->curso_id = $curso->id;
                        $inscripcion->numero_inscripcion = $numero_inscripcion;
                        $inscripcion->tipo_pago = 'EM';
                        $inscripcion->cargado_por_id = Auth::id();
                        $inscripcion->save();
                    }
                } else {
                    $semestre = Semestre::where('estado', 'AC')->orderBy('id', 'desc')->first();

                    $matriculacion = new Matriculacion();
                    $matriculacion->fecha = Carbon::now();
                    $matriculacion->alumno_id = $detalle['alumno'];
                    $matriculacion->semestre_id = $semestre->id;
                    $matriculacion->programa_id = $carrera->programa_id;
                    $matriculacion->carrera_id = $carrera->id;
                    $matriculacion->carrera_siu_id = null;
                    $matriculacion->tipo_pago = 'EM';
                    $matriculacion->venta_id = $venta->id;
                    $matriculacion->cargado_por_id = Auth::id();
                    $matriculacion->save();
                }

                $venta_detalle = new VentaDetalle();
                $venta_detalle->venta_id = $venta->id;
                $venta_detalle->cantidad = 1;
                $venta_detalle->descripcion = $articulo->nombre;
                $venta_detalle->descuento = 0;
                $venta_detalle->monto_bruto = str_replace('.', '', $detalle['precio']);
                $venta_detalle->monto_neto = str_replace('.', '', $detalle['precio']);
                $venta_detalle->articulo_id = $detalle['articulo'];
                $venta_detalle->save();

                $cuenta_contable_venta = CuentaContable::where('id', $articulo->detalle->cuenta_contado_id)->first();
                if (!$cuenta_contable_venta) {
                    return back()->with('error-message', 'Hubo un problema al realizar la venta. No es posible obtener la cuenta contable del artículo.');
                } else {
                    $cuenta_contable_venta->monto = $venta_detalle->monto_neto;
                    $cuenta_contable_venta->centro_costo = $detalle['centro_costo'];
                    $cuenta_contable_venta->subcentro_costo = $detalle['subcentro_costo'];
                    $cuentas_contables_ventas->push($cuenta_contable_venta);
                }

                if ($key == 0) {
                    $centro_costo = $detalle['centro_costo'];
                    $subcentro_costo = $detalle['subcentro_costo'];
                }
            }

            if ($venta->forma_pago == 'CO') {
                $cobro = new Cobro();
                $cobro->venta_id = $venta->id;
                $cobro->forma_pago_id = $request->metodo_pago;
                $cobro->monto = $monto_total;

                $bandera_nota_credito = false;

                switch ($request->metodo_pago) {
                    case 1: //efectivo
                        $cobro->caja_id = $caja->caja_id;
                        $cobro->save();

                        $movimiento_caja = new MovimientoCaja();
                        $movimiento_caja->fecha = Carbon::now();
                        $movimiento_caja->caja_destino_id = $caja->caja_id;
                        $movimiento_caja->sentido = 'D';
                        $movimiento_caja->monto = $cobro->monto;
                        $movimiento_caja->tipo_movimiento_id = 12; //aca va el ID del movimiento de caja del tipo Venta
                        $movimiento_caja->motivo = 'FACTURA N° ' . $venta->numero_factura . ' COBRADO CON EFECTIVO';
                        $movimiento_caja->venta_id = $venta->id;
                        $movimiento_caja->estado = 'AP';
                        $movimiento_caja->creado_por_id = Auth::id();
                        $movimiento_caja->aprobado_por_id = Auth::id();
                        $movimiento_caja->save();

                        $caja = Caja::findOrFail($caja->caja_id);
                        $caja->monto = $caja->monto + $cobro->monto;
                        $caja->save();

                        $cuenta_contable_cobro = CuentaContable::where('id', $caja->cuenta_ingreso_id)->first();
                        if (!$cuenta_contable_cobro) {
                            return back()->with('error-message', 'Hubo un problema al realizar la venta. No es posible obtener la cuenta contable de la caja.');
                        } else {
                            $cuenta_contable_cobro->monto = $cobro->monto;
                            $cuenta_contable_cobro->tipo = 'CAJA';
                            $cuenta_contable_cobro->caja = $caja->nombre;
                            $cuenta_contable_cobro->centro_costo = $centro_costo;
                            $cuenta_contable_cobro->subcentro_costo = $subcentro_costo;
                        }
                        break;
                    case 2: //tarjeta de debito
                        $caja = UsuarioCaja::where('usuario_id', Auth::id())->first();
                        $cuenta_bancaria = CuentaBancaria::where('acredita_tarjeta', true)->first();
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
                        $movimiento_caja->tipo_movimiento_id = 12; //aca va el ID del movimiento de caja del tipo Venta
                        $movimiento_caja->motivo = 'FACTURA N° ' . $venta->numero_factura . ' COBRADO CON TARJETA DE DEBITO DEL BANCO ' . $cobro->banco->nombre . '. N° Y FECHA DE TRANSACCION: ' . $cobro->numero_transaccion . ' - ' . $cobro->fecha_transaccion;
                        $movimiento_caja->venta_id = $venta->id;
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
                        $movimiento_banco->tipo_movimiento_id = 14; //aca va el ID del movimiento de banco del tipo Venta
                        $movimiento_banco->motivo = 'FACTURA N° ' . $venta->numero_factura . ' COBRADO CON TARJETA DE DEBITO DEL BANCO ' . $cobro->banco->nombre . '. N° Y FECHA DE TRANSACCION: ' . $cobro->numero_transaccion . ' - ' . $cobro->fecha_transaccion;
                        $movimiento_banco->venta_id = $venta->id;
                        $movimiento_banco->estado = 'PE';
                        $movimiento_banco->creado_por_id = Auth::id();
                        $movimiento_banco->aprobado_por_id = Auth::id();
                        $movimiento_banco->save();

                        //VERIFICAR LUEGO PARA HACER LOS CALCULOS DE % DE BANCARD
                        // $cuenta_bancaria->monto = $cuenta_bancaria->monto + $monto;
                        // $cuenta_bancaria->save();

                        $cuenta_contable_cobro = CuentaContable::where('id', $cuenta_bancaria->cuenta_ingreso_id)->first();
                        if (!$cuenta_contable_cobro) {
                            return back()->with('error-message', 'Hubo un problema al realizar la venta. La cuenta contable de la cuenta bancaria no se encuentra seleccionada.');
                        } else {
                            $cuenta_contable_cobro->monto = $cobro->monto;
                            $cuenta_contable_cobro->tipo = 'BANCO';
                            $cuenta_contable_cobro->caja = $cuenta_bancaria->banco->nombre . ' ' . $cuenta_bancaria->numero_cuenta;
                            $cuenta_contable_cobro->centro_costo = $centro_costo;
                            $cuenta_contable_cobro->subcentro_costo = $subcentro_costo;
                        }
                        break;
                    case 3: //tarjeta de credito
                        $caja = UsuarioCaja::where('usuario_id', Auth::id())->first();
                        $cuenta_bancaria = CuentaBancaria::where('acredita_tarjeta', true)->first();
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
                        $movimiento_caja->tipo_movimiento_id = 12; //aca va el ID del movimiento de caja del tipo Venta
                        $movimiento_caja->motivo = 'FACTURA N° ' . $venta->numero_factura . ' COBRADO CON TARJETA DE CREDITO DEL BANCO ' . $cobro->banco->nombre . '. N° Y FECHA DE TRANSACCION: ' . $cobro->numero_transaccion . ' - ' . $cobro->fecha_transaccion;
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
                        $movimiento_banco->tipo_movimiento_id = 14; //aca va el ID del movimiento de banco del tipo Venta
                        $movimiento_banco->motivo = 'FACTURA N° ' . $venta->numero_factura . ' COBRADO CON TARJETA DE CREDITO DEL BANCO ' . $cobro->banco->nombre . '. N° Y FECHA DE TRANSACCION: ' . $cobro->numero_transaccion . ' - ' . $cobro->fecha_transaccion;
                        $movimiento_banco->venta_id = $venta->id;
                        $movimiento_banco->estado = 'AP';
                        $movimiento_banco->creado_por_id = Auth::id();
                        $movimiento_banco->aprobado_por_id = Auth::id();
                        $movimiento_banco->save();

                        //VERIFICAR LUEGO PARA HACER LOS CALCULOS DE % DE BANCARD
                        // $cuenta_bancaria->monto = $cuenta_bancaria->monto + $monto;
                        // $cuenta_bancaria->save();

                        $cuenta_contable_cobro = CuentaContable::where('id', $cuenta_bancaria->cuenta_ingreso_id)->first();
                        if (!$cuenta_contable_cobro) {
                            return back()->with('error-message', 'Hubo un problema al realizar la venta. La cuenta contable de la cuenta bancaria no se encuentra seleccionada.');
                        } else {
                            $cuenta_contable_cobro->monto = $cobro->monto;
                            $cuenta_contable_cobro->tipo = 'BANCO';
                            $cuenta_contable_cobro->caja = $cuenta_bancaria->banco->nombre . ' ' . $cuenta_bancaria->numero_cuenta;
                            $cuenta_contable_cobro->centro_costo = $centro_costo;
                            $cuenta_contable_cobro->subcentro_costo = $subcentro_costo;
                        }
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
                        $movimiento_caja->tipo_movimiento_id = 12; //aca va el ID del movimiento de caja del tipo Venta
                        $movimiento_caja->motivo = 'FACTURA N° ' . $venta->numero_factura . ' COBRADO CON TRANSFERENCIA DESDE EL BANCO ' . $cobro->banco->nombre . 'A LA CUENTA BANCARIA ' . $cobro->cuentaBancaria->banco->nombre . ' - ' . $cobro->cuentaBancaria->numero_cuenta . '. N° Y FECHA DE TRANSACCION: ' . $cobro->numero_transaccion . ' - ' . $cobro->fecha_transaccion;
                        $movimiento_caja->venta_id = $venta->id;
                        $movimiento_caja->estado = 'AP';
                        $movimiento_caja->creado_por_id = Auth::id();
                        $movimiento_caja->aprobado_por_id = Auth::id();
                        $movimiento_caja->save();

                        $cuenta_bancaria = CuentaBancaria::findOrFail($cobro->cuenta_bancaria_id);

                        $movimiento_banco = new MovimientoBanco();
                        $movimiento_banco->fecha = Carbon::now();
                        $movimiento_banco->cuenta_bancaria_destino_id = $cuenta_bancaria->id;
                        $movimiento_banco->sentido = 'D';
                        $movimiento_banco->monto = $cobro->monto;
                        $movimiento_banco->tipo_movimiento_id = 14; //aca va el ID del movimiento de banco del tipo Venta
                        $movimiento_banco->motivo = 'FACTURA N° ' . $venta->numero_factura . ' COBRADO CON TRANSFERENCIA DESDE EL BANCO ' . $cobro->banco->nombre . 'A LA CUENTA BANCARIA ' . $cobro->cuentaBancaria->banco->nombre . ' - ' . $cobro->cuentaBancaria->numero_cuenta . '. N° Y FECHA DE TRANSACCION: ' . $cobro->numero_transaccion . ' - ' . $cobro->fecha_transaccion;
                        $movimiento_banco->venta_id = $venta->id;
                        $movimiento_banco->estado = 'PE';
                        $movimiento_banco->creado_por_id = Auth::id();
                        $movimiento_banco->save();

                        $cuenta_contable_cobro = CuentaContable::where('id', $cuenta_bancaria->cuenta_ingreso_id)->first();
                        if (!$cuenta_contable_cobro) {
                            return back()->with('error-message', 'Hubo un problema al realizar la venta. La cuenta contable de la cuenta bancaria no se encuentra seleccionada.');
                        } else {
                            $cuenta_contable_cobro->monto = $cobro->monto;
                            $cuenta_contable_cobro->tipo = 'BANCO';
                            $cuenta_contable_cobro->cuenta_bancaria = $cuenta_bancaria->banco->nombre . ' ' . $cuenta_bancaria->numero_cuenta;
                            $cuenta_contable_cobro->centro_costo = $centro_costo;
                            $cuenta_contable_cobro->subcentro_costo = $subcentro_costo;
                        }
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
                        $movimiento_caja->tipo_movimiento_id = 12; //aca va el ID del movimiento de caja del tipo Venta
                        $movimiento_caja->motivo = 'FACTURA N° ' . $venta->numero_factura . ' COBRADO CON DEPOSITO BANCARIO A LA CUENTA BANCARIA ' . $cobro->cuentaBancaria->banco->nombre . ' - ' . $cobro->cuentaBancaria->numero_cuenta . '. N° Y FECHA DE TRANSACCION: ' . $cobro->numero_transaccion . ' - ' . $cobro->fecha_transaccion;
                        $movimiento_caja->venta_id = $venta->id;
                        $movimiento_caja->estado = 'AP';
                        $movimiento_caja->creado_por_id = Auth::id();
                        $movimiento_caja->aprobado_por_id = Auth::id();
                        $movimiento_caja->save();

                        $cuenta_bancaria = CuentaBancaria::findOrFail($cobro->cuenta_bancaria_id);

                        $movimiento_banco = new MovimientoBanco();
                        $movimiento_banco->fecha = Carbon::now();
                        $movimiento_banco->cuenta_bancaria_destino_id = $cuenta_bancaria->id;
                        $movimiento_banco->sentido = 'D';
                        $movimiento_banco->monto = $cobro->monto;
                        $movimiento_banco->tipo_movimiento_id = 14; //aca va el ID del movimiento de banco del tipo Venta
                        $movimiento_banco->motivo = 'FACTURA N° ' . $venta->numero_factura . ' COBRADO CON DEPOSITO BANCARIO A LA CUENTA BANCARIA ' . $cobro->cuentaBancaria->banco->nombre . ' - ' . $cobro->cuentaBancaria->numero_cuenta . '. N° Y FECHA DE TRANSACCION: ' . $cobro->numero_transaccion . ' - ' . $cobro->fecha_transaccion;
                        $movimiento_banco->venta_id = $venta->id;
                        $movimiento_banco->estado = 'PE';
                        $movimiento_banco->creado_por_id = Auth::id();
                        $movimiento_banco->save();

                        $cuenta_contable_cobro = CuentaContable::where('id', $cuenta_bancaria->cuenta_ingreso_id)->first();
                        if (!$cuenta_contable_cobro) {
                            return back()->with('error-message', 'Hubo un problema al realizar la venta. La cuenta contable de la cuenta bancaria no se encuentra seleccionada.');
                        } else {
                            $cuenta_contable_cobro->monto = $cobro->monto;
                            $cuenta_contable_cobro->tipo = 'BANCO';
                            $cuenta_contable_cobro->cuenta_bancaria = $cuenta_bancaria->banco->nombre . ' ' . $cuenta_bancaria->numero_cuenta;
                            $cuenta_contable_cobro->centro_costo = $centro_costo;
                            $cuenta_contable_cobro->subcentro_costo = $subcentro_costo;
                        }
                        break;
                    case 6: //nota de credito
                        $bandera_nota_credito = true;

                        $caja = UsuarioCaja::where('usuario_id', Auth::id())->first();
                        $cobro->caja_id = $caja->caja_id;

                        $nota_credito = NotaCredito::where('id', $request->nota_credito)->first();
                        if (!$nota_credito) {
                            return back()->with('error-message', 'Hubo un problema al realizar la venta. No es posible obtener la nota de crédito seleccionada.');
                        }

                        $cobro->nota_credito_id = $nota_credito->id;
                        $cobro->monto = $nota_credito->monto_total;
                        $cobro->save();

                        $nota_credito->estado = 'UT';
                        $nota_credito->save();

                        $movimiento_caja = new MovimientoCaja();
                        $movimiento_caja->fecha = Carbon::now();
                        $movimiento_caja->caja_destino_id = $caja->caja_id;
                        $movimiento_caja->sentido = 'D';
                        $movimiento_caja->monto = $cobro->monto;
                        $movimiento_caja->tipo_movimiento_id = 12; //aca va el ID del movimiento de caja del tipo Venta
                        $movimiento_caja->motivo = 'FACTURA N° ' . $venta->numero_factura . ' COBRADO CON NOTA DE CREDITO N° ' . $nota_credito->numero_nota_credito . '.';
                        $movimiento_caja->venta_id = $venta->id;
                        $movimiento_caja->estado = 'AP';
                        $movimiento_caja->creado_por_id = Auth::id();
                        $movimiento_caja->aprobado_por_id = Auth::id();
                        $movimiento_caja->save();

                        $cuenta_contable_cobro = CuentaContable::where('id', 49)->first(); //verificar a que cuenta de credito hace la asignacion de cobro con nota de credito
                        if (!$cuenta_contable_cobro) {
                            return back()->with('error-message', 'Hubo un problema al realizar la venta. La cuenta contable de la cuenta bancaria no se encuentra seleccionada.');
                        } else {
                            $cuenta_contable_cobro->monto = $cobro->monto;
                            $cuenta_contable_cobro->tipo = 'NOTA DE CREDITO';
                            $cuenta_contable_cobro->caja = $caja->nombre;
                            $cuenta_contable_cobro->centro_costo = $centro_costo;
                            $cuenta_contable_cobro->subcentro_costo = $subcentro_costo;
                        }

                        $cobro_dos = new Cobro();
                        $cobro_dos->venta_id = $venta->id;
                        $cobro_dos->forma_pago_id = $request->metodo_pago_dos;
                        $cobro_dos->monto = $venta->monto_total - $nota_credito->monto_total;

                        switch ($request->metodo_pago_dos) {
                            case 1: //efectivo
                                $cobro_dos->caja_id = $caja->caja_id;
                                $cobro_dos->save();

                                $caja = Caja::findOrFail($caja->caja_id);
                                $caja->monto = $caja->monto + $cobro_dos->monto;
                                $caja->save();

                                $movimiento_caja = new MovimientoCaja();
                                $movimiento_caja->fecha = Carbon::now();
                                $movimiento_caja->caja_destino_id = $caja->caja_id;
                                $movimiento_caja->sentido = 'D';
                                $movimiento_caja->monto = $cobro->monto;
                                $movimiento_caja->tipo_movimiento_id = 12; //aca va el ID del movimiento de caja del tipo Venta
                                $movimiento_caja->motivo = 'FACTURA N° ' . $venta->numero_factura . ' COBRADO CON EFETCIVO';
                                $movimiento_caja->venta_id = $venta->id;
                                $movimiento_caja->estado = 'AP';
                                $movimiento_caja->creado_por_id = Auth::id();
                                $movimiento_caja->aprobado_por_id = Auth::id();
                                $movimiento_caja->save();

                                $cuenta_contable_cobro_dos = CuentaContable::where('id', $caja->cuenta_ingreso_id)->first();
                                if (!$cuenta_contable_cobro_dos) {
                                    return back()->with('error-message', 'Hubo un problema al realizar la venta. No es posible obtener la cuenta contable de la caja.');
                                } else {
                                    $cuenta_contable_cobro_dos->monto = $cobro_dos->monto;
                                    $cuenta_contable_cobro_dos->tipo = 'CAJA';
                                    $cuenta_contable_cobro_dos->caja = $caja->nombre;
                                    $cuenta_contable_cobro_dos->centro_costo = $centro_costo;
                                    $cuenta_contable_cobro_dos->subcentro_costo = $subcentro_costo;
                                }
                                break;
                            case 2: //tarjeta de debito
                                $caja = UsuarioCaja::where('usuario_id', Auth::id())->first();
                                $cuenta_bancaria = CuentaBancaria::where('acredita_tarjeta', true)->first();
                                if (!$cuenta_bancaria) {
                                    return back()->with('error-message', 'No existe una cuenta bancaria habilitada para cobros con tarjetas.');
                                }

                                $cobro_dos->caja_id = $caja->caja_id;
                                if (isset($request->banco_debito_dos)) {
                                    $cobro_dos->banco_id = $request->banco_debito_dos;
                                }
                                if (isset($request->numero_transaccion_debito_dos)) {
                                    $cobro_dos->numero_transaccion = $request->numero_transaccion_debito_dos;
                                }
                                if (isset($request->fecha_transaccion_debito_dos)) {
                                    $cobro_dos->fecha_transaccion = $request->fecha_transaccion_debito_dos;
                                }
                                $cobro_dos->cuenta_bancaria_id = $cuenta_bancaria->id;
                                $cobro_dos->save();

                                if ($cuenta_bancaria->moneda_id == 1) {
                                    $monto = $cobro_dos->monto;
                                } else {
                                    $cotizacion = Cotizacion::orderBy('id', 'desc')->precio_venta;
                                    $monto = $cobro_dos->monto / $cotizacion;
                                }

                                $movimiento_caja = new MovimientoCaja();
                                $movimiento_caja->fecha = Carbon::now();
                                $movimiento_caja->caja_destino_id = $caja->caja_id;
                                $movimiento_caja->sentido = 'D';
                                $movimiento_caja->monto = $cobro_dos->monto;
                                $movimiento_caja->tipo_movimiento_id = 12; //aca va el ID del movimiento de caja del tipo Venta
                                $movimiento_caja->motivo = 'FACTURA N° ' . $venta->numero_factura . ' COBRADO CON TARJETA DE DEBITO DEL BANCO ' . $cobro_dos->banco->nombre . '. N° Y FECHA DE TRANSACCION: ' . $cobro_dos->numero_transaccion . ' - ' . $cobro_dos->fecha_transaccion;
                                $movimiento_caja->venta_id = $venta->id;
                                $movimiento_caja->estado = 'PE';
                                $movimiento_caja->creado_por_id = Auth::id();
                                $movimiento_caja->aprobado_por_id = Auth::id();
                                $movimiento_caja->save();

                                $movimiento_banco = new MovimientoBanco();
                                $movimiento_banco->fecha = Carbon::now();
                                $movimiento_banco->cuenta_bancaria_destino_id = $cuenta_bancaria->id;
                                $movimiento_banco->sentido = 'D';
                                $movimiento_banco->monto = $monto;
                                $movimiento_banco->tipo_movimiento_id = 14; //aca va el ID del movimiento de banco del tipo Venta
                                $movimiento_banco->motivo = 'FACTURA N° ' . $venta->numero_factura . ' COBRADO CON TARJETA DE DEBITO DEL BANCO ' . $cobro_dos->banco->nombre . '. N° Y FECHA DE TRANSACCION: ' . $cobro_dos->numero_transaccion . ' - ' . $cobro_dos->fecha_transaccion;
                                $movimiento_banco->venta_id = $venta->id;
                                $movimiento_banco->estado = 'PE';
                                $movimiento_banco->creado_por_id = Auth::id();
                                $movimiento_banco->aprobado_por_id = Auth::id();
                                $movimiento_banco->save();

                                //VERIFICAR LUEGO PARA HACER LOS CALCULOS DE % DE BANCARD
                                // $cuenta_bancaria->monto = $cuenta_bancaria->monto + $monto;
                                // $cuenta_bancaria->save();

                                $cuenta_contable_cobro_dos = CuentaContable::where('id', $cuenta_bancaria->cuenta_ingreso_id)->first();
                                if (!$cuenta_contable_cobro_dos) {
                                    return back()->with('error-message', 'Hubo un problema al realizar la venta. La cuenta contable de la cuenta bancaria no se encuentra seleccionada.');
                                } else {
                                    $cuenta_contable_cobro_dos->monto = $cobro_dos->monto;
                                    $cuenta_contable_cobro_dos->tipo = 'BANCO';
                                    $cuenta_contable_cobro_dos->caja = $cuenta_bancaria->banco->nombre . ' ' . $cuenta_bancaria->numero_cuenta;
                                    $cuenta_contable_cobro_dos->centro_costo = $centro_costo;
                                    $cuenta_contable_cobro_dos->subcentro_costo = $subcentro_costo;
                                }
                                break;
                            case 3: //tarjeta de credito
                                $caja = UsuarioCaja::where('usuario_id', Auth::id())->first();
                                $cuenta_bancaria = CuentaBancaria::where('acredita_tarjeta', true)->first();
                                if (!$cuenta_bancaria) {
                                    return back()->with('error-message', 'No existe una cuenta bancaria habilitada para cobros con tarjetas.');
                                }

                                $cobro_dos->caja_id = $caja->caja_id;
                                if (isset($request->banco_credito_dos)) {
                                    $cobro_dos->banco_id = $request->banco_credito_dos;
                                }
                                if (isset($request->numero_transaccion_credito_dos)) {
                                    $cobro_dos->numero_transaccion = $request->numero_transaccion_credito_dos;
                                }
                                if (isset($request->fecha_transaccion_credito_dos)) {
                                    $cobro_dos->fecha_transaccion = $request->fecha_transaccion_credito_dos;
                                }
                                $cobro_dos->cuenta_bancaria_id = $cuenta_bancaria->id;
                                $cobro_dos->save();

                                $movimiento_caja = new MovimientoCaja();
                                $movimiento_caja->fecha = Carbon::now();
                                $movimiento_caja->caja_destino_id = $caja->caja_id;
                                $movimiento_caja->sentido = 'D';
                                $movimiento_caja->monto = $cobro_dos->monto;
                                $movimiento_caja->tipo_movimiento_id = 12; //aca va el ID del movimiento de caja del tipo Venta
                                $movimiento_caja->motivo = 'FACTURA N° ' . $venta->numero_factura . ' COBRADO CON TARJETA DE CREDITO DEL BANCO ' . $cobro_dos->banco->nombre . '. N° Y FECHA DE TRANSACCION: ' . $cobro_dos->numero_transaccion . ' - ' . $cobro_dos->fecha_transaccion;
                                $movimiento_caja->venta_id = $venta->id;
                                $movimiento_caja->estado = 'PE';
                                $movimiento_caja->creado_por_id = Auth::id();
                                $movimiento_caja->aprobado_por_id = Auth::id();
                                $movimiento_caja->save();

                                if ($cuenta_bancaria->moneda_id == 1) {
                                    $monto = $cobro_dos->monto;
                                } else {
                                    $cotizacion = Cotizacion::orderBy('id', 'desc')->precio_venta;
                                    $monto = $cobro_dos->monto / $cotizacion;
                                }

                                $movimiento_banco = new MovimientoBanco();
                                $movimiento_banco->fecha = Carbon::now();
                                $movimiento_banco->cuenta_bancaria_destino_id = $cuenta_bancaria->id;
                                $movimiento_banco->sentido = 'D';
                                $movimiento_banco->monto = $monto;
                                $movimiento_banco->tipo_movimiento_id = 14; //aca va el ID del movimiento de banco del tipo Venta
                                $movimiento_banco->motivo = 'FACTURA N° ' . $venta->numero_factura . ' COBRADO CON TARJETA DE CREDITO DEL BANCO ' . $cobro_dos->banco->nombre . '. N° Y FECHA DE TRANSACCION: ' . $cobro_dos->numero_transaccion . ' - ' . $cobro_dos->fecha_transaccion;
                                $movimiento_banco->venta_id = $venta->id;
                                $movimiento_banco->estado = 'AP';
                                $movimiento_banco->creado_por_id = Auth::id();
                                $movimiento_banco->aprobado_por_id = Auth::id();
                                $movimiento_banco->save();

                                //VERIFICAR LUEGO PARA HACER LOS CALCULOS DE % DE BANCARD
                                // $cuenta_bancaria->monto = $cuenta_bancaria->monto + $monto;
                                // $cuenta_bancaria->save();

                                $cuenta_contable_cobro_dos = CuentaContable::where('id', $cuenta_bancaria->cuenta_ingreso_id)->first();
                                if (!$cuenta_contable_cobro_dos) {
                                    return back()->with('error-message', 'Hubo un problema al realizar la venta. La cuenta contable de la cuenta bancaria no se encuentra seleccionada.');
                                } else {
                                    $cuenta_contable_cobro_dos->monto = $cobro_dos->monto;
                                    $cuenta_contable_cobro_dos->tipo = 'BANCO';
                                    $cuenta_contable_cobro_dos->caja = $cuenta_bancaria->banco->nombre . ' ' . $cuenta_bancaria->numero_cuenta;
                                    $cuenta_contable_cobro_dos->centro_costo = $centro_costo;
                                    $cuenta_contable_cobro_dos->subcentro_costo = $subcentro_costo;
                                }
                                break;
                            case 4: //transferencia
                                $caja = UsuarioCaja::where('usuario_id', Auth::id())->first();

                                $cobro_dos->caja_id = $caja->caja_id;
                                if (isset($request->banco_transferencia_dos)) {
                                    $cobro_dos->banco_id = $request->banco_transferencia_dos;
                                }
                                if (isset($request->numero_transaccion_transferencia_dos)) {
                                    $cobro_dos->numero_transaccion = $request->numero_transaccion_transferencia_dos;
                                }
                                if (isset($request->fecha_transaccion_transferencia_dos)) {
                                    $cobro_dos->fecha_transaccion = $request->fecha_transaccion_transferencia_dos;
                                }
                                if (isset($request->cuenta_bancaria_transferencia_dos)) {
                                    $cobro_dos->cuenta_bancaria_id = $request->cuenta_bancaria_transferencia_dos;
                                }
                                $cobro_dos->save();

                                $movimiento_caja = new MovimientoCaja();
                                $movimiento_caja->fecha = Carbon::now();
                                $movimiento_caja->caja_destino_id = $caja->caja_id;
                                $movimiento_caja->sentido = 'D';
                                $movimiento_caja->monto = $cobro_dos->monto;
                                $movimiento_caja->tipo_movimiento_id = 12; //aca va el ID del movimiento de caja del tipo Venta
                                $movimiento_caja->motivo = 'FACTURA N° ' . $venta->numero_factura . ' COBRADO CON TRANSFERENCIA DESDE EL BANCO ' . $cobro_dos->banco->nombre . 'A LA CUENTA BANCARIA ' . $cobro_dos->cuentaBancaria->banco->nombre . ' - ' . $cobro_dos->cuentaBancaria->numero_cuenta . '. N° Y FECHA DE TRANSACCION: ' . $cobro_dos->numero_transaccion . ' - ' . $cobro_dos->fecha_transaccion;
                                $movimiento_caja->venta_id = $venta->id;
                                $movimiento_caja->estado = 'AP';
                                $movimiento_caja->creado_por_id = Auth::id();
                                $movimiento_caja->aprobado_por_id = Auth::id();
                                $movimiento_caja->save();

                                $cuenta_bancaria = CuentaBancaria::findOrFail($cobro_dos->cuenta_bancaria_id);

                                $movimiento_banco = new MovimientoBanco();
                                $movimiento_banco->fecha = Carbon::now();
                                $movimiento_banco->cuenta_bancaria_destino_id = $cuenta_bancaria->id;
                                $movimiento_banco->sentido = 'D';
                                $movimiento_banco->monto = $cobro_dos->monto;
                                $movimiento_banco->tipo_movimiento_id = 14; //aca va el ID del movimiento de banco del tipo Venta
                                $movimiento_banco->motivo = 'FACTURA N° ' . $venta->numero_factura . ' COBRADO CON TRANSFERENCIA DESDE EL BANCO ' . $cobro_dos->banco->nombre . 'A LA CUENTA BANCARIA ' . $cobro_dos->cuentaBancaria->banco->nombre . ' - ' . $cobro_dos->cuentaBancaria->numero_cuenta . '. N° Y FECHA DE TRANSACCION: ' . $cobro_dos->numero_transaccion . ' - ' . $cobro_dos->fecha_transaccion;
                                $movimiento_banco->venta_id = $venta->id;
                                $movimiento_banco->estado = 'PE';
                                $movimiento_banco->creado_por_id = Auth::id();
                                $movimiento_banco->save();

                                $cuenta_contable_cobro_dos = CuentaContable::where('id', $cuenta_bancaria->cuenta_ingreso_id)->first();
                                if (!$cuenta_contable_cobro_dos) {
                                    return back()->with('error-message', 'Hubo un problema al realizar la venta. La cuenta contable de la cuenta bancaria no se encuentra seleccionada.');
                                } else {
                                    $cuenta_contable_cobro_dos->monto = $cobro_dos->monto;
                                    $cuenta_contable_cobro_dos->tipo = 'BANCO';
                                    $cuenta_contable_cobro_dos->cuenta_bancaria = $cuenta_bancaria->banco->nombre . ' ' . $cuenta_bancaria->numero_cuenta;
                                    $cuenta_contable_cobro_dos->centro_costo = $centro_costo;
                                    $cuenta_contable_cobro_dos->subcentro_costo = $subcentro_costo;
                                }
                                break;
                            case 5: //deposito
                                $caja = UsuarioCaja::where('usuario_id', Auth::id())->first();

                                $cobro_dos->caja_id = $caja->caja_id;
                                if (isset($request->numero_transaccion_deposito_dos)) {
                                    $cobro_dos->numero_transaccion = $request->numero_transaccion_deposito_dos;
                                }
                                if (isset($request->fecha_transaccion_deposito_dos)) {
                                    $cobro_dos->fecha_transaccion = $request->fecha_transaccion_deposito_dos;
                                }
                                if (isset($request->cuenta_bancaria_deposito_dos)) {
                                    $cobro_dos->cuenta_bancaria_id = $request->cuenta_bancaria_deposito_dos;
                                }
                                $cobro_dos->save();

                                $movimiento_caja = new MovimientoCaja();
                                $movimiento_caja->fecha = Carbon::now();
                                $movimiento_caja->caja_destino_id = $caja->caja_id;
                                $movimiento_caja->sentido = 'D';
                                $movimiento_caja->monto = $cobro_dos->monto;
                                $movimiento_caja->tipo_movimiento_id = 12; //aca va el ID del movimiento de caja del tipo Venta
                                $movimiento_caja->motivo = 'FACTURA N° ' . $venta->numero_factura . ' COBRADO CON DEPOSITO BANCARIO A LA CUENTA BANCARIA ' . $cobro_dos->cuentaBancaria->banco->nombre . ' - ' . $cobro_dos->cuentaBancaria->numero_cuenta . '. N° Y FECHA DE TRANSACCION: ' . $cobro_dos->numero_transaccion . ' - ' . $cobro_dos->fecha_transaccion;
                                $movimiento_caja->venta_id = $venta->id;
                                $movimiento_caja->estado = 'AP';
                                $movimiento_caja->creado_por_id = Auth::id();
                                $movimiento_caja->aprobado_por_id = Auth::id();
                                $movimiento_caja->save();

                                $cuenta_bancaria = CuentaBancaria::findOrFail($cobro_dos->cuenta_bancaria_id);

                                $movimiento_banco = new MovimientoBanco();
                                $movimiento_banco->fecha = Carbon::now();
                                $movimiento_banco->cuenta_bancaria_destino_id = $cuenta_bancaria->id;
                                $movimiento_banco->sentido = 'D';
                                $movimiento_banco->monto = $cobro_dos->monto;
                                $movimiento_banco->tipo_movimiento_id = 14; //aca va el ID del movimiento de banco del tipo Venta
                                $movimiento_banco->motivo = 'FACTURA N° ' . $venta->numero_factura . ' COBRADO CON DEPOSITO BANCARIO A LA CUENTA BANCARIA ' . $cobro_dos->cuentaBancaria->banco->nombre . ' - ' . $cobro_dos->cuentaBancaria->numero_cuenta . '. N° Y FECHA DE TRANSACCION: ' . $cobro_dos->numero_transaccion . ' - ' . $cobro_dos->fecha_transaccion;
                                $movimiento_banco->venta_id = $venta->id;
                                $movimiento_banco->estado = 'PE';
                                $movimiento_banco->creado_por_id = Auth::id();
                                $movimiento_banco->save();

                                $cuenta_contable_cobro_dos = CuentaContable::where('id', $cuenta_bancaria->cuenta_ingreso_id)->first();
                                if (!$cuenta_contable_cobro_dos) {
                                    return back()->with('error-message', 'Hubo un problema al realizar la venta. La cuenta contable de la cuenta bancaria no se encuentra seleccionada.');
                                } else {
                                    $cuenta_contable_cobro_dos->monto = $cobro_dos->monto;
                                    $cuenta_contable_cobro_dos->tipo = 'BANCO';
                                    $cuenta_contable_cobro_dos->cuenta_bancaria = $cuenta_bancaria->banco->nombre . ' ' . $cuenta_bancaria->numero_cuenta;
                                    $cuenta_contable_cobro_dos->centro_costo = $centro_costo;
                                    $cuenta_contable_cobro_dos->subcentro_costo = $subcentro_costo;
                                }
                                break;
                            default:
                                break;
                        }
                        break;
                    default:
                        break;
                }
                $cobro->save();

                if ($cuentas_contables_ventas->count() > 0 && $cuenta_contable_cobro) {
                    $old_asiento_contable = AsientoContable::orderBy('id', 'desc')->first();
                    if ($old_asiento_contable) {
                        $numero_asiento = $old_asiento_contable->numero + 1;
                    } else {
                        $numero_asiento = 1;
                    }

                    $asiento_contable = new AsientoContable();
                    $asiento_contable->numero = $numero_asiento;
                    $asiento_contable->fecha = Carbon::now();
                    $asiento_contable->origen = 'VENTA';
                    $asiento_contable->venta_id = $venta->id;
                    $asiento_contable->moneda_id = 1;
                    $asiento_contable->cotizacion_id = null;
                    $asiento_contable->unidad_negocio_id = $request->unidad_negocio;
                    $asiento_contable->subunidad_negocio_id = $request->subunidad_negocio;
                    $asiento_contable->save();

                    foreach($cuentas_contables_ventas as $cuenta_venta) {
                        $asiento_contable_detalle = new AsientoContableDetalle();
                        $asiento_contable_detalle->asiento_id = $asiento_contable->id;
                        $asiento_contable_detalle->cuenta_contable_id = $cuenta_venta->id;
                        $asiento_contable_detalle->descripcion = 'VENTA S/ FACTURA ' . $numero_factura . ' - CLIENTE: ' . $venta->cliente->nombre;
                        $asiento_contable_detalle->haber = $cuenta_venta->monto;
                        $asiento_contable_detalle->centro_costo_id = $cuenta_venta->centro_costo;
                        $asiento_contable_detalle->subcentro_costo_id = $cuenta_venta->subcentro_costo;
                        $asiento_contable_detalle->save();

                        $saldo_cuenta_contable = SaldoCuentaContable::where('id', $asiento_contable_detalle->cuenta_contable_id)->first();
                        if ($saldo_cuenta_contable) {
                            $saldo_cuenta_contable->saldo = $saldo_cuenta_contable->saldo + $asiento_contable_detalle->haber;
                            $saldo_cuenta_contable->save();
                        }

                        $saldo_cuenta_contable_detalle = new SaldoCuentaContableDetalle();
                        $saldo_cuenta_contable_detalle->saldo_cuenta_contable_id = $saldo_cuenta_contable->id;
                        $saldo_cuenta_contable_detalle->mes = Carbon::now()->format('m');
                        $saldo_cuenta_contable_detalle->anho = Carbon::now()->format('Y');
                        $saldo_cuenta_contable_detalle->haber = $asiento_contable_detalle->haber;
                        $saldo_cuenta_contable_detalle->save();
                    }

                    $asiento_contable_detalle = new AsientoContableDetalle();
                    $asiento_contable_detalle->asiento_id = $asiento_contable->id;
                    $asiento_contable_detalle->cuenta_contable_id = $cuenta_contable_cobro->id;
                    $asiento_contable_detalle->descripcion = 'COBRO S/ FACTURA ' . $numero_factura . ' - CLIENTE: ' . $venta->cliente->nombre;
                    $asiento_contable_detalle->debe = $cuenta_contable_cobro->monto;
                    $asiento_contable_detalle->centro_costo_id = $cuenta_contable_cobro->centro_costo;
                    $asiento_contable_detalle->subcentro_costo_id = $cuenta_contable_cobro->subcentro_costo;
                    $asiento_contable_detalle->save();

                    if ($bandera_nota_credito) {
                        $asiento_contable_detalle = new AsientoContableDetalle();
                        $asiento_contable_detalle->asiento_id = $asiento_contable->id;
                        $asiento_contable_detalle->cuenta_contable_id = $cuenta_contable_cobro_dos->id;
                        $asiento_contable_detalle->descripcion = 'COBRO S/ FACTURA ' . $numero_factura . ' - CLIENTE: ' . $venta->cliente->nombre;
                        $asiento_contable_detalle->debe = $cuenta_contable_cobro_dos->monto;
                        $asiento_contable_detalle->centro_costo_id = $cuenta_contable_cobro_dos->centro_costo;
                        $asiento_contable_detalle->subcentro_costo_id = $cuenta_contable_cobro_dos->subcentro_costo;
                        $asiento_contable_detalle->save();

                        if ($cuenta_contable_cobro_dos->tipo == 'CAJA') {
                            $saldo_cuenta_contable = SaldoCuentaContable::where('id', $asiento_contable_detalle->cuenta_contable_id)->first();
                            if ($saldo_cuenta_contable) {
                                $saldo_cuenta_contable->saldo = $saldo_cuenta_contable->saldo + $asiento_contable_detalle->debe;
                                $saldo_cuenta_contable->save();
                            }

                            $saldo_cuenta_contable_detalle = new SaldoCuentaContableDetalle();
                            $saldo_cuenta_contable_detalle->saldo_cuenta_contable_id = $saldo_cuenta_contable->id;
                            $saldo_cuenta_contable_detalle->mes = Carbon::now()->format('m');
                            $saldo_cuenta_contable_detalle->anho = Carbon::now()->format('Y');
                            $saldo_cuenta_contable_detalle->debe = $asiento_contable_detalle->debe;
                            $saldo_cuenta_contable_detalle->save();
                        }
                    }

                    if ($cuenta_contable_cobro->tipo == 'CAJA') {
                        $saldo_cuenta_contable = SaldoCuentaContable::where('id', $asiento_contable_detalle->cuenta_contable_id)->first();
                        if ($saldo_cuenta_contable) {
                            $saldo_cuenta_contable->saldo = $saldo_cuenta_contable->saldo + $asiento_contable_detalle->debe;
                            $saldo_cuenta_contable->save();
                        }

                        $saldo_cuenta_contable_detalle = new SaldoCuentaContableDetalle();
                        $saldo_cuenta_contable_detalle->saldo_cuenta_contable_id = $saldo_cuenta_contable->id;
                        $saldo_cuenta_contable_detalle->mes = Carbon::now()->format('m');
                        $saldo_cuenta_contable_detalle->anho = Carbon::now()->format('Y');
                        $saldo_cuenta_contable_detalle->debe = $asiento_contable_detalle->debe;
                        $saldo_cuenta_contable_detalle->save();
                    }
                }
            } else if ($venta->forma_pago == 'CR') {
                $cuenta_contable_cobro = CuentaContable::where('id', 18)->first(); //aca va la cuenta de cuentas a cobrar
                $cuenta_contable_cobro->monto = $venta->monto_total;
                $cuenta_contable_cobro->tipo = 'CREDITO';

                if ($cuentas_contables_ventas->count() > 0 && $cuenta_contable_cobro) {
                    $old_asiento_contable = AsientoContable::orderBy('id', 'desc')->first();
                    if ($old_asiento_contable) {
                        $numero_asiento = $old_asiento_contable->numero + 1;
                    } else {
                        $numero_asiento = 1;
                    }

                    $asiento_contable = new AsientoContable();
                    $asiento_contable->numero = $numero_asiento;
                    $asiento_contable->fecha = Carbon::now();
                    $asiento_contable->origen = 'VENTA';
                    $asiento_contable->venta_id = $venta->id;
                    $asiento_contable->moneda_id = 1;
                    $asiento_contable->cotizacion_id = null;
                    $asiento_contable->unidad_negocio_id = $request->unidad_negocio;
                    $asiento_contable->subunidad_negocio_id = $request->subunidad_negocio;
                    $asiento_contable->save();

                    foreach($cuentas_contables_ventas as $cuenta_venta) {
                        $asiento_contable_detalle = new AsientoContableDetalle();
                        $asiento_contable_detalle->asiento_id = $asiento_contable->id;
                        $asiento_contable_detalle->cuenta_contable_id = $cuenta_venta->id;
                        $asiento_contable_detalle->descripcion = 'VENTA S/ FACTURA ' . $numero_factura . ' - CLIENTE: ' . $venta->cliente->nombre;
                        $asiento_contable_detalle->haber = $cuenta_venta->monto;
                        $asiento_contable_detalle->centro_costo_id = $cuenta_venta->centro_costo;
                        $asiento_contable_detalle->subcentro_costo_id = $cuenta_venta->subcentro_costo;
                        $asiento_contable_detalle->save();

                        $saldo_cuenta_contable = SaldoCuentaContable::where('id', $asiento_contable_detalle->cuenta_contable_id)->first();
                        if ($saldo_cuenta_contable) {
                            $saldo_cuenta_contable->saldo = $saldo_cuenta_contable->saldo + $asiento_contable_detalle->haber;
                            $saldo_cuenta_contable->save();
                        }

                        $saldo_cuenta_contable_detalle = new SaldoCuentaContableDetalle();
                        $saldo_cuenta_contable_detalle->saldo_cuenta_contable_id = $saldo_cuenta_contable->id;
                        $saldo_cuenta_contable_detalle->mes = Carbon::now()->format('m');
                        $saldo_cuenta_contable_detalle->anho = Carbon::now()->format('Y');
                        $saldo_cuenta_contable_detalle->haber = $asiento_contable_detalle->haber;
                        $saldo_cuenta_contable_detalle->save();
                    }

                    $asiento_contable_detalle = new AsientoContableDetalle();
                    $asiento_contable_detalle->asiento_id = $asiento_contable->id;
                    $asiento_contable_detalle->cuenta_contable_id = $cuenta_contable_cobro->id;
                    $asiento_contable_detalle->descripcion = 'CREDITO S/ FACTURA ' . $numero_factura . ' - CLIENTE: ' . $venta->cliente->nombre;
                    $asiento_contable_detalle->debe = $cuenta_contable_cobro->monto;
                    $asiento_contable_detalle->centro_costo_id = $cuenta_venta->centro_costo;
                    $asiento_contable_detalle->subcentro_costo_id = $cuenta_venta->subcentro_costo;
                    $asiento_contable_detalle->save();

                    $saldo_cuenta_contable = SaldoCuentaContable::where('id', $asiento_contable_detalle->cuenta_contable_id)->first();
                    if ($saldo_cuenta_contable) {
                        $saldo_cuenta_contable->saldo = $saldo_cuenta_contable->saldo + $asiento_contable_detalle->debe;
                        $saldo_cuenta_contable->save();
                    }

                    $saldo_cuenta_contable_detalle = new SaldoCuentaContableDetalle();
                    $saldo_cuenta_contable_detalle->saldo_cuenta_contable_id = $saldo_cuenta_contable->id;
                    $saldo_cuenta_contable_detalle->mes = Carbon::now()->format('m');
                    $saldo_cuenta_contable_detalle->anho = Carbon::now()->format('Y');
                    $saldo_cuenta_contable_detalle->debe = $asiento_contable_detalle->debe;
                    $saldo_cuenta_contable_detalle->save();
                }
            }

            DB::commit();

            return redirect()->route('ventas.index')->with('success-message', 'La venta N° ' . $venta->numero_factura . ' fue creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('ventas.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('anular_ventas');

        DB::beginTransaction();

        try {
            $venta = Venta::with('ventaDetalles', 'cobros')->findOrFail($id);
            if (!Carbon::parse($venta->fecha)->isSameMonth(Carbon::now())) {
                return back()->with('error-message', 'No se puede anular la venta porque no pertenece al mes y año actual.');
            }

            $venta->anulado_por_id = Auth::id();
            $venta->estado = 'IN';
            $venta->save();

            foreach ($venta->cobros as $cobro) {
                if ($cobro->forma_pago_id == 1) {
                    $caja = Caja::findOrFail($cobro->caja_id);
                    $caja->monto = $caja->monto - $cobro->monto;
                    $caja->save();
                } elseif ($cobro->forma_pago_id == 6) {
                    $nota_credito = NotaCredito::where('id', $cobro->nota_credito_id)->first();
                    if ($nota_credito) {
                        $nota_credito->estado = 'AC';
                        $nota_credito->save();
                    }
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

            foreach ($venta->ventaDetalles as $detalle) {
                $pago_matriculacion = PagoMatriculacion::where('id', $detalle->pago_matriculacion_id)->first();
                $pago_solicitud = PagoSolicitud::where('id', $detalle->pago_solicitud_id)->first();
                $pago_tesis = PagoTesis::where('id', $detalle->pago_tesis_id)->first();
                $pago_tutoria = PagoTutoria::where('id', $detalle->pago_tutoria_id)->first();
                $pago_ubs = PagoInscripcionUbs::where('id', $detalle->pago_inscripcion_ubs_id)->first();
                $pago_tesis_ubs = PagoTesisUbs::where('id', $detalle->pago_tesis_ubs_id)->first();
                if ($pago_matriculacion) {
                    $pago_matriculacion->saldo = $pago_matriculacion->saldo + $venta->monto_total;
                    if ($pago_matriculacion->saldo == $pago_matriculacion->monto) {
                        $pago_matriculacion->estado = 'PE';
                        $pago_matriculacion->fecha_pago = null;
                    } elseif ($pago_matriculacion->saldo == 0) {
                        $pago_matriculacion->estado = 'CA';
                    } else {
                        $pago_matriculacion->estado = 'PA';
                    }
                    $pago_matriculacion->save();
                } elseif ($pago_solicitud) {
                    $solicitud = Solicitud::findOrFail($pago_solicitud->solicitud_id);
                    if ($solicitud->estado == 'PA') {
                        $solicitud->estado = 'AP';
                        $solicitud->fecha_pago = null;
                        $solicitud->save();

                        $pago_solicitud->saldo = $pago_solicitud->saldo + $venta->monto_total;
                        if ($pago_solicitud->saldo == $pago_solicitud->monto) {
                            $pago_solicitud->estado = 'PE';
                            $pago_solicitud->fecha_pago = null;
                        } elseif ($pago_solicitud->saldo == 0) {
                            $pago_solicitud->estado = 'CA';
                        } else {
                            $pago_solicitud->estado = 'PA';
                        }
                        $pago_solicitud->save();
                    }
                } elseif ($pago_tesis) {
                    $inscripcion = InscripcionTemaTesis::findOrFail($pago_tesis->inscripcion_id);
                    if ($inscripcion->estado == 'PA') {
                        $inscripcion->estado = 'BA';
                        $inscripcion->fecha_pago = null;
                        $inscripcion->save();

                        $pago_tesis->saldo = $pago_tesis->saldo + $venta->monto_total;
                        if ($pago_tesis->saldo == $pago_tesis->monto) {
                            $pago_tesis->estado = 'PE';
                            $pago_tesis->fecha_pago = null;
                        } elseif ($pago_tesis->saldo == 0) {
                            $pago_tesis->estado = 'CA';
                        } else {
                            $pago_tesis->estado = 'PA';
                        }
                        $pago_tesis->save();
                    }
                } elseif ($pago_tutoria) {
                    $tutoria = Tutoria::findOrFail($pago_tutoria->tutoria_id);
                    if ($tutoria->estado == 'PA') {
                        $tutoria->estado = 'IN';
                        $tutoria->save();

                        $pago_tutoria->saldo = $pago_tutoria->saldo + $venta->monto_total;
                        if ($pago_tutoria->saldo == $pago_tutoria->monto) {
                            $pago_tutoria->estado = 'PE';
                            $pago_tutoria->fecha_pago = null;
                        } elseif ($pago_tutoria->saldo == 0) {
                            $pago_tutoria->estado = 'CA';
                        } else {
                            $pago_tutoria->estado = 'PA';
                        }
                        $pago_tutoria->save();
                    }
                } elseif ($pago_ubs) {
                    $pago_ubs->saldo = $pago_ubs->saldo + $venta->monto_total;
                    if ($pago_ubs->saldo == $pago_ubs->monto) {
                        $pago_ubs->estado = 'PE';
                        $pago_ubs->fecha_pago = null;
                    } elseif ($pago_ubs->saldo == 0) {
                        $pago_ubs->estado = 'CA';
                    } else {
                        $pago_ubs->estado = 'PA';
                    }
                    $pago_ubs->save();
                } elseif ($pago_tesis_ubs) {
                    $inscripcion = InscripcionTemaTesisUbs::findOrFail($pago_tesis_ubs->inscripcion_id);
                    if ($inscripcion->estado == 'PA') {
                        $inscripcion->estado = 'BA';
                        $inscripcion->fecha_pago = null;

                        $pago_tesis_ubs->saldo = $pago_tesis_ubs->saldo + $venta->monto_total;
                        if ($pago_tesis_ubs->saldo == $pago_tesis_ubs->monto) {
                            $pago_tesis_ubs->estado = 'PE';
                            $pago_tesis_ubs->fecha_pago = null;
                            $inscripcion->pagado = 'PE';
                        } elseif ($pago_tesis_ubs->saldo == 0) {
                            $pago_tesis_ubs->estado = 'CA';
                            $inscripcion->pagado = 'CA';
                        } else {
                            $pago_tesis_ubs->estado = 'PA';
                            $inscripcion->pagado = 'PA';
                        }
                        $pago_tesis_ubs->save();
                        $inscripcion->save();
                    }
                }
            }

            $movimientos_cajas = MovimientoCaja::where('venta_id', $venta->id)->get();
            if ($movimientos_cajas->count() > 0) {
                foreach ($movimientos_cajas as $movimiento) {
                    $movimiento->estado = 'IN';
                    $movimiento->save();
                }
            }

            $movimientos_bancos = MovimientoBanco::where('venta_id', $venta->id)->get();
            if ($movimientos_bancos->count() > 0) {
                foreach ($movimientos_bancos as $movimiento) {
                    $movimiento->estado = 'IN';
                    $movimiento->save();
                }
            }

            $asiento_contable = AsientoContable::where('venta_id', $venta->id)->first();
            foreach ($asiento_contable->detalles as $detalle) {
                $cuenta_contable = CuentaContable::where('id', $detalle->cuenta_contable_id)->first();
                if ($cuenta_contable) {
                    $saldo_cuenta_contable = SaldoCuentaContable::where('cuenta_contable_id', $cuenta_contable->id)->first();
                    if ($saldo_cuenta_contable) {
                        if ($detalle->debe) {
                            $saldo_cuenta_contable->saldo = $saldo_cuenta_contable->saldo + $detalle->debe;
                        } elseif ($detalle->haber) {
                            $saldo_cuenta_contable->saldo = $saldo_cuenta_contable->saldo - $detalle->haber;
                        }
                        $saldo_cuenta_contable->saldo = $saldo_cuenta_contable->saldo - $detalle->haber;
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

            return redirect()->route('ventas.index')->with('error-message','La venta N° ' . $venta->numero_factura . ' fue anulada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('ventas.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_ventas');

        DB::beginTransaction();

        try {
            $venta = Venta::with('ventaDetalles', 'cobros')->findOrFail($id);

            if ($venta->estado == 'AC') {
                return back()->with('error-message', 'La venta no se puede eliminar. Su estado actualmente es facturado.');
            }

            $venta_detalles = VentaDetalle::where('venta_id', $venta->id)->delete();
            $cobros = Cobro::where('venta_id', $venta->id)->delete();
            $movimientos_cajas = MovimientoCaja::where('venta_id', $venta->id)->delete();
            $movimientos_bancos = MovimientoBanco::where('venta_id', $venta->id)->delete();
            $asiento_contable = AsientoContable::where('venta_id', $venta->id)->first();
            $asientos_contables_detalles = AsientoContableDetalle::where('asiento_id', $asiento_contable->id)->delete();
            $asiento_contable->delete();

            $venta->delete();

            DB::commit();

            return redirect()->route('ventas.index')->with('success-message','La venta N° ' . $venta->numero_factura . ' fue eliminada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('ventas.index')->with('error-message', 'La venta N° ' . $venta->numero_factura . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('ventas.index')->with('error-message', $e->getMessage());
            }
        }
    }

    public function get_subunidades_negocios($id)
    {
        $this->authorize('crear_ventas');

        DB::beginTransaction();

        try {
            $subunidades_negocios = SubunidadNegocioContable::where('unidad_id', $id)->where('estado', 'AC')->get();

            return response()->json([
                'subunidades_negocios' => $subunidades_negocios,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('ventas.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_subcentros_costos($id)
    {
        $this->authorize('crear_ventas');

        DB::beginTransaction();

        try {
            $subcentros_costos = SubcentroCostoContable::where('centro_costo_id', $id)->where('estado', 'AC')->get();

            return response()->json([
                'subcentros_costos' => $subcentros_costos,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('ventas.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_notas_creditos($id)
    {
        $this->authorize('crear_ventas');

        DB::beginTransaction();

        try {
            $notas_creditos = NotaCredito::where('cliente_id', $id)
                                ->where('pago_reversado', false)
                                ->where('estado', 'AC')
                                ->get();
            foreach ($notas_creditos as $nota_credito) {
                $nota_credito->monto_total = number_format($nota_credito->monto_total, 0, ',', '.');
            }

            return response()->json([
                'notas_creditos' => $notas_creditos,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('ventas.index')->with('error-message', $e->getMessage());
        }
    }
}
