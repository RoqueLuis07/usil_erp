<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\PuntoImpresion;
use App\Models\TipoDocumentoContable;
use App\Models\Timbrado;
use App\Models\User;


class PuntoImpresionController extends Controller
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
        $this->authorize('ver_puntos_impresiones');

        $tipos_documentos = TipoDocumentoContable::where('estado', 'AC')->get();
        $timbrados = Timbrado::where('estado', 'AC')->get();

        return view('puntos_impresiones.index')->with(compact('tipos_documentos', 'timbrados'));
    }

    public function index_ajax()
    {
        $this->authorize('ver_puntos_impresiones');

        try {
            $puntos_impresiones = PuntoImpresion::with('tipoDocumento', 'timbrado')->orderBy('id', 'desc')->get();
            return response()->json([
                'puntos_impresiones' => $puntos_impresiones,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('puntos_impresiones.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_puntos_impresiones');

        try {
            $punto_impresion = PuntoImpresion::with('tipoDocumento', 'timbrado')->findOrFail($id);
            $cargado = User::findOrFail($punto_impresion->cargado_por_id);
            $created_at = Carbon::parse($punto_impresion->created_at)->format('d/m/Y H:i:s');
            if ($punto_impresion->actualizado_por_id) {
                $actualizado = User::findOrFail($punto_impresion->actualizado_por_id);
                $updated_at = Carbon::parse($punto_impresion->updated_at)->format('d/m/Y H:i:s');
            } else {
                $actualizado = 0;
                $updated_at = 0;
            }
            return response()->json([
                'punto_impresion' => $punto_impresion,
                'cargado' => $cargado,
                'created_at' => $created_at,
                'actualizado' => $actualizado,
                'updated_at' => $updated_at,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('puntos_impresiones.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_puntos_impresiones');

        DB::beginTransaction();

            $request->validate([
                'nombre' => 'required',
                'codigo' => 'required',
                'tipo_documento' => ['required', 'numeric'],
                'timbrado' => ['required', 'numeric'],
                'numero_desde' => ['required', 'numeric', 'min:1', 'digits_between:1,7'],
                'numero_hasta' => ['required', 'numeric', 'min:' . $request->numero_desde, 'digits_between:1,7'],
            ]);

            try {
                $punto_impresion = new PuntoImpresion();
                $punto_impresion->nombre = removeAccents(Str::upper($request->nombre));
                $punto_impresion->codigo = $request->codigo;
                $punto_impresion->tipo_documento_id = $request->tipo_documento;
                $punto_impresion->timbrado_id = $request->timbrado;
                $punto_impresion->numero_desde = str_pad($request->numero_desde, 7,'0', STR_PAD_LEFT);
                $punto_impresion->numero_hasta = str_pad($request->numero_hasta, 7,'0', STR_PAD_LEFT);
                $punto_impresion->cargado_por_id = Auth::id();
                $punto_impresion->save();

                DB::commit();

                $puntos_impresiones = PuntoImpresion::get();
                return response()->json([
                    'message' => 'El punto de impresión ' . $punto_impresion->nombre . ' fue creado exitosamente.',
                    'puntos_impresiones' => $puntos_impresiones,
                    'selected' => $punto_impresion,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('puntos_impresiones.index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_puntos_impresiones');

        try {
            $punto_impresion = PuntoImpresion::with('tipoDocumento', 'timbrado')->findOrFail($id);
            $tipos_documentos = TipoDocumentoContable::where('estado', 'AC')->get();
            $timbrados = Timbrado::where('estado', 'AC')->get();
            return response()->json([
                'punto_impresion' => $punto_impresion,
                'tipos_documentos' => $tipos_documentos,
                'timbrados' => $timbrados,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('puntos_impresiones.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_puntos_impresiones');

        DB::beginTransaction();

        $request->validate([
            'nombre' => 'required',
            'codigo' => 'required',
            'tipo_documento' => ['required', 'numeric'],
            'timbrado' => ['required', 'numeric'],
            'numero_desde' => ['required', 'numeric', 'min: 1', 'digits_between:1,7'],
            'numero_hasta' => ['required', 'numeric', 'min:' . $request->numero_desde, 'digits_between:1,7'],
        ]);

        try {
            $punto_impresion = PuntoImpresion::findOrFail($id);
            $punto_impresion->nombre = removeAccents(Str::upper($request->nombre));
            $punto_impresion->codigo = $request->codigo;
            $punto_impresion->tipo_documento_id = $request->tipo_documento;
            $punto_impresion->timbrado_id = $request->timbrado;
            $punto_impresion->numero_desde = str_pad($request->numero_desde, 7,'0', STR_PAD_LEFT);
            $punto_impresion->numero_hasta = str_pad($request->numero_hasta, 7,'0', STR_PAD_LEFT);
            $punto_impresion->actualizado_por_id = Auth::id();
            $punto_impresion->save();

            DB::commit();

            $puntos_impresiones = PuntoImpresion::get();
            return response()->json([
                'message' => 'El punto de impresión ' . $punto_impresion->nombre . ' fue actualizado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('puntos_impresiones.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_unactivate($id)
    {
        $this->authorize('inactivar_puntos_impresiones');

        try {
            $punto_impresion = PuntoImpresion::findOrFail($id);
            return response()->json([
                'punto_impresion' => $punto_impresion,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('puntos_impresiones.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_puntos_impresiones');

        DB::beginTransaction();

        try {
            $punto_impresion = PuntoImpresion::findOrFail($id);
            $punto_impresion->actualizado_por_id = Auth::id();
            $punto_impresion->estado = 'IN';
            $punto_impresion->save();

            DB::commit();

            return response()->json([
                'message' => 'El punto de impresión ' . $punto_impresion->nombre . ' fue inactivado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('puntos_impresiones.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_activate($id)
    {
        $this->authorize('activar_puntos_impresiones');

        try {
            $punto_impresion = PuntoImpresion::findOrFail($id);
            return response()->json([
                'punto_impresion' => $punto_impresion,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('puntos_impresiones.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_puntos_impresiones');

        DB::beginTransaction();

        try {
            $punto_impresion = PuntoImpresion::findOrFail($id);
            $punto_impresion->actualizado_por_id = Auth::id();
            $punto_impresion->estado = 'AC';
            $punto_impresion->save();

            DB::commit();

            return response()->json([
                'message' => 'El punto de impresión ' . $punto_impresion->nombre . ' fue activado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('puntos_impresiones.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_puntos_impresiones');

        try {
            $punto_impresion = PuntoImpresion::findOrFail($id);
            return response()->json([
                'punto_impresion' => $punto_impresion,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('puntos_impresiones.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_puntos_impresiones');

        DB::beginTransaction();

        try {
            $punto_impresion = PuntoImpresion::findOrFail($id);
            $punto_impresion->delete();

            DB::commit();

            return response()->json([
                'message' => 'El punto de impresión ' . $punto_impresion->nombre . ' fue eliminado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('puntos_impresiones.index')->with('error-message', 'El punto de impresión ' . $punto_impresion->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('puntos_impresiones.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
