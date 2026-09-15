<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\Models\ExamenSuficienciaFechaSolicitud;
use App\Models\Semestre;
use App\Models\Programa;
use App\Models\User;

class ExamenSuficienciaFechaSolicitudController extends Controller
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
        $this->authorize('ver_fechas_solicitudes_examenes_suficiencia');

        $semestres = Semestre::where('estado', 'AC')->orderBy('id', 'desc')->get();
        $programas = Programa::whereIn('id', [1, 2, 3, 8])->where('estado', 'AC')->orderBy('id', 'asc')->get();

        return view('examenes_suficiencia/fechas_solicitudes.index')->with(compact('semestres', 'programas'));
    }

    public function index_ajax()
    {
        $this->authorize('ver_fechas_solicitudes_examenes_suficiencia');

        try {
            $examenes_suficiencias_fechas_solicitudes = ExamenSuficienciaFechaSolicitud::with('semestre', 'programa')->orderBy('id', 'desc')->get();
            foreach ($examenes_suficiencias_fechas_solicitudes as $fecha) {
                $fecha->fecha_inicio = Carbon::parse($fecha->fecha_inicio)->format('d/m/Y');
                $fecha->fecha_fin = Carbon::parse($fecha->fecha_fin)->format('d/m/Y');
            }

            return response()->json([
                'examenes_suficiencias_fechas_solicitudes' => $examenes_suficiencias_fechas_solicitudes,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function show($id)
    {
        $this->authorize('ver_fechas_solicitudes_examenes_suficiencia');

        try {
            $examen_suficiencia_fecha_solicitud = ExamenSuficienciaFechaSolicitud::with('semestre', 'programa')->findOrFail($id);
            $examen_suficiencia_fecha_solicitud->fecha_inicio = Carbon::parse($examen_suficiencia_fecha_solicitud->fecha_inicio)->format('d/m/Y');
            $examen_suficiencia_fecha_solicitud->fecha_fin = Carbon::parse($examen_suficiencia_fecha_solicitud->fecha_fin)->format('d/m/Y');
            $cargado = User::findOrFail($examen_suficiencia_fecha_solicitud->cargado_por_id);
            $created_at = Carbon::parse($examen_suficiencia_fecha_solicitud->created_at)->format('d/m/Y H:i:s');
            if ($examen_suficiencia_fecha_solicitud->actualizado_por_id) {
                $actualizado = User::findOrFail($examen_suficiencia_fecha_solicitud->actualizado_por_id);
                $updated_at = Carbon::parse($examen_suficiencia_fecha_solicitud->updated_at)->format('d/m/Y H:i:s');
            } else {
                $actualizado = 0;
                $updated_at = 0;
            }
            return response()->json([
                'examen_suficiencia_fecha_solicitud' => $examen_suficiencia_fecha_solicitud,
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
        $this->authorize('crear_fechas_solicitudes_examenes_suficiencia');

        DB::beginTransaction();

            $request->validate([
                'semestre' => ['required', 'numeric'],
                'programa' => ['required', 'numeric', Rule::unique('examenes_suficiencias_fechas_solicitudes', 'programa_id')
                ->where(fn ($query) => $query->where('programa_id', $request->programa)
                ->where('semestre_id', $request->semestre)
                ->where('estado', 'AC'))],
                'fecha_inicio' => ['required', 'date'],
                'fecha_fin' => ['required', 'date', 'after:fecha_inicio']
            ]);

            try {
                $examen_suficiencia_fecha_solicitud = new ExamenSuficienciaFechaSolicitud();
                $examen_suficiencia_fecha_solicitud->semestre_id = $request->semestre;
                $examen_suficiencia_fecha_solicitud->programa_id = $request->programa;
                $examen_suficiencia_fecha_solicitud->fecha_inicio = $request->fecha_inicio;
                $examen_suficiencia_fecha_solicitud->fecha_fin = $request->fecha_fin;
                $examen_suficiencia_fecha_solicitud->cargado_por_id = Auth::id();
                $examen_suficiencia_fecha_solicitud->save();

                DB::commit();

                $examenes_suficiencias_fechas_solicitudes = ExamenSuficienciaFechaSolicitud::with('semestre', 'programa')->get();
                return response()->json([
                    'message' => 'La fecha de solicitud de examen de suficiencia del semestre ' . $examen_suficiencia_fecha_solicitud->semestre->nombre . ' del programa ' . $examen_suficiencia_fecha_solicitud->programa->nombre . ' fue creada exitosamente.',
                    'examenes_suficiencias_fechas_solicitudes' => $examenes_suficiencias_fechas_solicitudes,
                    'selected' => $examen_suficiencia_fecha_solicitud,
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
        $this->authorize('editar_fechas_solicitudes_examenes_suficiencia');

        try {
            $examen_suficiencia_fecha_solicitud = ExamenSuficienciaFechaSolicitud::with('semestre', 'programa')->findOrFail($id);
            if ($examen_suficiencia_fecha_solicitud->semestre->estado == 'IN') {
                return redirect()->route('examenes_suficiencias_fechas_solicitudes.index')->with('error-message', 'Las fechas de examenes de suficiencia no pueden ser modificadas. El semestre ya se encuentra cerrado.');
            }
            return response()->json([
                'examen_suficiencia_fecha_solicitud' => $examen_suficiencia_fecha_solicitud,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_fechas_solicitudes_examenes_suficiencia');

        DB::beginTransaction();

        $request->validate([
            'semestre' => ['required', 'numeric'],
            'programa' => ['required', 'numeric', Rule::unique('examenes_suficiencias_fechas_solicitudes', 'programa_id')
                ->where(fn ($query) => $query->where('programa_id', $request->programa)
                ->where('semestre_id', $request->semestre)
                ->where('estado', 'AC'))->ignore($id)],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after:fecha_inicio']
        ]);

        try {
            $examen_suficiencia_fecha_solicitud = ExamenSuficienciaFechaSolicitud::findOrFail($id);
            $examen_suficiencia_fecha_solicitud->semestre_id = $request->semestre;
            $examen_suficiencia_fecha_solicitud->programa_id = $request->programa;
            $examen_suficiencia_fecha_solicitud->fecha_inicio = $request->fecha_inicio;
            $examen_suficiencia_fecha_solicitud->fecha_fin = $request->fecha_fin;
            $examen_suficiencia_fecha_solicitud->actualizado_por_id = Auth::id();
            $examen_suficiencia_fecha_solicitud->save();

            DB::commit();

            $examenes_suficiencias_fechas_solicitudes = ExamenSuficienciaFechaSolicitud::with('semestre', 'programa')->get();
            return response()->json([
                'message' => 'La fecha de solicitud de examen de suficiencia del semestre ' . $examen_suficiencia_fecha_solicitud->semestre->nombre . ' del programa ' . $examen_suficiencia_fecha_solicitud->programa->nombre . ' fue actualizada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('examenes_suficiencias_fechas_solicitudes.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_unactivate($id)
    {
        $this->authorize('inactivar_fechas_solicitudes_examenes_suficiencia');

        try {
            $examen_suficiencia_fecha_solicitud = ExamenSuficienciaFechaSolicitud::with('semestre', 'programa')->findOrFail($id);
            if ($examen_suficiencia_fecha_solicitud->semestre->estado == 'IN') {
                return redirect()->route('examenes_suficiencias_fechas_solicitudes.index')->with('error-message', 'Las fechas de examenes de suficiencia no pueden ser inactivadas. El semestre ya se encuentra cerrado.');
            }
            return response()->json([
                'examen_suficiencia_fecha_solicitud' => $examen_suficiencia_fecha_solicitud,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_fechas_solicitudes_examenes_suficiencia');

        DB::beginTransaction();

        try {
            $examen_suficiencia_fecha_solicitud = ExamenSuficienciaFechaSolicitud::findOrFail($id);
            $examen_suficiencia_fecha_solicitud->actualizado_por_id = Auth::id();
            $examen_suficiencia_fecha_solicitud->estado = 'IN';
            $examen_suficiencia_fecha_solicitud->save();

            DB::commit();

            return response()->json([
                'message' => 'La fecha de solicitud de examen de suficiencia del semestre ' . $examen_suficiencia_fecha_solicitud->semestre->nombre . ' del programa ' . $examen_suficiencia_fecha_solicitud->programa->nombre . ' fue inactivada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('examenes_suficiencias_fechas_solicitudes.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_activate($id)
    {
        $this->authorize('activar_fechas_solicitudes_examenes_suficiencia');

        try {
            $examen_suficiencia_fecha_solicitud = ExamenSuficienciaFechaSolicitud::with('semestre', 'programa')->findOrFail($id);
            if ($examen_suficiencia_fecha_solicitud->semestre->estado == 'IN') {
                return redirect()->route('examenes_suficiencias_fechas_solicitudes.index')->with('error-message', 'Las fechas de examenes de suficiencia no pueden ser activadas. El semestre ya se encuentra cerrado.');
            }
            return response()->json([
                'examen_suficiencia_fecha_solicitud' => $examen_suficiencia_fecha_solicitud,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_fechas_solicitudes_examenes_suficiencia');

        DB::beginTransaction();

        try {
            $examen_suficiencia_fecha_solicitud = ExamenSuficienciaFechaSolicitud::findOrFail($id);
            $examen_suficiencia_fecha_solicitud->actualizado_por_id = Auth::id();
            $examen_suficiencia_fecha_solicitud->estado = 'AC';
            $examen_suficiencia_fecha_solicitud->save();

            DB::commit();

            return response()->json([
                'message' => 'La fecha de solicitud de examen de suficiencia del semestre ' . $examen_suficiencia_fecha_solicitud->semestre->nombre . ' del programa ' . $examen_suficiencia_fecha_solicitud->programa->nombre . ' fue activada exitosamente.',
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
        $this->authorize('eliminar_fechas_solicitudes_examenes_suficiencia');

        try {
            $examen_suficiencia_fecha_solicitud = ExamenSuficienciaFechaSolicitud::with('semestre', 'programa')->findOrFail($id);
            if ($examen_suficiencia_fecha_solicitud->semestre->estado == 'IN') {
                return redirect()->route('examenes_suficiencias_fechas_solicitudes.index')->with('error-message', 'Las fechas de examenes de suficiencia no pueden ser eliminadas. El semestre ya se encuentra cerrado.');
            }
            return response()->json([
                'examen_suficiencia_fecha_solicitud' => $examen_suficiencia_fecha_solicitud,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_fechas_solicitudes_examenes_suficiencia');

        DB::beginTransaction();

        try {
            $examen_suficiencia_fecha_solicitud = ExamenSuficienciaFechaSolicitud::findOrFail($id);
            $examen_suficiencia_fecha_solicitud->delete();

            DB::commit();

            return response()->json([
                'message' => 'La fecha de solicitud de examen de suficiencia del semestre ' . $examen_suficiencia_fecha_solicitud->semestre->nombre . ' del programa ' . $examen_suficiencia_fecha_solicitud->programa->nombre . ' fue eliminada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return response()->json([
                    'message' => 'La fecha de solicitud de examen de suficiencia del semestre ' . $examen_suficiencia_fecha_solicitud->semestre->nombre . ' del programa ' . $examen_suficiencia_fecha_solicitud->programa->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.',
                ]);
            } else {
                return response()->json([
                    'message' => $e->getMessage(),
                ]);
            }
        }
    }
}
