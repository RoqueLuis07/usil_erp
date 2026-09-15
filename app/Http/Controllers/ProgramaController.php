<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


use App\Models\Programa;
use App\Models\User;


class ProgramaController extends Controller
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
        $this->authorize('ver_programas');

        return view('programas.index');
    }

    public function index_ajax()
    {
        $this->authorize('ver_programas');

        try {
            $programas = Programa::orderBy('nombre', 'asc')->get();
            return response()->json([
                'programas' => $programas,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('programas.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_programas');

        try {
            $programa = Programa::findOrFail($id);
            $cargado = User::findOrFail($programa->cargado_por_id);
            $created_at = Carbon::parse($programa->created_at)->format('d/m/Y H:i:s');
            if ($programa->actualizado_por_id) {
                $actualizado = User::findOrFail($programa->actualizado_por_id);
                $updated_at = Carbon::parse($programa->updated_at)->format('d/m/Y H:i:s');
            } else {
                $actualizado = 0;
                $updated_at = 0;
            }

            return response()->json([
                'programa' => $programa,
                'cargado' => $cargado,
                'created_at' => $created_at,
                'actualizado' => $actualizado,
                'updated_at' => $updated_at,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('programas.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_programas');

        DB::beginTransaction();

            $request->validate([
                'nombre' => ['required', Rule::unique('programas')],
                'cantidad_creditos' => 'required',
                'duracion' => 'required'
            ]);

            try {
                $programa = new Programa();
                $programa->nombre = removeAccents(Str::upper($request->nombre));
                $programa->cantidad_creditos = $request->cantidad_creditos;
                $programa->duracion = $request->duracion;
                $programa->cargado_por_id = Auth::id();
                $programa->save();

                DB::commit();

                $programas = Programa::get();
                return response()->json([
                    'message' => 'El programa' . $programa->nombre . ' fue creado exitosamente.',
                    'programas' => $programas,
                    'selected' => $programa,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('programas.index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_programas');

        try {
            $programa = Programa::findOrFail($id);
            return response()->json([
                'programa' => $programa,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('programas.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_programas');

        DB::beginTransaction();

        $request->validate([
            'nombre' => ['required', Rule::unique('programas')->ignore($id)],
            'cantidad_creditos' => 'required',
            'duracion' => 'required'
        ]);

        try {
            $programa = Programa::findOrFail($id);
            $programa->nombre = removeAccents(Str::upper($request->nombre));
            $programa->cantidad_creditos = $request->cantidad_creditos;
            $programa->duracion = $request->duracion;
            $programa->actualizado_por_id = Auth::id();
            $programa->save();

            DB::commit();

            $programas = Programa::get();
            return response()->json([
                'message' => 'El programa' . $programa->nombre . ' fue actualizado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('programas.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_unactivate($id)
    {
        $this->authorize('inactivar_programas');

        try {
            $programa = Programa::findOrFail($id);
            return response()->json([
                'programa' => $programa,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('programas.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_programas');

        DB::beginTransaction();

        try {
            $programa = Programa::findOrFail($id);
            $programa->estado = 'IN';
            $programa->save();

            DB::commit();

            return response()->json([
                'message' => 'El programa' . $programa->nombre . ' fue inactivado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('programas.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_activate($id)
    {
        $this->authorize('activar_programas');

        try {
            $programa = Programa::findOrFail($id);
            return response()->json([
                'programa' => $programa,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('programas.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_programas');

        DB::beginTransaction();

        try {
            $programa = Programa::findOrFail($id);
            $programa->estado = 'AC';
            $programa->save();

            DB::commit();

            return response()->json([
                'message' => 'El programa' . $programa->nombre . ' fue activado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('programas.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_programas');

        try {
            $programa = Programa::findOrFail($id);
            return response()->json([
                'programa' => $programa,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('programas.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_programas');

        DB::beginTransaction();

        try {
            $programa = Programa::findOrFail($id);
            $programa->delete();

            DB::commit();

            return response()->json([
                'message' => 'El programa' . $programa->nombre . ' fue eliminado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('programas.index')->with('error-message', 'El programa' . $programa->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('programas.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
