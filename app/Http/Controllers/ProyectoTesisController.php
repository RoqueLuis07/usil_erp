<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

use App\Models\ProyectoTesis;
use App\Models\InscripcionTemaTesis;
use App\Models\EntregaProyectoTesis;
use App\Models\BloqueBorradorTesis;
use App\Models\BorradorTesis;
use App\Models\RequerimientoEntregaTesis;

class ProyectoTesisController extends Controller
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
        $this->authorize('ver_proyectos_tesis');

        try {
            $temas = InscripcionTemaTesis::whereHas('proyectos')->orderBy('id', 'desc')->get();
            return view('tesis/proyectos/index')->with(compact('temas'));
        } catch (\Exception $e) {
            return redirect()->route('proyectos_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_proyectos_tesis');

        try {
            $tema = InscripcionTemaTesis::with(['proyectos' => function ($query) {
                $query->orderBy('numero_bloque', 'asc');
            }])->findOrFail($id);
            return view('tesis/proyectos/show')->with(compact('tema'));
        } catch (\Exception $e) {
            return redirect()->route('proyectos_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function show_entregas($id)
    {
        $this->authorize('ver_entregas_proyectos_tesis');

        try {
            $proyecto = ProyectoTesis::with(['entregas' => function ($query) {
                $query->orderBy('numero', 'desc');
            }])->findOrFail($id);

            return view('tesis/proyectos/show_entregas')->with(compact('proyecto'));
        } catch (\Exception $e) {
            return redirect()->route('proyectos_tesis.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function save_entrega(Request $request, $id)
    {
        $this->authorize('entregar_proyectos_tesis');

        $request->validate([
            'archivo' => ['required', 'file', 'extensions:doc,docx'],
            'comentario' => 'nullable'
        ]);

        DB::beginTransaction();

        try {
            $proyecto = ProyectoTesis::findOrFail($id);
            $proyecto->estado = 'EN';
            $proyecto->save();

            $ultima_entrega = EntregaProyectoTesis::where('proyecto_id', $proyecto->id)->orderBy('id', 'desc')->first();
            if ($ultima_entrega) {
                $numero_entrega = $ultima_entrega->numero + 1;
            } else {
                $numero_entrega = 1;
            }

            $cantidad_proyectos = ProyectoTesis::where('inscripcion_id', $proyecto->inscripcion_id)->count();
            $fecha_entrega = RequerimientoEntregaTesis::where('fecha_inicio_proyecto', '<=', Carbon::now())->where('fecha_fin_proyecto', '>=', Carbon::now())->first();
            if ($cantidad_proyectos == $proyecto->numero_bloque && !$fecha_entrega) {
                return response()->json([
                    'mensaje' => 'La entrega no se puede realizar, el día de hoy no se encuentra dentro del rango para entregas de proyecto.',
                ]);
            }

            $archivo = $request->archivo;
            $extension = $archivo->getClientOriginalExtension();
            $directorio = 'storage/tesis/proyectos/entregas';
            $directorio_storage = 'public/tesis/proyectos/entregas';

            $nombre = 'proyecto_bloque_' . $proyecto->bloque->numero . '_' . Str::lower(str_replace(' ', '_', $proyecto->inscripcion->tema) . '_' . $numero_entrega);
            $nombre_archivo = $nombre . '.' . $extension;
            Storage::putFileAs($directorio_storage . '/', $archivo, $nombre_archivo);

            $entrega = new EntregaProyectoTesis();
            $entrega->proyecto_id = $proyecto->id;
            $entrega->url_archivo = $directorio . '/' . $nombre_archivo;
            $entrega->comentario = removeAccents(Str::upper($request->comentario));
            $entrega->numero = $numero_entrega;
            $entrega->save();

            $inscripcion = InscripcionTemaTesis::findOrFail($proyecto->inscripcion_id);
            if ($inscripcion->estado == 'AA') {
                $inscripcion->estado = 'PC';
                $inscripcion->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'La entrega fue realizada existosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('proyectos_tesis.show_entregas', $id)->with('error-message', $e->getMessage());
        }
    }

    public function save_correccion(Request $request, $id)
    {
        $this->authorize('corregir_proyectos_tesis');

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

            $proyecto = ProyectoTesis::findOrFail($id);
            $proyecto->estado = 'CO';
            $proyecto->save();

            $ultima_entrega = EntregaProyectoTesis::where('proyecto_id', $proyecto->id)->orderBy('id', 'desc')->first();
            if ($ultima_entrega) {
                $numero_entrega = $ultima_entrega->numero + 1;
            } else {
                $numero_entrega = 1;
            }

            if ($request->archivo != null) {
                $archivo = $request->archivo;
                $extension = $archivo->getClientOriginalExtension();
                $directorio = 'storage/tesis/proyectos/correcciones';
                $directorio_storage = 'public/tesis/proyectos/correcciones';

                $nombre = 'proyecto_bloque_' . $proyecto->bloque->numero . '_' . Str::lower(str_replace(' ', '_', $proyecto->inscripcion->tema)) . '_' . $numero_entrega;
                $nombre_archivo = $nombre . '.' . $extension;
                Storage::putFileAs($directorio_storage . '/', $archivo, $nombre_archivo);

                $entrega = new EntregaProyectoTesis();
                $entrega->proyecto_id = $proyecto->id;
                $entrega->url_archivo = $directorio . '/' . $nombre_archivo;
                $entrega->comentario = removeAccents(Str::upper($request->comentario));
                $entrega->entrega = false;
                $entrega->numero = $numero_entrega;
                $entrega->save();
            } else {
                $entrega = new EntregaProyectoTesis();
                $entrega->proyecto_id = $proyecto->id;
                $entrega->comentario = removeAccents(Str::upper($request->comentario));
                $entrega->entrega = false;
                $entrega->numero = $numero_entrega;
                $entrega->save();
            }

            $inscripcion = InscripcionTemaTesis::findOrFail($proyecto->inscripcion_id);
            if ($inscripcion->estado == 'AA') {
                $inscripcion->estado = 'PC';
                $inscripcion->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'La corrección/comentario fue guardada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('proyectos_tesis.show_entregas', $id)->with('error-message', $e->getMessage());
        }
    }

    public function delete_entrega($id)
    {
        $this->authorize('eliminar_entregas_proyectos_tesis');

        DB::beginTransaction();

        try {
            $entrega = EntregaProyectoTesis::findOrFail($id);
            $ubicacion_archivo = $entrega->url_archivo;
            $ubicacion_archivo = str_replace('storage', 'public', $ubicacion_archivo);
            $proyecto = ProyectoTesis::findOrFail($entrega->proyecto_id);

            if ($entrega->entrega == false) {
                $tipo = 'corrección';
            } else {
                $tipo = 'entrega';
            }

            if ($proyecto->estado == 'AP') {
                return back()->with('error-message', 'No se puede eliminar la ' . $tipo . ' el bloque ya se encuentra aprobado.');
            }

            Storage::delete($ubicacion_archivo);
            $entrega->delete();

            $total_entregas = EntregaProyectoTesis::where('proyecto_id', $proyecto->id)->count();

            if ($total_entregas == 0) {
                $proyecto->estado = 'PE';
                $proyecto->save();

                $inscripcion = InscripcionTemaTesis::findOrFail($proyecto->inscripcion_id);
                $inscripcion->estado = 'PC';
                $inscripcion->save();
            }

            DB::commit();

            return redirect()->route('proyectos_tesis.show_entregas', $proyecto->id)->with('error-message', 'La ' . $tipo . ' fue eliminada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            $entrega = EntregaProyectoTesis::findOrFail($id);
            $proyecto = ProyectoTesis::findOrFail($entrega->proyecto_id);
            return redirect()->route('proyectos_tesis.show_entregas', $proyecto->id)->with('error-message', $e->getMessage());
        }
    }

    public function aprobar($id)
    {
        $this->authorize('aprobar_bloques_proyectos_tesis');

        DB::beginTransaction();

        try {
            $proyecto = ProyectoTesis::findOrFail($id);
            $proyecto->estado = 'AP';
            $proyecto->aprobado_tutor_id = Auth::id();
            $proyecto->fecha_aprobado_tutor = Carbon::now();
            $proyecto->save();

            $cantidad_proyectos = ProyectoTesis::where('inscripcion_id', $proyecto->inscripcion_id)->count();
            $cantidad_aprobados = ProyectoTesis::where('inscripcion_id', $proyecto->inscripcion_id)->where('estado', 'AP')->count();

            if ($cantidad_proyectos == $cantidad_aprobados) {
                $bloques = BloqueBorradorTesis::where('estado', 'AC')->get();
                foreach ($bloques as $bloque) {
                    $borrador = new BorradorTesis();
                    $borrador->inscripcion_id = $proyecto->inscripcion_id;
                    $borrador->bloque_id = $bloque->id;
                    $borrador->numero_bloque = $bloque->numero;
                    $borrador->save();

                    $inscripcion = InscripcionTemaTesis::findOrFail($proyecto->inscripcion_id);
                    $inscripcion->estado = 'AP';
                    $inscripcion->save();
                }
            }

            DB::commit();

            return redirect()->route('proyectos_tesis.show', $proyecto->inscripcion_id)->with('success-message', 'El bloque ' . $proyecto->nombre . ' fue aprobado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            $proyecto = ProyectoTesis::findOrFail($id);
            return redirect()->route('proyectos_tesis.show', $proyecto->inscripcion_id)->with('error-message', $e->getMessage());
        }
    }

    public function anular_aprobacion($id)
    {
        $this->authorize('anular_aprobacion_bloques_proyectos_tesis');

        DB::beginTransaction();

        try {
            $proyecto = ProyectoTesis::findOrFail($id);

            $cantidad_borradores_aprobados = BorradorTesis::where('inscripcion_id', $proyecto->inscripcion_id)->where('estado', 'AP')->count();
            if ($cantidad_borradores_aprobados > 0) {
                return back()->with('error-message', 'La aprobación del bloque ' . $proyecto->bloque->nombre . ' no se puede anular. Ya cuenta con bloques de borradores aprobados.');
            } else {
                $borradores = BorradorTesis::where('inscripcion_id', $proyecto->inscripcion_id)->get();
                if ($borradores) {
                    BorradorTesis::where('inscripcion_id', $proyecto->inscripcion_id)->delete();
                }
                $proyecto->estado = 'CO';
                $proyecto->aprobado_tutor_id = null;
                $proyecto->fecha_aprobado_tutor = null;
                $proyecto->save();

                $inscripcion = InscripcionTemaTesis::findOrFail($proyecto->inscripcion_id)->first();
                $inscripcion->estado = 'PC';
                $inscripcion->save();
            }

            DB::commit();

            return redirect()->route('proyectos_tesis.show', $proyecto->inscripcion_id)->with('error-message', 'La aprobación del bloque ' . $proyecto->nombre . ' fue anulada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            $proyecto = ProyectoTesis::findOrFail($id);
            return redirect()->route('proyectos_tesis.show', $proyecto->inscripcion_id)->with('error-message', $e->getMessage());
        }
    }

}
