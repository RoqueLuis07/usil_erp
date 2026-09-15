<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

use App\Models\Matriculacion;
use App\Models\PagoMatriculacion;
use App\Models\Inscripcion;
use App\Models\Alumno;
use App\Models\Semestre;
use App\Models\SemestreMalla;
use App\Models\Programa;
use App\Models\Carrera;
use App\Models\Malla;
use App\Models\MallaDetalle;
use App\Models\Empresa;
use App\Models\DiaSemana;
use App\Models\Articulo;
use App\Models\AlumnoPuntaje;
use App\Models\AlumnoNota;
use App\Models\Convenio;

class MatriculacionController extends Controller
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

    public function index(Request $request)
    {
        $this->authorize('ver_matriculaciones');

        try {
            $buscar = Str::upper($request->buscar);

            $hoy = Carbon::today();
            $semestre = Semestre::whereDate('fecha_inicio', '<=', $hoy)
                            ->whereDate('fecha_fin', '>=', $hoy)
                            ->where('estado', 'AC')
                            ->orderBy('id', 'asc')
                            ->first();

            if (!$request->filtro_periodo && ($request->filtro_fecha || $request->filtro_alumno || $request->filtro_ingreso || $request->filtro_programa || $request->filtro_carrera || $request->filtro_carrera_siu)) {
                $filtro_periodo = null;
            } else {
                $filtro_periodo = $semestre->id;
            }

            $filtro_periodo = $request->has('filtro_periodo') ? $request->filtro_periodo : $semestre->id;
            $filtro_fecha = $request->filtro_fecha;
            $filtro_alumno = $request->filtro_alumno;
            $filtro_ingreso = $request->filtro_ingreso;
            $filtro_programa = $request->filtro_programa;
            $filtro_carrera = $request->filtro_carrera;
            $filtro_carrera_siu = $request->filtro_carrera_siu;

            $query = Matriculacion::select('matriculaciones.*')
                        ->join('alumnos', 'matriculaciones.alumno_id', '=', 'alumnos.id')
                        ->with(['alumno', 'semestre', 'programa', 'carrera', 'carreraSiu']);

            if (!(blank($filtro_periodo))) {
                $query->where('semestre_id', $filtro_periodo);
            }

            if (!(blank($filtro_fecha))) {
                list($mes_inicio, $mes_fin) = explode('-', $filtro_fecha);
                $anho_actual = Carbon::now()->year;
                $fecha_inicio = Carbon::createFromFormat('Y-m', "{$anho_actual}-{$mes_inicio}")->startOfMonth();
                $fecha_fin = Carbon::createFromFormat('Y-m', "{$anho_actual}-{$mes_fin}")->endOfMonth();

                $query->whereBetween('fecha', [$fecha_inicio, $fecha_fin]);
            }

            if (!(blank($filtro_alumno))) {
                $query->where('alumno_id', $filtro_alumno);
            }

            if (!(blank($filtro_ingreso))) {
                $query->whereHas('alumno.matriculaciones', function($query) use ($filtro_ingreso) {
                    $query->whereHas('semestre', function($query) use ($filtro_ingreso) {
                        $query->where('id', $filtro_ingreso);
                    });
                });
            }

            if (!(blank($filtro_programa))) {
                $query->where('programa_id', $filtro_programa);
            }

            if (!(blank($filtro_carrera))) {
                $query->where('carrera_id', $filtro_carrera);
            }

            if (!(blank($filtro_carrera_siu))) {
                $query->where('carrera_siu_id', $filtro_carrera_siu);
            }

            if (!(blank($buscar))) {
				$query->where(function($q) use ($buscar) {
                    $q->where('matriculaciones.id', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%')
                      ->orWhere('matriculaciones.fecha', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%')
                      ->orWhereHas('alumno', function ($query) use ($buscar) {
                            $query->where('primer_nombre', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%')
                                ->orWhere('primer_apellido', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%')
                                ->orWhere('segundo_apellido', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%')
                                ->orWhere('numero_documento', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%');
                        })
                        ->orWhereHas('semestre', function ($query) use ($buscar) {
                            $query->where('nombre', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%');
                        })
                        ->orWhereHas('programa', function ($query) use ($buscar) {
                            $query->where('nombre', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%');
                        })
                        ->orWhereHas('carrera', function ($query) use ($buscar) {
                            $query->where('nombre_fantasia', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%');
                        })
                        ->orWhereHas('carreraSiu', function ($query) use ($buscar) {
                            $query->where('nombre_fantasia', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%');
                        });
                });
			}

            $matriculaciones = $query->orderBy('fecha', 'desc')->paginate(50);

            $matriculaciones_all = $query->orderBy('fecha', 'desc')->get();
            foreach ($matriculaciones_all as $matriculacion) {
                $primera_matriculacion = Matriculacion::where('alumno_id', $matriculacion->alumno_id)->orderBy('id', 'asc')->first();
                if ($primera_matriculacion) {
                    foreach ($matriculaciones as $m) {
                        if ($primera_matriculacion) {
                            $m->ingreso = $primera_matriculacion->semestre->nombre;
                        } else {
                            $m->ingreso = null;
                        }

                    }
                }
            }

            $semestres = Semestre::orderBy('id', 'desc')->get();
            $alumnos = Alumno::where('ubs', false)->where('estado', 'AC')->get();
            $programas = Programa::whereIn('id', [1, 2, 3, 8])->where('estado', 'AC')->get();
            $carreras = Carrera::whereHas('programa', function ($query) {
                $query->whereIn('id', [1, 2, 3, 8]);
            })->where('estado', 'AC')->get();

            $carreras_siu = Carrera::whereHas('programa', function ($query) {
                $query->where('id', 4);
            })->where('estado', 'AC')->get();

            return view('matriculaciones/index')->with(compact('matriculaciones', 'semestres', 'alumnos', 'programas', 'carreras', 'carreras_siu', 'buscar', 'filtro_periodo', 'filtro_fecha', 'filtro_alumno', 'filtro_ingreso', 'filtro_programa', 'filtro_carrera', 'filtro_carrera_siu'));
        } catch (\Exception $e) {
            return redirect()->route('matriculaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_matriculaciones');

        try {
            $matriculacion = Matriculacion::with(['pagosMatriculacion' => function ($query) {
                $query->orderBy('id');
            }], 'convenio.detalle')->findOrFail($id);
            $convenios = Convenio::where('tipo', 'CO')->where('estado', 'AC')->orderBy('nombre', 'asc')->get();
            return view('matriculaciones/show')->with(compact('matriculacion', 'convenios'));
        } catch (\Exception $e) {
            return redirect()->route('matriculaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_matriculaciones');

        try {
            $alumnos = Alumno::where('estado', 'AC')->get();
            $semestres = Semestre::whereHas('semestreMallas')->with('semestreMallas.malla.carrera.programa')->where('estado', 'AC')->get();
            // $semestres = Semestre::whereHas('semestreMallas', function($query) {
            //     $query->where('fecha_inicio_matriculacion', '<=', Carbon::today())
            //           ->where('fecha_fin_matriculacion', '>=', Carbon::today());
            // })->with('semestreMallas.malla.carrera.programa')
            //   ->where('estado', 'AC')
            //   ->get();
            $programas = Programa::whereIn('id', [1, 2, 3, 8])->where('estado', 'AC')->get();
            $carreras = Carrera::where('estado', 'AC')->get();
            $convenios = Convenio::where('tipo', 'CO')->where('estado', 'AC')->orderBy('nombre', 'asc')->get();

            return view('matriculaciones/create')->with(compact('alumnos', 'semestres', 'programas', 'carreras', 'convenios'));
        } catch (\Exception $e) {
            return redirect()->route('matriculaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_matriculaciones');

        // if ($request->semestre && $request->programa && $request->carrera) {
        //     $malla = Malla::where('carrera_id', $request->carrera)->where('estado', 'AC')->first();
        //     $semestre_malla = SemestreMalla::where('semestre_id', $request->semestre)->where('malla_id', $malla->id)->where('estado', 'AC')->first();
        //     $alumno = Alumno::findOrFail($request->alumno);
        //     if (!($semestre_malla->cantidad_cuotas && $semestre_malla->precio_contado && $semestre_malla->precio_cuota && $semestre_malla->precio_multa >=0 && $semestre_malla->dia_vencimiento_cuota && $semestre_malla->fecha_inicio_vencimiento_cuota && $semestre_malla->dias_gracia >= 0)) {
        //         return redirect()->route('matriculaciones.index')->with('error-message', 'La matriculación del alumno ' . $alumno->primer_nombre . ' ' . $alumno->primer_apellido . ' no se puede crear. Debe completar los precios y multas de la carrera ' . $semestre_malla->malla->carrera->nombre_fantasia . ' en el semestre.');
        //     }
        // }

        $request->validate([
            'alumno' => ['required', 'numeric'],
            'semestre' => ['required', 'numeric'],
            'programa' => ['required', 'numeric'],
            'carrera' => ['required', 'numeric', Rule::unique('matriculaciones', 'carrera_id')
                ->where(fn ($query) => $query->where('carrera_id', $request->carrera)
                ->where('programa_id', $request->programa)
                ->where('semestre_id', $request->semestre)
                ->where('alumno_id', $request->alumno)
                ->where('estado', 'AC'))],
            //El de arriba verifica que el conjunto de alumno, semestre, programa y carrera no existan,
            'validacion' => ['required', 'numeric'],
            'carrera_siu' => ['nullable', 'numeric', 'required_if:validacion,1'],
            'tipo_pago' => 'required',
            'convenio' => ['nullable', 'numeric'],
        ]);

        DB::beginTransaction();

        try {
            $matriculacion = new Matriculacion();
            $matriculacion->fecha = Carbon::now();
            $matriculacion->alumno_id = $request->alumno;
            $matriculacion->semestre_id = $request->semestre;
            $matriculacion->programa_id = $request->programa;
            $matriculacion->carrera_id = $request->carrera;
            $matriculacion->carrera_siu_id = $request->carrera_siu;
            $matriculacion->tipo_pago = $request->tipo_pago;
            $matriculacion->convenio_id = $request->convenio;
            $matriculacion->cargado_por_id = Auth::id();
            $matriculacion->save();

            // ACTIVAR PARA CUANDO SE HABILITE ADMINISTRACION

            // $carrera = Carrera::find($matriculacion->carrera_id);
            // if ($carrera) {
            //     $articulo = Articulo::find($carrera->articulo_id);
            // } else {
            //     return back()->with('error-message', 'Hubo un problema al realizar la matriculación. Si el problema persiste, contacte con el administador.');
            // }

            // if ($articulo) {
            //     switch ($matriculacion->tipo_pago) {
            //         case 'CO': //caso contado
            //             $cantidad_cuotas = 2;
            //             for ($i = 1; $i <= $cantidad_cuotas; $i++) {
            //                 $pago_matriculacion = new PagoMatriculacion();
            //                 $pago_matriculacion->matriculacion_id = $matriculacion->id;
            //                 $pago_matriculacion->alumno_id = $matriculacion->alumno_id;
            //                 if ($i == 1) {
            //                     $pago_matriculacion->descripcion = 'MATRICULA - ' . $matriculacion->semestre->nombre;
            //                     $pago_matriculacion->tipo = 'MA';
            //                     $pago_matriculacion->fecha_vencimiento = Carbon::now()->toDateString();
            //                     $pago_matriculacion->monto = $articulo->detalle->precio_matricula;
            //                     $pago_matriculacion->monto_convenio = 0;
            //                     $pago_matriculacion->monto_multa = 0;
            //                     $pago_matriculacion->saldo = $articulo->detalle->precio_matricula;
            //                 } else {
            //                     $pago_matriculacion->descripcion = 'SEMESTRE COMPLETO - ' . $matriculacion->semestre->nombre;
            //                     $pago_matriculacion->tipo = 'SC';
            //                     $pago_matriculacion->fecha_vencimiento = Carbon::now()->toDateString();
            //                     $pago_matriculacion->monto = $articulo->detalle->precio_contado;
            //                     $pago_matriculacion->monto_convenio = 0;
            //                     $pago_matriculacion->monto_multa = 0;
            //                     $pago_matriculacion->saldo = $articulo->detalle->precio_contado;
            //                 }
            //                 $pago_matriculacion->moneda_id = 1;
            //                 $pago_matriculacion->save();
            //             }
            //             //agregar lo de contabilidad, para que esto vaya a cobros_diferidos

            //             break;
            //         case 'CR': //caso cuotas
            //             $cantidad_cuotas = $articulo->detalle->cantidad_cuotas;
            //             $fecha = Carbon::createFromDate($articulo->detalle->fecha_vencimiento_primera_cuota);
            //             $dia = $articulo->detalle->dia_vencimiento_cuotas;
            //             $mes = $fecha->format('m');
            //             $anho = $fecha->format('Y');

            //             for ($i = 0; $i <= $cantidad_cuotas; $i++) {
            //                 $pago_matriculacion = new PagoMatriculacion();
            //                 $pago_matriculacion->matriculacion_id = $matriculacion->id;
            //                 $pago_matriculacion->alumno_id = $matriculacion->alumno_id;
            //                 if ($i == 0) {
            //                     $pago_matriculacion->descripcion = 'MATRICULA - ' . $matriculacion->semestre->nombre;
            //                     $pago_matriculacion->tipo = 'MA';
            //                     $pago_matriculacion->fecha_vencimiento = $articulo->detalle->fecha_vencimiento_primera_cuota;
            //                     $pago_matriculacion->monto = $articulo->detalle->precio_matricula;
            //                     $pago_matriculacion->monto_convenio = 0;
            //                     $pago_matriculacion->monto_multa = 0;
            //                     $pago_matriculacion->saldo = $articulo->detalle->precio_matricula;
            //                 } else {
            //                     $pago_matriculacion->descripcion = 'CUOTA ' . $i . ' DE ' . $cantidad_cuotas . ' - ' . $matriculacion->semestre->nombre;
            //                     if ($i == 1) {
            //                         $fecha_vencimiento = $articulo->detalle->fecha_vencimiento_primera_cuota;
            //                         $pago_matriculacion->fecha_vencimiento = $fecha_vencimiento;
            //                         $pago_matriculacion->tipo = '1C';
            //                     } else {
            //                         if ($i == 2) {
            //                             $pago_matriculacion->fecha_vencimiento = Carbon::parse($anho . '-' . $mes . '-' . $dia)->addMonth();
            //                         } else {
            //                             $pago_matriculacion->fecha_vencimiento = $fecha_vencimiento->addMonth();
            //                         }
            //                         $pago_matriculacion->tipo = 'CU';
            //                         $fecha_vencimiento = $pago_matriculacion->fecha_vencimiento;
            //                     }
            //                     $pago_matriculacion->monto = $articulo->detalle->precio_cuota;
            //                     $pago_matriculacion->monto_convenio = 0;
            //                     $pago_matriculacion->monto_multa = 0;
            //                     $pago_matriculacion->saldo = $articulo->detalle->precio_cuota;
            //                 }
            //                 $pago_matriculacion->moneda_id = 1;
            //                 $pago_matriculacion->save();
            //             }
            //             //agregar lo de contabilidad, para que esto vaya a cobros_diferidos

            //             break;
            //     }
            // } else {
            //     return back()->with('error-message', 'Se debe cargar el artículo con sus precios en contabilidad antes de realizar la matriculación del alumno.');
            // }

            if ($matriculacion->carrera_siu_id) {
                $carrera_siu = Carrera::find($matriculacion->carrera_siu_id)->first();
                if (!$carrera_siu) {
                    return back()->with('error-message', 'Hubo un problema al realizar la matriculación. No se puede obtener la carrera SIU.');
                } else {
                    $alumno_puntajes = AlumnoPuntaje::where('alumno_id', $matriculacion->alumno_id)->where('carrera_id', $carrera_siu->id)->get();
                    $malla_auxiliar_siu = Malla::whereHas('carrera', function ($query) {
                        $query->where('nombre_real', 'LIKE', '%SIU AUXILIAR%');
                    })->first();
                    $malla_siu = Malla::where('carrera_id', $carrera_siu->id)->first();
                    $materias_nuevas = $malla_siu->mallaDetalles->pluck('materia_id', 'materia.nombre_real');

                    $alumno_puntajes = AlumnoPuntaje::where('alumno_id', $matriculacion->alumno_id)->get();
                    foreach ($alumno_puntajes as $alumno_puntaje) {
                        $nombre_real = $alumno_puntaje->materia->nombre_real;
                        if ($alumno_puntaje->carrera_id != $carrera_siu->id) {
                            if (isset($materias_nuevas[$nombre_real])) {
                                $alumno_puntaje->materia_id = $materias_nuevas[$nombre_real];
                                $alumno_puntaje->carrera_id = $carrera_siu->id;
                                $alumno_puntaje->save();
                            }
                        }
                    }

                    $alumno_notas = AlumnoNota::where('alumno_id', $matriculacion->alumno_id)->get();
                    foreach ($alumno_notas as $alumno_nota) {
                        $nombre_real = $alumno_nota->materia->nombre_real;
                        if ($alumno_nota->carrera_id != $carrera_siu->id) {
                            if (isset($materias_nuevas[$nombre_real])) {
                                $alumno_nota->materia_id = $materias_nuevas[$nombre_real];
                                $alumno_nota->carrera_id = $carrera_siu->id;
                                $alumno_nota->save();
                            }
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('matriculaciones.index')->with('success-message', 'La matriculación del alumno ' . $matriculacion->alumno->primer_nombre . ' ' . $matriculacion->alumno->primer_apellido . ' fue creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('matriculaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_matriculaciones');

        DB::beginTransaction();

        try {
            $matriculacion = Matriculacion::findOrFail($id);
            if ($matriculacion->semestre->estado == 'IN') {
                return back()->with('error-message', 'La matriculación no puede inactivarse. El semestre se encuentra cerrado.');
            }
            $matriculacion->actualizado_por_id = Auth::id();
            $matriculacion->estado = 'IN';
            $matriculacion->save();

            // ACTIVAR CUANDO SE HABILITE ADMINISTRACION

            // $pagos_matriculacion = PagoMatriculacion::where('matriculacion_id', $matriculacion->id)->get();
            // foreach ($pagos_matriculacion as $pago_matriculacion) {
            //     if ($pago_matriculacion->estado == 'PE') {
            //         $pago_matriculacion->actualizado_por_id = Auth::id();
            //         $pago_matriculacion->estado = 'AN';
            //         $pago_matriculacion->save();
            //     }
            // }

            // HASTA ACA

            $inscripciones = Inscripcion::where('matriculacion_id', $matriculacion->id)->get();
            foreach ($inscripciones as $inscripcion) {
                if ($inscripcion->estado != 'AP') {
                    $inscripcion->actualizado_por_id = Auth::id();
                    $inscripcion->estado = 'DE';
                    $inscripcion->save();
                }
            }

            DB::commit();

            return redirect()->route('matriculaciones.index')->with('error-message', 'La matriculación del alumno ' . $matriculacion->alumno->primer_nombre . ' ' . $matriculacion->alumno->primer_apellido . ' fue inactivada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('matriculaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_matriculaciones');

        DB::beginTransaction();

        try {
            $matriculacion = Matriculacion::findOrFail($id);
            if ($matriculacion->semestre->estado == 'IN') {
                return back()->with('error-message', 'La matriculación no puede activarse. El semestre se encuentra cerrado.');
            }
            $matriculacion->actualizado_por_id = Auth::id();
            $matriculacion->estado = 'AC';
            $matriculacion->save();

            // ACTIVAR CUANDO SE HABILITE ADMINISTRACION

            // $pagos_matriculacion = PagoMatriculacion::where('matriculacion_id', $matriculacion->id)->get();
            // foreach ($pagos_matriculacion as $pago_matriculacion) {
            //     if ($pago_matriculacion->estado == 'AN') {
            //         $pago_matriculacion->actualizado_por_id = Auth::id();
            //         $pago_matriculacion->estado = 'PE';
            //         $pago_matriculacion->save();
            //     }
            // }

            // HASTA ACA

            $inscripciones = Inscripcion::where('matriculacion_id', $matriculacion->id)->get();
            foreach ($inscripciones as $inscripcion) {
                if ($inscripcion->estado == 'DE') {
                    $inscripcion->actualizado_por_id = Auth::id();
                    $inscripcion->estado = 'MA';
                    $inscripcion->save();
                }
            }

            DB::commit();

            return redirect()->route('matriculaciones.index')->with('success-message', 'La matriculación del alumno ' . $matriculacion->alumno->primer_nombre . ' ' . $matriculacion->alumno->primer_apellido . ' fue activada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('matriculaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_matriculaciones');

        DB::beginTransaction();

        try {
            $matriculacion = Matriculacion::findOrFail($id);
            if ($matriculacion->semestre->estado == 'IN') {
                return back()->with('error-message', 'La matriculación no puede eliminarse. El semestre se encuentra cerrado.');
            }

            // $pagos_matriculacion = PagoMatriculacion::where('matriculacion_id', $matriculacion->id)->get();
            // if ($pagos_matriculacion->sum('saldo') != 0) {
                // if ($pagos_matriculacion->sum('monto') != $pagos_matriculacion->sum('saldo')) {
                    // return redirect()->route('matriculaciones.index')->with('error-message', 'La matriculación del alumno ' . $matriculacion->alumno->primer_nombre . ' ' . $matriculacion->alumno->primer_apellido . ' no se puede eliminar. Ya cuenta con cuotas pagas y tiene saldo pendiente.');
                // } else {
                    // $pagos_matriculacion = PagoMatriculacion::where('matriculacion_id', $matriculacion->id)->delete();
                $inscripciones = Inscripcion::where('matriculacion_id', $matriculacion->id)->get();
                if ($inscripciones->count() != 0) {
                    foreach ($inscripciones as $inscripcion) {
                        $inscripcion->delete();
                    }
                }
                $matriculacion->delete();

                DB::commit();

                return redirect()->route('matriculaciones.index')->with('success-message', 'La matriculación del alumno ' . $matriculacion->alumno->primer_nombre . ' ' . $matriculacion->alumno->primer_apellido . ' fue eliminada exitosamente.');
                // }
            // } else {
            //     return redirect()->route('matriculaciones.index')->with('error-message', 'La matriculación del alumno ' . $matriculacion->alumno->primer_nombre . ' ' . $matriculacion->alumno->primer_apellido . ' no se puede eliminar. El alumno ya pagó todas sus cuotas.');
            // }

            // ACTIVAR CUANDO SE HABILITE ADMINISTRACION
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('matriculaciones.index')->with('error-message', 'La matriculación del alumno ' . $matriculacion->alumno->primer_nombre . ' ' . $matriculacion->alumno->primer_apellido . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('matriculaciones.index')->with('error-message', $e->getMessage());
            }
        }
    }

    public function get_carrera($alumno)
    {
        $this->authorize('crear_matriculaciones');

        DB::beginTransaction();

        try {
            $matriculaciones = Matriculacion::where('alumno_id', $alumno)->get();
            if ($matriculaciones->count() != 0) {
                $programas = collect();
                $carreras = collect();
                $carreras_siu = collect();

                foreach ($matriculaciones as $matriculacion) {
                    $programas->push($matriculacion->programa);
                    $carreras->push($matriculacion->carrera);
                    $carreras_siu->push($matriculacion->carreraSiu);
                }

                $programas = $programas->unique('id');
                $carreras = $carreras->unique('id');
                $carreras_siu = $carreras_siu->unique('id');

                return response()->json([
                    'programas' => $programas,
                    'carreras' => $carreras,
                    'carreras_siu' => $carreras_siu,
                ]);
            } else {
                return response()->json([
                    'validacion' => 0,
                ]);
            }
        } catch (\Exception $e) {
            return redirect()->route('matriculaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function validate_carrera_siu($alumno, $carrera, $programa)
    {
        $this->authorize('crear_matriculaciones');

        DB::beginTransaction();

        try {
            $validacion = 0;
            if ($programa == 1) {
                $matriculaciones = Matriculacion::where('alumno_id', $alumno)->where('carrera_id', $carrera)->count();
                $carrera_siu = Matriculacion::where('alumno_id', $alumno)->where('carrera_id', $carrera)->orderBy('id', 'desc')->first()->carrera_siu_id;
                if ($matriculaciones > 3) {
                    $validacion = 1;
                }
            }

            return response()->json([
                'carrera_siu' => $carrera_siu,
                'validacion' => $validacion,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('matriculaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function pdf_contrato($id)
    {
        $this->authorize('imprimir_contratos_matriculaciones');

        try {
            $empresa = Empresa::first();
            $fecha_hoy = Carbon::now();
            $matriculacion = Matriculacion::findOrFail($id);

            switch ($matriculacion->alumno->sexo->nombre) {
                case 'FEMENINO':
                    $estimado = 'Estimada';
                    break;

                default:
                $estimado = 'Estimado';
                    break;
            }

            $nombre_alumno = $matriculacion->alumno->primer_nombre;
            if ($matriculacion->alumno->segundo_nombre) {
                $nombre_alumno = $nombre_alumno . ' ' . $matriculacion->alumno->segundo_nombre;
            }
            if ($matriculacion->alumno->tercer_nombre) {
                $nombre_alumno = $nombre_alumno . ' ' . $matriculacion->alumno->tercer_nombre;
            }
            $nombre_alumno = $nombre_alumno . ' ' . $matriculacion->alumno->primer_apellido;
            if ($matriculacion->alumno->segundo_apellido) {
                $nombre_alumno = $nombre_alumno . ' ' . $matriculacion->alumno->segundo_apellido;
            }
            $nombre_alumno = Str::title($nombre_alumno);

            $malla = Malla::where('carrera_id', $matriculacion->carrera_id)->first();
            $total_creditos = MallaDetalle::where('malla_id', $malla->id)->sum('cantidad_creditos');

            $cantidad_semestres = Carrera::findOrFail($matriculacion->carrera_id)->cantidad_semestres;
            $duracion_carrera = $cantidad_semestres / 2;
            if ($duracion_carrera != 1) {
                $duracion_carrera = $duracion_carrera . ' años';
            } else {
                $duracion_carrera = $duracion_carrera . ' año';
            }

            $guion = Str::contains($matriculacion->semestre->nombre, '-');
		    $guion_espacio = Str::contains($matriculacion->semestre->nombre, ' - ');
		    $punto = Str::contains($matriculacion->semestre->nombre, '.');

            if ($guion) {
                list($anho, $semestre) = explode('-', $matriculacion->semestre->nombre);
            } else if ($guion_espacio) {
                list($anho, $semestre) = explode('-', $matriculacion->semestre->nombre);
            } else if ($punto) {
                list($anho, $semestre) = explode('.', $matriculacion->semestre->nombre);
            }

            $anho = (int)$anho;
            $semestre = (int)$semestre;

            for ($i=0; $i < $cantidad_semestres; $i++) {
                if ($semestre == 2) {
                    $semestre = 1;
                    $anho++;
                } else {
                    $semestre = 2;
                }
            }
            $periodo_egreso = $anho . '-' . str_pad($semestre, 2, '0', STR_PAD_LEFT);

            $matriculacion->licenciatura = false;
            if ($matriculacion->programa_id < 5) {
                $matriculacion->licenciatura = true;
            }

            $precios = []; // ELIMINAR CUANDO SE HABILITE ADMINISTRACION

            // ACTIVAR PARA CUANDO SE HABILITE ADMINISTRACION

            // $carrera = Carrera::find($matriculacion->carrera_id);
            // if ($carrera) {
            //     $articulo = Articulo::where('precio_matricula', '>', 0)
            //                         ->where('precio_contado', '>', 0)
            //                         ->where('precio_cuota', '>', 0)
            //                         ->where('dia_vencimiento_cuotas', '>', 0)
            //                         ->where('compra_venta', 'VENTA')
            //                         ->find($carrera->articulo_id);
            // } else {
            //     return back()->with('error-message', 'Hubo un problema al realizar la matriculación. Si el problema persiste, contacte con el administador.');
            // }

            // if ($articulo) {
            //     $precios = ['precio_matricula' => 'Gs. ' . number_format($articulo->detalle->precio_matricula, 0, ',', '.'),
            //                'numero_matriculas' => 2,
            //                'precio_cuotas' => 'Gs. ' . number_format($articulo->detalle->precio_cuota, 0, ',', '.'),
            //                'numero_cuotas' => 'Gs. ' . $articulo->detalle->cantidad_cuotas];
            // } else {
            //     return back()->with('error-message', 'Se debe cargar el artículo con sus precios en contabilidad antes de realizar la matriculación del alumno.');
            // }


            // $semestre_malla = SemestreMalla::where('semestre_id', $matriculacion->semestre_id)->where('malla_id', $malla->id)->first();
            // $precios = ['precio_matricula' => 'Gs. ' . number_format($semestre_malla->precio_matricula, 0, ',', '.'),
            //             'numero_matriculas' => 2,
            //             'precio_cuotas' => 'Gs. ' . number_format($semestre_malla->precio_cuota, 0, ',', '.'),
            //             'numero_cuotas' => $semestre_malla->cantidad_cuotas];

            // HASTA ACA

            $pdf = Pdf::loadView('matriculaciones/pdf_contrato', compact('empresa', 'fecha_hoy', 'matriculacion', 'estimado', 'nombre_alumno', 'total_creditos', 'duracion_carrera', 'periodo_egreso', 'precios'));
            $pdf->setPaper('A4');

            return $pdf->stream('contrato_' . $matriculacion->alumno->numero_documento . '.pdf');

        } catch (\Exception $e) {
            return redirect()->route('matriculaciones.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function show_horarios()
    {
        $this->authorize('ver_horarios_semestres_matriculaciones');

        try {
            $hoy = Carbon::today();
            $periodo_activo = Semestre::whereDate('fecha_inicio', '<=', $hoy)
                            ->whereDate('fecha_fin', '>=', $hoy)
                            ->where('estado', 'AC')
                            ->orderBy('id', 'asc')
                            ->first();
            if (!$periodo_activo) {
                return back()->with('error-message', 'No existe un semestre activo para ver el horario general.');
            }

            $semestre_mallas = SemestreMalla::with('semestreMallaMaterias.semestreMallaMateriaHorarios')->where('semestre_id', $periodo_activo->id)->get();

            $dias_semana = DiaSemana::get();

            $horarios = collect();
            $horarios_clases = collect();

            $horarios_clases->push(Carbon::createFromTime(8, 0),
                                        Carbon::createFromTime(9, 0),
                                        Carbon::createFromTime(10, 0),
                                        Carbon::createFromTime(11, 0),
                                        Carbon::createFromTime(12, 0),
                                        Carbon::createFromTime(13, 0),
                                        Carbon::createFromTime(14, 0),
                                        Carbon::createFromTime(18, 0),
                                        Carbon::createFromTime(20, 0),
                                        Carbon::createFromTime(22, 0)
                                        );

            foreach ($semestre_mallas as $sm) {
                foreach ($sm->semestreMallaMaterias as $smm) {
                    foreach ($smm->semestreMallaMateriaHorarios as $smmh) {
                        if (!$smmh->diaSemana) {
                            continue;
                        }
                        $datos_horarios = ['materia_id' => $smm->materia_id,
                                           'materia' => $smm->materia->nombre_fantasia,
                                           'dia' => $smmh->diaSemana->nombre,
                                           'hora_inicio' => $smmh->hora_inicio,
                                           'hora_fin' => $smmh->hora_fin];

                        $horarios->push($datos_horarios);
                    }
                }
            }

            $horarios = $horarios->sortBy(function($horario) {
                $dias_orden = [
                    'LUNES' => 1,
                    'MARTES' => 2,
                    'MIERCOLES' => 3,
                    'JUEVES' => 4,
                    'VIERNES' => 5,
                    'SABADO' => 6,
                    'DOMINGO' => 7,
                ];
                return $dias_orden[$horario['dia']];
            });
            $horarios = $horarios->values();

            if ($horarios->count() != 0) {
                return view('matriculaciones/show_horarios')->with(compact('periodo_activo', 'dias_semana', 'horarios_clases', 'horarios'));
            } else {
                return redirect()->route('matriculaciones.index')->with('error-message', 'No existen horarios cargados para mostrar.');
            }

        } catch (\Exception $e) {
            return redirect()->route('matriculaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function pdf_horarios()
    {
        $this->authorize('imprimir_horarios_semestres_matriculaciones');

        try {
            $hoy = Carbon::today();
            $periodo_activo = Semestre::whereDate('fecha_inicio', '<=', $hoy)
                            ->whereDate('fecha_fin', '>=', $hoy)
                            ->where('estado', 'AC')
                            ->orderBy('id', 'asc')
                            ->first();
            if (!$periodo_activo) {
                return back()->with('error-message', 'No existe un semestre activo para ver el horario general.');
            }

            $empresa = Empresa::first();
            $fecha_hoy = Carbon::now();
            $semestre_mallas = SemestreMalla::with('semestreMallaMaterias.semestreMallaMateriaHorarios')->where('semestre_id', $periodo_activo->id)->get();
            $dias_semana = DiaSemana::get();

            $horarios = collect();
            $horarios_clases = collect();

            $horarios_clases->push(Carbon::createFromTime(8, 0),
                                        Carbon::createFromTime(9, 0),
                                        Carbon::createFromTime(10, 0),
                                        Carbon::createFromTime(11, 0),
                                        Carbon::createFromTime(12, 0),
                                        Carbon::createFromTime(13, 0),
                                        Carbon::createFromTime(14, 0),
                                        Carbon::createFromTime(18, 0),
                                        Carbon::createFromTime(20, 0),
                                        Carbon::createFromTime(22, 0)
                                        );

            foreach ($semestre_mallas as $sm) {
                foreach ($sm->semestreMallaMaterias as $smm) {
                    foreach ($smm->semestreMallaMateriaHorarios as $smmh) {
                        if (!$smmh->diaSemana) {
                            continue;
                        }
                        $datos_horarios = ['materia_id' => $smm->materia_id,
                                           'materia' => $smm->materia->nombre_fantasia,
                                           'dia' => $smmh->diaSemana->nombre,
                                           'hora_inicio' => $smmh->hora_inicio,
                                           'hora_fin' => $smmh->hora_fin];

                        $horarios->push($datos_horarios);
                    }
                }
            }

            $horarios = $horarios->sortBy(function($horario) {
                $dias_orden = [
                    'LUNES' => 1,
                    'MARTES' => 2,
                    'MIERCOLES' => 3,
                    'JUEVES' => 4,
                    'VIERNES' => 5,
                    'SABADO' => 6,
                    'DOMINGO' => 7,
                ];
                return $dias_orden[$horario['dia']];
            });
            $horarios = $horarios->values();

            $pdf = Pdf::loadView('matriculaciones/pdf_horarios', compact('empresa', 'fecha_hoy', 'periodo_activo', 'dias_semana', 'horarios_clases', 'horarios'));
            $pdf->setPaper('A4', 'landscape');

            return $pdf->stream('horario_' . $periodo_activo->nombre . '.pdf');
        } catch (\Exception $e) {
            return redirect()->route('matriculaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function agregar_convenio(Request $request, $id)
    {
        $this->authorize('agregar_matriculaciones_convenios');

        $request->validate([
            'convenio' => ['required', 'numeric'],
        ]);

        DB::beginTransaction();

        try {
            //ELIMINAR CUANDO SE HABILITE ADMINISTRACION
            return redirect()->back()->with('error-message', 'Esta funcionalidad aún no está disponible. Por favor, contacte al administrador del sistema para más información.');

            $matriculacion = Matriculacion::findOrFail($id);
            $convenio = Convenio::with('detalle')->findOrFail($request->convenio);
            $pagos_matriculacion = PagoMatriculacion::where('matriculacion_id', $matriculacion->id)->orderBy('id', 'asc')->get();

            foreach ($pagos_matriculacion as $pago_matriculacion) {
                $monto = 0;

                if ($pago_matriculacion->tipo == 'MA' && $convenio->detalle->tipo_matricula) {
                    if (($pago_matriculacion->saldo != $pago_matriculacion->monto) && $pago_matriculacion != 'PE') {
                        return response()->json(['error' => 'No se puede aplicar el convenio. La matrícula ya cuenta con pagos realizados.']);
                    }

                    switch ($convenio->detalle->tipo_matricula) {
                        case 'VA':
                            $monto = $convenio->detalle->descuento_matricula;
                            break;
                        case 'PO':
                            $monto = ($convenio->detalle->porcentaje_matricula / 100) * $pago_matriculacion->monto;
                            break;
                    }
                }

                if ($pago_matriculacion->tipo == 'SC' && $convenio->detalle->tipo_contado) {
                    if (($pago_matriculacion->saldo != $pago_matriculacion->monto) && $pago_matriculacion != 'PE') {
                        return response()->json(['error' => 'No se puede aplicar el convenio. El semestre completo ya cuenta con pagos realizados.']);
                    }

                    switch ($convenio->detalle->tipo_contado) {
                        case 'VA':
                            $monto = $convenio->detalle->descuento_contado;
                            break;
                        case 'PO':
                            $monto = ($convenio->detalle->porcentaje_contado / 100) * $pago_matriculacion->monto;
                            break;
                    }
                }

                if (($pago_matriculacion->tipo == '1C' || $pago_matriculacion->tipo == 'CU') && $convenio->detalle->tipo_cuotas) {
                    if (($pago_matriculacion->saldo != $pago_matriculacion->monto) && $pago_matriculacion != 'PE') {
                        return response()->json(['error' => 'No se puede aplicar el convenio. La cuota ya cuenta con pagos realizados.']);
                    }

                    switch ($convenio->detalle->tipo_cuotas) {
                        case 'VA':
                            if ($convenio->detalle->aplica_a_cuotas == 'TO') {
                                $monto = $convenio->detalle->descuento_cuotas;
                            } elseif ($convenio->detalle->aplica_a_cuotas == '1C' && $pago_matriculacion->tipo == '1C') {
                                $monto = $convenio->detalle->descuento_cuota_1;
                            } elseif ($convenio->detalle->aplica_a_cuotas == 'CO') {
                                if ((str_contains($pago_matriculacion->descripcion, 'CUOTA 1')) && $convenio->detalle->descuento_cuota_1) {
                                    $monto = $convenio->detalle->descuento_cuota_1;
                                } elseif ((str_contains($pago_matriculacion->descripcion, 'CUOTA 2')) && $convenio->detalle->descuento_cuota_2) {
                                    $monto = $convenio->detalle->descuento_cuota_2;
                                } elseif ((str_contains($pago_matriculacion->descripcion, 'CUOTA 3')) && $convenio->detalle->descuento_cuota_3) {
                                    $monto = $convenio->detalle->descuento_cuota_3;
                                } elseif ((str_contains($pago_matriculacion->descripcion, 'CUOTA 4')) && $convenio->detalle->descuento_cuota_4) {
                                    $monto = $convenio->detalle->descuento_cuota_4;
                                } elseif ((str_contains($pago_matriculacion->descripcion, 'CUOTA 5')) && $convenio->detalle->descuento_cuota_5) {
                                    $monto = $convenio->detalle->descuento_cuota_5;
                                }
                            }
                            break;
                        case 'PO':
                            if ($convenio->detalle->aplica_a_cuotas == 'TO') {
                                $monto = ($convenio->detalle->porcentaje_cuotas / 100) * $pago_matriculacion->monto;
                            } elseif ($convenio->detalle->aplica_a_cuotas == '1C' && $pago_matriculacion->tipo == '1C') {
                                $monto = ($convenio->detalle->porcentaje_cuota_1 / 100) * $pago_matriculacion->monto;
                            } elseif ($convenio->detalle->aplica_a_cuotas == 'CO') {
                                if ((str_contains($pago_matriculacion->descripcion, 'CUOTA 1')) && $convenio->detalle->descuento_cuota_1) {
                                    $monto = ($convenio->detalle->porcentaje_cuota_1 / 100) * $pago_matriculacion->monto;
                                } elseif ((str_contains($pago_matriculacion->descripcion, 'CUOTA 2')) && $convenio->detalle->descuento_cuota_2) {
                                    $monto = ($convenio->detalle->porcentaje_cuota_2 / 100) * $pago_matriculacion->monto;
                                } elseif ((str_contains($pago_matriculacion->descripcion, 'CUOTA 3')) && $convenio->detalle->descuento_cuota_3) {
                                    $monto = ($convenio->detalle->porcentaje_cuota_3 / 100) * $pago_matriculacion->monto;
                                } elseif ((str_contains($pago_matriculacion->descripcion, 'CUOTA 4')) && $convenio->detalle->descuento_cuota_4) {
                                    $monto = ($convenio->detalle->porcentaje_cuota_4 / 100) * $pago_matriculacion->monto;
                                } elseif ((str_contains($pago_matriculacion->descripcion, 'CUOTA 5')) && $convenio->detalle->descuento_cuota_5) {
                                    $monto = ($convenio->detalle->porcentaje_cuota_5 / 100) * $pago_matriculacion->monto;
                                }
                            }
                            break;
                    }
                }


                if ($monto != 0) {
                    $pago_matriculacion->monto_convenio = $monto;
                    $pago_matriculacion->saldo = $pago_matriculacion->saldo - $monto;
                    if ($pago_matriculacion->saldo == 0) {
                        $pago_matriculacion->estado = 'CO';
                    } else {
                        $pago_matriculacion->estado = 'CP';
                    }

                    $pago_matriculacion->save();
                }
            }

            $matriculacion->convenio_id = $convenio->id;
            $matriculacion->save();

            DB::commit();

            return response()->json([
                'message' => 'El convenio fue aplicado a la matriculación exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('matriculaciones.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function eliminar_convenio($id)
    {
        $this->authorize('eliminar_matriculaciones_convenios');

        DB::beginTransaction();

        try {
            //ELIMINAR CUANDO SE HABILITE ADMINISTRACION
            return redirect()->back()->with('error-message', 'Esta funcionalidad aún no está disponible. Por favor, contacte al administrador del sistema para más información.');

            $matriculacion = Matriculacion::findOrFail($id);
            $convenio = Convenio::with('detalle')->findOrFail($matriculacion->convenio_id);
            $pagos_matriculacion = PagoMatriculacion::where('matriculacion_id', $matriculacion->id)->orderBy('id', 'asc')->get();

            foreach ($pagos_matriculacion as $pago_matriculacion) {
                $monto = 0;

                if ($pago_matriculacion->tipo == 'MA' && $convenio->detalle->tipo_matricula) {

                    switch ($convenio->detalle->tipo_matricula) {
                        case 'VA':
                            $monto = $convenio->detalle->descuento_matricula;
                            break;
                        case 'PO':
                            $monto = ($convenio->detalle->porcentaje_matricula / 100) * $pago_matriculacion->monto;
                            break;
                    }

                    if (($pago_matriculacion->saldo + $monto) != $pago_matriculacion->monto) {
                        return back()->with('error-message', 'No se puede desvincular el convenio. La matrícula ya cuenta con pagos realizados.');
                    }

                }

                if ($pago_matriculacion->tipo == 'SC' && $convenio->detalle->tipo_contado) {
                    switch ($convenio->detalle->tipo_contado) {
                        case 'VA':
                            $monto = $convenio->detalle->descuento_contado;
                            break;
                        case 'PO':
                            $monto = ($convenio->detalle->porcentaje_contado / 100) * $pago_matriculacion->monto;
                            break;
                    }

                    if (($pago_matriculacion->saldo + $monto) != $pago_matriculacion->monto) {
                        return back()->with('error-message', 'No se puede desvincular el convenio. El semestre completo ya cuenta con pagos realizados.');
                    }
                }

                if (($pago_matriculacion->tipo == '1C' || $pago_matriculacion->tipo == 'CU') && $convenio->detalle->tipo_cuotas) {
                    switch ($convenio->detalle->tipo_cuotas) {
                        case 'VA':
                            if ($convenio->detalle->aplica_a_cuotas == 'TO') {
                                $monto = $convenio->detalle->descuento_cuotas;
                            } elseif ($convenio->detalle->aplica_a_cuotas == '1C' && $pago_matriculacion->tipo == '1C') {
                                $monto = $convenio->detalle->descuento_cuota_1;
                            } elseif ($convenio->detalle->aplica_a_cuotas == 'CO') {
                                if ((str_contains($pago_matriculacion->descripcion, 'CUOTA 1')) && $convenio->detalle->descuento_cuota_1) {
                                    $monto = $convenio->detalle->descuento_cuota_1;
                                } elseif ((str_contains($pago_matriculacion->descripcion, 'CUOTA 2')) && $convenio->detalle->descuento_cuota_2) {
                                    $monto = $convenio->detalle->descuento_cuota_2;
                                } elseif ((str_contains($pago_matriculacion->descripcion, 'CUOTA 3')) && $convenio->detalle->descuento_cuota_3) {
                                    $monto = $convenio->detalle->descuento_cuota_3;
                                } elseif ((str_contains($pago_matriculacion->descripcion, 'CUOTA 4')) && $convenio->detalle->descuento_cuota_4) {
                                    $monto = $convenio->detalle->descuento_cuota_4;
                                } elseif ((str_contains($pago_matriculacion->descripcion, 'CUOTA 5')) && $convenio->detalle->descuento_cuota_5) {
                                    $monto = $convenio->detalle->descuento_cuota_5;
                                }
                            }
                            break;
                        case 'PO':
                            if ($convenio->detalle->aplica_a_cuotas == 'TO') {
                                $monto = ($convenio->detalle->porcentaje_cuotas / 100) * $pago_matriculacion->monto;
                            } elseif ($convenio->detalle->aplica_a_cuotas == '1C' && $pago_matriculacion->tipo == '1C') {
                                $monto = ($convenio->detalle->porcentaje_cuota_1 / 100) * $pago_matriculacion->monto;
                            } elseif ($convenio->detalle->aplica_a_cuotas == 'CO') {
                                if ((str_contains($pago_matriculacion->descripcion, 'CUOTA 1')) && $convenio->detalle->descuento_cuota_1) {
                                    $monto = ($convenio->detalle->porcentaje_cuota_1 / 100) * $pago_matriculacion->monto;
                                } elseif ((str_contains($pago_matriculacion->descripcion, 'CUOTA 2')) && $convenio->detalle->descuento_cuota_2) {
                                    $monto = ($convenio->detalle->porcentaje_cuota_2 / 100) * $pago_matriculacion->monto;
                                } elseif ((str_contains($pago_matriculacion->descripcion, 'CUOTA 3')) && $convenio->detalle->descuento_cuota_3) {
                                    $monto = ($convenio->detalle->porcentaje_cuota_3 / 100) * $pago_matriculacion->monto;
                                } elseif ((str_contains($pago_matriculacion->descripcion, 'CUOTA 4')) && $convenio->detalle->descuento_cuota_4) {
                                    $monto = ($convenio->detalle->porcentaje_cuota_4 / 100) * $pago_matriculacion->monto;
                                } elseif ((str_contains($pago_matriculacion->descripcion, 'CUOTA 5')) && $convenio->detalle->descuento_cuota_5) {
                                    $monto = ($convenio->detalle->porcentaje_cuota_5 / 100) * $pago_matriculacion->monto;
                                }
                            }
                            break;
                    }

                    if (($pago_matriculacion->saldo + $monto) != $pago_matriculacion->monto) {
                        return back()->with('error-message', 'No se puede desvincular el convenio. La cuota ya cuenta con pagos realizados.');
                    }
                }


                if ($monto != 0) {
                    $pago_matriculacion->monto_convenio = 0;
                    $pago_matriculacion->saldo = $pago_matriculacion->saldo + $monto;
                    if ($pago_matriculacion->saldo == $pago_matriculacion->monto) {
                        $pago_matriculacion->estado = 'PE';
                    }

                    $pago_matriculacion->save();
                }
            }

            $matriculacion->convenio_id = null;
            $matriculacion->save();

            DB::commit();

            return redirect()->route('matriculaciones.show', $id)->with('error-message', 'El convenio fue desvinculado de la matriculación exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('matriculaciones.show', $id)->with('error-message', $e->getMessage());
        }
    }
}
