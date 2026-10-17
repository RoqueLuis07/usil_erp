<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Luecano\NumeroALetras\NumeroALetras;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

use App\Models\Alumno;
use App\Models\Malla;
use App\Models\MallaDetalle;
use App\Models\SemestreMalla;
use App\Models\ActaEvaluacion;
use App\Models\ActaEvaluacionAlumno;
use App\Models\Materia;
use App\Models\Matriculacion;
use App\Models\Inscripcion;
use App\Models\AlumnoNota;
use App\Models\Empresa;
use App\Models\Carrera;
use App\Models\Convalidacion;
use App\Models\InscripcionTemaTesis;
use App\Models\ExamenSuficienciaActaEvaluacion;
use App\Models\ExamenSuficienciaActaEvaluacionAlumno;
use App\Models\Tutoria;
use App\Models\TutoriaActaEvaluacion;
use App\Models\TutoriaActaEvaluacionAlumno;

class CertificadoEstudioController extends Controller
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

    public function show($id, $tipo)
    {
        if ($this->authorize('ver_certificado_estudios_alumnos')) {
            try {
                $alumno = Alumno::with('alumnoNotas')->findOrFail($id);

                $semestres = collect(); //creamos la coleccion de semestre para luego retornar al front
                $promedios = collect(); //creamos la coleccion de promedios para luego retornar al front
                $total_carga_horaria = 0; //seteamos la carga horaria por semestre en 0
                $notas_all = $alumno->alumnoNotas; //seteamos una nueva variable con alumnoNotas

                if ($notas_all->count() > 0) { //verificamos que existan notas del alumno, si existe hace lo siguiente
                    foreach ($alumno->alumnoNotas as $key => $nota) { //recorremos las notas del alumno
                        $alumno_notas = $notas_all;

                        $detalle = Malla::with(['mallaDetalles' => function ($query) use ($nota) { //obtenemos los detalles de la malla
                            $query->where('materia_id', $nota->materia_id);
                        }])->where('carrera_id', $nota->carrera_id)
                            ->first();

                        if ($detalle->mallaDetalles->first()) {
                            $nota->semestre_materia = $detalle->mallaDetalles->first()->semestre; //agregamos el semestre de la materia a la nota para mostrar en el front   
                        }

                    }

                    $alumno_notas = $alumno_notas->sortBy('semestre_materia');

                    $notas_agrupadas = $alumno_notas->groupBy('semestre_materia');

                    foreach ($notas_agrupadas as $notas) {
                            // $notas_agrupadas = $alumno_notas->groupBy('semestre_materia');

                            // foreach ($notas_agrupadas as $key => $notas) {
                                $promedios = collect(); //seteamos la coleccion de promedios para calcular los promedios por semestre
                                $total_carga_horaria = 0;

                                foreach ($notas as $k => $nota) {
                                    $total_carga_horaria = $total_carga_horaria + $nota->materia->carga_horaria; //hacemos la suma de cada carga horaria para tener el total por semestre

                                    if (is_numeric($nota->calificacion)) {
                                        $promedios->push($nota->calificacion);

                                        $formatter = new NumeroALetras(); //llamamos a la funcion
                                        $nota->calificacion_letras = Str::lower($formatter->toWords($nota->calificacion, 0)); //convertimos la calificacion a letras para el texto
                                    } else {
                                        $nota->calificacion_letras = $nota->calificacion; //si no es numerica, dejamos la calificacion como esta
                                    }

                                    //como se guarda el nombre completo de la evaluacion en la nota, lo que hacemos es obtener la primera letra para obtener el acta
                                    $tipo_evaluacion = substr($nota->evaluacion, 0, 1); //el primer numero es cuantos espacios queres eliminar y el segundo numero cuanto queres dejar
                                    $acta = ActaEvaluacion::whereHas('alumnos', function ($query) use ($alumno) {
                                        $query->where('alumno_id', $alumno->id);
                                    })->where('carrera_id', $nota->carrera_id)->where('materia_id', $nota->materia_id)->where('semestre_id', $nota->semestre_id)->where('tipo', $tipo_evaluacion)->first(); //obtenemos el acta
                                    if ($acta) { //si el acta existe
                                            //verificar el numero de acta, como debe ser y luego cambiar esta parte
                                            $carrera = Carrera::findOrFail($nota->carrera_id); //obtenemos la carrera
                                            $nota->numero_acta = $acta->numero_acta; //concatenamos el numero de acta para obtener el formato necesitado
                                            $nota->fecha = $acta->fecha_evaluacion; //obtenemos la fecha de evaluacion
                                            //hasta aca hay que cambiar
                                    } else {
                                        $convalidacion = Convalidacion::whereHas('convalidacionDetalles', function ($query) use ($nota) {
                                            $query->where('materia_id', $nota->materia_id);
                                        })
                                        ->with(['convalidacionDetalles' => function ($query) use ($nota) {
                                            $query->where('materia_id', $nota->materia_id);
                                        }])
                                        ->where('carrera_id', $nota->carrera_id)
                                        ->where('alumno_id', $nota->alumno_id)
                                        ->where('estado', 'CO')
                                        ->first();

                                        if ($convalidacion) {
                                            $convalidacion_detalle = $convalidacion->convalidacionDetalles->first();
                                            $nota->carga_horaria_convalidacion = $convalidacion_detalle->carga_horaria_materia_origen;
                                            $nota->numero_acta = 'RES. ' . $convalidacion_detalle->numero_resolucion;
                                            $nota->fecha = $convalidacion_detalle->fecha_resolucion;
                                        }

                                        $acta_suficiencia = ExamenSuficienciaActaEvaluacion::where('carrera_id', $nota->carrera_id)->where('materia_id', $nota->materia_id)->where('semestre_id', $nota->semestre_id)->where('tipo', $tipo_evaluacion)->first(); //obtenemos el acta de suficiencia
                                        if ($acta_suficiencia) {
                                            $nota->numero_acta = $acta_suficiencia->numero_acta;
                                            $nota->fecha = $acta_suficiencia->fecha_evaluacion;
                                        }

                                        $tutoria = Tutoria::where('materia_id', $nota->materia_id)->where('semestre_id', $nota->semestre_id)->where('estado', 'FI')->first();
                                        if ($tutoria) {
                                            $acta_tutoria = TutoriaActaEvaluacion::where('tutoria_id', $tutoria->id)->first(); //obtenemos el acta de tutoria
                                            if ($acta_tutoria) {
                                                $nota->numero_acta = $acta_tutoria->numero_acta;
                                                $nota->fecha = $acta_tutoria->fecha_evaluacion;
                                            }
                                        }
                                    }
                                }

                                //creamos un array para luego insertar al collect de semestre
                                $datos_semestre = ['numero' => $nota->semestre_materia,
                                'promedio' => $promedios->avg(),
                                'carga_horaria' => $total_carga_horaria];
                                $semestres->push($datos_semestre); //insertamos en semestres el array generado
                            // }


                            //como se guarda el nombre completo de la evaluacion en la nota, lo que hacemos es obtener la primera letra para obtener el acta
                            $tipo_evaluacion = substr($nota->evaluacion, 0, 1); //el primer numero es cuantos espacios queres eliminar y el segundo numero cuanto queres dejar
                            $acta = ActaEvaluacion::whereHas('alumnos', function ($query) use ($alumno) {
                                $query->where('alumno_id', $alumno->id);
                            })->where('carrera_id', $nota->carrera_id)->where('materia_id', $nota->materia_id)->where('semestre_id', $nota->semestre_id)->where('tipo', $tipo_evaluacion)->first(); //obtenemos el acta
                            if ($acta) { //si el acta existe ejecuta lo siguiente
                                $nota->numero_acta = $acta->numero_acta;
                                $nota->fecha = $acta->fecha_evaluacion; //obtenemos la fecha de evaluacion

                                if (is_numeric($nota->calificacion)) {
                                    $formatter = new NumeroALetras();
                                    $nota->calificacion_letras = Str::lower($formatter->toWords($nota->calificacion, 0));
                                } else {
                                    $nota->calificacion_letras = $nota->calificacion; //si no es numerica, dejamos la calificacion como esta
                                }
                            }
                        
                    }


                    return view('certificados_estudios/show')->with(compact('alumno', 'semestres', 'alumno_notas', 'tipo'));
                } else { //si no existen notas del alumno ejecuta lo siguiente
                    return redirect()->route('alumnos.index')->with('error-message', 'No se puede visualizar el certificado de estudios. El alumno ' . $alumno->primer_nombre . ' ' . $alumno->primer_apellido . ' no cuenta con materias aprobadas.');
                }
            } catch (\Exception $e) {
                return redirect()->route('alumnos.index')->with('error-message', $e->getMessage());
            }
        } else {
            abort(403);
        }
    }

    public function generar($id, $tipo)
    {
        $this->authorize('generar_certificado_estudios_alumnos');

        try {
            $empresa = Empresa::first(); //obtenemos los datos de la empresa para el header
            $alumno = Alumno::with(['solicitudes' => function ($query) {
                $query->where('tipo_solicitud_id', '1')
                    ->where('estado', 'AP');
            }])->findOrFail($id);

            if ($tipo == 'PY') {
                $tesis = InscripcionTemaTesis::where('alumno_id', $alumno->id)->where('estado', 'EN')->first();
                if (!$tesis) {
                    if ($alumno->solicitudes->count() == 0) {
                        return back()->with('error-message', 'El certificado de estudios no puede ser generado. No existen solicitudes pendientes del alumno.');
                    }

                    foreach ($alumno->solicitudes as $solicitud) {
                        if ($solicitud->pagoSolicitud->saldo != 0) {
                            return back()->with('error-message', 'El certificado de estudios no puede ser generado. El alumno no realizó el pago.');
                        }
                        $solicitud->estado = 'GE';
                        $solicitud->save();
                    }
                }
            }

            $alumno = Alumno::with('alumnoNotas')->findOrFail($id);

            //esto es para el texto del pdf
            $nombre_suscribe = config('institucion.secretario_general'); //el nombre del que suscribe, no es muy variable es el mismo lo generamos aqui
            $nombre_rector = config('institucion.rector'); //el nombre del rector, ya que no es muy variable lo generamos solamentea aqui

            //funcion para concatenar el nombre completo del alumno ordenado por apellido nombre
            $apellido_nombre = $alumno->primer_apellido;
            if ($alumno->segundo_apellido) {
                $apellido_nombre .= ' ' . $alumno->segundo_apellido;
            }
            $nombre_completo = $apellido_nombre . ', ' . $alumno->primer_nombre;
            if ($alumno->segundo_nombre) {
                $nombre_completo .= ' ' . $alumno->segundo_nombre;
            }
            if ($alumno->tercer_nombre) {
                $nombre_completo .= ' ' . $alumno->tercer_nombre;
            }
            $alumno->apellido_nombre = $nombre_completo;
            //fin de funcion

            //funcion para concatenar el nombre completo del alumno ordenado por nombre apellido
            $nombre_apellido = $alumno->primer_nombre;
            if ($alumno->segundo_nombre) {
                $nombre_apellido .= ' ' . $alumno->segundo_nombre;
            }
            if ($alumno->tercer_nombre) {
                $nombre_apellido .= ' ' . $alumno->tercer_nombre;
            }
            $nombre_completo = $nombre_apellido . ' ' . $alumno->primer_apellido;
            if ($alumno->segundo_apellido) {
                $nombre_completo .= ' ' . $alumno->segundo_apellido;
            }
            $alumno->nombre_apellido = $nombre_completo;
            //fin de funcion

            //generamos el pronombre del alumno
            switch ($alumno->sexo->nombre) {
                case 'FEMENINO':
                    $alumno->pronombre = 'la alumna';
                    break;
                default:
                    $alumno->pronombre = 'el alumno';
                    break;
            }

            $alumno->nacionalidad = Str::lower($alumno->nacionalidades[0]->nombre); //agregamos el nombre de la nacionalidad al alumno
            //hasta aca es para el texto del pdf

            $semestres = collect(); //seteamos la coleccion de semestres para luego enviar al front
            $promedios_generales = collect(); //seteamos la coleccion de promedios generales para calcular el promedio general
            $carga_horaria_general = 0; //seteamos la carga horaria general para luego ir sumando y devolver al front
            $notas_all = $alumno->alumnoNotas; //seteamos una variable con las notas del alumno
            foreach ($alumno->alumnoNotas as $key => $nota) { //recorremos las notas del alumno
                $alumno_notas = $notas_all;

                $detalle = Malla::with(['mallaDetalles' => function ($query) use ($nota) { //obtenemos los detalles de la malla
                    $query->where('materia_id', $nota->materia_id);
                }])->where('carrera_id', $nota->carrera_id)
                    ->first();

                    if ($detalle->mallaDetalles->first()) {
                        $nota->semestre_materia = $detalle->mallaDetalles->first()->semestre; //agregamos el semestre de la materia a la nota para mostrar en el front   
                    }

            }

            $alumno_notas = $alumno_notas->sortBy('semestre_materia');
            $notas_agrupadas = $alumno_notas->groupBy('semestre_materia');

            foreach ($notas_agrupadas as $notas) {
                    // $notas_agrupadas = $alumno_notas->groupBy('semestre_materia');

                    // foreach ($notas_agrupadas as $key => $notas) {
                        $promedios = collect(); //seteamos la coleccion de promedios para calcular los promedios por semestre
                        $total_carga_horaria = 0; //seteamos el total de carga horaria por semestre para luego calular

                        foreach ($notas as $k => $nota) {
                            $total_carga_horaria = $total_carga_horaria + $nota->materia->carga_horaria; //hacemos la suma de cada carga horaria para tener el total por semestre

                            if (is_numeric($nota->calificacion)) { //verificamos si la calificacion es numerica
                                $promedios->push($nota->calificacion);
                                $promedios_generales->push($nota->calificacion); //insertamos la calificacion a promedios generales para luego calcular

                                $formatter = new NumeroALetras(); //llamamos a la funcion
                                $nota->calificacion_letras = Str::lower($formatter->toWords($nota->calificacion, 0)); //convertimos la calificacion a letras para el texto
                            } else {
                                $nota->calificacion_letras = $nota->calificacion; //si no es numerica, dejamos la calificacion como esta
                            }

                            //como se guarda el nombre completo de la evaluacion en la nota, lo que hacemos es obtener la primera letra para obtener el acta
                            $tipo_evaluacion = substr($nota->evaluacion, 0, 1); //el primer numero es cuantos espacios queres eliminar y el segundo numero cuanto queres dejar
                            $carrera = Carrera::where('id', $nota->carrera_id)->first(); //obtenemos la carrera
                            $acta = ActaEvaluacion::whereHas('alumnos', function ($query) use ($alumno) {
                                $query->where('alumno_id', $alumno->id);
                            })->where('carrera_id', $nota->carrera_id)->where('materia_id', $nota->materia_id)->where('semestre_id', $nota->semestre_id)->where('tipo', $tipo_evaluacion)->first(); //obtenemos el acta
                            if ($acta) { //si el acta existe
                                    $nota->numero_acta = $acta->numero_acta;
                                    $nota->fecha = $acta->fecha_evaluacion; //obtenemos la fecha de evaluacion
                            } else {
                                $convalidacion = Convalidacion::whereHas('convalidacionDetalles', function ($query) use ($nota) {
                                    $query->where('materia_id', $nota->materia_id);
                                })
                                ->with(['convalidacionDetalles' => function ($query) use ($nota) {
                                    $query->where('materia_id', $nota->materia_id);
                                }])
                                ->where('carrera_id', $nota->carrera_id)
                                ->where('alumno_id', $nota->alumno_id)
                                ->where('estado', 'CO')
                                ->first();

                                if ($convalidacion) {
                                    $convalidacion_detalle = $convalidacion->convalidacionDetalles->first();
                                    $nota->carga_horaria_convalidacion = $convalidacion_detalle->carga_horaria_materia_origen;
                                    $nota->numero_acta = 'RES. ' . $convalidacion_detalle->numero_resolucion;
                                    $nota->fecha = $convalidacion_detalle->fecha_resolucion;
                                }

                                $acta_suficiencia = ExamenSuficienciaActaEvaluacion::whereHas('alumnos', function ($query) use ($alumno) {
                                    $query->where('alumno_id', $alumno->id);
                                })->where('carrera_id', $nota->carrera_id)->where('materia_id', $nota->materia_id)->where('semestre_id', $nota->semestre_id)->where('tipo', $tipo_evaluacion)->first(); //obtenemos el acta de suficiencia
                                if ($acta_suficiencia) {
                                    $nota->numero_acta = $acta_suficiencia->numero_acta;
                                    $nota->fecha = $acta_suficiencia->fecha_evaluacion;
                                }

                                $tutoria = Tutoria::where('materia_id', $nota->materia_id)->where('semestre_id', $nota->semestre_id)->where('estado', 'FI')->first();
                                if ($tutoria) {
                                    $acta_tutoria = TutoriaActaEvaluacion::whereHas('alumnos', function ($query) use ($alumno) {
                                        $query->where('alumno_id', $alumno->id);
                                    })->where('tutoria_id', $tutoria->id)->first(); //obtenemos el acta de tutoria
                                    if ($acta_tutoria) {
                                        $nota->numero_acta = $acta_tutoria->numero_acta;
                                        $nota->fecha = $acta_tutoria->fecha_evaluacion;
                                    }
                                }
                            }
                        }

                        //creamos un array para luego insertar al collect de semestre
                        $datos_semestre = ['numero' => $nota->semestre_materia,
                        'promedio' => $promedios->avg(),
                        'carga_horaria' => $total_carga_horaria];
                        $semestres->push($datos_semestre); //insertamos en semestres el array generado

                        $carga_horaria_general = $carga_horaria_general + $total_carga_horaria; //sumamos las cargas horarias del semestre a la carga horaria general
                    // }
                
            }

                $carrera->duracion = $carrera->cantidad_semestres / 2; //obtenemos la duracion de la carrera en años

                $promedio_general = $promedios_generales->avg(); //generamos el promedio general
                $formatter = new NumeroALetras(); //llamamos a la funcion
                $promedio_general_letras = Str::lower($formatter->toWords($promedio_general, 2)); //convertimos el promedio general en letras para el texto
                $carga_horaria_general_letras = Str::lower($formatter->toWords($carga_horaria_general, 0)); //convertimos la carga horaria general en letras para el texto
                if (str_contains($promedio_general_letras, 'con')) { //si el promedio tiene decimales hacemos lo siguiente
                    $promedio_general_letras = $promedio_general_letras . ' centésimas'; //concatenamos la palabra centesimas al promedio general en letras para obtener el formato necesitado
                }
                Carbon::setLocale('es'); //seteamos nuestra fecha en español
                $fecha_hoy = Carbon::now(); //obtenemos la fecha de hoy

                $malla = Malla::with('mallaDetalles')->where('carrera_id', $carrera->id)->first(); //obtenemos la malla con sus detalles para comparar

                $alumno_notas_check = AlumnoNota::where('alumno_id', $alumno->id)->where('carrera_id', $carrera->id)->where('calificacion', '>', '1')->get();

                $carrera->terminado = false; //seteamos que la carrera no esta terminada
                if ($malla->mallaDetalles->count() == $alumno_notas_check->count()) { //si la carrera se encuentra terminada hace lo siguiente
                    $carrera->terminado = true; //seteamos que la carrera esta terminada
                }

                $tesis = InscripcionTemaTesis::where('alumno_id', $alumno->id)->where('estado', 'EN')->first();
                if ($tesis) {
                    $tesis->fecha_defensa = Carbon::parse($tesis->fecha_defensa);
                    $tesis->calificacion_letras = Str::lower($formatter->toWords($tesis->calificacion, 0));
                }

                $pdf = Pdf::loadView('certificados_estudios/pdf_paraguay', compact('empresa', 'nombre_suscribe', 'nombre_rector', 'alumno', 'carrera', 'semestres', 'promedio_general', 'promedio_general_letras', 'carga_horaria_general', 'carga_horaria_general_letras', 'fecha_hoy', 'alumno_notas', 'tipo', 'tesis'));
                $pdf->setPaper('A4');

                return $pdf->stream('certificado_estudios_' . $nota->alumno->numero_documento . '.pdf');
            

        } catch (\Exception $e) {
            return redirect()->route('certificados_estudios.show', ['id' => $id, 'tipo' => $tipo])->with('error-message', $e->getMessage());
        }
    }

    public function generar_informe_academico($id, $tipo)
    {
        $this->authorize('ver_informes_academicos_alumnos_pantalla');

        // try {
            $empresa = Empresa::first(); //obtenemos los datos de la empresa para el header

            $alumno = Alumno::with('alumnoNotas')->where('id', $id)->first();

            if (!$alumno) {
                return back()->with('error-message', 'Ocurrió un error al generar el informe académico, vuelva a intentarlo. Si el error persiste contactar con Soporte IT');
            }

            //funcion para concatenar el nombre completo del alumno ordenado por apellido nombre
            $apellido_nombre = $alumno->primer_apellido;
            if ($alumno->segundo_apellido) {
                $apellido_nombre .= ' ' . $alumno->segundo_apellido;
            }
            $nombre_completo = $apellido_nombre . ', ' . $alumno->primer_nombre;
            if ($alumno->segundo_nombre) {
                $nombre_completo .= ' ' . $alumno->segundo_nombre;
            }
            if ($alumno->tercer_nombre) {
                $nombre_completo .= ' ' . $alumno->tercer_nombre;
            }
            $alumno->apellido_nombre = $nombre_completo;
            //fin de funcion

                $semestres = collect(); //seteamos la coleccion de semestres para luego enviar al front
                $promedios_generales = collect(); //seteamos la coleccion de promedios generales para calcular el promedio general
                $carga_horaria_general = 0; //seteamos la carga horaria general para luego ir sumando y devolver al front
                $notas_all = $alumno->alumnoNotas; //seteamos una variable con las notas del alumno
                foreach ($alumno->alumnoNotas as $key => $nota) { //recorremos las notas del alumno
                    $alumno_notas = $notas_all;

                    $detalle = Malla::with(['mallaDetalles' => function ($query) use ($nota) { //obtenemos los detalles de la malla
                        $query->where('materia_id', $nota->materia_id);
                    }])->where('carrera_id', $nota->carrera_id)
                        ->first();

                    if ($detalle->mallaDetalles->first()) {
                        $nota->semestre_materia = $detalle->mallaDetalles->first()->semestre; //agregamos el semestre de la materia a la nota para mostrar en el front
                    }
                }

                $alumno_notas = $alumno_notas->sortBy('semestre_materia');
                $notas_agrupadas = $alumno_notas->groupBy('semestre_materia');

                foreach ($notas_agrupadas as $notas) {
                        $total_carga_horaria = 0; //seteamos el total de carga horaria por semestre para luego calular

                        foreach ($notas as $k => $nota) {
                            if (is_numeric($nota->calificacion)) { //verificamos si la calificacion es numerica
                                $promedios_generales->push($nota->calificacion); //insertamos la calificacion a promedios generales para luego calcular

                                $formatter = new NumeroALetras(); //llamamos a la funcion
                                $nota->calificacion_letras = Str::lower($formatter->toWords($nota->calificacion, 0)); //convertimos la calificacion a letras para el texto
                            } else {
                                $nota->calificacion_letras = $nota->calificacion; //si no es numerica, dejamos la calificacion como esta
                            }

                            //como se guarda el nombre completo de la evaluacion en la nota, lo que hacemos es obtener la primera letra para obtener el acta
                            $tipo_evaluacion = substr($nota->evaluacion, 0, 1); //el primer numero es cuantos espacios queres eliminar y el segundo numero cuanto queres dejar
                            $carrera = Carrera::where('id', $nota->carrera_id)->first(); //obtenemos la carrera
                            $acta = ActaEvaluacion::whereHas('alumnos', function ($query) use ($alumno) {
                                $query->where('alumno_id', $alumno->id);
                            })->where('carrera_id', $nota->carrera_id)->where('materia_id', $nota->materia_id)->where('semestre_id', $nota->semestre_id)->where('tipo', $tipo_evaluacion)->first(); //obtenemos el acta
                            if ($acta) { //si el acta existe
                                    $nota->numero_acta = $acta->numero_acta;
                                    $nota->fecha = $acta->fecha_evaluacion; //obtenemos la fecha de evaluacion

                                    $acta_detalle = ActaEvaluacionAlumno::where('acta_evaluacion_id', $acta->id)->where('alumno_id', $alumno->id)->first(); //obtenemos el detalle del acta
                                    if ($acta_detalle) {
                                        $nota->puntaje_total = $acta_detalle->puntos_obtenidos + $acta_detalle->puntos_examen;
                                    }
                            } else {
                                $convalidacion = Convalidacion::whereHas('convalidacionDetalles', function ($query) use ($nota) {
                                    $query->where('materia_id', $nota->materia_id);
                                })
                                ->with(['convalidacionDetalles' => function ($query) use ($nota) {
                                    $query->where('materia_id', $nota->materia_id);
                                }])
                                ->where('carrera_id', $nota->carrera_id)
                                ->where('alumno_id', $nota->alumno_id)
                                ->where('estado', 'CO')
                                ->first();

                                if ($convalidacion) {
                                    $convalidacion_detalle = $convalidacion->convalidacionDetalles->first();
                                    $nota->carga_horaria_convalidacion = $convalidacion_detalle->carga_horaria_materia_origen;
                                    $nota->numero_acta = 'RES. ' . $convalidacion_detalle->numero_resolucion;
                                    $nota->fecha = $convalidacion_detalle->fecha_resolucion;
                                }

                                $acta_suficiencia = ExamenSuficienciaActaEvaluacion::whereHas('alumnos', function ($query) use ($alumno) {
                                    $query->where('alumno_id', $alumno->id);
                                })->where('carrera_id', $nota->carrera_id)->where('materia_id', $nota->materia_id)->where('semestre_id', $nota->semestre_id)->where('tipo', $tipo_evaluacion)->first(); //obtenemos el acta de suficiencia
                                if ($acta_suficiencia) {
                                    $nota->numero_acta = $acta_suficiencia->numero_acta;
                                    $nota->fecha = $acta_suficiencia->fecha_evaluacion;

                                    $acta_suficiencia_detalle = ExamenSuficienciaActaEvaluacionAlumno::where('acta_evaluacion_id', $acta_suficiencia->id)->where('alumno_id', $alumno->id)->first(); //obtenemos el detalle del acta de suficiencia
                                    if ($acta_suficiencia_detalle) {
                                        $nota->puntaje_total = $acta_suficiencia_detalle->puntos_obtenidos + $acta_suficiencia_detalle->puntos_examen;
                                    }
                                }

                                $tutoria = Tutoria::where('materia_id', $nota->materia_id)->where('semestre_id', $nota->semestre_id)->where('estado', 'FI')->first();
                                if ($tutoria) {
                                    $acta_tutoria = TutoriaActaEvaluacion::whereHas('alumnos', function ($query) use ($alumno) {
                                        $query->where('alumno_id', $alumno->id);
                                    })->where('tutoria_id', $tutoria->id)->first(); //obtenemos el acta de tutoria
                                    if ($acta_tutoria) {
                                        $nota->numero_acta = $acta_tutoria->numero_acta;
                                        $nota->fecha = $acta_tutoria->fecha_evaluacion;

                                        $acta_tutoria_detalle = TutoriaActaEvaluacionAlumno::where('acta_evaluacion_id', $acta_tutoria->id)->where('alumno_id', $alumno->id)->first(); //obtenemos el detalle del acta de tutoria
                                        if ($acta_tutoria_detalle) {
                                            $nota->puntaje_total = $acta_tutoria_detalle->puntos_obtenidos + $acta_tutoria_detalle->puntos_examen;
                                        }
                                    }
                                }
                            }
                        }

                        $cantidad_materias = MallaDetalle::whereHas('malla', function ($query) use ($nota) {
                            $query->where('carrera_id', $nota->carrera_id);
                        })->where('semestre', $nota->semestre_materia)->count(); //obtenemos la cantidad de materias del semestre

                        //creamos un array para luego insertar al collect de semestre
                        $datos_semestre = ['numero' => $nota->semestre_materia,
                                        'cantidad_materias' => $cantidad_materias];
                        $semestres->push($datos_semestre); //insertamos en semestres el array generado
                    
                }

                    $promedio_general = $promedios_generales->avg(); //generamos el promedio general
                    $formatter = new NumeroALetras(); //llamamos a la funcion
                    Carbon::setLocale('es'); //seteamos nuestra fecha en español
                    $fecha_hoy = Carbon::now(); //obtenemos la fecha de hoy

                    $malla = Malla::with('mallaDetalles')->where('carrera_id', $carrera->id)->first(); //obtenemos la malla con sus detalles para comparar

                    $alumno_notas_check = AlumnoNota::where('alumno_id', $alumno->id)->where('carrera_id', $carrera->id)->where('calificacion', '>', '1')->get();

                    $pdf = Pdf::loadView('pantallas_alumnos/informes_academicos/pdf_paraguay', compact('empresa', 'alumno', 'carrera', 'semestres', 'promedio_general', 'fecha_hoy', 'alumno_notas'));
                    $pdf->setPaper('A4');

                    return $pdf->stream('informe_academico_' . $nota->alumno->numero_documento . '.pdf');
                
            

        // } catch (\Exception $e) {
        //     return redirect()->route('pantallas_alumnos.index', $id)->with('error-message', 'Ocurrió un error al generar el informe académico. Si el error persiste, contacte al administrador.');
        // }
    }

}
