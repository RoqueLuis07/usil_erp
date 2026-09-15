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
use App\Models\Docente;
use App\Models\Malla;
use App\Models\Matriculacion;
use App\Models\Inscripcion;


class SemestreMallaMateriaController extends Controller
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

    public function show($id)
    {
        $this->authorize('ver_materias_periodos');

        try {
            $semestre_malla = SemestreMalla::with(['semestreMallaMaterias' => function ($query) {
                $query->orderBy('id');
            }])->findOrFail($id);

            foreach ($semestre_malla->semestreMallaMaterias as $smm) {
                foreach ($smm->semestreMallaMateriahorarios as $horario) {
                    if ($horario->dia_semana_id) {
                        $smm->horario = 'OK';
                    } else {
                        $smm->horario = 'NO';
                    }
                }
            }

            $docentes = Docente::where('estado', 'AC')->get();

            return view('semestres/materias/show')->with(compact('semestre_malla', 'docentes'));
        } catch (\Exception $e) {
            return redirect()->route('semestres.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_parametros_materias_periodos');

        $semestre_malla_materia = SemestreMallaMateria::findOrFail($id);
        $parcial_required = 'required';
        if ($semeste_malla_materia->semestreMalla->malla->carrera->programa_id == 3) {
            $parcial_required = 'nullable';
        }

        $request->validate([
            'docente' => ['required', 'numeric'],
            'aula' => 'required',
            'fecha_examen_parcial' => [$parcial_required, 'date'],
            'fecha_examen_ordinario' => ['required', 'date', 'after:fecha_examen_parcial'],
            'fecha_examen_complementario' => ['required', 'date', 'after:fecha_examen_ordinario'],
            'fecha_examen_extraordinario' => ['required', 'date', 'after:fecha_examen_complementario'],
            'observaciones' => 'nullable'
        ]);

        DB::beginTransaction();

        try {
            $semestre_malla_materia = SemestreMallaMateria::findOrFail($id);
            $semestre_malla_materia->docente_id = $request->docente;
            $semestre_malla_materia->aula = removeAccents(Str::upper($request->aula));
            $semestre_malla_materia->fecha_examen_parcial = $request->fecha_examen_parcial;
            $semestre_malla_materia->fecha_examen_ordinario = $request->fecha_examen_ordinario;
            $semestre_malla_materia->fecha_examen_complementario = $request->fecha_examen_complementario;
            $semestre_malla_materia->fecha_examen_extraordinario = $request->fecha_examen_extraordinario;
            $semestre_malla_materia->observaciones = removeAccents(Str::upper($request->observaciones));
            $semestre_malla_materia->save();

            $semestre_malla = SemestreMalla::findOrFail($semestre_malla_materia->semestre_malla_id);
            $malla = Malla::findOrFail($semestre_malla->malla_id);

            $matriculaciones = Matriculacion::where('semestre_id', $semestre_malla->semestre_id)->where('carrera_id', $malla->carrera_id)->where('estado', 'AC')->get();

            foreach ($matriculaciones as $matriculacion) {
                $inscripciones = Inscripcion::where('matriculacion_id', $matriculacion->id)->where('materia_id', $semestre_malla_materia->materia_id)->get();
                foreach ($inscripciones as $inscripcion) {
                    $inscripcion->docente_id = $semestre_malla_materia->docente_id;
                    $inscripcion->save();
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Los detalles de la materia ' . $semestre_malla_materia->materia->nombre_fantasia . ' en esta carrera del semestre fueron actualizados exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('semestres.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('editar_parametros_materias_periodos');

        DB::beginTransaction();

        try {
            $malla_semestre_materia = SemestreMallaMateria::findOrFail($id);
            $malla_semestre_materia->estado = 'IN';
            $malla_semestre_materia->save();

            $semestre_malla = SemestreMalla::findOrFail($malla_semestre_materia->semestre_malla_id);

            DB::commit();

            return response()->json([
                'message' => 'La materia ' . $malla_semestre_materia->materia->nombre_fantasia . ' en la carrera ' . $semestre_malla->malla->carrera->nombre_fantasia . ' fue inactivada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect('semestres_carreras_materias/' . $id)->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('editar_parametros_materias_periodos');

        DB::beginTransaction();

        try {
            $malla_semestre_materia = SemestreMallaMateria::findOrFail($id);
            $malla_semestre_materia->estado = 'AC';
            $malla_semestre_materia->save();

            $semestre_malla = SemestreMalla::findOrFail($malla_semestre_materia->semestre_malla_id);

            DB::commit();

            return response()->json([
                'message' => 'La materia ' . $malla_semestre_materia->materia->nombre_fantasia . ' en la carrera ' . $semestre_malla->malla->carrera->nombre_fantasia . ' fue activada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect('semestres_carreras_materias/' . $id)->with('error-message', $e->getMessage());
        }
    }
}
