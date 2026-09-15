<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use App\Models\LineaTesisUbs;


class LineaTesisUbsController extends Controller
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
        $this->authorize('ver_lineas_tesis_ubs');

        return view('ubs.tesis.lineas.index');
    }

    public function index_ajax()
    {
        $this->authorize('ver_lineas_tesis_ubs');

        try {
            $lineas_tesis = LineaTesisUbs::orderBy('id', 'asc')->get();
            return response()->json([
                'lineas_tesis' => $lineas_tesis,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tesis_ubs.lineas_index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_lineas_tesis_ubs');

        try {
            $linea_tesis = LineaTesisUbs::findOrFail($id);
            return response()->json([
                'linea_tesis' => $linea_tesis,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tesis_ubs.lineas_index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_lineas_tesis_ubs');

        DB::beginTransaction();

            $request->validate([
                'nombre' => ['required', Rule::unique('lineas_tesis_ubs')]
            ]);

            try {
                $linea_tesis = new LineaTesisUbs();
                $linea_tesis->nombre = removeAccents(Str::upper($request->nombre));
                $linea_tesis->cargado_por_id = Auth::id();
                $linea_tesis->save();

                DB::commit();

                $lineas_tesis = LineaTesisUbs::get();
                return response()->json([
                    'message' => 'La línea de trabajo final de grado ' . $linea_tesis->nombre . ' fue creado exitosamente.',
                    'lineas_tesis' => $lineas_tesis,
                    'selected' => $linea_tesis,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('tesis_ubs.lineas_index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_lineas_tesis_ubs');

        try {
            $linea_tesis = LineaTesisUbs::findOrFail($id);
            return response()->json([
                'linea_tesis' => $linea_tesis,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tesis_ubs.lineas_index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_lineas_tesis_ubs');

        DB::beginTransaction();

        $request->validate([
            'nombre' => ['required', Rule::unique('lineas_tesis_ubs')->ignore($id)]
        ]);

        try {
            $linea_tesis = LineaTesisUbs::findOrFail($id);
            $linea_tesis->nombre = removeAccents(Str::upper($request->nombre));
            $linea_tesis->actualizado_por_id = Auth::id();
            $linea_tesis->save();

            DB::commit();

            $lineas_tesis = LineaTesisUbs::get();
            return response()->json([
                'message' => 'La línea de trabajo final de grado ' . $linea_tesis->nombre . ' fue actualizado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tesis_ubs.lineas_index')->with('error-message', $e->getMessage());
        }
    }

    public function get_unactivate($id)
    {
        $this->authorize('inactivar_lineas_tesis_ubs');

        try {
            $linea_tesis = LineaTesisUbs::findOrFail($id);
            return response()->json([
                'linea_tesis' => $linea_tesis,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tesis_ubs.lineas_index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_lineas_tesis_ubs');

        DB::beginTransaction();

        try {
            $linea_tesis = LineaTesisUbs::findOrFail($id);
            $linea_tesis->actualizado_por_id = Auth::id();
            $linea_tesis->estado = 'IN';
            $linea_tesis->save();

            DB::commit();

            return response()->json([
                'message' => 'La línea de trabajo final de grado ' . $linea_tesis->nombre . ' fue inactivado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tesis_ubs.lineas_index')->with('error-message', $e->getMessage());
        }
    }

    public function get_activate($id)
    {
        $this->authorize('activar_lineas_tesis_ubs');

        try {
            $linea_tesis = LineaTesisUbs::findOrFail($id);
            return response()->json([
                'linea_tesis' => $linea_tesis,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tesis_ubs.lineas_index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_lineas_tesis_ubs');

        DB::beginTransaction();

        try {
            $linea_tesis = LineaTesisUbs::findOrFail($id);
            $linea_tesis->actualizado_por_id = Auth::id();
            $linea_tesis->estado = 'AC';
            $linea_tesis->save();

            DB::commit();

            return response()->json([
                'message' => 'La línea de trabajo final de grado ' . $linea_tesis->nombre . ' fue activado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tesis_ubs.lineas_index')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_lineas_tesis_ubs');

        try {
            $linea_tesis = LineaTesisUbs::findOrFail($id);
            return response()->json([
                'linea_tesis' => $linea_tesis,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tesis_ubs.lineas_index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_lineas_tesis_ubs');

        DB::beginTransaction();

        try {
            $linea_tesis = LineaTesisUbs::findOrFail($id);
            $linea_tesis->delete();

            DB::commit();

            return response()->json([
                'message' => 'La línea de trabajo final de grado ' . $linea_tesis->nombre . ' fue eliminado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('tesis_ubs.lineas_index')->with('error-message', 'La línea de trabajo final de grado ' . $linea_tesis->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('tesis_ubs.lineas_index')->with('error-message', $e->getMessage());
            }
        }
    }
}
