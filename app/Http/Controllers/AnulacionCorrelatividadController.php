<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use App\Models\AnulacionCorrelatividad;
use App\Models\Alumno;
use App\Models\Semestre;


class AnulacionCorrelatividadController extends Controller
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
        $this->authorize('ver_anulaciones_correlatividades');

        try {
            $alumnos = Alumno::where('estado', 'AC')->get();
            $semestre = Semestre::where('estado', 'AC')->orderBy('id', 'desc')->first();

            return view('anulaciones_correlatividades.index')->with(compact('alumnos', 'semestre'));
        } catch (\Exception $e) {
            return redirect()->route('anulaciones_correlatividades.index')->with('error-message', $e->getMessage());
        }
    }

    public function index_ajax()
    {
        $this->authorize('ver_anulaciones_correlatividades');

        try {
            $anulaciones = AnulacionCorrelatividad::with('alumno', 'semestre')->orderBy('id', 'desc')->get();
            return response()->json([
                'anulaciones' => $anulaciones,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function show($id)
    {
        $this->authorize('ver_anulaciones_correlatividades');

        try {
            $anulacion = AnulacionCorrelatividad::with('alumno', 'semestre')->findOrFail($id);

            return response()->json([
                'anulacion' => $anulacion,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_anulaciones_correlatividades');

        $request->validate([
            'alumno' => ['required', 'numeric', Rule::unique('anulaciones_correlatividades', 'alumno_id')
                            ->where(fn ($query) => $query->where('estado', 'AC'))],
            //El de arriba verifica que el alumno no tenga ninguna anulacion cargada con estado activo,
            'motivo' => 'required'
        ]);

        try {
            $semestre = Semestre::where('estado', 'AC')->orderBy('id', 'desc')->first();

            $anulacion = new AnulacionCorrelatividad();
            $anulacion->alumno_id = $request->alumno;
            $anulacion->semestre_id = $semestre->id;
            $anulacion->motivo = removeAccents(Str::upper($request->motivo));
            $anulacion->cargado_por_id = Auth::id();
            $anulacion->save();

            return response()->json([
                'message' => 'La anulación de correlatividades del alumno ' . $anulacion->alumno->primer_nombre . ' ' . $anulacion->alumno->primer_apellido .  ' fue creada exitosamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function get_unactivate($id)
    {
        $this->authorize('inactivar_anulaciones_correlatividades');

        try {
            $anulacion = AnulacionCorrelatividad::with('alumno')->findOrFail($id);

            return response()->json([
                'anulacion' => $anulacion,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_anulaciones_correlatividades');

        DB::beginTransaction();

        try {
            $anulacion = AnulacionCorrelatividad::findOrFail($id);
            $anulacion->estado = 'IN';
            $anulacion->save();

            DB::commit();

            return response()->json([
                'message' => 'La anulación de correlatividades del alumno ' . $anulacion->alumno->primer_nombre . ' ' . $anulacion->alumno->primer_apellido .  ' fue inactivada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('anulaciones_correlatividades.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_activate($id)
    {
        $this->authorize('activar_anulaciones_correlatividades');

        try {
            $anulacion = AnulacionCorrelatividad::with('alumno')->findOrFail($id);

            return response()->json([
                'anulacion' => $anulacion,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_anulaciones_correlatividades');

        DB::beginTransaction();

        try {
            $anulacion = AnulacionCorrelatividad::findOrFail($id);
            $anulacion->estado = 'AC';
            $anulacion->save();

            DB::commit();

            return response()->json([
                'message' => 'La anulación de correlatividades del alumno ' . $anulacion->alumno->primer_nombre . ' ' . $anulacion->alumno->primer_apellido .  ' fue activada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('anulaciones_correlatividades.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_anulaciones_correlatividades');

        try {
            $anulacion = AnulacionCorrelatividad::with('alumno')->findOrFail($id);

            return response()->json([
                'anulacion' => $anulacion,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_anulaciones_correlatividades');

        DB::beginTransaction();

        try {
            $anulacion = AnulacionCorrelatividad::findOrFail($id);

            if ($anulacion->estado == 'IN') {
                return response()->json([
                    'message' => 'La anulación de correlatividad no puede ser eliminada. Ésta ya fue utilizada.',
                ]);
            } else {
                $anulacion->delete();
            }

            DB::commit();

            return response()->json([
                'message' => 'La anulación de correlatividades del alumno ' . $anulacion->alumno->primer_nombre . ' ' . $anulacion->alumno->primer_apellido .  ' fue eliminada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('anulaciones_correlatividades.index')->with('error-message', $e->getMessage());
        }
    }
}
