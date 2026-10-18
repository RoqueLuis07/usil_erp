<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\Models\Semestre;

/**
 * Períodos académicos (ej. "2026-1"). Extensión los usa para ubicar cada actividad en su período
 * y para el reporte por carrera y semestre.
 */
class SemestreController extends Controller
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

    public function index()
    {
        $this->authorize('ver_periodos');

        try {
            $semestres = Semestre::orderBy('id', 'desc')->get();
            return view('semestres/index')->with(compact('semestres'));
        } catch (\Exception $e) {
            return redirect()->route('root')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_periodos');

        return view('semestres/create');
    }

    public function store(Request $request)
    {
        $this->authorize('crear_periodos');

        $request->validate([
            'nombre_semestre' => ['required', Rule::unique('semestres', 'nombre')],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio'],
        ]);

        DB::beginTransaction();

        try {
            $semestre = new Semestre();
            $semestre->nombre = removeAccents(Str::upper(trim($request->nombre_semestre)));
            $semestre->fecha_inicio = $request->fecha_inicio;
            $semestre->fecha_fin = $request->fecha_fin;
            $semestre->cargado_por_id = Auth::id();
            $semestre->save();

            DB::commit();

            return redirect()->route('semestres.index')->with('success-message', 'El semestre ' . $semestre->nombre . ' fue creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('semestres.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_periodos');

        DB::beginTransaction();

        try {
            $semestre = Semestre::findOrFail($id);
            $semestre->actualizado_por_id = Auth::id();
            $semestre->estado = 'IN';
            $semestre->save();

            DB::commit();

            return redirect()->route('semestres.index')->with('error-message', 'El semestre ' . $semestre->nombre . ' fue inactivado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('semestres.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_periodos');

        DB::beginTransaction();

        try {
            $semestre = Semestre::findOrFail($id);
            if ($semestre->fecha_fin < Carbon::today()->startOfDay()) {
                return back()->with('error-message', 'El semestre no puede activarse. La fecha de fin es menor a la fecha de hoy.');
            }
            $semestre->actualizado_por_id = Auth::id();
            $semestre->estado = 'AC';
            $semestre->save();

            DB::commit();

            return redirect()->route('semestres.index')->with('success-message', 'El semestre ' . $semestre->nombre . ' fue activado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('semestres.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_periodos');

        DB::beginTransaction();

        try {
            $semestre = Semestre::findOrFail($id);
            $semestre->delete();

            DB::commit();

            return redirect()->route('semestres.index')->with('success-message', 'El semestre ' . $semestre->nombre . ' fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            // Foreign key: el período ya lo usan actividades u otros registros
            if (stripos($e->getMessage(), 'foreign key') !== false || stripos($e->getMessage(), 'Integrity constraint') !== false) {
                return redirect()->route('semestres.index')->with('error-message', 'El semestre no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            }
            return redirect()->route('semestres.index')->with('error-message', $e->getMessage());
        }
    }
}
