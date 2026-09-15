<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

use App\Models\RubricaAlumnoTesis;
use App\Models\RubricaAlumnoDetalleTesis;
use App\Models\RubricaTesis;
use App\Models\RubricaDetalleTesis;
use App\Models\InscripcionTemaTesis;
use App\Models\Materia;
use App\Models\Semestre;
use App\Models\AlumnoPuntaje;
use App\Models\Escala;
use App\Models\EscalaDetalle;
use App\Models\AlumnoNota;


class RubricaAlumnoTesisController extends Controller
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

    public function show_proceso($id)
    {
        $this->authorize('ver_rubricas_alumnos_tesis');

        try {
            $rubrica = RubricaAlumnoTesis::with(['detalles' => function ($query) {
                $query->orderBy('id');
            }])->where('inscripcion_id', $id)->first();

            $inscripcion = InscripcionTemaTesis::findOrFail($id);
            $total_posible = RubricaTesis::where('tipo_id', $inscripcion->tipo_id)->where('estado', 'AC')->first()->detalles->sum('puntos');

            return view('tesis/cargar_rubricas/proceso/show')->with(compact('rubrica', 'total_posible'));
        } catch (\Exception $e) {
            return redirect()->route('inscripciones_temas_tesis.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function create_proceso($id)
    {
        $this->authorize('cargar_rubricas_alumnos_tesis');

        try {
            $inscripcion = InscripcionTemaTesis::findOrFail($id);
            $rubrica = RubricaTesis::with('detalles')->where('tipo_id', $inscripcion->tipo_id)->where('estado', 'AC')->first();
            return view('tesis/cargar_rubricas/proceso/create')->with(compact('inscripcion', 'rubrica'));
        } catch (\Exception $e) {
            return redirect()->route('inscripcion_temas_tesis.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function store_proceso(Request $request, $id)
    {
        $this->authorize('cargar_rubricas_alumnos_tesis');

        $request->validate([
            'detalles' => ['required', 'array'],
            'detalles.*.id' => ['required', 'numeric'],
            'detalles.*.puntaje_obtenido' => ['required', 'numeric', 'min:0']
        ]);

        DB::beginTransaction();

        try {
            $inscripcion = InscripcionTemaTesis::findOrFail($id);

            $rubrica = new RubricaAlumnoTesis();
            $rubrica->inscripcion_id = $inscripcion->id;
            $rubrica->puntaje_total = 0;
            $rubrica->cargado_por_id = Auth::id();
            $rubrica->save();

            $total_puntos = 0;
            foreach ($request->detalles as $detalle) {
                $rubrica_detalle = new RubricaAlumnoDetalleTesis();
                $rubrica_detalle->rubrica_id = $rubrica->id;
                $rubrica_detalle->rubrica_detalle_id = $detalle['id'];
                $rubrica_detalle->puntos_obtenidos = $detalle['puntaje_obtenido'];
                $rubrica_detalle->save();

                $total_puntos = $total_puntos + $detalle['puntaje_obtenido'];
            }

            $rubrica_id = $rubrica->id;
            $rubrica = RubricaAlumnoTesis::findOrFail($rubrica_id);
            $rubrica->puntaje_total = $total_puntos;
            $rubrica->save();

            DB::commit();

            return redirect()->route('inscripciones_temas_tesis.show', $id)->with('success-message', 'La rúbrica del alumno fue cargada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('inscripciones_temas_tesis.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function edit_proceso($id)
    {
        $this->authorize('editar_rubricas_alumnos_tesis');

        try {
            $rubrica = RubricaAlumnoTesis::with(['detalles' => function ($query) {
                $query->orderBy('id');
            }])->where('inscripcion_id', $id)->first();

            $inscripcion = InscripcionTemaTesis::findOrFail($id);
            $total_posible = RubricaTesis::where('tipo_id', $inscripcion->tipo_id)->where('estado', 'AC')->first()->detalles->sum('puntos');
            return view('tesis/cargar_rubricas/proceso/edit')->with(compact('rubrica', 'total_posible'));
        } catch (\Exception $e) {
            return redirect()->route('cargar_rubricas_tesis.show_proceso', $id)->with('error-message', $e->getMessage());
        }
    }

    public function update_proceso(Request $request, $id)
    {
        $this->authorize('editar_rubricas_alumnos_tesis');

        $request->validate([
            'detalles' => ['required', 'array'],
            'detalles.*.id' => ['required', 'numeric'],
            'detalles.*.puntaje_obtenido' => ['required', 'numeric', 'min:0']
        ]);

        DB::beginTransaction();

        try {
            $inscripcion = InscripcionTemaTesis::findOrFail($id);
            $rubrica = RubricaAlumnoTesis::where('inscripcion_id', $id)->first();
            $total_puntos = 0;

            RubricaAlumnoDetalleTesis::where('rubrica_id', $rubrica->id)->delete();
            foreach ($request->detalles as $detalle) {
                $rubrica_detalle = new RubricaAlumnoDetalleTesis();
                $rubrica_detalle->rubrica_id = $rubrica->id;
                $rubrica_detalle->rubrica_detalle_id = $detalle['id'];
                $rubrica_detalle->puntos_obtenidos = $detalle['puntaje_obtenido'];
                $rubrica_detalle->save();

                $total_puntos = $total_puntos + $detalle['puntaje_obtenido'];
            }

            $rubrica->actualizado_por_id = Auth::id();
            $rubrica->puntaje_total = $total_puntos;
            $rubrica->save();

            DB::commit();

            return redirect()->route('cargar_rubricas_tesis.show_proceso', $id)->with('success-message', 'La rúbrica del alumno fue actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('cargar_rubricas_tesis.show_proceso', $id)->with('error-message', $e->getMessage());
        }
    }

    public function show_defensa($id)
    {
        $this->authorize('ver_rubricas_alumnos_defensas_tesis');

        try {
            $rubrica = RubricaAlumnoTesis::where('inscripcion_id', $id)->first();

            $inscripcion = InscripcionTemaTesis::findOrFail($id);
            $total_proceso = RubricaTesis::where('tipo_id', $inscripcion->tipo_id)->where('estado', 'AC')->first()->detalles->sum('puntos');
            $total_posible = 100 - $total_proceso;
            $rubrica = RubricaAlumnoTesis::where('inscripcion_id', $inscripcion->id)->orderBy('id', 'desc')->first();

            return view('tesis/cargar_rubricas/defensa/show')->with(compact('rubrica', 'total_posible', 'rubrica'));
        } catch (\Exception $e) {
            return redirect()->route('inscripciones_temas_tesis.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function create_defensa($id)
    {
        $this->authorize('cargar_rubricas_alumnos_defensas_tesis');

        try {
            $inscripcion = InscripcionTemaTesis::findOrFail($id);
            $total_proceso = RubricaTesis::where('tipo_id', $inscripcion->tipo_id)->where('estado', 'AC')->first()->detalles->sum('puntos');
            $total_posible = 100 - $total_proceso;

            return view('tesis/cargar_rubricas/defensa/create')->with(compact('inscripcion', 'total_posible'));
        } catch (\Exception $e) {
            return redirect()->route('inscripcion_temas_tesis.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function store_defensa(Request $request, $id)
    {
        $this->authorize('cargar_rubricas_alumnos_defensas_tesis');

        $inscripcion = InscripcionTemaTesis::findOrFail($id);
        $total_proceso = RubricaTesis::where('tipo_id', $inscripcion->tipo_id)->where('estado', 'AC')->first()->detalles->sum('puntos');
        $total_posible = 100 - $total_proceso;

        $request->validate([
            'puntaje_obtenido' => ['required', 'numeric', 'min:0', 'max:' . $total_posible],
            'acta' => ['required', 'file', 'extensions:pdf']
        ]);

        DB::beginTransaction();

        try {
            $inscripcion = InscripcionTemaTesis::findOrFail($id);

            $rubrica = new RubricaAlumnoTesis();
            $rubrica->inscripcion_id = $inscripcion->id;
            $rubrica->puntaje_total = $request->puntaje_obtenido;
            $rubrica->cargado_por_id = Auth::id();

            //cargar archivo
            $archivo = $request->acta;
            $extension = $archivo->getClientOriginalExtension();
            $directorio = 'storage/actas_evaluaciones/firmados/defensas';
            $directorio_storage = 'public/actas_evaluaciones/firmados/defensas';
            $nombre = $inscripcion->id . '_' . Str::lower(str_replace(' ', '_', $inscripcion->alumno->primer_nombre)) . '_' . Str::lower(str_replace(' ', '_', $inscripcion->alumno->primer_apellido));
            $nombre_archivo = $nombre . '.' . $extension;
            Storage::putFileAs($directorio_storage . '/', $archivo, $nombre_archivo);

            $rubrica->url_acta = $directorio . '/' . $nombre_archivo;
            $rubrica->save();

            $programa_id = $inscripcion->carrera->programa_id;
            switch ($programa_id) {
                case 1:
                    $buscar = 'TRABAJO FINAL DE GRADO - GN';
                    break;
                case 1:
                    $buscar = 'TRABAJO FINAL DE GRADO - CPEL';
                    break;
                case 1:
                    $buscar = 'TRABAJO FINAL DE GRADO - GND';
                    break;
                default:
                    break;
            }
            $materia = Materia::where('nombre_fantasia', $buscar)->first();
            $semestre = Semestre::where('estado', 'AC')->orderBy('id', 'desc')->first();
            $puntos_obtenidos = RubricaAlumnoTesis::where('inscripcion_id', $inscripcion->id)->get()->sum('puntaje_total');

            $alumno_puntaje = new AlumnoPuntaje();
            $alumno_puntaje->alumno_id = $inscripcion->alumno_id;
            $alumno_puntaje->materia_id = $materia->id;
            $alumno_puntaje->carrera_id = $inscripcion->carrera_id;
            $alumno_puntaje->semestre_id = $semestre->id;
            $alumno_puntaje->evaluacion_id = 4;
            $alumno_puntaje->puntos_obtenidos = $puntos_obtenidos;
            $alumno_puntaje->cargado_por_id = Auth::id();
            $alumno_puntaje->save();

            $escala = Escala::where('programa_id', $programa_id)->first();
            $nota = EscalaDetalle::where('escala_id', $escala->id)->where('punto_minimo', '<=', $alumno_puntaje->puntos_obtenidos)->where('punto_maximo', '>=', $alumno_puntaje->puntos_obtenidos)->first()->nota;

            $alumno_nota = new AlumnoNota();
            $alumno_nota->alumno_id = $alumno_puntaje->alumno_id;
            $alumno_nota->carrera_id = $alumno_puntaje->carrera_id;
            $alumno_nota->materia_id = $alumno_puntaje->materia_id;
            $alumno_nota->semestre_id = $alumno_puntaje->semestre_id;
            $alumno_nota->evaluacion = 'ORDINARIO';
            $alumno_nota->calificacion = $nota;
            $alumno_nota->save();

            if ($nota != 1) {
                $inscripcion->estado = 'EN';
            } else {
                $inscripcion->estado = 'RR';
            }
            $inscripcion->calificacion = $nota;
            $inscripcion->save();

            DB::commit();

            return redirect()->route('inscripciones_temas_tesis.show', $id)->with('success-message', 'La rúbrica del alumno fue cargada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('inscripciones_temas_tesis.show', $id)->with('error-message', $e->getMessage());
        }
    }
}
