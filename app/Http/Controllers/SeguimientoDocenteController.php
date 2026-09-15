<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\ClaseMateria;
use App\Models\Materia;
use App\Models\Semestre;
use App\Models\Carrera;
use App\Models\Malla;
use App\Models\SemestreMalla;
use App\Models\SemestreMallaMateria;
use App\Models\Modalidad;
use App\Models\AlumnoAsistencia;
use App\Models\Docente;
use App\Models\ActaEvaluacion;
use App\Models\Matriculacion;
use App\Models\Inscripcion;
use App\Models\DiaSemana;
use App\Models\Programa;
use App\Models\Empresa;


class SeguimientoDocenteController extends Controller
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

    public function index(Request $request)
    {
        $this->authorize('ver_materias_clases');

        try {
            $semestre = Semestre::where('estado', 'AC')->orderBy('id', 'desc')->first();

            $filtro_mes = $request->has('filtro_mes') ? $request->filtro_mes : Carbon::now()->month;
            $filtro_periodo = $request->has('filtro_periodo') ? $request->filtro_periodo : $semestre->id;
            $filtro_programa = $request->has('filtro_programa') ? $request->filtro_programa : 'ninguno';

            $dias_semana = DiaSemana::whereBetween('id', [2, 6])->get();

            $semestre = Semestre::where('id', $filtro_periodo)->first();
            $partes = preg_split('/[-.]/', $semestre->nombre);
            $anho = $partes[0];

            $primer_dia_mes = Carbon::createFromDate($anho, $filtro_mes, 1);

            $fechas = [
                '2' => [],
                '3' => [],
                '4' => [],
                '5' => [],
                '6' => []
            ];

            $primer_dia_mes = $primer_dia_mes->copy();

            while ($primer_dia_mes->month ==  $filtro_mes) {
                switch ($primer_dia_mes->dayOfWeek) {
                    case Carbon::MONDAY:
                        $fechas['2'][] = $primer_dia_mes->day;
                        break;
                    case Carbon::TUESDAY:
                        $fechas['3'][] = $primer_dia_mes->day;
                        break;
                    case Carbon::WEDNESDAY:
                        $fechas['4'][] = $primer_dia_mes->day;
                        break;
                    case Carbon::THURSDAY:
                        $fechas['5'][] = $primer_dia_mes->day;
                        break;
                    case Carbon::FRIDAY:
                        $fechas['6'][] = $primer_dia_mes->day;
                        break;
                }

                $primer_dia_mes->addDay();
            }

            if ($filtro_programa == 'ninguno') {
                $programa_filtro = null;
            } else {
                $programa_filtro = $filtro_programa;
            }

            $semestre_materias = SemestreMallaMateria::whereHas('semestreMalla', function ($query) use ($semestre) {
                $query->where('semestre_id', $semestre->id);
            })
            ->where('semestres_mallas_materias.estado', 'AC')
            ->when($programa_filtro, function ($query) use ($programa_filtro) {
                return $query->where('carreras.programa_id', $programa_filtro); // Solo aplica el filtro si existe un programa_id
            })
            ->join('materias', 'semestres_mallas_materias.materia_id', '=', 'materias.id')
            ->join('semestres_mallas', 'semestres_mallas.id', '=', 'semestres_mallas_materias.semestre_malla_id')
            ->join('mallas', 'mallas.id', '=', 'semestres_mallas.malla_id')
            ->join('carreras', 'carreras.id', '=', 'mallas.carrera_id')
            ->leftJoin('semestres_mallas_materias_horarios', 'semestres_mallas_materias_horarios.semestre_malla_materia_id', '=', 'semestres_mallas_materias.id')
            ->leftJoin('docentes', 'docentes.id', '=', 'semestres_mallas_materias.docente_id')
            ->select(
                'materias.id as materia_id',
                'materias.nombre_real as materia',
                'semestres_mallas_materias.id',
                'carreras.abreviatura as carrera',
                'docentes.id as docente_id',
                \DB::raw('
                    CONCAT(
                        docentes.primer_nombre, \' \',
                        docentes.segundo_nombre, \' \',
                        docentes.tercer_nombre, \' \',
                        docentes.primer_apellido, \' \',
                        docentes.segundo_apellido
                    ) as docente
                '),
                'semestres_mallas_materias_horarios.dia_semana_id as dia_semana_id',
                'semestres_mallas_materias_horarios.hora_inicio as hora_inicio',
                'semestres_mallas_materias_horarios.hora_fin as hora_fin',
                'semestres_mallas_materias.aula as aula'
            )
            ->orderBy('materias.nombre_real')
            ->get();

            $meses = range(1, 12);
            $semestres = Semestre::orderBy('id', 'desc')->get();
            $programas = Programa::where('estado', 'AC')->whereIn('id', [1, 2, 3, 8])->orderBy('nombre', 'asc')->get();
        
            $clases_materias = ClaseMateria::whereMonth('fecha_hora', $filtro_mes)->where('semestre_id', $semestre->id)->get();

            return view('seguimiento_docente/index')->with(compact('dias_semana', 'fechas', 'semestre_materias', 'clases_materias', 'meses', 'semestres', 'programas', 'filtro_mes', 'filtro_periodo', 'filtro_programa'));
        } catch (\Exception $e) {
            return redirect()->route('seguimiento_docente.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_materias_clases');

        try {
            $clase = ClaseMateria::with('alumnoAsistencias')->findOrFail($id);

            return view('seguimiento_docente/show')->with(compact('clase'));
        } catch (\Exception $e) {
            return redirect()->route('seguimiento_docente.index')->with('error-message', $e->getMessage());
        }
    }

    public function create_materia($materia_id, $semestre_id, $carrera_id)
    {
        $this->authorize('crear_clases_materias_semestres');

        try {
            if (ActaEvaluacion::where('materia_id', $materia_id)->where('semestre_id', $semestre_id)->where('carrera_id', $carrera_id)->exists()) {
                return redirect()->route('seguimiento_docente.index')->with('error-message', 'No se puede generar una clase, ya existe un acta de evaluación generado.');
            }

            $materia = Materia::findOrFail($materia_id);
            $semestre = Semestre::findOrFail($semestre_id);
            $carrera = collect();
            $modalidades = Modalidad::where('estado', 'AC')->get();

            $malla = Malla::where('carrera_id', $carrera_id)->first();
            $semestre_malla = SemestreMalla::where('semestre_id', $semestre_id)->where('malla_id', $malla->id)->first();
            $semestre_malla_materia = SemestreMallaMateria::with('semestreMalla.malla.mallaDetalles')->where('materia_id', $materia_id)->where('semestre_malla_id', $semestre_malla->id)->first();
            foreach ($semestre_malla_materia->semestreMalla->malla->mallaDetalles as $detalle) {
                if ($detalle->materia_id == $materia->id) {
                    $semestre_materia = $detalle->semestre;
                }
            }
            $docente = $semestre_malla_materia->docente;
            if (!$docente) {
                return redirect()->route('seguimiento_docente.index')->with('error-message', 'No se puede generar una clase sin seleccionar previamente un docente para la materia.');
            }

            $clase = ClaseMateria::where('materia_id', $materia_id)->where('semestre_id', $semestre_id)->orderBy('id', 'desc')->first();
            if ($clase) {
                if ($clase->alumnoAsistencias->count() == 0) {
                    return back()->with('error-message', 'No se puede crear una clase, debe completar la asistencia de la anterior para continuar.');
                }
            }

            return view('seguimiento_docente/create')->with(compact('materia', 'semestre', 'carrera', 'modalidades', 'docente'));
        } catch (\Exception $e) {
            return redirect()->route('seguimiento_docente.index')->with('error-message', $e->getMessage());
        }
    }

    public function create_carrera($materia_id, $semestre_id, $carrera_id)
    {
        $this->authorize('crear_clases_materias_semestres');

        try {
            if (ActaEvaluacion::where('materia_id', $materia_id)->where('semestre_id', $semestre_id)->where('carrera_id', $carrera_id)->exists()) {
                return redirect()->route('seguimiento_docente.index')->with('error-message', 'No se puede generar una clase, ya existe un acta de evaluación generado.');
            }

            $materia = Materia::findOrFail($materia_id);
            $semestre = Semestre::findOrFail($semestre_id);
            $carrera = Carrera::findOrFail($carrera_id);
            $modalidades = Modalidad::where('estado', 'AC')->get();

            $malla = Malla::where('carrera_id', $carrera_id)->first();
            $semestre_malla = SemestreMalla::where('semestre_id', $semestre_id)->where('malla_id', $malla->id)->first();
            $semestre_malla_materia = SemestreMallaMateria::with('semestreMalla.malla.mallaDetalles')->where('materia_id', $materia_id)->where('semestre_malla_id', $semestre_malla->id)->first();
            foreach ($semestre_malla_materia->semestreMalla->malla->mallaDetalles as $detalle) {
                if ($detalle->materia_id == $materia->id) {
                    $semestre_materia = $detalle->semestre;
                }
            }
            $docente = $semestre_malla_materia->docente;
            if (!$docente) {
                return redirect()->route('seguimiento_docente.index')->with('error-message', 'No se puede generar una clase sin seleccionar previamente un docente para la materia.');
            }

            $clase = ClaseMateria::where('materia_id', $materia_id)->where('semestre_id', $semestre_id)->orderBy('id', 'desc')->first();
            if ($clase) {
                if ($clase->alumnoAsistencias->count() == 0) {
                    return back()->with('error-message', 'No se puede crear una clase, debe completar la asistencia de la anterior para continuar.');
                }
            }

            return view('seguimiento_docente/create')->with(compact('materia', 'semestre', 'carrera' ,'modalidades', 'docente'));
        } catch (\Exception $e) {
            return redirect()->route('seguimiento_docente.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_clases_materias_semestres');

        $request->validate([
            'fecha_hora' => ['required', 'date'],
            'tema_desarrollado' => 'required',
            'horas_desarrollo' => ['required', 'numeric'],
            'modalidad' => ['required', 'numeric'],
            'observaciones' => 'nullable'
        ]);

        DB::beginTransaction();

        try {
            $clase_materia = new ClaseMateria();
            $clase_materia->docente_id = $request->docente;
            if ($request->carrera) {
                $clase_materia->carrera_id = $request->carrera;
            }
            $clase_materia->materia_id = $request->materia;
            $clase_materia->semestre_id = $request->semestre;
            $clase_materia->fecha_hora = $request->fecha_hora;
            $clase_materia->tema_desarrollado = removeAccents(Str::upper($request->tema_desarrollado));
            $clase_materia->horas_desarrollo = $request->horas_desarrollo;
            $clase_materia->modalidad_id = $request->modalidad;
            $clase_materia->observaciones = $request->observaciones;
            $clase_materia->save();

            $matriculaciones = Matriculacion::where('semestre_id', $clase_materia->semestre_id)->get();
            foreach ($matriculaciones as $matriculacion) {
                $inscripciones = Inscripcion::where('matriculacion_id', $matriculacion->id)->where('materia_id', $clase_materia->materia_id)->get();
                foreach ($inscripciones as $inscripcion) {
                    if ($inscripcion->estado == 'MA') {
                        $inscripcion->estado = 'EC';
                        $inscripcion->save();
                    }
                }
            }

            DB::commit();

            if ($request->generado_docente == 'SI') {
                return redirect()->route('pantallas_docentes.index', Auth::id())->with('success-message', 'La clase de la materia ' . $clase_materia->materia->nombre_fantasia . ' fue creada existosamente.');
            } else {
                return redirect()->route('seguimiento_docente.index')->with('success-message', 'La clase de la materia ' . $clase_materia->materia->nombre_fantasia . ' fue creada existosamente.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('seguimiento_docente.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_materias_clases');

        DB::beginTransaction();

        try {
            $clase = ClaseMateria::findOrFail($id);
            if ($clase->semestre->estado == 'IN') {
                return back()->with('error-message', 'La clase no se puede eliminar. El semestre se encuentra cerrado.');
            }
            $asistencia = AlumnoAsistencia::where('clase_materia_id', $clase->id)->delete();
            $clase->delete();

            DB::commit();

            return redirect()->route('seguimiento_docente.index')->with('success-message', 'La clase y asistencia de la materia ' . $clase->materia->nombre_fantasia . ' fue eliminada existosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('seguimiento_docente.index')->with('error-message', 'La clase de la materia ' . $clase->materia->nombre_fantasia . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('seguimiento_docente.index')->with('error-message', $e->getMessage());
            }
        }
    }

    public function generate_pdf($filtro_mes, $filtro_periodo, $filtro_programa)
    {
        $this->authorize('ver_materias_clases');

        DB::beginTransaction();

        try {
            $empresa = Empresa::first();
            $titulo = 'SEGUIMIENTO DE DOCENTES';
            $fecha_hoy = Carbon::now();

            $semestre = Semestre::where('estado', 'AC')->orderBy('id', 'desc')->first();

            $dias_semana = DiaSemana::whereBetween('id', [2, 6])->get();

            $semestre = Semestre::where('id', $filtro_periodo)->first();
            $partes = preg_split('/[-.]/', $semestre->nombre);
            $anho = $partes[0];
            $mes = Str::upper(Carbon::now()->month($filtro_mes)->translatedFormat('F'));

            $primer_dia_mes = Carbon::createFromDate($anho, $filtro_mes, 1);

            $fechas = [
                '2' => [],
                '3' => [],
                '4' => [],
                '5' => [],
                '6' => []
            ];

            $primer_dia_mes = $primer_dia_mes->copy();

            while ($primer_dia_mes->month ==  $filtro_mes) {
                switch ($primer_dia_mes->dayOfWeek) {
                    case Carbon::MONDAY:
                        $fechas['2'][] = $primer_dia_mes->day;
                        break;
                    case Carbon::TUESDAY:
                        $fechas['3'][] = $primer_dia_mes->day;
                        break;
                    case Carbon::WEDNESDAY:
                        $fechas['4'][] = $primer_dia_mes->day;
                        break;
                    case Carbon::THURSDAY:
                        $fechas['5'][] = $primer_dia_mes->day;
                        break;
                    case Carbon::FRIDAY:
                        $fechas['6'][] = $primer_dia_mes->day;
                        break;
                }

                $primer_dia_mes->addDay();
            }

            if ($filtro_programa == 'ninguno') {
                $programa_filtro = null;
            } else {
                $programa_filtro = $filtro_programa;
            }

            $semestre_materias = SemestreMallaMateria::whereHas('semestreMalla', function ($query) use ($semestre) {
                $query->where('semestre_id', $semestre->id);
            })
            ->where('semestres_mallas_materias.estado', 'AC')
            ->when($programa_filtro, function ($query) use ($programa_filtro) {
                return $query->where('carreras.programa_id', $programa_filtro); // Solo aplica el filtro si existe un programa_id
            })
            ->join('materias', 'semestres_mallas_materias.materia_id', '=', 'materias.id')
            ->join('semestres_mallas', 'semestres_mallas.id', '=', 'semestres_mallas_materias.semestre_malla_id')
            ->join('mallas', 'mallas.id', '=', 'semestres_mallas.malla_id')
            ->join('carreras', 'carreras.id', '=', 'mallas.carrera_id')
            ->leftJoin('semestres_mallas_materias_horarios', 'semestres_mallas_materias_horarios.semestre_malla_materia_id', '=', 'semestres_mallas_materias.id')
            ->leftJoin('docentes', 'docentes.id', '=', 'semestres_mallas_materias.docente_id')
            ->select(
                'materias.id as materia_id',
                'materias.nombre_real as materia',
                'semestres_mallas_materias.id',
                'carreras.abreviatura as carrera',
                'docentes.id as docente_id',
                \DB::raw('
                    CONCAT(
                        docentes.primer_nombre, \' \',
                        docentes.segundo_nombre, \' \',
                        docentes.tercer_nombre, \' \',
                        docentes.primer_apellido, \' \',
                        docentes.segundo_apellido
                    ) as docente
                '),
                'semestres_mallas_materias_horarios.dia_semana_id as dia_semana_id',
                'semestres_mallas_materias_horarios.hora_inicio as hora_inicio',
                'semestres_mallas_materias_horarios.hora_fin as hora_fin',
                'semestres_mallas_materias.aula as aula'
            )
            ->orderBy('materias.nombre_real')
            ->get();
        
            $clases_materias = ClaseMateria::whereMonth('fecha_hora', $filtro_mes)->where('semestre_id', $semestre->id)->get();

            $filtro_periodo = $semestre->nombre;

            if ($programa_filtro) {
                $filtro_programa = Programa::findOrFail($programa_filtro)->nombre;
            }

            $pdf = Pdf::loadView('seguimiento_docente/pdf', compact('empresa', 'titulo', 'fecha_hoy', 'dias_semana', 'fechas', 'mes', 'semestre_materias', 'clases_materias', 'filtro_mes', 'filtro_periodo', 'filtro_programa'));
            $pdf->setPaper('A4', 'landscape');

            return $pdf->stream('seguimiento_docente_' . Carbon::now()->format('d_m_Y_H_i_s') . '.pdf');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('seguimiento_docente.index')->with('error-message', $e->getMessage());
        }
    }
}
