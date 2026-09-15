<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\Models\FechaDesmatriculacion;
use App\Models\Semestre;
use App\Models\Programa;
use App\Models\User;

class FechaDesmatriculacionController extends Controller
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
        $this->authorize('ver_fechas_desmatriculaciones');

        $semestres = Semestre::where('estado', 'AC')->orderBy('id', 'desc')->get();
        $programas = Programa::whereIn('id', [1, 2, 3, 8])->where('estado', 'AC')->orderBy('id', 'asc')->get();

        return view('fechas_desmatriculaciones.index')->with(compact('semestres', 'programas'));
    }

    public function index_ajax()
    {
        $this->authorize('ver_fechas_desmatriculaciones');

        try {
            $fechas_desmatriculaciones = FechaDesmatriculacion::with('semestre', 'programa')->orderBy('id', 'desc')->get();
            foreach ($fechas_desmatriculaciones as $fecha) {
                $fecha->fecha_inicio = Carbon::parse($fecha->fecha_inicio)->format('d/m/Y');
                $fecha->fecha_fin = Carbon::parse($fecha->fecha_fin)->format('d/m/Y');
            }

            return response()->json([
                'fechas_desmatriculaciones' => $fechas_desmatriculaciones,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function show($id)
    {
        $this->authorize('ver_fechas_desmatriculaciones');

        try {
            $fecha_desmatriculacion = FechaDesmatriculacion::with('semestre', 'programa')->findOrFail($id);
            $fecha_desmatriculacion->fecha_inicio = Carbon::parse($fecha_desmatriculacion->fecha_inicio)->format('d/m/Y');
            $fecha_desmatriculacion->fecha_fin = Carbon::parse($fecha_desmatriculacion->fecha_fin)->format('d/m/Y');
            $cargado = User::findOrFail($fecha_desmatriculacion->cargado_por_id);
            $created_at = Carbon::parse($fecha_desmatriculacion->created_at)->format('d/m/Y H:i:s');
            if ($fecha_desmatriculacion->actualizado_por_id) {
                $actualizado = User::findOrFail($fecha_desmatriculacion->actualizado_por_id);
                $updated_at = Carbon::parse($fecha_desmatriculacion->updated_at)->format('d/m/Y H:i:s');
            } else {
                $actualizado = 0;
                $updated_at = 0;
            }
            return response()->json([
                'fecha_desmatriculacion' => $fecha_desmatriculacion,
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
        $this->authorize('crear_fechas_desmatriculaciones');

        DB::beginTransaction();

            $request->validate([
                'semestre' => ['required', 'numeric'],
                'programa' => ['required', 'numeric', Rule::unique('fechas_desmatriculaciones', 'programa_id')
                ->where(fn ($query) => $query->where('programa_id', $request->programa)
                ->where('semestre_id', $request->semestre)
                ->where('estado', 'AC'))],
                'fecha_inicio' => ['required', 'date'],
                'fecha_fin' => ['required', 'date', 'after:fecha_inicio']
            ]);

            try {
                $fecha_desmatriculacion = new FechaDesmatriculacion();
                $fecha_desmatriculacion->semestre_id = $request->semestre;
                $fecha_desmatriculacion->programa_id = $request->programa;
                $fecha_desmatriculacion->fecha_inicio = $request->fecha_inicio;
                $fecha_desmatriculacion->fecha_fin = $request->fecha_fin;
                $fecha_desmatriculacion->cargado_por_id = Auth::id();
                $fecha_desmatriculacion->save();

                DB::commit();

                $fechas_desmatriculaciones = FechaDesmatriculacion::with('semestre', 'programa')->get();
                return response()->json([
                    'message' => 'La fecha de desmatriculación del semestre ' . $fecha_desmatriculacion->semestre->nombre . ' del programa ' . $fecha_desmatriculacion->programa->nombre . ' fue creada exitosamente.',
                    'fechas_desmatriculaciones' => $fechas_desmatriculaciones,
                    'selected' => $fecha_desmatriculacion,
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
        $this->authorize('editar_fechas_desmatriculaciones');

        try {
            $fecha_desmatriculacion = FechaDesmatriculacion::with('semestre', 'programa')->findOrFail($id);
            return response()->json([
                'fecha_desmatriculacion' => $fecha_desmatriculacion,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_fechas_desmatriculaciones');

        DB::beginTransaction();

        $request->validate([
            'semestre' => ['required', 'numeric'],
            'programa' => ['required', 'numeric', Rule::unique('fechas_desmatriculaciones', 'programa_id')
                ->where(fn ($query) => $query->where('programa_id', $request->programa)
                ->where('semestre_id', $request->semestre)
                ->where('estado', 'AC'))->ignore($id)],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after:fecha_inicio']
        ]);

        try {
            $fecha_desmatriculacion = FechaDesmatriculacion::findOrFail($id);
            $fecha_desmatriculacion->semestre_id = $request->semestre;
            $fecha_desmatriculacion->programa_id = $request->programa;
            $fecha_desmatriculacion->fecha_inicio = $request->fecha_inicio;
            $fecha_desmatriculacion->fecha_fin = $request->fecha_fin;
            $fecha_desmatriculacion->actualizado_por_id = Auth::id();
            $fecha_desmatriculacion->save();

            DB::commit();

            $fechas_desmatriculaciones = FechaDesmatriculacion::with('semestre', 'programa')->get();
            return response()->json([
                'message' => 'La fecha de desmatriculación del semestre ' . $fecha_desmatriculacion->semestre->nombre . ' del programa ' . $fecha_desmatriculacion->programa->nombre . ' fue actualizada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('fechas_desmatriculaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_unactivate($id)
    {
        $this->authorize('inactivar_fechas_desmatriculaciones');

        try {
            $fecha_desmatriculacion = FechaDesmatriculacion::with('semestre', 'programa')->findOrFail($id);
            return response()->json([
                'fecha_desmatriculacion' => $fecha_desmatriculacion,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_fechas_desmatriculaciones');

        DB::beginTransaction();

        try {
            $fecha_desmatriculacion = FechaDesmatriculacion::findOrFail($id);
            $fecha_desmatriculacion->actualizado_por_id = Auth::id();
            $fecha_desmatriculacion->estado = 'IN';
            $fecha_desmatriculacion->save();

            DB::commit();

            return response()->json([
                'message' => 'La fecha de desmatriculación del semestre ' . $fecha_desmatriculacion->semestre->nombre . ' del programa ' . $fecha_desmatriculacion->programa->nombre . ' fue inactivada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('fechas_desmatriculaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_activate($id)
    {
        $this->authorize('activar_fechas_desmatriculaciones');

        try {
            $fecha_desmatriculacion = FechaDesmatriculacion::with('semestre', 'programa')->findOrFail($id);
            return response()->json([
                'fecha_desmatriculacion' => $fecha_desmatriculacion,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_fechas_desmatriculaciones');

        DB::beginTransaction();

        try {
            $fecha_desmatriculacion = FechaDesmatriculacion::findOrFail($id);
            $fecha_desmatriculacion->actualizado_por_id = Auth::id();
            $fecha_desmatriculacion->estado = 'AC';
            $fecha_desmatriculacion->save();

            DB::commit();

            return response()->json([
                'message' => 'La fecha de desmatriculación del semestre ' . $fecha_desmatriculacion->semestre->nombre . ' del programa ' . $fecha_desmatriculacion->programa->nombre . ' fue activada exitosamente.',
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
        $this->authorize('eliminar_fechas_desmatriculaciones');

        try {
            $fecha_desmatriculacion = FechaDesmatriculacion::with('semestre', 'programa')->findOrFail($id);
            return response()->json([
                'fecha_desmatriculacion' => $fecha_desmatriculacion,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_fechas_desmatriculaciones');

        DB::beginTransaction();

        try {
            $fecha_desmatriculacion = FechaDesmatriculacion::findOrFail($id);
            $fecha_desmatriculacion->delete();

            DB::commit();

            return response()->json([
                'message' => 'La fecha de desmatriculación del semestre ' . $fecha_desmatriculacion->semestre->nombre . ' del programa ' . $fecha_desmatriculacion->programa->nombre . ' fue eliminada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return response()->json([
                    'message' => 'La fecha de desmatriculación del semestre ' . $fecha_desmatriculacion->semestre->nombre . ' del programa ' . $fecha_desmatriculacion->programa->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.',
                ]);
            } else {
                return response()->json([
                    'message' => $e->getMessage(),
                ]);
            }
        }
    }
}
