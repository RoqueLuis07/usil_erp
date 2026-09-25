<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

use App\Models\AnteproyectoTesis;
use App\Models\InscripcionTemaTesis;
use App\Models\EntregaAnteproyectoTesis;
use App\Models\BloqueProyectoTesis;
use App\Models\ProyectoTesis;
use App\Models\RequerimientoEntregaTesis;

class AnteproyectoTesisController extends Controller
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
        $this->authorize('ver_anteproyectos_tesis');

        try {
            $temas = InscripcionTemaTesis::whereHas('anteproyectos')->orderBy('id', 'desc')->get();
            return view('tesis/anteproyectos/index')->with(compact('temas'));
        } catch (\Exception $e) {
            return redirect()->route('anteproyectos_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_anteproyectos_tesis');

        try {
            $tema = InscripcionTemaTesis::with(['anteproyectos' => function ($query) {
                $query->orderBy('numero_bloque', 'asc');
            }])->findOrFail($id);
            return view('tesis/anteproyectos/show')->with(compact('tema'));
        } catch (\Exception $e) {
            return redirect()->route('anteproyectos_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function show_entregas($id)
    {
        $this->authorize('ver_entregas_anteproyectos_tesis');

        try {
            $anteproyecto = AnteproyectoTesis::with(['entregas' => function ($query) {
                $query->orderBy('numero', 'desc');
            }])->findOrFail($id);

            return view('tesis/anteproyectos/show_entregas')->with(compact('anteproyecto'));
        } catch (\Exception $e) {
            return redirect()->route('anteproyectos_tesis.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function save_entrega(Request $request, $id)
    {
        $this->authorize('entregar_anteproyectos_tesis');

        $request->validate([
            'archivo' => ['required', 'file', 'extensions:doc,docx'],
            'comentario' => 'nullable'
        ]);

        DB::beginTransaction();

        try {
            $anteproyecto = AnteproyectoTesis::findOrFail($id);
            $anteproyecto->estado = 'EN';
            $anteproyecto->save();

            $ultima_entrega = EntregaAnteproyectoTesis::where('anteproyecto_id', $anteproyecto->id)->orderBy('id', 'desc')->first();
            if ($ultima_entrega) {
                $numero_entrega = $ultima_entrega->numero + 1;
            } else {
                $numero_entrega = 1;
            }

            $cantidad_anteproyectos = AnteproyectoTesis::where('inscripcion_id', $anteproyecto->inscripcion_id)->count();
            $fecha_entrega = RequerimientoEntregaTesis::where('fecha_inicio_anteproyecto', '<=', Carbon::now())->where('fecha_fin_anteproyecto', '>=', Carbon::now())->first();
            if ($cantidad_anteproyectos == $anteproyecto->numero_bloque && !$fecha_entrega) {
                return response()->json([
                    'mensaje' => 'La entrega no se puede realizar, el día de hoy no se encuentra dentro del rango para entregas de anteproyecto.',
                ]);
            }

            $archivo = $request->archivo;
            $extension = $archivo->getClientOriginalExtension();
            $directorio = 'storage/tesis/anteproyectos/entregas';
            $directorio_storage = 'public/tesis/anteproyectos/entregas';

            $nombre = 'anteproyecto_bloque_' . $anteproyecto->bloque->numero . '_' . Str::lower(str_replace(' ', '_', $anteproyecto->inscripcion->tema) . '_' . $numero_entrega);
            $nombre_archivo = $nombre . '.' . $extension;
            Storage::putFileAs($directorio_storage . '/', $archivo, $nombre_archivo);

            $entrega = new EntregaAnteproyectoTesis();
            $entrega->anteproyecto_id = $anteproyecto->id;
            $entrega->url_archivo = $directorio . '/' . $nombre_archivo;
            $entrega->comentario = removeAccents(Str::upper($request->comentario));
            $entrega->numero = $numero_entrega;
            $entrega->save();

            $inscripcion = InscripcionTemaTesis::findOrFail($anteproyecto->inscripcion_id);
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
                return redirect()->route('anteproyectos_tesis.show_entregas', $id)->with('error-message', $e->getMessage());
            }
        }
    }

    public function save_correccion(Request $request, $id)
    {
        $this->authorize('corregir_anteproyectos_tesis');

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

            $anteproyecto = AnteproyectoTesis::findOrFail($id);
            $anteproyecto->estado = 'CO';
            $anteproyecto->save();

            $ultima_entrega = EntregaAnteproyectoTesis::where('anteproyecto_id', $anteproyecto->id)->orderBy('id', 'desc')->first();
            if ($ultima_entrega) {
                $numero_entrega = $ultima_entrega->numero + 1;
            } else {
                $numero_entrega = 1;
            }

            if ($request->archivo != null) {
                $archivo = $request->archivo;
                $extension = $archivo->getClientOriginalExtension();
                $directorio = 'storage/tesis/anteproyectos/correcciones';
                $directorio_storage = 'public/tesis/anteproyectos/correcciones';

                $nombre = 'anteproyecto_bloque_' . $anteproyecto->bloque->numero . '_' . Str::lower(str_replace(' ', '_', $anteproyecto->inscripcion->tema)) . '_' . $numero_entrega;
                $nombre_archivo = $nombre . '.' . $extension;
                Storage::putFileAs($directorio_storage . '/', $archivo, $nombre_archivo);

                $entrega = new EntregaAnteproyectoTesis();
                $entrega->anteproyecto_id = $anteproyecto->id;
                $entrega->url_archivo = $directorio . '/' . $nombre_archivo;
                $entrega->comentario = removeAccents(Str::upper($request->comentario));
                $entrega->entrega = false;
                $entrega->numero = $numero_entrega;
                $entrega->save();
            } else {
                $entrega = new EntregaAnteproyectoTesis();
                $entrega->anteproyecto_id = $anteproyecto->id;
                $entrega->comentario = removeAccents(Str::upper($request->comentario));
                $entrega->entrega = false;
                $entrega->numero = $numero_entrega;
                $entrega->save();
            }

            $inscripcion = InscripcionTemaTesis::findOrFail($anteproyecto->inscripcion_id);
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
                return redirect()->route('anteproyectos_tesis.show_entregas', $id)->with('error-message', $e->getMessage());
            }
        }
    }

    public function delete_entrega($id)
    {
        $this->authorize('eliminar_entregas_anteproyectos tesis');

        DB::beginTransaction();

        try {
            $entrega = EntregaAnteproyectoTesis::findOrFail($id);
            $ubicacion_archivo = $entrega->url_archivo;
            $ubicacion_archivo = str_replace('storage', 'public', $ubicacion_archivo);
            $anteproyecto = AnteproyectoTesis::findOrFail($entrega->anteproyecto_id);

            if ($entrega->entrega == false) {
                $tipo = 'corrección';
            } else {
                $tipo = 'entrega';
            }

            if ($anteproyecto->estado == 'AP') {
                return back()->with('error-message', 'No se puede eliminar la ' . $tipo . ' el bloque ya se encuentra aprobado.');
            }

            Storage::delete($ubicacion_archivo);
            $entrega->delete();

            $total_entregas = EntregaAnteproyectoTesis::where('anteproyecto_id', $anteproyecto->id)->count();

            if ($total_entregas == 0) {
                $anteproyecto->estado = 'PE';
                $anteproyecto->save();

                $inscripcion = InscripcionTemaTesis::findOrFail($anteproyecto->inscripcion_id);
                $inscripcion->estado = 'AC';
                $inscripcion->save();
            }

            DB::commit();

            if (Auth::user()->hasRole('ALUMNO')) {
                return redirect()->route('pantallas_alumnos.show_entregas_anteproyectos_tesis', $anteproyecto->id)->with('error-message', 'La ' . $tipo . ' fue eliminada exitosamente.');
            } else {
                return redirect()->route('anteproyectos_tesis.show_entregas', $anteproyecto->id)->with('error-message', 'La ' . $tipo . ' fue eliminada exitosamente.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            $entrega = EntregaAnteproyectoTesis::findOrFail($id);
            $anteproyecto = AnteproyectoTesis::findOrFail($entrega->anteproyecto_id);

            if (Auth::user()->hasRole('ALUMNO')) {
                return redirect()->route('pantallas_alumnos.show_entregas_anteproyectos_tesis', $anteproyecto->id)->with('error-message', $e->getMessage());
            } else {
                return redirect()->route('anteproyectos_tesis.show_entregas', $anteproyecto->id)->with('error-message', $e->getMessage());
            }


        }
    }

    public function aprobar($id)
    {
        $this->authorize('aprobar_bloques_anteproyectos_tesis');

        DB::beginTransaction();

        try {
            $anteproyecto = AnteproyectoTesis::findOrFail($id);
            $anteproyecto->estado = 'AP';
            $anteproyecto->aprobado_tutor_id = Auth::id();
            $anteproyecto->fecha_aprobado_tutor = Carbon::now();
            $anteproyecto->save();

            $cantidad_anteproyectos = AnteproyectoTesis::where('inscripcion_id', $anteproyecto->inscripcion_id)->count();
            $cantidad_aprobados = AnteproyectoTesis::where('inscripcion_id', $anteproyecto->inscripcion_id)->where('estado', 'AP')->count();

            if ($cantidad_anteproyectos == $cantidad_aprobados) {
                $bloques = BloqueProyectoTesis::where('estado', 'AC')->get();
                foreach ($bloques as $bloque) {
                    $proyecto = new ProyectoTesis();
                    $proyecto->inscripcion_id = $anteproyecto->inscripcion_id;
                    $proyecto->bloque_id = $bloque->id;
                    $proyecto->numero_bloque = $bloque->numero;
                    $proyecto->save();

                    $inscripcion = InscripcionTemaTesis::findOrFail($anteproyecto->inscripcion_id);
                    $inscripcion->estado = 'AA';
                    $inscripcion->save();
                }
            }

            DB::commit();

            return redirect()->route('anteproyectos_tesis.show', $anteproyecto->inscripcion_id)->with('success-message', 'El bloque ' . $anteproyecto->nombre . ' fue aprobado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            $anteproyecto = AnteproyectoTesis::findOrFail($id);
            return redirect()->route('anteproyectos_tesis.show', $anteproyecto->inscripcion_id)->with('error-message', $e->getMessage());
        }
    }

    public function anular_aprobacion($id)
    {
        $this->authorize('anular_aprobacion_bloques_anteproyectos_tesis');

        DB::beginTransaction();

        try {
            $anteproyecto = AnteproyectoTesis::findOrFail($id);

            $cantidad_proyectos_aprobados = ProyectoTesis::where('inscripcion_id', $anteproyecto->inscripcion_id)->where('estado', 'AP')->count();
            if ($cantidad_proyectos_aprobados > 0) {
                return back()->with('error-message', 'La aprobación del bloque ' . $anteproyecto->bloque->nombre . ' no se puede anular. Ya cuenta con bloques de proyecto aprobados.');
            } else {
                $proyectos = ProyectoTesis::where('inscripcion_id', $anteproyecto->inscripcion_id)->get();
                if ($proyectos) {
                    ProyectoTesis::where('inscripcion_id', $anteproyecto->inscripcion_id)->delete();
                }
                $anteproyecto->estado = 'CO';
                $anteproyecto->aprobado_tutor_id = null;
                $anteproyecto->fecha_aprobado_tutor = null;
                $anteproyecto->save();

                $inscripcion = InscripcionTemaTesis::findOrFail($anteproyecto->inscripcion_id)->first();
                $inscripcion->estado = 'EC';
                $inscripcion->save();
            }

            DB::commit();

            return redirect()->route('anteproyectos_tesis.show', $anteproyecto->inscripcion_id)->with('error-message', 'La aprobación del bloque ' . $anteproyecto->nombre . ' fue anulada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            $anteproyecto = AnteproyectoTesis::findOrFail($id);
            return redirect()->route('anteproyectos_tesis.show', $anteproyecto->inscripcion_id)->with('error-message', $e->getMessage());
        }
    }

}
