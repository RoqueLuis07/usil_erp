<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

use App\Models\BorradorTesis;
use App\Models\InscripcionTemaTesis;
use App\Models\EntregaBorradorTesis;
use App\Models\PagoTesis;
use App\Models\RequerimientoEntregaTesis;
use App\Models\Articulo;

class BorradorTesisController extends Controller
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
        $this->authorize('ver_borradores_tesis');

        try {
            $temas = InscripcionTemaTesis::whereHas('borradores')->orderBy('id', 'desc')->get();
            return view('tesis/borradores/index')->with(compact('temas'));
        } catch (\Exception $e) {
            return redirect()->route('borradores_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_borradores_tesis');

        try {
            $tema = InscripcionTemaTesis::with(['borradores' => function ($query) {
                $query->orderBy('numero_bloque', 'asc');
            }])->findOrFail($id);
            return view('tesis/borradores/show')->with(compact('tema'));
        } catch (\Exception $e) {
            return redirect()->route('borradores_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function show_entregas($id)
    {
        $this->authorize('ver_entregas_borradores_tesis');

        try {
            $borrador = BorradorTesis::with(['entregas' => function ($query) {
                $query->orderBy('numero', 'desc');
            }])->findOrFail($id);

            return view('tesis/borradores/show_entregas')->with(compact('borrador'));
        } catch (\Exception $e) {
            return redirect()->route('borradores_tesis.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function save_entrega(Request $request, $id)
    {
        $this->authorize('entregar_borradores_tesis');

        $request->validate([
            'archivo' => ['required', 'file', 'extensions:doc,docx'],
            'comentario' => 'nullable'
        ]);

        DB::beginTransaction();

        try {
            $borrador = BorradorTesis::findOrFail($id);
            $borrador->estado = 'EN';
            $borrador->save();

            $ultima_entrega = EntregaBorradorTesis::where('borrador_id', $borrador->id)->orderBy('id', 'desc')->first();
            if ($ultima_entrega) {
                $numero_entrega = $ultima_entrega->numero + 1;
            } else {
                $numero_entrega = 1;
            }

            $fecha_entrega = RequerimientoEntregaTesis::where('fecha_inicio_borrador', '<=', Carbon::now())->where('fecha_fin_borrador', '>=', Carbon::now())->first();
            if (!$fecha_entrega) {
                return response()->json([
                    'mensaje' => 'La entrega no se puede realizar, el día de hoy no se encuentra dentro del rango para entregas de borrador.',
                ]);
            }

            $archivo = $request->archivo;
            $extension = $archivo->getClientOriginalExtension();
            $directorio = 'storage/tesis/borradores/entregas';
            $directorio_storage = 'public/tesis/borradores/entregas';

            $nombre = 'borrador_bloque_' . $borrador->bloque->numero . '_' . Str::lower(str_replace(' ', '_', $borrador->inscripcion->tema) . '_' . $numero_entrega);
            $nombre_archivo = $nombre . '.' . $extension;
            Storage::putFileAs($directorio_storage . '/', $archivo, $nombre_archivo);

            $entrega = new EntregaBorradorTesis();
            $entrega->borrador_id = $borrador->id;
            $entrega->url_archivo = $directorio . '/' . $nombre_archivo;
            $entrega->comentario = removeAccents(Str::upper($request->comentario));
            $entrega->numero = $numero_entrega;
            $entrega->save();

            $inscripcion = InscripcionTemaTesis::findOrFail($borrador->inscripcion_id);
            if ($inscripcion->estado == 'AP') {
                $inscripcion->estado = 'BC';
                $inscripcion->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'La entrega fue realizada existosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('borradores_tesis.show_entregas', $id)->with('error-message', $e->getMessage());
        }
    }

    public function save_correccion(Request $request, $id)
    {
        $this->authorize('corregir_borradores_tesis');

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

            $borrador = BorradorTesis::findOrFail($id);
            $borrador->estado = 'CO';
            $borrador->save();

            $ultima_entrega = EntregaBorradorTesis::where('borrador_id', $borrador->id)->orderBy('id', 'desc')->first();
            if ($ultima_entrega) {
                $numero_entrega = $ultima_entrega->numero + 1;
            } else {
                $numero_entrega = 1;
            }

            if ($request->archivo != null) {
                $archivo = $request->archivo;
                $extension = $archivo->getClientOriginalExtension();
                $directorio = 'storage/tesis/borradores/correcciones';
                $directorio_storage = 'public/tesis/borradores/correcciones';

                $nombre = 'borrador_bloque_' . $borrador->bloque->numero . '_' . Str::lower(str_replace(' ', '_', $borrador->inscripcion->tema)) . '_' . $numero_entrega;
                $nombre_archivo = $nombre . '.' . $extension;
                Storage::putFileAs($directorio_storage . '/', $archivo, $nombre_archivo);

                $entrega = new EntregaBorradorTesis();
                $entrega->borrador_id = $borrador->id;
                $entrega->url_archivo = $directorio . '/' . $nombre_archivo;
                $entrega->comentario = removeAccents(Str::upper($request->comentario));
                $entrega->entrega = false;
                $entrega->numero = $numero_entrega;
                $entrega->save();
            } else {
                $entrega = new EntregaBorradorTesis();
                $entrega->borrador_id = $borrador->id;
                $entrega->comentario = removeAccents(Str::upper($request->comentario));
                $entrega->entrega = false;
                $entrega->numero = $numero_entrega;
                $entrega->save();
            }

            $inscripcion = InscripcionTemaTesis::findOrFail($borrador->inscripcion_id);
            if ($inscripcion->estado == 'AP') {
                $inscripcion->estado = 'BC';
                $inscripcion->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'La corrección/comentario fue guardada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('borradores_tesis.show_entregas', $id)->with('error-message', $e->getMessage());
        }
    }

    public function delete_entrega($id)
    {
        $this->authorize('eliminar_entregas_borradores_tesis');

        DB::beginTransaction();

        try {
            $entrega = EntregaBorradorTesis::findOrFail($id);
            $ubicacion_archivo = $entrega->url_archivo;
            $ubicacion_archivo = str_replace('storage', 'public', $ubicacion_archivo);
            $borrador = BorradorTesis::findOrFail($entrega->borrador_id);

            if ($entrega->entrega == false) {
                $tipo = 'corrección';
            } else {
                $tipo = 'entrega';
            }

            if ($borrador->estado == 'AP') {
                return back()->with('error-message', 'No se puede eliminar la ' . $tipo . ' el bloque ya se encuentra aprobado.');
            }

            Storage::delete($ubicacion_archivo);
            $entrega->delete();

            $total_entregas = EntregaBorradorTesis::where('borrador_id', $borrador->id)->count();

            if ($total_entregas == 0) {
                $borrador->estado = 'PE';
                $borrador->save();

                $inscripcion = InscripcionTemaTesis::findOrFail($borrador->inscripcion_id);
                $inscripcion->estado = 'AP';
                $inscripcion->save();
            }

            DB::commit();

            return redirect()->route('borradores_tesis.show_entregas', $borrador->id)->with('error-message', 'La ' . $tipo . ' fue eliminada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            $entrega = EntregaBorradorTesis::findOrFail($id);
            $borrador = BorradorTesis::findOrFail($entrega->borrador_id);
            return redirect()->route('borradores_tesis.show_entregas', $borrador->id)->with('error-message', $e->getMessage());
        }
    }

    public function aprobar($id)
    {
        $this->authorize('aprobar_bloques_borradores_tesis');

        DB::beginTransaction();

        try {
            $borrador = BorradorTesis::findOrFail($id);
            $borrador->estado = 'AP';
            $borrador->aprobado_tutor_id = Auth::id();
            $borrador->fecha_aprobado_tutor = Carbon::now();
            $borrador->save();

            $cantidad_borradores = BorradorTesis::where('inscripcion_id', $borrador->inscripcion_id)->count();
            $cantidad_aprobados = BorradorTesis::where('inscripcion_id', $borrador->inscripcion_id)->where('estado', 'AP')->count();

            if ($cantidad_borradores == $cantidad_aprobados) {
                $inscripcion = InscripcionTemaTesis::findOrFail($borrador->inscripcion_id);
                $inscripcion->estado = 'BP';
                $inscripcion->save();

                if (!PagoTesis::where('inscripcion_id', $id)->exists()) {
                    $articulo = Articulo::where('id', $inscripcion->carrera->articulo_id)->first();

                    if ($articulo) {
                        $pago = new PagoTesis();
                        $pago->inscripcion_id = $id;
                        $pago->descripcion = 'TRABAJO FINAL ACADEMICO';
                        $pago->vencimiento = Carbon::now();
                        $pago->monto = $articulo->detalle->precio_titulo + $articulo->detalle->precio_defensa;
                        $pago->saldo = $articulo->detalle->precio_titulo + $articulo->detalle->precio_defensa;
                        $pago->save();
                    } else {
                        return back()->with('error-message', 'El borrador no se puede aprobar. No se puede obtener el artículo asociado a la carrera.');
                    }
                }
            }

            DB::commit();

            return redirect()->route('borradores_tesis.show', $borrador->inscripcion_id)->with('success-message', 'El bloque ' . $borrador->nombre . ' fue aprobado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            $borrador = BorradorTesis::findOrFail($id);
            return redirect()->route('borradores_tesis.show', $borrador->inscripcion_id)->with('error-message', $e->getMessage());
        }
    }

    public function anular_aprobacion($id)
    {
        $this->authorize('anular_aprobacion_bloques_borradores_tesis');

        DB::beginTransaction();

        try {
            $borrador = BorradorTesis::findOrFail($id);

            $inscripcion = InscripcionTemaTesis::findOrFail($borrador->inscripcion_id)->first();
            if ($inscripcion->fecha_defensa) {
                return back()->with('error-message', 'La aprobación de la entrega ' . $borrador->bloque->numero . 'no se puede anular. Ya cuenta con fecha de defensa.');
            } else {
                $borrador->estado = 'CO';
                $borrador->aprobado_tutor_id = null;
                $borrador->fecha_aprobado_tutor = null;
                $borrador->save();

                $inscripcion->estado = 'BC';
                $inscripcion->save();
            }



            DB::commit();

            return redirect()->route('borradores_tesis.show', $borrador->inscripcion_id)->with('error-message', 'La aprobación del bloque ' . $borrador->nombre . ' fue anulada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            $borrador = BorradorTesis::findOrFail($id);
            return redirect()->route('borradores_tesis.show', $borrador->inscripcion_id)->with('error-message', $e->getMessage());
        }
    }

    public function aprobar_todos($id)
    {
        $this->authorize('aprobar_todos_bloques_borradores_tesis');

        DB::beginTransaction();

        try {
            $borradores = BorradorTesis::where('inscripcion_id', $id)->get();
            foreach ($borradores as $borrador) {
                if ($borrador->estado != 'AP') {
                    $borrador->estado = 'AP';
                    $borrador->aprobado_tutor_id = Auth::id();
                    $borrador->fecha_aprobado_tutor = Carbon::now();
                    $borrador->save();
                }
            }

            if (!PagoTesis::where('inscripcion_id', $id)->exists()) {
                $inscripcion = InscripcionTemaTesis::findOrFail($borrador->inscripcion_id);
                $articulo = Articulo::where('id', $inscripcion->carrera->articulo_id)->first();

                if ($articulo) {
                    $pago = new PagoTesis();
                    $pago->inscripcion_id = $id;
                    $pago->descripcion = 'TRABAJO FINAL ACADEMICO';
                    $pago->vencimiento = Carbon::now();
                    $pago->monto = $articulo->detalle->precio_titulo + $articulo->detalle->precio_defensa;
                    $pago->saldo = $articulo->detalle->precio_titulo + $articulo->detalle->precio_defensa;
                    $pago->save();
                } else {
                    return back()->with('error-message', 'El borrador no se puede aprobar. No se puede obtener el artículo asociado a la carrera.');
                }
            }

            $inscripcion = InscripcionTemaTesis::findOrFail($id);
            $inscripcion->estado = 'AB';
            $inscripcion->save();

            DB::commit();

            return redirect()->route('borradores_tesis.show', $id)->with('success-message', 'Todas las entregas fueron aprobadas exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('borradores_tesis.show', $id)->with('error_message',$e->getMessage());
        }
    }

    public function desaprobar_todos($id)
    {
        $this->authorize('desaprobar_todos_bloques_borradores_tesis');

        DB::beginTransaction();

        try {
            $borradores = BorradorTesis::where('inscripcion_id', $id)->get();
            $inscripcion = InscripcionTemaTesis::findOrFail($id)->first();

            if ($inscripcion->fecha_defensa) {
                return back()->with('error-message', 'Las aprobaciones de las entregas no se pueden anular. Ya cuenta con fecha de defensa.');
            } else {
                $pago = PagoTesis::where('inscripcion_id', $id)->whereIn('estado', ['PA', 'CA'])->first();
                if ($pago) {
                    if ($pago->estado == 'PA') {
                        $tipo = 'un pago parcial.';
                    } else {
                        $tipo = 'el pago.';
                    }
                    return back()->with('error-message', 'Las aprobaciones de las entregas no se pueden anular. El alumno ya realizó ' . $tipo);
                } else {
                    foreach ($borradores as $borrador) {
                        $borrador->estado = 'PE';
                        $entregas = EntregaBorradorTesis::where('borrador_id', $borrador->id)->get();
                        if ($entregas) {
                            foreach ($entregas as $entrega) {
                                if ($entrega->entrega == true) {
                                    $borrador->estado = 'EN';
                                } else if ($entrega->entrega == false) {
                                    $borrador->estado = 'CO';
                                }
                            }
                        }

                        $borrador->aprobado_tutor_id = null;
                        $borrador->fecha_aprobado_tutor = null;
                        $borrador->save();

                        $inscripcion->estado = 'BC';
                        $inscripcion->save();

                        PagoTesis::where('inscripcion_id', $inscripcion->id)->delete();
                    }
                }
            }

            DB::commit();

            return redirect()->route('borradores_tesis.show', $id)->with('error-message', 'Todas las aprobaciones de entregas fueron anuladas exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('borradores_tesis.show', $id)->with('error-message', $e->getMessage());
        }
    }

}
