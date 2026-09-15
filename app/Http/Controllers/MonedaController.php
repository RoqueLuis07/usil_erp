<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

use App\Models\Moneda;


class MonedaController extends Controller
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
        $this->authorize('ver_monedas');

        return view('monedas.index');
    }

    public function index_ajax()
    {
        $this->authorize('ver_monedas');

        try {
            $monedas = Moneda::orderBy('nombre', 'asc')->get();
            return response()->json([
                'monedas' => $monedas,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('monedas.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_monedas');

        try {
            $moneda = Moneda::findOrFail($id);
            return response()->json([
                'moneda' => $moneda,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('monedas.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_monedas');

        DB::beginTransaction();

            $request->validate([
                'nombre' => ['required', Rule::unique('monedas')],
                'codigo' => ['required', Rule::unique('monedas')],
            ]);

            try {
                $moneda = new Moneda();
                $moneda->nombre = removeAccents(Str::upper($request->nombre));
                $moneda->codigo = removeAccents(Str::upper($request->codigo));
                $moneda->save();

                DB::commit();

                $monedas = Moneda::get();
                return response()->json([
                    'message' => 'La moneda ' . $moneda->nombre . ' fue creada exitosamente.',
                    'monedas' => $monedas,
                    'selected' => $moneda,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('monedas.index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_monedas');

        try {
            $moneda = Moneda::findOrFail($id);
            return response()->json([
                'moneda' => $moneda,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('monedas.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_monedas');

        DB::beginTransaction();

        $request->validate([
            'nombre' => ['required', Rule::unique('monedas')->ignore($id)],
            'codigo' => ['required', Rule::unique('monedas')->ignore($id)],
        ]);

        try {
            $moneda = Moneda::findOrFail($id);
            $moneda->nombre = removeAccents(Str::upper($request->nombre));
            $moneda->codigo = removeAccents(Str::upper($request->codigo));
            $moneda->save();

            DB::commit();

            $monedas = Moneda::get();
            return response()->json([
                'message' => 'La moneda ' . $moneda->nombre . ' fue actualizada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('monedas.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_unactivate($id)
    {
        $this->authorize('inactivar_monedas');

        try {
            $moneda = Moneda::findOrFail($id);
            return response()->json([
                'moneda' => $moneda,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('monedas.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_monedas');

        DB::beginTransaction();

        try {
            $moneda = Moneda::findOrFail($id);
            $moneda->estado = 'IN';
            $moneda->save();

            DB::commit();

            return response()->json([
                'message' => 'La moneda ' . $moneda->nombre . ' fue inactivada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('monedas.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_activate($id)
    {
        $this->authorize('activar_monedas');

        try {
            $moneda = Moneda::findOrFail($id);
            return response()->json([
                'moneda' => $moneda,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('monedas.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_monedas');

        DB::beginTransaction();

        try {
            $moneda = Moneda::findOrFail($id);
            $moneda->estado = 'AC';
            $moneda->save();

            DB::commit();

            return response()->json([
                'message' => 'La moneda ' . $moneda->nombre . ' fue activada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('monedas.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_monedas');

        try {
            $moneda = Moneda::findOrFail($id);
            return response()->json([
                'moneda' => $moneda,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('monedas.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_monedas');

        DB::beginTransaction();

        try {
            $moneda = Moneda::findOrFail($id);
            $moneda->delete();

            DB::commit();

            return response()->json([
                'message' => 'La moneda ' . $moneda->nombre . ' fue eliminada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('monedas.index')->with('error-message', 'La moneda ' . $moneda->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('monedas.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
