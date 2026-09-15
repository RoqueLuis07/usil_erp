<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use App\Models\TipoSolicitud;


class TipoSolicitudController extends Controller
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
        $this->authorize('ver_tipos_solicitudes');

        return view('tipos_solicitudes.index');
    }

    public function index_ajax()
    {
        $this->authorize('ver_tipos_solicitudes');

        try {
            $tipos_solicitudes = TipoSolicitud::orderBy('id', 'asc')->get();
            return response()->json([
                'tipos_solicitudes' => $tipos_solicitudes,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_solicitudes.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_tipos_solicitudes');

        try {
            $tipo_solicitud = TipoSolicitud::findOrFail($id);
            return response()->json([
                'tipo_solicitud' => $tipo_solicitud,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_solicitudes.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_tipos_solicitudes');

        DB::beginTransaction();

            $request->validate([
                'nombre' => ['required', Rule::unique('tipos_solicitudes')],
                'pagado' => 'required',
            ]);

            try {
                $tipo_solicitud = new TipoSolicitud();
                $tipo_solicitud->nombre = removeAccents(Str::upper($request->nombre));
                $tipo_solicitud->pagado = $request->pagado;
                $tipo_solicitud->cargado_por_id = Auth::id();
                $tipo_solicitud->save();

                DB::commit();

                $tipos_solicitudes = TipoSolicitud::get();
                return response()->json([
                    'message' => 'El tipo de solicitud ' . $tipo_solicitud->nombre . ' fue creado exitosamente.',
                    'tipos_solicitudes' => $tipos_solicitudes,
                    'selected' => $tipo_solicitud,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('tipos_solicitudes.index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_tipos_solicitudes');

        try {
            $tipo_solicitud = TipoSolicitud::findOrFail($id);
            return response()->json([
                'tipo_solicitud' => $tipo_solicitud,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_solicitudes.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_tipos_solicitudes');

        DB::beginTransaction();

        $request->validate([
            'nombre' => ['required', Rule::unique('tipos_solicitudes')->ignore($id)],
            'pagado' => 'required',
        ]);

        try {
            $tipo_solicitud = TipoSolicitud::findOrFail($id);
            $tipo_solicitud->nombre = removeAccents(Str::upper($request->nombre));
            $tipo_solicitud->pagado = $request->pagado;
            $tipo_solicitud->actualizado_por_id = Auth::id();
            $tipo_solicitud->save();

            DB::commit();

            $tipos_solicitudes = TipoSolicitud::get();
            return response()->json([
                'message' => 'El tipo de solicitud ' . $tipo_solicitud->nombre . ' fue actualizado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tipos_solicitudes.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_unactivate($id)
    {
        $this->authorize('inactivar_tipos_solicitudes');

        try {
            $tipo_solicitud = TipoSolicitud::findOrFail($id);
            return response()->json([
                'tipo_solicitud' => $tipo_solicitud,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_solicitudes.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_tipos_solicitudes');

        DB::beginTransaction();

        try {
            $tipo_solicitud = TipoSolicitud::findOrFail($id);
            $tipo_solicitud->actualizado_por_id = Auth::id();
            $tipo_solicitud->estado = 'IN';
            $tipo_solicitud->save();

            DB::commit();

            return response()->json([
                'message' => 'El tipo de solicitud ' . $tipo_solicitud->nombre . ' fue inactivado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tipos_solicitudes.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_activate($id)
    {
        $this->authorize('activar_tipos_solicitudes');

        try {
            $tipo_solicitud = TipoSolicitud::findOrFail($id);
            return response()->json([
                'tipo_solicitud' => $tipo_solicitud,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_solicitudes.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_tipos_solicitudes');

        DB::beginTransaction();

        try {
            $tipo_solicitud = TipoSolicitud::findOrFail($id);
            $tipo_solicitud->actualizado_por_id = Auth::id();
            $tipo_solicitud->estado = 'AC';
            $tipo_solicitud->save();

            DB::commit();

            return response()->json([
                'message' => 'El tipo de solicitud ' . $tipo_solicitud->nombre . ' fue activado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tipos_solicitudes.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_tipos_solicitudes');

        try {
            $tipo_solicitud = TipoSolicitud::findOrFail($id);
            return response()->json([
                'tipo_solicitud' => $tipo_solicitud,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_solicitudes.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_tipos_solicitudes');

        DB::beginTransaction();

        try {
            $tipo_solicitud = TipoSolicitud::findOrFail($id);
            $tipo_solicitud->delete();

            DB::commit();

            return response()->json([
                'message' => 'El tipo de solicitud ' . $tipo_solicitud->nombre . ' fue eliminado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('tipos_solicitudes.index')->with('error-message', 'El tipo de solicitud ' . $tipo_solicitud->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('tipos_solicitudes.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
