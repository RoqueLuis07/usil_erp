<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use App\Models\TipoMovimiento;


class TipoMovimientoController extends Controller
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
        $this->authorize('ver_tipos_movimientos');

        return view('tipos_movimientos.index');
    }

    public function index_ajax()
    {
        $this->authorize('ver_tipos_movimientos');

        try {
            $tipos_movimientos = TipoMovimiento::orderBy('id', 'asc')->get();
            return response()->json([
                'tipos_movimientos' => $tipos_movimientos,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_movimientos.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_tipos_movimientos');

        try {
            $tipo_movimiento = TipoMovimiento::findOrFail($id);
            return response()->json([
                'tipo_movimiento' => $tipo_movimiento,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_movimientos.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_tipos_movimientos');

        DB::beginTransaction();

            $request->validate([
                'nombre' => 'required',
                'tipo' => ['required', Rule::unique('tipos_movimientos')->where(fn ($query) => $query->where('tipo', $request->tipo)->where('nombre', removeAccents(Str::upper($request->nombre))))],
                //El de arriba verifica que el conjunto de nombre y tipo no existan
            ]);

            try {
                $tipo_movimiento = new TipoMovimiento();
                $tipo_movimiento->nombre = removeAccents(Str::upper($request->nombre));
                $tipo_movimiento->tipo = $request->tipo;
                $tipo_movimiento->save();

                DB::commit();

                $tipos_movimientos = TipoMovimiento::get();
                return response()->json([
                    'message' => 'El tipo de movimiento ' . $tipo_movimiento->nombre . ' fue creado exitosamente.',
                    'tipos_movimientos' => $tipos_movimientos,
                    'selected' => $tipo_movimiento,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('tipos_movimientos.index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_tipos_movimientos');

        try {
            $tipo_movimiento = TipoMovimiento::findOrFail($id);
            return response()->json([
                'tipo_movimiento' => $tipo_movimiento,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_movimientos.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_tipos_movimientos');

        DB::beginTransaction();

        $request->validate([
            'nombre' => 'required',
            'tipo' => ['required', Rule::unique('tipos_movimientos')->where(fn ($query) => $query->where('tipo', $request->tipo)->where('nombre', removeAccents(Str::upper($request->nombre))))->ignore($id)],
        ]);

        try {
            $tipo_movimiento = TipoMovimiento::findOrFail($id);
            $tipo_movimiento->nombre = removeAccents(Str::upper($request->nombre));
            $tipo_movimiento->tipo = $request->tipo;
            $tipo_movimiento->save();

            DB::commit();

            $tipos_movimientos = TipoMovimiento::get();
            return response()->json([
                'message' => 'El tipo de movimiento ' . $tipo_movimiento->nombre . ' fue actualizado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tipos_movimientos.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_unactivate($id)
    {
        $this->authorize('inactivar_tipos_movimientos');

        try {
            $tipo_movimiento = TipoMovimiento::findOrFail($id);
            return response()->json([
                'tipo_movimiento' => $tipo_movimiento,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_movimientos.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_tipos_movimientos');

        DB::beginTransaction();

        try {
            $tipo_movimiento = TipoMovimiento::findOrFail($id);
            $tipo_movimiento->estado = 'IN';
            $tipo_movimiento->save();

            DB::commit();

            return response()->json([
                'message' => 'El tipo de movimiento ' . $tipo_movimiento->nombre . ' fue inactivado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tipos_movimientos.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_activate($id)
    {
        $this->authorize('activar_tipos_movimientos');

        try {
            $tipo_movimiento = TipoMovimiento::findOrFail($id);
            return response()->json([
                'tipo_movimiento' => $tipo_movimiento,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_movimientos.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_tipos_movimientos');

        DB::beginTransaction();

        try {
            $tipo_movimiento = TipoMovimiento::findOrFail($id);
            $tipo_movimiento->estado = 'AC';
            $tipo_movimiento->save();

            DB::commit();

            return response()->json([
                'message' => 'El tipo de movimiento ' . $tipo_movimiento->nombre . ' fue activado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tipos_movimientos.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_tipos_movimientos');

        try {
            $tipo_movimiento = TipoMovimiento::findOrFail($id);
            return response()->json([
                'tipo_movimiento' => $tipo_movimiento,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_movimientos.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_tipos_movimientos');

        DB::beginTransaction();

        try {
            $tipo_movimiento = TipoMovimiento::findOrFail($id);
            $tipo_movimiento->delete();

            DB::commit();

            return response()->json([
                'message' => 'El tipo de movimiento ' . $tipo_movimiento->nombre . ' fue eliminado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('tipos_movimientos.index')->with('error-message', 'El tipo de movimiento ' . $tipo_movimiento->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('tipos_movimientos.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
