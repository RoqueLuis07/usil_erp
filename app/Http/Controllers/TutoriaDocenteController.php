<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\Docente;
use App\Models\Tutoria;
use App\Models\TutoriaClase;
use App\Models\Modalidad;
use App\Models\Evaluacion;
use App\Models\Semestre;
use App\Models\TutoriaActaEvaluacion;

class TutoriaDocenteController extends Controller
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
        // $this->authorize('ver_tutorias_docentes_pantalla');

        try {
            $docente = Docente::where('usuario_id', $id)->first();
            if (!$docente) {
                return redirect()->route('pantallas_docentes.index', $id)->with('error-message', 'Ocurrió un error. Contacta al administrador del sistema.');
            }
            $hoy = Carbon::today();
            $semestre = Semestre::whereDate('fecha_inicio', '<=', $hoy)
                            ->whereDate('fecha_fin', '>=', $hoy)
                            ->where('estado', 'AC')
                            ->orderBy('id', 'asc')
                            ->first();
            $periodo_activo = $semestre ? $semestre->id : null;

            $tutorias = Tutoria::with('alumnos')->where('docente_id', $docente->id)->where('semestre_id', $periodo_activo)->get();

            return view('pantallas_docentes/tutorias/index')->with(compact('docente', 'tutorias'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_docentes.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        // $this->authorize('ver_tutorias_docentes_pantalla');

        try {
            $docente = Docente::where('usuario_id', Auth::id())->first();
            if (!$docente) {
                return redirect()->route('pantallas_docentes.index', Auth::id())->with('error-message', 'Ocurrió un error. Contacta al administrador del sistema.');
            }
            $tutoria = Tutoria::with(['alumnos' => function ($query) {
                $query->where('estado', '!=', 'AN')
                    ->with(['alumno' => function ($q) {
                        $q->orderBy('primer_apellido', 'asc')
                            ->orderBy('primer_nombre', 'asc');
                    }]);
            }], 'horarios')->findOrFail($id);

            return view('pantallas_docentes/tutorias/show')->with(compact('docente', 'tutoria'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_docentes.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function index_clases($id)
    {
        // $this->authorize('ver_clases_tutorias_docentes_pantalla');

        try {
            $docente = Docente::where('usuario_id', Auth::id())->first();
            if (!$docente) {
                return redirect()->route('pantallas_docentes.index', Auth::id())->with('error-message', 'Ocurrió un error. Contacta al administrador del sistema.');
            }

            $tutoria = Tutoria::findOrFail($id);

            if (!$tutoria->docente_id || !$tutoria->fecha_inicio || !$tutoria->fecha_fin || !$tutoria->cantidad_clases || !$tutoria->cantidad_horas) {
                return back()->with('error-message', 'No es posible generar clases, faltan cargar datos. Contacta al Dpto. Académico.');
            }

            $clases = TutoriaClase::where('tutoria_id', $id)->orderBy('fecha_hora', 'desc')->get();

            return view('pantallas_docentes/tutorias/clases/index')->with(compact('docente', 'tutoria', 'clases'));
        } catch (\Exception $e) {
            return redirect()->route('tutorias_docentes.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function show_clases($id)
    {
        // $this->authorize('ver_clases_tutorias_docentes_pantalla');

        try {
            $docente = Docente::where('usuario_id', Auth::id())->first();
            if (!$docente) {
                return redirect()->route('pantallas_docentes.index', Auth::id())->with('error-message', 'Ocurrió un error. Contacta al administrador del sistema.');
            }

            $clase = TutoriaClase::with('asistencias')->findOrFail($id);

            return view('pantallas_docentes/tutorias/clases/show')->with(compact('docente', 'clase'));
        } catch (\Exception $e) {
            $clase = TutoriaClase::findOrFail($id);
            return redirect()->route('tutorias_docentes.index_clases', $clase->tutoria_id)->with('error-message', $e->getMessage());
        }
    }

    public function create_clases($id)
    {
        //  $this->authorize('crear_clases_tutorias_docentes_pantalla');

        try {
            $docente = Docente::where('usuario_id', Auth::id())->first();
            if (!$docente) {
                return redirect()->route('pantallas_docentes.index', Auth::id())->with('error-message', 'Ocurrió un error. Contacta al administrador del sistema.');
            }

            // QUITAR EL ESTADO IN CUANDO SE INCLUYA ADMINISTRACION

            $tutoria = Tutoria::with(['alumnos' => function ($query) {
                $query->whereIn('estado', ['IN', 'PA', 'EC']);
            }], 'horarios')->findOrFail($id);

            // HASTA ACA

            if ($tutoria->semestre->estado == 'IN') {
                return back()->with('error-message', 'La clase de la tutoria no puede crearse. El semestre se encuentra cerrado.');
            }

            if (!$tutoria->docente_id || !$tutoria->fecha_inicio || !$tutoria->fecha_fin || !$tutoria->cantidad_clases || !$tutoria->cantidad_horas) {
                return back()->with('error-message', 'No es posible generar clases, los datos están incompletos. Contacta al Dpto. Académico.');
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

            return view('pantallas_docentes/tutorias/clases/create')->with(compact('docente', 'tutoria', 'modalidades', 'cantidad_horas'));
        } catch (\Exception $e) {
            return redirect()->route('tutorias_docentes.index_clases', $id)->with('error-message', $e->getMessage());
        }
    }

    public function puntajes($id)
    {
        // $this->authorize('crear_evaluaciones_tutorias_docentes_pantalla');

        try {
            $docente = Docente::where('usuario_id', Auth::id())->first();
            if (!$docente) {
                return redirect()->route('pantallas_docentes.index', Auth::id())->with('error-message', 'Ocurrió un error. Contacta al administrador del sistema.');
            }

            if (!TutoriaActaEvaluacion::where('tutoria_id', $id)->exists()) {
                return back()->with('error-message', 'No se pueden cargar las evaluaciones. El acta de evaluación debe ser generado primeramente.');
            }

            $tutoria = Tutoria::with(['alumnos' => function ($query) {
                $query->where('estado', 'EC');
            }])->findOrFail($id);
            if ($tutoria->semestre->estado == 'IN') {
                return back()->with('error-message', 'El puntaje del alumno en la tutoria no puede cargarse. El semestre se encuentra cerrado.');
            }

            if (!$tutoria->docente_id || !$tutoria->fecha_inicio || !$tutoria->fecha_fin || !$tutoria->cantidad_clases || !$tutoria->cantidad_horas) {
                return back()->with('error-message', 'No es posible generar clases, los datos están incompletos. Contacta al Dpto. Académico.');
            }

            $evaluacion = Evaluacion::findOrFail(4);

            if ($tutoria->alumnos->count() == 0) {
                return back()->with('error-message', 'No se pueden cargar evaluaciones en una tutoría sin alumnos inscriptos.');
            }

            return view('pantallas_docentes/tutorias/evaluaciones/create')->with(compact('docente', 'tutoria', 'evaluacion'));
        } catch (\Exception $e) {
            return redirect()->route('tutorias_docentes.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }
}
