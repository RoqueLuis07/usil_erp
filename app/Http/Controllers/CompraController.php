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
use Carbon\Carbon;


use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\OrdenCompra;
use App\Models\Proveedor;
use App\Models\TimbradoProveedor;
use App\Models\Moneda;
use App\Models\Cotizacion;
use App\Models\UnidadNegocioContable;
use App\Models\SubunidadNegocioContable;
use App\Models\Articulo;
use App\Models\CentroCostoContable;
use App\Models\SubcentroCostoContable;
use App\Models\CuentaContable;
use App\Models\AsientoContable;
use App\Models\AsientoContableDetalle;
use App\Models\SaldoCuentaContable;
use App\Models\SaldoCuentaContableDetalle;


class CompraController extends Controller
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
        $this->authorize('ver_compras');

        try {
            $compras = Compra::with('detalles')->orderBy('id', 'desc')->get();
            return view('compras/index')->with(compact('compras'));
        } catch (\Exception $e) {
            return redirect()->route('compras.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_compras');

        try {
            $compra = Compra::with('detalles', 'asientoContable')->findOrFail($id);

            $monto_total = 0;
            foreach ($compra->detalles as $detalle) {
                $detalle->subtotal = $detalle->cantidad * $detalle->precio_costo;
                $monto_total += $detalle->subtotal;
            }
            $compra->monto_total = $monto_total;

            return view('compras/show')->with(compact('compra'));
        } catch (\Exception $e) {
            return redirect()->route('compras.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_compras');

        try {
            $proveedores = Proveedor::where('estado', 'AC')->get();
            $monedas = Moneda::where('estado', 'AC')->get();
            $unidades_negocios = UnidadNegocioContable::where('estado', 'AC')->get();
            $articulos = Articulo::where('compra_venta', 'COMPRA')->where('estado', 'AC')->get();
            $centros_costos = CentroCostoContable::where('estado', 'AC')->get();
            return view('compras/create')->with(compact('proveedores', 'monedas', 'unidades_negocios', 'articulos', 'centros_costos'));
        } catch (\Exception $e) {
            return redirect()->route('compras.index')->with('error-message', $e->getMessage());
        }
    }

    public function create_from_orden($id)
    {
        $this->authorize('crear_compras');

        try {
            $orden_compra = OrdenCompra::with('detalles')->findOrFail($id);
            $timbrados = TimbradoProveedor::where('proveedor_id', $orden_compra->proveedor_id)->where('estado', 'AC')->get();
            $unidades_negocios = UnidadNegocioContable::where('estado', 'AC')->get();
            $centros_costos = CentroCostoContable::where('estado', 'AC')->get();
            return view('compras/create_from_orden')->with(compact('orden_compra', 'timbrados', 'unidades_negocios', 'centros_costos'));
        } catch (\Exception $e) {
            return redirect()->route('compras.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_compras');

        $request->validate([
            'fecha' => ['required', 'date'],
            'proveedor' => ['required', 'numeric'],
            'timbrado' => ['required', 'numeric'],
            'orden_compra' => ['required', 'numeric'],
            'numero_factura' => ['required', 'regex:/^\d{2}[1-9]-\d{2}[1-9]-\d{6}[1-9]$/', 'max:15', Rule::unique('compras', 'numero_factura')
                ->where(fn ($query) => $query->where('numero_factura', $request->numero_factura)
                ->where('proveedor_id', $request->proveedor)
                ->where('estado', '!=', 'AN'))],
            'moneda' => ['required', 'numeric'],
            'condicion_compra' => ['required'],
            'credito_a' => ['nullable', 'numeric', 'required_if:condicion_compra,CR', 'min:7', 'max:120'],
            'unidad_negocio' => ['required', 'numeric'],
            'subunidad_negocio' => ['required', 'numeric'],
            'observaciones' => 'nullable',
            'adjunto' => ['nullable', 'file', 'extensions:jpg,jpeg,png,pdf'],

            'detalles' => ['required', 'array'],
            'detalles.*.articulo' => ['required', 'numeric'],
            'detalles.*.descripcion' => 'nullable',
            'detalles.*.centro_costo' => ['required', 'numeric'],
            'detalles.*.subcentro_costo' => ['required', 'numeric'],
            'detalles.*.cantidad' => ['required', 'numeric', 'min:1'],
            'detalles.*.precio_costo' => ['required', 'numeric', 'min:1'],
            'detalles.*.iva' => ['required', 'numeric', 'min:0', 'max:10']
        ]);

        DB::beginTransaction();

        try {
            $compra = new Compra();
            $compra->fecha = $request->fecha;
            $compra->proveedor_id = $request->proveedor;
            $compra->condicion_compra = $request->condicion_compra;
            if ($request->condicion_compra == 'CR') {
                $compra->credito_a = $request->credito_a;
                $compra->fecha_vencimiento = Carbon::parse($compra->fecha)->addDays($compra->credito_a);
                $compra->estado = 'CR';
            } else {
                $compra->credito_a = null;
            }
            $compra->timbrado_id = $request->timbrado;
            $compra->numero_factura = $request->numero_factura;
            $compra->moneda_id = $request->moneda;
            if ($compra->moneda_id == 2) {
                $cotizacion = Cotizacion::orderBy('id', 'desc')->first();
                if (!$cotizacion) {
                    return back()->with('error-message', 'La compra en dólares no puede ser guardada, no se encuentra una cotización cargada.');
                }
                $compra->cotizacion_id = $cotizacion->id;
            }
            $compra->unidad_negocio_id = $request->unidad_negocio;
            $compra->subunidad_negocio_id = $request->subunidad_negocio;

            $monto_total = 0;
            foreach ($request->detalles as $detalle) {
                $monto_total += $detalle['cantidad'] * $detalle['precio_costo'];
            }
            $compra->monto_total = $monto_total;

            if ($request->orden_compra) {
                $compra->orden_compra_id = $request->orden_compra;
                $orden_compra = OrdenCompra::where('id', $compra->orden_compra_id)->first();
                if (!$orden_compra) {
                    return back()->with('error-message', 'La compra no puede ser guardada, no se encuentra la orden de compra seleccionada.');
                }

                $orden_compra->estado = 'CO';
                $orden_compra->save();
            }

            if ($request->adjunto) {
                $archivo = $request->adjunto;
                $extension = $archivo->getClientOriginalExtension();
                $directorio = 'storage/compras/factura';
                // Crear el directorio si no existe
                if (!File::exists($directorio)) {
                    File::makeDirectory($directorio, 0755, true);
                }

                if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png') {
                    $manager = new ImageManager(Driver::class);
                    $image = $manager->read($archivo);
                    $image = $image->encode(new AutoEncoder(quality: 50));
                    $nombre = $numero_orden . '_' . Carbon::now()->format('Ymd_His') . '_' . Str::lower($request->condicion_compra);
                    $nombre_archivo = $nombre . '.' . $extension;
                    $image->save($directorio . '/' . $nombre_archivo);
                } else {
                    $directorio_storage = 'public/compras/factura';
                    // Crear el directorio si no existe
                    if (!Storage::exists($directorio_storage)) {
                        Storage::makeDirectory($directorio_storage);
                    }

                    $nombre = $numero_orden . '_' . Carbon::now()->format('Ymd_His') . '_' . Str::lower($request->condicion_compra);
                    $nombre_archivo = $nombre . '.' . $extension;
                    Storage::putFileAs($directorio_storage . '/', $archivo, $nombre_archivo);
                }
                $compra->url_factura = $directorio . '/' . $nombre_archivo;
            }
            $compra->observaciones = $request->observaciones;
            $compra->cargado_por_id = Auth::id();
            $compra->save();

            $cuentas_contables_compras = collect();

            $old_asiento_contable = AsientoContable::orderBy('id', 'desc')->first();
            if ($old_asiento_contable) {
                $numero_asiento = $old_asiento_contable->numero + 1;
            } else {
                $numero_asiento = 1;
            }

            $asiento_contable = new AsientoContable();
            $asiento_contable->numero = $numero_asiento;
            $asiento_contable->fecha = Carbon::now();
            $asiento_contable->origen = 'COMPRA';
            $asiento_contable->compra_id = $compra->id;
            $asiento_contable->moneda_id = 1;
            $asiento_contable->cotizacion_id = null;
            $asiento_contable->unidad_negocio_id = $request->unidad_negocio;
            $asiento_contable->subunidad_negocio_id = $request->subunidad_negocio;
            $asiento_contable->save();

            foreach ($request->detalles as $detalle) { //utilizar el array obtenido arriba
                $compra_detalle = new CompraDetalle();
                $compra_detalle->compra_id = $compra->id;
                $compra_detalle->articulo_id = $detalle['articulo'];
                $compra_detalle->descripcion = removeAccents(Str::upper($detalle['descripcion']));
                $compra_detalle->cantidad = $detalle['cantidad'];
                $compra_detalle->precio_costo = $detalle['precio_costo'];
                $compra_detalle->iva = $detalle['iva'];
                $compra_detalle->centro_costo_id = $detalle['centro_costo'];
                $compra_detalle->subcentro_costo_id = $detalle['subcentro_costo'];
                $compra_detalle->save();

                $articulo = Articulo::where('id', $compra_detalle->articulo_id)->first();
                if (!$articulo) {
                    return back()->with('error-message', 'Hubo un problema al guardar la compra. No es posible obtener el artículo.');
                }

                $articulo->impuesto = $compra_detalle->iva;
                $articulo->ultima_compra = $compra->fecha;
                $articulo->detalle->precio_ultima_compra = $compra_detalle->precio_costo;
                if ($articulo->tiene_stock) {
                    $articulo->stock += $compra_detalle->cantidad;
                }
                $articulo->save();

                $cuenta_contable_compra = CuentaContable::where('id', $articulo->detalle->cuenta_compra_id)->first();
                if (!$cuenta_contable_compra) {
                    return back()->with('error-message', 'Hubo un problema al guardar la compra. No es posible obtener la cuenta contable del artículo.');
                } else {
                    $cuenta_contable_compra->monto = $compra_detalle->precio_costo;
                    $cuenta_contable_compra->iva = $compra_detalle->iva;
                    $cuenta_contable_compra->centro_costo = $compra_detalle->centro_costo_id;
                    $cuenta_contable_compra->subcentro_costo = $compra_detalle->subcentro_costo_id;
                    $cuentas_contables_compras->push($cuenta_contable_compra);
                }

                if ($cuentas_contables_compras->count() > 0) {
                    $monto_sin_iva = 0;
                    $monto_iva = 0;
                    foreach($cuentas_contables_compras as $cuenta_compra) {
                        $asiento_contable_detalle_sin_iva = new AsientoContableDetalle();
                        $asiento_contable_detalle_sin_iva->asiento_id = $asiento_contable->id;
                        $asiento_contable_detalle_sin_iva->cuenta_contable_id = $cuenta_compra->id;
                        $asiento_contable_detalle_sin_iva->descripcion = 'COMPRA S/ FACTURA ' . $compra->numero_factura . ' - PROVEEDOR: ' . $compra->proveedor->razon_social;
                        $cuenta_iva = 32; //cuenta contable por defecto del credito fiscal
                        switch ($cuenta_compra->iva) {
                            case 10:
                                $monto_sin_iva += $cuenta_compra->monto / 11;
                                $monto_iva += $cuenta_compra->monto - ($cuenta_compra->monto / 11);
                                $cuenta_iva = 32; //verificar cuenta contable del credito fiscal para 10%
                                break;
                            case 5:
                                $monto_sin_iva += $cuenta_compra->monto / 21;
                                $monto_iva += $cuenta_compra->monto - ($cuenta_compra->monto / 21);
                                $cuenta_iva = 32; //verificar cuenta contable del credito fiscal para 5%
                            default:
                                $monto_sin_iva += $cuenta_compra->monto;
                                break;
                        }
                        $asiento_contable_detalle_sin_iva->debe = $monto_sin_iva;
                        $asiento_contable_detalle_sin_iva->centro_costo_id = $cuenta_compra->centro_costo;
                        $asiento_contable_detalle_sin_iva->subcentro_costo_id = $cuenta_compra->subcentro_costo;
                        $asiento_contable_detalle_sin_iva->save();

                        $saldo_cuenta_contable_sin_iva = SaldoCuentaContable::where('id', $asiento_contable_detalle_sin_iva->cuenta_contable_id)->first();
                        if ($saldo_cuenta_contable_sin_iva) {
                            $saldo_cuenta_contable_sin_iva->saldo = $saldo_cuenta_contable_sin_iva->saldo + $asiento_contable_detalle_sin_iva->debe;
                            $saldo_cuenta_contable_sin_iva->save();
                        }

                        $saldo_cuenta_contable_detalle_sin_iva = new SaldoCuentaContableDetalle();
                        $saldo_cuenta_contable_detalle_sin_iva->saldo_cuenta_contable_id = $saldo_cuenta_contable_sin_iva->id;
                        $saldo_cuenta_contable_detalle_sin_iva->mes = Carbon::now()->format('m');
                        $saldo_cuenta_contable_detalle_sin_iva->anho = Carbon::now()->format('Y');
                        $saldo_cuenta_contable_detalle_sin_iva->debe = $asiento_contable_detalle_sin_iva->debe;
                        $saldo_cuenta_contable_detalle_sin_iva->save();

                        $asiento_contable_detalle_iva = new AsientoContableDetalle();
                        $asiento_contable_detalle_iva->asiento_id = $asiento_contable->id;
                        $asiento_contable_detalle_iva->cuenta_contable_id = $cuenta_iva;
                        $asiento_contable_detalle_iva->descripcion = 'I.V.A. S/ COMPRA FACTURA ' . $compra->numero_factura . ' - PROVEEDOR: ' . $compra->proveedor->razon_social;
                        $asiento_contable_detalle_iva->debe = $monto_iva;
                        $asiento_contable_detalle_iva->centro_costo_id = $cuenta_compra->centro_costo;
                        $asiento_contable_detalle_iva->subcentro_costo_id = $cuenta_compra->subcentro_costo;
                        $asiento_contable_detalle_iva->save();

                        $saldo_cuenta_contable_iva = SaldoCuentaContable::where('id', $asiento_contable_detalle_iva->cuenta_contable_id)->first();
                        if ($saldo_cuenta_contable_iva) {
                            $saldo_cuenta_contable_iva->saldo = $saldo_cuenta_contable_iva->saldo + $asiento_contable_detalle_iva->debe;
                            $saldo_cuenta_contable_iva->save();
                        }

                        $saldo_cuenta_contable_detalle_iva = new SaldoCuentaContableDetalle();
                        $saldo_cuenta_contable_detalle_iva->saldo_cuenta_contable_id = $saldo_cuenta_contable_iva->id;
                        $saldo_cuenta_contable_detalle_iva->mes = Carbon::now()->format('m');
                        $saldo_cuenta_contable_detalle_iva->anho = Carbon::now()->format('Y');
                        $saldo_cuenta_contable_detalle_iva->debe = $asiento_contable_detalle_iva->debe;
                        $saldo_cuenta_contable_detalle_iva->save();
                    }

                    $asiento_contable_detalle_proveedor = new AsientoContableDetalle();
                    $asiento_contable_detalle_proveedor->asiento_id = $asiento_contable->id;
                    $asiento_contable_detalle_proveedor->cuenta_contable_id = 69; //verificar cuenta contable de proveedores
                    $asiento_contable_detalle_proveedor->descripcion = 'COMPRA S/ FACTURA ' . $compra->numero_factura . ' - PROVEEDOR: ' . $compra->proveedor->razon_social;
                    $asiento_contable_detalle_proveedor->haber = $compra->monto_total;
                    $asiento_contable_detalle_proveedor->save();

                    $saldo_cuenta_contable_proveedor = SaldoCuentaContable::where('id', $asiento_contable_detalle_proveedor->cuenta_contable_id)->first();
                    if ($saldo_cuenta_contable_proveedor) {
                        $saldo_cuenta_contable_proveedor->saldo = $saldo_cuenta_contable_proveedor->saldo - $asiento_contable_detalle_proveedor->haber;
                        $saldo_cuenta_contable_proveedor->save();
                    }

                    $saldo_cuenta_contable_detalle_proveedor = new SaldoCuentaContableDetalle();
                    $saldo_cuenta_contable_detalle_proveedor->saldo_cuenta_contable_id = $saldo_cuenta_contable_proveedor->id;
                    $saldo_cuenta_contable_detalle_proveedor->mes = Carbon::now()->format('m');
                    $saldo_cuenta_contable_detalle_proveedor->anho = Carbon::now()->format('Y');
                    $saldo_cuenta_contable_detalle_proveedor->haber = $asiento_contable_detalle_proveedor->haber;
                    $saldo_cuenta_contable_detalle_proveedor->save();
                }
            }

            DB::commit();

            return redirect()->route('compras.index')->with('success-message', 'La factura de compra N° ' . $compra->numero_factura . ' del proveedor ' . $compra->proveedor->nombre . ' fue creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('compras.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_compras');

        DB::beginTransaction();

        try {
            $compra = Compra::findOrFail($id);

            if ($compra->estado != 'PE' && $compra->estado != 'CR') {
                return back()->with('error-message', 'La factura de compra N° ' . $compra->numero_factura . ' del proveedor ' . $compra->proveedor->razon_social . ' no se puede anular porque ya no se encuentra pendiente.');
            }

            if ($compra->orden_compra_id) {
                $orden_compra = OrdenCompra::where('id', $compra->orden_compra_id)->first();
                if (!$orden_compra) {
                    return back()->with('error-message', 'La factura de compra N° ' . $compra->numero_factura . ' del proveedor ' . $compra->proveedor->razon_social . ' no se puede anular porque no se encuentra la orden de compra asociada.');
                }
                $orden_compra->estado = 'AP';
                $orden_compra->save();
            }

            AsientoContableDetalle::where('asiento_id', $compra->asientoContable->id)->delete();
            AsientoContable::where('compra_id', $compra->id)->delete();

            $compra->estado = 'AN';
            $compra->save();

            DB::commit();

            return redirect()->route('compras.index')->with('error-message','La factura de compra N° ' . $compra->numero_factura . ' fue anulada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('compras.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_compras');

        DB::beginTransaction();

        try {
            $compra = Compra::findOrFail($id);

            if ($compra->estado != 'AN') {
                return back()->with('error-message', 'La factura de compra N° ' . $compra->numero_factura . ' del proveedor ' . $compra->proveedor->razon_social . ' no se puede eliminar porque no se encuentra anulada.');
            }

            if ($compra->url_factura) {
                $ubicacion_archivo = $compra->url_factura;
                $ubicacion_archivo = str_replace('storage', 'public', $ubicacion_archivo);
                Storage::delete($ubicacion_archivo);
            }

            $compra_detalles = CompraDetalle::where('compra_id', $compra->id)->delete();

            $compra->delete();

            DB::commit();

            return redirect()->route('compras.index')->with('success-message','La factura de compra N° ' . $compra->numero_factura . ' del proveedor ' . $compra->proveedor->razon_social . ' fue eliminada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('compras.index')->with('error-message', 'La factura de compra N° ' . $compra->numero_factura . ' del proveedor ' . $compra->proveedor->razon_social . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('compras.index')->with('error-message', $e->getMessage());
            }
        }
    }

    public function get_timbrados_proveedores($id)
    {
        $this->authorize('crear_compras');

        DB::beginTransaction();

        try {
            $timbrados = TimbradoProveedor::where('proveedor_id', $id)
                                ->where('estado', 'AC')
                                ->get();

            if ($timbrados->count() == 0) {
                return response()->json([
                    'error' => 'No existen timbrados activos para el proveedor seleccionado. Por favor, cargue el timbrado y vuelva a esta página para poder continuar.',
                ]);
            }

            foreach ($timbrados as $timbrado) {
                $timbrado->fecha_fin = Carbon::parse($timbrado->fecha_fin)->format('d/m/Y');
            }

            return response()->json([
                'timbrados' => $timbrados,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('compras.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_ordenes_compras($id)
    {
        $this->authorize('crear_compras');

        DB::beginTransaction();

        try {
            $ordenes_compras = OrdenCompra::with('detalles.articulo')
                                ->where('proveedor_id', $id)
                                ->where('estado', 'AP')
                                ->get();
            foreach ($ordenes_compras as $orden_compra) {
                $decimales = 0;
                if ($orden_compra->moneda_id == 2) {
                    $decimales = 2;
                }
                $orden_compra->monto_total = number_format($orden_compra->monto_total, $decimales, ',', '.');
            }

            return response()->json([
                'ordenes_compras' => $ordenes_compras,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('compras.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_orden_compra($id)
    {
        $this->authorize('crear_compras');

        DB::beginTransaction();

        try {
            $orden_compra = OrdenCompra::with('detalles')
                                ->findOrFail($id);

            foreach ($orden_compra->detalles as $detalle) {
                $detalle->articulo_nombre = $detalle->articulo->nombre;
            }

            return response()->json([
                'orden_compra' => $orden_compra,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('compras.index')->with('error-message', $e->getMessage());
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

    public function get_subcentros_costos($id)
    {
        $this->authorize('crear_compras');

        DB::beginTransaction();

        try {
            $subcentros_costos = SubcentroCostoContable::where('centro_costo_id', $id)->where('estado', 'AC')->get();

            return response()->json([
                'subcentros_costos' => $subcentros_costos,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('compras.index')->with('error-message', $e->getMessage());
        }
    }
}
