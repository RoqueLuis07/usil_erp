<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use App\Models\TipoDocumentoContable;


class TipoDocumentoContableController extends Controller
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
        $this->authorize('ver_tipos_documentos_contables');

        return view('tipos_documentos_contables.index');
    }

    public function index_ajax()
    {
        $this->authorize('ver_tipos_documentos_contables');

        try {
            $tipos_documentos = TipoDocumentoContable::orderBy('id', 'asc')->get();
            return response()->json([
                'tipos_documentos' => $tipos_documentos,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_documentos_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_tipos_documentos_contables');

        try {
            $tipo_documento = TipoDocumentoContable::findOrFail($id);
            return response()->json([
                'tipo_documento' => $tipo_documento,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_documentos_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_tipos_documentos_contables');

        DB::beginTransaction();

            $request->validate([
                'nombre' => 'required',
            ]);

            try {
                $tipo_documento = new TipoDocumentoContable();
                $tipo_documento->nombre = removeAccents(Str::upper($request->nombre));
                $tipo_documento->save();

                DB::commit();

                $tipos_documentos = TipoDocumentoContable::get();
                return response()->json([
                    'message' => 'El tipo de documento ' . $tipo_documento->nombre . ' fue creado exitosamente.',
                    'tipos_documentos' => $tipos_documentos,
                    'selected' => $tipo_documento,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('tipos_documentos_contables.index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_tipos_documentos_contables');

        try {
            $tipo_documento = TipoDocumentoContable::findOrFail($id);
            return response()->json([
                'tipo_documento' => $tipo_documento,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_documentos_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_tipos_documentos_contables');

        DB::beginTransaction();

        $request->validate([
            'nombre' => 'required',
        ]);

        try {
            $tipo_documento = TipoDocumentoContable::findOrFail($id);
            $tipo_documento->nombre = removeAccents(Str::upper($request->nombre));
            $tipo_documento->tipo = $request->tipo;
            $tipo_documento->save();

            DB::commit();

            $tipos_documentos = TipoDocumentoContable::get();
            return response()->json([
                'message' => 'El tipo de documento ' . $tipo_documento->nombre . ' fue actualizado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tipos_documentos_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_unactivate($id)
    {
        $this->authorize('inactivar_tipos_documentos_contables');

        try {
            $tipo_documento = TipoDocumentoContable::findOrFail($id);
            return response()->json([
                'tipo_documento' => $tipo_documento,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_documentos_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_tipos_documentos_contables');

        DB::beginTransaction();

        try {
            $tipo_documento = TipoDocumentoContable::findOrFail($id);
            $tipo_documento->estado = 'IN';
            $tipo_documento->save();

            DB::commit();

            return response()->json([
                'message' => 'El tipo de documento ' . $tipo_documento->nombre . ' fue inactivado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tipos_documentos_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_activate($id)
    {
        $this->authorize('activar_tipos_documentos_contables');

        try {
            $tipo_documento = TipoDocumentoContable::findOrFail($id);
            return response()->json([
                'tipo_documento' => $tipo_documento,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_documentos_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_tipos_documentos_contables');

        DB::beginTransaction();

        try {
            $tipo_documento = TipoDocumentoContable::findOrFail($id);
            $tipo_documento->estado = 'AC';
            $tipo_documento->save();

            DB::commit();

            return response()->json([
                'message' => 'El tipo de documento ' . $tipo_documento->nombre . ' fue activado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tipos_documentos_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_tipos_documentos_contables');

        try {
            $tipo_documento = TipoDocumentoContable::findOrFail($id);
            return response()->json([
                'tipo_documento' => $tipo_documento,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_documentos_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_tipos_documentos_contables');

        DB::beginTransaction();

        try {
            $tipo_documento = TipoDocumentoContable::findOrFail($id);
            $tipo_documento->delete();

            DB::commit();

            return response()->json([
                'message' => 'El tipo de documento ' . $tipo_documento->nombre . ' fue eliminado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('tipos_documentos_contables.index')->with('error-message', 'El tipo de documento ' . $tipo_documento->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('tipos_documentos_contables.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
