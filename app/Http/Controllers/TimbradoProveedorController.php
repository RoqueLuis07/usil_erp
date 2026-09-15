<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\TimbradoProveedor;
use App\Models\Proveedor;
use App\Models\User;


class TimbradoProveedorController extends Controller
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
        $this->authorize('ver_timbrados_proveedores');

        try {
            $proveedores = Proveedor::where('estado', 'AC')->orderBy('razon_social')->get();

            return view('timbrados_proveedores.index')->with(compact('proveedores'));
        } catch (\Exception $e) {
            return redirect()->route('timbrados_proveedores.index')->with('error-message', $e->getMessage());
        }
    }

    public function index_ajax()
    {
        $this->authorize('ver_timbrados_proveedores');

        try {
            $timbrados = TimbradoProveedor::with('proveedor')->orderBy('valido_hasta', 'desc')->get();
            foreach ($timbrados as $timbrado) {
                $timbrado->valido_desde = Carbon::parse($timbrado->valido_desde)->format('d/m/Y');
                $timbrado->valido_hasta = Carbon::parse($timbrado->valido_hasta)->format('d/m/Y');
            }

            return response()->json([
                'timbrados' => $timbrados,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('timbrados_proveedores.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_timbrados_proveedores');

        try {
            $timbrado = TimbradoProveedor::with('proveedor')->findOrFail($id);
            $timbrado->valido_desde = Carbon::parse($timbrado->valido_desde)->format('d/m/Y');
            $timbrado->valido_hasta = Carbon::parse($timbrado->valido_hasta)->format('d/m/Y');
            $cargado = User::findOrFail($timbrado->cargado_por_id);
            $created_at = Carbon::parse($timbrado->created_at)->format('d/m/Y H:i:s');
            if ($timbrado->actualizado_por_id) {
                $actualizado = User::findOrFail($timbrado->actualizado_por_id);
                $updated_at = Carbon::parse($timbrado->updated_at)->format('d/m/Y H:i:s');
            } else {
                $actualizado = 0;
                $updated_at = 0;
            }
            return response()->json([
                'timbrado' => $timbrado,
                'cargado' => $cargado,
                'created_at' => $created_at,
                'actualizado' => $actualizado,
                'updated_at' => $updated_at,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('timbrados_proveedores.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_timbrados_proveedores');

        DB::beginTransaction();

            $request->validate([
                'proveedor' => ['required', 'numeric'],
                'numero' => ['required', 'numeric', Rule::unique('timbrados_proveedores', 'numero')
                    ->where(fn ($query) => $query->where('numero', $request->numero)
                    ->where('proveedor_id', $request->proveedor)
                    ->where('estado', 'AC'))],
                'valido_desde' => ['required', 'date'],
                'valido_hasta' => ['required', 'date', 'after:valido_desde'],
            ]);

            try {
                $timbrado = new TimbradoProveedor();
                $timbrado->proveedor_id = $request->proveedor;
                $timbrado->numero = $request->numero;
                $timbrado->valido_desde = $request->valido_desde;
                $timbrado->valido_hasta = $request->valido_hasta;
                $timbrado->cargado_por_id = Auth::id();
                $timbrado->save();

                DB::commit();

                $timbrados = TimbradoProveedor::get();
                return response()->json([
                    'message' => 'El timbrado ' . $timbrado->numero . ' del proveedor ' . $timbrado->proveedor->razon_social . ' fue creado exitosamente.',
                    'timbrados' => $timbrados,
                    'selected' => $timbrado,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('timbrados_proveedores.index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_timbrados_proveedores');

        try {
            $timbrado = TimbradoProveedor::with('proveedor')->findOrFail($id);
            $proveedores = Proveedor::where('estado', 'AC')->orderBy('razon_social')->get();
            return response()->json([
                'timbrado' => $timbrado,
                'proveedores' => $proveedores,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('timbrados_proveedores.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_timbrados_proveedores');

        DB::beginTransaction();

        $request->validate([
                'proveedor' => ['required', 'numeric'],
                'numero' => ['required', 'numeric', Rule::unique('timbrados_proveedores', 'numero')
                    ->where(fn ($query) => $query->where('numero', $request->numero)
                    ->where('proveedor_id', $request->proveedor)
                    ->where('estado', 'AC'))->ignore($id)],
                'valido_desde' => ['required', 'date'],
                'valido_hasta' => ['required', 'date', 'after:valido_desde'],
            ]);

        try {
            $timbrado = TimbradoProveedor::findOrFail($id);
            $timbrado->proveedor_id = $request->proveedor;
            $timbrado->numero = $request->numero;
            $timbrado->valido_desde = $request->valido_desde;
            $timbrado->valido_hasta = $request->valido_hasta;
            $timbrado->actualizado_por_id = Auth::id();
            $timbrado->save();

            DB::commit();

            $timbrados = TimbradoProveedor::get();
            return response()->json([
                'message' => 'El timbrado ' . $timbrado->numero . ' del proveedor ' . $timbrado->proveedor->razon_social . ' fue actualizado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('timbrados_proveedores.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_unactivate($id)
    {
        $this->authorize('inactivar_timbrados_proveedores');

        try {
            $timbrado = TimbradoProveedor::with('proveedor')->findOrFail($id);
            return response()->json([
                'timbrado' => $timbrado,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('timbrados_proveedores.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_timbrados_proveedores');

        DB::beginTransaction();

        try {
            $timbrado = TimbradoProveedor::findOrFail($id);
            $timbrado->actualizado_por_id = Auth::id();
            $timbrado->estado = 'IN';
            $timbrado->save();

            DB::commit();

            return response()->json([
                'message' => 'El timbrado ' . $timbrado->numero . ' del proveedor ' . $timbrado->proveedor->razon_social . ' fue inactivado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('timbrados_proveedores.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_activate($id)
    {
        $this->authorize('activar_timbrados_proveedores');

        try {
            $timbrado = TimbradoProveedor::with('proveedor')->findOrFail($id);
            return response()->json([
                'timbrado' => $timbrado,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('timbrados_proveedores.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_timbrados_proveedores');

        DB::beginTransaction();

        try {
            $timbrado = TimbradoProveedor::findOrFail($id);
            $timbrado->actualizado_por_id = Auth::id();
            $timbrado->estado = 'AC';
            $timbrado->save();

            DB::commit();

            return response()->json([
                'message' => 'El timbrado ' . $timbrado->numero . ' del proveedor ' . $timbrado->proveedor->razon_social . ' fue activado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('timbrados_proveedores.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_timbrados_proveedores');

        try {
            $timbrado = TimbradoProveedor::with('proveedor')->findOrFail($id);
            return response()->json([
                'timbrado' => $timbrado,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('timbrados_proveedores.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_timbrados_proveedores');

        DB::beginTransaction();

        try {
            $timbrado = TimbradoProveedor::findOrFail($id);
            $timbrado->delete();

            DB::commit();

            return response()->json([
                'message' => 'El timbrado ' . $timbrado->numero . ' del proveedor ' . $timbrado->proveedor->razon_social . ' fue eliminado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('timbrados_proveedores.index')->with('error-message', 'El timbrado ' . $timbrado->numero . ' del proveedor ' . $timbrado->proveedor->razon_social . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('timbrados_proveedores.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
