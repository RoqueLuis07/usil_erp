<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\Models\Encuesta;


class EncuestaController extends Controller
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
        $this->authorize('ver_encuestas');

        return view('encuestas.index');
    }

    public function index_ajax()
    {
        $this->authorize('ver_encuestas');

        try {
            $encuestas = Encuesta::orderBy('fecha_publicacion', 'desc')->get();
            foreach ($encuestas as $encuesta) {
                $encuesta->fecha_publicacion = Carbon::parse($encuesta->fecha_publicacion)->format('d/m/Y');
                $encuesta->fecha_vencimiento = Carbon::parse($encuesta->fecha_vencimiento)->format('d/m/Y');
            }

            return response()->json([
                'encuestas' => $encuestas,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function show($id)
    {
        $this->authorize('ver_encuestas');

        try {
            $encuesta = Encuesta::findOrFail($id);
            $cargado = User::findOrFail($encuesta->cargado_por_id);
            $created_at = Carbon::parse($encuesta->created_at)->format('d/m/Y H:i:s');
            if ($encuesta->actualizado_por_id) {
                $actualizado = User::findOrFail($encuesta->actualizado_por_id);
                $updated_at = Carbon::parse($encuesta->updated_at)->format('d/m/Y H:i:s');
            } else {
                $actualizado = 0;
                $updated_at = 0;
            }
            return response()->json([
                'encuesta' => $encuesta,
                'cargado' => $cargado,
                'created_at' => $created_at,
                'actualizado' => $actualizado,
                'updated_at' => $updated_at,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_encuestas');

        DB::beginTransaction();

            $request->validate([
                'fecha_publicacion' => ['required', 'date'],
                'nombre' => 'required',
                'tipo' => 'required',
                'url_forms' => ['required', 'active_url', Rule::unique('encuestas')],
                'fecha_vencimiento' => ['required', 'date', 'after:fecha_publicacion']
            ]);

            try {
                $encuesta = new Encuesta();
                $encuesta->fecha_publicacion = $request->fecha_publicacion;
                $encuesta->nombre = removeAccents(Str::upper($request->nombre));
                $encuesta->tipo = $request->tipo;
                $encuesta->url_forms = $request->url_forms;
                $encuesta->fecha_vencimiento = $request->fecha_vencimiento;
                $encuesta->cargado_por_id = Auth::id();
                if (Carbon::now() > $request->fecha_publicacion) {
                    $encuesta->estado = 'PU';
                }
                $encuesta->save();

                DB::commit();

                $encuestas = Encuesta::get();
                return response()->json([
                    'message' => 'La encuesta ' . $encuesta->nombre . ' fue creada exitosamente.',
                    'encuestas' => $encuestas,
                    'selected' => $encuesta,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'message' => $e->getMessage(),
                ]);
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_encuestas');

        try {
            $encuesta = Encuesta::findOrFail($id);
            return response()->json([
                'encuesta' => $encuesta,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_encuestas');

        DB::beginTransaction();

        $request->validate([
            'fecha_publicacion' => ['required', 'date'],
            'nombre' => 'required',
            'tipo' => 'required',
            'url_forms' => ['required', 'active_url', Rule::unique('encuestas')->ignore($id)],
            'fecha_vencimiento' => ['required', 'date', 'after:fecha_publicacion']
        ]);

        try {
            $encuesta = Encuesta::findOrFail($id);
            $encuesta->fecha_publicacion = $request->fecha_publicacion;
            $encuesta->nombre = removeAccents(Str::upper($request->nombre));
            $encuesta->tipo = $request->tipo;
            $encuesta->url_forms = $request->url_forms;
            $encuesta->fecha_vencimiento = $request->fecha_vencimiento;
            if ($request->fecha_cambiada == 'SI') {
                $encuesta->fecha_publicacion = $request->fecha_publicacion;
                if (Carbon::now() > $request->fecha_publicacion) {
                    $encuesta->estado = 'PU';
                } else {
                    $encuesta->estado = 'PE';
                }
            }
            $encuesta->actualizado_por_id = Auth::id();
            $encuesta->save();

            DB::commit();

            $encuestas = Encuesta::get();
            return response()->json([
                'message' => 'La encuesta ' . $encuesta->nombre . ' fue actualizada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('encuestas.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_unactivate($id)
    {
        this->authorize('inactivar_encuestas');

        try {
            $encuesta = Encuesta::findOrFail($id);
            return response()->json([
                'encuesta' => $encuesta,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function unactivate($id)
    {
        this->authorize('inactivar_encuestas');

        DB::beginTransaction();

        try {
            $encuesta = Encuesta::findOrFail($id);
            $encuesta->actualizado_por_id = Auth::id();
            $encuesta->estado = 'IN';
            $encuesta->save();

            DB::commit();

            return response()->json([
                'message' => 'La encuesta ' . $encuesta->nombre . ' fue no publicada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('encuestas.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_activate($id)
    {
        this->authorize('activar_encuestas');

        try {
            $encuesta = Encuesta::findOrFail($id);
            return response()->json([
                'encuesta' => $encuesta,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function activate($id)
    {
        this->authorize('activar_encuestas');

        DB::beginTransaction();

        try {
            $encuesta = Encuesta::findOrFail($id);
            $encuesta->actualizado_por_id = Auth::id();
            $encuesta->estado = 'AC';
            $encuesta->save();

            DB::commit();

            return response()->json([
                'message' => 'La encuesta ' . $encuesta->nombre . ' fue publicada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function get_destroy($id)
    {
        this->authorize('eliminar_encuestas');

        try {
            $encuesta = Encuesta::findOrFail($id);
            return response()->json([
                'encuesta' => $encuesta,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function destroy($id)
    {
        this->authorize('eliminar_encuestas');

        DB::beginTransaction();

        try {
            $encuesta = Encuesta::findOrFail($id);
            $encuesta->delete();

            DB::commit();

            return response()->json([
                'message' => 'La encuesta ' . $encuesta->nombre . ' fue eliminada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return response()->json([
                    'message' => 'La encuesta ' . $encuesta->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.',
                ]);
            } else {
                return response()->json([
                    'message' => $e->getMessage(),
                ]);
            }
        }
    }
}
