<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use App\Models\BloqueProyectoTesis;

class BloqueProyectoTesisController extends Controller
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
        $this->authorize('ver_bloques_proyectos_tesis');

        return view('tesis.bloques_proyectos.index');
    }

    public function index_ajax()
    {
        $this->authorize('ver_bloques_proyectos_tesis');

        try {
            $bloques = BloqueProyectoTesis::orderBy('id', 'asc')->get();
            return response()->json([
                'bloques' => $bloques,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('bloques_proyectos_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_bloques_proyectos_tesis');

        try {
            $bloque = BloqueProyectoTesis::findOrFail($id);
            return response()->json([
                'bloque' => $bloque,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('bloques_proyectos_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_bloques_proyectos_tesis');

        DB::beginTransaction();

            $request->validate([
                'numero' => ['required', 'numeric', 'min:1', 'max:10', Rule::unique('bloques_proyectos_tesis')],
                'nombre' => ['required', Rule::unique('bloques_proyectos_tesis')]
            ]);

            try {
                $bloque = new BloqueProyectoTesis();
                $bloque->numero = $request->numero;
                $bloque->nombre = removeAccents(Str::upper($request->nombre));
                $bloque->cargado_por_id = Auth::id();
                $bloque->save();

                DB::commit();

                $bloques = BloqueProyectoTesis::get();
                return response()->json([
                    'message' => 'El bloque de proyecto ' . $bloque->nombre . ' fue creado exitosamente.',
                    'bloques' => $bloques,
                    'selected' => $bloque,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('bloques_proyectos_tesis.index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_bloques_proyectos_tesis');

        try {
            $bloque = BloqueProyectoTesis::findOrFail($id);
            return response()->json([
                'bloque' => $bloque,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('bloques_proyectos_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_bloques_proyectos_tesis');

        DB::beginTransaction();

        $request->validate([
            'numero' => ['required', 'numeric', 'min:1', 'max:10', Rule::unique('bloques_proyectos_tesis')->ignore($id)],
            'nombre' => ['required', Rule::unique('bloques_proyectos_tesis')->ignore($id)]
        ]);

        try {
            $bloque = BloqueProyectoTesis::findOrFail($id);
            $bloque->numero = $request->numero;
            $bloque->nombre = removeAccents(Str::upper($request->nombre));
            $bloque->actualizado_por_id = Auth::id();
            $bloque->save();

            DB::commit();

            $bloques = BloqueProyectoTesis::get();
            return response()->json([
                'message' => 'El bloque de proyecto ' . $bloque->nombre . ' fue actualizado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('bloques_proyectos_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_unactivate($id)
    {
        $this->authorize('inactivar_bloques_proyectos_tesis');

        try {
            $bloque = BloqueProyectoTesis::findOrFail($id);
            return response()->json([
                'bloque' => $bloque,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('bloques_proyectos_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_bloques_proyectos_tesis');

        DB::beginTransaction();

        try {
            $bloque = BloqueProyectoTesis::findOrFail($id);
            $bloque->actualizado_por_id = Auth::id();
            $bloque->estado = 'IN';
            $bloque->save();

            DB::commit();

            return response()->json([
                'message' => 'El bloque de proyecto ' . $bloque->nombre . ' fue inactivado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('bloques_proyectos_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_activate($id)
    {
        $this->authorize('activar_bloques_proyectos_tesis');

        try {
            $bloque = BloqueProyectoTesis::findOrFail($id);
            return response()->json([
                'bloque' => $bloque,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('bloques_proyectos_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_bloques_proyectos_tesis');

        DB::beginTransaction();

        try {
            $bloque = BloqueProyectoTesis::findOrFail($id);
            $bloque->actualizado_por_id = Auth::id();
            $bloque->estado = 'AC';
            $bloque->save();

            DB::commit();

            return response()->json([
                'message' => 'El bloque de proyecto ' . $bloque->nombre . ' fue activado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('bloques_proyectos_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_bloques_proyectos_tesis');

        try {
            $bloque = BloqueProyectoTesis::findOrFail($id);
            return response()->json([
                'bloque' => $bloque,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('bloques_proyectos_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_bloques_proyectos_tesis');

        DB::beginTransaction();

        try {
            $bloque = BloqueProyectoTesis::findOrFail($id);
            $bloque->delete();

            DB::commit();

            return response()->json([
                'message' => 'El bloque de proyecto ' . $bloque->nombre . ' fue eliminado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('bloques_proyectos_tesis.index')->with('error-message', 'El bloque de proyecto ' . $bloque->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('bloques_proyectos_tesis.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
