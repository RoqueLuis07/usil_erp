<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use App\Models\TutoriaPrecio;
use App\Models\Modalidad;
use App\Models\Carrera;
use App\Models\Articulo;

class TutoriaPrecioController extends Controller
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
        $this->authorize('ver_precios_tutorias');

        $modalidades = Modalidad::where('estado', 'AC')->get();
        $carreras = Carrera::where('estado', 'AC')->get();
        $articulos = Articulo::where('estado', 'AC')->get();

        return view('tutorias.precios.index')->with(compact('modalidades', 'carreras', 'articulos'));
    }

    public function index_ajax()
    {
        $this->authorize('ver_precios_tutorias');

        try {
            $precios = TutoriaPrecio::with('modalidad', 'carrera', 'articulo.detalle')->orderBy('id', 'asc')->get();

            foreach ($precios as $precio) {
                $precio->articulo->detalle->precio_contado = number_format($precio->articulo->detalle->precio_contado, 0, ',', '.');
            }

            return response()->json([
                'precios' => $precios,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tutorias_precios.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_precios_tutorias');

        try {
            $precio = TutoriaPrecio::with('modalidad', 'carrera', 'articulo.detalle')->findOrFail($id);
            $precio->articulo->detalle->precio_contado = number_format($precio->articulo->detalle->precio_contado, 0, ',', '.');
            return response()->json([
                'precio' => $precio,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tutorias_precios.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('editar_precios_tutorias');

        DB::beginTransaction();

            $request->validate([
                'modalidad' => ['required', 'numeric', Rule::unique('tutorias_precios', 'modalidad_id')
                    ->where(fn ($query) => $query->where('modalidad_id', $request->modalidad)
                    ->where('carrera_id', $request->carrera))],
                //El de arriba verifica que el conjunto de modalidad y carrera no existan,
                'carrera' => ['required', 'numeric'],
                'articulo' => ['required', 'numeric']
            ]);

            try {
                $precio = new TutoriaPrecio();
                $precio->modalidad_id = $request->modalidad;
                $precio->carrera_id = $request->carrera;
                $precio->articulo_id = $request->articulo;
                $precio->cargado_por_id = Auth::id();
                $precio->save();

                DB::commit();

                $precios = TutoriaPrecio::with('modalidad')->get();
                return response()->json([
                    'message' => 'El precio de la tutoría fue creado exitosamente.',
                    'precios' => $precios,
                    'selected' => $precio,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('tutorias_precios.index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_precios_tutorias');

        try {
            $precio = TutoriaPrecio::with('modalidad', 'carrera', 'articulo.detalle')->findOrFail($id);

            $modalidades = Modalidad::where('estado', 'AC')->get();
            $carreras = Carrera::where('estado', 'AC')->get();
            $articulos = Articulo::where('estado', 'AC')->get();
            return response()->json([
                'precio' => $precio,
                'modalidades' => $modalidades,
                'carreras' => $carreras,
                'articulos' => $articulos,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tutorias_precios.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_precios_tutorias');

        DB::beginTransaction();

        $request->validate([
            'modalidad' => ['required', 'numeric', Rule::unique('tutorias_precios', 'modalidad_id')
                    ->where(fn ($query) => $query->where('modalidad_id', $request->modalidad)
                    ->where('carrera_id', $request->carrera))->ignore($id)],
                //El de arriba verifica que el conjunto de modalidad y carrera no existan,
            'carrera' => ['required', 'numeric'],
            'articulo' => ['required', 'numeric']
        ]);

        try {
            $precio = TutoriaPrecio::findOrFail($id);
            $precio->modalidad_id = $request->modalidad;
            $precio->carrera_id = $request->carrera;
            $precio->articulo_id = $request->articulo;
            $precio->actualizado_por_id = Auth::id();
            $precio->save();

            DB::commit();

            $precios = TutoriaPrecio::get();
            return response()->json([
                'message' => 'El precio de la tutoría fue actualizado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tutorias_precios.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_precios_tutorias');

        try {
            $precio = TutoriaPrecio::with('modalidad', 'carrera')->findOrFail($id);
            return response()->json([
                'precio' => $precio,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tutorias_precios.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_precios_tutorias');

        DB::beginTransaction();

        try {
            $precio = TutoriaPrecio::findOrFail($id);
            $precio->delete();

            DB::commit();

            return response()->json([
                'message' => 'El precio de la tutoría fue eliminado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('tutorias_precios.index')->with('error-message', 'El precio de la tutoría no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('tutorias_precios.index')->with('error-message', $e->getMessage());
            }
        }
    }

    public function get_precio($id)
    {
        $this->authorize('editar_precios_tutorias');

        try {
            $articulo = Articulo::findOrFail($id);
            $precio = $articulo->detalle->precio_contado;
            return response()->json([
                'precio' => $precio,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tutorias_precios.index')->with('error-message', $e->getMessage());
        }
    }
}
