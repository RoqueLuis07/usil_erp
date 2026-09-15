<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use App\Models\TipoCarrera;


class TipoCarreraController extends Controller
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
        $this->authorize('ver_tipos_carreras');

        return view('tipos_carreras.index');
    }

    public function index_ajax()
    {
        $this->authorize('ver_tipos_carreras');

        try {
            $tipos_carreras = TipoCarrera::orderBy('nombre', 'asc')->get();
            return response()->json([
                'tipos_carreras' => $tipos_carreras,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_carreras.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_tipos_carreras');

        try {
            $tipo_carrera = TipoCarrera::findOrFail($id);
            return response()->json([
                'tipo_carrera' => $tipo_carrera,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_carreras.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_tipos_carreras');

        DB::beginTransaction();

            $request->validate([
                'nombre' => ['required', Rule::unique('tipos_carreras')],
            ]);

            try {
                $tipo_carrera = new TipoCarrera();
                $tipo_carrera->nombre = removeAccents(Str::upper($request->nombre));
                $tipo_carrera->cargado_por_id = Auth::id();
                $tipo_carrera->save();

                DB::commit();

                $tipos_carreras = TipoCarrera::get();
                return response()->json([
                    'message' => 'El tipo de carrera ' . $tipo_carrera->nombre . ' fue creado exitosamente.',
                    'tipos_carreras' => $tipos_carreras,
                    'selected' => $tipo_carrera,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('tipos_carreras.index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_tipos_carreras');

        try {
            $tipo_carrera = TipoCarrera::findOrFail($id);
            return response()->json([
                'tipo_carrera' => $tipo_carrera,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_carreras.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_tipos_carreras');

        DB::beginTransaction();

        $request->validate([
            'nombre' => ['required', Rule::unique('tipos_carreras')->ignore($id)],
        ]);

        try {
            $tipo_carrera = TipoCarrera::findOrFail($id);
            $tipo_carrera->nombre = removeAccents(Str::upper($request->nombre));
            $tipo_carrera->actualizado_por_id = Auth::id();
            $tipo_carrera->save();

            DB::commit();

            $tipos_carreras = TipoCarrera::get();
            return response()->json([
                'message' => 'El tipo de carrera ' . $tipo_carrera->nombre . ' fue actualizado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tipos_carreras.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_unactivate($id)
    {
        $this->authorize('inactivar_tipos_carreras');

        try {
            $tipo_carrera = TipoCarrera::findOrFail($id);
            return response()->json([
                'tipo_carrera' => $tipo_carrera,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_carreras.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_tipos_carreras');

        DB::beginTransaction();

        try {
            $tipo_carrera = TipoCarrera::findOrFail($id);
            $tipo_carrera->actualizado_por_id = Auth::id();
            $tipo_carrera->estado = 'IN';
            $tipo_carrera->save();

            DB::commit();

            return response()->json([
                'message' => 'El tipo de carrera ' . $tipo_carrera->nombre . ' fue inactivado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tipos_carreras.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_activate($id)
    {
        $this->authorize('activar_tipos_carreras');

        try {
            $tipo_carrera = TipoCarrera::findOrFail($id);
            return response()->json([
                'tipo_carrera' => $tipo_carrera,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_carreras.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_tipos_carreras');

        DB::beginTransaction();

        try {
            $tipo_carrera = TipoCarrera::findOrFail($id);
            $tipo_carrera->actualizado_por_id = Auth::id();
            $tipo_carrera->estado = 'AC';
            $tipo_carrera->save();

            DB::commit();

            return response()->json([
                'message' => 'El tipo de carrera ' . $tipo_carrera->nombre . ' fue activado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tipos_carreras.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_tipos_carreras');

        try {
            $tipo_carrera = TipoCarrera::findOrFail($id);
            return response()->json([
                'tipo_carrera' => $tipo_carrera,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_carreras.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_tipos_carreras');

        DB::beginTransaction();

        try {
            $tipo_carrera = TipoCarrera::findOrFail($id);
            $tipo_carrera->delete();

            DB::commit();

            return response()->json([
                'message' => 'El tipo de carrera ' . $tipo_carrera->nombre . ' fue eliminado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('tipos_carreras.index')->with('error-message', 'El tipo de carrera ' . $tipo_carrera->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('tipos_carreras.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
