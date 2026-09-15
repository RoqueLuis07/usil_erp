<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\Models\Semestre;
use App\Models\SemestreMalla;
use App\Models\SemestreMallaMateria;
use App\Models\SemestreMallaMateriaHorario;
use App\Models\Docente;
use App\Models\Inscripcion;
use App\Models\Carrera;
use App\Models\Programa;

class MateriaSemestreController extends Controller
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
        $this->authorize('ver_materias_semestres');

        try {
            $materias_semestres = SemestreMallaMateria::with([
                'materia',
                'semestreMalla.malla.carrera.programa',
                'semestreMalla.semestre',
                'docente'
            ])
            ->where('semestres_mallas_materias.estado', 'AC')
            ->join('materias', 'semestres_mallas_materias.materia_id', '=', 'materias.id')
            ->join('semestres_mallas', 'semestres_mallas_materias.semestre_malla_id', '=', 'semestres_mallas.id')
            ->orderBy('materias.nombre_fantasia', 'asc')
            ->orderBy('semestres_mallas.semestre_id', 'desc')
            ->get();

            // convertimos a array para optimizar carga en la vista, pasamos solamente lo necesario
            $datos = $materias_semestres->map(function ($materia_semestre) {
                $inscriptos = Inscripcion::whereHas('matriculacion', function ($query) use ($materia_semestre) {
                    $query->where('semestre_id', $materia_semestre->semestreMalla->semestre_id);
                })
                ->where('materia_id', $materia_semestre->materia_id)
                ->where('docente_id', $materia_semestre->docente_id)
                ->count();

                return [
                    'semestre' => $this->obtenerSemestre($materia_semestre),
                    'materia_id' => $materia_semestre->materia_id,
                    'materia' => $materia_semestre->materia->nombre_fantasia,
                    'docente_id' => $materia_semestre->docente_id,
                    'docente' => $materia_semestre->docente ? $materia_semestre->docente->primer_nombre . ' ' . $materia_semestre->docente->primer_apellido : null,
                    'carrera_id' => $materia_semestre->semestreMalla->malla->carrera_id,
                    'carrera' => $materia_semestre->semestreMalla->malla->carrera->nombre_fantasia,
                    'programa_id' => $materia_semestre->semestreMalla->malla->carrera->programa_id,
                    'programa' => $materia_semestre->semestreMalla->malla->carrera->programa->nombre,
                    'inscriptos' => $inscriptos,
                    'semestre_id' => $materia_semestre->semestreMalla->semestre_id,
                    'periodo' => $materia_semestre->semestreMalla->semestre->nombre,
                ];
            });

            $semestres = Semestre::get();
            $docentes = Docente::where('estado', 'AC')->get();
            $carreras = Carrera::where('estado', 'AC')->get();
            $programas = Programa::whereIn('id', [1, 2, 3, 8])->where('estado', 'AC')->get();

            $hoy = Carbon::today();
            $semestre = Semestre::whereDate('fecha_inicio', '<=', $hoy)
                            ->whereDate('fecha_fin', '>=', $hoy)
                            ->where('estado', 'AC')
                            ->orderBy('id', 'asc')
                            ->first();

            $periodo_activo = $semestre ? $semestre->id : null;


            return view('materias_semestres/index')->with(compact('datos', 'semestres', 'docentes', 'carreras', 'programas', 'periodo_activo'));
        } catch (\Exception $e) {
            return redirect()->route('materias_semestres.index')->with('error-message', $e->getMessage());
        }
    }

    private function obtenerSemestre($materia_semestre)
    {
        foreach ($materia_semestre->semestreMalla->malla->mallaDetalles as $detalle) {
            if ($detalle->materia_id == $materia_semestre->materia_id) {
                return $detalle->semestre;
            }
        }
        return null;
    }
}
