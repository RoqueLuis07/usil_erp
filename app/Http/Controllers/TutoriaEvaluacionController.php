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

use App\Models\TutoriaEvaluacion;
use App\Models\Tutoria;
use App\Models\TutoriaAlumno;
use App\Models\TutoriaActaEvaluacion;
use App\Models\TutoriaActaEvaluacionAlumno;
use App\Models\Evaluacion;
use App\Models\Escala;
use App\Models\AlumnoNota;
use App\Models\Matriculacion;
use App\Models\Inscripcion;
use App\Models\Empresa;

class TutoriaEvaluacionController extends Controller
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
        $this->authorize('ver_evaluaciones_tutorias');

        try {
            $evaluacion = TutoriaEvaluacion::where('tutoria_id', $id)->first();
            if ($evaluacion) {
                return view('tutorias/evaluaciones/show')->with(compact('evaluacion'));
            } else {
                return redirect()->route('tutorias_evaluaciones.create', $id);
            }
        } catch (\Exception $e) {
            return redirect()->route('tutorias.index')->with('error-message', $e->getMessage());
        }
    }

    public function create($id)
    {
        $this->authorize('crear_evaluaciones_tutorias');

        try {
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
                return back()->with('error-message', 'Debe cargar todos los datos de la tutoría para poder generar clases.');
            }

            $evaluacion = Evaluacion::findOrFail(4);

            if ($tutoria->alumnos->count() == 0) {
                return back()->with('error-message', 'No se pueden cargar evaluaciones en una tutoría sin alumnos inscriptos.');
            }

            return view('tutorias/evaluaciones/create')->with(compact('tutoria', 'evaluacion'));
        } catch (\Exception $e) {
            return redirect()->route('tutorias.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request, $id)
    {
        $this->authorize('crear_evaluaciones_tutorias');

        $request->validate([
            'evaluacion' => ['required', 'numeric'],

            'detalles' => ['required', 'array'],
            'detalles.*.alumno' => ['required', 'numeric'],
            'detalles.*.observacion' => 'nullable',
            'detalles.*.puntaje_obtenido' => ['nullable', 'required_without:detalles.*.observacion']
        ]);

        DB::beginTransaction();

        try {
            $tutoria = Tutoria::findOrFail($id);
            if ($tutoria->semestre->estado == 'IN') {
                return back()->with('error-message', 'El puntaje del alumno en la tutoria no puede cargarse. El semestre se encuentra cerrado.');
            }

            foreach ($request->detalles as $detalle) {
                $tutoria_evaluacion = new TutoriaEvaluacion();
                $tutoria_evaluacion->tutoria_id = $tutoria->id;
                $tutoria_evaluacion->alumno_id = $detalle['alumno'];
                $tutoria_evaluacion->evaluacion_id = $request->evaluacion;
                $tutoria_evaluacion->puntos_obtenidos = $detalle['puntaje_obtenido'];
                $tutoria_evaluacion->observacion = removeAccents(Str::upper($detalle['observacion']));
                $tutoria_evaluacion->cargado_por_id = Auth::id();
                $tutoria_evaluacion->save();

                $tutoria_alumno = TutoriaAlumno::where('tutoria_id', $tutoria->id)->where('alumno_id', $tutoria_evaluacion->alumno_id)->first();
                if (!$tutoria_evaluacion->observacion) {
                    $escala = Escala::with('escalaDetalles')->where('programa_id', $tutoria_alumno->carrera->programa_id)->first();
                    foreach ($escala->escalaDetalles as $escala_detalle) {
                        if ($tutoria_evaluacion->puntos_obtenidos >= $escala_detalle->punto_minimo && $tutoria_evaluacion->puntos_obtenidos <= $escala_detalle->punto_maximo) {
                            $nota = $escala_detalle->nota;
                        }
                    }

                    $tutoria_alumno->calificacion = $nota;
                    $tutoria_alumno->puntos_obtenidos = $tutoria_evaluacion->puntos_obtenidos;
                    if ($tutoria_alumno->calificacion > 1) {
                        $tutoria_alumno->estado = 'AP';
                    } else {
                        $tutoria_alumno->estado = 'RE';
                    }
                } else {
                    $tutoria_alumno->calificacion = 1;
                }
                $tutoria_alumno->save();

                $alumno_nota = new AlumnoNota();
                $alumno_nota->alumno_id = $tutoria_evaluacion->alumno_id;
                $alumno_nota->carrera_id = $tutoria_alumno->carrera_id;
                $alumno_nota->materia_id = $tutoria->materia_id;
                $alumno_nota->semestre_id = $tutoria->semestre_id;
                $alumno_nota->evaluacion = $tutoria_evaluacion->evaluacion->nombre;
                $alumno_nota->calificacion = $tutoria_alumno->calificacion;
                $alumno_nota->save();

                $matriculacion = Matriculacion::with('inscripciones')->where('alumno_id', $tutoria_evaluacion->alumno_id)->where('semestre_id', $tutoria->semestre_id)->first();
                foreach ($matriculacion->inscripciones as $inscripcion) {
                    if ($inscripcion->materia_id == $tutoria->materia_id) {
                        $inscripcion->estado = $tutoria_alumno->estado;
                        $inscripcion->save();
                    } else {
                        $new_inscripcion = new Inscripcion();
                        $new_inscripcion->fecha = $tutoria->created_at;
                        $new_inscripcion->matriculacion_id = $matriculacion->id;
                        $new_inscripcion->materia_id = $tutoria->materia_id;
                        $new_inscripcion->alumno_id = $tutoria_evaluacion->alumno_id;
                        $new_inscripcion->docente_id = $tutoria->docente_id;
                        $new_inscripcion->estado = $tutoria_alumno->estado;
                        $new_inscripcion->cargado_por_id = Auth::id();
                        $new_inscripcion->save();
                    }
                }

                $acta = TutoriaActaEvaluacion::with('alumnos')->where('tutoria_id', $tutoria->id)->first();
                foreach ($acta->alumnos as $acta_detalle) {
                    if ($acta_detalle->alumno_id = $tutoria_evaluacion->alumno_id) {
                        $acta_detalle->puntos_examen = $tutoria_evaluacion->puntos_obtenidos;
                        $acta_detalle->calificacion = $tutoria_alumno->calificacion;
                        $acta_detalle->save();
                    }
                }
            }

            $tutoria->estado = 'FI';
            $tutoria->save();

            DB::commit();

            if ($request->generado_docente == 'SI') {
                return redirect()->route('tutorias_docentes.index', Auth::id())->with('success-message', 'La evaluación de tutoría de la materia ' . $tutoria_evaluacion->tutoria->materia->nombre_fantasia . ' fue creada existosamente.');
            } else {
                return redirect()->route('tutorias.index')->with('success-message', 'La evaluación de tutoría de la materia ' . $tutoria_evaluacion->tutoria->materia->nombre_fantasia . ' fue creada existosamente.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tutorias.index')->with('error-message', $e->getMessage());
        }
    }

    public function show_acta($id)
    {
        $this->authorize('ver_actas_tutorias');

        DB::beginTransaction();

        try {
            $tutoria = Tutoria::with(['alumnos' => function ($query) {
                $query->where('estado', 'EC');
            }], 'actas')->findOrFail($id);

            if ($tutoria->estado == 'FI') {
                $tutoria = Tutoria::with(['alumnos' => function ($query) {
                    $query->whereIn('estado', ['RE', 'AP']);
                }], 'actas')->findOrFail($id);
            }

            if (!$tutoria->docente_id || !$tutoria->fecha_inicio || !$tutoria->fecha_fin || !$tutoria->cantidad_clases || !$tutoria->cantidad_horas) {
                return back()->with('error-message', 'Debe cargar todos los datos de la tutoría para poder generar clases.');
            }

            $alumnos = collect();

            foreach ($tutoria->alumnos as $detalle) {
                if ($tutoria->cantidad_clases == $detalle->cantidad_asistencias) {
                    $alumnos->push($detalle);
                }
            }

            return view('tutorias/evaluaciones/actas/show')->with(compact('tutoria', 'alumnos'));

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tutorias_clases.index', $id)->with('error-message', $e->getMessage());
        }
    }

    public function generate_acta($id)
    {
        $this->authorize('generar_actas_tutorias');

        DB::beginTransaction();

        try {
            $tutoria = Tutoria::with(['alumnos' => function ($query) {
                $query->where('estado', 'EC');
            }], 'actas')->findOrFail($id);
            if ($tutoria->semestre->estado == 'IN') {
                return back()->with('error-message', 'El acta de evaluación de la tutoria no puede generarse. El semestre se encuentra cerrado.');
            }

            if (!$tutoria->docente_id || !$tutoria->fecha_inicio || !$tutoria->fecha_fin || !$tutoria->cantidad_clases || !$tutoria->cantidad_horas) {
                return back()->with('error-message', 'Debe cargar todos los datos de la tutoría para poder generar clases.');
            }

            $alumnos = collect();

            foreach ($tutoria->alumnos as $detalle) {
                if ($tutoria->cantidad_clases == $detalle->cantidad_asistencias) {
                    $alumnos->push($detalle);
                }
            }

            $materia = $tutoria->materia;
            $semestre = $tutoria->semestre;

            $evaluacion = Evaluacion::where('tipo_evaluacion_id', 4)->first();
            $fecha_evaluacion = Carbon::now();
            $nombre_examen = 'TUTORÍA';
            $tipo_acta = 'O';
            $oportunidad = 'PRIMERA';
            $seccion = 'M';
            $turno = 'MAÑANA';

            $docente = $tutoria->docente;

            $old_acta = TutoriaActaEvaluacion::orderBy('id', 'desc')->where('numero_acta', 'LIKE', '%' . 'TUT' . '%')->first();
            $anho_actual = Carbon::now()->format('Y');
            if ($old_acta) {
                $numero_acta = substr($old_acta->numero_acta, 5, 191); //el primer numero es cuantos espacios queres eliminar y el segundo numero cuanto queres dejar
                $partes_numero = explode('/', $numero_acta); //separamos el string de numero_dictamen
                if ($anho_actual == $partes_numero[1]) {
                    $numero_nuevo = intval($partes_numero[0]) + 1;
                    $numero = 'TUT' . str_pad($numero_nuevo, 5,'0', STR_PAD_LEFT) . '/' . $partes_numero[1];
                } else {
                    $numero = 'TUT' . str_pad(1, 5, '0', STR_PAD_LEFT) . '/'. $anho_actual;
                }
            } else {
                $numero = 'TUT' . str_pad(1, 5, '0', STR_PAD_LEFT) . '/'. $anho_actual;
            }

            $acta = TutoriaActaEvaluacion::with('alumnos')->where('tutoria_id', $tutoria->id);

            $existe_acta = TutoriaActaEvaluacion::where('tutoria_id', $tutoria->id)->first();

            if ($existe_acta) {
                return redirect()->route('tutorias_evaluaciones.show_acta', $tutoria->id)->with('error-message', 'El acta ya existe, no se puede vovler a generar, aquí te lo mostramos.');
            }

            $acta = new TutoriaActaEvaluacion();
            $acta->numero_acta = $numero;
            $acta->tipo = $tipo_acta;
            $acta->tutoria_id = $tutoria->id;
            $acta->fecha_evaluacion = $fecha_evaluacion;
            $acta->seccion = $seccion;
            $acta->generado_por_id = Auth::id();
            $acta->save();

            foreach ($alumnos as $detalle) {
                $acta_alumnos = new TutoriaActaEvaluacionAlumno();
                $acta_alumnos->acta_evaluacion_id = $acta->id;
                $acta_alumnos->alumno_id = $detalle->alumno_id;
                $acta_alumnos->save();
            }

            DB::commit();

            $empresa = Empresa::first();

            $titulo = 'ACTA DE EVALUACIÓN FINAL - ' . $nombre_examen;

            switch ($docente->nivel_academico_id) {
                case 1:
                    $prenombre_docente = 'LIC.';
                    break;

                case 2:
                    $prenombre_docente = 'MGTR.';
                    break;
                case 3:
                    $prenombre_docente = 'DR.';
                    break;
                case 4:
                    $prenombre_docente = 'PHD.';
                    break;
                default:
                    $prenombre_docente = '';
                    break;
            }

            $fecha_hoy = Carbon::now();
            $pdf = Pdf::loadView('tutorias/evaluaciones/actas/pdf', compact('empresa', 'titulo', 'oportunidad', 'numero', 'materia', 'semestre', 'alumnos', 'fecha_evaluacion', 'seccion', 'turno', 'docente', 'prenombre_docente', 'fecha_hoy'));
            $pdf->setPaper('A4', 'landscape');
            $contenido_pdf = $pdf->output();

            $nombre_archivo = 'acta_' . Str::lower(str_replace('/', '_', $numero)) . '.pdf';
            $contenido_pdf = $pdf->output();
            $ubicacion_archivo = 'public/actas_evaluaciones/tutorias/generados/' . $nombre_archivo;
            Storage::put($ubicacion_archivo, $contenido_pdf);
            $acta->ubicacion_acta = str_replace('public', 'storage', $ubicacion_archivo);
            $acta->save();

            return $pdf->stream('acta_' . Str::lower(str_replace('/', '_', $numero)) . '.pdf');

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tutorias.index')->with('error-message', $e->getMessage());
        }
    }

    public function subir_acta(Request $request, $id)
    {
        $this->authorize('subir_adjunto_actas_tutorias');

        $request->validate([
            'acta' => ['required', 'file', 'extensions:jpg,png,pdf']
        ]);

        DB::beginTransaction();

        try {
            $acta_evaluacion = TutoriaActaEvaluacion::where('tutoria_id', $id)->first();

            //cargar archivo
            $archivo = $request->acta;
            $extension = $archivo->getClientOriginalExtension();
            $directorio = 'storage/actas_evaluaciones/tutorias/firmados';
            if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png') {
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($archivo);
                $image = $image->encode(new AutoEncoder(quality: 50));
                $nombre = 'acta_' . Str::lower(str_replace('/', '_', $acta_evaluacion->numero_acta));
                $nombre_archivo = $nombre . '.' . $extension;
                $image->save($directorio . '/' . $nombre_archivo);
            } else {
                $directorio_storage = 'public/actas_evaluaciones/tutorias/firmados';
                $nombre = 'acta_' . Str::lower(str_replace('/', '_', $acta_evaluacion->numero_acta));
                $nombre_archivo = $nombre . '.' . $extension;
                Storage::putFileAs($directorio_storage . '/', $archivo, $nombre_archivo);
            }
            $acta_evaluacion->ubicacion_adjunto = $directorio . '/' . $nombre_archivo;
            $acta_evaluacion->save();

            DB::commit();

            return response()->json([
                'message' => 'El adjunto del acta N° ' . $acta_evaluacion->numero_acta . ' de la materia ' . $acta_evaluacion->tutoria->materia->nombre_fantasia . ' fue subido exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tutorias_evaluaciones.show_acta', $id)->with('error-message', $e->getMessage());
        }
    }

    public function eliminar_acta($id)
    {
        $this->authorize('eliminar_adjunto_actas_tutorias');

        DB::beginTransaction();

        try {
            $acta_evaluacion = TutoriaActaEvaluacion::where('tutoria_id', $id)->first();

            $ubicacion_archivo = $acta_evaluacion->ubicacion_adjunto;
            $ubicacion_archivo = str_replace('storage', 'public', $ubicacion_archivo);
            Storage::delete($ubicacion_archivo);

            $acta_evaluacion->ubicacion_adjunto = null;
            $acta_evaluacion->save();

            DB::commit();

            return redirect()->route('tutorias_evaluaciones.show_acta', $id)->with('error-message','El adjunto del acta N° ' . $acta_evaluacion->numero_acta . ' de la materia ' . $acta_evaluacion->tutoria->materia->nombre_fantasia . ' fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tutorias_evaluaciones.show_acta', $id)->with('error-message', $e->getMessage());
        }
    }
}
