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
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\Empresa;
use App\Models\ActaEvaluacion;
use App\Models\ActaEvaluacionAlumno;
use App\Models\AlumnoNota;
use App\Models\Matriculacion;
use App\Models\Inscripcion;
use App\Models\Materia;
use App\Models\Semestre;
use App\Models\Carrera;
use App\Models\Evaluacion;
use App\Models\Alumno;
use App\Models\Malla;
use App\Models\SemestreMalla;
use App\Models\SemestreMallaMateria;
use App\Models\ClaseMateria;


class ActaEvaluacionController extends Controller
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
        $this->authorize('ver_actas');

        try {
            $actas_evaluaciones = ActaEvaluacion::orderBy('numero_acta', 'desc')->get();
            return view('actas_evaluaciones/index')->with(compact('actas_evaluaciones'));
        } catch (\Exception $e) {
            return redirect()->route('actas_evaluaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_actas');

        try {
            $acta_evaluacion = ActaEvaluacion::with('alumnos')->findOrFail($id);
            return view('actas_evaluaciones/show')->with(compact('acta_evaluacion'));
        } catch (\Exception $e) {
            return redirect()->route('actas_evaluaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function show_actas($materia_id, $semestre_id, $carrera_id)
    {
        $this->authorize('ver_actas_evaluaciones_materias_semestres');

        DB::beginTransaction();

        try {
            $materia = Materia::findOrFail($materia_id);
            $semestre = Semestre::findOrFail($semestre_id);
            $carrera = Carrera::findOrFail($carrera_id);

            $acta = ActaEvaluacion::with('alumnos')->where('semestre_id', $semestre_id)->where('carrera_id', $carrera_id)->where('materia_id', $materia_id);
            $cantidad_actas = $acta->count();

            if ($cantidad_actas > 0) {
                if ($cantidad_actas != 1) {
                    $tipo = 6;
                    $tipo_examen = 'Extraordinario';
                } else {
                    $tipo = 5;
                    $tipo_examen = 'Complementario';
                }
            } else {
                $tipo = 4;
                $tipo_examen = 'Ordinario';
            }

            $evaluacion = Evaluacion::where('tipo_evaluacion_id', $tipo)->first();
            $puntaje_minimo_requerido = $evaluacion->puntaje_minimo_requerido;
            switch ($carrera->carrera_id) {
                case 1:
                    $porcentaje_minimo_asistencia = 70;
                    break;
                case 2:
                    $porcentaje_minimo_asistencia = 50;
                    break;
                default:
                    $porcentaje_minimo_asistencia = 0;
                    break;
            }
			
			$alumnos_total = Alumno::whereHas('alumnoPuntajes', function ($query) use ($materia_id, $semestre_id) {
				$query->where('materia_id', $materia_id)
					  ->where('semestre_id', $semestre_id);
			})
			->whereHas('alumnoAsistencias', function ($query) use ($materia_id, $semestre_id) {
				$query->where('materia_id', $materia_id)
					  ->where('semestre_id', $semestre_id);
			})
			->whereHas('matriculaciones', function ($query) use ($carrera_id, $materia_id, $semestre_id) {
				$query->where('carrera_id', $carrera_id)
					  ->where('semestre_id', $semestre_id)
					  ->whereHas('inscripciones', function ($query) use ($materia_id) {
						    $query->where('materia_id', $materia_id)
								->where('estado', 'EC');
					  });
                    //   ->whereHas('pagosMatriculacion', function ($query) {
                    //         $query->where('estado', 'CA')
                    //             ->where('saldo', 0);
                    //   });
			})->get();
			
			$clases_materias = ClaseMateria::where('materia_id', $materia_id)->where('semestre_id', $semestre_id)->sum('horas_desarrollo');
			
			$alumnos = collect();
			
			foreach ($alumnos_total as $alumno) {
				$alumno->asistencia_obtenida = 0;
				$alumno->puntos_obtenidos = 0;
				foreach ($alumno->alumnoAsistencias as $asistencia) {
					if ($asistencia->materia_id == $materia_id && $asistencia->semestre_id == $semestre_id && ($asistencia->estado == 'PR' || $asistencia->estado == 'AJ')) {
						$alumno->asistencia_obtenida = $alumno->asistencia_obtenida + $asistencia->horas_desarrollo;
					}
				}
				
				$alumno->asistencia_obtenida = ($alumno->asistencia_obtenida / $clases_materias) * 100;
				
				foreach ($alumno->alumnoPuntajes as $puntaje) {
					if ($puntaje->materia_id == $materia_id && $puntaje->semestre_id == $semestre_id) {
						$alumno->puntos_obtenidos = $alumno->puntos_obtenidos + $puntaje->puntos_obtenidos;
					}
				}
				
				if ($alumno->asistencia_obtenida >= $porcentaje_minimo_asistencia && $alumno->puntos_obtenidos >= $puntaje_minimo_requerido) {
					$alumnos->push($alumno);
				}
			}

            $semestre_malla_materia = SemestreMallaMateria::where('materia_id', $materia_id)
                ->whereHas('semestreMalla', function ($query) use ($semestre_id) {
                    $query->where('semestre_id', $semestre_id);
                })->first();

            return view('materias_semestres/evaluaciones/actas/show')->with(compact('materia', 'semestre', 'carrera', 'alumnos', 'semestre_malla_materia', 'cantidad_actas', 'tipo_examen'));

        } catch (\Exception $e) {
            return redirect()->route('materias_semestres.index')->with('error-message', $e->getMessage());
        }
    }

    public function generar_actas(Request $request, $materia_id, $semestre_id, $carrera_id, $tipo)
    {
        $this->authorize('generar_actas_evaluaciones_materias_semestres');

        DB::beginTransaction();

        try {
            $materia = Materia::findOrFail($materia_id);
            $semestre = Semestre::findOrFail($semestre_id);
            if ($semestre->estado == 'IN') {
                return back()->with('error-message', 'El acta de evaluación no puede ser generado. El semestre se encuentra cerrado.');
            }

            $carrera = Carrera::findOrFail($carrera_id);

            $evaluacion = Evaluacion::where('tipo_evaluacion_id', $tipo)->first();
            $puntaje_minimo_requerido = $evaluacion->puntaje_minimo_requerido;
            switch ($carrera->carrera_id) {
                case 1:
                    $porcentaje_minimo_asistencia = 70;
                    break;
                case 2:
                    $porcentaje_minimo_asistencia = 50;
                    break;
                default:
                    $porcentaje_minimo_asistencia = 0;
                    break;
            }

            $alumnos_total = Alumno::whereHas('alumnoPuntajes', function ($query) use ($materia_id, $semestre_id) {
				$query->where('materia_id', $materia_id)
					  ->where('semestre_id', $semestre_id);
			})
			->whereHas('alumnoAsistencias', function ($query) use ($materia_id, $semestre_id) {
				$query->where('materia_id', $materia_id)
					  ->where('semestre_id', $semestre_id);
			})
			->whereHas('matriculaciones', function ($query) use ($carrera_id, $materia_id, $semestre_id) {
				$query->where('carrera_id', $carrera_id)
					  ->where('semestre_id', $semestre_id)
					  ->whereHas('inscripciones', function ($query) use ($materia_id) {
						  $query->where('materia_id', $materia_id)
								->where('estado', 'EC');
					  });
                      //   ->whereHas('pagosMatriculacion', function ($query) {
                      //         $query->where('estado', 'CA')
                      //             ->where('saldo', 0);
                      //   });
			})->get();
			
			$clases_materias = ClaseMateria::where('materia_id', $materia_id)->where('semestre_id', $semestre_id)->sum('horas_desarrollo');
			
			$alumnos = collect();
			
			foreach ($alumnos_total as $alumno) {
				$alumno->asistencia_obtenida = 0;
				$alumno->puntos_obtenidos = 0;
				foreach ($alumno->alumnoAsistencias as $asistencia) {
					if ($asistencia->materia_id == $materia_id && $asistencia->semestre_id == $semestre_id && ($asistencia->estado == 'PR' || $asistencia->estado == 'AJ')) {
						$alumno->asistencia_obtenida = $alumno->asistencia_obtenida + $asistencia->horas_desarrollo;
					}
				}
				
				$alumno->asistencia_obtenida = ($alumno->asistencia_obtenida / $clases_materias) * 100;
				
				foreach ($alumno->alumnoPuntajes as $puntaje) {
					if ($puntaje->materia_id == $materia_id && $puntaje->semestre_id == $semestre_id) {
						$alumno->puntos_obtenidos = $alumno->puntos_obtenidos + $puntaje->puntos_obtenidos;
					}
				}
				
				if ($alumno->asistencia_obtenida >= $porcentaje_minimo_asistencia && $alumno->puntos_obtenidos >= $puntaje_minimo_requerido) {
					$alumnos->push($alumno);
				}
			}

            $malla = Malla::where('carrera_id', $carrera_id)->first();
            $semestre_malla = SemestreMalla::where('semestre_id', $semestre_id)->where('malla_id', $malla->id)->first();
            $semestre_malla_materia = SemestreMallaMateria::with('semestreMalla.malla.mallaDetalles')->where('materia_id', $materia_id)->where('semestre_malla_id', $semestre_malla->id)->first();

            switch ($tipo) {
                case 4:
                    $fecha_evaluacion = $semestre_malla_materia->fecha_examen_ordinario;
                    $nombre_examen = 'ORDINARIO';
                    $tipo_acta = 'O';
                    $oportunidad = 'PRIMERA';
                    break;
                case 5:
                    $fecha_evaluacion = $semestre_malla_materia->fecha_examen_complementario;
                    $nombre_examen = 'COMPLEMENTARIO';
                    $tipo_acta = 'C';
                    $oportunidad = 'SEGUNDA';
                    break;
                case 6:
                    $fecha_evaluacion = $semestre_malla_materia->fecha_examen_extraordinario;
                    $nombre_examen = 'EXTRAORDINARIO';
                    $tipo_acta = 'E';
                    $oportunidad = 'TERCERA';
                    break;
                default:
                    return redirect()->route('materias_semestres.index')->with('error-message', 'Ocurrió un error en la generación. Si el problema persiste, contacte con el Administrador.');
                    break;
            }

            if (!$fecha_evaluacion) {
                return redirect()->route('materias_semestres.index')->with('error-message', 'La fecha de evaluación del EXAMEN ' . $nombre_examen . ' no se encuentra cargada, por favor cárguela e intente nuevamente.');
            }

            foreach ($semestre_malla_materia->semestreMalla->malla->mallaDetalles as $detalle) {
                if ($detalle->materia_id == $materia->id) {
                    $semestre_materia = $detalle->semestre;
                }
            }
            $docente = $semestre_malla_materia->docente;

            $old_acta = ActaEvaluacion::orderBy('id', 'desc')->where('carrera_id', $carrera_id)->first();
            $anho_actual = Carbon::now()->format('Y');
            $carrera = Carrera::findOrFail($carrera_id);
            if ($old_acta) {
                $numero_acta = substr($old_acta->numero_acta, 5, 191); //el primer numero es cuantos espacios queres eliminar y el segundo numero cuanto queres dejar
                $partes_numero = explode('/', $numero_acta); //separamos el string de numero_dictamen
                if ($anho_actual == $partes_numero[1]) {
                    $numero_nuevo = intval($partes_numero[0]) + 1;
                    $numero = $carrera->abreviatura . str_pad($numero_nuevo, 5,'0', STR_PAD_LEFT) . '/' . $partes_numero[1];
                } else {
                    $numero = $carrera->abreviatura . str_pad(1, 5, '0', STR_PAD_LEFT) . '/'. $anho_actual;
                }
            } else {
                $numero = $carrera->abreviatura . str_pad(1, 5, '0', STR_PAD_LEFT) . '/'. $anho_actual;
            }

            $acta = ActaEvaluacion::with('alumnos')->where('semestre_id', $semestre_id)->where('carrera_id', $carrera_id)->where('materia_id', $materia_id);

            $existe_acta = ActaEvaluacion::where('materia_id', $materia_id)
                ->where('tipo', $tipo_acta)
                ->where('carrera_id', $carrera_id)
                ->where('semestre_id', $semestre_id)
                ->where('docente_id', $docente->id)->first();

            if ($existe_acta) {
                return redirect()->route('actas_evaluaciones.show', $existe_acta->id)->with('error-message', 'El acta ya existe, no se puede vovler a generar, aquí te lo mostramos.');
            }

            $acta = new ActaEvaluacion();
            $acta->numero_acta = $numero;
            $acta->tipo = $tipo_acta;
            $acta->carrera_id = $carrera_id;
            $acta->materia_id = $materia_id;
            $acta->semestre_id = $semestre_id;
            $acta->docente_id = $docente->id;
            $acta->fecha_evaluacion = $fecha_evaluacion;
            $acta->materia_semestre = $semestre_materia;
            $acta->seccion = $carrera->programa->turno;
            $acta->generado_por_id = Auth::id();
            $acta->save();

            foreach ($alumnos as $alumno) {
                $acta_alumnos = new ActaEvaluacionAlumno();
                $acta_alumnos->acta_evaluacion_id = $acta->id;
                $acta_alumnos->alumno_id = $alumno->id;
                $acta_alumnos->puntos_obtenidos = $alumno->puntos_obtenidos;
                $acta_alumnos->save();
            }

            DB::commit();

            $empresa = Empresa::first();

            $titulo = 'ACTA DE EVALUACIÓN FINAL - ' . $nombre_examen;

            switch ($docente->nivel_academico_id) {
                case 1:
                    $prenombre_docente = 'LIC.';
                    break;

                case 2:
                    $prenombre_docente = 'MGTR.';
                    break;
                case 3:
                    $prenombre_docente = 'DR.';
                    break;
                case 4:
                    $prenombre_docente = 'PHD.';
                    break;
                default:
                    $prenombre_docente = '';
                    break;
            }

            $fecha_hoy = Carbon::now();
            $pdf = Pdf::loadView('materias_semestres/evaluaciones/actas/pdf', compact('empresa', 'titulo', 'oportunidad', 'numero', 'materia', 'semestre', 'carrera', 'alumnos', 'fecha_evaluacion', 'semestre_materia', 'docente', 'prenombre_docente', 'fecha_hoy'));
            $pdf->setPaper('A4', 'landscape');
            $contenido_pdf = $pdf->output();

            $nombre_archivo = 'acta_' . Str::lower(str_replace('/', '_', $numero)) . '.pdf';
            $contenido_pdf = $pdf->output();
            $ubicacion_archivo = 'public/actas_evaluaciones/generados/' . $nombre_archivo;
            Storage::put($ubicacion_archivo, $contenido_pdf);
            $acta->ubicacion_acta = str_replace('public', 'storage', $ubicacion_archivo);
            $acta->save();

            DB::commit();

            return $pdf->stream('acta_' . Str::lower(str_replace('/', '_', $numero)) . '.pdf');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('materias_semestres.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_actas');

        DB::beginTransaction();

        try {
            $acta_evaluacion = ActaEvaluacion::findOrFail($id);

            if ($acta_evaluacion->semestre->estado == 'IN') {
                return back()->with('error-message', 'El acta de evaluación no se puede borrar. El semestre se encuentra cerrado.');
            }

            $ubicacion_acta = $acta_evaluacion->ubicacion_acta;
            $ubicacion_acta = str_replace('storage', 'public', $ubicacion_acta);
            Storage::delete($ubicacion_acta);
            $ubicacion_archivo = $acta_evaluacion->ubicacion_adjunto;
            $ubicacion_archivo = str_replace('storage', 'public', $ubicacion_archivo);
            Storage::delete($ubicacion_archivo);

            $actas_evaluaciones_alumnos = ActaEvaluacionAlumno::where('acta_evaluacion_id', $id)->get();

            foreach ($actas_evaluaciones_alumnos as $lista_alumno) {
                $alumno_nota = AlumnoNota::where('alumno_id', $lista_alumno->alumno_id)
                    ->where('carrera_id', $acta_evaluacion->carrera_id)
                    ->where('materia_id', $acta_evaluacion->materia_id)
                    ->where('semestre_id', $acta_evaluacion->semestre_id)
                    ->delete();

                    $matriculacion = Matriculacion::where('alumno_id', $lista_alumno->alumno_id)
                        ->where('semestre_id', $acta_evaluacion->semestre_id)
                        ->where('programa_id', $acta_evaluacion->carrera->programa_id)
                        ->where('carrera_id', $acta_evaluacion->carrera_id)
                        ->first();

                    $inscripcion = Inscripcion::where('matriculacion_id', $matriculacion->id)
                        ->where('materia_id', $acta_evaluacion->materia_id)
                        ->where('alumno_id', $lista_alumno->alumno_id)
                        ->first();

                    if ($inscripcion->estado == 'AP' || $inscripcion->estado == 'RE') {
                        $inscripcion->estado = 'EC';
                        $inscripcion->save();
                    }
            }

            $actas_evaluaciones_alumnos = ActaEvaluacionAlumno::where('acta_evaluacion_id', $id)->delete();
            $acta_evaluacion->delete();

            DB::commit();

            return redirect()->route('actas_evaluaciones.index')->with('success-message','El acta N° ' . str_pad($acta_evaluacion->numero_acta, 7, 0, STR_PAD_LEFT) . ' de la materia ' . $acta_evaluacion->materia->nombre_fantasia . ' de la carrera ' . $acta_evaluacion->carrera->nombre . ' del semestre ' . $acta_evaluacion->semestre->nombre . ' fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('actas_evaluaciones.index')->with('error-message', 'El acta N° ' . str_pad($acta_evaluacion->numero_acta, 7, 0, STR_PAD_LEFT) . ' de la materia ' . $acta_evaluacion->materia->nombre_fantasia . ' de la carrera ' . $acta_evaluacion->carrera->nombre . ' del semestre ' . $acta_evaluacion->semestre->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('actas_evaluaciones.index')->with('error-message', $e->getMessage());
            }
        }
    }

    public function subir_acta(Request $request, $id)
    {
        $this->authorize('subir_adjunto_actas');

        $request->validate([
            'acta' => ['required', 'file', 'extensions:jpg,png,pdf']
        ]);

        DB::beginTransaction();

        try {
            $acta_evaluacion = ActaEvaluacion::findOrFail($id);

            //cargar archivo
            $archivo = $request->acta;
            $extension = $archivo->getClientOriginalExtension();
            $directorio = 'storage/actas_evaluaciones/firmados';
            if ($extension == 'jpg' || $extension == 'jpeg' || $extension == 'png') {
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($archivo);
                $image = $image->encode(new AutoEncoder(quality: 50));
                $nombre = Str::lower(str_replace('/', '_', $acta_evaluacion->numero_acta)) . '_' . Str::lower($acta_evaluacion->tipo) . '_' . Str::lower($acta_evaluacion->materia->nombre_fantasia) . '_' . Str::lower($acta_evaluacion->carrera->nombre_fantasia) . '_' . $acta_evaluacion->semestre->nombre;
                $nombre_archivo = $nombre . '.' . $extension;
                $image->save($directorio . '/' . $nombre_archivo);
            } else {
                $directorio_storage = 'public/actas_evaluaciones/firmados';
                $nombre = Str::lower(str_replace('/', '_', $acta_evaluacion->numero_acta)) . '_' . Str::lower($acta_evaluacion->tipo) . '_' . Str::lower($acta_evaluacion->materia->nombre_fantasia) . '_' . Str::lower($acta_evaluacion->carrera->nombre_fantasia) . '_' . $acta_evaluacion->semestre->nombre;
                $nombre_archivo = $nombre . '.' . $extension;
                Storage::putFileAs($directorio_storage . '/', $archivo, $nombre_archivo);
            }
            $acta_evaluacion->ubicacion_adjunto = $directorio . '/' . $nombre_archivo;
            $acta_evaluacion->save();

            DB::commit();

            return response()->json([
                'message' => 'El adjunto del acta N° ' . $acta_evaluacion->numero_acta . ' de la materia ' . $acta_evaluacion->materia->nombre_fantasia . ' de la carrera ' . $acta_evaluacion->carrera->nombre_fantasia . ' del semestre ' . $acta_evaluacion->semestre->nombre . ' fue subido exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('actas_evaluaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function eliminar_acta($id)
    {
        $this->authorize('eliminar_adjunto_actas');

        DB::beginTransaction();

        try {
            $acta_evaluacion = ActaEvaluacion::findOrFail($id);

            $ubicacion_archivo = $acta_evaluacion->ubicacion_adjunto;
            $ubicacion_archivo = str_replace('storage', 'public', $ubicacion_archivo);
            Storage::delete($ubicacion_archivo);

            $acta_evaluacion->ubicacion_adjunto = null;
            $acta_evaluacion->save();


            DB::commit();

            return redirect()->route('actas_evaluaciones.show', $acta_evaluacion->id)->with('error-message','El adjunto del acta N° ' . str_pad($acta_evaluacion->numero_acta, 7, 0, STR_PAD_LEFT) . ' de la materia ' . $acta_evaluacion->materia->nombre_fantasia . ' de la carrera ' . $acta_evaluacion->carrera->nombre_fantasia . ' del semestre ' . $acta_evaluacion->semestre->nombre . ' fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('actas_evaluaciones.index')->with('error-message', $e->getMessage());
        }
    }
}
