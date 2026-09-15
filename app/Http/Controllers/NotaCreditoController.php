<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\Models\NotaCredito;
use App\Models\Venta;
use App\Models\PuntoImpresion;
use App\Models\Cliente;
use App\Models\Articulo;
use App\Models\AsientoContable;
use App\Models\AsientoContableDetalle;
use App\Models\CuentaContable;
use App\Models\SaldoCuentaContable;
use App\Models\SaldoCuentaContableDetalle;
use App\Models\UnidadNegocioContable;
use App\Models\CentroCostoContable;
use App\Models\SubunidadNegocioContable;
use App\Models\SubcentroCostoContable;
use App\Models\MovimientoCaja;
use App\Models\MovimientoBanco;
use App\Models\Caja;
use App\Models\CuentaBancaria;

class NotaCreditoController extends Controller
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
        $this->authorize('ver_notas_creditos');

        try {
            $notas_creditos = NotaCredito::orderBy('id', 'desc')->get();
            return view('notas_creditos/index')->with(compact('notas_creditos'));
        } catch (\Exception $e) {
            return redirect()->route('notas_creditos.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_notas_creditos');

        try {
            $fecha_hoy = Carbon::today()->format('d/m/Y');

            $punto_impresion = PuntoImpresion::where('tipo_documento_id', 3)->where('estado', 'AC')->orderBy('id', 'desc')->first();
            if (!$punto_impresion) {
                return redirect()->route('notas_creditos.index')->with('error-message', 'No se encuentra registrado ningún punto de impresión activo en el sistema para realizar la nota de crédito.');
            }
            $ultima_nota_credito = NotaCredito::orderBy('id', 'desc')->first();
            if ($ultima_nota_credito) {
                $numero = $ultima_nota_credito->numero_nota_credito;
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
                $numero_nota_credito = $punto_impresion->codigo . $numero;
            } else {
                return redirect()->route('notas_creditos.index')->with('error-message', 'El punto de impresión activo no se encuentra dentro del rango para realizar la nota de crédito ' . $numero_nota_credito . '.');
            }

            $clientes = Cliente::where('estado', 'AC')->get();
            $unidades_negocios = UnidadNegocioContable::where('estado', 'AC')->get();
            $centros_costos = CentroCostoContable::where('estado', 'AC')->get();

            return view('notas_creditos/create')->with(compact('fecha_hoy', 'numero_nota_credito', 'clientes', 'unidades_negocios', 'centros_costos'));
        } catch (\Exception $e) {
            return redirect()->route('notas_creditos.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_notas_creditos');

        $request->validate([
            'aplica_a' => ['required'],
            'reversar_pago' => 'required',
            'cliente' => ['required', 'numeric'],
            'venta' => ['required', 'numeric'],
            'descripcion' => 'nullable',
        ]);

        DB::beginTransaction();

        try {
            $punto_impresion = PuntoImpresion::where('tipo_documento_id', 3)->where('estado', 'AC')->orderBy('id', 'desc')->first();
            if (!$punto_impresion) {
                return redirect()->route('notas_creditos.index')->with('error-message', 'No se encuentra registrado ningún punto de impresión activo en el sistema para realizar la nota de crédito.');
            }
            $ultima_nota_credito = NotaCredito::orderBy('id', 'desc')->first();
            if ($ultima_nota_credito) {
                $numero = $ultima_nota_credito->numero_nota_credito;
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
                $numero_nota_credito = $punto_impresion->codigo . $numero;
            } else {
                return redirect()->route('notas_creditos.index')->with('error-message', 'El punto de impresión activo no se encuentra dentro del rango para realizar la factura ' . $numero_factura . '.');
            }

            $nota_credito = new NotaCredito();
            $nota_credito->fecha = Carbon::now();
            $nota_credito->cliente_id = $request->cliente;
            $nota_credito->punto_impresion_id = $punto_impresion->id;
            $nota_credito->venta_id = $request->venta;
            $nota_credito->numero_nota_credito = $numero_nota_credito;
            $nota_credito->pago_reversado = $request->reversar_pago;


            $venta = Venta::where('id', $request->venta)->first();
            if (!$venta) {
                return back()->with('error-message', 'Hubo un problema al realizar la nota de crédito. No es posible obtener la venta seleccionada.');
            }

            if ($request->aplica_a == 'TOTAL') {
                $nota_credito->monto_total = $venta->monto_total;
            } elseif ($request->aplica_a == 'SALDO') {
                $nota_credito->monto_total = $venta->saldo;
            }

            if($nota_credito->monto_total <= 0) {
                return back()->with('error-message', 'El monto total de la nota de crédito debe ser mayor a cero.');
            }

            if ($request->descripcion) {
                $nota_credito->descripcion = $request->descripcion;
            } else {
                $nota_credito->descripcion = 'NC POR VENTA N° ' . $venta->numero_factura;
            }

            $nota_credito->cargado_por_id = Auth::id();
            $nota_credito->save();

            $venta->saldo = 0;
            $venta->estado = 'NC';
            $venta->save();

            if ($request->reversar_pago == 'true') {
                foreach ($venta->cobros as $cobro) {
                    $cobro->estado = 'IN';
                    $cobro->save();
                }

                $movimientos_cajas = MovimientoCaja::where('venta_id', $venta->id)->get();
                if (!$movimientos_cajas) {
                    return back()->with('error-message', 'Hubo un problema al realizar la nota de crédito. No es posible obtener los movimientos de caja asociados a la venta.');
                }

                foreach ($movimientos_cajas as $movimiento_caja) {
                    $movimiento_caja->estado = 'IN';
                    $movimiento_caja->save();

                    $caja = Caja::where('id', $movimiento_caja->caja_destino_id)->first();
                    if (!$caja) {
                        return back()->with('error-message', 'Hubo un problema al realizar la nota de crédito. No es posible obtener la caja asociada a la venta.');
                    }
                    $caja->monto = $caja->monto - $movimiento_caja->monto;
                    $caja->save();
                }

                $movimientos_bancos = MovimientoBanco::where('venta_id', $venta->id)->get();
                if (!$movimientos_bancos) {
                    return back()->with('error-message', 'Hubo un problema al realizar la nota de crédito. No es posible obtener los movimientos de bancos asociados a la venta.');
                }

                foreach ($movimientos_bancos as $movimiento_banco) {
                    $movimiento_banco->estado = 'IN';
                    $movimiento_banco->save();

                    $cuenta_bancaria = CuentaBancaria::where('id', $movimiento_banco->cuenta_bancaria_destino_id)->first();
                    if (!$cuenta_bancaria) {
                        return back()->with('error-message', 'Hubo un problema al realizar la nota de crédito. No es posible obtener el banco asociado a la venta.');
                    }
                    $cuenta_bancaria->monto = $cuenta_bancaria->monto - $movimiento_banco->monto;
                    $cuenta_bancaria->save();
                }
            }

            $asiento_contable = AsientoContable::where('venta_id', $venta->id)->first();
            if ($asiento_contable) {
                $numero_asiento = $asiento_contable->numero + 1;

                $new_asiento_contable = new AsientoContable();
                $new_asiento_contable->numero = $numero_asiento;
                $new_asiento_contable->fecha = Carbon::now();
                $new_asiento_contable->origen = 'NOTA CREDITO';
                $new_asiento_contable->nota_credito_id = $nota_credito->id;
                $new_asiento_contable->moneda_id = 1;
                $new_asiento_contable->cotizacion_id = null;
                $new_asiento_contable->unidad_negocio_id = $asiento_contable->unidad_negocio_id;
                $new_asiento_contable->subunidad_negocio_id = $asiento_contable->subunidad_negocio_id;
                $new_asiento_contable->save();

                $asiento_contable_detalles = AsientoContableDetalle::where('asiento_id', $asiento_contable->id)->get();
                foreach ($asiento_contable_detalles as $detalle) {
                    $new_asiento_contable_detalle = new AsientoContableDetalle();
                    $new_asiento_contable_detalle->asiento_id = $new_asiento_contable->id;
                    $new_asiento_contable_detalle->cuenta_contable_id = $detalle->cuenta_contable_id;
                    $new_asiento_contable_detalle->descripcion = 'NOTA DE CREDITO S/ VENTA N° ' . $venta->numero_factura . ' - CLIENTE: ' . $venta->cliente->nombre;
                    if ($detalle->debe) {
                        $new_asiento_contable_detalle->haber = $detalle->debe;
                        if ($request->reversar_pago == 'false') {
                            $new_asiento_contable_detalle->haber = 0;
                        }
                    } elseif ($detalle->haber) {
                        $new_asiento_contable_detalle->debe = $detalle->haber;
                    }

                    $new_asiento_contable_detalle->centro_costo_id = $detalle->centro_costo_id;
                    $new_asiento_contable_detalle->subcentro_costo_id = $detalle->subcentro_costo_id;
                    if ($new_asiento_contable_detalle->haber > 0 || $new_asiento_contable_detalle->debe > 0) {
                        $new_asiento_contable_detalle->save();
                    }

                    $saldo_cuenta_contable = SaldoCuentaContable::where('id', $new_asiento_contable_detalle->cuenta_contable_id)->first();
                    if ($saldo_cuenta_contable) {
                        if ($new_asiento_contable_detalle->debe) {
                            $saldo_cuenta_contable->saldo = $saldo_cuenta_contable->saldo - $new_asiento_contable_detalle->debe;
                        } elseif ($new_asiento_contable_detalle->haber) {
                            $saldo_cuenta_contable->saldo = $saldo_cuenta_contable->saldo + $new_asiento_contable_detalle->haber;
                        }
                        if ($new_asiento_contable_detalle->haber > 0 || $new_asiento_contable_detalle->debe > 0) {
                            $saldo_cuenta_contable->save();
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('notas_creditos.index')->with('success-message', 'La nota de crédito N° ' . $nota_credito->numero_nota_credito . ' fue creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('notas_creditos.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('anular_notas_creditos');

        DB::beginTransaction();

        try {
            $nota_credito = NotaCredito::findOrFail($id);
            if (!Carbon::parse($nota_credito->fecha)->isSameMonth(Carbon::now())) {
                return back()->with('error-message', 'No se puede anular la nota de crédito porque no pertenece al mes y año actual.');
            }
            $nota_credito->anulado_por_id = Auth::id();
            $nota_credito->estado = 'IN';
            $nota_credito->save();

            $venta = Venta::where('id', $nota_credito->venta_id)->first();
            if (!$venta) {
                return back()->with('error-message', 'Hubo un problema al anular la nota de crédito. No es posible obtener la venta.');
            }

            if ($venta->monto_total == $venta->cobros->sum('monto')) {
                $venta->estado = 'AC';
            } else {
                $venta->saldo = $venta->saldo + ($venta->monto_total - $venta->cobros->sum('monto'));
                $venta->estado = 'PE';
            }
            $venta->save();

            if ($nota_credito->pago_reversado == true) {
                $movimientos_cajas = MovimientoCaja::where('venta_id', $venta->id)->get();
                if (!$movimientos_cajas) {
                    return back()->with('error-message', 'Hubo un problema al anular la nota de crédito. No es posible obtener los movimientos de caja asociados a la venta.');
                }
                foreach ($movimientos_cajas as $movimiento_caja) {
                    $movimiento_caja->estado = 'AC';
                    $movimiento_caja->save();

                    $caja = Caja::where('id', $movimiento_caja->caja_destino_id)->first();
                    if (!$caja) {
                        return back()->with('error-message', 'Hubo un problema al anular la nota de crédito. No es posible obtener la caja asociada a la venta.');
                    }
                    $caja->monto = $caja->monto + $movimiento_caja->monto;
                    $caja->save();
                }

                $movimientos_bancos = MovimientoBanco::where('venta_id', $venta->id)->get();
                if (!$movimientos_bancos) {
                    return back()->with('error-message', 'Hubo un problema al anular la nota de crédito. No es posible obtener los movimientos de bancos asociados a la venta.');
                }
                foreach ($movimientos_bancos as $movimiento_banco) {
                    $movimiento_banco->estado = 'AC';
                    $movimiento_banco->save();

                    $cuenta_bancaria = CuentaBancaria::where('id', $movimiento_banco->cuenta_bancaria_destino_id)->first();
                    if (!$cuenta_bancaria) {
                        return back()->with('error-message', 'Hubo un problema al anular la nota de crédito. No es posible obtener el banco asociado a la venta.');
                    }
                    $cuenta_bancaria->monto = $cuenta_bancaria->monto + $movimiento_banco->monto;
                    $cuenta_bancaria->save();
                }
            }

            $asiento_contable = AsientoContable::where('nota_credito_id', $nota_credito->id)->first();
            foreach ($asiento_contable->detalles as $detalle) {
                $cuenta_contable = CuentaContable::find($detalle->cuenta_contable_id)->first();
                if ($cuenta_contable) {
                    $saldo_cuenta_contable = SaldoCuentaContable::where('cuenta_contable_id', $cuenta_contable->id)->first();
                    if ($saldo_cuenta_contable) {
                        if ($detalle->debe) {
                            $saldo_cuenta_contable->saldo = $saldo_cuenta_contable->saldo - $detalle->debe;
                        } elseif ($detalle->haber) {
                            $saldo_cuenta_contable->saldo = $saldo_cuenta_contable->saldo + $detalle->haber;
                        }
                        $saldo_cuenta_contable->saldo = $saldo_cuenta_contable->saldo + $detalle->haber;
                        $saldo_cuenta_contable->save();

                        SaldoCuentaContableDetalle::where('saldo_cuenta_contable_id', $saldo_cuenta_contable->id)->where('venta_id', $venta->id)->delete();
                    } else {
                        return back()->with('error-message', 'Hubo un problema al anular la nota de crédito. No se encontró la cuenta contable del asiento.');
                    }
                } else {
                    return back()->with('error-message', 'Hubo un problema al anular la nota de crédito. No se encontró la cuenta contable del asiento.');
                }
            }
            $asiento_contable->estado = 'IN';
            $asiento_contable->actualizado_por_id = Auth::id();
            $asiento_contable->save();

            DB::commit();

            return redirect()->route('notas_creditos.index')->with('error-message','La nota de crédito N° ' . $nota_credito->numero_nota_credito . ' fue anulada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('notas_creditos.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_notas_creditos');

        DB::beginTransaction();

        try {
            $nota_credito = NotaCredito::findOrFail($id);

            if ($nota_credito->estado == 'AC' || $nota_credito->estado == 'UT') {
                return back()->with('error-message', 'La nota de crédito no se puede eliminar. No se encuentra previamente anulada.');
            }

            $asiento_contable = AsientoContable::where('nota_credito_id', $nota_credito->id)->first();
            $asientos_contables_detalles = AsientoContableDetalle::where('asiento_id', $asiento_contable->id)->delete();
            $asiento_contable->delete();

            $nota_credito->delete();

            DB::commit();

            return redirect()->route('notas_creditos.index')->with('success-message','La nota de crédito N° ' . $nota_credito->numero_nota_credito . ' fue eliminada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('notas_creditos.index')->with('error-message', 'La nota de crédito N° ' . $nota_credito->numero_nota_credito . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('notas_creditos.index')->with('error-message', $e->getMessage());
            }
        }
    }

    public function get_ventas($id)
    {
        $this->authorize('crear_notas_creditos');

        DB::beginTransaction();

        try {
            $ventas = Venta::where('cliente_id', $id)->where('estado', '!=', 'IN')->where('estado', '!=', 'NC')->get();
            foreach ($ventas as $venta) {
                $venta->monto_total = number_format($venta->monto_total, 0, ',', '.');
                $venta->saldo = number_format($venta->saldo, 0, ',', '.');
            }

            return response()->json([
                'ventas' => $ventas,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('notas_creditos.index')->with('error-message', $e->getMessage());
        }
    }
}
