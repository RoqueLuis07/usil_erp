<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\AutoEncoder;
use Luecano\NumeroALetras\NumeroALetras;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

use App\Models\OrdenPago;
use App\Models\Compra;
use App\Models\Proveedor;
use App\Models\Pago;
use App\Models\FormaPago;
use App\Models\Banco;
use App\Models\CuentaBancaria;
use App\Models\Caja;
use App\Models\UsuarioCaja;
use App\Models\Moneda;
use App\Models\Cotizacion;
use App\Models\Articulo;
use App\Models\UnidadNegocioContable;
use App\Models\SubunidadNegocioContable;
use App\Models\CuentaContable;
use App\Models\AsientoContable;
use App\Models\AsientoContableDetalle;
use App\Models\SaldoCuentaContable;
use App\Models\SaldoCuentaContableDetalle;
use App\Models\Empresa;

class OrdenPagoController extends Controller
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
        $this->authorize('ver_pagos_ordenes');

        try {
            $ordenes_pagos = OrdenPago::orderBy('id', 'desc')->get();
            return view('ordenes_pagos/index')->with(compact('ordenes_pagos'));
        } catch (\Exception $e) {
            return redirect()->route('ordenes_pagos.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_pagos_ordenes');

        try {
            $orden_pago = OrdenPago::with('pago', 'asientoContable')->findOrFail($id);

            return view('ordenes_pagos/show')->with(compact('orden_pago'));
        } catch (\Exception $e) {
            return redirect()->route('ordenes_pagos.index')->with('error-message', $e->getMessage());
        }
    }

    public function create($tipo)
    {
        $this->authorize('crear_pagos_ordenes');

        try {
            $ultima_orden = OrdenPago::orderBy('id', 'desc')->first();
            if ($ultima_orden) {
                $numero_orden = $ultima_orden->id + 1;
            } else {
                $numero_orden = 1;
            }

            $formas_pagos = FormaPago::where('estado', 'AC')->whereIn('id', [1, 4, 5, 7])->get();
            $bancos = Banco::where('estado', 'AC')->get();
            $cuentas_bancarias = CuentaBancaria::where('estado', 'AC')->get();
            $cajas = Caja::where('estado', 'AC')->get();

            if ($tipo == 'PR') {
                $compras = Compra::with('detalles')->whereIn('estado', ['PE', 'CR'])->get();

                return view('ordenes_pagos/create_proveedores')->with(compact('numero_orden', 'formas_pagos', 'bancos', 'cuentas_bancarias', 'cajas', 'compras'));
            } else if ($tipo == 'AN') {
                $proveedores = Proveedor::where('estado', 'AC')->get();
                $monedas = Moneda::where('estado', 'AC')->get();
                $unidades_negocios = UnidadNegocioContable::where('estado', 'AC')->get();

                return view('ordenes_pagos/create_anticipos')->with(compact('numero_orden', 'formas_pagos', 'bancos', 'cuentas_bancarias', 'cajas', 'proveedores', 'monedas', 'unidades_negocios'));
            } else if ($tipo == 'GE') {
                $proveedores = Proveedor::where('estado', 'AC')->get();
                $cuentas_contables = CuentaContable::where('imputable', true)->where('estado', 'AC')->get();
                $monedas = Moneda::where('estado', 'AC')->get();
                $unidades_negocios = UnidadNegocioContable::where('estado', 'AC')->get();

                return view('ordenes_pagos/create_gerencia')->with(compact('numero_orden', 'formas_pagos', 'bancos', 'cuentas_bancarias', 'cajas', 'cuentas_contables', 'proveedores', 'monedas', 'unidades_negocios'));
            }
        } catch (\Exception $e) {
            return redirect()->route('ordenes_pagos.index')->with('error-message', $e->getMessage());
        }
    }

    public function store_proveedor(Request $request)
    {
        $this->authorize('crear_pagos_ordenes');

        $request->validate([
            'forma_pago' => ['required', 'numeric'],
            'caja' => ['nullable', 'numeric', Rule::requiredIf(function () {
                return in_array(request()->forma_pago, [1, 5]);
            })],
            'cuenta_bancaria' => ['nullable', 'numeric', Rule::requiredIf(function () {
                return in_array(request()->forma_pago, [4, 7]);
            })],
            'numero_cheque' => ['nullable', 'numeric', 'required_if:forma_pago,7'],
            'numero_serie' => ['nullable', 'numeric', 'required_if:forma_pago,7'],
            'compra' => ['required', 'numeric'],
            'fecha_factura' => 'nullable',
            'proveedor' => 'nullable',
            'ruc_proveedor' => 'nullable',
            'numero_factura' => 'nullable',
            'condicion' => 'nullable',
            'monto_total' => 'nullable',
        ]);

        DB::beginTransaction();

        try {
            $ultima_orden = OrdenPago::orderBy('id', 'desc')->first();
            if ($ultima_orden) {
                $numero_orden = $ultima_orden->id + 1;
            } else {
                $numero_orden = 1;
            }

            $compra = Compra::where('id', $request->compra)->first();
            if (!$compra) {
                return back()->with('error-message', 'La compra no pudo ser encontrada. Contacte con el administrador.');
            }

            $orden_pago = new OrdenPago();
            $orden_pago->compra_id = $compra->id;
            $orden_pago->proveedor_id = $compra->proveedor_id;
            $orden_pago->tipo = 'PR';
            $orden_pago->forma_pago_id = $request->forma_pago;
            $orden_pago->moneda_id = $compra->moneda_id;
            $orden_pago->concepto = 'PAGO FACTURA NRO.' . $compra->numero_factura;
            $orden_pago->monto_total = $compra->monto_total;
            $orden_pago->cargado_por_id = Auth::id();

            if ($orden_pago->forma_pago_id == 4) {
                $cuenta_bancaria = CuentaBancaria::findOrFail($request->cuenta_bancaria);
                if (($orden_pago->moneda_id == 1 && $cuenta_bancaria->moneda_id == 2) || ($orden_pago->moneda_id == 2 && $cuenta_bancaria->moneda_id == 1)) {
                    $cotizacion = Cotizacion::orderBy('id', 'desc')->first();
                    $orden_pago->cotizacion_id = $cotizacion->id;
                }
            }

            $orden_pago->unidad_negocio_id = $compra->unidad_negocio_id;
            $orden_pago->subunidad_negocio_id = $compra->subunidad_negocio_id;
            $orden_pago->save();

            $pago = new Pago();
            $pago->orden_pago_id = $orden_pago->id;
            $pago->forma_pago_id = $orden_pago->forma_pago_id;
            $pago->monto = $orden_pago->monto_total;
            if ($orden_pago->forma_pago_id == 1) {
                $pago->caja_id = $request->caja;
            } else if ($orden_pago->forma_pago_id == 4 || $orden_pago->forma_pago_id == 7) {
                $pago->cuenta_bancaria_id = $request->cuenta_bancaria;
                if ($orden_pago->forma_pago_id == 7) {
                    $pago->numero_cheque = $request->numero_cheque;
                    $pago->numero_serie_cheque = $request->numero_serie;
                }
            } else if ($orden_pago->forma_pago_id == 5) {
                $pago->caja_id = $request->caja;
                $pago->banco_id = $orden_pago->proveedor->banco_id;
            }
            $pago->proveedor_id = $orden_pago->proveedor_id;
            $pago->save();

            DB::commit();

            return redirect()->route('ordenes_pagos.index')->with('success-message', 'La OP N° ' . $orden_pago->id . ' fue creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('ordenes_pagos.index')->with('error-message', $e->getMessage());
        }
    }

    public function store_anticipo(Request $request)
    {
        $this->authorize('crear_pagos_ordenes');

        $request->validate([
            'forma_pago' => ['required', 'numeric'],
            'caja' => ['nullable', 'numeric', Rule::requiredIf(function () {
                return in_array(request()->forma_pago, [1, 5]);
            })],
            'cuenta_bancaria' => ['nullable', 'numeric', Rule::requiredIf(function () {
                return in_array(request()->forma_pago, [4, 7]);
            })],
            'numero_cheque' => ['nullable', 'numeric', 'required_if:forma_pago,7'],
            'numero_serie' => ['nullable', 'numeric', 'required_if:forma_pago,7'],
            'proveedor' => 'nullable',
            'moneda' => ['required', 'numeric'],
            'monto_guaranies' => ['nullable', 'required_if:moneda,1'],
            'monto_dolares' => ['nullable', 'required_if:moneda,2'],
            'unidad_negocio' => ['required', 'numeric'],
            'subunidad_negocio' => ['required', 'numeric'],
            'concepto' => 'required'

        ]);

        DB::beginTransaction();

        try {
            $ultima_orden = OrdenPago::orderBy('id', 'desc')->first();
            if ($ultima_orden) {
                $numero_orden = $ultima_orden->id + 1;
            } else {
                $numero_orden = 1;
            }

            $orden_pago = new OrdenPago();
            $orden_pago->proveedor_id = $request->proveedor;
            $orden_pago->tipo = 'AN';
            $orden_pago->forma_pago_id = $request->forma_pago;
            $orden_pago->moneda_id = $request->moneda;
            $orden_pago->concepto = removeAccents(Str::upper($request->concepto));

            if ($orden_pago->moneda_id == 1) {
                $monto = str_replace('.', '', $request->monto_guaranies);
            } else {
                $monto = str_replace('.', '', $request->monto_dolares);
                $monto = str_replace(',', '.', $monto);
            }
            $orden_pago->monto_total = $monto;
            $orden_pago->cargado_por_id = Auth::id();

            if ($orden_pago->forma_pago_id == 4) {
                $cuenta_bancaria = CuentaBancaria::findOrFail($request->cuenta_bancaria);
                if (($orden_pago->moneda_id == 1 && $cuenta_bancaria->moneda_id == 2) || ($orden_pago->moneda_id == 2 && $cuenta_bancaria->moneda_id == 1)) {
                    $cotizacion = Cotizacion::orderBy('id', 'desc')->first();
                    $orden_pago->cotizacion_id = $cotizacion->id;
                }
            }

            $orden_pago->unidad_negocio_id = $request->unidad_negocio;
            $orden_pago->subunidad_negocio_id = $request->subunidad_negocio;
            $orden_pago->save();

            $pago = new Pago();
            $pago->orden_pago_id = $orden_pago->id;
            $pago->forma_pago_id = $orden_pago->forma_pago_id;
            $pago->monto = $orden_pago->monto_total;
            if ($orden_pago->forma_pago_id == 1) {
                $pago->caja_id = $request->caja;
            } else if ($orden_pago->forma_pago_id == 4 || $orden_pago->forma_pago_id == 7) {
                $pago->cuenta_bancaria_id = $request->cuenta_bancaria;
                if ($orden_pago->forma_pago_id == 7) {
                    $pago->numero_cheque = $request->numero_cheque;
                    $pago->numero_serie_cheque = $request->numero_serie;
                }
            } else if ($orden_pago->forma_pago_id == 5) {
                $pago->caja_id = $request->caja;
                $pago->banco_id = $orden_pago->proveedor->banco_id;
            }
            $pago->proveedor_id = $orden_pago->proveedor_id;
            $pago->save();

            DB::commit();

            return redirect()->route('ordenes_pagos.index')->with('success-message', 'La OP N° ' . $orden_pago->id . ' fue creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('ordenes_pagos.index')->with('error-message', $e->getMessage());
        }
    }

    public function store_gerencia(Request $request)
    {
        $this->authorize('crear_pagos_ordenes');

        $request->validate([
            'forma_pago' => ['required', 'numeric'],
            'caja' => ['nullable', 'numeric', Rule::requiredIf(function () {
                return in_array(request()->forma_pago, [1, 5]);
            })],
            'cuenta_bancaria' => ['nullable', 'numeric', Rule::requiredIf(function () {
                return in_array(request()->forma_pago, [4, 7]);
            })],
            'numero_cheque' => ['nullable', 'numeric', 'required_if:forma_pago,7'],
            'numero_serie' => ['nullable', 'numeric', 'required_if:forma_pago,7'],
            'proveedor' => 'nullable',
            'moneda' => ['required', 'numeric'],
            'monto_guaranies' => ['nullable', 'required_if:moneda,1'],
            'monto_dolares' => ['nullable', 'required_if:moneda,2'],
            'unidad_negocio' => ['required', 'numeric'],
            'subunidad_negocio' => ['required', 'numeric'],
            'cuenta_contable' => ['required', 'numeric'],
            'concepto' => 'required',

        ]);

        DB::beginTransaction();

        try {
            $ultima_orden = OrdenPago::orderBy('id', 'desc')->first();
            if ($ultima_orden) {
                $numero_orden = $ultima_orden->id + 1;
            } else {
                $numero_orden = 1;
            }

            $orden_pago = new OrdenPago();
            $orden_pago->proveedor_id = $request->proveedor;
            $orden_pago->tipo = 'GE';
            $orden_pago->forma_pago_id = $request->forma_pago;
            $orden_pago->moneda_id = $request->moneda;
            $orden_pago->concepto = removeAccents(Str::upper($request->concepto));

            if ($orden_pago->moneda_id == 1) {
                $monto = str_replace('.', '', $request->monto_guaranies);
            } else {
                $monto = str_replace('.', '', $request->monto_dolares);
                $monto = str_replace(',', '.', $monto);
            }
            $orden_pago->cuenta_contable_id = $request->cuenta_contable;
            $orden_pago->monto_total = $monto;
            $orden_pago->cargado_por_id = Auth::id();

            if ($orden_pago->forma_pago_id == 4) {
                $cuenta_bancaria = CuentaBancaria::findOrFail($request->cuenta_bancaria);
                if (($orden_pago->moneda_id == 1 && $cuenta_bancaria->moneda_id == 2) || ($orden_pago->moneda_id == 2 && $cuenta_bancaria->moneda_id == 1)) {
                    $cotizacion = Cotizacion::orderBy('id', 'desc')->first();
                    $orden_pago->cotizacion_id = $cotizacion->id;
                }
            }

            $orden_pago->unidad_negocio_id = $request->unidad_negocio;
            $orden_pago->subunidad_negocio_id = $request->subunidad_negocio;
            $orden_pago->save();

            $pago = new Pago();
            $pago->orden_pago_id = $orden_pago->id;
            $pago->forma_pago_id = $orden_pago->forma_pago_id;
            $pago->monto = $orden_pago->monto_total;
            if ($orden_pago->forma_pago_id == 1) {
                $pago->caja_id = $request->caja;
            } else if ($orden_pago->forma_pago_id == 4 || $orden_pago->forma_pago_id == 7) {
                $pago->cuenta_bancaria_id = $request->cuenta_bancaria;
                if ($orden_pago->forma_pago_id == 7) {
                    $pago->numero_cheque = $request->numero_cheque;
                    $pago->numero_serie_cheque = $request->numero_serie;
                }
            } else if ($orden_pago->forma_pago_id == 5) {
                $pago->caja_id = $request->caja;
                $pago->banco_id = $orden_pago->proveedor->banco_id;
            }
            $pago->proveedor_id = $orden_pago->proveedor_id;
            $pago->save();

            DB::commit();

            return redirect()->route('ordenes_pagos.index')->with('success-message', 'La OP N° ' . $orden_pago->id . ' fue creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('ordenes_pagos.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_pagos_ordenes');

        DB::beginTransaction();

        try {
            $orden_pago = OrdenPago::findOrFail($id);

            if ($orden_pago->estado != 'PE') {
                return back()->with('error-message', 'La OP N° ' . $orden_pago->id . ' no se puede anular porque ya no se encuentra pendiente.');
            }

            Pago::where('orden_pago_id', $orden_pago->id)->delete();

            $orden_pago->estado = 'AN';
            $orden_pago->anulado_por_id = Auth::id();
            $orden_pago->save();

            DB::commit();

            return redirect()->route('ordenes_pagos.index')->with('error-message','La OP N° ' . $orden_pago->id . ' fue anulada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('ordenes_pagos.index')->with('error-message', $e->getMessage());
        }
    }

    public function approve($id)
    {
        $this->authorize('aprobar_pagos_ordenes');

        DB::beginTransaction();

        try {
            $orden_pago = OrdenPago::findOrFail($id);
            $orden_pago->aprobado_por_id = Auth::id();
            $orden_pago->estado = 'AP';
            $orden_pago->save();

            $old_asiento_contable = AsientoContable::orderBy('id', 'desc')->first();
            if ($old_asiento_contable) {
                $numero_asiento = $old_asiento_contable->numero + 1;
            } else {
                $numero_asiento = 1;
            }

            $asiento_contable = new AsientoContable();
            $asiento_contable->numero = $numero_asiento;
            $asiento_contable->fecha = Carbon::now();
            $asiento_contable->origen = 'ORDEN DE PAGO';
            $asiento_contable->orden_pago_id = $orden_pago->id;
            $asiento_contable->moneda_id = 1;
            $asiento_contable->cotizacion_id = null;
            $asiento_contable->unidad_negocio_id = $orden_pago->unidad_negocio_id;
            $asiento_contable->subunidad_negocio_id = $orden_pago->subunidad_negocio_id;
            $asiento_contable->save();

            $asiento_contable_detalle_1 = new AsientoContableDetalle();
            $asiento_contable_detalle_1->asiento_id = $asiento_contable->id;
            if ($orden_pago->tipo == 'GE') {
                $asiento_contable_detalle_1->cuenta_contable_id = $orden_pago->cuenta_contable_id;
            } else {
                $asiento_contable_detalle_1->cuenta_contable_id = 69; //cuenta de PROVEEDORES LOCALES
            }
            $asiento_contable_detalle_1->centro_costo_id = 1; //centro de costo NINGUNO
            $asiento_contable_detalle_1->subcentro_costo_id = 1; //subcentro de costo NINGUNO
            if ($orden_pago->tipo == 'PR') {
                $asiento_contable_detalle_1->descripcion = 'OP ' . $orden_pago->id . ' S/FACTURA PROV. ' . $orden_pago->compra->numero_factura . ' - PROV. ' . $orden_pago->proveedor->razon_social;
            } else if ($orden_pago->tipo == 'GE') {
                $asiento_contable_detalle_1->descripcion = 'OP ' . $orden_pago->id . ' GERENCIA ' . ' - PROV. ' . $orden_pago->proveedor->razon_social;
            } else if ($orden_pago->tipo == 'AN') {
                $asiento_contable_detalle_1->descripcion = 'OP ' . $orden_pago->id . ' ANTICIPO ' . ' - PROV. ' . $orden_pago->proveedor->razon_social;
            }
            $asiento_contable_detalle_1->debe = $orden_pago->monto_total;
            $asiento_contable_detalle_1->save();

            $asiento_contable_detalle_2 = new AsientoContableDetalle();
            $asiento_contable_detalle_2->asiento_id = $asiento_contable->id;
            $asiento_contable_detalle_2->centro_costo_id = 1; //centro de costo NINGUNO
            $asiento_contable_detalle_2->subcentro_costo_id = 1; //subcentro de costo NINGUNO

            $pago = Pago::where('orden_pago_id', $orden_pago->id)->first();
            if ($orden_pago->forma_pago_id == 1 || $orden_pago->forma_pago_id == 5) {
                $caja = Caja::findOrFail($pago->caja_id);
                $asiento_contable_detalle_2->cuenta_contable_id = $caja->cuenta_egreso_id;
                if ($orden_pago->forma_pago_id == 1) {
                    $asiento_contable_detalle_2->descripcion = 'OP ' . $orden_pago->id . ' - EFECTIVO - ' . $orden_pago->proveedor->razon_social;
                } else {
                    $asiento_contable_detalle_2->descripcion = 'OP ' . $orden_pago->id . ' - DEP. BANC. - ' . $orden_pago->proveedor->razon_social;
                }
            } else if ($orden_pago->forma_pago_id == 4 || $orden_pago->forma_pago_id == 7) {
                $cuenta_bancaria = CuentaBancaria::findOrFail($pago->cuenta_bancaria_id);
                $asiento_contable_detalle_2->cuenta_contable_id = $cuenta_bancaria->cuenta_egreso_id;
                if ($orden_pago->forma_pago_id == 4) {
                    $asiento_contable_detalle_2->descripcion = 'OP ' . $orden_pago->id . ' - TRANSF. BANC. - ' . $orden_pago->proveedor->razon_social;
                } else {
                    $asiento_contable_detalle_2->descripcion = 'OP ' . $orden_pago->id . ' - CHEQUE - ' . $orden_pago->proveedor->razon_social;
                }
            }
            $asiento_contable_detalle_2->haber = $orden_pago->monto_total;
            $asiento_contable_detalle_2->save();

            $saldo_cuenta_contable_1 = SaldoCuentaContable::where('id', $asiento_contable_detalle_1->cuenta_contable_id)->first();
            if ($saldo_cuenta_contable_1) {
                $saldo_cuenta_contable_1->saldo = $saldo_cuenta_contable_1->saldo + $asiento_contable_detalle_1->debe;
                $saldo_cuenta_contable_1->save();
            }

            $saldo_cuenta_contable_detalle_1 = new SaldoCuentaContableDetalle();
            $saldo_cuenta_contable_detalle_1->saldo_cuenta_contable_id = $saldo_cuenta_contable_1->id;
            $saldo_cuenta_contable_detalle_1->mes = Carbon::now()->format('m');
            $saldo_cuenta_contable_detalle_1->anho = Carbon::now()->format('Y');
            $saldo_cuenta_contable_detalle_1->debe = $asiento_contable_detalle_2->debe;
            $saldo_cuenta_contable_detalle_1->save();

            $saldo_cuenta_contable_2 = SaldoCuentaContable::where('id', $asiento_contable_detalle_2->cuenta_contable_id)->first();
            if ($saldo_cuenta_contable_2) {
                $saldo_cuenta_contable_2->saldo = $saldo_cuenta_contable_2->saldo - $asiento_contable_detalle_2->haber;
                $saldo_cuenta_contable_2->save();
            }

            $saldo_cuenta_contable_detalle_2 = new SaldoCuentaContableDetalle();
            $saldo_cuenta_contable_detalle_2->saldo_cuenta_contable_id = $saldo_cuenta_contable_2->id;
            $saldo_cuenta_contable_detalle_2->mes = Carbon::now()->format('m');
            $saldo_cuenta_contable_detalle_2->anho = Carbon::now()->format('Y');
            $saldo_cuenta_contable_detalle_2->haber = $asiento_contable_detalle_2->haber;
            $saldo_cuenta_contable_detalle_2->save();

            if ($orden_pago->compra_id) {
                $compra = Compra::where('id', $orden_pago->compra_id)->first();
                if (!$compra) {
                    return back()->with('error-message', 'La OP N° ' . $orden_pago->id . ' no se puede aprobar. No se encuentra la compra asociada.');
                }

                $compra->estado = 'OP';
                $compra->save();
            }

            DB::commit();

            return redirect()->route('ordenes_pagos.index')->with('success-message','La OC N° ' . $orden_pago->id . ' fue aprobada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('ordenes_pagos.index')->with('error-message', $e->getMessage());
        }
    }

    public function unapprove($id)
    {
        $this->authorize('desaprobar_pagos_ordenes');

        DB::beginTransaction();

        try {
            $orden_pago = OrdenPago::findOrFail($id);

            if ($orden_pago->estado != 'AP') {
                return back()->with('error-message', 'La OC N° ' . $orden_pago->id . ' no se puede desaprobar, ésta no se encuentra previamente aprobada.');
            }

            AsientoContableDetalle::where('asiento_id', $orden_pago->asientoContable->id)->delete();
            AsientoContable::where('orden_pago_id', $orden_pago->id)->delete();

            $orden_pago->aprobado_por_id = null;
            $orden_pago->estado = 'PE';
            $orden_pago->save();

            if ($orden_pago->compra_id) {
                $compra = Compra::where('id', $orden_pago->compra_id)->first();
                if (!$compra) {
                    return back()->with('error-message', 'La OP N° ' . $orden_pago->id . ' no se puede anular. No se encuentra la compra asociada.');
                }

                if ($compra->condicion_compra == 'CR') {
                    $compra->estado == 'CR';
                } else {
                    $compra->estado == 'PE';
                }
                $compra->save();
            }

            DB::commit();

            return redirect()->route('ordenes_pagos.index')->with('error-message','La OC N° ' . $orden_pago->id . ' fue desaprobada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('ordenes_pagos.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_pagos_ordenes');

        DB::beginTransaction();

        try {
            $orden_pago = OrdenPago::findOrFail($id);

            if ($orden_pago->estado != 'AN') {
                return back()->with('error-message', 'La OP N° ' . $orden_pago->id . ' no se puede eliminar porque no se encuentra anulada.');
            }

            $orden_pago->delete();

            DB::commit();

            return redirect()->route('ordenes_pagos.index')->with('success-message','La OP N° ' . $orden_pago->id . ' fue eliminada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('ordenes_pagos.index')->with('error-message', 'La OP N° ' . $orden_pago->id . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('ordenes_pagos.index')->with('error-message', $e->getMessage());
            }
        }
    }

    public function imprimir($id, $tipo)
    {
        $this->authorize('reimprimir_pagos_ordenes');

        // try {
            $empresa = Empresa::first();
            $fecha_hoy = Carbon::now();
            $orden_pago = OrdenPago::with('pago')->findOrFail($id);

            if ($orden_pago->tipo == 'PR') {
                $pdf = Pdf::loadView('ordenes_pagos/pdfs/pdf_proveedores', compact('empresa', 'fecha_hoy', 'orden_pago'));
            } else if ($orden_pago->tipo == 'GE') {
                $pdf = Pdf::loadView('ordenes_pagos/pdfs/pdf_gerencia', compact('empresa', 'fecha_hoy', 'orden_pago'));
            } else if ($orden_pago->tipo == 'AN') {
                $pagos = Pago::where('proveedor_id', $orden_pago->proveedor_id)->where('estado', 'PA')->orderBy('id', 'desc')->limit(3)->get();

                $pdf = Pdf::loadView('ordenes_pagos/pdfs/pdf_anticipos', compact('empresa', 'fecha_hoy', 'orden_pago', 'pagos'));
            }
            $pdf->setPaper('A4');

            return $pdf->stream('op_' . $orden_pago->id . '.pdf');

        // } catch (\Exception $e) {
        //     return redirect()->route('ordenes_pagos.index')->with('error-message', $e->getMessage());
        // }
    }

    public function get_compra($id)
    {
        $this->authorize('crear_pagos_ordenes');

        DB::beginTransaction();

        try {
            $compra = Compra::findOrFail($id);
            $compra->fecha = Carbon::parse($compra->fecha)->format('d/m/Y');
            $compra->proveedor_nombre = $compra->proveedor->razon_social;
            $compra->ruc_proveedor = $compra->proveedor->ruc;
            if ($compra->condicion_compra == 'CR') {
                $compra->condicion = 'CREDITO';
                if ($compra->credito_a == 1) {
                    $compra->condicion .= ' (' . $compra->credito_a . ' día)';
                } else {
                    $compra->condicion .= ' (' . $compra->credito_a . ' días)';
                }
            }
            $compra->monto_total = number_format($compra->monto_total, 0, ',', '.');

            return response()->json([
                'compra' => $compra,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('ordenes_pagos.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_subunidades_negocios($id)
    {
        $this->authorize('crear_compras');

        DB::beginTransaction();

        try {
            $subunidades_negocios = SubunidadNegocioContable::where('unidad_id', $id)->where('estado', 'AC')->get();

            return response()->json([
                'subunidades_negocios' => $subunidades_negocios,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('compras.index')->with('error-message', $e->getMessage());
        }
    }
}
