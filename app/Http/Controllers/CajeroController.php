<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Luecano\NumeroALetras\NumeroALetras;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

use App\Models\Caja;
use App\Models\User;
use App\Models\UsuarioCaja;
use App\Models\Alumno;
use App\Models\AlumnoCliente;
use App\Models\PagoMatriculacion;
use App\Models\PagoSolicitud;
use App\Models\PagoTesis;
use App\Models\TutoriaAlumno;
use App\Models\TutoriaPrecio;
use App\Models\PagoTutoria;
use App\Models\PagoInscripcionUbs;
use App\Models\FormaPago;
use App\Models\Banco;
use App\Models\CuentaBancaria;
use App\Models\PuntoImpresion;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Cobro;
use App\Models\MovimientoCaja;
use App\Models\MovimientoBanco;
use App\Models\Cotizacion;
use App\Models\Empresa;
use App\Models\ArqueoCaja;
use App\Models\Solicitud;
use App\Models\InscripcionTemaTesis;
use App\Models\FechaDefensaTesis;
use App\Models\InscripcionUbs;
use App\Models\InscripcionTemaTesisUbs;
use App\Models\PagoTesisUbs;
use App\Models\Carrera;
use App\Models\Curso;
use App\Models\Articulo;
use App\Models\CuentaContable;
use App\Models\AsientoContable;
use App\Models\AsientoContableDetalle;
use App\Models\SaldoCuentaContable;
use App\Models\SaldoCuentaContableDetalle;
use App\Models\Convenio;
use App\Models\NotaCredito;

class CajeroController extends Controller
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

    public function create()
    {
        $this->authorize('crear_ventas_cajero');

        try {
            $alumnos = Alumno::where('estado', 'AC')->orderBy('primer_nombre', 'asc')->get();
            $formas_pagos = FormaPago::where('estado', 'AC')->orderBy('id', 'asc')->get();
            $bancos = Banco::where('estado', 'AC')->orderBy('nombre', 'asc')->get();
            $cuentas_bancarias = CuentaBancaria::with(['banco' => function ($query) {
                $query->orderBy('nombre', 'asc');
            }])
              ->where('moneda_id', 1)
              ->where('estado', 'AC')
              ->get();

            $punto_impresion = PuntoImpresion::where('tipo_documento_id', 1)->where('estado', 'AC')->orderBy('id', 'desc')->first();
            if (!$punto_impresion) {
                return redirect()->route('root')->with('error-message', 'No se encuentra registrado ningún punto de impresión activo en el sistema para realizar la venta.');
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

            $fecha_hoy = Carbon::now()->format('d/m/Y');

            $numero_int = intval($numero);
            $punto_impresion_desde_int = intval($punto_impresion->numero_desde);
            $punto_impresion_hasta_int = intval($punto_impresion->numero_hasta);
            if ($numero_int >= $punto_impresion_desde_int && $numero_int <= $punto_impresion_hasta_int) {
                $numero_factura = $punto_impresion->codigo . $numero;
            } else {
                return redirect()->route('root')->with('error-message', 'El punto de impresión activo no se encuentra dentro del rango para realizar la factura ' . $numero_factura . '.');
            }

            $caja = UsuarioCaja::where('usuario_id', Auth::id())->first();
            if (!$caja) {
                return redirect()->route('root')->with('error-message', 'Usted no cuenta con una caja registrada a su nombre para realizar la venta.');
            }

            $descuentos = Convenio::with('detalle')->where('tipo', 'DE')->where('estado', 'AC')->orderBy('nombre', 'asc')->get();

            return view('cajeros.create')->with(compact('alumnos', 'formas_pagos', 'bancos', 'cuentas_bancarias', 'fecha_hoy', 'numero_factura', 'caja', 'descuentos'));
        } catch (\Exception $e) {
            return redirect()->route('ventas.index')->with('error-message', $e->getMessage());
        }

    }

    public function store(Request $request)
    {
        $this->authorize('crear_ventas_cajero');

        $request->validate([
            'alumno' => ['required', 'numeric'],
            'cliente' => ['required', 'numeric'],
            'numero_factura' => 'required',
            'descuento_aplicado' => 'nullable',

            'detalles' => ['required', 'array'],
            'detalles.*.descripcion' => 'required',
            'detalles.*.bruto' => 'required',
            'detalles.*.descuento' => 'required',
            'detalles.*.a_pagar' => ['required', 'numeric'],

            'pagos' => ['required', 'array'],
            'pagos.*.forma_pago' => ['required', 'numeric'],

            'pagos.*.efectivo' => ['nullable', 'required_if:pagos.*.forma_pago,1'],

            'pagos.*.banco_debito' => ['nullable', 'numeric', 'required_if:pagos.*.forma_pago,2'],
            'pagos.*.monto_debito' => ['nullable', 'numeric', 'required_if:pagos.*.forma_pago,2'],
            'pagos.*.numero_transaccion_debito' => ['nullable', 'numeric', 'required_if:pagos.*.forma_pago,2'],
            'pagos.*.fecha_transaccion_debito' => ['nullable', 'date', 'required_if:pagos.*.forma_pago,2'],

            'pagos.*.banco_credito' => ['nullable', 'numeric', 'required_if:pagos.*.forma_pago,3'],
            'pagos.*.monto_credito' => ['nullable', 'numeric', 'required_if:pagos.*.forma_pago,3'],
            'pagos.*.numero_transaccion_credito' => ['nullable', 'numeric', 'required_if:pagos.*.forma_pago,3'],
            'pagos.*.fecha_transaccion_credito' => ['nullable', 'date', 'required_if:pagos.*.forma_pago,3'],

            'pagos.*.banco_transferencia' => ['nullable', 'numeric', 'required_if:pagos.*.forma_pago,4'],
            'pagos.*.cuenta_bancaria_transferencia' => ['nullable', 'numeric', 'required_if:pagos.*.forma_pago,4'],
            'pagos.*.numero_transaccion_transferencia' => ['nullable', 'numeric', 'required_if:pagos.*.forma_pago,4'],
            'pagos.*.fecha_transaccion_transferencia' => ['nullable', 'date', 'required_if:pagos.*.forma_pago,4'],
            'pagos.*.monto_transferencia' => ['nullable', 'numeric', 'required_if:pagos.*.forma_pago,4'],

            'pagos.*.cuenta_bancaria_deposito' => ['nullable', 'numeric', 'required_if:pagos.*.forma_pago,5'],
            'pagos.*.monto_deposito' => ['nullable', 'numeric', 'required_if:pagos.*.forma_pago,5'],
            'pagos.*.numero_transaccion_deposito' => ['nullable', 'numeric', 'required_if:pagos.*.forma_pago,5'],
            'pagos.*.fecha_transaccion_deposito' => ['nullable', 'date', 'required_if:pagos.*.forma_pago,5'],

            'pagos.*.nota_credito' => ['nullable', 'numeric', 'required_if:pagos.*.forma_pago,6'],
            'pagos.*.monto_nota_credito' => ['nullable', 'required_if:pagos.*.forma_pago,6'],

            'pagos.*.banco_cheque' => ['nullable', 'numeric', 'required_if:pagos.*.forma_pago,7'],
            'pagos.*.numero_cheque' => ['nullable', 'required_if:pagos.*.forma_pago,7'],
            'pagos.*.numero_serie_cheque' => ['nullable', 'required_if:pagos.*.forma_pago,7'],
            'pagos.*.emisor_cheque' => ['nullable', 'date', 'required_if:pagos.*.forma_pago,7'],
            'pagos.*.monto_cheque' => ['nullable', 'numeric', 'required_if:pagos.*.forma_pago,7'],
        ]);

        DB::beginTransaction();

        try {
            $caja = UsuarioCaja::where('usuario_id', Auth::id())->first();
            if (!$caja) {
                return back()->with('error-message', 'El usuario no cuenta con una caja registrada a su nombre.');
            }
            $numero_factura = $request->numero_factura;

            $punto_impresion = PuntoImpresion::where('tipo_documento_id', 1)->where('estado', 'AC')->orderBy('id', 'desc')->first();
            if (!$punto_impresion) {
                return back()->with('error-message', 'No se encuentra registrado ningún punto de impresión activo en el sistema para realizar la venta.');
            }

            $venta = new Venta();
            $venta->fecha = Carbon::now();
            $venta->alumno_id = $request->alumno;
            $venta->cliente_id = $request->cliente;
            $venta->punto_impresion_id = $punto_impresion->id;
            $venta->forma_pago = 'CO';
            $venta->numero_factura = $numero_factura;
            $venta->monto_total = $request->monto_total;

            if ($request->descuento_aplicado) {
                $venta->descuento_aplicado_id = $request->descuento_aplicado;
            }

            $venta->cargado_por_id = Auth::id();
            $venta->save();

            $cuentas_contables_ventas = collect();
            $cuentas_contables_descuentos = collect();

            foreach ($request->detalles as $detalle) {
                $venta_detalle = new VentaDetalle();
                $venta_detalle->venta_id = $venta->id;
                $venta_detalle->cantidad = 1;
                $venta_detalle->monto_bruto = str_replace('.', '', $detalle['bruto']);
                $venta_detalle->monto_neto = $detalle['a_pagar'];
                if ($venta_detalle->monto_bruto == ($venta_detalle->monto_neto + $detalle['descuento'])) {
                    $venta_detalle->descripcion = $detalle['descripcion'];
                } else {
                    $venta_detalle->descripcion = $detalle['descripcion'] . ' - PAGO PARCIAL';
                }
                $venta_detalle->descuento = $detalle['descuento'];

                if ($detalle['tipo'] == 'matriculacion') {
                    $venta_detalle->pago_matriculacion_id = $detalle['id'];
                    $pago_matriculacion = PagoMatriculacion::findOrFail($detalle['id']);
                    $pago_matriculacion->saldo = $pago_matriculacion->saldo - ($venta_detalle->monto_neto + $venta_detalle->descuento);
                    if ($pago_matriculacion->saldo == 0) {
                        $pago_matriculacion->estado = 'CA';
                    } else {
                        $pago_matriculacion->estado = 'PA';
                    }
                    $pago_matriculacion->fecha_pago = Carbon::now();
                    $pago_matriculacion->save();

                    $carrera = Carrera::find($pago_matriculacion->matriculacion->carrera_id);
                    if ($carrera) {
                        $articulo = Articulo::find($carrera->articulo_id);
                    } else {
                        return response()->json([
                            'mensaje' => 'Hubo un problema al realizar la venta. No es posible obtener la carrera del alumno.'
                        ]);
                    }

                    if ($articulo) {
                        switch ($pago_matriculacion->tipo) {
                            case 'MA':
                                $cuenta_contable_venta = CuentaContable::where('id', $articulo->detalle->cuenta_matricula_id)->first();
                                if (!$cuenta_contable_venta) {
                                    return response()->json([
                                        'mensaje' => 'Hubo un problema al realizar la venta. No es posible obtener la cuenta contable del artículo.'
                                    ]);
                                } else {
                                    if ($venta_detalle->descuento > 0) {
                                        $cuenta_contable_venta->monto = $venta_detalle->monto_bruto;
                                    } else {
                                        $cuenta_contable_venta->monto = $venta_detalle->monto_neto;
                                    }
                                    $cuentas_contables_ventas->push($cuenta_contable_venta);
                                }
                                break;
                            case 'SC':
                                $cuenta_contable_venta = CuentaContable::where('id', $articulo->detalle->cuenta_contado_id)->first();
                                if (!$cuenta_contable_venta) {
                                    return response()->json([
                                        'mensaje' => 'Hubo un problema al realizar la venta. No es posible obtener la cuenta contable del artículo.'
                                    ]);
                                } else {
                                    if ($venta_detalle->descuento > 0) {
                                        $cuenta_contable_venta->monto = $venta_detalle->monto_bruto;
                                    } else {
                                        $cuenta_contable_venta->monto = $venta_detalle->monto_neto;
                                    }
                                    $cuentas_contables_ventas->push($cuenta_contable_venta);
                                }
                                break;
                            default:
                                $cuenta_contable_venta = CuentaContable::where('id', $articulo->detalle->cuenta_cuota_id)->first();
                                if (!$cuenta_contable_venta) {
                                    return response()->json([
                                        'mensaje' => 'Hubo un problema al realizar la venta. No es posible obtener la cuenta contable del artículo.'
                                    ]);
                                } else {
                                    if ($venta_detalle->descuento > 0) {
                                        $cuenta_contable_venta->monto = $venta_detalle->monto_bruto;
                                    } else {
                                        $cuenta_contable_venta->monto = $venta_detalle->monto_neto;
                                    }
                                    $cuentas_contables_ventas->push($cuenta_contable_venta);
                                }
                                break;
                        }

                        if ($venta_detalle->descuento > 0) {
                            $cuenta_contable_descuento = CuentaContable::where('id', $articulo->detalle->cuenta_descuento_id)->first();
                            if (!$cuenta_contable_descuento) {
                                return response()->json([
                                    'mensaje' => 'Hubo un problema al realizar la venta. No es posible obtener la cuenta contable de descuentos del artículo.'
                                ]);
                            } else {
                                $cuenta_contable_descuento->monto = $venta_detalle->descuento;
                                $cuentas_contables_descuentos->push($cuenta_contable_descuento);
                            }
                        }
                    } else {
                        return response()->json([
                            'mensaje' => 'Hubo un problema al realizar la venta. No es posible obtener el artículo asociado a la carrera del alumno.'
                        ]);
                    }

                } else if ($detalle['tipo'] == 'solicitud') { //falta tagregar tipo tutoria
                    $venta_detalle->pago_solicitud_id = $detalle['id'];
                    $pago_solicitud = PagoSolicitud::findOrFail($detalle['id']);
                    $pago_solicitud->saldo = $pago_solicitud->saldo - ($venta_detalle->monto_neto + $venta_detalle->descuento);
                    if ($pago_solicitud->saldo == 0) {
                        $pago_solicitud->estado = 'CA';
                    } else {
                        $pago_solicitud->estado = 'PA';
                    }
                    $pago_solicitud->fecha_pago = Carbon::now();
                    $pago_solicitud->save();

                    $solicitud = Solicitud::findOrFail($pago_solicitud->solicitud_id);
                    $solicitud->fecha_pago = Carbon::now();
                    $solicitud->estado = 'PA';
                    $solicitud->save();

                    if ($solicitud->tipo_solicitud_id != 5) {
                        $articulo = Articulo::where('id', $solicitud->articulo_id)->first();
                        if ($articulo) {
                            switch ($solicitud->tipo_solicitud_id) {
                                case 1:
                                    $cuenta_contable_articulo = CuentaContable::where('id', $articulo->detalle->cuenta_certificado_id)->first()->id;
                                    break;
                                case 2:
                                    $cuenta_contable_articulo = CuentaContable::where('id', $articulo->detalle->cuenta_examen_suficiencia_id)->first()->id;
                                    break;
                                case 4:
                                    $cuenta_contable_articulo = CuentaContable::where('id', $articulo->detalle->cuenta_constancia_carrera_id)->first()->id;
                                    break;
                                default:
                                    break;
                            }

                            if (!$cuenta_contable_articulo) {
                                return response()->json([
                                    'mensaje' => 'Hubo un problema al realizar la venta. No es posible obtener la cuenta contable del artículo.'
                                ]);
                            }

                            $cuenta_contable_venta = CuentaContable::where('id', $cuenta_contable_articulo)->first();
                            if (!$cuenta_contable_venta) {
                                return response()->json([
                                    'mensaje' => 'Hubo un problema al realizar la venta. No es posible obtener la cuenta contable del artículo.'
                                ]);
                            } else {
                                $cuenta_contable_venta->monto = $venta_detalle->monto_bruto;
                                $cuentas_contables_ventas->push($cuenta_contable_venta);
                            }

                            if ($venta_detalle->descuento > 0) {
                                $cuenta_contable_descuento = CuentaContable::where('id', $articulo->detalle->cuenta_descuento_id)->first();
                                if (!$cuenta_contable_descuento) {
                                    return response()->json([
                                        'mensaje' => 'Hubo un problema al realizar la venta. No es posible obtener la cuenta contable de descuentos del artículo.'
                                    ]);
                                } else {
                                    $cuenta_contable_descuento->monto = $venta_detalle->descuento;
                                    $cuentas_contables_descuentos->push($cuenta_contable_descuento);
                                }
                            }
                        } else {
                            return response()->json([
                                'mensaje' => 'Hubo un problema al realizar la venta. No es posible obtener el artículo asociado a la carrera del alumno.'
                            ]);
                        }
                    } else {
                        return response()->json([
                            'mensaje' => 'Hubo un problema al realizar la venta. Contacte al administrador.'
                        ]);
                    }

                } else if ($detalle['tipo'] == 'tesis') {
                    $venta_detalle->pago_tesis_id = $detalle['id'];
                    $pago_tesis = PagoTesis::findOrFail($detalle['id']);
                    $pago_tesis->saldo = $pago_tesis->saldo - ($venta_detalle->monto_neto + $venta_detalle->descuento);
                    if ($pago_tesis->saldo == 0) {
                        $pago_tesis->estado = 'CA';
                    } else {
                        $pago_tesis->estado = 'PA';
                    }
                    $pago_tesis->fecha_pago = Carbon::now();
                    $pago_tesis->save();

                    $fecha_defensa = FechaDefensaTesis::where('estado', 'LI')->first();

                    if ($fecha_defensa) {
                        $fecha_defensa->estado = 'OC';
                        $fecha_defensa->save();

                        $tesis = InscripcionTemaTesis::findOrFail($pago_tesis->inscripcion_id);
                        $tesis->fecha_defensa = $fecha_defensa->fecha . ' ' . $fecha_defensa->hora;
                        $tesis->estado = 'FE';
                        $tesis->save();
                    } else {
                        return response()->json([
                            'mensaje' => 'El pago del trabajo final de grado no puede ser realizado, no existen fechas de defensa disponibles para asignar.'
                        ]);
                    }

                    $articulo = Carrera::where('id', $tesis->carrera_id)->first()->articulo;
                    if ($articulo) {
                        $cuenta_contable_titulo = CuentaContable::where('id', $articulo->detalle->cuenta_titulo_id)->first();
                        $cuenta_contable_defensa = CuentaContable::where('id', $articulo->detalle->cuenta_defensa_id)->first();
                        if (!$cuenta_contable_titulo || !$cuenta_contable_defensa) {
                            return response()->json([
                                'mensaje' => 'Hubo un problema al realizar la venta. No es posible obtener la cuenta contable del artículo.'
                            ]);
                        } else {
                            $cuenta_contable_titulo->monto = $venta_detalle->monto_bruto - $articulo->detalle->precio_defensa;
                            $cuentas_contables_ventas->push($cuenta_contable_titulo);
                            $cuenta_contable_defensa->monto = $venta_detalle->monto_bruto - $articulo->detalle->precio_titulo;
                            $cuentas_contables_ventas->push($cuenta_contable_defensa);

                        }

                        if ($venta_detalle->descuento > 0) {
                            $cuenta_contable_descuento = CuentaContable::where('id', $articulo->detalle->cuenta_descuento_id)->first();
                            if (!$cuenta_contable_descuento) {
                                return response()->json([
                                    'mensaje' => 'Hubo un problema al realizar la venta. No es posible obtener la cuenta contable de descuentos del artículo.'
                                ]);
                            } else {
                                $cuenta_contable_descuento->monto = $venta_detalle->descuento;
                                $cuentas_contables_descuentos->push($cuenta_contable_descuento);
                            }
                        }
                    } else {
                        return response()->json([
                            'mensaje' => 'Hubo un problema al realizar la venta. No es posible obtener el artículo asociado a la carrera del alumno.'
                        ]);
                    }

                } else if ($detalle['tipo'] == 'tutoria') {
                    $venta_detalle->pago_tutoria_id = $detalle['id'];
                    $pago_tutoria = PagoTutoria::findOrFail($detalle['id']);
                    $pago_tutoria->saldo = $pago_tutoria->saldo - ($venta_detalle->monto_neto + $venta_detalle->descuento);
                    if ($pago_tutoria->saldo == 0) {
                        $pago_tutoria->estado = 'CA';

                        $tutoria_alumno = TutoriaAlumno::where('tutoria_id', $pago_tutoria->tutoria_id)->where('alumno_id', $venta->alumno_id)->first();
                        $tutoria_alumno->estado == 'PA';
                        $tutoria_alumno->save();
                    } else {
                        $pago_tutoria->estaado = 'PA';
                    }
                    $pago_tutoria->fecha_pago = Carbon::now();
                    $pago_tutoria->save();

                    $tutoria_alumno = TutoriaAlumno::where('tutoria_id', $pago_tutoria->tutoria_id)->where('alumno_id', $venta->alumno_id)->first();

                    $tutoria_precio = TutoriaPrecio::where('modalidad_id', $pago_tutoria->tutoria->modalidad_id)->where('carrera_id', $tutoria_alumno->carrera_id)->first();
                    $articulo = Articulo::find($tutoria_precio->articulo_id);
                    $cuenta_contable_venta = CuentaContable::where('id', $tutoria_precio->articulo->detalle->cuenta_contado_id)->first();
                    if (!$cuenta_contable_venta) {
                        return response()->json([
                            'mensaje' => 'Hubo un problema al realizar la venta. No es posible obtener la cuenta contable del artículo.'
                        ]);
                    } else {
                        $cuenta_contable_venta->monto = $venta_detalle->monto_bruto;
                        $cuentas_contables_ventas->push($cuenta_contable_venta);
                    }

                    if ($venta_detalle->descuento > 0) {
                        $cuenta_contable_descuento = CuentaContable::where('id', $articulo->detalle->cuenta_descuento_id)->first();
                        if (!$cuenta_contable_descuento) {
                            return response()->json([
                                'mensaje' => 'Hubo un problema al realizar la venta. No es posible obtener la cuenta contable de descuentos del artículo.'
                            ]);
                        } else {
                            $cuenta_contable_descuento->monto = $venta_detalle->descuento;
                            $cuentas_contables_descuentos->push($cuenta_contable_descuento);
                        }
                    }

                } else if ($detalle['tipo'] == 'inscripcion_ubs') {
                    $venta_detalle->pago_inscripcion_ubs_id = $detalle['id'];
                    $pago_inscripcion = PagoInscripcionUbs::findOrFail($detalle['id']);
                    $pago_inscripcion->saldo = $pago_inscripcion->saldo - ($venta_detalle->monto_neto + $venta_detalle->descuento);
                    if ($pago_inscripcion->saldo == 0) {
                        $pago_inscripcion->estado = 'CA';
                    } else {
                        $pago_inscripcion->estado = 'PA';
                    }
                    $pago_inscripcion->fecha_pago = Carbon::now();
                    $pago_inscripcion->save();

                    $inscripcion = InscripcionUbs::find($pago_inscripcion->inscripcion_id);
                    if ($inscripcion) {
                        $articulo = Curso::where('id', $inscripcion->curso_id)->first()->articulo;
                        if ($articulo) {
                            if ($pago_inscripcion->tipo == null) {
                                $cuenta_contable_venta = CuentaContable::where('id', $articulo->detalle->cuenta_contado_id)->first();
                                if (!$cuenta_contable_venta) {
                                    return response()->json([
                                        'mensaje' => 'Hubo un problema al realizar la venta. No es posible obtener la cuenta contable del artículo.'
                                    ]);
                                } else {
                                    $cuenta_contable_venta->monto = $venta_detalle->monto_bruto;
                                    $cuentas_contables_ventas->push($cuenta_contable_venta);
                                }
                            } else {
                                $cuenta_contable_venta = CuentaContable::where('id', $articulo->detalle->cuenta_cuota_id)->first();
                                if (!$cuenta_contable_venta) {
                                    return response()->json([
                                        'mensaje' => 'Hubo un problema al realizar la venta. No es posible obtener la cuenta contable del artículo.'
                                    ]);
                                } else {
                                    $cuenta_contable_venta->monto = $venta_detalle->monto_bruto;
                                    $cuentas_contables_ventas->push($cuenta_contable_venta);
                                }
                            }

                            if ($venta_detalle->descuento > 0) {
                                $cuenta_contable_descuento = CuentaContable::where('id', $articulo->detalle->cuenta_descuento_id)->first();
                                if (!$cuenta_contable_descuento) {
                                    return response()->json([
                                        'mensaje' => 'Hubo un problema al realizar la venta. No es posible obtener la cuenta contable de descuentos del artículo.'
                                    ]);
                                } else {
                                    $cuenta_contable_descuento->monto = $venta_detalle->descuento;
                                    $cuentas_contables_descuentos->push($cuenta_contable_descuento);
                                }
                            }
                        } else {
                            return response()->json([
                                'mensaje' => 'Hubo un problema al realizar la venta. No es posible obtener el articulo asociado al curso del alumno.'
                            ]);
                        }
                    } else {
                        return response()->json([
                            'mensaje' => 'Hubo un problema al realizar la venta. No es posible obtener la inscripcion del alumno.'
                        ]);
                    }


                } else if ($detalle['tipo'] == 'tesis_ubs') {
                    $venta_detalle->pago_tesis_ubs_id = $detalle['id'];
                    $pago_tesis = PagoTesisUbs::findOrFail($detalle['id']);
                    $tesis = InscripcionTemaTesisUbs::findOrFail($pago_tesis->tesis_id);

                    $pago_tesis->saldo = $pago_tesis->saldo - ($venta_detalle->monto_neto + $venta_detalle->descuento);
                    if ($pago_tesis->saldo == 0) {
                        $pago_tesis->estado = 'CA';

                        $tesis->estado = 'PA';
                        $tesis->pagado = 'CA';
                        $tesis->fecha_pago = $venta->fecha;
                    } else {
                        $pago_tesis->estado = 'PA';

                        $tesis->pagado = 'PA';
                    }
                    $pago_tesis->fecha_pago = Carbon::now();
                    $pago_tesis->save();
                    $tesis->save();

                    $articulo = Curso::where('id', $tesis->curso_id)->first()->articulo;
                    if ($articulo) {
                        if ($pago_tesis->descripcion == 'DEFENSA DE TESIS') {
                            $cuenta_contable_venta = CuentaContable::where('id', $articulo->detalle->cuenta_defensa_id)->first();
                            if (!$cuenta_contable_venta) {
                                return response()->json([
                                    'mensaje' => 'Hubo un problema al realizar la venta. No es posible obtener la cuenta contable del artículo.'
                                ]);
                            } else {
                                $cuenta_contable_venta->monto = $venta_detalle->monto_bruto;
                                $cuentas_contables_ventas->push($cuenta_contable_venta);
                            }
                        } else {
                            $cuenta_contable_venta = CuentaContable::where('id', $articulo->detalle->cuenta_titulo_id)->first();
                            if (!$cuenta_contable_venta) {
                                return response()->json([
                                    'mensaje' => 'Hubo un problema al realizar la venta. No es posible obtener la cuenta contable del artículo.'
                                ]);
                            } else {
                                $cuenta_contable_venta->monto = $venta_detalle->monto_bruto;
                                $cuentas_contables_ventas->push($cuenta_contable_venta);
                            }
                        }
                    } else {
                        return response()->json([
                            'mensaje' => 'Hubo un problema al realizar la venta. No es posible obtener el artículo asociado a la carrera del alumno.'
                        ]);
                    }

                }
                $venta_detalle->save();
            }

            $cuentas_contables_cobros = collect();

            foreach ($request->pagos as $pago) {
                $cobro = new Cobro();
                $cobro->venta_id = $venta->id;
                $cobro->forma_pago_id = $pago['forma_pago'];
                if ($pago['forma_pago'] == 1) { //caso efectivo
                    $caja = UsuarioCaja::where('usuario_id', Auth::id())->first();

                    $cobro->monto = $pago['efectivo'];
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
                        return response()->json([
                            'mensaje' => 'Hubo un problema al realizar la venta. La cuenta contable de la caja no se encuentra seleccionada.'
                        ]);
                    } else {
                        $cuenta_contable_cobro->monto = $cobro->monto;
                        $cuenta_contable_cobro->tipo = 'CAJA';
                        $cuenta_contable_cobro->caja = $caja->nombre;
                        $cuentas_contables_cobros->push($cuenta_contable_cobro);
                    }

                } else if ($pago['forma_pago'] == 2) { //caso tarjeta de debito
                    $caja = UsuarioCaja::where('usuario_id', Auth::id())->first();
                    $cuenta_bancaria = CuentaBancaria::where('acredita_tarjeta', true)->first();
                    if (!$cuenta_bancaria) {
                        return back()->with('error-message', 'No existe una cuenta bancaria habilitada para cobros con tarjetas.');
                    }

                    $cobro->monto = $pago['monto_debito'];
                    $cobro->caja_id = $caja->caja_id;
                    if (isset($pago['banco_debito'])) {
                        $cobro->banco_id = $pago['banco_debito'];
                    }
                    if (isset($pago['numero_transaccion_debito'])) {
                        $cobro->numero_transaccion = $pago['numero_transaccion_debito'];
                    }
                    if (isset($pago['fecha_transaccion_debito'])) {
                        $cobro->fecha_transaccion = $pago['fecha_transaccion_debito'];
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
                        return response()->json([
                            'mensaje' => 'Hubo un problema al realizar la venta. La cuenta contable de la cuenta bancaria no se encuentra seleccionada.'
                        ]);
                    } else {
                        $cuenta_contable_cobro->monto = $cobro->monto;
                        $cuenta_contable_cobro->tipo = 'BANCO';
                        $cuenta_contable_cobro->caja = $cuenta_bancaria->banco->nombre . ' ' . $cuenta_bancaria->numero_cuenta;
                        $cuentas_contables_cobros->push($cuenta_contable_cobro);
                    }

                } else if ($pago['forma_pago'] == 3) { //caso para tarjeta de credito
                    $caja = UsuarioCaja::where('usuario_id', Auth::id())->first();
                    $cuenta_bancaria = CuentaBancaria::where('acredita_tarjeta', true)->first();
                    if (!$cuenta_bancaria) {
                        return back()->with('error-message', 'No existe una cuenta bancaria habilitada para cobros con tarjetas.');
                    }

                    $cobro->monto = $pago['monto_credito'];
                    $cobro->caja_id = $caja->caja_id;
                    if (isset($pago['banco_credito'])) {
                        $cobro->banco_id = $pago['banco_credito'];
                    }
                    if (isset($pago['numero_transaccion_credito'])) {
                        $cobro->numero_transaccion = $pago['numero_transaccion_credito'];
                    }
                    if (isset($pago['fecha_transaccion_credito'])) {
                        $cobro->fecha_transaccion = $pago['fecha_transaccion_credito'];
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
                        return response()->json([
                            'mensaje' => 'Hubo un problema al realizar la venta. La cuenta contable de la cuenta bancaria no se encuentra seleccionada.'
                        ]);
                    } else {
                        $cuenta_contable_cobro->monto = $cobro->monto;
                        $cuenta_contable_cobro->tipo = 'BANCO';
                        $cuenta_contable_cobro->caja = $cuenta_bancaria->banco->nombre . ' ' . $cuenta_bancaria->numero_cuenta;
                        $cuentas_contables_cobros->push($cuenta_contable_cobro);
                    }

                } else if ($pago['forma_pago'] == 4) { //caso para transferencia
                    $caja = UsuarioCaja::where('usuario_id', Auth::id())->first();

                    $cobro->monto = $pago['monto_transferencia'];
                    $cobro->caja_id = $caja->caja_id;
                    if (isset($pago['banco_transferencia'])) {
                        $cobro->banco_id = $pago['banco_transferencia'];
                    }
                    if (isset($pago['numero_transaccion_transferencia'])) {
                        $cobro->numero_transaccion = $pago['numero_transaccion_transferencia'];
                    }
                    if (isset($pago['fecha_transaccion_transferencia'])) {
                        $cobro->fecha_transaccion = $pago['fecha_transaccion_transferencia'];
                    }
                    if (isset($pago['cuenta_bancaria_transferencia'])) {
                        $cobro->cuenta_bancaria_id = $pago['cuenta_bancaria_transferencia'];
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
                        return response()->json([
                            'mensaje' => 'Hubo un problema al realizar la venta. La cuenta contable de la cuenta bancaria no se encuentra seleccionada.'
                        ]);
                    } else {
                        $cuenta_contable_cobro->monto = $cobro->monto;
                        $cuenta_contable_cobro->tipo = 'BANCO';
                        $cuenta_contable_cobro->cuenta_bancaria = $cuenta_bancaria->banco->nombre . ' ' . $cuenta_bancaria->numero_cuenta;
                        $cuentas_contables_cobros->push($cuenta_contable_cobro);
                    }

                } else if ($pago['forma_pago'] == 5) { //caso para deposito
                    $caja = UsuarioCaja::where('usuario_id', Auth::id())->first();

                    $cobro->monto = $pago['monto_deposito'];
                    $cobro->caja_id = $caja->caja_id;
                    if (isset($pago['numero_transaccion_deposito'])) {
                        $cobro->numero_transaccion = $pago['numero_transaccion_deposito'];
                    }
                    if (isset($pago['fecha_transaccion_deposito'])) {
                        $cobro->fecha_transaccion = $pago['fecha_transaccion_deposito'];
                    }
                    if (isset($pago['cuenta_bancaria_deposito'])) {
                        $cobro->cuenta_bancaria_id = $pago['cuenta_bancaria_deposito'];
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
                        return response()->json([
                            'mensaje' => 'Hubo un problema al realizar la venta. La cuenta contable de la cuenta bancaria no se encuentra seleccionada.'
                        ]);
                    } else {
                        $cuenta_contable_cobro->monto = $cobro->monto;
                        $cuenta_contable_cobro->tipo = 'BANCO';
                        $cuenta_contable_cobro->cuenta_bancaria = $cuenta_bancaria->banco->nombre . ' ' . $cuenta_bancaria->numero_cuenta;
                        $cuentas_contables_cobros->push($cuenta_contable_cobro);
                    }
                } else if ($pago['forma_pago'] == 6) { //caso para notas de credito
                    $caja = UsuarioCaja::where('usuario_id', Auth::id())->first();

                    $cobro->monto = str_replace('.', '', $pago['monto_nota_credito']);
                    $cobro->caja_id = $caja->caja_id;
                    $cobro->save();

                    $movimiento_caja = new MovimientoCaja();
                    $movimiento_caja->fecha = Carbon::now();
                    $movimiento_caja->caja_destino_id = $caja->caja_id;
                    $movimiento_caja->sentido = 'D';
                    $movimiento_caja->monto = $cobro->monto;
                    $movimiento_caja->tipo_movimiento_id = 12; //aca va el ID del movimiento de caja del tipo Venta
                    $movimiento_caja->motivo = 'FACTURA N° ' . $venta->numero_factura . ' COBRADO CON NOTA DE CREDITO';
                    $movimiento_caja->venta_id = $venta->id;
                    $movimiento_caja->estado = 'AP';
                    $movimiento_caja->creado_por_id = Auth::id();
                    $movimiento_caja->aprobado_por_id = Auth::id();
                    $movimiento_caja->save();

                    $nota_credito = NotaCredito::where('id', $pago['nota_credito'])->first();
                    if (!$nota_credito) {
                        return response()->json([
                            'mensaje' => 'Hubo un problema al realizar la venta. No se ha podido obtener la nota de crédito seleccionada.',
                        ]);
                    } else {
                        $nota_credito->estado = 'UT';
                        $nota_credito->save();
                    }

                    $cuenta_contable_cobro = CuentaContable::where('id', 1)->first(); //Cuenta contable de Notas de Credito
                    if (!$cuenta_contable_cobro) {
                        return response()->json([
                            'mensaje' => 'Hubo un problema al realizar la venta. La cuenta contable de Notas de Credito no se encuentra seleccionada.'
                        ]);
                    } else {
                        $cuenta_contable_cobro->monto = $cobro->monto;
                        $cuenta_contable_cobro->tipo = 'NOTA DE CREDITO';
                        $cuentas_contables_cobros->push($cuenta_contable_cobro);
                    }
                } else if ($pago['forma_pago'] == 7) { //caso para cheque
                    $caja = UsuarioCaja::where('usuario_id', Auth::id())->first();

                    $cobro->monto = $pago['monto_cheque'];
                    $cobro->caja_id = $caja->caja_id;
                    if (isset($pago['banco_cheque'])) {
                        $cobro->banco_id = $pago['banco_cheque'];
                    }
                    if (isset($pago['numero_cheque'])) {
                        $cobro->numero_cheque = $pago['numero_cheque'];
                    }
                    if (isset($pago['numero_serie_cheque'])) {
                        $cobro->numero_serie = $pago['numero_serie_cheque'];
                    }
                    if (isset($pago['emisor_cheque'])) {
                        $cobro->emisor_cheque = $pago['emisor_cheque'];
                    }
                    $cobro->save();

                    $movimiento_caja = new MovimientoCaja();
                    $movimiento_caja->fecha = Carbon::now();
                    $movimiento_caja->caja_destino_id = $caja->caja_id;
                    $movimiento_caja->sentido = 'D';
                    $movimiento_caja->monto = $cobro->monto;
                    $movimiento_caja->tipo_movimiento_id = 12; //aca va el ID del movimiento de caja del tipo Venta
                    $movimiento_caja->motivo = 'FACTURA N° ' . $venta->numero_factura . ' COBRADO CON CHEQUE N° ' . $cobro->numero_cheque . ' DEL BANCO ' . $cobro->banco->nombre . ' DEL EMISOR ' . $cobro->emisor_cheque . ' .';
                    $movimiento_caja->venta_id = $venta->id;
                    $movimiento_caja->estado = 'AP';
                    $movimiento_caja->creado_por_id = Auth::id();
                    $movimiento_caja->aprobado_por_id = Auth::id();
                    $movimiento_caja->save();

                    $caja = Caja::findOrFail($caja->caja_id);
                    $caja->monto_cheque = $caja->monto_cheque + $cobro->monto;
                    $caja->save();

                    $cuenta_contable_cobro = CuentaContable::where('id', $caja->cuenta_ingreso_id)->first();
                    if (!$cuenta_contable_cobro) {
                        return response()->json([
                            'mensaje' => 'Hubo un problema al realizar la venta. La cuenta contable de la caja no se encuentra seleccionada.'
                        ]);
                    } else {
                        $cuenta_contable_cobro->monto = $cobro->monto;
                        $cuenta_contable_cobro->tipo = 'CAJA';
                        $cuenta_contable_cobro->caja = $caja->nombre;
                        $cuentas_contables_cobros->push($cuenta_contable_cobro);
                    }

                }
            }

            if ($cuentas_contables_ventas->count() > 0 && $cuentas_contables_cobros->count() > 0) {
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
                $asiento_contable->unidad_negocio_id = $articulo->unidad_id;
                $asiento_contable->subunidad_negocio_id = $articulo->subunidad_id;
                $asiento_contable->save();

                switch ($venta->alumno->sexo_id) {
                    case 2:
                        $pronombre = 'ALUMNA';
                        break;
                    default:
                        $pronombre = 'ALUMNO';
                        break;
                }

                foreach($cuentas_contables_ventas as $cuenta_venta) {
                    $asiento_contable_detalle = new AsientoContableDetalle();
                    $asiento_contable_detalle->asiento_id = $asiento_contable->id;
                    $asiento_contable_detalle->cuenta_contable_id = $cuenta_venta->id;
                    $asiento_contable_detalle->descripcion = 'VENTA S/ FACTURA ' . $numero_factura . ' - CLIENTE: ' . $venta->cliente->nombre . ' - ' . $pronombre . ': ' . $venta->alumno->primer_nombre . ' ' . $venta->alumno->primer_apellido;
                    $asiento_contable_detalle->haber = $cuenta_venta->monto;
                    $asiento_contable_detalle->centro_costo_id = $articulo->centro_costo_id;
                    $asiento_contable_detalle->subcentro_costo_id = $articulo->subcentro_costo_id;
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

                foreach($cuentas_contables_cobros as $cuenta_cobro) {
                    $asiento_contable_detalle = new AsientoContableDetalle();
                    $asiento_contable_detalle->asiento_id = $asiento_contable->id;
                    $asiento_contable_detalle->cuenta_contable_id = $cuenta_cobro->id;
                    $asiento_contable_detalle->descripcion = 'COBRO S/ FACTURA ' . $numero_factura . ' - CLIENTE: ' . $venta->cliente->nombre . ' - ' . $pronombre . ': ' . $venta->alumno->primer_nombre . ' ' . $venta->alumno->primer_apellido . ' - ' . $cuenta_contable_cobro->tipo . ' ' . $cuenta_cobro->caja ;
                    $asiento_contable_detalle->debe = $cuenta_cobro->monto;
                    $asiento_contable_detalle->centro_costo_id = $articulo->centro_costo_id;
                    $asiento_contable_detalle->subcentro_costo_id = $articulo->subcentro_costo_id;
                    if ($cuenta_cobro->tipo != 'NOTA DE CREDITO') {
                        $asiento_contable_detalle->save();

                        if ($cuenta_cobro->tipo == 'CAJA') {
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
                }

                if ($cuentas_contables_descuentos->count() > 0) {
                    foreach ($cuentas_contables_descuentos as $cuenta_descuento) {
                        $asiento_contable_detalle = new AsientoContableDetalle();
                        $asiento_contable_detalle->asiento_id = $asiento_contable->id;
                        $asiento_contable_detalle->cuenta_contable_id = $cuenta_descuento->id;
                        $asiento_contable_detalle->descripcion = 'DESCUENTO S/ FACTURA ' . $numero_factura . ' - CLIENTE: ' . $venta->cliente->nombre . ' - ' . $pronombre . ': ' . $venta->alumno->primer_nombre . ' ' . $venta->alumno->primer_apellido;
                        $asiento_contable_detalle->debe = $cuenta_descuento->monto;
                        $asiento_contable_detalle->centro_costo_id = $articulo->centro_costo_id;
                        $asiento_contable_detalle->subcentro_costo_id = $articulo->subcentro_costo_id;
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
                        $saldo_cuenta_contable_detalle->debe = $asiento_contable_detalle->debe;
                        $saldo_cuenta_contable_detalle->save();
                    }
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'La factura de venta del alumno ' . $venta->alumno->primer_nombre . ' ' . $venta->alumno->primer_apellido . ' fue facturada existosamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('cajero.create')->with('error-message', $e->getMessage());
        }
    }

    public function get_clientes($alumno)
    {
        $this->authorize('crear_ventas_cajero');

        try {
            $clientes = AlumnoCliente::with('cliente')->where('alumno_id', $alumno)->orderBy('es_principal', 'desc')->get();
            return response()->json([
                'clientes' => $clientes,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('cajero.create')->with('error-message', $e->getMessage());
        }
    }

    public function get_pagos($alumno)
    {
        $this->authorize('crear_ventas_cajero');

        try {
            $pagos = collect();

            $pagos_matriculaciones = PagoMatriculacion::whereHas('matriculacion', function ($query) use ($alumno) {
                $query->where('alumno_id', $alumno);
            })->where('estado', '!=', 'CA')
              ->orderBy('id', 'asc')
              ->get();

            foreach ($pagos_matriculaciones as $pago) {
                $datos_pagos = ['id' => $pago->id,
                                'descripcion' => $pago->descripcion,
                                'bruto' => number_format($pago->monto, 0, ',', '.'),
                                'convenio' => number_format($pago->monto_convenio, 0, ',', '.'),
                                'saldo' => number_format($pago->saldo, 0, ',', '.'),
                                'tipo' => 'matriculacion'];
                $pagos->push($datos_pagos);
            }

            $pagos_solicitudes = PagoSolicitud::whereHas('solicitud', function ($query) use ($alumno) {
                $query->where('alumno_id', $alumno);
            })->where('estado', '!=', 'CA')
              ->orderBy('id', 'asc')
              ->get();

            foreach ($pagos_solicitudes as $pago) {
                $datos_pagos = ['id' => $pago->id,
                                'descripcion' => $pago->descripcion,
                                'bruto' => number_format($pago->monto, 0, ',', '.'),
                                'convenio' => number_format($pago->monto_convenio, 0, ',', '.'),
                                'saldo' => number_format($pago->saldo, 0, ',', '.'),
                                'tipo' => 'solicitud'];
                $pagos->push($datos_pagos);
            }

            $pagos_tesis = PagoTesis::whereHas('tesis', function ($query) use ($alumno) {
                $query->where('alumno_id', $alumno);
            })->where('estado', '!=', 'CA')
              ->orderBy('id', 'asc')
              ->get();

            foreach ($pagos_tesis as $pago) {
                $datos_pagos = ['id' => $pago->id,
                                'descripcion' => $pago->descripcion,
                                'bruto' => number_format($pago->monto, 0, ',', '.'),
                                'convenio' => number_format($pago->monto_convenio, 0, ',', '.'),
                                'saldo' => number_format($pago->saldo, 0, ',', '.'),
                                'tipo' => 'tesis'];
                $pagos->push($datos_pagos);
            }

            $pagos_tutorias = PagoTutoria::whereHas('tutoria.alumnos', function ($query) use ($alumno) {
                $query->where('alumno_id', $alumno);
            })->where('estado', '!=', 'CA')
              ->orderBy('id', 'asc')
              ->get();

            foreach ($pagos_tutorias as $pago) {
                $datos_pago = ['id' => $pago->id,
                                'descripcion' => $pago->descripcion,
                                'bruto' => number_format($pago->monto, 0, ',', '.'),
                                'convenio' => number_format($pago->monto_convenio, 0, ',', '.'),
                                'saldo' => number_format($pago->saldo, 0, ',', '.'),
                                'tipo' => 'tutoria'];
                $pagos->push($datos_pago);
            }

            $pagos_inscripciones_ubs = PagoInscripcionUbs::whereHas('inscripcion', function ($query) use ($alumno) {
                $query->where('alumno_id', $alumno);
            })->where('estado', '!=', 'CA')
            ->orderBy('id', 'asc')
            ->get();

            foreach ($pagos_inscripciones_ubs as $pago) {
                $datos_pagos = ['id' => $pago->id,
                                'descripcion' => $pago->descripcion,
                                'bruto' => number_format($pago->monto, 0, ',', '.'),
                                'convenio' => number_format($pago->monto_convenio, 0, ',', '.'),
                                'saldo' => number_format($pago->saldo, 0, ',', '.'),
                                'tipo' => 'inscripcion_ubs'];
                $pagos->push($datos_pagos);
            }

            $pagos_tesis_ubs = PagoTesisUbs::whereHas('tesis', function ($query) use ($alumno) {
                $query->where('alumno_id', $alumno);
            })->where('estado', '!=', 'CA')
              ->orderBy('id', 'asc')
              ->get();

            foreach ($pagos_tesis_ubs as $pago) {
                $datos_pagos = ['id' => $pago->id,
                                'descripcion' => $pago->descripcion,
                                'bruto' => number_format($pago->monto, 0, ',', '.'),
                                'convenio' => number_format($pago->monto_convenio, 0, ',', '.'),
                                'saldo' => number_format($pago->saldo, 0, ',', '.'),
                                'tipo' => 'tesis_ubs'];
                $pagos->push($datos_pagos);
            }

            return response()->json([
                'pagos' => $pagos,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('cajero.create')->with('error-message', $e->getMessage());
        }
    }

    public function get_notas_creditos($cliente)
    {
        $this->authorize('crear_ventas_cajero');

        try {
            $notas_creditos = NotaCredito::where('cliente_id', $cliente)
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
            return redirect()->route('cajero.create')->with('error-message', $e->getMessage());
        }
    }

    public function arqueos_index()
    {
        $this->authorize('ver_arqueos_cajas_cajero');

        try {
            $caja = UsuarioCaja::where('usuario_id', Auth::id())->first();
            if (!$caja) {
                return redirect()->route('cajero.create')->with('error-message', 'Usted no cuenta con una caja registrada a su nombre para ver sus arqueos.');
            }

            $arqueos_cajas = ArqueoCaja::where('caja_id', $caja->caja_id)->get();
            if ($arqueos_cajas->count() == 0) {
                return redirect()->route('cajero.create')->with('error-message', 'No se encuentran arqueos generados para su caja.');
            }


            return view('cajeros.index_arqueos')->with(compact('arqueos_cajas'));
        } catch (\Exception $e) {
            return redirect()->route('cajero.create')->with('error-message', $e->getMessage());
        }
    }

    public function create_arqueo()
    {
        $this->authorize('crear_arqueos_cajas_cajero');

        try {
            $caja = UsuarioCaja::where('usuario_id', Auth::id())->first();
            if (!$caja) {
                return redirect()->route('root')->with('error-message', 'Usted no cuenta con una caja registrada a su nombre para realizar el arqueo.');
            }

            return view('cajeros.create_arqueo')->with(compact('caja'));
        } catch (\Exception $e) {
            return redirect()->route('cajero.create')->with('error-message', $e->getMessage());
        }
    }

    public function generate_arqueo(Request $request)
    {
        $this->authorize('crear_arqueos_cajas_cajero');

        $request->validate([
            'caja' => ['required', 'numeric'],
            'fecha' => ['required', 'date']
        ]);

        try {
            $movimientos = MovimientoCaja::where(function ($query) use ($request) {
                $query->where('caja_origen_id', $request->caja)
                      ->orWhere('caja_destino_id', $request->caja);
            })
            ->whereDate('fecha', $request->fecha)
            ->whereIn('estado', ['AP', 'IN'])
            ->orderBy('id', 'asc')
            ->get();

            if ($movimientos->count() == 0) {
                return back()->with('error-message', 'No se puede generar el arqueo de caja. No se registraron movimientos en su caja en la fecha seleccionada.');
            }

            $ids_ventas = collect();

            $ventas = 0;
            $compras = 0;
            $gastos = 0;
            $efectivo = 0;
            $tarjetas_debitos = 0;
            $tarjetas_creditos = 0;
            $transferencias = 0;
            $depositos = 0;
            $otros_ingresos = 0;
            $otros_egresos = 0;
            foreach ($movimientos as $movimiento) {
                if ($movimiento->tipo_movimiento_id == 1) {
                    $otros_ingresos = $otros_ingresos + $movimiento->monto;
                } else if ($movimiento->tipo_movimiento_id == 2 || $movimiento->tipo_movimiento_id == 3) {
                    $otros_egresos = $otros_egresos + $movimiento->monto;
                } else if ($movimiento->tipo_movimiento_id == 12 && $movimiento->estado != 'IN') {
                    $ventas = $ventas + $movimiento->monto;
                    $ids_ventas->push($movimiento->venta_id);
                }
            }

            foreach ($ids_ventas as $venta_id) {
                $venta = Venta::with('cobros')->findOrFail($venta_id);
                foreach ($venta->cobros as $cobro) {
                    if ($cobro->forma_pago_id == 1) {
                        $efectivo = $efectivo + $cobro->monto;
                    } else if ($cobro->forma_pago_id == 2) {
                        $tarjetas_debitos = $tarjetas_debitos + $cobro->monto;
                    } else if ($cobro->forma_pago_id == 3) {
                        $tarjetas_creditos = $tarjetas_creditos + $cobro->monto;
                    } else if ($cobro->forma_pago_id == 4) {
                        $transferencias = $transferencias + $cobro->monto;
                    } else if ($cobro->forma_pago_id == 5) {
                        $depositos = $depositos + $cobro->monto;
                    }
                }
            }

            $total_ingresos = $ventas + $otros_ingresos;
            $total_egresos = $compras + $gastos + $otros_egresos;

            $total_resumen = $total_ingresos - $total_egresos;

            $empresa = Empresa::first();
            $caja = Caja::findOrFail($request->caja);
            $fecha_hoy = Carbon::now();
            $fecha_arqueo = Carbon::parse($request->fecha)->format('d/m/Y');

            $pdf = Pdf::loadView('cajeros/pdf_arqueo', compact('empresa', 'fecha_hoy', 'fecha_arqueo', 'caja', 'movimientos', 'ventas', 'compras', 'gastos', 'efectivo', 'tarjetas_debitos', 'tarjetas_creditos', 'transferencias', 'depositos', 'otros_ingresos', 'otros_egresos', 'total_ingresos', 'total_egresos', 'total_resumen'));
            $pdf->setPaper('A4');
            $contenido_pdf = $pdf->output();

            $nombre_archivo = 'arqueo_' . Str::lower(str_replace(' ', '_', $caja->nombre)) . '_' . str_replace('-', '', $request->fecha) . '.pdf';
            $contenido_pdf = $pdf->output();
            $ubicacion_archivo = 'public/arqueos/' . $nombre_archivo;
            Storage::put($ubicacion_archivo, $contenido_pdf);

            $arqueo_caja = ArqueoCaja::where('caja_id', $caja->id)->where('fecha', $request->fecha)->first();
            if ($arqueo_caja) {
                $arqueo_caja->delete();
            }

            $arqueo = new ArqueoCaja();
            $arqueo->caja_id = $caja->id;
            $arqueo->fecha = $request->fecha;
            $arqueo->url_ubicacion = 'storage/arqueos/' . $nombre_archivo;
            $arqueo->generado_por_id = Auth::id();
            $arqueo->save();

            return $pdf->stream('arqueo_' . Str::lower(str_replace(' ', '_', $caja->nombre)) . '_' . str_replace('-', '', $request->fecha) . '.pdf');
        } catch (\Exception $e) {
            return redirect()->route('cajero.create')->with('error-message', $e->getMessage());
        }
    }
}
