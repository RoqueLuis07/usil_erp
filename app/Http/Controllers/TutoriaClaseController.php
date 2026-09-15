<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\Models\TutoriaClase;
use App\Models\Tutoria;
use App\Models\TutoriaAlumno;
use App\Models\TutoriaHorario;
use App\Models\TutoriaAsistencia;
use App\Models\Modalidad;


class TutoriaClaseController extends Controller
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

    public function index($id)
    {
        $this->authorize('ver_clases_tutorias');

        try {
            $tutoria = Tutoria::findOrFail($id);

            if (!$tutoria->docente_id || !$tutoria->fecha_inicio || !$tutoria->fecha_fin || !$tutoria->cantidad_clases || !$tutoria->cantidad_horas) {
                return back()->with('error-message', 'Debe cargar todos los datos de la tutoría para poder generar clases.');
            }

            $clases = TutoriaClase::where('tutoria_id', $id)->orderBy('fecha_hora', 'desc')->get();

            return view('tutorias/clases/index')->with(compact('tutoria', 'clases'));
        } catch (\Exception $e) {
            return redirect()->route('tutorias.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_clases_tutorias');

        try {
            $clase = TutoriaClase::with('asistencias')->findOrFail($id);

            return view('tutorias/clases/show')->with(compact('clase'));
        } catch (\Exception $e) {
            return redirect()->route('tutorias_clases.index', $clase->tutoria_id)->with('error-message', $e->getMessage());
        }
    }

    public function create($id)
    {
         $this->authorize('crear_clases_tutorias');

        try {
            $tutoria = Tutoria::with(['alumnos' => function ($query) {
                $query->whereIn('estado', ['PA', 'EC']);
            }], 'horarios')->findOrFail($id);

            if ($tutoria->semestre->estado == 'IN') {
                return back()->with('error-message', 'La clase de la tutoria no puede crearse. El semestre se encuentra cerrado.');
            }

            if (!$tutoria->docente_id || !$tutoria->fecha_inicio || !$tutoria->fecha_fin || !$tutoria->cantidad_clases || !$tutoria->cantidad_horas) {
                return back()->with('error-message', 'Debe cargar todos los datos de la tutoría para poder generar clases.');
            }

            if ($tutoria->alumnos->count() == 0) {
                return back()->with('error-message', 'No se pueden cargar clases en una tutoría sin alumnos inscriptos.');
            }

            $modalidades = Modalidad::where('estado', 'AC')->get();

            Carbon::setLocale('es');
            $dia_hoy = Str::upper(Carbon::now()->isoFormat('dddd'));

            if ($tutoria->horarios->count() > 0) {
                foreach ($tutoria->horarios as $horario) {
                    if ($horario->dia_semana_id) {
                        if ($horario->diaSemana->nombre == $dia_hoy) {
                            $hora_inicio = Carbon::createFromFormat('H:i:s', $horario->hora_inicio);
                            $hora_fin = Carbon::createFromFormat('H:i:s', $horario->hora_fin);
                            $cantidad_horas = $hora_inicio->diffInHours($hora_fin);
                        } else {
                            $cantidad_horas = 1;
                        }
                    }
                }
            }

            return view('tutorias/clases/create')->with(compact('tutoria', 'modalidades', 'cantidad_horas'));
        } catch (\Exception $e) {
            return redirect()->route('tutorias_clases.index', $id)->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request, $id)
    {
         $this->authorize('crear_clases_tutorias');

        $request->validate([
            'fecha_hora' => ['required', 'date'],
            'tema_desarrollado' => 'required',
            'horas_desarrollo' => ['required', 'numeric', 'min:1', 'max:2'],
            'modalidad' => ['required', 'numeric'],
            'observaciones' => 'nullable',

            'asistencias' => ['required', 'array'],
            'asistencias.*.alumno' => ['required', 'numeric'],
            'asistencias.*.estado' => ['required', 'size:2'],
            'asistencias.*.*observaciones' => 'nullable'
        ]);

        DB::beginTransaction();

        try {
            $tutoria = Tutoria::with('alumnos')->findOrFail($id);
            if ($tutoria->semestre->estado == 'IN') {
                return back()->with('error-message', 'La clase de la tutoria no puede crearse. El semestre se encuentra cerrado.');
            }

            $clase = new TutoriaClase();
            $clase->tutoria_id = $tutoria->id;
            $clase->fecha_hora = $request->fecha_hora;
            $clase->tema_desarrollado = removeAccents(Str::upper($request->tema_desarrollado));
            $clase->horas_desarrollo = $request->horas_desarrollo;
            $clase->modalidad_id = $request->modalidad;
            $clase->observaciones = $request->observaciones;
            $clase->save();

            foreach ($request->asistencias as $array) {
                $asistencia = new TutoriaAsistencia();
                $asistencia->clase_id = $clase->id;
                $asistencia->alumno_id = $array['alumno'];
                $asistencia->fecha = $clase->fecha_hora;
                $asistencia->estado = $array['estado'];
                $asistencia->observaciones = $array['observaciones'];
                $asistencia->save();

                foreach ($tutoria->alumnos as $detalle) {
                    if ($detalle->alumno_id == $array['alumno'] && $array['estado'] == 'PR') {
                        $detalle->cantidad_asistencias = $detalle->cantidad_asistencias + 1;
                        $detalle->estado = 'EC';
                        $detalle->save();
                    } else {
                        $detalle->estado = 'AN';
                        $detalle->save();
                    }
                }
            }

            $alumnos_activos = TutoriaAlumno::where('tutoria_id', $tutoria->id)->where('estado', '!=', 'AN')->count();

            if ($alumnos_activos > 0) {
                $tutoria->estado = 'EC';
                $tutoria->save();
            } else {
                $tutoria->estado = 'FI';
                $tutoria->save();
            }

            DB::commit();

            if ($request->generado_docente == 'SI') {
                return redirect()->route('tutorias_docentes.index_clases', $clase->tutoria_id)->with('success-message', 'La clase de tutoría de la materia ' . $clase->tutoria->materia->nombre_fantasia . ' fue creada existosamente.');
            } else {
                return redirect()->route('tutorias_clases.index', $id)->with('success-message', 'La clase de tutoría de la materia ' . $clase->tutoria->materia->nombre_fantasia . ' fue creada existosamente.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tutorias_clases.index', $id)->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar asistencias_alumnos_tutorias');

        DB::beginTransaction();

        try {
            $asistencia = TutoriaAsistencia::findOrFail($id);
            if ($asistencia->tutoria->semestre->estado == 'IN') {
                return back()->with('error-message', 'La asistencia del alumno en la tutoria no puede actualizarse. El semestre se encuentra cerrado.');
            }
            $old_asistencia = $asistencia->estado;

            $asistencia->estado = $request->estado;
            $asistencia->save();

            $tutoria = Tutoria::findOrFail($asistencia->clase->tutoria_id);

            if ($asistencia->estado == 'AU' && $old_asistencia != 'AU') {
                $alumno = TutoriaAlumno::where('tutoria_id', $asistencia->clase->tutoria_id)->where('alumno_id', $asistencia->alumno_id)->first();
                $alumno->estado = 'AN';
                if ($alumno->cantidad_asistencias != 0) {
                    $alumno->cantidad_asistencias = $alumno->cantidad_asistencias - 1;
                }
                $alumno->save();
            }

            if ($old_asistencia == 'AU' && ($asistencia->estado == 'PR' || $asistencia->estado == 'AJ')) {
                $alumno = TutoriaAlumno::where('tutoria_id', $asistencia->clase->tutoria_id)->where('alumno_id', $asistencia->alumno_id)->first();
                $alumno->cantidad_asistencias = $alumno->cantidad_asistencias + 1;

                $asistencias_alumno = TutoriaAsistencia::where('clase_id', $asistencia->clase_id)->where('alumno_id', $asistencia->alumno_id)->where('estado', 'AU')->count();
                if ($asistencias_alumno == 0) {
                    $alumno->estado = 'EC';
                }
                $alumno->save();
            }

            $asistencias = TutoriaAsistencia::where('clase_id', $asistencia->clase_id)->where('estado', '!=', 'AU')->count();
            if ($asistencias == 0) {
                $tutoria->estado = 'FI';
            } else {
                $tutoria->estado = 'EC';
            }
            $tutoria->save();

            DB::commit();

            return response()->json([
                'message' => 'La asistencia del alumno ' . $asistencia->alumno->primer_nombre . ' ' . $asistencia->alumno->primer_apellido . ' fue actualizada correctamente.',
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tutorias.index')->with('error-message', $e->getMessage());
        }
    }

    public function update_observacion(Request $request, $id)
    {
        $this->authorize('editar asistencias_alumnos_tutorias');

        DB::beginTransaction();

        try {
            $asistencia = TutoriaAsistencia::findOrFail($id);
            if ($asistencia->tutoria->semestre->estado == 'IN') {
                return back()->with('error-message', 'La observación de la asistencia del alumno en la tutoria no puede actualizarse. El semestre se encuentra cerrado.');
            }
            $asistencia->observaciones = removeAccents(Str::upper($request->observaciones));
            $asistencia->save();

            DB::commit();

            return response()->json([
                'message' => 'La observación de la asistencia del alumno ' . $asistencia->alumno->primer_nombre . ' ' . $asistencia->alumno->primer_apellido . ' fue actualizada correctamente.',
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tutorias.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_clases_tutorias');

        DB::beginTransaction();

        try {
            $clase = TutoriaClase::findOrFail($id);
            $tutoria = Tutoria::findOrFail($clase->tutoria_id);
            if ($tutoria->semestre->estado == 'IN') {
                return back()->with('error-message', 'La tutoria no puede eliminarse. El semestre se encuentra cerrado.');
            }

            if ($tutoria->estado == 'FI') {
                return back()->with('error-message', 'La clase no puede ser eliminada. La tutoría ya se encuentra finalizada.');
            }

            $asistencias = TutoriaAsistencia::where('clase_id', $clase->id)->get();

            foreach ($asistencias as $asistencia) {
                foreach ($tutoria->alumnos as $detalle) {
                    if ($asistencia->alumno_id == $detalle->alumno_id) {
                        $detalle->cantidad_asistencias = $detalle->cantidad_asistencias - 1;
                        if ($detalle->cantidad_asistencias == 0) {
                            $detalle->estado = 'IN';
                        }
                        $detalle->save();
                    }
                }
            }

            TutoriaAsistencia::where('clase_id', $clase->id)->delete();
            $clase->delete();

            if ($tutoria->clases->count() == 0) {
                $tutoria->estado = 'AC';
                $tutoria->save();
            }

            DB::commit();

            return redirect()->route('tutorias_clases.index', $clase->tutoria_id)->with('success-message', 'La clase y asistencia de tutoría de la materia ' . $clase->tutoria->materia->nombre_fantasia . ' fue eliminada existosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('tutorias_clases.index', $clase->tutoria_id)->with('error-message', 'La clase de tutoría de la materia ' . $clase->tutoria->materia->nombre_fantasia . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('clases_materias.index', $clase->tutoria_id)->with('error-message', $e->getMessage());
            }
        }
    }
}
