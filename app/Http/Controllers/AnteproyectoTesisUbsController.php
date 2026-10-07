<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

use App\Models\AnteproyectoTesisUbs;
use App\Models\InscripcionTemaTesisUbs;
use App\Models\BloqueAnteproyectoTesisUbs;
use App\Models\EntregaAnteproyectoTesisUbs;
use App\Models\CursoModulo;
use App\Models\BorradorTesisUbs;
use App\Models\Escala;
use App\Models\AlumnoPuntajeUbs;
use App\Models\AlumnoNotaUbs;
use App\Models\InscripcionModulo;


class AnteproyectoTesisUbsController extends Controller
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
        $this->authorize('ver_anteproyectos_tesis_ubs');

        try {
            $tesis = InscripcionTemaTesisUbs::with(['anteproyectos' => function ($query) {
                $query->orderBy('numero_bloque', 'asc');
            }])->findOrFail($id);

            $escala = Escala::with('escalaDetalles')->where('programa_id', $tesis->curso->programa_id)->where('estado', 'AC')->first();

            return view('ubs/tesis/anteproyectos/show')->with(compact('tesis', 'escala'));
        } catch (\Exception $e) {
            return redirect()->route('tesis_ubs.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function create($id)
    {
        $this->authorize('crear_anteproyectos_tesis_ubs');

        try {
            $tesis = InscripcionTemaTesisUbs::findOrFail($id);
            $bloques = BloqueAnteproyectoTesisUbs::where('estado', 'AC')->get();
            $modulos = CursoModulo::where('curso_id', $tesis->curso_id)->get();

            return view('ubs/tesis/anteproyectos/create')->with(compact('tesis', 'bloques', 'modulos'));
        } catch (\Exception $e) {
            return redirect()->route('tesis_ubs.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request, $id)
    {
        $this->authorize('crear_anteproyectos_tesis_ubs');

        $request->validate([
            'detalles' => ['required', 'array'],
            'detalles.*.bloque' => ['required', 'numeric'],
            'detalles.*.modulo' => ['required', 'numeric']
        ]);

        DB::beginTransaction();

        try {
            $tesis = InscripcionTemaTesisUbs::findOrFail($id);

            foreach ($request->detalles as $detalle) {
                $anteproyecto = new AnteproyectoTesisUbs();
                $anteproyecto->inscripcion_id = $tesis->id;
                $bloque = BloqueAnteproyectoTesisUbs::findOrFail($detalle['bloque']);
                $anteproyecto->bloque_id = $bloque->id;
                $anteproyecto->numero_bloque = $bloque->numero;
                $anteproyecto->modulo_id = $detalle['modulo'];
                $anteproyecto->save();
            }

            DB::commit();

            return redirect()->route('tesis_ubs.show', $id)->with('success-message', 'El anteproyecto de la tesis fue generado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tesis_ubs.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function show_entregas($id)
    {
        $this->authorize('ver_entregas_anteproyectos_tesis_ubs');

        try {
            $anteproyecto = AnteproyectoTesisUbs::with(['entregas' => function ($query) {
                $query->orderBy('numero', 'desc');
            }])->findOrFail($id);

            return view('ubs/tesis/anteproyectos/show_entregas')->with(compact('anteproyecto'));
        } catch (\Exception $e) {
            return redirect()->route('tesis_ubs.show_anteproyecto', $id)->with('error-message', $e->getMessage());
        }
    }

    public function save_entrega(Request $request, $id)
    {
        $this->authorize('entregar_anteproyectos_tesis_ubs');

        $request->validate([
            'archivo' => ['required', 'file', 'extensions:doc,docx'],
            'comentario' => 'nullable'
        ]);

        DB::beginTransaction();

        try {
            $anteproyecto = AnteproyectoTesisUbs::findOrFail($id);
            $anteproyecto->estado = 'EN';
            $anteproyecto->save();

            $ultima_entrega = EntregaAnteproyectoTesisUbs::where('anteproyecto_id', $anteproyecto->id)->orderBy('id', 'desc')->first();
            if ($ultima_entrega) {
                $numero_entrega = $ultima_entrega->numero + 1;
            } else {
                $numero_entrega = 1;
            }

            $cantidad_anteproyectos = AnteproyectoTesisUbs::where('inscripcion_id', $anteproyecto->inscripcion_id)->count();

            $archivo = $request->archivo;
            $extension = $archivo->getClientOriginalExtension();
            $directorio = 'storage/maestrias/tesis/anteproyectos/entregas';
            $directorio_storage = 'public/maestrias/tesis/anteproyectos/entregas';

            $nombre = 'anteproyecto_bloque_' . $anteproyecto->bloque->numero . '_' . Str::lower(str_replace(' ', '_', $anteproyecto->inscripcion->tema) . '_' . $numero_entrega);
            $nombre_archivo = $nombre . '.' . $extension;
            Storage::putFileAs($directorio_storage . '/', $archivo, $nombre_archivo);

            $entrega = new EntregaAnteproyectoTesisUbs();
            $entrega->anteproyecto_id = $anteproyecto->id;
            $entrega->url_archivo = $directorio . '/' . $nombre_archivo;
            $entrega->comentario = removeAccents(Str::upper($request->comentario));
            $entrega->numero = $numero_entrega;
            $entrega->save();

            $inscripcion = InscripcionTemaTesisUbs::findOrFail($anteproyecto->inscripcion_id);
            if ($inscripcion->estado == 'AC') {
                $inscripcion->estado = 'EC';
                $inscripcion->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'La entrega fue realizada existosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            if (Auth::user()->hasRole('ALUMNO')) {
                return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', $e->getMessage());
            } else {
                return redirect()->route('tesis_ubs.show_entregas_anteproyecto', $id)->with('error-message', $e->getMessage());
            }
        }
    }

    public function save_correccion(Request $request, $id)
    {
        $this->authorize('corregir_anteproyectos_tesis_ubs');

        $request->validate([
            'archivo' => ['nullable', 'file', 'extensions:doc,docx'],
            'comentario' => 'nullable'
        ]);

        DB::beginTransaction();

        try {
            if ($request->archivo == null && $request->comentario == null) {
                return response()->json([
                    'mensaje' => 'La corrección/comentario no se puede guardar vacía. Por favor, complete uno de los campos.',
                ]);
            }

            $anteproyecto = AnteproyectoTesisUbs::findOrFail($id);
            $anteproyecto->estado = 'CO';
            $anteproyecto->save();

            $ultima_entrega = EntregaAnteproyectoTesisUbs::where('anteproyecto_id', $anteproyecto->id)->orderBy('id', 'desc')->first();
            if ($ultima_entrega) {
                $numero_entrega = $ultima_entrega->numero + 1;
            } else {
                $numero_entrega = 1;
            }

            if ($request->archivo != null) {
                $archivo = $request->archivo;
                $extension = $archivo->getClientOriginalExtension();
                $directorio = 'storage/maestrias/tesis/anteproyectos/correcciones';
                $directorio_storage = 'public/maestrias/tesis/anteproyectos/correcciones';

                $nombre = 'anteproyecto_bloque_' . $anteproyecto->bloque->numero . '_' . Str::lower(str_replace(' ', '_', $anteproyecto->inscripcion->tema)) . '_' . $numero_entrega;
                $nombre_archivo = $nombre . '.' . $extension;
                Storage::putFileAs($directorio_storage . '/', $archivo, $nombre_archivo);

                $entrega = new EntregaAnteproyectoTesisUbs();
                $entrega->anteproyecto_id = $anteproyecto->id;
                $entrega->url_archivo = $directorio . '/' . $nombre_archivo;
                $entrega->comentario = removeAccents(Str::upper($request->comentario));
                $entrega->entrega = false;
                $entrega->numero = $numero_entrega;
                $entrega->save();
            } else {
                $entrega = new EntregaAnteproyectoTesisUbs();
                $entrega->anteproyecto_id = $anteproyecto->id;
                $entrega->comentario = removeAccents(Str::upper($request->comentario));
                $entrega->entrega = false;
                $entrega->numero = $numero_entrega;
                $entrega->save();
            }

            $inscripcion = InscripcionTemaTesisUbs::findOrFail($anteproyecto->inscripcion_id);
            if ($inscripcion->estado == 'AC') {
                $inscripcion->estado = 'EC';
                $inscripcion->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'La corrección/comentario fue guardada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            if (Auth::user()->hasAnyRole(['DOCENTE', 'ENCARGADO_DOCENTE'])) {
                return redirect()->route('pantallas_docentes.index', Auth::id())->with('error-message', $e->getMessage());
            } else {
                return redirect()->route('tesis_ubs.show_entregas_anteproyecto', $id)->with('error-message', $e->getMessage());
            }
        }
    }

    public function delete_entrega($id)
    {
        $this->authorize('eliminar_entregas_anteproyectos_tesis_ubs');

        DB::beginTransaction();

        try {
            $entrega = EntregaAnteproyectoTesisUbs::findOrFail($id);
            $ubicacion_archivo = $entrega->url_archivo;
            $ubicacion_archivo = str_replace('storage', 'public', $ubicacion_archivo);
            $anteproyecto = AnteproyectoTesisUbs::findOrFail($entrega->anteproyecto_id);

            if ($entrega->entrega == false) {
                $tipo = 'corrección';
            } else {
                $tipo = 'entrega';
            }

            if ($anteproyecto->estado == 'AC' || $anteproyecto->estado == 'AT') {
                return back()->with('error-message', 'No se puede eliminar la ' . $tipo . ' el bloque ya se encuentra aprobado.');
            }

            Storage::delete($ubicacion_archivo);
            $entrega->delete();

            $total_entregas = EntregaAnteproyectoTesisUbs::where('anteproyecto_id', $anteproyecto->id)->count();

            if ($total_entregas == 0) {
                $anteproyecto->estado = 'PE';
                $anteproyecto->save();

                $inscripcion = InscripcionTemaTesisUbs::findOrFail($anteproyecto->inscripcion_id);
                $inscripcion->estado = 'AC';
                $inscripcion->save();
            }

            DB::commit();

            if (Auth::user()->hasRole('ALUMNO')) {
                return redirect()->route('pantallas_alumnos.show_entregas_anteproyectos_tesis', $anteproyecto->id)->with('error-message', 'La ' . $tipo . ' fue eliminada exitosamente.');
            } else {
                return redirect()->route('tesis_ubs.show_entregas_anteproyecto', $anteproyecto->id)->with('error-message', 'La ' . $tipo . ' fue eliminada exitosamente.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            $entrega = EntregaAnteproyectoTesisUbs::findOrFail($id);
            $anteproyecto = AnteproyectoTesisUbs::findOrFail($entrega->anteproyecto_id);

            if (Auth::user()->hasRole('ALUMNO')) {
                return redirect()->route('pantallas_alumnos.show_entregas_anteproyectos_tesis', $anteproyecto->id)->with('error-message', $e->getMessage());
            } else {
                return redirect()->route('tesis_ubs.show_entregas_anteproyecto', $anteproyecto->id)->with('error-message', $e->getMessage());
            }


        }
    }

    public function aprobar_tutor($id)
    {
        $this->authorize('aprobar_tutor_anteproyectos_tesis_ubs');

        DB::beginTransaction();

        try {
            $anteproyecto = AnteproyectoTesisUbs::findOrFail($id);
            $anteproyecto->estado = 'AT';
            $anteproyecto->aprobado_tutor_id = Auth::id();
            $anteproyecto->fecha_aprobado_tutor = Carbon::now();
            $anteproyecto->save();

            DB::commit();

            return redirect()->route('tesis_ubs.show_anteproyecto', $anteproyecto->inscripcion_id)->with('success-message', 'El bloque ' . $anteproyecto->nombre . ' fue aprobado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            $anteproyecto = AnteproyectoTesisUbs::findOrFail($id);
            return redirect()->route('tesis_ubs.show_anteproyecto', $anteproyecto->inscripcion_id)->with('error-message', $e->getMessage());
        }
    }

    public function anular_aprobacion_tutor($id)
    {
        $this->authorize('anular_aprobacion_tutor_anteproyectos_tesis_ubs');

        DB::beginTransaction();

        try {
            $anteproyecto = AnteproyectoTesisUbs::findOrFail($id);

            $anteproyecto->estado = 'CO';
            $anteproyecto->aprobado_tutor_id = null;
            $anteproyecto->fecha_aprobado_tutor = null;
            $anteproyecto->save();

            DB::commit();

            return redirect()->route('tesis_ubs.show_anteproyecto', $anteproyecto->inscripcion_id)->with('error-message', 'La aprobación del bloque ' . $anteproyecto->nombre . ' fue anulada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            $anteproyecto = AnteproyectoTesisUbs::findOrFail($id);
            return redirect()->route('tesis_ubs.show_anteproyecto', $anteproyecto->inscripcion_id)->with('error-message', $e->getMessage());
        }
    }

    public function aprobar_calidad(Request $request, $id)
    {
        $this->authorize('aprobar_calidad_anteproyectos_tesis_ubs');

        $request->validate([
            'puntaje_obtenido' => ['required', 'numeric', 'min:0', 'max:100']
        ]);

        DB::beginTransaction();

        try {
            $anteproyecto = AnteproyectoTesisUbs::findOrFail($id);
            $anteproyecto->estado = 'AC';
            $anteproyecto->aprobado_calidad_id = Auth::id();
            $anteproyecto->fecha_aprobado_calidad = Carbon::now();
            $anteproyecto->save();

            $cantidad_anteproyectos = AnteproyectoTesisUbs::where('inscripcion_id', $anteproyecto->inscripcion_id)->count();
            $cantidad_aprobados = AnteproyectoTesisUbs::where('inscripcion_id', $anteproyecto->inscripcion_id)->where('estado', 'AT')->orWhere('estado', 'AC')->count();

            $inscripcion = InscripcionTemaTesisUbs::findOrFail($anteproyecto->inscripcion_id);
            if ($cantidad_anteproyectos == $cantidad_aprobados) {
                $inscripcion->estado = 'AA';
            }

            $alumno_puntaje = new AlumnoPuntajeUbs();
            $alumno_puntaje->alumno_id = $inscripcion->alumno_id;
            $alumno_puntaje->curso_id = $inscripcion->curso_id;
            $alumno_puntaje->modulo_id = $anteproyecto->modulo_id;
            $alumno_puntaje->evaluacion_id = 2;
            $alumno_puntaje->puntos_obtenidos = $request->puntaje_obtenido;
            $alumno_puntaje->cargado_por_id = Auth::id();
            $alumno_puntaje->save();

            $alumno_nota = new AlumnoNotaUbs();
            $alumno_nota->alumno_id = $inscripcion->alumno_id;
            $alumno_nota->curso_id = $inscripcion->curso_id;
            $alumno_nota->modulo_id = $anteproyecto->modulo_id;
            $alumno_nota->evaluacion_id = 2;
            $alumno_nota->evaluacion = 'ORDINARIO';

            $escala = Escala::with('escalaDetalles')->where('programa_id', $inscripcion->curso->programa_id)->where('estado', 'AC')->first();
            foreach ($escala->escalaDetalles as $detalle) {
                if ($alumno_puntaje->puntos_obtenidos >= $detalle->punto_minimo && $alumno_puntaje->puntos_obtenidos <= $detalle->punto_maximo) {
                    $alumno_nota->calificacion = $detalle->nota;
                    $inscripcion->calificacion = $detalle->nota;
                }
            }
            $alumno_nota->save();
            $inscripcion->save();

            $inscripcion_modulo = InscripcionModulo::where('alumno_id', $alumno_puntaje->alumno_id)->where('curso_id', $alumno_puntaje->curso_id)->where('modulo_id', $alumno_puntaje->modulo_id)->first();
            if ($alumno_nota->calififacion != 1) {
                $inscripcion_modulo->estado = 'AP';
                $inscripcion_modulo->save();

                $orden_actual = CursoModulo::where('curso_id', $alumno_puntaje->curso_id)->where('modulo_id', $alumno_puntaje->modulo_id)->orderBy('id', 'asc')->first()->orden;
                $orden_nuevo = $orden_actual + 1;

                $curso_modulo = CursoModulo::where('curso_id', $alumno_puntaje->curso_id)->where('orden', $orden_nuevo)->orderBy('id', 'asc')->first();
                if ($curso_modulo) {
                    $nueva_inscripcion = new InscripcionModulo();
                    $nueva_inscripcion->alumno_id = $alumno_puntaje->alumno_id;
                    $nueva_inscripcion->curso_id = $alumno_puntaje->curso_id;
                    $nueva_inscripcion->modulo_id = $curso_modulo->modulo_id;
                    $nueva_inscripcion->save();
                }
            } else {
                $inscripcion_modulo->estado = 'RE';
                $inscripcion_modulo->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'El bloque fue aprobado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            $anteproyecto = AnteproyectoTesisUbs::findOrFail($id);
            return redirect()->route('tesis_ubs.show_anteproyecto', $anteproyecto->inscripcion_id)->with('error-message', $e->getMessage());
        }
    }

    public function anular_aprobacion_calidad($id)
    {
        $this->authorize('anular_aprobacion_calidad_anteproyectos_tesis_ubs');

        DB::beginTransaction();

        try {
            $anteproyecto = AnteproyectoTesisUbs::findOrFail($id);

            $cantidad_borradores_aprobados = BorradorTesisUbs::where('inscripcion_id', $anteproyecto->inscripcion_id)->where('estado', 'AT')->orWhere('estado', 'AC')->count();
            if ($cantidad_borradores_aprobados > 0) {
                return back()->with('error-message', 'La aprobación del bloque ' . $anteproyecto->bloque->nombre . ' no se puede anular. Ya cuenta con bloques de borradores aprobados.');
            } else {
                $borradores = BorradorTesisUbs::where('inscripcion_id', $anteproyecto->inscripcion_id)->get();
                if ($borradores) {
                    BorradorTesisUbs::where('inscripcion_id', $anteproyecto->inscripcion_id)->delete();
                }
                $anteproyecto->estado = 'AT';
                $anteproyecto->aprobado_calidad_id = null;
                $anteproyecto->fecha_aprobado_calidad = null;
                $anteproyecto->save();

                $alumno_puntaje = AlumnoPuntajeUbs::where('alumno_id', $anteproyecto->inscripcion->alumno_id)->where('curso_id', $anteproyecto->inscripcion->curso_id)->where('modulo_id', $anteproyecto->modulo_id)->first();
                $alumno_puntaje->delete();

                $alumno_nota = AlumnoNotaUbs::where('alumno_id', $anteproyecto->inscripcion->alumno_id)->where('curso_id', $anteproyecto->inscripcion->curso_id)->where('modulo_id', $anteproyecto->modulo_id)->first();
                $alumno_nota->delete();

                $inscripcion_modulo = InscripcionModulo::where('alumno_id', $anteproyecto->inscripcion->alumno_id)->where('curso_id', $anteproyecto->inscripcion->curso_id)->where('modulo_id', $anteproyecto->modulo_id)->first();
                $inscripcion_modulo->estado = 'MA';
                $inscripcion_modulo->save();

                $orden_actual = CursoModulo::where('curso_id', $anteproyecto->inscripcion->curso_id)->where('modulo_id', $anteproyecto->modulo_id)->orderBy('id', 'asc')->first()->orden;
                $orden_nuevo = $orden_actual + 1;
                $modulo_nuevo_id = CursoModulo::where('curso_id', $anteproyecto->inscripcion->curso_id)->where('orden', $orden_nuevo)->first()->modulo_id;

                $inscripcion_modulo_eliminar = InscripcionModulo::where('alumno_id', $anteproyecto->inscripcion->alumno_id)->where('curso_id', $anteproyecto->inscripcion->curso_id)->where('modulo_id', $modulo_nuevo_id)->first();
                if ($inscripcion_modulo_eliminar->estado != 'MA') {
                    switch ($inscripcion_modulo_eliminar->estado) {
                        case 'AP':
                            $tipo = 'aprobado';
                            break;
                        case 'RE':
                            $tipo = 'reprobado';
                            break;
                        default:
                            break;
                    }
                    return redirect()->route('tesis_ubs.show_anteproyecto', $anteproyecto->inscripcion_id)->with('error-message', 'La aprobación del bloque ' . $anteproyecto->nombre . ' no se puede anular. El módulo ya se encuentra ' . $tipo . '.');
                } else {
                    $inscripcion_modulo_eliminar->delete();
                }

                $inscripcion = InscripcionTemaTesisUbs::findOrFail($anteproyecto->inscripcion_id)->first();
                $inscripcion->estado = 'EC';
                $inscripcion->save();
            }

            DB::commit();

            return redirect()->route('tesis_ubs.show_anteproyecto', $anteproyecto->inscripcion_id)->with('error-message', 'La aprobación del bloque ' . $anteproyecto->nombre . ' fue anulada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            $anteproyecto = AnteproyectoTesisUbs::findOrFail($id);
            return redirect()->route('tesis_ubs.show_anteproyecto', $anteproyecto->inscripcion_id)->with('error-message', $e->getMessage());
        }
    }

}
