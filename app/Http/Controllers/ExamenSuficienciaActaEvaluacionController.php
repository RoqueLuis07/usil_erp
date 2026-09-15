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

class ExamenSuficienciaActaEvaluacionController extends Controller
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
        $this->authorize('ver_actas_examenes_suficiencia');

        try {
            $examenes = ExamenSuficiencia::orderBy('created_at', 'asc')->get();
            $actas = ExamenSuficienciaActaEvaluacion::orderBy('created_at', 'asc')->get();
            $materias = collect();
            $carreras = collect();

            foreach ($examenes as $examen) {
                if ($examen->estado == 'AC') {
                    $materias->push($examen->materia);
                    $carreras->push($examen->carrera);
                }
            }

            $materias = $materias->unique();
            $carreras = $carreras->unique();

            return view('examenes_suficiencia/actas/index')->with(compact('actas', 'materias', 'carreras'));
        } catch (\Exception $e) {
            return redirect()->route('examenes_suficiencia.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_actas_examenes_suficiencia');

        DB::beginTransaction();

        try {
            $acta = ExamenSuficienciaActaEvaluacion::with('alumnos')->findOrFail($id);
            $escala = Escala::with('escalaDetalles')->where('programa_id', $acta->carrera->programa_id)->first();

            return view('examenes_suficiencia/actas/show')->with(compact('acta', 'escala'));
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('examenes_suficiencia.index')->with('error-message', $e->getMessage());
        }
    }

    public function generate(Request $request)
    {
        $this->authorize('generar_actas_examenes_suficiencia');

        DB::beginTransaction();

        try {
            $hoy = Carbon::today();
            $semestre = Semestre::whereDate('fecha_inicio', '<=', $hoy)
                            ->whereDate('fecha_fin', '>=', $hoy)
                            ->where('estado', 'AC')
                            ->orderBy('id', 'asc')
                            ->first();
            $periodo_activo = $semestre ? $semestre->id : null;
            $materia = Materia::findOrFail($request->materia);
            $carrera = Carrera::findOrFail($request->carrera);
            $alumnos = collect();

            $examenes = ExamenSuficiencia::where('materia_id', $request->materia)->where('carrera_id', $request->carrera)->where('semestre_id', $periodo_activo)->where('estado', 'AC')->get();
            foreach ($examenes as $key => $examen) {
                if ($key == 0) {
                    $fecha_evaluacion = $examen->fecha_examen;
                    $nombre_examen = 'SUFICIENCIA';
                    $tipo_acta = 'S';
                    $oportunidad = 'PRIMERA';
                    $semestre = $examen->semestre;
                    $docente = $examen->docente;
                }

                if ($examen->solicitud->pagoSolicitud->estado == 'CA') {
                    $alumnos->push($examen->alumno);

                    $examen->estado = 'GE';
                    $examen->save();
                }
            }

            if ($alumnos->count() == 0) {
                return back()->with('error-message', 'No existen alumnos habilitados para generar el acta solicitado.');
            }

            $old_acta = ExamenSuficienciaActaEvaluacion::orderBy('id', 'desc')->first();
            $anho_actual = Carbon::now()->format('Y');
            if ($old_acta) {
                $numero_acta = substr($old_acta->numero_acta, 5, 191); //el primer numero es cuantos espacios queres eliminar y el segundo numero cuanto queres dejar
                $partes_numero = explode('/', $numero_acta); //separamos el string de numero_dictamen
                if ($anho_actual == $partes_numero[1]) {
                    $numero_nuevo = intval($partes_numero[0]) + 1;
                    $numero = 'SUF' . str_pad($numero_nuevo, 5,'0', STR_PAD_LEFT) . '/' . $partes_numero[1];
                } else {
                    $numero = 'SUF' . str_pad(1, 5, '0', STR_PAD_LEFT) . '/'. $anho_actual;
                }
            } else {
                $numero = 'SUF' . str_pad(1, 5, '0', STR_PAD_LEFT) . '/'. $anho_actual;
            }

            $existe_acta = ExamenSuficienciaActaEvaluacion::where('materia_id', $materia->id)->where('carrera_id', $carrera->id)->where('semestre_id', $semestre->id)->first();

            if ($existe_acta) {
                return redirect()->route('examenes_suficiencia_.show_acta', $existe_acta->id)->with('error-message', 'El acta ya existe, no se puede vovler a generar, aquí te lo mostramos.');
            }

            $acta = new ExamenSuficienciaActaEvaluacion();
            $acta->numero_acta = $numero;
            $acta->tipo = $tipo_acta;
            $acta->carrera_id = $carrera->id;
            $acta->materia_id = $materia->id;
            $acta->semestre_id = $semestre->id;
            $acta->docente_id = $docente->id;
            $acta->fecha_evaluacion = $fecha_evaluacion;
            $acta->seccion = $carrera->programa->turno;
            $acta->generado_por_id = Auth::id();
            $acta->save();

            $seccion = $acta->seccion;
            switch ($seccion) {
                case 'M':
                    $turno = 'MAÑANA';
                    break;
                case 'T':
                    $turno = 'TARDE';
                    break;
                case 'N':
                    $turno = 'NOCHE';
                    break;
                default:
                    $turno = '';
                    break;
            }

            foreach ($alumnos as $alumno) {
                $acta_alumnos = new ExamenSuficienciaActaEvaluacionAlumno();
                $acta_alumnos->acta_evaluacion_id = $acta->id;
                $acta_alumnos->alumno_id = $alumno->id;
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
            $pdf = Pdf::loadView('examenes_suficiencia/actas/pdf', compact('empresa', 'titulo', 'oportunidad', 'numero', 'materia', 'semestre', 'carrera', 'seccion', 'turno', 'alumnos', 'fecha_evaluacion', 'docente', 'prenombre_docente', 'fecha_hoy'));
            $pdf->setPaper('A4', 'landscape');
            $contenido_pdf = $pdf->output();

            $nombre_archivo = 'acta_' . Str::lower(str_replace('/', '_', $numero)) . '.pdf';
            $contenido_pdf = $pdf->output();
            $ubicacion_archivo = 'public/actas_evaluaciones/examenes_suficiencia/generados/' . $nombre_archivo;
            Storage::put($ubicacion_archivo, $contenido_pdf);
            $acta->ubicacion_acta = str_replace('public', 'storage', $ubicacion_archivo);
            $acta->save();

            DB::commit();

            return $pdf->stream('acta_' . Str::lower(str_replace('/', '_', $numero)) . '.pdf');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('examenes_suficiencia.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_actas_examenes_suficiencia');

        DB::beginTransaction();

        try {
            $acta = ExamenSuficienciaActaEvaluacion::findOrFail($id);
            if ($acta->semestre->estado == 'IN') {
                return back()->with('error-message', 'El acta de evaluación del examen de suficiencia no puede ser eliminado. El semestre se encuentra cerrado.');
            }

            if ($acta->alumnos->sum('puntos_examen') > 0 || $acta->alumno->sum('calificacion') > 0) {
                return back()->with('error-message','El acta de evaluación N° ' . $acta->numero_acta . ' no se puede eliminar. Los puntajes ya fueron cargados.');
            } else {
                $ubicacion_acta = $acta->ubicacion_acta;
                $ubicacion_acta = str_replace('storage', 'public', $ubicacion_acta);
                Storage::delete($ubicacion_acta);
                $ubicacion_archivo = $acta->ubicacion_adjunto;
                $ubicacion_archivo = str_replace('storage', 'public', $ubicacion_archivo);
                Storage::delete($ubicacion_archivo);

                ExamenSuficienciaActaEvaluacionAlumno::where('acta_evaluacion_id', $id)->delete();
                $acta->delete();
            }

            DB::commit();

            return redirect()->route('examenes_suficiencia.index_actas')->with('success-message', 'El acta de evaluación N° ' . $acta->numero_acta . ' fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('examenes_suficiencia.index_actas')->with('error-message', 'El acta de evaluación N° ' . $acta->numero_acta . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('examenes_suficiencia.index_actas')->with('error-message', $e->getMessage());
            }
        }
    }

    public function subir_acta(Request $request, $id)
    {
        $this->authorize('subir_adjunto_actas_examenes_suficiencia');

        $request->validate([
            'acta' => ['required', 'file', 'extensions:jpg,png,pdf']
        ]);

        DB::beginTransaction();

        try {
            $acta = ExamenSuficienciaActaEvaluacion::findOrFail($id);

            //cargar archivo
            $archivo = $request->acta;
            $extension = $archivo->getClientOriginalExtension();
            $directorio = 'storage/actas_evaluaciones/examenes_suficiencia/firmados';
            if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png') {
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($archivo);
                $image = $image->encode(new AutoEncoder(quality: 50));
                $nombre = Str::lower(str_replace('/', '_', $acta->numero_acta)) . '_' . Str::lower($acta->tipo) . '_' . Str::lower($acta->materia->nombre_fantasia) . '_' . Str::lower($acta->carrera->nombre_fantasia) . '_' . $acta->semestre->nombre;
                $nombre_archivo = $nombre . '.' . $extension;
                $image->save($directorio . '/' . $nombre_archivo);
            } else {
                $directorio_storage = 'public/actas_evaluaciones/examenes_suficiencia/firmados';
                $nombre = Str::lower(str_replace('/', '_', $acta->numero_acta)) . '_' . Str::lower($acta->tipo) . '_' . Str::lower($acta->materia->nombre_fantasia) . '_' . Str::lower($acta->carrera->nombre_fantasia) . '_' . $acta->semestre->nombre;
                $nombre_archivo = $nombre . '.' . $extension;
                Storage::putFileAs($directorio_storage . '/', $archivo, $nombre_archivo);
            }
            $acta->ubicacion_adjunto = $directorio . '/' . $nombre_archivo;
            $acta->save();

            DB::commit();

            return response()->json([
                'message' => 'El adjunto del acta N° ' . $acta->numero_acta . ' de la materia ' . $acta->materia->nombre_fantasia . ' de la carrera ' . $acta->carrera->nombre_fantasia . ' del semestre ' . $acta->semestre->nombre . ' fue subido exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('examenes_suficiencia.index_actas')->with('error-message', $e->getMessage());
        }
    }

    public function eliminar_acta($id)
    {
        $this->authorize('eliminar_adjunto_actas_examenes_suficiencia');

        DB::beginTransaction();

        try {
            $acta = ExamenSuficienciaActaEvaluacion::findOrFail($id);

            $ubicacion_archivo = $acta->ubicacion_adjunto;
            $ubicacion_archivo = str_replace('storage', 'public', $ubicacion_archivo);
            Storage::delete($ubicacion_archivo);

            $acta->ubicacion_adjunto = null;
            $acta->save();


            DB::commit();

            return redirect()->route('examenes_suficiencia.show_acta', $acta->id)->with('error-message', 'El adjunto del acta N° ' . $acta->numero_acta . ' de la materia ' . $acta->materia->nombre_fantasia . ' de la carrera ' . $acta->carrera->nombre_fantasia . ' del semestre ' . $acta->semestre->nombre . ' fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('examenes_suficiencia.index_actas')->with('error-message', $e->getMessage());
        }
    }
}
