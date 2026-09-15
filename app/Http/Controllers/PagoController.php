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

use App\Models\Pago;
use App\Models\OrdenPago;
use App\Models\CuentaBancaria;
use App\Models\Caja;
use App\Models\MovimientoCaja;
use App\Models\MovimientoBanco;

class PagoController extends Controller
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
        $this->authorize('ver_pagos');

        try {
            $pagos = Pago::whereHas('ordenPago', function ($query) {
                $query->whereIn('estado', ['AP', 'PA']);
            })
            ->orderBy('id', 'desc')
            ->get();

            return view('pagos/index')->with(compact('pagos'));
        } catch (\Exception $e) {
            return redirect()->route('pagos.index')->with('error-message', $e->getMessage());
        }
    }

    public function store($id)
    {
        $this->authorize('crear_pagos');

        DB::beginTransaction();

        try {
            $pago = Pago::findOrFail($id);
            if ($pago->estado != 'PE') {
                return back()->with('error-message', 'El pago de la OP N° ' . $pago->orden_pago_id . ' no se puede realizar porque ya no se encuentra pendiente.');
            }

            $orden_pago = OrdenPago::findOrFail($pago->orden_pago_id);
            if ($orden_pago->estado != 'AP') {
                return back()->with('error-message', 'El pago de la OP N° ' . $pago->orden_pago_id . ' no se puede realizar porque la OP no se encuentra aprobada.');
            }

            $pago->fecha = Carbon::now();
            $pago->estado = 'PA';
            $pago->save();

            $orden_pago->estado = 'PA';
            $orden_pago->save();

            if ($pago->forma_pago_id == 1 || $pago->forma_pago_id == 5) {
                $caja = Caja::where('id', $pago->caja_id)->first();
                if (!$caja) {
                    return back()->with('error-message', 'El pago de la OP N° ' . $pago->orden_pago_id . ' no se puede realizar. No se encuentra la caja asociada.');
                }

                $monto = $pago->monto;
                if ($pago->ordenPago->moneda_id == 2) {
                    $monto = $pago->monto * $pago->ordenPago->cotizacion->precio_venta;
                }
                $caja->monto = $caja->monto - $monto;

                $movimiento_caja = new MovimientoCaja();
                $movimiento_caja->fecha = Carbon::now();
                $movimiento_caja->caja_origen_id = $caja->id;
                $movimiento_caja->sentido = 'D';
                $movimiento_caja->monto = $monto;
                $movimiento_caja->tipo_movimiento_id = 13; //movimiento de compra
                $movimiento_caja->motivo = 'PAGO DE OP N° ' . $pago->orden_pago_id;
                $movimiento_caja->pago_id = $pago->id;
                $movimiento_caja->estado = 'AP';
                $movimiento_caja->creado_por_id = Auth::id();
                $movimiento_caja->aprobado_por_id = Auth::id();
                $movimiento_caja->save();

            } else if ($orden_pago->forma_pago_id == 4 || $orden_pago->forma_pago_id == 7) {
                $cuenta_bancaria = CuentaBancaria::where('id', $pago->cuenta_bancaria_id)->first();
                if (!$cuenta_bancaria) {
                    return back()->with('error-message', 'El pago de la OP N° ' . $pago->orden_pago_id . ' no se puede realizar. No se encuentra la cuenta bancaria asociada.');
                }

                $monto = $pago->monto;
                if ($pago->ordenPago->moneda_id == 1 && $cuenta_bancaria->moneda_id == 2) {
                    $monto = $pago->monto / $pago->ordenPago->cotizacion->precio_compra;
                } elseif ($pago->ordenPago->moneda_id == 2 && $cuenta_bancaria->moneda_id == 1) {
                    $monto = $pago->monto * $pago->ordenPago->cotizacion->precio_venta;
                }
                $cuenta_bancaria->monto = $cuenta_bancaria->monto - $monto;

                $movimiento_banco = new MovimientoBanco();
                $movimiento_banco->fecha = Carbon::now();
                $movimiento_banco->cuenta_bancaria_origen_id = $cuenta_bancaria->id;
                $movimiento_banco->sentido = 'D';
                $movimiento_banco->monto = $monto;
                $movimiento_banco->tipo_movimiento_id = 13; //movimiento de compra
                $movimiento_banco->motivo = 'PAGO DE OP N° ' . $pago->orden_pago_id;
                $movimiento_banco->pago_id = $pago->id;
                $movimiento_banco->estado = 'AP';
                $movimiento_banco->creado_por_id = Auth::id();
                $movimiento_banco->aprobado_por_id = Auth::id();
                $movimiento_banco->save();
            }

            if ($orden_pago->compra_id) {
                $compra = Compra::where('id', $orden_pago->compra_id)->first();
                if (!$compra) {
                    return back()->with('error-message', 'El pago de la OP N° ' . $pago->orden_pago_id . ' no se puede realizar. No se encuentra la compra asociada.');
                }

                $compra->estado = 'PA';
                $compra->save();
            }

            DB::commit();

            return redirect()->route('pagos.index')->with('success-message', 'El pago de la OP N° ' . $pago->orden_pago_id . ' fue realizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('pagos.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('anular_pagos');

        DB::beginTransaction();

        try {
            $pago = Pago::findOrFail($id);

            if ($pago->estado != 'PA') {
                return back()->with('error-message', 'El pago de la OP N° ' . $pago->orden_pago_id . ' no se puede anular porque no se encuentra previamente realizado.');
            }

            $pago->fecha = null;
            $pago->estado = 'PE';
            $pago->save();

            $orden_pago = OrdenPago::findOrFail($pago->orden_pago_id);
            $orden_pago->estado = 'AP';
            $orden_pago->save();

            if ($pago->forma_pago_id == 1 || $pago->forma_pago_id == 5) {
                $caja = Caja::where('id', $pago->caja_id)->first();
                if (!$caja) {
                    return back()->with('error-message', 'El pago de la OP N° ' . $pago->orden_pago_id . ' no se puede anular. No se encuentra la caja asociada.');
                }

                $monto = $pago->monto;
                if ($pago->ordenPago->moneda_id == 2) {
                    $monto = $pago->monto * $pago->ordenPago->cotizacion->precio_venta;
                }
                $caja->monto = $caja->monto + $monto;

                $movimiento_caja = MovimientoCaja::where('pago_id', $pago->id)->first();
                if (!$movimiento_caja) {
                    return back()->with('error-message', 'El pago de la OP N° ' . $pago->orden_pago_id . ' no se puede anular. No se encuentra el movimiento de caja asociado.');
                }

                $movimiento_caja->delete();
            } else if ($orden_pago->forma_pago_id == 4 || $orden_pago->forma_pago_id == 7) {
                $cuenta_bancaria = CuentaBancaria::where('id', $pago->cuenta_bancaria_id)->first();
                if (!$cuenta_bancaria) {
                    return back()->with('error-message', 'El pago de la OP N° ' . $pago->orden_pago_id . ' no se puede anular. No se encuentra la cuenta bancaria asociada.');
                }

                $monto = $pago->monto;
                if ($pago->ordenPago->moneda_id == 1 && $cuenta_bancaria->moneda_id == 2) {
                    $monto = $pago->monto / $pago->ordenPago->cotizacion->precio_compra;
                } elseif ($pago->ordenPago->moneda_id == 2 && $cuenta_bancaria->moneda_id == 1) {
                    $monto = $pago->monto * $pago->ordenPago->cotizacion->precio_venta;
                }
                $cuenta_bancaria->monto = $cuenta_bancaria->monto - $monto;

                $movimiento_banco = MovimientoBanco::where('pago_id', $pago->id)->first();
                if (!$movimiento_banco) {
                    return back()->with('error-message', 'El pago de la OP N° ' . $pago->orden_pago_id . ' no se puede anular. No se encuentra el movimiento de banco asociado.');
                }

                $movimiento_banco->delete();
            }

            if ($orden_pago->compra_id) {
                $compra = Compra::where('id', $orden_pago->compra_id)->first();
                if (!$compra) {
                    return back()->with('error-message', 'El pago de la OP N° ' . $pago->orden_pago_id . ' no se puede anular. No se encuentra la compra asociada.');
                }

                $compra->estado = 'OP';
                $compra->save();
            }

            DB::commit();

            return redirect()->route('pagos.index')->with('error-message','El pago de la OP N° ' . $pago->orden_pago_id . ' fue anulado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('pagos.index')->with('error-message', $e->getMessage());
        }
    }
}
