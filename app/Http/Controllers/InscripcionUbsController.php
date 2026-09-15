<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

use App\Models\InscripcionUbs;
use App\Models\PagoInscripcionUbs;
use App\Models\Alumno;
use App\Models\Curso;
use App\Models\CursoPrecio;
use App\Models\InscripcionModulo;
use App\Models\Empresa;
use App\Models\Semestre;
use App\Models\Convenio;

class InscripcionUbsController extends Controller
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
        $this->authorize('ver_inscripciones_ubs');

        try {
            $buscar = Str::upper($request->buscar);

            $inscripciones = InscripcionUbs::orderBy('id', 'desc')->paginate(50);

            if (!(blank($buscar))) {
				$inscripciones = InscripcionUbs::orWhere('id', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%')
                ->orWhere('fecha', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%')
                ->orWhereHas('alumno', function ($query) use ($buscar) {
                    $query->where('primer_nombre', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%')
                          ->orWhere('primer_apellido', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%')
                          ->orWhere('segundo_apellido', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%')
                          ->orWhere('numero_documento', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%');
                })
                ->orWhereHas('curso', function ($query) use ($buscar) {
                    $query->where('nombre_fantasia', 'LIKE', '%' . str_replace(' ', '%', $buscar) . '%');
                })
				->paginate(50);
			}


            return view('ubs/inscripciones/index')->with(compact('inscripciones', 'buscar'));
        } catch (\Exception $e) {
            return redirect()->route('inscripciones_ubs.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_inscripciones_ubs');

        try {
            $inscripcion = InscripcionUbs::with(['pagosInscripciones' => function ($query) {
                $query->orderBy('id');
            }])->findOrFail($id);
            $convenios = Convenio::where('tipo', 'CO')->where('estado', 'AC')->get();
            return view('ubs/inscripciones/show')->with(compact('inscripcion', 'convenios'));
        } catch (\Exception $e) {
            return redirect()->route('inscripciones_ubs.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_inscripciones_ubs');

        try {
            $alumnos = Alumno::where('ubs', true)->where('estado', 'AC')->get();
            $cursos = Curso::where('estado', 'AC')->get();

            return view('ubs/inscripciones/create')->with(compact('alumnos', 'cursos'));
        } catch (\Exception $e) {
            return redirect()->route('inscripciones_ubs.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_inscripciones_ubs');

        $request->validate([
            'alumno' => ['required', 'numeric'],
            'curso' => ['required', 'numeric', Rule::unique('inscripciones_ubs', 'curso_id')
                ->where(fn ($query) => $query->where('curso_id', $request->curso)
                ->where('alumno_id', $request->alumno)
                ->where('estado', 'AC'))],
            //El de arriba verifica que el conjunto de alumno y curso no existan,
            'tipo_pago' => 'required'
        ]);

        DB::beginTransaction();

        try {
            $inscripcion = new InscripcionUbs();
            $inscripcion->fecha = Carbon::now();
            $inscripcion->alumno_id = $request->alumno;
            $inscripcion->curso_id = $request->curso;
            $inscripcion->tipo_pago = $request->tipo_pago;

            //obtenemos el ultimo numero de inscripcion para luego utilizar en el certificado
            $ultima_inscripcion = InscripcionUbs::where('curso_id', $request->curso)->orderBy('id', 'desc')->first();

            if ($ultima_inscripcion) {
                $inscripcion->numero_inscripcion = $ultima_inscripcion->numero_inscripcion + 1;
            } else {
                $inscripcion->numero_inscripcion = 1;
            }
            $inscripcion->cargado_por_id = Auth::id();
            $inscripcion->save();

            if ($inscripcion->curso->tipo_curso_id == 3) {
                $curso = Curso::findOrFail($inscripcion->curso_id);
                $curso_modulo = $curso->modulos()->orderBy('id', 'asc')->first();

                if ($curso_modulo) {
                    $inscripcion_modulo = new InscripcionModulo();
                    $inscripcion_modulo->alumno_id = $inscripcion->alumno_id;
                    $inscripcion_modulo->curso_id = $inscripcion->curso_id;
                    $inscripcion_modulo->modulo_id = $curso_modulo->modulo_id;
                    $inscripcion_modulo->save();
                } else {
                    return redirect()->route('inscripciones_ubs.index')->with('error_message', 'La inscripción no se puede realizar. El curso seleccionado no cuenta con módulos asignados.');
                }
            }

            // ACTIVAR PARA CUANDO SE HABILITE ADMINISTRACION

            //caso contado
            if ($inscripcion->tipo_pago == 'CO') {
                $cantidad_cuotas = 1;
                for ($i=1; $i <= $cantidad_cuotas; $i++) {
                    $pago_inscripcion = new PagoInscripcionUbs();
                    $pago_inscripcion->inscripcion_id = $inscripcion->id;
                    $pago_inscripcion->alumno_id = $inscripcion->alumno_id;
                    $pago_inscripcion->descripcion = ' PAGO COMPLETO - ' . $inscripcion->curso->nombre_fantasia;
                    $pago_inscripcion->fecha_vencimiento = Carbon::now()->toDateString();
                    $pago_inscripcion->tipo = 'CO';
                    $pago_inscripcion->monto = $inscripcion->curso->precios->precio_contado;
                    $pago_inscripcion->saldo = $inscripcion->curso->precios->precio_contado;
                    $pago_inscripcion->moneda_id = 1;
                    $pago_inscripcion->save();
                }
            //caso cuota
            } else if ($inscripcion->tipo_pago == 'CR') {
                $cantidad_cuotas = $inscripcion->curso->precios->cantidad_cuotas;
                $fecha = Carbon::createFromDate($inscripcion->curso->precios->fecha_inicio_vencimiento_cuota);
                $dia = $inscripcion->curso->precios->dia_vencimiento_cuota;
                $mes = $fecha->format('m');
                $anho = $fecha->format('y');

                for ($i=1; $i <= $cantidad_cuotas ; $i++) {
                    $pago_inscripcion = new PagoInscripcionUbs();
                    $pago_inscripcion->inscripcion_id = $inscripcion->id;
                    $pago_inscripcion->alumno_id = $inscripcion->alumno_id;
                    $pago_inscripcion->descripcion = 'CUOTA ' . $i . ' DE ' . $cantidad_cuotas . ' - ' . $inscripcion->curso->nombre_fantasia;
                    if ($i == 1) {
                        $fecha_vencimiento = $inscripcion->curso->precios->fecha_inicio_vencimiento_cuota; //a la primera cuota le ponemos el primer vencimiento configurado en los parametros
                        $pago_inscripcion->fecha_vencimiento = $fecha_vencimiento;
                        $pago_inscripcion->tipo = '1C';
                    } else {
                        if ($i == 2) {
                            $pago_inscripcion->fecha_vencimiento = Carbon::parse($anho . '-' . $mes . '-' . $dia)->addMonth(); //aumentamos un mes a la fecha de vencimiento
                        } else {
                            $pago_inscripcion->fecha_vencimiento = $fecha_vencimiento->addMonth();
                        }
                        $pago_inscripcion->tipo = 'CU';
                        $fecha_vencimiento = $pago_inscripcion->fecha_vencimiento; //seteamos la nueva fecha de vencimiento a la fecha original mas un mes
                    }
                    $pago_inscripcion->monto = $inscripcion->curso->precios->precio_cuota;
                    $pago_inscripcion->monto_convenio = 0;
                    $pago_inscripcion->monto_multa = 0;
                    $pago_inscripcion->saldo = $inscripcion->curso->precios->precio_cuota;
                    $pago_inscripcion->moneda_id = 1;
                    $pago_inscripcion->save();
                }
            }

            // HASTA ACA

            DB::commit();

            return redirect()->route('inscripciones_ubs.index')->with('success-message', 'La inscripción del alumno ' . $inscripcion->alumno->primer_nombre . ' ' . $inscripcion->alumno->primer_apellido . ' fue creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('inscripciones_ubs.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_inscripciones_ubs');

        DB::beginTransaction();

        try {
            $inscripcion = InscripcionUbs::findOrFail($id);
            $inscripcion->actualizado_por_id = Auth::id();
            $inscripcion->estado = 'IN';
            $inscripcion->save();

            // ACTIVAR CUANDO SE HABILITE ADMINISTRACION

            $pagos_inscripcion = PagoInscripcionUbs::where('inscripcion_id', $inscripcion->id)->get();
            foreach ($pagos_inscripcion as $pago_inscripcion) {
                if ($pago_inscripcion->estado == 'PE') {
                    $pago_inscripcion->estado = 'AN';
                    $pago_inscripcion->save();
                }
            }

            // HASTA ACA

            DB::commit();

            return redirect()->route('inscripciones_ubs.index')->with('error-message', 'La inscripción del alumno ' . $inscripcion->alumno->primer_nombre . ' ' . $inscripcion->alumno->primer_apellido . ' fue inactivada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('inscripciones_ubs.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_inscripciones_ubs');

        DB::beginTransaction();

        try {
            $inscripcion = InscripcionUbs::findOrFail($id);
            $inscripcion->actualizado_por_id = Auth::id();
            $inscripcion->estado = 'AC';
            $inscripcion->save();

            // ACTIVAR CUANDO SE HABILITE ADMINISTRACION

            $pagos_inscripcion = PagoInscripcionUbs::where('inscripcion_id', $inscripcion->id)->get();
            foreach ($pagos_inscripcion as $pago_inscripcion) {
                if ($pago_inscripcion->estado == 'AN') {
                    $pago_inscripcion->actualizado_por_id = Auth::id();
                    $pago_inscripcion->estado = 'PE';
                    $pago_inscripcion->save();
                }
            }

            // HASTA ACA

            DB::commit();

            return redirect()->route('inscripciones_ubs.index')->with('success-message', 'La matriculación del alumno ' . $inscripcion->alumno->primer_nombre . ' ' . $inscripcion->alumno->primer_apellido . ' fue activada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('inscripciones_ubs.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_inscripciones_ubs');

        DB::beginTransaction();

        try {
            $inscripcion = InscripcionUbs::findOrFail($id);

            $pagos_inscripcion = PagoInscripcionUbs::where('inscripcion_id', $inscripcion->id)->get();
            if ($pagos_inscripcion->sum('saldo') != $pagos_inscripcion->sum('monto')) {
                return redirect()->route('inscripciones_ubs.index')->with('error-message', 'La inscripción del alumno ' . $inscripcion->alumno->primer_nombre . ' ' . $inscripcion->alumno->primer_apellido . ' no se puede eliminar. Ya cuenta con cuotas pagadas.');
            } else {
                $pagos_inscripcion = PagoInscripcionUbs::where('inscripcion_id', $inscripcion->id)->delete();
                $inscripcion->delete();

                DB::commit();

                return redirect()->route('inscripciones_ubs.index')->with('success-message', 'La inscripción del alumno ' . $inscripcion->alumno->primer_nombre . ' ' . $inscripcion->alumno->primer_apellido . ' fue eliminada exitosamente.');
            }

            // ACTIVAR CUANDO SE HABILITE ADMINISTRACION
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('inscripciones_ubs.index')->with('error-message', 'La inscripción del alumno ' . $inscripcion->alumno->primer_nombre . ' ' . $inscripcion->alumno->primer_apellido . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('inscripciones_ubs.index')->with('error-message', $e->getMessage());
            }
        }
    }

    public function pdf_contrato($id)
    {
         $this->authorize('imprimir_contratos_inscripciones_ubs');

        try {
            $empresa = Empresa::first();
            $fecha_hoy = Carbon::now();
            $inscripcion = InscripcionUbs::findOrFail($id);

            switch ($inscripcion->alumno->sexo->nombre) {
                case 'FEMENINO':
                    $estimado = 'Estimada';
                    break;

                default:
                $estimado = 'Estimado';
                    break;
            }

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
            $nombre_alumno = Str::title($nombre_alumno);

            $periodo_ingreso = Semestre::where('estado', 'AC')->orderBy('id', 'desc')->first()->nombre;

            list($anho, $semestre) = explode('-', $periodo_ingreso);
            $anho = (int)$anho;
            $semestre = (int)$semestre;

            $duracion_semestres = $inscripcion->curso->duracion * 2;

            for ($i=0; $i < $duracion_semestres; $i++) {
                if ($semestre == 2) {
                    $semestre = 1;
                    $anho++;
                } else {
                    $semestre = 2;
                }
            }
            $periodo_egreso = $anho . '-' . str_pad($semestre, 2, '0', STR_PAD_LEFT);

            // $precios = collect(); // ELIMINAR CUANDO SE HABILITE ADMINISTRACION

            $precios = CursoPrecio::where('curso_id', $inscripcion->curso_id)->first(); // ACTIVAR CUANDO SE HABILITE ADMINISTRACION

            $pdf = Pdf::loadView('ubs/inscripciones/pdf_contrato', compact('empresa', 'fecha_hoy', 'inscripcion', 'estimado', 'nombre_alumno', 'periodo_ingreso', 'periodo_egreso', 'precios'));
            $pdf->setPaper('A4');

            return $pdf->stream('contrato_' . $inscripcion->alumno->numero_documento . '.pdf');

        } catch (\Exception $e) {
            return redirect()->route('inscripciones_ubs.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function agregar_convenio(Request $request, $id)
    {
        $this->authorize('agregar_inscripciones_ubs_convenios');

        $request->validate([
            'convenio' => ['required', 'numeric'],
        ]);

        DB::beginTransaction();

        try {
            // ELIMINAR CUANDO SE HABILITE ADMINISTRACION
            // return redirect()->back()->with('error-message', 'Esta funcionalidad aún no está disponible. Por favor, contacte al administrador del sistema para más información.');

            $inscripcion = InscripcionUbs::findOrFail($id);
            $convenio = Convenio::with('detalle')->findOrFail($request->convenio);
            $pagos_inscripciones = PagoInscripcionUbs::where('inscripcion_id', $inscripcion->id)->orderBy('id', 'asc')->get();

            foreach ($pagos_inscripciones as $pago_inscripcion) {
                $monto = 0;

                if ($pago_inscripcion->tipo == 'CO' && $convenio->detalle->tipo_contado) {
                    if (($pago_inscripcion->saldo != $pago_inscripcion->monto) && $pago_inscripcion != 'PE') {
                        return response()->json(['error' => 'No se puede aplicar el convenio. El curso completo ya cuenta con pagos realizados.']);
                    }

                    switch ($convenio->detalle->tipo_contado) {
                        case 'VA':
                            $monto = $convenio->detalle->descuento_contado;
                            break;
                        case 'PO':
                            $monto = ($convenio->detalle->porcentaje_contado / 100) * $pago_inscripcion->monto;
                            break;
                    }
                }

                if (($pago_inscripcion->tipo == '1C' || $pago_inscripcion->tipo == 'CU') && $convenio->detalle->tipo_cuotas) {
                    if (($pago_inscripcion->saldo != $pago_inscripcion->monto) && $pago_inscripcion != 'PE') {
                        return response()->json(['error' => 'No se puede aplicar el convenio. La cuota ya cuenta con pagos realizados.']);
                    }

                    switch ($convenio->detalle->tipo_cuotas) {
                        case 'VA':
                            if ($convenio->detalle->aplica_a_cuotas == 'TO') {
                                $monto = $convenio->detalle->descuento_cuotas;
                            } elseif ($convenio->detalle->aplica_a_cuotas == '1C' && $pago_inscripcion->tipo == '1C') {
                                $monto = $convenio->detalle->descuento_cuota_1;
                            } elseif ($convenio->detalle->aplica_a_cuotas == 'CO') {
                                if ((str_contains($pago_inscripcion->descripcion, 'CUOTA 1')) && $convenio->detalle->descuento_cuota_1) {
                                    $monto = $convenio->detalle->descuento_cuota_1;
                                } elseif ((str_contains($pago_inscripcion->descripcion, 'CUOTA 2')) && $convenio->detalle->descuento_cuota_2) {
                                    $monto = $convenio->detalle->descuento_cuota_2;
                                } elseif ((str_contains($pago_inscripcion->descripcion, 'CUOTA 3')) && $convenio->detalle->descuento_cuota_3) {
                                    $monto = $convenio->detalle->descuento_cuota_3;
                                } elseif ((str_contains($pago_inscripcion->descripcion, 'CUOTA 4')) && $convenio->detalle->descuento_cuota_4) {
                                    $monto = $convenio->detalle->descuento_cuota_4;
                                } elseif ((str_contains($pago_inscripcion->descripcion, 'CUOTA 5')) && $convenio->detalle->descuento_cuota_5) {
                                    $monto = $convenio->detalle->descuento_cuota_5;
                                }
                            }
                            break;
                        case 'PO':
                            if ($convenio->detalle->aplica_a_cuotas == 'TO') {
                                $monto = ($convenio->detalle->porcentaje_cuotas / 100) * $pago_inscripcion->monto;
                            } elseif ($convenio->detalle->aplica_a_cuotas == '1C' && $pago_inscripcion->tipo == '1C') {
                                $monto = ($convenio->detalle->porcentaje_cuota_1 / 100) * $pago_inscripcion->monto;
                            } elseif ($convenio->detalle->aplica_a_cuotas == 'CO') {
                                if ((str_contains($pago_inscripcion->descripcion, 'CUOTA 1')) && $convenio->detalle->descuento_cuota_1) {
                                    $monto = ($convenio->detalle->porcentaje_cuota_1 / 100) * $pago_inscripcion->monto;
                                } elseif ((str_contains($pago_inscripcion->descripcion, 'CUOTA 2')) && $convenio->detalle->descuento_cuota_2) {
                                    $monto = ($convenio->detalle->porcentaje_cuota_2 / 100) * $pago_inscripcion->monto;
                                } elseif ((str_contains($pago_inscripcion->descripcion, 'CUOTA 3')) && $convenio->detalle->descuento_cuota_3) {
                                    $monto = ($convenio->detalle->porcentaje_cuota_3 / 100) * $pago_inscripcion->monto;
                                } elseif ((str_contains($pago_inscripcion->descripcion, 'CUOTA 4')) && $convenio->detalle->descuento_cuota_4) {
                                    $monto = ($convenio->detalle->porcentaje_cuota_4 / 100) * $pago_inscripcion->monto;
                                } elseif ((str_contains($pago_inscripcion->descripcion, 'CUOTA 5')) && $convenio->detalle->descuento_cuota_5) {
                                    $monto = ($convenio->detalle->porcentaje_cuota_5 / 100) * $pago_inscripcion->monto;
                                }
                            }
                            break;
                    }
                }


                if ($monto != 0) {
                    $pago_inscripcion->monto_convenio = $monto;
                    $pago_inscripcion->saldo = $pago_inscripcion->saldo - $monto;
                    if ($pago_inscripcion->saldo == 0) {
                        $pago_inscripcion->estado = 'CO';
                    } else {
                        $pago_inscripcion->estado = 'CP';
                    }

                    $pago_inscripcion->save();
                }
            }

            $inscripcion->convenio_id = $convenio->id;
            $inscripcion->save();

            DB::commit();

            return response()->json([
                'message' => 'El convenio fue aplicado a la inscripción exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('inscripciones_ubs.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function eliminar_convenio($id)
    {
        $this->authorize('eliminar_inscripciones_ubs_convenios');

        DB::beginTransaction();

        try {
            // ELIMINAR CUANDO SE HABILITE ADMINISTRACION
            // return redirect()->back()->with('error-message', 'Esta funcionalidad aún no está disponible. Por favor, contacte al administrador del sistema para más información.');

            $inscripcion = InscripcionUbs::findOrFail($id);
            $convenio = Convenio::with('detalle')->findOrFail($inscripcion->convenio_id);
            $pagos_inscripcion = PagoInscripcionUbs::where('inscripcion_id', $inscripcion->id)->orderBy('id', 'asc')->get();

            foreach ($pagos_inscripcion as $pago_inscripcion) {
                $monto = 0;

                if ($pago_inscripcion->tipo == 'CO' && $convenio->detalle->tipo_contado) {
                    switch ($convenio->detalle->tipo_contado) {
                        case 'VA':
                            $monto = $convenio->detalle->descuento_contado;
                            break;
                        case 'PO':
                            $monto = ($convenio->detalle->porcentaje_contado / 100) * $pago_inscripcion->monto;
                            break;
                    }

                    if (($pago_inscripcion->saldo + $monto) != $pago_inscripcion->monto) {
                        return back()->with('error-message', 'No se puede desvincular el convenio. El curso completo ya cuenta con pagos realizados.');
                    }
                }

                if (($pago_inscripcion->tipo == '1C' || $pago_inscripcion->tipo == 'CU') && $convenio->detalle->tipo_cuotas) {
                    switch ($convenio->detalle->tipo_cuotas) {
                        case 'VA':
                            if ($convenio->detalle->aplica_a_cuotas == 'TO') {
                                $monto = $convenio->detalle->descuento_cuotas;
                            } elseif ($convenio->detalle->aplica_a_cuotas == '1C' && $pago_inscripcion->tipo == '1C') {
                                $monto = $convenio->detalle->descuento_cuota_1;
                            } elseif ($convenio->detalle->aplica_a_cuotas == 'CO') {
                                if ((str_contains($pago_inscripcion->descripcion, 'CUOTA 1')) && $convenio->detalle->descuento_cuota_1) {
                                    $monto = $convenio->detalle->descuento_cuota_1;
                                } elseif ((str_contains($pago_inscripcion->descripcion, 'CUOTA 2')) && $convenio->detalle->descuento_cuota_2) {
                                    $monto = $convenio->detalle->descuento_cuota_2;
                                } elseif ((str_contains($pago_inscripcion->descripcion, 'CUOTA 3')) && $convenio->detalle->descuento_cuota_3) {
                                    $monto = $convenio->detalle->descuento_cuota_3;
                                } elseif ((str_contains($pago_inscripcion->descripcion, 'CUOTA 4')) && $convenio->detalle->descuento_cuota_4) {
                                    $monto = $convenio->detalle->descuento_cuota_4;
                                } elseif ((str_contains($pago_inscripcion->descripcion, 'CUOTA 5')) && $convenio->detalle->descuento_cuota_5) {
                                    $monto = $convenio->detalle->descuento_cuota_5;
                                }
                            }
                            break;
                        case 'PO':
                            if ($convenio->detalle->aplica_a_cuotas == 'TO') {
                                $monto = ($convenio->detalle->porcentaje_cuotas / 100) * $pago_inscripcion->monto;
                            } elseif ($convenio->detalle->aplica_a_cuotas == '1C' && $pago_inscripcion->tipo == '1C') {
                                $monto = ($convenio->detalle->porcentaje_cuota_1 / 100) * $pago_inscripcion->monto;
                            } elseif ($convenio->detalle->aplica_a_cuotas == 'CO') {
                                if ((str_contains($pago_inscripcion->descripcion, 'CUOTA 1')) && $convenio->detalle->descuento_cuota_1) {
                                    $monto = ($convenio->detalle->porcentaje_cuota_1 / 100) * $pago_inscripcion->monto;
                                } elseif ((str_contains($pago_inscripcion->descripcion, 'CUOTA 2')) && $convenio->detalle->descuento_cuota_2) {
                                    $monto = ($convenio->detalle->porcentaje_cuota_2 / 100) * $pago_inscripcion->monto;
                                } elseif ((str_contains($pago_inscripcion->descripcion, 'CUOTA 3')) && $convenio->detalle->descuento_cuota_3) {
                                    $monto = ($convenio->detalle->porcentaje_cuota_3 / 100) * $pago_inscripcion->monto;
                                } elseif ((str_contains($pago_inscripcion->descripcion, 'CUOTA 4')) && $convenio->detalle->descuento_cuota_4) {
                                    $monto = ($convenio->detalle->porcentaje_cuota_4 / 100) * $pago_inscripcion->monto;
                                } elseif ((str_contains($pago_inscripcion->descripcion, 'CUOTA 5')) && $convenio->detalle->descuento_cuota_5) {
                                    $monto = ($convenio->detalle->porcentaje_cuota_5 / 100) * $pago_inscripcion->monto;
                                }
                            }
                            break;
                    }

                    if (($pago_inscripcion->saldo + $monto) != $pago_inscripcion->monto) {
                        return back()->with('error-message', 'No se puede desvincular el convenio. La cuota ya cuenta con pagos realizados.');
                    }
                }


                if ($monto != 0) {
                    $pago_inscripcion->monto_convenio = 0;
                    $pago_inscripcion->saldo = $pago_inscripcion->saldo + $monto;
                    if ($pago_inscripcion->saldo == $pago_inscripcion->monto) {
                        $pago_inscripcion->estado = 'PE';
                    }

                    $pago_inscripcion->save();
                }
            }

            $inscripcion->convenio_id = null;
            $inscripcion->save();

            DB::commit();

            return redirect()->route('inscripciones_ubs.show', $id)->with('error-message', 'El convenio fue desvinculado de la inscricpión exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('inscripciones_ubs.show', $id)->with('error-message', $e->getMessage());
        }
    }
}
