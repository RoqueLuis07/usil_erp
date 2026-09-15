<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\CategoriaProveedor;
use App\Models\Proveedor;
use App\Models\User;


class CategoriaProveedorController extends Controller
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
        $this->authorize('ver_categorias_proveedores');

        try {
            return view('categorias_proveedores.index');
        } catch (\Exception $e) {
            return redirect()->route('categorias_proveedores.index')->with('error-message', $e->getMessage());
        }
    }

    public function index_ajax()
    {
        $this->authorize('ver_categorias_proveedores');

        try {
            $categorias = CategoriaProveedor::get();

            return response()->json([
                'categorias' => $categorias,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('categorias_proveedores.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_categorias_proveedores');

        try {
            $categoria = CategoriaProveedor::findOrFail($id);
            return response()->json([
                'categoria' => $categoria,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('categorias_proveedores.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_categorias_proveedores');

        DB::beginTransaction();

            $request->validate([
                'nombre' => ['required'],
            ]);

            try {
                $categoria = new CategoriaProveedor();
                $categoria->nombre = removeAccents(Str::upper($request->nombre));
                $categoria->save();

                DB::commit();

                $categorias = CategoriaProveedor::get();
                return response()->json([
                    'message' => 'La categoría ' . $categoria->nombre . ' fue creada exitosamente.',
                    'categorias' => $categorias,
                    'selected' => $categoria,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('categorias_proveedores.index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_categorias_proveedores');

        try {
            $categoria = CategoriaProveedor::findOrFail($id);
            return response()->json([
                'categoria' => $categoria,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('categorias_proveedores.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_categorias_proveedores');

        DB::beginTransaction();

        $request->validate([
                'nombre' => ['required'],
            ]);

        try {
            $categoria = CategoriaProveedor::findOrFail($id);
            $categoria->nombre = removeAccents(Str::upper($request->nombre));
            $categoria->save();

            DB::commit();

            $categorias = CategoriaProveedor::get();
            return response()->json([
                'message' => 'La categoría ' . $categoria->nombre . ' fue actualizada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('categorias_proveedores.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_unactivate($id)
    {
        $this->authorize('inactivar_categorias_proveedores');

        try {
            $categoria = CategoriaProveedor::findOrFail($id);
            return response()->json([
                'categoria' => $categoria,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('categorias_proveedores.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_categorias_proveedores');

        DB::beginTransaction();

        try {
            $categoria = CategoriaProveedor::findOrFail($id);
            $categoria->estado = 'IN';
            $categoria->save();

            DB::commit();

            return response()->json([
                'message' => 'La categoría ' . $categoria->nombre . ' fue inactivada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('categorias_proveedores.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_activate($id)
    {
        $this->authorize('activar_categorias_proveedores');

        try {
            $categoria = CategoriaProveedor::findOrFail($id);
            return response()->json([
                'categoria' => $categoria,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('categorias_proveedores.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_categorias_proveedores');

        DB::beginTransaction();

        try {
            $categoria = CategoriaProveedor::findOrFail($id);
            $categoria->estado = 'AC';
            $categoria->save();

            DB::commit();

            return response()->json([
                'message' => 'La categoría ' . $categoria->nombre . ' fue activada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('categorias_proveedores.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_categorias_proveedores');

        try {
            $categoria = CategoriaProveedor::findOrFail($id);
            return response()->json([
                'categoria' => $categoria,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('categorias_proveedores.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_categorias_proveedores');

        DB::beginTransaction();

        try {
            $categoria = CategoriaProveedor::findOrFail($id);
            $categoria->delete();

            DB::commit();

            return response()->json([
                'message' => 'La categoría ' . $categoria->nombre . ' fue eliminada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('categorias_proveedores.index')->with('error-message', 'La categoría ' . $categoria->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('categorias_proveedores.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
