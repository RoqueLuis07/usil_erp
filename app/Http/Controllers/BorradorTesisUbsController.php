<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

use App\Models\BorradorTesisUbs;
use App\Models\InscripcionTemaTesisUbs;
use App\Models\BloqueBorradorTesisUbs;
use App\Models\EntregaBorradorTesisUbs;
use App\Models\CursoModulo;
use App\Models\Escala;
use App\Models\AlumnoPuntajeUbs;
use App\Models\AlumnoNotaUbs;
use App\Models\InscripcionModulo;
use App\Models\PagoTesisUbs;
use App\Models\Curso;

class BorradorTesisUbsController extends Controller
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
        $this->authorize('ver_borradores_tesis_ubs');

        try {
            $tesis = InscripcionTemaTesisUbs::with(['borradores' => function ($query) {
                $query->orderBy('numero_bloque', 'asc');
            }])->findOrFail($id);
            $escala = Escala::with('escalaDetalles')->where('programa_id', $tesis->curso->programa_id)->first();
            return view('ubs/tesis/borradores/show')->with(compact('tesis', 'escala'));
        } catch (\Exception $e) {
            return redirect()->route('tesis_ubs.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function create($id)
    {
        $this->authorize('crear_borradores_tesis_ubs');

        try {
            $tesis = InscripcionTemaTesisUbs::findOrFail($id);
            $bloques = BloqueBorradorTesisUbs::where('estado', 'AC')->get();
            $modulos = CursoModulo::where('curso_id', $tesis->curso_id)->get();

            return view('ubs/tesis/borradores/create')->with(compact('tesis', 'bloques', 'modulos'));
        } catch (\Exception $e) {
            return redirect()->route('tesis_ubs.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request, $id)
    {
        $this->authorize('crear_borradores_tesis_ubs');

        $request->validate([
            'detalles' => ['required', 'array'],
            'detalles.*.bloque' => ['required', 'numeric'],
            'detalles.*.modulo' => ['required', 'numeric']
        ]);

        DB::beginTransaction();

        try {
            $tesis = InscripcionTemaTesisUbs::findOrFail($id);

            foreach ($request->detalles as $detalle) {
                $borrador = new BorradorTesisUbs();
                $borrador->inscripcion_id = $tesis->id;
                $bloque = BloqueBorradorTesisUbs::findOrFail($detalle['bloque']);
                $borrador->bloque_id = $bloque->id;
                $borrador->numero_bloque = $bloque->numero;
                $borrador->modulo_id = $detalle['modulo'];
                $borrador->save();
            }

            DB::commit();

            return redirect()->route('tesis_ubs.show', $id)->with('success-message', 'El borrador de la tesis fue generado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tesis_ubs.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function show_entregas($id)
    {
        $this->authorize('ver_entregas_borradores_tesis_ubs');

        try {
            $borrador = BorradorTesisUbs::with(['entregas' => function ($query) {
                $query->orderBy('numero', 'desc');
            }])->findOrFail($id);

            return view('ubs/tesis/borradores/show_entregas')->with(compact('borrador'));
        } catch (\Exception $e) {
            return redirect()->route('tesis_ubs.show_borrador', $id)->with('error-message', $e->getMessage());
        }
    }

    public function save_entrega(Request $request, $id)
    {
        $this->authorize('entregar_borradores_tesis_ubs');

        $request->validate([
            'archivo' => ['required', 'file', 'extensions:doc,docx'],
            'comentario' => 'nullable'
        ]);

        DB::beginTransaction();

        try {
            $borrador = BorradorTesisUbs::findOrFail($id);
            $borrador->estado = 'EN';
            $borrador->save();

            $ultima_entrega = EntregaBorradorTesisUbs::where('borrador_id', $borrador->id)->orderBy('id', 'desc')->first();
            if ($ultima_entrega) {
                $numero_entrega = $ultima_entrega->numero + 1;
            } else {
                $numero_entrega = 1;
            }

            $cantidad_borradores = BorradorTesisUbs::where('inscripcion_id', $borrador->inscripcion_id)->count();

            $archivo = $request->archivo;
            $extension = $archivo->getClientOriginalExtension();
            $directorio = 'storage/maestrias/tesis/borradores/entregas';
            $directorio_storage = 'public/maestrias/tesis/borradores/entregas';

            $nombre = 'borrador_bloque_' . $borrador->bloque->numero . '_' . Str::lower(str_replace(' ', '_', $borrador->inscripcion->tema) . '_' . $numero_entrega);
            $nombre_archivo = $nombre . '.' . $extension;
            Storage::putFileAs($directorio_storage . '/', $archivo, $nombre_archivo);

            $entrega = new EntregaBorradorTesisUbs();
            $entrega->borrador_id = $borrador->id;
            $entrega->url_archivo = $directorio . '/' . $nombre_archivo;
            $entrega->comentario = removeAccents(Str::upper($request->comentario));
            $entrega->numero = $numero_entrega;
            $entrega->save();

            $inscripcion = InscripcionTemaTesisUbs::findOrFail($borrador->inscripcion_id);
            if ($inscripcion->estado == 'AA') {
                $inscripcion->estado = 'BC';
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
                return redirect()->route('tesis_ubs.show_entregas_borrador', $id)->with('error-message', $e->getMessage());
            }
        }
    }

    public function save_correccion(Request $request, $id)
    {
        $this->authorize('corregir_borradores_tesis_ubs');

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

            $borrador = BorradorTesisUbs::findOrFail($id);
            $borrador->estado = 'CO';
            $borrador->save();

            $ultima_entrega = EntregaBorradorTesisUbs::where('borrador_id', $borrador->id)->orderBy('id', 'desc')->first();
            if ($ultima_entrega) {
                $numero_entrega = $ultima_entrega->numero + 1;
            } else {
                $numero_entrega = 1;
            }

            if ($request->archivo != null) {
                $archivo = $request->archivo;
                $extension = $archivo->getClientOriginalExtension();
                $directorio = 'storage/maestrias/tesis/borradores/correcciones';
                $directorio_storage = 'public/maestrias/tesis/borradores/correcciones';

                $nombre = 'borrador_bloque_' . $borrador->bloque->numero . '_' . Str::lower(str_replace(' ', '_', $borrador->inscripcion->tema)) . '_' . $numero_entrega;
                $nombre_archivo = $nombre . '.' . $extension;
                Storage::putFileAs($directorio_storage . '/', $archivo, $nombre_archivo);

                $entrega = new EntregaBorradorTesisUbs();
                $entrega->borrador_id = $borrador->id;
                $entrega->url_archivo = $directorio . '/' . $nombre_archivo;
                $entrega->comentario = removeAccents(Str::upper($request->comentario));
                $entrega->entrega = false;
                $entrega->numero = $numero_entrega;
                $entrega->save();
            } else {
                $entrega = new EntregaBorradorTesisUbs();
                $entrega->borrador_id = $borrador->id;
                $entrega->comentario = removeAccents(Str::upper($request->comentario));
                $entrega->entrega = false;
                $entrega->numero = $numero_entrega;
                $entrega->save();
            }

            $inscripcion = InscripcionTemaTesisUbs::findOrFail($borrador->inscripcion_id);
            if ($inscripcion->estado == 'AA') {
                $inscripcion->estado = 'BC';
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
                return redirect()->route('tesis_ubs.show_entregas_borrador', $id)->with('error-message', $e->getMessage());
            }
        }
    }

    public function delete_entrega($id)
    {
        $this->authorize('eliminar_entregas_borradores_tesis_ubs');

        DB::beginTransaction();

        try {
            $entrega = EntregaBorradorTesisUbs::findOrFail($id);
            $ubicacion_archivo = $entrega->url_archivo;
            $ubicacion_archivo = str_replace('storage', 'public', $ubicacion_archivo);
            $borrador = BorradorTesisUbs::findOrFail($entrega->borrador_id);

            if ($entrega->entrega == false) {
                $tipo = 'corrección';
            } else {
                $tipo = 'entrega';
            }

            if ($borrador->estado == 'AC' || $borrador->estado == 'AT') {
                return back()->with('error-message', 'No se puede eliminar la ' . $tipo . ' el bloque ya se encuentra aprobado.');
            }

            Storage::delete($ubicacion_archivo);
            $entrega->delete();

            $total_entregas = EntregaBorradorTesisUbs::where('borrador_id', $borrador->id)->count();

            if ($total_entregas == 0) {
                $borrador->estado = 'PE';
                $borrador->save();

                $inscripcion = InscripcionTemaTesisUbs::findOrFail($borrador->inscripcion_id);
                $inscripcion->estado = 'AA';
                $inscripcion->save();
            }

            DB::commit();

            if (Auth::user()->hasRole('ALUMNO')) {
                return redirect()->route('pantallas_alumnos.show_entregas_borradores_tesis', $borrador->id)->with('error-message', 'La ' . $tipo . ' fue eliminada exitosamente.');
            } else {
                return redirect()->route('tesis_ubs.show_entregas_borrador', $borrador->id)->with('error-message', 'La ' . $tipo . ' fue eliminada exitosamente.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            $entrega = EntregaBorradorTesisUbs::findOrFail($id);
            $borrador = BorradorTesisUbs::findOrFail($entrega->borrador_id);

            if (Auth::user()->hasRole('ALUMNO')) {
                return redirect()->route('pantallas_alumnos.show_entregas_borradores_tesis', $borrador->id)->with('error-message', $e->getMessage());
            } else {
                return redirect()->route('tesis_ubs.show_entregas_borrador', $borrador->id)->with('error-message', $e->getMessage());
            }


        }
    }

    public function aprobar_tutor($id)
    {
        $this->authorize('aprobar_tutor_borradores_tesis_ubs');

        DB::beginTransaction();

        try {
            $borrador = BorradorTesisUbs::findOrFail($id);
            $borrador->estado = 'AT';
            $borrador->aprobado_tutor_id = Auth::id();
            $borrador->fecha_aprobado_tutor = Carbon::now();
            $borrador->save();

            DB::commit();

            return redirect()->route('tesis_ubs.show_borrador', $borrador->inscripcion_id)->with('success-message', 'El bloque ' . $borrador->nombre . ' fue aprobado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            $borrador = BorradorTesisUbs::findOrFail($id);
            return redirect()->route('tesis_ubs.show_borrador', $borrador->inscripcion_id)->with('error-message', $e->getMessage());
        }
    }

    public function anular_aprobacion_tutor($id)
    {
        $this->authorize('anular_aprobacion_tutor_borradores_tesis_ubs');

        DB::beginTransaction();

        try {
            $borrador = BorradorTesisUbs::findOrFail($id);

            $borrador->estado = 'CO';
            $borrador->aprobado_tutor_id = null;
            $borrador->fecha_aprobado_tutor = null;
            $borrador->save();

            DB::commit();

            return redirect()->route('tesis_ubs.show_borrador', $borrador->inscripcion_id)->with('error-message', 'La aprobación del bloque ' . $borrador->nombre . ' fue anulada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            $borrador = BorradorTesisUbs::findOrFail($id);
            return redirect()->route('tesis_ubs.show_borrador', $borrador->inscripcion_id)->with('error-message', $e->getMessage());
        }
    }

    public function aprobar_calidad(Request $request, $id)
    {
        $this->authorize('aprobar_calidad_borradores_tesis_ubs');

        DB::beginTransaction();

        try {
            $borrador = BorradorTesisUbs::findOrFail($id);
            $borrador->estado = 'AC';
            $borrador->aprobado_calidad_id = Auth::id();
            $borrador->fecha_aprobado_calidad = Carbon::now();
            $borrador->save();

            $cantidad_borradores = BorradorTesisUbs::where('inscripcion_id', $borrador->inscripcion_id)->count();
            $cantidad_aprobados = BorradorTesisUbs::where('inscripcion_id', $borrador->inscripcion_id)->where('estado', 'AT')->orWhere('estado', 'AC')->count();

            $inscripcion = InscripcionTemaTesisUbs::findOrFail($borrador->inscripcion_id);
            if ($cantidad_borradores == $cantidad_aprobados) {
                $inscripcion->estado = 'AB';
            }

            $alumno_puntaje = new AlumnoPuntajeUbs();
            $alumno_puntaje->alumno_id = $inscripcion->alumno_id;
            $alumno_puntaje->curso_id = $inscripcion->curso_id;
            $alumno_puntaje->modulo_id = $borrador->modulo_id;
            $alumno_puntaje->evaluacion_id = 2;
            $alumno_puntaje->puntos_obtenidos = $request->puntaje_obtenido;
            $alumno_puntaje->cargado_por_id = Auth::id();
            $alumno_puntaje->save();

            $alumno_nota = new AlumnoNotaUbs();
            $alumno_nota->alumno_id = $inscripcion->alumno_id;
            $alumno_nota->curso_id = $inscripcion->curso_id;
            $alumno_nota->modulo_id = $borrador->modulo_id;
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

                $cantidad_borradores_aprobados = BorradorTesisUbs::where('inscripcion_id', $borrador->inscripcion_id)->where('estado', 'AT')->orWhere('estado', 'AC')->count();
                $cantidad_borradores = BorradorTesisUbs::where('inscripcion_id', $borrador->inscripcion_id)->count();
                if ($cantidad_borradores_aprobados == $cantidad_borradores) {
                    $pago_defensa = new PagoTesisUbs();
                    $pago_defensa->inscripcion_id = $inscripcion->id;
                    $pago_defensa->descripcion = 'DEFENSA DE TESIS';
                    $pago_defensa->vencimiento = Carbon::now();
                    $pago_defensa->monto = $inscripcion->curso->precios->precio_defensa;
                    $pago_defensa->saldo = $inscripcion->curso->precios->precio_defensa;
                    $pago_defensa->save();

                    $pago_titulo = new PagoTesisUbs();
                    $pago_titulo->inscripcion_id = $inscripcion->id;
                    $pago_titulo->descripcion = 'TITULACIÓN';
                    $pago_titulo->vencimiento = Carbon::now();
                    $pago_titulo->monto = $inscripcion->curso->precios->precio_titulo;
                    $pago_titulo->saldo = $inscripcion->curso->precios->precio_titulo;
                    $pago_titulo->save();
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
            $borrador = BorradorTesisUbs::findOrFail($id);
            return redirect()->route('tesis_ubs.show_borrador', $borrador->inscripcion_id)->with('error-message', $e->getMessage());
        }
    }

    public function anular_aprobacion_calidad($id)
    {
        $this->authorize('anular_aprobacion_calidad_borradores_tesis_ubs');

        DB::beginTransaction();

        try {
            $borrador = BorradorTesisUbs::findOrFail($id);
            $borrador->estado = 'AT';
            $borrador->aprobado_calidad_id = null;
            $borrador->fecha_aprobado_calidad = null;
            $borrador->save();

            $alumno_puntaje = AlumnoPuntajeUbs::where('alumno_id', $borrador->inscripcion->alumno_id)->where('curso_id', $borrador->inscripcion->curso_id)->where('modulo_id', $borrador->modulo_id)->first();
            $alumno_puntaje->delete();

            $alumno_nota = AlumnoNotaUbs::where('alumno_id', $borrador->inscripcion->alumno_id)->where('curso_id', $borrador->inscripcion->curso_id)->where('modulo_id', $borrador->modulo_id)->first();
            $alumno_nota->delete();

            $inscripcion_modulo = InscripcionModulo::where('alumno_id', $borrador->inscripcion->alumno_id)->where('curso_id', $borrador->inscripcion->curso_id)->where('modulo_id', $borrador->modulo_id)->first();
            $inscripcion_modulo->estado = 'MA';
            $inscripcion_modulo->save();

            $orden_actual = CursoModulo::where('curso_id', $borrador->inscripcion->curso_id)->where('modulo_id', $borrador->modulo_id)->orderBy('id', 'asc')->first()->orden;
            $orden_nuevo = $orden_actual + 1;
            $modulo_nuevo = CursoModulo::where('curso_id', $borrador->inscripcion->curso_id)->where('orden', $orden_nuevo)->first();

            if ($modulo_nuevo) {
                $inscripcion_modulo_eliminar = InscripcionModulo::where('alumno_id', $borrador->inscripcion->alumno_id)->where('curso_id', $borrador->inscripcion->curso_id)->where('modulo_id', $modulo_nuevo->id)->first();
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
                    return redirect()->route('tesis_ubs.show_borrador', $borrador->inscripcion_id)->with('error-message', 'La aprobación del bloque ' . $borrador->nombre . ' no se puede anular. El módulo ya se encuentra ' . $tipo . '.');
                } else {
                    $inscripcion_modulo_eliminar->delete();
                }
            }

            $inscripcion = InscripcionTemaTesisUbs::findOrFail($borrador->inscripcion_id)->first();
            $inscripcion->estado = 'BC';
            $inscripcion->save();

            DB::commit();

            return redirect()->route('tesis_ubs.show_borrador', $borrador->inscripcion_id)->with('error-message', 'La aprobación del bloque ' . $borrador->nombre . ' fue anulada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            $borrador = BorradorTesisUbs::findOrFail($id);
            return redirect()->route('tesis_ubs.show_borrador', $borrador->inscripcion_id)->with('error-message', $e->getMessage());
        }
    }

}
