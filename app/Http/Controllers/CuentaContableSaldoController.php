<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\Models\SaldoCuentaContable;
use App\Models\SaldoCuentaContableDetalle;

class CuentaContableSaldoController extends Controller
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

    public function index(Request $request)
    {
        $this->authorize('ver_cuentas_contables_saldos');

        try {
            $anho_seleccionado = (int)$request->anho ? (int)$request->anho : (int)Carbon::today()->year;
            $detallesExistentes = SaldoCuentaContableDetalle::where('anho', $anho_seleccionado)->exists();

            if ($detallesExistentes) {
                $saldos = SaldoCuentaContable::with(['detalles' => function ($query) use ($anho_seleccionado) {
                                $query->where('anho', $anho_seleccionado);
                            }])
                            ->whereHas('cuentaContable', function ($query) {
                                $query->where('imputable', true);
                            })
                            ->orderBy('id', 'asc')
                            ->get();
            } else {
                $saldos = collect();
            }


            $meses = collect([
                'Enero' => [],
                'Febrero' => [],
                'Marzo' => [],
                'Abril' => [],
                'Mayo' => [],
                'Junio' => [],
                'Julio' => [],
                'Agosto' => [],
                'Septiembre' => [],
                'Octubre' => [],
                'Noviembre' => [],
                'Diciembre' => [],
            ]);

            $anhos = collect();
            $total_anho = [];
            $saldos_recursivos = collect();

            foreach ($saldos as $saldo) {
                foreach ($saldo->detalles as $detalle) {
                    if (!$anhos->contains($detalle->anho)) {
                        $anhos->push($detalle->anho);
                    }

                    $mes = Carbon::create()->month($detalle->mes)->translatedFormat('F');
                    $mes = Str::title($mes);

                    $debe = $detalle->debe ?? 0;
                    $haber = $detalle->haber ?? 0;

                    if ($meses->has($mes)) {
                        $cuenta_id = $detalle->saldoCuentaContable->cuenta_contable_id;

                        $meses = $meses->map(function ($item, $key) use ($mes, $debe, $haber, $cuenta_id, $detalle) {
                            if ($key === $mes) {
                                if (isset($item[$cuenta_id])) {
                                    $item[$cuenta_id]['debe'] += $debe;
                                    $item[$cuenta_id]['haber'] += $haber;
                                    $item[$cuenta_id]['saldo'] += $debe - $haber;
                                } else {
                                    $item[$cuenta_id] = [
                                        'debe' => $debe,
                                        'haber' => $haber,
                                        'saldo' => $debe - $haber,
                                        'cuenta_id' => $cuenta_id
                                    ];
                                }
                            }
                            return $item;
                        });

                        $total_saldo = $saldo->cuentaContable->obtenerSaldoRecursivo($anho_seleccionado);

                        if (!isset($total_anho[$cuenta_id])) {
                            $total_anho[$cuenta_id] = [
                                'debe' => $debe,
                                'haber' => $haber,
                                'saldo' => $debe - $haber,
                                'cuenta_id' => $cuenta_id,
                            ];
                        } else {
                            $total_anho[$cuenta_id]['debe'] += $debe;
                            $total_anho[$cuenta_id]['haber'] += $haber;
                            $total_anho[$cuenta_id]['saldo'] += $debe - $haber;
                        }
                    }
                }
            }

            $meses = $meses->filter(function ($cuentas) {
                return collect($cuentas)->filter(function ($cuenta) {
                    return $cuenta['debe'] != 0 || $cuenta['haber'] != 0 || $cuenta['saldo'] != 0;
                })->isNotEmpty();
            });

            if ($anhos->isEmpty()) {
                $anhos->push((int)Carbon::today()->year);
            }

            return view('cuentas_contables_saldos/index')->with(compact('saldos', 'meses', 'anhos', 'anho_seleccionado', 'total_anho'));
        } catch (\Exception $e) {
            return redirect()->route('cuentas_contables.index')->with('error-message', $e->getMessage());
        }
    }
}
