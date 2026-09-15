<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

use App\Models\InscripcionTemaTesis;
use App\Models\Alumno;
use App\Models\Carrera;
use App\Models\Matriculacion;
use App\Models\TipoTesis;
use App\Models\AreaTesis;
use App\Models\LineaTesis;
use App\Models\Docente;
use App\Models\BloqueAnteproyectoTesis;
use App\Models\AnteproyectoTesis;
use App\Models\Empresa;
use App\Models\ActaEvaluacion;

class InscripcionTemaTesisController extends Controller
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
        $this->authorize('ver_inscripciones_tesis');

        try {
            $inscripciones = InscripcionTemaTesis::orderBy('id', 'desc')->get();
            return view('tesis/inscripciones_temas/index')->with(compact('inscripciones'));
        } catch (\Exception $e) {
            return redirect()->route('inscripciones_temas_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_inscripciones_tesis');

        try {
            $inscripcion = InscripcionTemaTesis::findOrFail($id);
            return view('tesis/inscripciones_temas/show')->with(compact('inscripcion'));
        } catch (\Exception $e) {
            return redirect()->route('inscripciones_temas_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_inscripciones_tesis');

        try {
            $alumnos = Alumno::where('estado', 'AC')->get();
            // $alumno = Alumno::where('usuario_id', Auth::id())->first();
            // $matriculaciones = Matriculacion::where('alumno_id', $alumno->id)->where('estado', 'AC')->orderBy('id', 'desc')->get();
            // $carreras = collect();
            // foreach ($matriculaciones as $matriculacion) {
            //     $carreras->push($matriculacion->carrera);
            // }
            $carreras = Carrera::where('estado', 'AC')->get();
            $tipos = TipoTesis::where('estado', 'AC')->orderBy('nombre', 'asc')->get();
            $areas = AreaTesis::where('estado', 'AC')->orderBy('nombre', 'asc')->get();
            $lineas = LineaTesis::where('estado', 'AC')->orderBy('nombre', 'asc')->get();
            $tutores = Docente::where('tutor_tesis', true)->where('estado', 'AC')->get();
            return view('tesis/inscripciones_temas/create')->with(compact('alumnos', 'carreras', 'tipos', 'areas', 'lineas', 'tutores'));
            // return view('tesis/inscripciones_temas/create')->with(compact('alumno', 'carreras', 'tipos', 'areas', 'lineas', 'tutores'));
        } catch (\Exception $e) {
            return redirect()->route('inscripciones_temas_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_inscripciones_tesis');

        $request->validate([
            'alumno' => ['required', 'numeric'],
            'carrera' => ['required', 'numeric'],
            'tipo' => ['required', 'numeric'],
            'area' => ['required', 'numeric'],
            'linea' => ['required', 'numeric'],
            'tutor' => ['required', 'numeric'],
            'alumno' => ['required', 'numeric'],
            'lugar_investigacion' => 'nullable',
            'tema' => 'required',
            'justificacion' => 'required',
        ]);

        DB::beginTransaction();

        try {
            $old_inscripcion = InscripcionTemaTesis::where('alumno_id', $request->alumno)->where('estado', 'EN')->first();
            if ($old_inscripcion) {
                if ($request->generado_alumno == 'SI') {
                    return redirect()->route('pantallas_alumnos.inscripciones_tesis', Auth::id())->with('error-message', 'El tema de trabajo final de grado no puede ser inscripto. Ya cuenta con un trabajo final de grado aprobado.');
                } else {
                    return redirect()->route('inscripciones_temas_tesis.index')->with('error-message', 'El tema de trabajo final de grado no puede ser inscripto. El alumno seleccionado ya cuenta con un trabajo final de grado aprobado.');
                }

            }

            $inscripcion = new InscripcionTemaTesis();
            $inscripcion->fecha = Carbon::now();
            $inscripcion->alumno_id = $request->alumno;
            $inscripcion->carrera_id = $request->carrera;
            $inscripcion->programa_id = Carrera::findOrFail($request->carrera)->programa_id;
            $inscripcion->tipo_id = $request->tipo;
            $inscripcion->area_id = $request->area;
            $inscripcion->linea_id = $request->linea;
            $inscripcion->tutor_id = $request->tutor;
            $inscripcion->lugar_investigacion = removeAccents(Str::upper($inscripcion->lugar_ivestigacion));
            $inscripcion->tema = removeAccents(Str::upper($request->tema));
            $inscripcion->justificacion = removeAccents(Str::upper($request->justificacion));
            $inscripcion->save();

            DB::commit();

            if ($request->generado_alumno == 'SI') {
                return redirect()->route('pantallas_alumnos.inscripciones_tesis', Auth::id())->with('success-message', 'El tema de trabajo final de grado del alumno ' . $inscripcion->alumno->primer_nombre . ' ' . $inscripcion->alumno->primer_apellido . ' fue creado exitosamente.');
            } else {
                return redirect()->route('inscripciones_temas_tesis.index')->with('success-message', 'El tema de trabajo final de grado del alumno ' . $inscripcion->alumno->primer_nombre . ' ' . $inscripcion->alumno->primer_apellido . ' fue creado exitosamente.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('inscripciones_temas_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_inscripciones_tesis');

        try {
            $inscripcion = InscripcionTemaTesis::findOrFail($id);
            $matriculaciones = Matriculacion::where('alumno_id', $inscripcion->alumno_id)->where('estado', 'AC')->orderBy('id', 'desc')->get();
            $carreras = collect();
            foreach ($matriculaciones as $matriculacion) {
                $carreras->push($matriculacion->carrera);
            }
            $tipos = TipoTesis::where('estado', 'AC')->orderBy('nombre', 'asc')->get();
            $areas = AreaTesis::where('estado', 'AC')->orderBy('nombre', 'asc')->get();
            $lineas = LineaTesis::where('estado', 'AC')->orderBy('nombre', 'asc')->get();
            $tutores = Docente::where('tutor_tesis', true)->where('estado', 'AC')->get();
            return view('tesis/inscripciones_temas/edit')->with(compact('inscripcion', 'carreras', 'tipos', 'areas', 'lineas', 'tutores'));
        } catch (\Exception $e) {
            return redirect()->route('inscripciones_temas_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {

    }

    public function aprobar_tutor($id)
    {
        $this->authorize('aprobar_tutor_inscripciones_tesis');

        DB::beginTransaction();

        try {
            $inscripcion = InscripcionTemaTesis::findOrFail($id);
            $inscripcion->aprobado_tutor_id = Auth::id();
            $inscripcion->fecha_aprobado_tutor = Carbon::now();
            $inscripcion->estado = 'AT';
            $inscripcion->save();

            DB::commit();

            return redirect()->route('inscripciones_temas_tesis.show', $id)->with('success-message', 'El tema de trabajo final de grado del alumno ' . $inscripcion->alumno->primer_nombre . ' ' . $inscripcion->alumno->primer_apellido . ' fue aprobado por el tutor exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('inscripciones_temas_tesis.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function anular_aprobacion_tutor($id)
    {
        $this->authorize('anular_tutor_inscripciones_tesis');

        DB::beginTransaction();

        try {
            $inscripcion = InscripcionTemaTesis::findOrFail($id);
            $inscripcion->aprobado_tutor_id = null;
            $inscripcion->fecha_aprobado_tutor = null;
            $inscripcion->estado = 'PE';
            $inscripcion->save();

            DB::commit();

            return redirect()->route('inscripciones_temas_tesis.show', $id)->with('error-message', 'La aprobación del tutor del tema de trabajo final de grado del alumno ' . $inscripcion->alumno->primer_nombre . ' ' . $inscripcion->alumno->primer_apellido . ' fue anulada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('inscripciones_temas_tesis.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function aprobar_coordinacion($id)
    {
        $this->authorize('aprobar_coordinacion_inscripciones_tesis');

        DB::beginTransaction();

        try {
            $inscripcion = InscripcionTemaTesis::findOrFail($id);
            $inscripcion->aprobado_coordinacion_id = Auth::id();
            $inscripcion->fecha_aprobado_coordinacion = Carbon::now();
            $inscripcion->estado = 'AC';
            $inscripcion->save();

            $bloques = BloqueAnteproyectoTesis::where('estado', 'AC')->get();
            foreach ($bloques as $bloque) {
                $anteproyecto = new AnteproyectoTesis();
                $anteproyecto->inscripcion_id = $inscripcion->id;
                $anteproyecto->bloque_id = $bloque->id;
                $anteproyecto->numero_bloque = $bloque->numero;
                $anteproyecto->save();
            }

            DB::commit();

            return redirect()->route('inscripciones_temas_tesis.show', $id)->with('success-message', 'El tema de trabajo final de grado del alumno ' . $inscripcion->alumno->primer_nombre . ' ' . $inscripcion->alumno->primer_apellido . ' fue aprobado por coordinación exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('inscripciones_temas_tesis.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function anular_aprobacion_coordinacion($id)
    {
        $this->authorize('anular_coordinacion_inscripciones_tesis');

        DB::beginTransaction();

        try {
            $inscripcion = InscripcionTemaTesis::findOrFail($id);
            $inscripcion->aprobado_coordinacion_id = null;
            $inscripcion->fecha_aprobado_coordinacion = null;
            $inscripcion->estado = 'AT';
            $inscripcion->save();

            $anteproyectos = AnteproyectoTesis::where('inscripcion_id', $inscripcion->id)->delete();

            DB::commit();

            return redirect()->route('inscripciones_temas_tesis.show', $id)->with('error-message', 'La aprobación de coordinacion del tema de trabajo final de grado del alumno ' . $inscripcion->alumno->primer_nombre . ' ' . $inscripcion->alumno->primer_apellido . ' fue anulada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('inscripciones_temas_tesis.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function rechazar($id)
    {
        $this->authorize('rechazar_inscripciones_tesis');

        DB::beginTransaction();

        try {
            $inscripcion = InscripcionTemaTesis::findOrFail($id);
            $inscripcion->rechazado_por_id = Auth::id();
            $inscripcion->fecha_rechazo = Carbon::now();
            $inscripcion->estado = 'RE';
            $inscripcion->save();

            DB::commit();

            return redirect()->route('inscripciones_temas_tesis.show', $id)->with('error-message', 'El tema de trabajo final de grado del alumno ' . $inscripcion->alumno->primer_nombre . ' ' . $inscripcion->alumno->primer_apellido . ' fue rechazado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('inscripciones_temas_tesis.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function anular_rechazo($id)
    {
        $this->authorize('anular_rechazo_inscripciones_tesis');

        DB::beginTransaction();

        try {
            $inscripcion = InscripcionTemaTesis::findOrFail($id);
            $inscripcion->rechazado_por_id = null;
            $inscripcion->fecha_rechazo = null;
            $inscripcion->estado = 'PE';
            $inscripcion->save();

            DB::commit();

            return redirect()->route('inscripciones_temas_tesis.show', $id)->with('error-message', 'El rechazo del tema de trabajo final de grado del alumno ' . $inscripcion->alumno->primer_nombre . ' ' . $inscripcion->alumno->primer_apellido . ' fue anulado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('inscripciones_temas_tesis.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function generate_acta($id)
    {
        $this->authorize('generar_actas_tesis');

        DB::beginTransaction();

        try {
            $empresa = Empresa::first();
            $fecha_hoy = Carbon::now();

            $inscripcion = InscripcionTemaTesis::findOrFail($id);
            $inscripcion->acta_generado = true;
            $inscripcion->save();

            DB::commit();

            $inscripcion->fecha_defensa = Carbon::parse($inscripcion->fecha_defensa);

            $nombre_alumno = $inscripcion->alumno->primer_nombre;
            if ($inscripcion->alumno->segundo_nombre) {
                $nombre_alumno = $nombre_alumno . ' ' . $inscripcion->alumno->segundo_nombre;
            }
            if ($inscripcion->alumno->tercer_nombre) {
                $nombre_alumno = $nombre_alumno . ' ' . $inscripcion->alumno->tercer_nombre;
            }
            $nombre_alumno = $nombre_alumno . ' ' . $inscripcion->alumno->primer_apellido;
            if ($inscripcion->alumno->segundo_apellido) {
                $nombre_alumno = $nombre_alumno . ' ' . $inscripcion->alumno->segundo_apellido;
            }
            $inscripcion->nombre_alumno = $nombre_alumno;

            switch ($inscripcion->sexo_id) {
                case 2:
                    $inscripcion->del_alumno = 'de la';
                    $inscripcion->prenombre_alumno = 'la';
                    break;
                default:
                $inscripcion->del_alumno = 'del';
                    $inscripcion->prenombre_alumno = 'el';
                    break;
            }

            $old_acta = ActaEvaluacion::orderBy('id', 'desc')->where('carrera_id', $inscripcion->carrera_id)->first();
            $anho_actual = Carbon::now()->format('Y');
            if ($old_acta) {
                $numero_acta = substr($old_acta->numero_acta, 3, 191); //el primer numero es cuantos espacios queres eliminar y el segundo numero cuanto queres dejar
                $partes_numero = explode('/', $numero_acta); //separamos el string de numero_dictamen
                if ($anho_actual == $partes_numero[1]) {
                    $numero_nuevo = intval($partes_numero[0]) + 1;
                    $numero = $inscripcion->carrera->abreviatura . str_pad($numero_nuevo, 3,'0', STR_PAD_LEFT) . '/' . $partes_numero[1];
                } else {
                    $numero = $inscripcion->carrera->abreviatura . str_pad(1, 3, '0', STR_PAD_LEFT) . '/'. $anho_actual;
                }
            } else {
                $numero = $inscripcion->carrera->abreviatura . str_pad(1, 3, '0', STR_PAD_LEFT) . '/'. $anho_actual;
            }
            $inscripcion->numero_acta = $numero;


            $pdf = Pdf::loadView('tesis/acta_pdf', compact('empresa', 'fecha_hoy', 'inscripcion'));
            $pdf->setPaper('A4');

            return $pdf->stream('acta_tfg_' . Str::lower(str_replace(' ', '', $inscripcion->tema)) . '.pdf');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('mallas.show', $id)->with('error-message', $e->getMessage());
        }
    }
}
