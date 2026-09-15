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
use App\Models\DiaSemana;
use App\Models\Docente;


class SemestreMallaMateriaHorarioController extends Controller
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

    public function edit($id)
    {
        $this->authorize('editar_horarios_materias_periodos');

        try {
            $semestre_malla_materia_horario = SemestreMallaMateriaHorario::where('semestre_malla_materia_id', $id)->get();
            $semestre_malla_materia = SemestreMallaMateria::with('SemestreMallaMateriaHorarios')->findOrFail($id);
            $semestre_malla = SemestreMalla::findOrFail($semestre_malla_materia->semestre_malla_id);

            $dias_semana = DiaSemana::whereBetween('id', [2,6])->get();

            return view('semestres/materias/horarios/edit')->with(compact('semestre_malla_materia_horario', 'semestre_malla_materia', 'semestre_malla', 'dias_semana'));
        } catch (\Exception $e) {
            return redirect()->route('semestres.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_horarios_materias_periodos');

        $request->validate([
            'detalles' => ['required', 'array'],
            'detalles.*.dia' => ['required', 'numeric'],
            'detalles.*.hora_inicio' => ['required', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', function ($attribute, $value, $fail) use ($request) {
                $horaInicio = '08:00';
                $horaFin = '21:00';

                if (strtotime($value) < strtotime($horaInicio) || strtotime($value) > strtotime($horaFin)) {
                    return $fail('La :attribute debe estar entre 08:00 y 21:00.');
                }

                // Obtener el índice de la hora de inicio correspondiente
                preg_match('/detalles\.(\d+)\.hora_inicio/', $attribute, $matches);
                $index = $matches[1];

                $horaFinValue = $request->input("detalles.$index.hora_fin");
                if ($horaFinValue) {
                    $inicio = strtotime($value);
                    $fin = strtotime($horaFinValue);

                    if ($fin <= $inicio) {
                        return $fail('La :attribute debe ser menor a la hora de fin.');
                    }
                    if (($fin - $inicio) < 3600) {
                        return $fail('La :attribute debe ser al menos una hora menor a la hora de fin.');
                    }
                }
            }],
            'detalles.*.hora_fin' => ['required', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', function ($attribute, $value, $fail) use ($request) {
                $horaInicio = '09:00';
                $horaFin = '22:00';

                if (strtotime($value) < strtotime($horaInicio) || strtotime($value) > strtotime($horaFin)) {
                    return $fail('La :attribut debe estar entre 09:00 y 22:00.');
                }

                // Obtener el índice de la hora de inicio correspondiente
                preg_match('/detalles\.(\d+)\.hora_fin/', $attribute, $matches);
                $index = $matches[1];

                $horaInicioValue = $request->input("detalles.$index.hora_inicio");
                $inicio = strtotime($horaInicioValue);
                $fin = strtotime($value);

                if ($fin <= $inicio) {
                    return $fail('La :attribute debe ser mayor a la hora de inicio.');
                }
                if (($fin - $inicio) < 3600) {
                    return $fail('La :attribute debe ser al menos una hora mayor a la hora de inicio.');
                }
            }],
        ]);

        DB::beginTransaction();

        $semestre_malla_materia_horario = SemestreMallaMateriaHorario::where('semestre_malla_materia_id', $id)->delete();

        try {
            foreach ($request->detalles as $detalle) {
                $semestre_malla_materia_horario = new SemestreMallaMateriaHorario();
                $semestre_malla_materia_horario->semestre_malla_materia_id = $id;
                $semestre_malla_materia_horario->dia_semana_id = $detalle['dia'];
                $semestre_malla_materia_horario->hora_inicio = $detalle['hora_inicio'];
                $semestre_malla_materia_horario->hora_fin = $detalle['hora_fin'];
                $semestre_malla_materia_horario->save();
            }

            $semestre_malla_materia = SemestreMallaMateria::findOrFail($id);
            $semestre_malla = SemestreMalla::findOrFail($semestre_malla_materia->semestre_malla_id);

            DB::commit();

            $docentes = Docente::where('estado', 'AC')->get();

            $request->session()->flash('success-message', 'Los horarios de la materia ' . $semestre_malla_materia->materia->nombre_fantasia . ' en la carrera de ' . $semestre_malla->malla->carrera->nombre . ' - ' . $semestre_malla->malla->tipoMalla->nombre . ' del semestre fueron actualizados exitosamente.');
            return view('semestres/materias/show')->with(compact('semestre_malla', 'docentes'));
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('semestres.index')->with('error-message', $e->getMessage());
        }
    }
}
