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


use App\Models\OrdenCompra;
use App\Models\OrdenCompraDetalle;
use App\Models\Proveedor;
use App\Models\Moneda;
use App\Models\Articulo;
use App\Models\Empresa;


class OrdenCompraController extends Controller
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
        $this->authorize('ver_compras_ordenes');

        try {
            $ordenes_compras = OrdenCompra::orderBy('id', 'desc')->get();
            return view('ordenes_compras/index')->with(compact('ordenes_compras'));
        } catch (\Exception $e) {
            return redirect()->route('ordenes_compras.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_compras_ordenes');

        try {
            $orden_compra = OrdenCompra::with('detalles')->findOrFail($id);

            $monto_total = 0;
            foreach ($orden_compra->detalles as $detalle) {
                $detalle->subtotal = $detalle->cantidad * $detalle->precio_costo;
                $monto_total += $detalle->subtotal;
            }
            $orden_compra->monto_total = $monto_total;

            return view('ordenes_compras/show')->with(compact('orden_compra'));
        } catch (\Exception $e) {
            return redirect()->route('ordenes_compras.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_compras_ordenes');

        try {
            $ultima_orden = OrdenCompra::orderBy('id', 'desc')->first();
            if ($ultima_orden) {
                $numero_orden = $ultima_orden->id + 1;
            } else {
                $numero_orden = 1;
            }

            $proveedores = Proveedor::where('estado', 'AC')->get();
            $monedas = Moneda::where('estado', 'AC')->get();
            $articulos = Articulo::where('compra_venta', 'COMPRA')->where('estado', 'AC')->get();
            return view('ordenes_compras/create')->with(compact('numero_orden', 'proveedores', 'monedas', 'articulos'));
        } catch (\Exception $e) {
            return redirect()->route('ordenes_compras.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_compras_ordenes');

        $request->validate([
            'proveedor' => ['required', 'numeric'],
            'condicion_compra' => ['required'],
            'credito_a' => ['nullable', 'numeric', 'required_if:condicion_compra,CR', 'min:7', 'max:120'],
            'moneda' => ['required', 'numeric'],
            'adjunto' => ['nullable', 'file', 'extensions:jpg,jpeg,png,pdf'],

            'detalles' => ['required', 'array'],
            'detalles.*.articulo' => ['required', 'numeric'],
            'detalles.*.descripcion' => 'nullable',
            'detalles.*.cantidad' => ['required', 'numeric', 'min:1'],
            'detalles.*.precio_costo' => ['required', 'numeric', 'min:1']
        ]);

        DB::beginTransaction();

        try {
            $ultima_orden = OrdenCompra::orderBy('id', 'desc')->first();
            if ($ultima_orden) {
                $numero_orden = $ultima_orden->id + 1;
            } else {
                $numero_orden = 1;
            }

            $orden_compra = new OrdenCompra();
            $orden_compra->proveedor_id = $request->proveedor;
            $orden_compra->condicion_compra = $request->condicion_compra;
            if ($request->condicion_compra == 'CR') {
                $orden_compra->credito_a = $request->credito_a;
            } else {
                $orden_compra->credito_a = null;
            }
            $orden_compra->moneda_id = $request->moneda;

            $monto_total = 0;
            foreach ($request->detalles as $detalle) {
                $monto_total += $detalle['cantidad'] * $detalle['precio_costo'];
            }
            $orden_compra->monto_total = $monto_total;

            if ($request->adjunto) {
                $archivo = $request->adjunto;
                $extension = $archivo->getClientOriginalExtension();
                $directorio = 'storage/compras/ordenes_compras';
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
                    $directorio_storage = 'public/compras/ordenes_compras';
                    // Crear el directorio si no existe
                    if (!Storage::exists($directorio_storage)) {
                        Storage::makeDirectory($directorio_storage);
                    }

                    $nombre = $numero_orden . '_' . Carbon::now()->format('Ymd_His') . '_' . Str::lower($request->condicion_compra);
                    $nombre_archivo = $nombre . '.' . $extension;
                    Storage::putFileAs($directorio_storage . '/', $archivo, $nombre_archivo);
                }
                $orden_compra->url_presupuesto = $directorio . '/' . $nombre_archivo;
            }
            $orden_compra->observaciones = $request->observaciones;
            $orden_compra->cargado_por_id = Auth::id();
            $orden_compra->save();

            foreach ($request->detalles as $detalle) { //utilizar el array obtenido arriba
                $orden_detalle = new OrdenCompraDetalle();
                $orden_detalle->orden_compra_id = $orden_compra->id;
                $orden_detalle->articulo_id = $detalle['articulo'];
                $orden_detalle->descripcion = removeAccents(Str::upper($detalle['descripcion']));
                $orden_detalle->cantidad = $detalle['cantidad'];
                $orden_detalle->precio_costo = $detalle['precio_costo'];
                $orden_detalle->save();
            }

            DB::commit();

            return redirect()->route('ordenes_compras.index')->with('success-message', 'La OC N° ' . $orden_compra->id . ' fue creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('ordenes_compras.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_compras_ordenes');

        try {
            $orden_compra = OrdenCompra::with('detalles')->findOrFail($id);

            if ($orden_compra->estado != 'PE') {
                return back()->with('error-message', 'La OC N° ' . $orden_compra->id . ' no se puede editar porque ya no se encuentra pendiente.');
            }

            $monto_total = 0;
            foreach ($orden_compra->detalles as $detalle) {
                $detalle->subtotal = $detalle->cantidad * $detalle->precio_costo;
                $detalle->precio_costo = number_format($detalle->precio_costo, 0, '.', '');
                $monto_total += $detalle->subtotal;
            }
            $orden_compra->monto_total = $monto_total;

            $proveedores = Proveedor::where('estado', 'AC')->get();
            $monedas = Moneda::where('estado', 'AC')->get();
            $articulos = Articulo::where('compra_venta', 'COMPRA')->where('estado', 'AC')->get();
            return view('ordenes_compras.edit')->with(compact('orden_compra', 'proveedores', 'monedas', 'articulos'));
        } catch (\Exception $e) {
            return redirect()->route('ordenes_compras.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_compras_ordenes');

        $request->validate([
            'proveedor' => ['required', 'numeric'],
            'condicion_compra' => ['required'],
            'credito_a' => ['nullable', 'numeric', 'required_if:condicion_compra,CR', 'min:7', 'max:120'],
            'moneda' => ['required', 'numeric'],
            'adjunto' => ['nullable', 'file', 'extensions:jpg,jpeg,png,pdf'],

            'detalles' => ['required', 'array'],
            'detalles.*.articulo' => ['required', 'numeric'],
            'detalles.*.descripcion' => 'nullable',
            'detalles.*.cantidad' => ['required', 'numeric', 'min:1'],
            'detalles.*.precio_costo' => ['required', 'numeric', 'min:1']
        ]);

        DB::beginTransaction();

        try {
            $orden_compra = OrdenCompra::findOrFail($id);

            if ($orden_compra->estado != 'PE') {
                return redirect()->route('ordenes_compras.index')->with('error-message', 'La OC N° ' . $orden_compra->id . ' no se puede editar porque ya no se encuentra pendiente.');
            }

            $orden_compra->proveedor_id = $request->proveedor;
            $orden_compra->condicion_compra = $request->condicion_compra;
            if ($request->condicion_compra == 'CR') {
                $orden_compra->credito_a = $request->credito_a;
            } else {
                $orden_compra->credito_a = null;
            }
            $orden_compra->moneda_id = $request->moneda;

            $monto_total = 0;
            foreach ($request->detalles as $detalle) {
                $monto_total += $detalle['cantidad'] * $detalle['precio_costo'];
            }
            $orden_compra->monto_total = $monto_total;

            if ($request->adjunto) {
                if ($orden_compra->url_presupuesto) {
                    $ubicacion_archivo = $orden_compra->url_presupuesto;
                    $ubicacion_archivo = str_replace('storage', 'public', $ubicacion_archivo);
                    Storage::delete($ubicacion_archivo);
                }

                $archivo = $request->adjunto;
                $extension = $archivo->getClientOriginalExtension();
                $directorio = 'storage/compras/ordenes_compras';
                // Crear el directorio si no existe
                if (!File::exists($directorio)) {
                    File::makeDirectory($directorio, 0755, true);
                }

                if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png') {
                    $manager = new ImageManager(Driver::class);
                    $image = $manager->read($archivo);
                    $image = $image->encode(new AutoEncoder(quality: 50));
                    $nombre = $orden_compra->id . '_' . Carbon::parse($orden_compra->created_at)->format('Ymd_His') . '_' . Str::lower($request->condicion_compra);
                    $nombre_archivo = $nombre . '.' . $extension;
                    $image->save($directorio . '/' . $nombre_archivo);
                } else {
                    $directorio_storage = 'public/compras/ordenes_compras';
                    // Crear el directorio si no existe
                    if (!Storage::exists($directorio_storage)) {
                        Storage::makeDirectory($directorio_storage);
                    }

                    $nombre = $orden_compra->id . '_' . Carbon::parse($orden_compra->created_at)->format('Ymd_His') . '_' . Str::lower($request->condicion_compra);
                    $nombre_archivo = $nombre . '.' . $extension;
                    Storage::putFileAs($directorio_storage . '/', $archivo, $nombre_archivo);
                }
                $orden_compra->url_presupuesto = $directorio . '/' . $nombre_archivo;
            }
            $orden_compra->observaciones = $request->observaciones;
            $orden_compra->actualizado_por_id = Auth::id();
            $orden_compra->save();

            OrdenCompraDetalle::where('orden_compra_id', $orden_compra->id)->delete(); // Eliminar los detalles anteriores

            foreach ($request->detalles as $detalle) { //utilizar el array obtenido arriba
                $orden_detalle = new OrdenCompraDetalle();
                $orden_detalle->orden_compra_id = $orden_compra->id;
                $orden_detalle->articulo_id = $detalle['articulo'];
                $orden_detalle->descripcion = removeAccents(Str::upper($detalle['descripcion']));
                $orden_detalle->cantidad = $detalle['cantidad'];
                $orden_detalle->precio_costo = $detalle['precio_costo'];
                $orden_detalle->save();
            }

            DB::commit();

            return redirect()->route('ordenes_compras.index')->with('success-message', 'La OC N° ' . $orden_compra->id . ' fue actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('ordenes_compras.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_compras_ordenes');

        DB::beginTransaction();

        try {
            $orden_compra = OrdenCompra::findOrFail($id);

            if ($orden_compra->estado != 'PE') {
                return back()->with('error-message', 'La OC N° ' . $orden_compra->id . ' no se puede anular porque ya no se encuentra pendiente.');
            }

            $orden_compra->actualizado_por_id = Auth::id();
            $orden_compra->estado = 'AN';
            $orden_compra->save();

            DB::commit();

            return redirect()->route('ordenes_compras.index')->with('error-message','La OC N° ' . $orden_compra->id . ' fue anulada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('ordenes_compras.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_compras_ordenes');

        DB::beginTransaction();

        try {
            $orden_compra = OrdenCompra::findOrFail($id);
            $orden_compra->actualizado_por_id = Auth::id();
            $orden_compra->estado = 'PE';
            $orden_compra->save();

            DB::commit();

            return redirect()->route('ordenes_compras.index')->with('success-message','La OC N° ' . $orden_compra->id . ' fue desanulada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('ordenes_compras.index')->with('error-message', $e->getMessage());
        }
    }

    public function approve($id)
    {
        $this->authorize('aprobar_compras_ordenes');

        DB::beginTransaction();

        try {
            $orden_compra = OrdenCompra::findOrFail($id);
            $orden_compra->actualizado_por_id = Auth::id();
            $orden_compra->estado = 'AP';
            $orden_compra->save();

            DB::commit();

            return redirect()->route('ordenes_compras.index')->with('success-message','La OC N° ' . $orden_compra->id . ' fue aprobada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('ordenes_compras.index')->with('error-message', $e->getMessage());
        }
    }

    public function unapprove($id)
    {
        $this->authorize('desaprobar_compras_ordenes');

        DB::beginTransaction();

        try {
            $orden_compra = OrdenCompra::findOrFail($id);

            if ($orden_compra->estado != 'AP') {
                return back()->with('error-message', 'La OC N° ' . $orden_compra->id . ' no se puede desaprobar, ésta no se encuentra previamente aprobada.');
            }

            $orden_compra->actualizado_por_id = Auth::id();
            $orden_compra->estado = 'PE';
            $orden_compra->save();

            DB::commit();

            return redirect()->route('ordenes_compras.index')->with('error-message','La OC N° ' . $orden_compra->id . ' fue desaprobada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('ordenes_compras.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_compras_ordenes');

        DB::beginTransaction();

        try {
            $orden_compra = OrdenCompra::findOrFail($id);

            if ($orden_compra->estado != 'AN') {
                return back()->with('error-message', 'La OC N° ' . $orden_compra->id . ' no se puede eliminar porque no se encuentra anulada.');
            }

            if ($orden_compra->url_presupuesto) {
                $ubicacion_archivo = $orden_compra->url_presupuesto;
                $ubicacion_archivo = str_replace('storage', 'public', $ubicacion_archivo);
                Storage::delete($ubicacion_archivo);
            }

            $orden_compra_detalles = OrdenCompraDetalle::where('orden_compra_id', $orden_compra->id)->delete();

            $orden_compra->delete();

            DB::commit();

            return redirect()->route('ordenes_compras.index')->with('success-message','La OC N° ' . $orden_compra->id . ' fue eliminada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('ordenes_compras.index')->with('error-message', 'La OC N° ' . $orden_compra->id . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('ordenes_compras.index')->with('error-message', $e->getMessage());
            }
        }
    }

    public function imprimir($id)
    {
        $this->authorize('reimprimir_compras_ordenes');

        try {
            $empresa = Empresa::first();
            $fecha_hoy = Carbon::now();
            $orden_compra = OrdenCompra::with('detalles')->findOrFail($id);

            $monto_total = 0;
            foreach ($orden_compra->detalles as $detalle) {
                $detalle->subtotal = $detalle->cantidad * $detalle->precio_costo;
                $detalle->precio_costo = number_format($detalle->precio_costo, 0, '.', '');
                $monto_total += $detalle->subtotal;
            }
            $orden_compra->monto_total = $monto_total;

            $pdf = Pdf::loadView('ordenes_compras/pdf', compact('empresa', 'fecha_hoy', 'orden_compra'));
            $pdf->setPaper('A4');

            return $pdf->stream('oc_' . $orden_compra->id . '.pdf');

        } catch (\Exception $e) {
            return redirect()->route('ordenes_compras.index')->with('error-message', $e->getMessage());
        }
    }
}
