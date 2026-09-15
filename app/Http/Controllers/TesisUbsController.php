<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

use App\Models\InscripcionTemaTesisUbs;
use App\Models\Alumno;
use App\Models\Curso;
use App\Models\Docente;
use App\Models\LineaTesisUbs;
use App\Models\InscripcionUbs;
use App\Models\FechaDefensaTesisUbs;
use App\Models\Empresa;
use App\Models\ActaEvaluacionUbs;
use App\Models\Escala;

class TesisUbsController extends Controller
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
        $this->authorize('ver_tesis_ubs');

        try {
            $tesis = InscripcionTemaTesisUbs::get();

            return view('ubs/tesis/index')->with(compact('tesis'));
        } catch (\Exception $e) {
            return redirect('/')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_tesis_ubs');

        try {
            $tesis = InscripcionTemaTesisUbs::findOrFail($id);
            $fechas_defensas = FechaDefensaTesisUbs::where('estado', 'LI')->orderBy('fecha', 'asc')->orderBy('hora', 'asc')->get();
            $escala = Escala::with('escalaDetalles')->where('programa_id', $tesis->curso->programa_id)->where('estado', 'AC')->first();

            return view('ubs/tesis/show')->with(compact('tesis', 'fechas_defensas', 'escala'));
        } catch (\Exception $e) {
            return redirect()->route('tesis_ubs.index')->with('error-message', $e->getMessage());
        }
    }

    public function parametros_index()
    {
        $this->authorize('ver_parametros_tesis_ubs');

        try {
            return view('ubs/tesis/parametros/index');
        } catch (\Exception $e) {
            return redirect('/')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_tesis_ubs');

        try {
            $alumnos = Alumno::where('ubs', true)->where('estado', 'AC')->get();
            $maestrias = Curso::where('es_maestria', true)->where('estado', 'AC')->get();
            $tutores = Docente::where('ubs', true)->where('tutor_tesis', true)->where('estado', 'AC')->get();
            $lineas = LineaTesisUbs::where('estado', 'AC')->get();


            return view('ubs/tesis/create')->with(compact('alumnos', 'maestrias', 'tutores', 'lineas'));
        } catch (\Exception $e) {
            return redirect()->route('tesis_ubs.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_tesis_ubs');

        $request->validate([
            'alumno' => ['required', 'numeric'],
            'maestria' => ['required', 'numeric'],
            'linea' => ['required', 'numeric'],
            'tutor' => ['required', 'numeric'],
            'tema' => 'required',
            'titulo' => 'required',
        ]);

        DB::beginTransaction();

        try {
            // $old_inscripcion = InscripcionTemaTesisUbs::where('alumno_id', $request->alumno)->where('estado', 'EN')->first();
            // if ($old_inscripcion) {
            //     if ($request->generado_alumno == 'SI') {
            //         return redirect()->route('pantallas_alumnos.inscripciones_tesis', Auth::id())->with('error-message', 'El tema de trabajo final de grado no puede ser inscripto. Ya cuenta con un trabajo final de grado aprobado.');
            //     } else {
            //         return redirect()->route('tesis_ubs.index')->with('error-message', 'El tema de tesis no puede ser inscripto. El alumno seleccionado ya cuenta con una tesis aprobada.');
            //     }
            // }

            $inscripcion = new InscripcionTemaTesisUbs();
            $inscripcion->fecha = Carbon::now();
            $inscripcion->alumno_id = $request->alumno;
            $inscripcion->curso_id = $request->maestria;
            $inscripcion->linea_id = $request->linea;
            $inscripcion->tutor_id = $request->tutor;
            $inscripcion->tema = removeAccents(Str::upper($request->tema));
            $inscripcion->titulo = removeAccents(Str::upper($request->titulo));
            $inscripcion->save();

            DB::commit();

            if ($request->generado_alumno == 'SI') {
                return redirect()->route('pantallas_alumnos.inscripciones_tesis', Auth::id())->with('success-message', 'Su tema de tesis fue creado exitosamente.');
            } else {
                return redirect()->route('tesis_ubs.index')->with('success-message', 'El tema de tesis fue creado exitosamente.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tesis_ubs.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_tesis_ubs');

        try {
            $tesis = InscripcionTemaTesisUbs::findOrFail($id);
            $inscripciones = InscripcionUbs::where('alumno_id', $tesis->alumno_id)->where('estado', 'AC')->get();
            $maestrias = collect();
            foreach ($inscripciones as $inscripcion) {
                if ($inscripcion->curso->es_maestria == true) {
                    $maestrias->push($inscripcion->curso);
                }
            }
            $tutores = Docente::where('ubs', true)->where('tutor_tesis', true)->where('estado', 'AC')->get();
            $lineas = LineaTesisUbs::where('estado', 'AC')->get();

            return view('ubs/tesis/edit')->with(compact('tesis', 'maestrias', 'tutores', 'lineas'));
        } catch (\Exception $e) {
            return redirect()->route('tesis_ubs.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_tesis_ubs');

        $request->validate([
            'maestria' => ['required', 'numeric'],
            'linea' => ['required', 'numeric'],
            'tutor' => ['required', 'numeric'],
            'tema' => 'required',
            'titulo' => 'required',
        ]);

        DB::beginTransaction();

        try {
            $inscripcion = InscripcionTemaTesisUbs::findOrFail($id);
            $inscripcion->curso_id = $request->maestria;
            $inscripcion->linea_id = $request->linea;
            $inscripcion->tutor_id = $request->tutor;
            $inscripcion->tema = removeAccents(Str::upper($request->tema));
            $inscripcion->titulo = removeAccents(Str::upper($request->titulo));
            $inscripcion->save();

            DB::commit();

            return redirect()->route('tesis_ubs.index')->with('success-message', 'El tema de tesis fue actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tesis_ubs.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_maestrias($id)
    {
        $this->authorize('crear_tesis_ubs');

        try {
            $inscripciones = InscripcionUbs::where('alumno_id', $id)->where('estado', 'AC')->get();
            $maestrias = collect();
            foreach ($inscripciones as $inscripcion) {
                if ($inscripcion->curso->es_maestria == true) {
                    $maestrias->push($inscripcion->curso);
                }
            }

            if ($maestrias->count() == 0) {
                return response()->json([
                    'message' => 'El alumno seleccionado no se encuentra apto para elaborar la tesis.',
                ]);
            } else {
                return response()->json([
                    'maestrias' => $maestrias,
                ]);
            }

        } catch (\Exception $e) {
            return redirect()->route('tesis_ubs.index')->with('error-message', $e->getMessage());
        }
    }

    public function asignar_fecha_defensa(Request $request, $id)
    {
        $this->authorize('asignar_fecha_defensa_tesis_ubs');

        $request->validate([
            'fecha' => ['required', 'numeric']
        ]);

        DB::beginTransaction();

        try {
            $tesis = InscripcionTemaTesisUbs::findOrFail($id);
            $tesis->fecha_defensa_id = $request->fecha;
            $tesis->estado = 'FE';
            $tesis->save();

            $fecha = FechaDefensaTesisUbs::findOrFail($request->fecha);
            $fecha->estado = 'OC';
            $fecha->save();

            DB::commit();

            return response()->json([
                'message' => 'La fecha de defensa fue asignada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tesis_ubs.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function puntuar_defensa(Request $request, $id)
    {
        $this->authorize('puntuar_defensas_tesis_ubs');

        $request->validate([
            'puntaje_obtenido' => ['required', 'numeric']
        ]);

        DB::beginTransaction();

        try {
            $tesis = InscripcionTemaTesisUbs::findOrFail($id);
            $escala = Escala::with('escalaDetalles')->where('programa_id', $tesis->curso->programa_id)->first();

            foreach ($escala->escalaDetalles as $detalle) {
                if ($request->puntaje_obtenido >= $detalle->punto_minimo && $request->puntaje_obtenido <= $detalle->punto_maximo) {
                    $nota = $detalle->nota;
                }
            }

            $cantidad_anteproyectos = $tesis->anteproyectos->count();
            $suma_anteproyectos = $tesis->anteproyectos->sum('calificacion');
            $cantidad_borradores = $tesis->borradores->count();
            $suma_borradores = $tesis->borradores->sum('calificacion');

            $cantidad_notas = $cantidad_anteproyectos + $cantidad_borradores + 1;
            $total_notas = $suma_anteproyectos + $suma_borradores + $nota;

            $nota_final = $total_notas / $cantidad_notas;

            $tesis->calificacion = ceil($nota_final * 100) / 100;

            if ($tesis->calificacion > 1) {
                $tesis->estado = 'EN';
            } else {
                $tesis->estado = 'RE';
            }
            $tesis->save();

            DB::commit();

            return response()->json([
                'message' => 'El puntaje de defensa y la calificación final de la tesis fue asignada correctamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tesis_ubs.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function generate_acta($id)
    {
        $this->authorize('generar_actas_tesis_ubs');

        DB::beginTransaction();

        try {
            $empresa = Empresa::first();
            $fecha_hoy = Carbon::now();

            $inscripcion = InscripcionTemaTesisUbs::findOrFail($id);
            $inscripcion->acta_generado = true;
            $inscripcion->save();

            DB::commit();

            $inscripcion->fecha_defensa = Carbon::parse($inscripcion->fechaDefensa->fecha);

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

            switch ($inscripcion->alumno->sexo_id) {
                case 2:
                    $inscripcion->del_alumno = 'de la';
                    $inscripcion->prenombre_alumno = 'la';
                    break;
                default:
                $inscripcion->del_alumno = 'del';
                    $inscripcion->prenombre_alumno = 'el';
                    break;
            }

            $old_acta = ActaEvaluacionUbs::orderBy('id', 'desc')->where('curso_id', $inscripcion->curso_id)->first();
            $anho_actual = Carbon::now()->format('Y');
            $curso = Curso::findOrFail($inscripcion->curso_id);
            if ($old_acta) {
                $numero_acta = substr($old_acta->numero_acta, 3, 191); //el primer numero es cuantos espacios queres eliminar y el segundo numero cuanto queres dejar
                $partes_numero = explode('/', $numero_acta); //separamos el string de numero_dictamen
                $anho_old_acta = $partes_numero[1];
                $partes_numero = explode('.', $partes_numero[0]);
                if ($anho_actual == $anho_old_acta) {
                    $numero_nuevo = intval($partes_numero[4]) + 1;
                    $numero = $curso->codigo . str_pad($numero_nuevo, 3,'0', STR_PAD_LEFT) . '/' . $anho_old_acta;
                } else {
                    $numero = $curso->codigo . str_pad(1, 3, '0', STR_PAD_LEFT) . '/'. $anho_actual;
                }
            } else {
                $numero = $curso->codigo . str_pad(1, 3, '0', STR_PAD_LEFT) . '/'. $anho_actual;
            }
            $inscripcion->numero_acta = $numero;


            $pdf = Pdf::loadView('ubs/tesis/acta_pdf', compact('empresa', 'fecha_hoy', 'inscripcion'));
            $pdf->setPaper('A4');

            return $pdf->stream('acta_tesis_' . Str::lower(str_replace(' ', '', $inscripcion->tema)) . '.pdf');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tesis_ubs.show', $id)->with('error-message', $e->getMessage());
        }
    }
}
