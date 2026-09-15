<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\AutoEncoder;
use Carbon\Carbon;

use App\Models\Solicitud;
use App\Models\TipoSolicitud;
use App\Models\Alumno;
use App\Models\PagoSolicitud;
use App\Models\Semestre;
use App\Models\Matriculacion;
use App\Models\Inscripcion;
use App\Models\Modalidad;
use App\Models\ExamenSuficiencia;
use App\Models\ExamenSuficienciaFechaSolicitud;
use App\Models\Docente;
use App\Models\Tutoria;
use App\Models\TutoriaPrecio;
use App\Models\TutoriaHorario;
use App\Models\TutoriaAlumno;
use App\Models\PagoTutoria;
use App\Models\Articulo;
use App\Models\SemestreMalla;
use App\Models\FechaDesmatriculacion;

class SolicitudController extends Controller
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
        $this->authorize('ver_solicitudes');

        try {
            $docente = Docente::where('usuario_id', Auth::id())->first();
            if ($docente) {
                $hoy = Carbon::today();
                $semestre = Semestre::whereDate('fecha_inicio', '<=', $hoy)
                                ->whereDate('fecha_fin', '>=', $hoy)
                                ->where('estado', 'AC')
                                ->orderBy('id', 'asc')
                                ->first();
                if ($semestre) {
                    $periodo_activo = $semestre ? $semestre->id : null;
                    $semestre_malla = SemestreMalla::where('coordinador_id', $docente->id)->where('semestre_id', $periodo_activo)->first();
                    if ($semestre_malla) {
                        $solicitudes = Solicitud::where('programa_id', $semestre_malla->malla->carrera->programa_id)->orderBy('created_at', 'desc')->get();
                    } else {
                        $semestre_malla = SemestreMalla::where('coordinador_id', $docente->id)->orderBy('id', 'desc')->first();
                        if ($semestre_malla) {
                            $solicitudes = Solicitud::where('programa_id', $semestre_malla->malla->carrera->programa_id)->orderBy('created_at', 'desc')->get();
                        }
                    }
                }
            } else {
                $solicitudes = Solicitud::orderBy('created_at', 'desc')->get();
            }
            return view('solicitudes/index')->with(compact('solicitudes'));
        } catch (\Exception $e) {
            return redirect()->route('solicitudes.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_solicitudes');

        try {
            $solicitud = Solicitud::with('pagoSolicitud')->findOrFail($id);

            if ($solicitud->tipo_solicitud_id == 5 && $solicitud->tutoria_id) {
                $pago_tutoria = PagoTutoria::where('tutoria_id', $solicitud->tutoria_id)->where('alumno_id', $solicitud->alumno_id)->first();

                return view('solicitudes/show')->with(compact('solicitud', 'pago_tutoria'));
            } elseif ($solicitud->tipo_solicitud_id == 2) {
                $modalidades = Modalidad::where('estado', 'AC')->get();
                $docentes = Docente::where('estado', 'AC')->get();

                return view('solicitudes/show')->with(compact('solicitud', 'modalidades', 'docentes'));
            } else {
                return view('solicitudes/show')->with(compact('solicitud'));
            }
        } catch (\Exception $e) {
            return redirect()->route('solicitudes.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_solicitudes');

        $request->validate([
            'tipo_solicitud' => ['required', 'numeric'],
            'materia_suficiencia' => ['nullable', 'numeric', 'required_if:tipo_solicitud,2'],
            'materia_inasistencia' => ['nullable', 'numeric', 'required_if:tipo_solicitud,3'],
            'adjunto_suficiencia' => ['nullable', 'file', 'extensions:pdf'],
            'adjunto_inasistencia' => ['nullable', 'required_if:tipo_solicitud,3', 'file', 'extensions:jpg,png,pdf'],
            'fecha_inasistencia' => ['nullable', 'required_if:tipo_solicitud,3', 'date'],
            'materia_tutoria' => ['nullable', 'numeric', 'required_if:tipo_solicitud,5'],
            'modalidad_tutoria' => ['nullable', 'numeric', 'required_if:tipo_solicitud,5'],
            'observacion' => 'nullable'
        ]);

        DB::beginTransaction();

        try {
            $alumno = Alumno::where('usuario_id', Auth::id())->first();
            $hoy = Carbon::today();
            $semestre = Semestre::whereDate('fecha_inicio', '<=', $hoy)
                            ->whereDate('fecha_fin', '>=', $hoy)
                            ->where('estado', 'AC')
                            ->orderBy('id', 'asc')
                            ->first();
            if (!$semestre) {
                return back()->with('error-message', 'No hay un semestre activo para realizar una solicitud.');
            }

            if ($request->tipo_solicitud == 3) {
                $cantidad_solicitudes = Solicitud::where('alumno_id', $alumno->id)->where('tipo_solicitud_id', 3)->where('semestre_id', $semestre->id)->where('estado', 'EN')->count();
                if ($cantidad_solicitudes > 3) {
                    return back()->with('error-message', 'La solicitud no puede ser realizada. Ya cuenta con 3 solicitudes de inasistencia en este semestre.');
                }
            }

            $matriculacion = Matriculacion::where('alumno_id', $alumno->id)->where('semestre_id', $semestre->id)->where('estado', 'AC')->first();
            if ($matriculacion) {
                $programa_alumno = $matriculacion->programa_id;
                $carrera_alumno = $matriculacion->carrera;
            } else {
                $matriculacion = Matriculacion::where('alumno_id', $alumno->id)->orderBy('id', 'desc')->first();
            }

            $solicitud = new Solicitud();
            $solicitud->fecha_solicitud = Carbon::now();
            $solicitud->alumno_id = $alumno->id;
            $solicitud->semestre_id = $semestre->id;
            $solicitud->programa_id = $programa_alumno;
            $solicitud->tipo_solicitud_id = $request->tipo_solicitud;
            $solicitud->observaciones = removeAccents(Str::upper($request->observaciones));

            // $articulo = Articulo::find($carrera_alumno->articulo_id)->first();
            // if (!$articulo) {
            //     return back()->with('error-message', 'El artículo no fue encontrado.');
            // }
            // else {
                if ($solicitud->tipo_solicitud_id == 1) {
                    $solicitud->estado = 'AP';
                    // $solicitud->articulo_id = $articulo->id;
                    $solicitud->fecha_aprobacion = Carbon::now();
                    $solicitud->save();

                    // ACTIVAR CON ADMINISTRACION

                    // $pago = new PagoSolicitud();
                    // $pago->solicitud_id = $solicitud->id;
                    // $pago->descripcion = $solicitud->tipoSolicitud->nombre;
                    // $pago->vencimiento = Carbon::createFromDate($solicitud->fecha_aprobacion)->addMonth();
                    // $pago->monto = $articulo->detalle->precio_certificado;
                    // $pago->saldo = $articulo->detalle->precio_certificado;
                    // $pago->save();

                    //HASTA ACA
                } else if ($request->tipo_solicitud == 2) {
                    $solicitud->materia_id = $request->materia_suficiencia;
                    $archivo = $request->adjunto_suficiencia;
                    $directorio = 'storage/solicitudes/suficiencias';
                    $directorio_storage = 'public/solicitudes/suficiencias';
                    $nombre_solicitud = 'suficiencia';
                } else if ($request->tipo_solicitud == 3) {
                    $solicitud->materia_id = $request->materia_inasistencias;
                    $solicitud->fecha_inasistencia = $request->fecha_inasistencia;
                    $archivo = $request->adjunto_inasistencias;
                    $directorio = 'storage/solicitudes/inasistenciass';
                    $directorio_storage = 'public/solicitudes/inasistenciass';
                    $nombre_solicitud = 'inasistencia';
                } else if ($request->tipo_solicitud == 5) {
                    $solicitud->materia_id = $request->materia_tutoria;
                    $solicitud->modalidad_id = $request->modalidad_tutoria;

                    // $old_tutoria = Tutoria::where('materia_id', $solicitud->materia_id)->where('modalidad_id', $request->modalidad_tutoria)->where('estado', 'AC')->first();
                    // if (!$old_tutoria) {
                    //     $tutoria = new Tutoria();
                    //     $tutoria->materia_id = $request->materia_tutoria;
                    //     $tutoria->semestre_id = $periodo_activo;
                    //     $tutoria->modalidad_id = $request->modalidad_tutoria;
                    //     $tutoria->cargado_por_id = Auth::id();
                    //     $tutoria->save();
                    // }
                } else if ($request->tipo_solicitud == 6) {
                    $solicitud->materia_id = $request->materia_desmatriculacion;
                }

                if ($request->tipo_solicitud == 2 || $request->tipo_solicitud == 3) {
                    if ($archivo) {
                        $extension = $archivo->getClientOriginalExtension();
                        if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png') {
                            $manager = new ImageManager(Driver::class);
                            $image = $manager->read($archivo);
                            $image = $image->encode(new AutoEncoder(quality: 50));
                            $nombre = $nombre_solicitud . '_' . Str::lower($alumno->primer_nombre) . '_' . $alumno->primer_apellido;
                            $nombre_archivo = $nombre . '.' . $extension;
                            $image->save($directorio . '/' . $nombre_archivo);
                        } else {
                            $nombre = $nombre_solicitud . '_' . Str::lower($alumno->primer_nombre) . '_' . $alumno->primer_apellido;
                            $nombre_archivo = $nombre . '.' . $extension;
                            Storage::putFileAs($directorio_storage . '/', $archivo, $nombre_archivo);
                        }
                        $solicitud->adjunto = $directorio . '/' . $nombre_archivo;
                    }
                }

                if ($request->tipo_generacion == 'AL') {
                    $solicitud->tipo_generacion = 'AL';
                } elseif ($request->tipo_generacion == 'AD') {
                    $solicitud->tipo_generacion = 'AD';
                }

                $solicitud->save();
            // }

            // if ($solicitud->tipo_solicitud_id != 5) {
            // } else {
            //     if (!$old_tutoria) {
            //         $tutoria_alumno = new TutoriaAlumno();
            //         $tutoria_alumno->tutoria_id = $tutoria->id;
            //         $tutoria_alumno->alumno_id = $solicitud->alumno_id;
            //         $tutoria_alumno->carrera_id = $carrera_alumno;
            //         $tutoria_alumno->save();

            //         $tutoria_precio = TutoriaPrecio::where('modalidad_id', $tutoria->modalidad_id)->first();

            //         $pago_tutoria = new PagoTutoria();
            //         $pago_tutoria->tutoria_id = $tutoria->id;
            //         $pago_tutoria->descripcion = 'TUTORIA ' . $tutoria->materia->nombre_fantasia . ' - ' . $tutoria->semestre->nombre;
            //         $pago_tutoria->vencimiento = Carbon::now();
            //         $pago_tutoria->monto = $tutoria_precio->precio;
            //         $pago_tutoria->saldo = $tutoria_precio->precio;
            //         $pago_tutoria->alumno_id = $tutoria_alumno->alumno_id;
            //         $pago_tutoria->save();
            //     } else {
            //         $alumno_existe = TutoriaAlumno::where('tutoria_id', $old_tutoria->id)->where('alumno_id', $solicitud->alumno_id)->exists();
            //         if (!$alumno_existe) {
            //             $tutoria_alumno = new TutoriaAlumno();
            //             $tutoria_alumno->tutoria_id = $old_tutoria->id;
            //             $tutoria_alumno->alumno_id = $solicitud->alumno_id;
            //             $tutoria_alumno->carrera_id = $carrera_alumno;
            //             $tutoria_alumno->save();

            //             $tutoria_precio = TutoriaPrecio::where('modalidad_id', $old_tutoria->modalidad_id)->first();

            //             $pago_tutoria = new PagoTutoria();
            //             $pago_tutoria->tutoria_id = $old_tutoria->id;
            //             $pago_tutoria->descripcion = 'TUTORIA ' . $old_tutoria->materia->nombre_fantasia . ' - ' . $old_tutoria->semestre->nombre;
            //             $pago_tutoria->vencimiento = Carbon::now();
            //             $pago_tutoria->monto = $tutoria_precio->precio;
            //             $pago_tutoria->saldo = $tutoria_precio->precio;
            //             $pago_tutoria->alumno_id = $tutoria_alumno->alumno_id;
            //             $pago_tutoria->save();
            //         } else {
            //             return back()->with('error-message', 'La solicitud no puede ser realizada, ya se encuentra inscripto en una tutoría de la materia seleccionada.');
            //         }
            //     }
            // }

            DB::commit();

            return redirect()->route('pantallas_alumnos.index', Auth::id())->with('success-message','Su solicitud fue cargada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('pantallas_alumnos.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function approve(Request $request, $id)
    {
        $this->authorize('aprobar_solicitudes');

        if ($request->tipo == 'SUFICIENCIA') {
            $request->validate([
                'fecha_examen' => ['required', 'date',
                    function ($attribute, $value, $fail) {
                        $fecha_hoy = Carbon::now()->startOfDay();
                        $fecha_examen = Carbon::createFromFormat('Y-m-d H:i:s', $value)->startOfDay();
                        if ($fecha_examen->diffInDays($fecha_hoy) < 2) {
                            $fail('La fecha de examen no puede ser anterior a 48 horas desde hoy.');
                        }
                    }
                ],
                'modalidad' => ['required', 'numeric'],
                'docente' => ['required', 'numeric']
            ]);
        }

        DB::beginTransaction();

        try {
            $solicitud = Solicitud::findOrFail($id);
            $solicitud->estado = 'AP';
            $solicitud->fecha_aprobacion = Carbon::now();
            $solicitud->aprobado_por_id = Auth::id();

            if ($solicitud->tipo_solicitud_id == 1) {
                //
            } elseif ($solicitud->tipo_solicitud_id == 2) {
                $old_inscripciones = Inscripciones::whereHas('matriculacion', function ($query) use ($solicitud) {
                    $query->where('semestre_id', $solicitud->semestre_id)
                        ->where('alumno_id', $solicitud->alumno_id)
                        ->where('estado', 'AC');
                })->where('materia_id', $solicitud->materia_id)
                  ->first();

                // $pago = new PagoSolicitud();
                // $pago->solicitud_id = $solicitud->id;
                // if ($solicitud->tipo_solicitud_id == 2) {
                //     $pago->descripcion = $solicitud->tipoSolicitud->nombre . ' ' . $solicitud->materia->nombre_real . ' - ' . $solicitud->semestre->nombre;
                // } else {
                //     $pago->descripcion = $solicitud->tipoSolicitud->nombre;
                // }

                // $articulo = Articulo::where('carrera_id', $matriculacion->carrera_id)->where('precio_examen_suficiencia', '>', 0)->first();
                // $solicitud->articulo_id = $articulo->id;

                // $pago->vencimiento = Carbon::createFromDate($solicitud->fecha_aprobacion)->addMonth();
                // $pago->monto = $articulo->precio_examen_suficiencia;
                // $pago->saldo = $articulo->precio_examen_suficiencia;
                // $pago->save();

                if ($inscripcion->matriculacion->semestre->estado == 'IN') {
                    return back()->with('error-message', 'El exámen de suficiencia no puede realizarse. El semestre se encuentra cerrado.');
                }

                if ($inscripcion->estado != 'MA') {
                    $fecha_suficiencia = ExamenSuficienciaFechaSolicitud::where('semestre_id', $inscripcion->matriculacion->semestre_id)->where('programa_id', $inscripcion->matriculacion->programa_id)->where('estado', 'AC')->first();
                    if (!$fecha_suficiencia) {
                        return back()->with('error-message', 'El exámen de suficiencia del alumno ' . $inscripcion->matriculacion->alumno->primer_nombre . ' ' . $inscripcion->matriculacion->alumno->primer_apellido . ' no puede realizarse. Primeramente se deben parametrizar las fechas El exámen de suficiencia.');
                    }

                    $fecha_hoy = Carbon::today();
                    $fecha_inicio_suficiencia = Carbon::createFromDate($fecha_suficiencia->fecha_inicio)->startOfDay();
                    $fecha_fin_suficiencia = Carbon::createFromDate($fecha_suficiencia->fecha_fin)->endOfDay();

                    if (!$fecha_hoy->between($fecha_inicio_suficiencia, $fecha_fin_suficiencia)) {
                        return back()->with('error-message', 'El exámen de suficiencia del alumno ' . $inscripcion->matriculacion->alumno->primer_nombre . ' ' . $inscripcion->matriculacion->alumno->primer_apellido . ' no puede realizarse. La fecha de hoy no se encuentra entre las fechas de examenes de suficiencia parametrizadas.');
                    }
                } else {
                    if ($inscripcion->estado == 'RE') {
                        $tipo = 'reprobó';
                    } else {
                        $tipo = 'aprobó';
                    }
                    return back()->with('error-message', 'El exámen de suficiencia del alumno ' . $inscripcion->matriculacion->alumno->primer_nombre . ' ' . $inscripcion->matriculacion->alumno->primer_apellido . ' no puede realizarse. El alumno ya ' . $tipo . ' la materia.');
                }

                $examen = new ExamenSuficiencia();
                $examen->solicitud_id = $solicitud->id;
                $examen->alumno_id = $solicitud->alumno_id;
                $examen->carrera_id = $matriculacion->carrera_id;
                $examen->materia_id = $solicitud->materia_id;
                $examen->docente_id = $request->docente;
                $examen->semestre_id = $solicitud->semestre_id;
                $examen->modalidad_id = $request->modalidad;
                $examen->fecha_examen = $request->fecha_examen;
                $examen->cargado_por_id = Auth::id();
                $examen->save();

                if (!$old_inscripcion) {
                    $inscripcion = new Inscripcion();
                    $inscripcion->fecha = Carbon::now();
                    $inscripcion->matriculacion_id = $matriculacion->id;
                    $inscripcion->materia_id = $examen->materia_id;
                    $inscripcion->alumno_id = $examen->alumno_id;
                    $inscripcion->docente_id = $examen->docente_id;
                    $inscripcion->estado = 'MA';
                    $inscripcion->cargado_por_id = Auth::id();
                    $inscripcion->save();
                }
            } elseif ($solicitud->tipo_solicitud_id == 3) {
                //
            } elseif ($solicitud->tipo_solicitud_id == 4) {
                // $articulo = Articulo::where('carrera_id', $matriculacion->carrera_id)->where('precio_constancia_carrera', '>', 0)->first();
                // $solicitud->articulo_id = $articulo->id;

                // $pago->vencimiento = Carbon::createFromDate($solicitud->fecha_aprobacion)->addMonth();
                // $pago->monto = $articulo->precio_constancia_carrera;
                // $pago->saldo = $articulo->precio_constancia_carrera;
                // $pago->save();
            } elseif ($solicitud->tipo_solicitud_id == 5) {
                $carrera_alumno = $matriculacion->carrera_id;
                // $tutoria_precio = TutoriaPrecio::where('modalidad_id', $solicitud->modalidad_id)->where('carrera_id', $carrera_alumno)->first();

                $old_tutoria = Tutoria::where('materia_id', $solicitud->materia_id)->where('modalidad_id', $solicitud->modalidad_id)->where('estado', 'AC')->first();
                if (!$old_tutoria) {
                    $tutoria = new Tutoria();
                    $tutoria->materia_id = $solicitud->materia_id;
                    $tutoria->semestre_id = $solicitud->semestre_id;
                    $tutoria->modalidad_id = $solicitud->modalidad_id;
                    $tutoria->cargado_por_id = Auth::id();
                    $tutoria->save();

                    $tutoria_alumno = new TutoriaAlumno();
                    $tutoria_alumno->tutoria_id = $tutoria->id;
                    $tutoria_alumno->alumno_id = $solicitud->alumno_id;
                    $tutoria_alumno->carrera_id = $carrera_alumno;
                    $tutoria_alumno->save();

                    // $pago_tutoria = new PagoTutoria();
                    // $pago_tutoria->tutoria_id = $tutoria->id;
                    // $pago_tutoria->descripcion = 'TUTORIA ' . $tutoria->materia->nombre_fantasia . ' - ' . $tutoria->semestre->nombre;
                    // $pago_tutoria->vencimiento = Carbon::now();
                    // $pago_tutoria->monto = $tutoria_precio->articulo->detalle->precio_contado;
                    // $pago_tutoria->saldo = $tutoria_precio->articulo->detalle->precio_contado;
                    // $pago_tutoria->alumno_id = $tutoria_alumno->alumno_id;
                    // $pago_tutoria->save();

                    $solicitud->tutoria_id = $tutoria->id;
                } else {
                    $alumno_existe = TutoriaAlumno::where('tutoria_id', $old_tutoria->id)->where('alumno_id', $solicitud->alumno_id)->exists();
                    if (!$alumno_existe) {
                        $tutoria_alumno = new TutoriaAlumno();
                        $tutoria_alumno->tutoria_id = $old_tutoria->id;
                        $tutoria_alumno->alumno_id = $solicitud->alumno_id;
                        $tutoria_alumno->carrera_id = $carrera_alumno;
                        $tutoria_alumno->save();

                        // $pago_tutoria = new PagoTutoria();
                        // $pago_tutoria->tutoria_id = $old_tutoria->id;
                        // $pago_tutoria->descripcion = 'TUTORIA ' . $old_tutoria->materia->nombre_fantasia . ' - ' . $old_tutoria->semestre->nombre;
                        // $pago_tutoria->vencimiento = Carbon::now();
                        // $pago_tutoria->monto = $tutoria_precio->articulo->detalle->precio_contado;
                        // $pago_tutoria->saldo = $tutoria_precio->articulo->detalle->precio_contado;
                        // $pago_tutoria->alumno_id = $tutoria_alumno->alumno_id;
                        // $pago_tutoria->save();

                        $solicitud->tutoria_id = $old_tutoria->id;
                    } else {
                        return back()->with('error-message', 'La solicitud no puede ser aprobada, el alumno ya se encuentra inscripto en una tutoría de la materia seleccionada.');
                    }
                }
            } elseif ($solicitud->tipo_solicitud_id == 6) {
                $inscripcion = Inscripciones::whereHas('matriculacion', function ($query) use ($solicitud) {
                    $query->where('semestre_id', $solicitud->semestre_id)
                        ->where('alumno_id', $solicitud->alumno_id)
                        ->where('estado', 'AC');
                })->where('materia_id', $solicitud->materia_id)
                  ->first();

                if ($inscripcion->matriculacion->semestre->estado == 'IN') {
                    return back()->with('error-message', 'La desmatriculación no puede realizarse. El semestre se encuentra cerrado.');
                }

                if ($inscripcion->estado != 'MA' || $inscripcion->estado != 'EC') {
                    $fecha_desmatriculacion = FechaDesmatriculacion::where('semestre_id', $inscripcion->matriculacion->semestre_id)->where('programa_id', $inscripcion->matriculacion->programa_id)->where('estado', 'AC')->first();
                    if (!$fecha_desmatriculacion) {
                        return back()->with('error-message', 'La desmatriculación del alumno ' . $inscripcion->matriculacion->alumno->primer_nombre . ' ' . $inscripcion->matriculacion->alumno->primer_apellido . ' no puede realizarse. Primeramente se deben parametrizar las fechas de desmatriculación.');
                    }

                    $fecha_hoy = Carbon::today();
                    $fecha_inicio_desmatriculacion = Carbon::createFromDate($fecha_desmatriculacion->fecha_inicio)->startOfDay();
                    $fecha_fin_desmatriculacion = Carbon::createFromDate($fecha_desmatriculacion->fecha_fin)->endOfDay();

                    if ($fecha_hoy->between($fecha_inicio_desmatriculacion, $fecha_fin_desmatriculacion)) {
                        $inscripcion->estado = 'DE';
                        $inscripcion->save();
                    } else {
                        return back()->with('error-message', 'La desmatriculación del alumno ' . $inscripcion->matriculacion->alumno->primer_nombre . ' ' . $inscripcion->matriculacion->alumno->primer_apellido . ' no puede realizarse. La fecha de hoy no se encuentra entre las fechas de desmatriculación parametrizadas.');
                    }
                } else {
                    if ($inscripcion->estado == 'RE') {
                        $tipo = 'reprobó';
                    } else {
                        $tipo = 'aprobó';
                    }
                    return back()->with('error-message', 'La desmatriculación del alumno ' . $inscripcion->matriculacion->alumno->primer_nombre . ' ' . $inscripcion->matriculacion->alumno->primer_apellido . ' no puede realizarse. El alumno ya ' . $tipo . ' la materia.');
                }
            }

            $solicitud->save();

            DB::commit();

            if ($solicitud->tipo_solicitud_id == 2) {
                return response()->json([
                    'message' => 'La solicitud ' . $solicitud->tipoSolicitud->nombre . ' del alumno ' . $solicitud->alumno->primer_nombre . ' ' . $solicitud->alumno->primer_apellido . ' fue aprobada exitosamente.',
                ]);
            } else {
                return redirect()->route('solicitudes.show', $id)->with('success-message','La solicitud ' . $solicitud->tipoSolicitud->nombre . ' del alumno ' . $solicitud->alumno->primer_nombre . ' ' . $solicitud->alumno->primer_apellido . ' fue aprobada exitosamente.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('solicitudes.index')->with('error-message', $e->getMessage());
        }
    }

    public function unapprove($id)
    {
        $this->authorize('anular_aprobacion_solicitudes');

        DB::beginTransaction();

        try {
            $solicitud = Solicitud::findOrFail($id);
            if ($solicitud->estado == 'AP') {
                $solicitud->estado = 'PE';
                $solicitud->fecha_aprobacion = null;
                $solicitud->aprobado_por_id = null;
                $solicitud->save();

                if ($solicitud->tipo_solicitud_id == 2) {
                    $examen = ExamenSuficiencia::where('solicitud_id', $solicitud->id)->where('estado', '!=', 'AC')->first();
                    if ($examen) {
                        switch ($examen->estado) {
                            case 'PA':
                                $tipo = 'pagado';
                                break;
                            case 'AP':
                                $tipo = 'aprobado';
                                break;
                            case 'RE':
                                $tipo = 'reprobado';
                                break;
                            default:
                                $tipo = 'calificado';
                                break;
                        }
                        return redirect()->route('solicitudes.show', $id)->with('error-message', 'La aprobación de la solicitud no puede ser anulada. El examen de suficiencia ya se encuentra ' . $tipo . '.');
                    } else {
                        ExamenSuficiencia::where('solicitud_id', $solicitud->id)->delete();
                    }
                } else if ($solicitud->tipo_solicitud_id == 5) {
                    $tutoria = Tutoria::findOrFail($solicitud->tutoria_id);
                    if ($tutoria->estado != 'AC') {
                        switch ($tutoria->estado) {
                            case 'EC':
                                $tipo = 'en curso';
                                break;
                            case 'FI':
                                $tipo = 'finalizada';
                                break;
                            default:
                                $tipo = 'en curso';
                                break;
                        }
                        return redirect()->route('solicitudes.show', $id)->with('error-message', 'La aprobación de la solicitud no puede ser anulada. La tutoría ya se encuentra ' . $tipo . '.');
                    } else {
                        $tutoria_alumno = TutoriaAlumno::where('tutoria_id', $solicitud->tutoria_id)->where('alumno_id', $solicitud->alumno_id)->first();
                        if ($tutoria_alumno->estado == 'PA') {
                            return redirect()->route('solicitudes.show', $id)->with('error-message', 'La aprobación de la solicitud no puede ser anulada. El alumno ya abonó la tutoría.');
                        } else {
                            $tutoria_alumno->delete();
                            PagoTutoria::where('tutoria_id', $solicitud->tutoria_id)->where('alumno_id', $solicitud->alumno_id)->delete();
                            $tutoria = Tutoria::findOrFail($solicitud->tutoria_id);
                            if ($tutoria->alumnos->count() == 0) {
                                TutoriaHorario::where('tutoria_id', $tutoria->id)->delete();
                                $tutoria->delete();
                            }
                        }
                    }
                }

                if (PagoSolicitud::where('solicitud_id', $solicitud->id)->exists()) {
                    PagoSolicitud::where('solicitud_id', $solicitud->id)->delete();
                }

                DB::commit();

                return redirect()->route('solicitudes.show', $id)->with('error-message','La aprobación de la solicitud ' . $solicitud->tipoSolicitud->nombre . ' del alumno ' . $solicitud->alumno->primer_nombre . ' ' . $solicitud->alumno->primer_apellido . ' fue anulada exitosamente.');
            } else {
                return redirect()->route('solicitudes.show', $id)->with('error-message','La aprobación de la solicitud ' . $solicitud->tipoSolicitud->nombre . ' del alumno ' . $solicitud->alumno->primer_nombre . ' ' . $solicitud->alumno->primer_apellido . ' no se puede anular. Ya se encuentra pagada.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('solicitudes.index')->with('error-message', $e->getMessage());
        }
    }

    public function para_entrega($id)
    {
        $this->authorize('retirar_solicitudes');

        DB::beginTransaction();

        try {
            $solicitud = Solicitud::findOrFail($id);
            $solicitud->estado = 'PR';
            $solicitud->save();

            DB::commit();

            return redirect()->route('solicitudes.show', $id)->with('success-message','El estado de la solicitud ' . $solicitud->tipoSolicitud->nombre . ' del alumno ' . $solicitud->alumno->primer_nombre . ' ' . $solicitud->alumno->primer_apellido . ' fue cambiado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('solicitudes.index')->with('error-message', $e->getMessage());
        }
    }

    public function reject($id)
    {
        $this->authorize('rechazar_solicitudes');

        DB::beginTransaction();

        try {
            $solicitud = Solicitud::findOrFail($id);
            if ($solicitud->estado == 'PE') {
                $solicitud->estado = 'RE';
                $solicitud->rechazado_por_id = Auth::id();
                $solicitud->save();

                DB::commit();

                return redirect()->route('solicitudes.show', $id)->with('error-message','La solicitud ' . $solicitud->tipoSolicitud->nombre . ' del alumno ' . $solicitud->alumno->primer_nombre . ' ' . $solicitud->alumno->primer_apellido . ' fue rechazada exitosamente.');
            } else {
                return redirect()->route('solicitudes.show', $id)->with('error-message','La solicitud ' . $solicitud->tipoSolicitud->nombre . ' del alumno ' . $solicitud->alumno->primer_nombre . ' ' . $solicitud->alumno->primer_apellido . ' no se puede rechazar. Ya se encuentra aprobada.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('solicitudes.index')->with('error-message', $e->getMessage());
        }
    }

    public function unreject($id)
    {
        $this->authorize('anular_rechazo_solicitudes');

        DB::beginTransaction();

        try {
            $solicitud = Solicitud::findOrFail($id);
            $solicitud->estado = 'PE';
            $solicitud->rechazado_por_id = null;
            $solicitud->save();

            DB::commit();

            return redirect()->route('solicitudes.show', $id)->with('success-message','El rechazo de la solicitud ' . $solicitud->tipoSolicitud->nombre . ' del alumno ' . $solicitud->alumno->primer_nombre . ' ' . $solicitud->alumno->primer_apellido . ' fue anulado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('solicitudes.index')->with('error-message', $e->getMessage());
        }
    }

    public function deliver($id)
    {
        $this->authorize('entregar_solicitudes');

        DB::beginTransaction();

        try {
            $solicitud = Solicitud::findOrFail($id);
            if (($solicitud->estado == 'PA' || $solicitud->estado == 'PR') && $solicitud->pagoSolicitud->estado == 'CA') {
                $solicitud->estado = 'EN';
                $solicitud->fecha_entrega = Carbon::now();
                $solicitud->entregado_por_id = Auth::id();
                $solicitud->save();

                DB::commit();

                return redirect()->route('solicitudes.show', $id)->with('success-message','La solicitud ' . $solicitud->tipoSolicitud->nombre . ' del alumno ' . $solicitud->alumno->primer_nombre . ' ' . $solicitud->alumno->primer_apellido . ' fue entregada exitosamente.');
            } else {
                return redirect()->route('solicitudes.show', $id)->with('error-message','La solicitud ' . $solicitud->tipoSolicitud->nombre . ' del alumno ' . $solicitud->alumno->primer_nombre . ' ' . $solicitud->alumno->primer_apellido . ' no se puede entregar. El pago no fue realizado.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('solicitudes.index')->with('error-message', $e->getMessage());
        }
    }

    public function undeliver($id)
    {
        $this->authorize('anular_entrega_solicitudes');

        DB::beginTransaction();

        try {
            $solicitud = Solicitud::findOrFail($id);
            $solicitud->estado = 'PA';
            $solicitud->fecha_entrega = null;
            $solicitud->entregado_por_id = null;
            $solicitud->save();

            DB::commit();

            return redirect()->route('solicitudes.show', $id)->with('error-message','La entrega de la solicitud ' . $solicitud->tipoSolicitud->nombre . ' del alumno ' . $solicitud->alumno->primer_nombre . ' ' . $solicitud->alumno->primer_apellido . ' fue anulada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('solicitudes.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_solicitudes');

        DB::beginTransaction();

        try {
            $solicitud = Solicitud::findOrFail($id);
            if ($solicitud->estado == 'PE' || $solicitud->estado == 'RE') {
                if ($solicitud->pagoSolicitud) {
                    $solicitud->pagoSolicitud->delete();
                }
                $solicitud->delete();

                DB::commit();

                return redirect()->route('solicitudes.index')->with('success-message','La solicitud ' . $solicitud->tipoSolicitud->nombre . ' del alumno ' . $solicitud->alumno->primer_nombre . ' ' . $solicitud->alumno->primer_apellido . ' fue eliminada exitosamente.');
            } else {
                switch ($solicitud->estado) {
                    case 'AP':
                        $estado = 'aprobada';
                        break;
                    case 'PA':
                        $estado = 'pagada';
                        break;
                    case 'EN':
                        $estado = 'entregada';
                        break;
                    default:
                        break;
                }

                return redirect()->route('solicitudes.index')->with('error-message','La solicitud ' . $solicitud->tipoSolicitud->nombre . ' del alumno ' . $solicitud->alumno->primer_nombre . ' ' . $solicitud->alumno->primer_apellido . ' no se puede eliminar porque ya se encuentra ' . $estado . '.');
            }
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('solicitudes.index')->with('error-message', 'La solicitud ' . $solicitud->tipoSolicitud->nombre . ' del alumno ' . $solicitud->alumno->primer_nombre . ' ' . $solicitud->alumno->primer_apellido . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('solicitudes.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
