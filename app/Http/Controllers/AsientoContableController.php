<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\Models\AsientoContable;
use App\Models\AsientoContableDetalle;
use App\Models\CuentaContable;
use App\Models\SaldoCuentaContable;
use App\Models\SaldoCuentaContableDetalle;
use App\Models\UnidadNegocioContable;
use App\Models\SubnidadNegocioContable;
use App\Models\CentroCostoContable;
use App\Models\SubcentroCostoContable;

class AsientoContableController extends Controller
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
        $this->authorize('ver_asientos_contables');

        try {
            $asientos = AsientoContableDetalle::whereHas('asiento', function ($query) {
                $query->where('estado', 'AC');
            })->orderBy('id', 'desc')->get();
            return view('asientos_contables/index')->with(compact('asientos'));
        } catch (\Exception $e) {
            return redirect()->route('asientos_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_asientos_contables');

        try {
            $asiento = AsientoContable::with('detalles')->findOrFail($id);
            return view('asientos_contables/show')->with(compact('asiento'));
        } catch (\Exception $e) {
            return redirect()->route('asientos_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_asientos_contables');

        try {
            $asiento_contable = AsientoContable::orderBy('id', 'desc')->first();
            if ($asiento_contable) {
                $id_asiento = $asiento_contable->id + 1;
                $numero_asiento = $asiento_contable->numero + 1;
            } else {
                $id_asiento = 1;
                $numero_asiento = 1;
            }

            $cuentas_contables = CuentaContable::where('imputable', true)->where('estado', 'AC')->get();
            $unidades_negocios = UnidadNegocioContable::where('estado', 'AC')->get();
            $centros_costos = CentroCostoContable::where('estado', 'AC')->get();

            return view('asientos_contables/create')->with(compact('cuentas_contables', 'id_asiento', 'numero_asiento', 'unidades_negocios', 'centros_costos'));
        } catch (\Exception $e) {
            return redirect()->route('asientos_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_asientos_contables');

        $request->validate([
            'fecha' => ['required', 'date', 'before_or_equal:today'],
            'unidad_negocio' => ['required', 'numeric'],
            'subunidad_negocio' => ['required', 'numeric'],

            'detalles' => ['required', 'array', 'max:2'],
            'detalles.*.cuenta_contable' => ['required', 'numeric'],
            'detalles.*.descripcion' => 'required',
            'detalles.*.centro_costo' => ['required', 'numeric'],
            'detalles.*.subcentro_costo' => ['required', 'numeric'],
            'detalles.*.debe' => ['nullable', 'numeric', 'min:1', 'required_without:detalles.*.haber'],
            'detalles.*.haber' => ['nullable', 'numeric', 'min:1', 'required_without:detalles.*.debe'],

            'diferencia' => ['required', 'numeric']
        ]);

        DB::beginTransaction();

        try {
            $old_asiento = AsientoContable::orderBy('id', 'desc')->first();
            if ($old_asiento) {
                $numero_asiento = $old_asiento->numero + 1;
            } else {
                $numero_asiento = 1;
            }

            $asiento = new AsientoContable();
            $asiento->numero = $numero_asiento;
            $asiento->fecha = $request->fecha;
            $asiento->origen = 'MANUAL';
            $asiento->moneda_id = 1;
            $asiento->unidad_negocio_id = $request->unidad_negocio;
            $asiento->subunidad_negocio_id = $request->subunidad_negocio;
            $asiento->cargado_por_id = Auth::id();
            $asiento->save();

            foreach($request->detalles as $detalle) {
                $asiento_detalle = new AsientoContableDetalle;
                $asiento_detalle->asiento_id = $asiento->id;
                $asiento_detalle->cuenta_contable_id = $detalle['cuenta_contable'];
                $asiento_detalle->descripcion = $detalle['descripcion'];
                $asiento_detalle->debe = $detalle['debe'];
                $asiento_detalle->haber = $detalle['haber'];
                $asiento_detalle->centro_costo_id = $detalle['centro_costo'];
                $asiento_detalle->subcentro_costo_id = $detalle['subcentro_costo'];
                $asiento_detalle->save();

                $saldo_cuenta = SaldoCuentaContable::where('cuenta_contable_id', $asiento_detalle->cuenta_contable_id)->first();
                if ($asiento_detalle->debe) {
                    $saldo_cuenta->saldo = $saldo_cuenta->saldo + $asiento_detalle->debe;
                } else {
                    $saldo_cuenta->saldo = $saldo_cuenta->saldo - $asiento_detalle->haber;
                }
                $saldo_cuenta->save();

                $saldo_cuenta_detalle = new SaldoCuentaContableDetalle();
                $saldo_cuenta_detalle->saldo_cuenta_contable_id = $saldo_cuenta->id;
                $saldo_cuenta_detalle->mes = Carbon::today()->format('m');
                $saldo_cuenta_detalle->anho = Carbon::today()->format('Y');
                if ($asiento_detalle->debe) {
                    $saldo_cuenta_detalle->debe = $asiento_detalle->debe;
                } else {
                    $saldo_cuenta_detalle->haber = $asiento_detalle->haber;
                }
                $saldo_cuenta_detalle->save();
            }

            DB::commit();

            return redirect()->route('asientos_contables.index')->with('success-message', 'El asiento N° ' . $asiento->id . ' fue creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('asientos_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_asientos_contables');

        try {

            return view('asientos_contables/edit');
        } catch (\Exception $e) {
            return redirect()->route('asientos_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_asientos_contables');

        $request->validate([

        ]);

        DB::beginTransaction();

        try {

            DB::commit();

            return redirect()->route('asientos_contables.index')->with('success-message', 'El asiento N° ' . $asiento->id . ' fue actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('asientos_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_asientos_contables');

        DB::beginTransaction();

        try {
            $asiento = AsientoContable::findOrFail($id);

            if ($asiento->origen != 'MANUAL') {
                return back()->with('error-message', 'El asiento no se puede eliminar. Este proviene de un/a ' . Str::title($asiento->origen) . '.');
            }

            $mes_asiento = Carbon::parse($asiento->fecha)->format('m');
            $anho_asiento = Carbon::parse($asiento->fecha)->format('y');

            $asiento_detalles = AsientoContableDetalle::where('asiento_id', $asiento->id)->get();
            foreach ($asiento_detalles as $detalle) {
                if ($detalle->debe != null) {
                    $monto = $detalle->debe;
                } else {
                    $monto = $detalle->haber;
                }

                $saldo_cuenta_contable = SaldoCuentaContable::where('cuenta_contable_id', $detalle->cuenta_contable_id)->first();
                $saldo_cuenta_contable->saldo = $saldo_cuenta_contable->saldo - $monto;
                $saldo_cuenta_contable->save();

                $saldo_cuenta_contable_detalles = SaldoCuentaContableDetalle::where('saldo_cuenta_contable_id', $saldo_cuenta_contable->id)->where('mes', $mes_asiento)->where('anho', $anho_asiento)->get();
                foreach ($saldo_cuenta_contable_detalles as $saldo_detalle) {
                    if ($detalle->debe != null) {
                        $saldo_detalle->debe = $saldo_detalle->debe - $monto;
                    } else {
                        $saldo_detalle->haber = $saldo_detalle->haber - $monto;
                    }
                    $saldo_detalle->save();
                }
                $detalle->delete();
            }

            $asiento->delete();

            DB::commit();

            return redirect()->route('asientos_contables.index')->with('success-message','El asiento contable N° ' . $asiento->id . ' fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('asientos_contables.index')->with('error-message', 'El asiento contable N° ' . $asiento->id . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('asientos_contables.index')->with('error-message', $e->getMessage());
            }
        }
    }

    public function get_subunidades_negocios($id)
    {
        $this->authorize('crear_asientos_contables');

        DB::beginTransaction();

        try {
            $subunidades_negocios = SubunidadNegocioContable::where('unidad_id', $id)->where('estado', 'AC')->get();

            return response()->json([
                'subunidades_negocios' => $subunidades_negocios,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('asientos_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_subcentros_costos($id)
    {
        $this->authorize('crear_asientos_contables');

        DB::beginTransaction();

        try {
            $subcentros_costos = SubcentroCostoContable::where('centro_costo_id', $id)->where('estado', 'AC')->get();

            return response()->json([
                'subcentros_costos' => $subcentros_costos,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('asientos_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function renumerar(Request $request)
    {
        // $this->authorize('renumerar_asientos_contables');

        $request->validate([
            'fecha' => ['required', 'date_format:m-Y'],
        ]);

        DB::beginTransaction();

        try {
            $mes = explode('-', $request->fecha)[0];
            $anho = explode('-', $request->fecha)[1];

            $fecha_inicio = Carbon::createFromDate($anho, $mes, 1)->startOfMonth();
            $fecha_fin = Carbon::createFromDate($anho, $mes, 1)->endOfMonth();


            $asientos = AsientoContable::whereBetween('fecha', [$fecha_inicio, $fecha_fin])->where('estado', 'AC')->get();
            if ($asientos->isEmpty()) {
                return back()->with('error-message', 'No se encontraron asientos contables para renumerar.');
            }

            foreach ($asientos as $key => $asiento) {
                $numero_asiento = $key + 1;

                $asiento->numero = $numero_asiento;
                $asiento->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'Asientos contables renumerados exitosamente.',
            ]);


        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('asientos_contables.index')->with('error-message', $e->getMessage());
        }
    }
}
