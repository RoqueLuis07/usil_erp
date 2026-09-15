<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\Models\Cotizacion;


class CotizacionController extends Controller
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
        $this->authorize('ver_cotizaciones');

        return view('cotizaciones.index');
    }

    public function index_ajax()
    {
        $this->authorize('ver_cotizaciones');

        try {
            $cotizaciones = Cotizacion::orderBy('fecha', 'desc')->get();
            foreach ($cotizaciones as $cotizacion) {
                $cotizacion->fecha = Carbon::parse($cotizacion->fecha)->format('d/m/Y H:i:s');
                $cotizacion->precio_compra = number_format($cotizacion->precio_compra, 0, ',', '.');
                $cotizacion->precio_venta = number_format($cotizacion->precio_venta, 0, ',', '.');
            }
            return response()->json([
                'cotizaciones' => $cotizaciones,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('cotizaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_cotizaciones');

        try {
            $cotizacion = Cotizacion::findOrFail($id);
            $cotizacion->fecha = Carbon::parse($cotizacion->fecha)->format('d/m/Y H:i:s');
            $cotizacion->precio_compra = number_format($cotizacion->precio_compra, 0, ',', '.');
            $cotizacion->precio_venta = number_format($cotizacion->precio_venta, 0, ',', '.');
            return response()->json([
                'cotizacion' => $cotizacion,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('cotizaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_cotizaciones');

        DB::beginTransaction();

            $request->validate([
                'fecha' => 'required',
                'precio_compra' => 'required',
                'precio_venta' => 'required'
            ]);

            try {
                $cotizacion = new Cotizacion();
                $cotizacion->fecha = $request->fecha;
                $cotizacion->precio_compra = $request->precio_compra;
                $cotizacion->precio_venta = $request->precio_venta;
                $cotizacion->save();

                DB::commit();

                $cotizaciones = Cotizacion::orderBy('fecha', 'desc')->get();
                foreach ($cotizaciones as $cotizacion) {
                    $cotizacion->fecha = Carbon::parse($cotizacion->fecha)->format('d/m/Y H:i:s');
                    $cotizacion->precio_compra = number_format($cotizacion->precio_compra, 0, ',', '.');
                    $cotizacion->precio_venta = number_format($cotizacion->precio_venta, 0, ',', '.');
                }
                return response()->json([
                    'message' => 'La cotización fue creada exitosamente.',
                    'cotizaciones' => $cotizaciones,
                    'selected' => $cotizacion,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('cotizaciones.index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_cotizaciones');

        try {
            $cotizacion = Cotizacion::findOrFail($id);
            return response()->json([
                'cotizacion' => $cotizacion,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('cotizaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_cotizaciones');

        DB::beginTransaction();

        $request->validate([
            'fecha' => 'required',
            'precio_compra' => 'required',
            'precio_venta' => 'required'
        ]);

        try {
            $cotizacion = Cotizacion::findOrFail($id);
            $cotizacion->fecha = $request->fecha;
            $cotizacion->precio_compra = $request->precio_compra;
            $cotizacion->precio_venta = $request->precio_venta;
            $cotizacion->save();

            DB::commit();

            $cotizaciones = Cotizacion::get();
            return response()->json([
                'message' => 'La cotización fue actualizada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('cotizaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_cotizaciones');

        try {
            $cotizacion = Cotizacion::findOrFail($id);
            return response()->json([
                'cotizacion' => $cotizacion,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('cotizaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_cotizaciones');

        DB::beginTransaction();

        try {
            $cotizacion = Cotizacion::findOrFail($id);
            $cotizacion->delete();

            DB::commit();

            return response()->json([
                'message' => 'La cotización fue eliminada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('cotizaciones.index')->with('error-message', 'La cotización no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('cotizaciones.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
