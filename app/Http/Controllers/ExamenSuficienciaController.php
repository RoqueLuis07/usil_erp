<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\AutoEncoder;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\ExamenSuficiencia;
use App\Models\Solicitud;
use App\Models\PagoSolicitud;
use App\Models\Alumno;
use App\Models\Materia;
use App\Models\Carrera;
use App\Models\Docente;
use App\Models\Semestre;
use App\Models\Modalidad;
use App\Models\Matriculacion;
use App\Models\Inscripcion;
use App\Models\AlumnoNota;
use App\Models\Escala;
use App\Models\EscalaDetalle;
use App\Models\ExamenSuficienciaActaEvaluacion;
use App\Models\ExamenSuficienciaActaEvaluacionAlumno;
use App\Models\Empresa;
use App\Models\MateriaSuficiencia;
use App\Models\Malla;

class ExamenSuficienciaController extends Controller
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
        $this->authorize('ver_examenes_suficiencia');

        try {
            $examenes = ExamenSuficiencia::orderBy('created_at', 'asc')->get();

            return view('examenes_suficiencia/index')->with(compact('examenes'));
        } catch (\Exception $e) {
            return redirect()->route('examenes_suficiencia.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_examenes_suficiencia');

        try {
            $examen = ExamenSuficiencia::findOrFail($id);

            $matriculacion = Matriculacion::where('alumno_id', $examen->alumno_id)->where('semestre_id', $examen->semestre_id)->where('estado', 'AC')->first();
            if ($matriculacion) {
                $escala = Escala::with('escalaDetalles')->where('programa_id', $matriculacion->programa_id)->first();
            } else {
                $escala = Escala::with('escalaDetalles')->where('programa_id', $examen->carrera->programa_id)->first();
            }

            return view('examenes_suficiencia/show')->with(compact('examen', 'escala'));
        } catch (\Exception $e) {
            return redirect()->route('examenes_suficiencia.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_examenes_suficiencia');

        try {
            $docentes = Docente::where('estado', 'AC')->get();
            $alumnos = Alumno::where('estado', 'AC')->get();
            $modalidades = Modalidad::where('estado', 'AC')->get();
            $materias = MateriaSuficiencia::where('estado', 'AC')->get();

            return view('examenes_suficiencia/create')->with(compact('docentes', 'alumnos', 'modalidades', 'materias'));
        } catch (\Exception $e) {
            return redirect()->route('examenes_suficiencia.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_examenes_suficiencia');

        $request->validate([
            'alumno' => ['required', 'numeric'],
            'alumno_documento' => 'nullable',
            'materia' => ['required', 'numeric'],
            'docente' => ['required', 'numeric'],
            'docente_documento' => 'nullable',
            'modalidad' => ['required', 'numeric'],
            'fecha_examen' => ['required', 'date', 'after_or_equal:today'],
            'aula_examen' => 'required',
        ]);

        DB::beginTransaction();

        try {
            $old_examen = ExamenSuficiencia::where('alumno_id', $request->alumno)->where('materia_id', $request->materia)->first();
            if ($old_examen) {
                return back()->with('error-message', 'El examen de suficiencia no puede ser creado. El alumno ya tomó un examen de la materia seleccionada.');
            }

            $hoy = Carbon::today();
            $semestre = Semestre::whereDate('fecha_inicio', '<=', $hoy)
                            ->whereDate('fecha_fin', '>=', $hoy)
                            ->where('estado', 'AC')
                            ->orderBy('id', 'asc')
                            ->first();
            $periodo_activo = $semestre ? $semestre->id : null;
            $matriculacion = Matriculacion::where('alumno_id', $request->alumno)->where('semestre_id', $periodo_activo->id)->where('estado', 'AC')->first();

            $solicitud = new Solicitud();
            $solicitud->fecha_solicitud = Carbon::now();
            $solicitud->alumno_id = $request->alumno;
            $solicitud->tipo_solicitud_id = 2;
            $solicitud->materia_id = $request->materia;
            $solicitud->semestre_id = $periodo_activo->id;
            $solicitud->programa_id = $matriculacion->carrera->programa_id;
            $solicitud->fecha_aprobacion = Carbon::now();
            $solicitud->estado = 'AP';
            $solicitud->aprobado_por_id = Auth::id();
            $solicitud->modalidad_id = $request->modalidad;
            $solicitud->save();

            // $pago = new PagoSolicitud();
            // $pago->solicitud_id = $solicitud->id;
            // $pago->descripcion = $solicitud->tipoSolicitud->nombre . ' ' . $solicitud->materia->nombre_real . ' - ' . $solicitud->semestre->nombre;
            // $pago->vencimiento = Carbon::now();
            // $pago->monto = $solicitud->tipoSolicitud->monto;
            // $pago->saldo = $solicitud->tipoSolicitud->monto;
            // $pago->save();

            $examen = new ExamenSuficiencia();
            $examen->solicitud_id = $solicitud->id;
            $examen->alumno_id = $request->alumno;
            $examen->carrera_id = $matriculacion->carrera_id;
            $examen->materia_id = $request->materia;
            $examen->docente_id = $request->docente;
            $examen->semestre_id = $periodo_activo->id;
            $examen->modalidad_id = $request->modalidad;
            $examen->fecha_examen = $request->fecha_examen;
            $examen->aula_examen = $request->aula_examen;
            $examen->cargado_por_id = Auth::id();
            $examen->save();

            DB::commit();

            return redirect()->route('examenes_suficiencia.index')->with('success-message', 'El examen de suficiencia fue creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('examenes_suficiencia.index')->with('error-message', $e->getMessage());
        }
    }

    public function create_puntaje($id)
    {
        $this->authorize('cargar_puntajes_examenes_suficiencia');

        DB::beginTransaction();

        try {
            $acta = ExamenSuficienciaActaEvaluacion::with(['alumnos.alumno' => function ($query) {
                $query->orderBy('primer_apellido');
            }])->findOrFail($id);

            $escala = Escala::with('escalaDetalles')->where('programa_id', $acta->carrera->programa_id)->first();

            return view('examenes_suficiencia/puntajes/create')->with(compact('acta', 'escala'));
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('examenes_suficiencia.show_acta', $id)->with('error-message', $e->getMessage());
        }
    }

    public function store_puntaje(Request $request, $id)
    {
        $this->authorize('cargar_puntajes_examenes_suficiencia');

        $request->validate([
            'detalles' => ['required', 'array'],
            'detalles.*.puntos_obtenidos' => ['required', 'numeric', 'min:0', 'max:100'],
            'detalles.*.calificacion' => 'required'
        ]);

        DB::beginTransaction();

        try {
            $acta = ExamenSuficienciaActaEvaluacion::with('alumnos')->findOrFail($id);
            $escala = Escala::where('programa_id', $acta->carrera->programa_id)->where('estado', 'AC')->first();;

            foreach ($acta->alumnos as $acta_alumno) {
                foreach ($request->detalles as $detalle) {
                    if ($detalle['alumno'] == $acta_alumno->alumno_id) {
                        $acta_alumno->puntos_examen = $detalle['puntos_obtenidos'];

                        $nota = EscalaDetalle::where('escala_id', $escala->id)->where('punto_minimo', '<=', $acta_alumno->puntos_examen)->where('punto_maximo', '>=', $acta_alumno->puntos_examen)->first()->nota;

                        $acta_alumno->calificacion = $nota;
                        $acta_alumno->save();

                        $examen = ExamenSuficiencia::where('materia_id', $acta->materia_id)->where('carrera_id', $acta->carrera_id)->where('alumno_id', $acta_alumno->alumno_id)->where('estado', 'GE')->first();
                        $examen->puntos_obtenidos = $acta_alumno->puntos_examen;
                        $examen->calificacion = $nota;

                        if ($nota > 1) {
                            $examen->estado = 'AP';

                            $alumno_nota = new AlumnoNota();
                            $alumno_nota->alumno_id = $acta_alumno->alumno_id;
                            $alumno_nota->carrera_id = $acta->carrera_id;
                            $alumno_nota->materia_id = $acta->materia_id;
                            $alumno_nota->semestre_id = $acta->semestre_id;
                            $alumno_nota->evaluacion = 'SUFICIENCIA';
                            $alumno_nota->calificacion = $nota;
                            $alumno_nota->save();

                            $inscripcion = Inscripcion::where('materia_id', $acta->materia_id)->where('alumno_id', $acta_alumno->alumno_id)->first();
                            $inscripcion->estado = 'SU';
                            $inscripcion->save();
                        } else {
                            $examen->estado = 'RE';
                        }
                        $examen->save();
                    }
                }
            }

            DB::commit();

            return redirect()->route('examenes_suficiencia.show_acta', $id)->with('success-message', 'Los puntajes de los exámenes de suficiencia fueron cargados exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('examenes_suficiencia.show_acta', $id)->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_examenes_suficiencia');

        try {
            $examen = ExamenSuficiencia::findOrFail($id);
            if ($examen->semestre->estado == 'IN') {
                return back()->with('error-message', 'El examen de suficiencia no puede ser actualizado. El semestre se encuentra cerrado.');
            }
            $docentes = Docente::where('estado', 'AC')->get();
            $modalidades = Modalidad::where('estado', 'AC')->get();

            return view('examenes_suficiencia/edit')->with(compact('examen', 'docentes', 'modalidades'));
        } catch (\Exception $e) {
            return redirect()->route('examenes_suficiencia.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_examenes_suficiencia');

        $examen = ExamenSuficiencia::findOrFail($id);

        $request->validate([
            'docente' => ['required', 'numeric'],
            'docente_documento' => 'nullable',
            'modalidad' => ['required', 'numeric'],
            'fecha_examen' => ['required', 'date', 'after_or_equal:' . Carbon::parse($examen->fecha_examen)->format('Y-m-d H:i:s')],
            'aula_examen' => 'required',
        ]);

        DB::beginTransaction();

        try {
            $examen = ExamenSuficiencia::findOrFail($id);
            if ($examen->semestre->estado == 'IN') {
                return back()->with('error-message', 'El examen de suficiencia no puede ser actualizado. El semestre se encuentra cerrado.');
            }
            $examen->docente_id = $request->docente;
            $examen->modalidad_id = $request->modalidad;
            $examen->fecha_examen = $request->fecha_examen;
            $examen->aula_examen = $request->aula_examen;
            $examen->actualizado_por_id = Auth::id();
            $examen->save();

            DB::commit();

            return redirect()->route('examenes_suficiencia.index')->with('success-message', 'El examen de suficiencia fue actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('examenes_suficiencia.index')->with('error-message', $e->getMessage());
        }
    }

    public function update_puntaje(Request $request, $id)
    {
        $this->authorize('editar_puntaje_examenes_suficiencia');

        $request->validate([
            'puntos_obtenidos' => ['required', 'numeric', 'min:0', 'max:100'],
            'calificacion' => 'required'
        ]);

        DB::beginTransaction();

        try {
            $acta_alumno = ExamenSuficienciaActaEvaluacionAlumno::findOrFail($id);
            $acta = ExamenSuficienciaActaEvaluacion::findOrFail($acta_alumno->acta_evaluacion_id);
            $escala = Escala::where('programa_id', $acta->carrera->programa_id)->where('estado', 'AC')->first();
            $nota = EscalaDetalle::where('escala_id', $escala->id)->where('punto_minimo', '<=', $request->puntos_obtenidos)->where('punto_maximo', '>=', $request->puntos_obtenidos)->first()->nota;

            $acta_alumno->puntos_examen = $request->puntos_obtenidos;
            $acta_alumno->calificacion = $nota;
            $acta_alumno->save();

            $examen = ExamenSuficiencia::where('materia_id', $acta->materia_id)->where('carrera_id', $acta->carrera_id)->where('alumno_id', $acta_alumno->alumno_id)->whereIn('estado', ['AP', 'RE'])->first();
            if ($examen->semestre->estado == 'IN') {
                return back()->with('error-message', 'El puntaje del alumno en el examen de suficiencia no puede ser actualizado. El semestre se encuentra cerrado.');
            }
            $alumno_nota = AlumnoNota::where('alumno_id', $acta_alumno->alumno_id)->where('carrera_id', $acta->carrera_id)->where('materia_id', $acta->materia_id)->where('semestre_id', $acta->semestre_id)->where('evaluacion', 'SUFICIENCIA')->first();
            $inscripcion = Inscripcion::where('materia_id', $acta->materia_id)->where('alumno_id', $acta_alumno->alumno_id)->first();

            $examen->puntos_obtenidos = $acta_alumno->puntos_examen;
            $examen->calificacion = $nota;

            if ($nota > 1) {
                $examen->estado = 'AP';

                $alumno_nota = AlumnoNota::where('alumno_id', $acta_alumno->alumno_id)->where('carrera_id', $acta->carrera_id)->where('materia_id', $acta->materia_id)->where('semestre_id', $acta->semestre_id)->where('evaluacion', 'SUFICIENCIA')->first();
                if ($alumno_nota) {
                    $alumno_nota->calificacion = $nota;
                } else {
                    $alumno_nota = new AlumnoNota();
                    $alumno_nota->alumno_id = $acta_alumno->alumno_id;
                    $alumno_nota->carrera_id = $acta->carrera_id;
                    $alumno_nota->materia_id = $acta->materia_id;
                    $alumno_nota->semestre_id = $acta->semestre_id;
                    $alumno_nota->evaluacion = 'SUFICIENCIA';
                    $alumno_nota->calificacion = $nota;
                }
                $alumno_nota->save();

                $inscripcion->estado = 'SU';
            } else {
                $examen->estado = 'RE';

                if ($alumno_nota) {
                    $alumno_nota->delete();
                }

                if ($inscripcion) {
                    $inscripcion->estado = 'MA';
                }
            }
            $inscripcion->save();
            $examen->save();

            DB::commit();

            return response()->json([
                'message' => 'El puntaje obtenido por el alumno ' . $acta_alumno->alumno->primer_nombre . ' ' . $acta_alumno->alumno->primer_apellido . ' fue actualizado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('examenes_suficiencia.show_acta', $id)->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_examenes_suficiencia');

        DB::beginTransaction();

        try {
            $examen = ExamenSuficiencia::findOrFail($id);
            if ($examen->semestre->estado == 'IN') {
                return back()->with('error-message', 'El examen de suficiencia no puede ser eliminado. El semestre se encuentra cerrado.');
            }

            if ($examen->estado == 'AC') {
                $examen->solicitud->pagoSolicitud->delete();
                $examen->delete();

                $solicitud = Solicitud::findOrFail($examen->solicitud_id);
                $solicitud->estado = 'RE';
                $solicitud->save();

                DB::commit();

                return redirect()->route('examenes_suficiencia.index')->with('success-message', 'El examen de suficiencia de la materia ' . $examen->materia->nombre_fantasia . ' del alumno ' . $examen->alumno->primer_nombre . ' ' . $examen->alumno->primer_apellido . ' fue eliminada exitosamente. La solicitud fue rechazada automáticamente.');
            } else {
                switch ($examen->estado) {
                    case 'AP':
                        $estado = 'aprobado';
                        break;
                    case 'GE':
                        $estado = 'con acta de evaluación generado';
                        break;
                    case 'RE':
                        $estado = 'reprobado';
                        break;
                    default:
                        break;
                }

                return redirect()->route('examenes_suficiencia.index')->with('error-message', 'El examen de suficiencia de la materia ' . $examen->materia->nombre_fantasia . ' del alumno ' . $examen->alumno->primer_nombre . ' ' . $examen->alumno->primer_apellido . ' no se puede eliminar porque ya se encuentra ' . $estado . '.');
            }
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('examenes_suficiencia.index')->with('error-message', 'El examen de suficiencia de la materia ' . $examen->materia->nombre_fantasia . ' del alumno ' . $examen->alumno->primer_nombre . ' ' . $examen->alumno->primer_apellido . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('examenes_suficiencia.index')->with('error-message', $e->getMessage());
            }
        }
    }

    public function get_materias($id)
    {
        $this->authorize('crear_examenes_suficiencia');

        try {
            $hoy = Carbon::today();
            $semestre = Semestre::whereDate('fecha_inicio', '<=', $hoy)
                            ->whereDate('fecha_fin', '>=', $hoy)
                            ->where('estado', 'AC')
                            ->orderBy('id', 'asc')
                            ->first();
            $matriculacion = Matriculacion::where('alumno_id', $id)->where('semestre_id', $semestre->id)->orderBy('id', 'desc')->first();
            if ($matriculacion) {
                $malla = Malla::with('mallaDetalles')->where('carrera_id', $matriculacion->carrera_id)->first();
                $suficiencias = MateriaSuficiencia::with('materia')->where('estado', 'AC')->get();
                $materias = collect();
                foreach ($suficiencias as $materia) {
                    foreach ($malla->mallaDetalles as $detalle) {
                        if ($materia->materia_id == $detalle->materia_id) {
                            $materias->push($materia);
                        }
                    }
                }

                return response()->json([
                    'materias' => $materias,
                ]);
            } else {
                return response()->json([
                    'message' => 'El alumno no cuenta con una matriculacion en el período actual.',
                ]);
            }
        } catch (\Exception $e) {
            DB::rollback();
            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('examenes_suficiencia.index')->with('error-message', 'El examen de suficiencia de la materia ' . $examen->materia->nombre_fantasia . ' del alumno ' . $examen->alumno->primer_nombre . ' ' . $examen->alumno->primer_apellido . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('examenes_suficiencia.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
