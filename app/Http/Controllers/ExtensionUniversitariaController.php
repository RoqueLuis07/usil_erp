<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

use App\Models\ExtensionUniversitaria;
use App\Models\ExtensionUniversitariaDetalle;
use App\Models\TipoExtensionUniversitaria;
use App\Models\Docente;
use App\Models\Alumno;
use App\Models\AlumnoExtension;
use App\Models\Empresa;

class ExtensionUniversitariaController extends Controller
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
        $this->authorize('ver_extensiones_universitarias');

        try {
            $extensiones = ExtensionUniversitaria::orderBy('id', 'desc')->get();
            $alumnos = Alumno::whereHas('extensionesDetalles')->distinct()->orderBy('primer_nombre', 'asc')->get();
            $periodos = collect();
            foreach ($extensiones as $extension) {
                if ($extension->estado == 'FI') {
                    $fecha = Carbon::parse($extension->fecha_inicio);
                    $anho = $fecha->year;
                    if ($fecha->month <= 7) {
                        $periodo = $anho . '-1';
                    } else {
                        $periodo = $anho . '-2';
                    }
                    $periodos->push($periodo);
                }
            }
            $periodos = $periodos->unique()->sort()->values();
            $tipos_extensiones = TipoExtensionUniversitaria::where('estado', 'AC')->orderBy('nombre', 'asc')->get();
            return view('extensiones_universitarias/index')->with(compact('extensiones', 'alumnos', 'periodos', 'tipos_extensiones'));
        } catch (\Exception $e) {
            return redirect()->route('extensiones_universitarias.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_extensiones_universitarias');

        try {
            $extension = ExtensionUniversitaria::with('extensionUniversitariaDetalles')->findOrFail($id);
            return view('extensiones_universitarias/show')->with(compact('extension'));
        } catch (\Exception $e) {
            return redirect()->route('extensiones_universitarias.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_extensiones_universitarias');

        try {
            $tipos_extensiones = TipoExtensionUniversitaria::where('estado', 'AC')->orderBy('nombre', 'asc')->get();
            $docentes = Docente::where('estado', 'AC')->orderBy('primer_nombre', 'asc')->get();
            $alumnos = Alumno::where('estado', 'AC')->orderBy('primer_nombre', 'asc')->get();
            return view('extensiones_universitarias/create')->with(compact('tipos_extensiones', 'docentes', 'alumnos'));
        } catch (\Exception $e) {
            return redirect()->route('extensiones_universitarias.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_extensiones_universitarias');

        //preguntamos si el usuario tiene el rol de docente para el validador
        if (Auth::user()->hasRole('DOCENTE')) {
            $required = 'nullable';
        } else {
            $required = 'required';
        }

        $request->validate([
            'nombre_proyecto' => 'required',
            'tipo_extension' => ['required', 'numeric'],
            'docente' => [$required, 'numeric'],
            'cantidad_horas_proyecto' => ['required', 'numeric', 'min:1'],
            'proyecto' => ['required', 'file', 'extensions:pdf'],
			'fecha_inicio' => ['required', 'date'],
			'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'tiene_certificado' => 'required',
            'detalles' => ['required', 'array'],

            'detalles.*.alumno' => ['required', 'numeric'],
        ]);

        DB::beginTransaction();

        try {
            $extension = new ExtensionUniversitaria();
            $extension->nombre = removeAccents(Str::upper($request->nombre_proyecto));
            $extension->tipo_extension_id = $request->tipo_extension;
            $extension->cantidad_horas = $request->cantidad_horas_proyecto;
            if ($request->docente) {
                $extension->docente_id = $request->docente;
            } else {
                $docente = Docente::where('usuario_id', Auth::id())->first();
                $extension->docente_id = $docente->id;
            }
			$extension->fecha_inicio = $request->fecha_inicio;
			$extension->fecha_fin = $request->fecha_fin;
            $extension->tiene_certificado = $request->tiene_certificado;

            //cargar archivo
            $archivo = $request->proyecto;
            $extension_archivo = $archivo->getClientOriginalExtension();
            $directorio = 'storage/extensiones_universitarias/proyectos';
            $directorio_storage = 'public/extensiones_universitarias/proyectos';
            $nombre = 'proyecto_' . Str::lower(str_replace(' ', '_', $request->nombre_proyecto));
            $nombre_archivo = $nombre . '.' . $extension_archivo;
            Storage::putFileAs($directorio_storage . '/', $archivo, $nombre_archivo);
            $extension->ubicacion_proyecto = $directorio . '/' . $nombre_archivo;
            $extension->extension_proyecto = $extension_archivo;

            $extension->cargado_por_id = Auth::id();
            $extension->save();

            foreach ($request->detalles as $detalle) {
                $extension_detalle = new ExtensionUniversitariaDetalle();
                $extension_detalle->extension_universitaria_id = $extension->id;
                $extension_detalle->alumno_id = $detalle['alumno'];
                $extension_detalle->save();

                // $alumno_extension_existe = AlumnoExtension::where('alumno_id', $extension_detalle->alumno_id)->exists();
                // if (!$alumno_extension_existe) {
                //     $alumno_extension = new AlumnoExtension();
                //     $alumno_extension->alumno_id = $detalle['alumno'];
                //     $alumno_extension->save();
                // }
            }

            DB::commit();

            if ($request->generado_docente == 'SI') {
                return redirect()->route('pantallas_docentes.extensiones_universitarias', Auth::id())->with('success-message', 'La extensión universitaria ' . $extension->nombre . ' fue creada exitosamente.');
            } else {
                return redirect()->route('extensiones_universitarias.index')->with('success-message', 'La extensión universitaria ' . $extension->nombre . ' fue creada exitosamente.');
            }

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('extensiones_universitarias.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_extensiones_universitarias');

        try {
            $extension = ExtensionUniversitaria::with('extensionUniversitariaDetalles')->findOrFail($id);
            $tipos_extensiones = TipoExtensionUniversitaria::where('estado', 'AC')->get();
            $docentes = Docente::where('estado', 'AC')->orderBy('primer_nombre', 'asc')->get();
            $alumnos = Alumno::where('estado', 'AC')->orderBy('primer_nombre', 'asc')->get();
            return view('extensiones_universitarias/edit')->with(compact('extension', 'tipos_extensiones', 'docentes', 'alumnos'));
        } catch (\Exception $e) {
            return redirect()->route('extensiones_universitarias.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_extensiones_universitarias');

        $request->validate([
            'nombre_proyecto' => 'required',
            'tipo_extension' => ['required', 'numeric'],
            'docente' => ['required', 'numeric'],
            'cantidad_horas_proyecto' => ['required', 'numeric', 'min:1'],
			'fecha_inicio' => ['required', 'date'],
			'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'tiene_certificado' => 'required',
            'detalles' => ['required', 'array'],

            'detalles.*.alumno' => ['required', 'numeric'],
        ]);

        DB::beginTransaction();

        try {
            $extension = ExtensionUniversitaria::findOrFail($id);
            $extension->nombre = removeAccents(Str::upper($request->nombre_proyecto));
            $extension->tipo_extension_id = $request->tipo_extension;
            $extension->cantidad_horas = $request->cantidad_horas_proyecto;
            $extension->docente_id = $request->docente;
			$extension->fecha_inicio = $request->fecha_inicio;
			$extension->fecha_fin = $request->fecha_fin;
            $extension->tiene_certificado = $request->tiene_certificado;
            $extension->actualizado_por_id = Auth::id();
            $extension->save();

            //obtener el detalle de la extension y eliminar lo que habia para poder crear de vuelta
            $extension_detalles = ExtensionUniversitariaDetalle::where('extension_universitaria_id', $id)->delete();

            foreach ($request->detalles as $detalle) {
                $extension_detalle = new ExtensionUniversitariaDetalle();
                $extension_detalle->extension_universitaria_id = $extension->id;
                $extension_detalle->alumno_id = $detalle['alumno'];
                $extension_detalle->save();
            }

            DB::commit();

            return redirect()->route('extensiones_universitarias.index')->with('success-message', 'La extensión universitaria ' . $extension->nombre . ' fue actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('extensiones_universitarias.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit_hours($id)
    {
        $this->authorize('editar_horas_alumnos_extensiones_universitarias');

        try {
            $extension = ExtensionUniversitaria::with('extensionUniversitariaDetalles')->findOrFail($id);

            // if ($extension->tiene_certificado) {
            //     $extension = ExtensionUniversitaria::with(['extensionUniversitariaDetalles' => function ($query) {
            //         $query->where('url_certificado', '!=', null);
            //     }])->findOrFail($id);
            // }

            if ($extension->estado == 'IN' || $extension->estado == 'CO') {
                return view('extensiones_universitarias/edit_hours')->with(compact('extension'));
            } else {
                return redirect()->route('extensiones_universitarias.show', $extension->id)->with('error-message', 'La extensión universitaria debe estar aprobada para poder cargar las horas de los alumnos.');
            }
        } catch (\Exception $e) {
            return redirect()->route('extensiones_universitarias.show', $extension->id)->with('error-message', $e->getMessage());
        }
    }

    public function update_hours(Request $request, $id)
    {
        $this->authorize('editar_horas_alumnos_extensiones_universitarias');

        $request->validate([
            'detalles' => ['required', 'array'],

            'detalles.*.alumno' => ['required', 'numeric'],
            'detalles.*.cantidad_horas_alumno' => ['required', 'numeric', 'min:0'],
        ]);

        DB::beginTransaction();

        try {
            $extension = ExtensionUniversitaria::findOrFail($id);
            $extension_detalles = ExtensionUniversitariaDetalle::where('extension_universitaria_id', $extension->id)->get();
            foreach ($extension_detalles as $extension_detalle) {
                foreach ($request->detalles as $detalle) {
                    if ($extension_detalle->alumno_id == $detalle['alumno']) {
                        $extension_detalle->cantidad_horas = $detalle['cantidad_horas_alumno'];
                        $extension_detalle->save();
                    }
                }
            }
            $extension->actualizado_por_id = Auth::id();
            $extension->estado = 'CO';
            $extension->save();

            DB::commit();

            return redirect()->route('extensiones_universitarias.show', $extension->id)->with('success-message', 'Las horas de los alumnos de la extensión universitaria ' . $extension->nombre . ' fue actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('extensiones_universitarias.show', $extension->id)->with('error-message', $e->getMessage());
        }
    }

    public function approve($id)
    {
        $this->authorize('aprobar_extensiones_universitarias');

        DB::beginTransaction();

        try {
            $extension = ExtensionUniversitaria::findOrFail($id);
            $extension->actualizado_por_id = Auth::id();
            $extension->estado = 'AP';
            $extension->save();

            DB::commit();

            return redirect()->route('extensiones_universitarias.index')->with('success-message','La extensión universitaria ' . $extension->nombre . ' fue aprobada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('extensiones_universitarias.index')->with('error-message', $e->getMessage());
        }
    }

    public function unapprove($id)
    {
        $this->authorize('anular_aprobacion_extensiones_universitarias');

        DB::beginTransaction();

        try {
            $extension = ExtensionUniversitaria::findOrFail($id);
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

            return redirect()->route('extensiones_universitarias.index')->with('error-message','La extensión universitaria ' . $extension->nombre . ' fue desaprobada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('extensiones_universitarias.index')->with('error-message', $e->getMessage());
        }
    }

    public function reject($id)
    {
        $this->authorize('rechazar_extensiones_universitarias');

        DB::beginTransaction();

        try {
            $extension = ExtensionUniversitaria::findOrFail($id);
            $extension->actualizado_por_id = Auth::id();
            $extension->estado = 'RE';
            $extension->save();

            DB::commit();

            return redirect()->route('extensiones_universitarias.index')->with('error-message','La extensión universitaria ' . $extension->nombre . ' fue rechazada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('extensiones_universitarias.index')->with('error-message', $e->getMessage());
        }
    }

    public function unreject($id)
    {
        $this->authorize('anular_rechazo_extensiones_universitarias');

        DB::beginTransaction();

        try {
            $extension = ExtensionUniversitaria::findOrFail($id);
            $extension->actualizado_por_id = Auth::id();
            $extension->estado = 'PE';
            $extension->save();

            DB::commit();

            return redirect()->route('extensiones_universitarias.index')->with('success-message','La anulación del rechazo de la extensión universitaria ' . $extension->nombre . ' fue guardada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('extensiones_universitarias.index')->with('error-message', $e->getMessage());
        }
    }

    public function finish($id)
    {
        $this->authorize('finalizar_extensiones_universitarias');

        DB::beginTransaction();

        try {
            $extension = ExtensionUniversitaria::findOrFail($id);
            $extension_detalles = ExtensionUniversitariaDetalle::where('extension_universitaria_id', $extension->id)->get();
            foreach ($extension_detalles as $extension_detalle) {
                $alumno_extension_existe = AlumnoExtension::where('alumno_id', $extension_detalle->alumno_id)->where('tipo_extension_id', $extension->tipo_extension_id)->exists();
                if ($alumno_extension_existe) {
                    $alumno_extension = AlumnoExtension::where('alumno_id', $extension_detalle->alumno_id)->where('tipo_extension_id', $extension->tipo_extension_id)->first();
                    $alumno_extension->cantidad_actividades = $alumno_extension->cantidad_actividades + 1;
                    $alumno_extension->cantidad_horas = $alumno_extension->cantidad_horas + $extension_detalle->cantidad_horas;
                    $alumno_extension->save();
                } else {
                    $alumno_extension = new AlumnoExtension();
                    $alumno_extension->alumno_id = $extension_detalle->alumno_id;
                    $alumno_extension->tipo_extension_id = $extension->tipo_extension_id;
                    $alumno_extension->cantidad_actividades = 1;
                    $alumno_extension->cantidad_horas = $extension_detalle->cantidad_horas;
                    $alumno_extension->save();
                }
            }
            $extension->actualizado_por_id = Auth::id();
            $extension->estado = 'FI';
            $extension->save();

            DB::commit();

            return redirect()->route('extensiones_universitarias.index')->with('success-message','La extensión universitaria ' . $extension->nombre . ' fue finalizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('extensiones_universitarias.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_extensiones_universitarias');

        DB::beginTransaction();

        try {
            $extension = ExtensionUniversitaria::findOrFail($id);

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
                return redirect()->route('extensiones_universitarias.index')->with('error-message','La extensión universitaria ' . $extension->nombre . ' no se puede eliminar. Ya se encuentra ' . $estado . '.');
            } else {
                $ubicacion_proyecto = $extension->ubicacion_proyecto;
                $ubicacion_proyecto = str_replace('storage', 'public', $ubicacion_proyecto);
                Storage::delete($ubicacion_proyecto);

                $ubicacion_informe = $extension->ubicacion_informe;
                $ubicacion_informe = str_replace('storage', 'public', $ubicacion_informe);
                Storage::delete($ubicacion_informe);

                $extension_detalles = ExtensionUniversitariaDetalle::where('extension_universitaria_id', $extension->id)->delete();

                $extension->delete();

                DB::commit();

                return redirect()->route('extensiones_universitarias.index')->with('success-message','La extensión universitaria ' . $extension->nombre . ' fue eliminada exitosamente.');
            }
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('extensiones_universitarias.index')->with('error-message', 'La extensión universitaria ' . $extension->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('extensiones_universitarias.index')->with('error-message', $e->getMessage());
            }
        }
    }

    public function cargar_informe(Request $request, $id)
    {
        $this->authorize('cargar_informes_extensiones_universitarias');

        $request->validate([
            'informe' => ['required', 'file', 'extensions:pdf']
        ]);

        DB::beginTransaction();

        try {
            $extension = ExtensionUniversitaria::findOrFail($id);

            //cargar archivo
            $archivo = $request->informe;
            $extension_archivo = $archivo->getClientOriginalExtension();
            $directorio = 'storage/extensiones_universitarias/informes';
            $directorio_storage = 'public/extensiones_universitarias/informes';
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
            return redirect()->route('extensiones_universitarias.show', $extension->id)->with('error-message', $e->getMessage());
        }
    }

    public function change_adjuntos(Request $request, $id)
    {
        if ($this->authorize('cambiar_adjunto_proyectos_extensiones_universitarias') || $this->authorize('cambiar_adjunto_informes_extensiones_universitarias')) {
            $request->validate([
                'tipo_adjunto' => 'required',
                'archivo' => ['required', 'file', 'extensions:pdf']
            ]);

            DB::beginTransaction();

            try {
                $extension = ExtensionUniversitaria::findOrFail($id);
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
                    return redirect()->route('extensiones_universitarias.index')->with('error-message','No se puede cambiar el proyecto de la extensión universitaria ' . $extension->nombre . ' . Ya se encuentra ' . $estado . '.');
                } elseif ($extension->estado == 'CO' || $extension->estado == 'FI') {
                    return redirect()->route('extensiones_universitarias.index')->with('error-message','No se puede cambiar el informe de la extensión universitaria ' . $extension->nombre . ' . Ya se encuentra ' . $estado . '.');
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
                    $directorio = 'storage/extensiones_universitarias/' . $tipo . 's';
                    $directorio_storage = 'public/extensiones_universitarias/' . $tipo . 's';
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
                return redirect()->route('extensiones_universitarias.show', $extension->id)->with('error-message', $e->getMessage());
            }
        } else {
            abort(403);
        }
    }

    public function show_reporte(Request $request)
    {
        // $this->authorize('generar_reportes_extensiones_universitarias');

        try {
            $proyecto = '';
            $periodo = '';
            $tipo_extension = '';
            $alumno = '';
            $usuario = Auth::user()->name;
            $fecha = Carbon::now()->translatedFormat('l d/m/Y');
            $hora = Carbon::now()->format('H:i:s');

            $query = ExtensionUniversitaria::query();

            if ($request->proyecto) {
                $query->where('id', $request->proyecto);
                $proyecto = ExtensionUniversitaria::findOrFail($request->proyecto);
            }

            if ($request->periodo) {
                $anho = explode('-', $request->periodo)[0];
                $query->whereYear('fecha_inicio', $anho);
                $periodo = $request->periodo;
            }

            if ($request->tipo_extension) {
                $query->where('tipo_extension_id', $request->tipo_extension);
                $tipo_extension = TipoExtensionUniversitaria::findOrFail($request->tipo_extension);
            }

            if ($request->alumno) {
                $query->whereHas('extensionUniversitariaDetalles', function ($query) use ($request) {
                    $query->where('alumno_id', $request->alumno);
                });
                $alumno = Alumno::findOrFail($request->alumno);
            }

            $extensiones = $query->where('estado', 'FI')->get();
            foreach ($extensiones as $extension) {
                $fecha = Carbon::parse($extension->fecha_inicio);
                $anho = $fecha->year;
                if ($fecha->month <= 7) {
                    $extension->periodo = $anho . '-1';
                } else {
                    $extension->periodo = $anho . '-2';
                }
            }

            if ($extensiones->count() == 0) {
                return back()->with('error-message', 'El reporte no puede ser visualizado. No existen extensiones universitarias con la combinación de filtros seleccionada.');
            }

            return view('extensiones_universitarias/show_reporte')->with(compact('extensiones', 'proyecto', 'periodo', 'tipo_extension', 'alumno', 'usuario', 'fecha', 'hora'));
        } catch (\Exception $e) {
            return redirect()->route('extensiones_universitarias.index')->with('error-message', $e->getMessage());
        }
    }

    public function generate_reporte(Request $request)
    {
        // $this->authorize('generar_reportes_extensiones_universitarias');

        try {
            $proyecto = '';
            $periodo = '';
            $tipo_extension = '';
            $alumno = '';

            $empresa = Empresa::first(); //obtenemos los datos de la empresa para el header
            $fecha_hoy = Carbon::now(); //obtenemos la fecha de hoy para el footer
            $query = ExtensionUniversitaria::query();

            if ($request->proyecto) {
                $query->where('id', $request->proyecto);
                $proyecto = ExtensionUniversitaria::findOrFail($request->proyecto);
            }

            if ($request->periodo) {
                $anho = explode('-', $request->periodo)[0];
                $query->whereYear('fecha_inicio', $anho);
                $periodo = $request->periodo;
            }

            if ($request->tipo_extension) {
                $query->where('tipo_extension_id', $request->tipo_extension);
                $tipo_extension = TipoExtensionUniversitaria::findOrFail($request->tipo_extension);
            }

            if ($request->alumno) {
                $query->whereHas('extensionUniversitariaDetalles', function ($query) use ($request) {
                    $query->where('alumno_id', $request->alumno);
                });
                $alumno = Alumno::findOrFail($request->alumno);
            }

            $extensiones = $query->where('estado', 'FI')->get();
            foreach ($extensiones as $extension) {
                $fecha = Carbon::parse($extension->fecha_inicio);
                $anho = $fecha->year;
                if ($fecha->month <= 7) {
                    $extension->periodo = $anho . '-1';
                } else {
                    $extension->periodo = $anho . '-2';
                }
            }

            $pdf = Pdf::loadView('extensiones_universitarias/pdf', compact('empresa', 'fecha_hoy', 'extensiones', 'proyecto', 'periodo' ,'tipo_extension', 'alumno'));
            $pdf->setPaper('A4', 'landscape');

            return $pdf->stream('rpt_extensiones_universitaria_' . Carbon::now()->format('dmY_His') . '.pdf');
        } catch (\Exception $e) {
            return redirect()->route('extensiones_universitarias.index')->with('error-message', $e->getMessage());
        }
    }
}
