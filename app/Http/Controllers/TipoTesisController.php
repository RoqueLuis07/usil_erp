<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use App\Models\TipoTesis;


class TipoTesisController extends Controller
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
        $this->authorize('ver_tipos_tesis');

        return view('tesis.tipos.index');
    }

    public function index_ajax()
    {
        $this->authorize('ver_tipos_tesis');

        try {
            $tipos_tesis = TipoTesis::orderBy('id', 'asc')->get();
            return response()->json([
                'tipos_tesis' => $tipos_tesis,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_tipos_tesis');

        try {
            $tipo_tesis = TipoTesis::findOrFail($id);
            return response()->json([
                'tipo_tesis' => $tipo_tesis,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_tipos_tesis');

        DB::beginTransaction();

            $request->validate([
                'nombre' => ['required', Rule::unique('tipos_tesis')]
            ]);

            try {
                $tipo_tesis = new TipoTesis();
                $tipo_tesis->nombre = removeAccents(Str::upper($request->nombre));
                $tipo_tesis->cargado_por_id = Auth::id();
                $tipo_tesis->save();

                DB::commit();

                $tipos_tesis = TipoTesis::get();
                return response()->json([
                    'message' => 'El tipo de trabajo final de grado ' . $tipo_tesis->nombre . ' fue creado exitosamente.',
                    'tipos_tesis' => $tipos_tesis,
                    'selected' => $tipo_tesis,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('tipos_tesis.index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_tipos_tesis');

        try {
            $tipo_tesis = TipoTesis::findOrFail($id);
            return response()->json([
                'tipo_tesis' => $tipo_tesis,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_tipos_tesis');

        DB::beginTransaction();

        $request->validate([
            'nombre' => ['required', Rule::unique('tipos_tesis')->ignore($id)]
        ]);

        try {
            $tipo_tesis = TipoTesis::findOrFail($id);
            $tipo_tesis->nombre = removeAccents(Str::upper($request->nombre));
            $tipo_tesis->actualizado_por_id = Auth::id();
            $tipo_tesis->save();

            DB::commit();

            $tipos_tesis = TipoTesis::get();
            return response()->json([
                'message' => 'El tipo de trabajo final de grado ' . $tipo_tesis->nombre . ' fue actualizado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tipos_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_unactivate($id)
    {
        $this->authorize('inactivar_tipos_tesis');

        try {
            $tipo_tesis = TipoTesis::findOrFail($id);
            return response()->json([
                'tipo_tesis' => $tipo_tesis,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_tipos_tesis');

        DB::beginTransaction();

        try {
            $tipo_tesis = TipoTesis::findOrFail($id);
            $tipo_tesis->actualizado_por_id = Auth::id();
            $tipo_tesis->estado = 'IN';
            $tipo_tesis->save();

            DB::commit();

            return response()->json([
                'message' => 'El tipo de trabajo final de grado ' . $tipo_tesis->nombre . ' fue inactivado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tipos_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_activate($id)
    {
        $this->authorize('activar_tipos_tesis');

        try {
            $tipo_tesis = TipoTesis::findOrFail($id);
            return response()->json([
                'tipo_tesis' => $tipo_tesis,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_tipos_tesis');

        DB::beginTransaction();

        try {
            $tipo_tesis = TipoTesis::findOrFail($id);
            $tipo_tesis->actualizado_por_id = Auth::id();
            $tipo_tesis->estado = 'AC';
            $tipo_tesis->save();

            DB::commit();

            return response()->json([
                'message' => 'El tipo de trabajo final de grado ' . $tipo_tesis->nombre . ' fue activado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tipos_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_tipos_tesis');

        try {
            $tipo_tesis = TipoTesis::findOrFail($id);
            return response()->json([
                'tipo_tesis' => $tipo_tesis,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_tipos_tesis');

        DB::beginTransaction();

        try {
            $tipo_tesis = TipoTesis::findOrFail($id);
            $tipo_tesis->delete();

            DB::commit();

            return response()->json([
                'message' => 'El tipo de trabajo final de grado ' . $tipo_tesis->nombre . ' fue eliminado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('tipos_tesis.index')->with('error-message', 'El tipo de trabajo final de grado ' . $tipo_tesis->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('tipos_tesis.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
