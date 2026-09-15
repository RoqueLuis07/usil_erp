<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

use App\Models\ExtensionUniversitariaUbs;
use App\Models\TipoExtensionUniversitariaUbs;
use App\Models\Alumno;
use App\Models\AlumnoExtensionUbs;

class ExtensionUniversitariaUbsController extends Controller
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
        $this->authorize('ver_extensiones_ubs');

        try {
            $extensiones = ExtensionUniversitariaUbs::orderBy('id', 'desc')->get();
            return view('ubs/maestrias/extensiones_universitarias/index')->with(compact('extensiones'));
        } catch (\Exception $e) {
            return redirect()->route('extensiones_universitarias_ubs.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_extensiones_ubs');

        try {
            $extension = ExtensionUniversitariaUbs::findOrFail($id);
            return view('ubs/maestrias/extensiones_universitarias/show')->with(compact('extension'));
        } catch (\Exception $e) {
            return redirect()->route('extensiones_universitarias_ubs.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_extensiones_ubs');

        try {
            $tipos_extensiones = TipoExtensionUniversitariaUbs::where('estado', 'AC')->get();
            $alumnos = Alumno::where('estado', 'AC')->orderBy('primer_nombre', 'asc')->get();
            return view('ubs/maestrias/extensiones_universitarias/create')->with(compact('tipos_extensiones', 'alumnos'));
        } catch (\Exception $e) {
            return redirect()->route('extensiones_universitarias_ubs.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_extensiones_ubs');

        //preguntamos si el usuario tiene el rol de alumno para el validador
        if (Auth::user()->hasRole('ALUMNO')) {
             $required = 'nullable';
        } else {
             $required = 'required';
        }

        $request->validate([
            'nombre_proyecto' => 'required',
            'tipo_extension' => ['required', 'numeric'],
            'alumno' => [$required, 'numeric'],
            'cantidad_horas_proyecto' => ['required', 'numeric', 'min:1'],
            'proyecto' => ['required', 'file', 'extensions:pdf'],
        ]);

        DB::beginTransaction();

        try {
            $extension = new ExtensionUniversitariaUbs();
            $extension->nombre = removeAccents(Str::upper($request->nombre_proyecto));
            $extension->tipo_extension_id = $request->tipo_extension;
            $extension->cantidad_horas = $request->cantidad_horas_proyecto;
            if ($request->alumno) {
                $extension->alumno_id = $request->alumno;
            } else {
                $alumno = Alumno::where('usuario_id', Auth::id())->first();
                $extension->alumno_id = $docente->id;
            }

            //cargar archivo
            $archivo = $request->proyecto;
            $extension_archivo = $archivo->getClientOriginalExtension();
            $directorio = 'storage/maestrias/extensiones_universitarias/proyectos';
            $directorio_storage = 'public/maestrias/extensiones_universitarias/proyectos';
            $nombre = 'proyecto_' . Str::lower(str_replace(' ', '_', $request->nombre_proyecto));
            $nombre_archivo = $nombre . '.' . $extension_archivo;
            Storage::putFileAs($directorio_storage . '/', $archivo, $nombre_archivo);
            $extension->ubicacion_proyecto = $directorio . '/' . $nombre_archivo;
            $extension->extension_proyecto = $extension_archivo;

            $extension->cargado_por_id = Auth::id();
            $extension->save();

            DB::commit();

            if ($request->generado_docente == 'SI') {
                return redirect()->route('pantallas_docentes.extensiones.index')->with('success-message', 'La extensión universitaria ' . $extension->nombre . ' fue creada exitosamente.');
            } else {
                return redirect()->route('extensiones_universitarias_ubs.index')->with('success-message', 'La extensión universitaria ' . $extension->nombre . ' fue creada exitosamente.');
            }

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('extensiones_universitarias_ubs.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_extensiones_ubs');

        try {
            $extension = ExtensionUniversitariaUbs::with('extensionUniversitariaDetalles')->findOrFail($id);
            $tipos_extensiones = TipoExtensionUniversitariaUbs::where('estado', 'AC')->get();
            $alumnos = Alumno::where('estado', 'AC')->orderBy('primer_nombre', 'asc')->get();
            return view('ubs/maestrias/extensiones_universitarias/edit')->with(compact('extension', 'tipos_extensiones', 'alumnos'));
        } catch (\Exception $e) {
            return redirect()->route('extensiones_universitarias_ubs.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_extensiones_ubs');

        $request->validate([
            'nombre_proyecto' => 'required',
            'tipo_extension' => ['required', 'numeric'],
            'alumno' => ['required', 'numeric'],
            'cantidad_horas_proyecto' => ['required', 'numeric', 'min:1'],
        ]);

        DB::beginTransaction();

        try {
            $extension = ExtensionUniversitariaUbs::findOrFail($id);
            $extension->nombre = removeAccents(Str::upper($request->nombre_proyecto));
            $extension->tipo_extension_id = $request->tipo_extension;
            $extension->cantidad_horas = $request->cantidad_horas_proyecto;
            $extension->alumno_id = $request->alumno;
            $extension->actualizado_por_id = Auth::id();
            $extension->save();

            DB::commit();

            return redirect()->route('extensiones_universitarias_ubs.index')->with('success-message', 'La extensión universitaria ' . $extension->nombre . ' fue actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('extensiones_universitarias_ubs.index')->with('error-message', $e->getMessage());
        }
    }

    public function approve($id)
    {
        $this->authorize('aprobar_extensiones_ubs');

        DB::beginTransaction();

        try {
            $extension = ExtensionUniversitariaUbs::findOrFail($id);
            $extension->actualizado_por_id = Auth::id();
            $extension->estado = 'AP';
            $extension->save();

            DB::commit();

            return redirect()->route('extensiones_universitarias_ubs.index')->with('success-message','La extensión universitaria ' . $extension->nombre . ' fue aprobada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('extensiones_universitarias_ubs.index')->with('error-message', $e->getMessage());
        }
    }

    public function unapprove($id)
    {
        $this->authorize('anular_aprobacion_extensiones_ubs');

        DB::beginTransaction();

        try {
            $extension = ExtensionUniversitariaUbs::findOrFail($id);
            $extension->actualizado_por_id = Auth::id();
            if ($extension->ubicacion_informe) {
                $ubicacion_archivo = $extension->ubicacion_informe;
                $ubicacion_archivo = str_replace('storage', 'public', $ubicacion_archivo);
                Storage::delete($ubicacion_archivo);
                $extension->ubicacion_informe = null;
                $extension->extension_informe = null;
            }
            $extension->estado = 'PE';
            $extension->save();

            DB::commit();

            return redirect()->route('extensiones_universitarias_ubs.index')->with('error-message','La extensión universitaria ' . $extension->nombre . ' fue desaprobada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('extensiones_universitarias_ubs.index')->with('error-message', $e->getMessage());
        }
    }

    public function reject($id)
    {
        $this->authorize('rechazar_extensiones_ubs');

        DB::beginTransaction();

        try {
            $extension = ExtensionUniversitariaUbs::findOrFail($id);
            $extension->actualizado_por_id = Auth::id();
            $extension->estado = 'RE';
            $extension->save();

            DB::commit();

            return redirect()->route('extensiones_universitarias_ubs.index')->with('error-message','La extensión universitaria ' . $extension->nombre . ' fue rechazada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('extensiones_universitarias_ubs.index')->with('error-message', $e->getMessage());
        }
    }

    public function unreject($id)
    {
        $this->authorize('anular_rechazo_extensiones_ubs');

        DB::beginTransaction();

        try {
            $extension = ExtensionUniversitariaUbs::findOrFail($id);
            $extension->actualizado_por_id = Auth::id();
            $extension->estado = 'PE';
            $extension->save();

            DB::commit();

            return redirect()->route('extensiones_universitarias_ubs.index')->with('success-message','La anulación del rechazo de la extensión universitaria ' . $extension->nombre . ' fue guardada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('extensiones_universitarias_ubs.index')->with('error-message', $e->getMessage());
        }
    }

    public function update_hours(Request $request, $id)
    {
         $this->authorize('editar_horas_alumnos_extensiones_ubs');

        $request->validate([
            'horas' => ['required', 'numeric', 'min:0']
        ]);

        DB::beginTransaction();

        try {
            $extension = ExtensionUniversitariaUbs::findOrFail($id);
            $extension->alumno_horas_realizadas = $request->horas;
            $extension->estado = 'CO';
            $extension->actualizado_por_id = Auth::id();
            $extension->save();

            DB::commit();

            return response()->json([
                'message' => 'Las horas del alumno de la extensión universitaria ' . $extension->nombre . ' fue cargada exitosamente.',
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('extensiones_universitarias_ubs.index')->with('error-message', $e->getMessage());
        }
    }

    public function finish($id)
    {
        $this->authorize('finalizar_extensiones_ubs');

        DB::beginTransaction();

        try {
            $extension = ExtensionUniversitariaUbs::findOrFail($id);
            $extension->actualizado_por_id = Auth::id();
            $extension->estado = 'FI';
            $extension->save();

            $alumno_extension_existe = AlumnoExtensionUbs::where('alumno_id', $extension->alumno_id)->where('tipo_extension_id', $extension->tipo_extension_id)->exists();
            if ($alumno_extension_existe) {
                $alumno_extension = AlumnoExtensionUbs::where('alumno_id', $extension->alumno_id)->where('tipo_extension_id', $extension->tipo_extension_id)->first();
                $alumno_extension->cantidad_actividades = $alumno_extension->cantidad_actividades + 1;
                $alumno_extension->cantidad_horas = $alumno_extension->cantidad_horas + $extension->alumno_horas_realizadas;
                $alumno_extension->save();
            } else {
                $alumno_extension = new AlumnoExtensionUbs();
                $alumno_extension->alumno_id = $extension->alumno_id;
                $alumno_extension->tipo_extension_id = $extension->tipo_extension_id;
                $alumno_extension->cantidad_actividades = 1;
                $alumno_extension->cantidad_horas = $extension->alumno_horas_realizadas;
                $alumno_extension->save();
            }

            DB::commit();

            return redirect()->route('extensiones_universitarias_ubs.index')->with('success-message','La extensión universitaria ' . $extension->nombre . ' fue finalizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('extensiones_universitarias_ubs.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_extensiones_ubs');

        DB::beginTransaction();

        try {
            $extension = ExtensionUniversitariaUbs::findOrFail($id);

            switch ($extension->estado) {
                case 'AP':
                    $estado = 'aprobada';
                    break;
                case 'IN':
                    $estado = 'informada';
                    break;
                case 'CO':
                    $estado = 'con horas cargadas';
                    break;
                case 'FI':
                    $estado = 'finalizada';
                    break;
                default:
                    break;
            }


            if ($extension->estado == 'AP' || $extension->estado == 'IN' || $extension->estado == 'CO' || $extension->estado == 'FI') {
                return redirect()->route('extensiones_universitarias_ubs.index')->with('error-message','La extensión universitaria ' . $extension->nombre . ' no se puede eliminar. Ya se encuentra ' . $estado . '.');
            } else {
                $ubicacion_proyecto = $extension->ubicacion_proyecto;
                $ubicacion_proyecto = str_replace('storage', 'public', $ubicacion_proyecto);
                Storage::delete($ubicacion_proyecto);

                $ubicacion_informe = $extension->ubicacion_informe;
                $ubicacion_informe = str_replace('storage', 'public', $ubicacion_informe);
                Storage::delete($ubicacion_informe);

                $extension->delete();

                DB::commit();

                return redirect()->route('extensiones_universitarias_ubs.index')->with('success-message','La extensión universitaria ' . $extension->nombre . ' fue eliminada exitosamente.');
            }
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('extensiones_universitarias_ubs.index')->with('error-message', 'La extensión universitaria ' . $extension->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('extensiones_universitarias_ubs.index')->with('error-message', $e->getMessage());
            }
        }
    }

    public function cargar_informe(Request $request, $id)
    {
        $this->authorize('cargar_adjunto_informes_extensiones_ubs');

        $request->validate([
            'informe' => ['required', 'file', 'extensions:pdf']
        ]);

        DB::beginTransaction();

        try {
            $extension = ExtensionUniversitariaUbs::findOrFail($id);

            //cargar archivo
            $archivo = $request->informe;
            $extension_archivo = $archivo->getClientOriginalExtension();
            $directorio = 'storage/maestrias/extensiones_universitarias/informes';
            $directorio_storage = 'public/maestrias/extensiones_universitarias/informes';
            $nombre = 'informe_' . Str::lower(str_replace(' ', '_', $extension->nombre));
            $nombre_archivo = $nombre . '.' . $extension_archivo;
            Storage::putFileAs($directorio_storage . '/', $archivo, $nombre_archivo);

            $extension->ubicacion_informe = $directorio . '/' . $nombre_archivo;
            $extension->extension_informe = $extension_archivo;
            $extension->estado = 'IN';
            $extension->save();

            DB::commit();

            return response()->json([
                'message' => 'El informe de la extensión universitaria ' . $extension->nombre . ' fue cargado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('extensiones_universitarias_ubs.show', $extension->id)->with('error-message', $e->getMessage());
        }
    }

    public function change_adjuntos(Request $request, $id)
    {
        if ($this->authorize('cambiar_adjunto_proyectos_extensions_ubs') || $this->authorize('cambiar_adjunto_informes_extensiones_ubs')) {
            $request->validate([
                'tipo_adjunto' => 'required',
                'archivo' => ['required', 'file', 'extensions:pdf']
            ]);

            DB::beginTransaction();

            try {
                $extension = ExtensionUniversitariaUbs::findOrFail($id);
                switch ($extension->estado) {
                    case 'AP':
                        $estado = 'aprobada';
                        break;
                    case 'IN':
                        $estado = 'informada';
                        break;
                    case 'CO':
                        $estado = 'con horas cargadas';
                        break;
                    case 'FI':
                        $estado = 'finalizada';
                        break;
                    default:
                        break;
                }

                if ($extension->estado == 'AP') {
                    return redirect()->route('extensiones_universitarias_ubs.index')->with('error-message','No se puede cambiar el proyecto de la extensión universitaria ' . $extension->nombre . ' . Ya se encuentra ' . $estado . '.');
                } elseif ($extension->estado == 'CO' || $extension->estado == 'FI') {
                    return redirect()->route('extensiones_universitarias_ubs.index')->with('error-message','No se puede cambiar el informe de la extensión universitaria ' . $extension->nombre . ' . Ya se encuentra ' . $estado . '.');
                } else {
                    $tipo = Str::lower($request->tipo_adjunto);
                    if ($tipo == 'proyecto') {
                        $ubicacion_archivo = $extension->ubicacion_proyecto;
                        $ubicacion_archivo = str_replace('storage', 'public', $ubicacion_archivo);
                        Storage::delete($ubicacion_archivo);
                    } elseif ($tipo == 'informe') {
                        $ubicacion_archivo = $extension->ubicacion_informe;
                        $ubicacion_archivo = str_replace('storage', 'public', $ubicacion_archivo);
                        Storage::delete($ubicacion_archivo);
                    }

                    //cargar archivo
                    $archivo = $request->archivo;
                    $extension_archivo = $archivo->getClientOriginalExtension();
                    $directorio = 'storage/maestrias/extensiones_universitarias/' . $tipo . 's';
                    $directorio_storage = 'public/maestrias/extensiones_universitarias/' . $tipo . 's';
                    $nombre = $tipo . '_' . Str::lower(str_replace(' ', '_', $extension->nombre));
                    $nombre_archivo = $nombre . '.' . $extension_archivo;
                    Storage::putFileAs($directorio_storage . '/', $archivo, $nombre_archivo);
                    if ($tipo == 'proyecto') {
                        $extension->ubicacion_proyecto = $directorio . '/' . $nombre_archivo;
                        $extension->extension_proyecto = $extension_archivo;
                    } elseif ($tipo == 'informe') {
                        $extension->ubicacion_informe = $directorio . '/' . $nombre_archivo;
                        $extension->extension_informe = $extension_archivo;
                    }
                    $extension->save();

                    DB::commit();

                    return response()->json([
                        'message' => 'El ' . $tipo . ' de la extensión universitaria ' . $extension->nombre . ' fue reemplazado exitosamente.',
                    ]);
                }
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('extensiones_universitarias_ubs.show', $extension->id)->with('error-message', $e->getMessage());
            }
        } else {
            abort(403);
        }
    }
}
