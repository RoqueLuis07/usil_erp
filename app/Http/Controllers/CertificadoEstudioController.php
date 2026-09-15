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
use App\Models\MallaEspejo;
use App\Models\MallaEspejoDetalle;
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
        if ($this->authorize('ver_certificado_estudios_alumnos') || $this->authorize('ver_certificado_estudios_siu_alumnos')) {
            try {
                $alumno = Alumno::with('alumnoNotas')->findOrFail($id);

                $semestres = collect(); //creamos la coleccion de semestre para luego retornar al front
                $semestres_romanos = collect(); //creamos la coleccion de semestre para luego retornar al front
                $promedios = collect(); //creamos la coleccion de promedios para luego retornar al front
                $total_carga_horaria = 0; //seteamos la carga horaria por semestre en 0
                $notas_all = $alumno->alumnoNotas; //seteamos una nueva variable con alumnoNotas

                if ($notas_all->count() > 0) { //verificamos que existan notas del alumno, si existe hace lo siguiente
                    foreach ($alumno->alumnoNotas as $key => $nota) { //recorremos las notas del alumno
                        if ($nota->carrera->programa_id != 4 && $tipo != 'SIU') { //caso para certificado de Paraguay
                            $alumno_notas = $notas_all->filter(function ($item) use ($nota, $tipo) {
                                return ($item->carrera->programa_id != 4 && $tipo != 'SIU'); //filtra y devuelve solamente las materias de Paraguay
                            });
                        } else if ($nota->carrera->programa_id == 4 && $tipo == 'SIU') { //caso para certificado de SIU
                            $alumno_notas = $notas_all->filter(function ($item) use ($nota, $tipo) {
                                return ($item->carrera->programa_id == 4 && $tipo == 'SIU'); //filtra y devuelve solamente las materias SIU
                            });
                        }

                        $detalle = Malla::with(['mallaDetalles' => function ($query) use ($nota) { //obtenemos los detalles de la malla
                            $query->where('materia_id', $nota->materia_id);
                        }])->where('carrera_id', $nota->carrera_id)
                            ->first();

                        if ($detalle->mallaDetalles->first()) {
                            $nota->semestre_materia = $detalle->mallaDetalles->first()->semestre; //agregamos el semestre de la materia a la nota para mostrar en el front   
                        }

                        // if ($key == 0 && $tipo == 'SIU') {
                        //     $malla = Malla::where('carrera_id', $nota->carrera_id)->first();
                        //     $malla_detalles = MallaDetalle::where('malla_id', $malla->id)->orderBy('semestre')->get();
                        //     $malla_detalles = $malla_detalles->groupBy('semestre');
                        //     foreach ($malla_detalles as $k => $malla_detalle) {
                        //         if ($nota->semestre_materia == $k) {
                        //             $semestres_romanos->push(['numero' => $k]);
                        //             $semestres->push(['numero' => $k]);
                        //         }
                        //     }
                        // }
                    }

                    $alumno_notas = $alumno_notas->sortBy('semestre_materia');

                    $notas_agrupadas = $alumno_notas->groupBy('semestre_materia');

                    foreach ($notas_agrupadas as $notas) {
                        if ($tipo == 'SIU') { //para el caso de SIU
                            foreach ($notas as $k => $nota) {
                                $matriculacion = Matriculacion::where('alumno_id', $nota->alumno_id)->where('semestre_id', $nota->semestre_id)->where('carrera_siu_id', $nota->carrera_id)->first(); //obtenemos la matriculacion del alumno
                                if ($matriculacion) { //si la matriculacion existe
                                    $alumno_nota = AlumnoNota::where('alumno_id', $matriculacion->alumno_id)->where('carrera_id', $matriculacion->carrera_siu_id)->where('materia_id', $nota->materia_id)->where('semestre_id', $matriculacion->semestre_id)->first(); //obtenemos la nota del alumno

                                    $acta = ActaEvaluacion::whereHas('alumnos', function ($query) use ($alumno) {
                                        $query->where('alumno_id', $alumno->id);
                                    })->where('carrera_id', $nota->carrera_id)->where('materia_id', $nota->materia_id)->where('semestre_id', $nota->semestre_id)->where('tipo', substr($nota->evaluacion, 0, 1))->first(); //obtenemos el acta
                                    if ($acta) { //si el acta existe
                                        $nota->fecha = $acta->fecha_evaluacion; //agregamos la fecha de evaluacion a la nota
                                    } else {
                                        $malla_paraguay = Malla::where('carrera_id', $matriculacion->carrera_id)->where('estado', 'AC')->first();
                                        $malla_siu = Malla::where('carrera_id', $matriculacion->carrera_siu_id)->where('estado', 'AC')->first();

                                        $malla_espejo = MallaEspejo::whereHas('mallaEspejoDetalles', function ($query) use ($nota) {
                                            $query->where('materia_siu_id', $nota->materia_id);
                                        })->with(['mallaEspejoDetalles' => function ($query) use ($nota) {
                                            $query->where('materia_siu_id', $nota->materia_id);
                                        }])
                                        ->where('malla_paraguay_id', $malla_paraguay->id)->where('malla_siu_id', $malla_siu->id)->first();

                                        $acta = ActaEvaluacion::whereHas('alumnos', function ($query) use ($alumno) {
                                            $query->where('alumno_id', $alumno->id);
                                        })->where('carrera_id', $matriculacion->carrera_id)->where('materia_id', $malla_espejo->mallaEspejoDetalles->first()->materia_paraguay_id)->where('semestre_id', $nota->semestre_id)->first(); //obtenemos el acta de evaluacion
                                        if ($acta) {
                                            $nota->fecha = $acta->fecha_evaluacion; //agregamos la fecha de evaluacion a la nota
                                        }
                                    }

                                    // $acta_detalle = ActaEvaluacionAlumno::where('alumno_id', $nota->alumno_id)->where('calificacion', $alumno_nota->calificacion)->first(); //obtenemos el detalle del acta
                                    // if ($acta_detalle) {
                                    //     $acta = ActaEvaluacion::findOrFail($acta_detalle->acta_evaluacion_id); //obtenemos el acta
                                    //     if ($acta) { //si el acta existe ejecuta lo siguiente
                                    //         $nota->fecha = $acta->fecha_evaluacion; //agregamos a la nota la fecha de evaluacion
                                    //     }
                                    // }
                                }

                                // $acta = ActaEvaluacion::findOrFail($acta_detalle->acta_evaluacion_id); //obtenemos el acta
                                // if ($acta) { //si el acta existe ejecuta lo siguiente
                                //     $nota->fecha = $acta->fecha_evaluacion; //agregamos a la nota la fecha de evaluacion
                                // }
                            }
                            $semestres_romanos->push(['numero' => $nota->semestre_materia]);
                            $semestres->push(['numero' => $nota->semestre_materia]);
                        } else { //para el caso de Paraguay
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
                                            //aqui cambiamos el nombre completo de la carrera por su abreviatura
                                            switch ($nota->carrera->nombre_fantasia) {
                                                case 'MARKETING':
                                                    $carrera->abreviado = 'MKT';
                                                    break;
                                                case 'ADMINISTRACION DE EMPRESAS':
                                                    $carrera->abreviado = 'ADM';
                                                    break;
                                                case 'NEGOCIOS INTERNACIONALES':
                                                    $carrera->abreviado = 'NEG';
                                                    break;
                                                default:
                                                    $carrera->abreviado = '';
                                                    break;
                                            }
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
                    }

                    if ($tipo == 'SIU') {
                        $this->convertirNumerosSemestresARomanos($semestres_romanos); //convertimos los numeros de los semestres a romanos con la funcion declarada al final
                    }

                    return view('certificados_estudios/show')->with(compact('alumno', 'semestres', 'semestres_romanos', 'alumno_notas', 'tipo'));
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
            $nombre_suscribe = 'Abg. Raquel Hellmann'; //el nombre del que suscribe, no es muy variable es el mismo lo generamos aqui
            $nombre_rector = 'Mg. Yan Speranza'; //el nombre del rector, ya que no es muy variable lo generamos solamentea aqui

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
            $semestres_romanos = collect(); //seteamos la coleccion de semestres para luego enviar al front
            $promedios_generales = collect(); //seteamos la coleccion de promedios generales para calcular el promedio general
            $carga_horaria_general = 0; //seteamos la carga horaria general para luego ir sumando y devolver al front
            $notas_all = $alumno->alumnoNotas; //seteamos una variable con las notas del alumno
            foreach ($alumno->alumnoNotas as $key => $nota) { //recorremos las notas del alumno
                if ($nota->carrera->programa_id != 4 && $tipo != 'SIU') { //caso para certificado de Paraguay
                    $alumno_notas = $notas_all->filter(function ($item) use ($nota, $tipo) {
                        return ($item->carrera->programa_id != 4 && $tipo != 'SIU'); //filtra y devuelve solamente las materias de Paraguay
                    });
                } else if ($nota->carrera->programa_id == 4 && $tipo == 'SIU') { //caso para certificado de SIU
                    $alumno_notas = $notas_all->filter(function ($item) use ($nota, $tipo) {
                        return ($item->carrera->programa_id == 4 && $tipo == 'SIU'); //filtra y devuelve solamente las materias SIU
                    });
                }

                $detalle = Malla::with(['mallaDetalles' => function ($query) use ($nota) { //obtenemos los detalles de la malla
                    $query->where('materia_id', $nota->materia_id);
                }])->where('carrera_id', $nota->carrera_id)
                    ->first();

                    if ($detalle->mallaDetalles->first()) {
                        $nota->semestre_materia = $detalle->mallaDetalles->first()->semestre; //agregamos el semestre de la materia a la nota para mostrar en el front   
                    }

                // if ($key == 0 && $tipo == 'SIU') {
                //     $malla = Malla::where('carrera_id', $nota->carrera_id)->first();
                //     $malla_detalles = MallaDetalle::where('malla_id', $malla->id)->orderBy('semestre')->get();
                //     $malla_detalles = $malla_detalles->groupBy('semestre');
                //     foreach ($malla_detalles as $k => $malla_detalle) {
                //         if ($nota->semestre_materia == $k) {
                //             $semestres_romanos->push(['numero' => $k]);
                //             $semestres->push(['numero' => $k]);
                //         }
                //     }
                // }
            }

            $alumno_notas = $alumno_notas->sortBy('semestre_materia');
            $notas_agrupadas = $alumno_notas->groupBy('semestre_materia');

            foreach ($notas_agrupadas as $notas) {
                if ($tipo == 'SIU') { //para el caso de SIU
                    foreach ($notas as $k => $nota) {
                        $matriculacion = Matriculacion::where('alumno_id', $nota->alumno_id)->where('semestre_id', $nota->semestre_id)->where('carrera_siu_id', $nota->carrera_id)->first(); //obtenemos la matriculacion del alumno
                        if ($matriculacion) { //si la matriculacion existe
                            $alumno_nota = AlumnoNota::where('alumno_id', $matriculacion->alumno_id)->where('carrera_id', $matriculacion->carrera_siu_id)->where('materia_id', $nota->materia_id)->where('semestre_id', $matriculacion->semestre_id)->first(); //obtenemos la nota del alumno

                            $acta = ActaEvaluacion::whereHas('alumnos', function ($query) use ($alumno) {
                                $query->where('alumno_id', $alumno->id);
                            })->where('carrera_id', $nota->carrera_id)->where('materia_id', $nota->materia_id)->where('semestre_id', $nota->semestre_id)->where('tipo', substr($nota->evaluacion, 0, 1))->first(); //obtenemos el acta de evaluacion
                            if ($acta) { //si el acta existe
                                $nota->fecha = $acta->fecha_evaluacion; //agregamos la fecha de evaluacion a la nota
                            } else {
                                $malla_paraguay = Malla::where('carrera_id', $matriculacion->carrera_id)->where('estado', 'AC')->first();
                                $malla_siu = Malla::where('carrera_id', $matriculacion->carrera_siu_id)->where('estado', 'AC')->first();

                                $malla_espejo = MallaEspejo::whereHas('mallaEspejoDetalles', function ($query) use ($nota) {
                                    $query->where('materia_siu_id', $nota->materia_id);
                                })->with(['mallaEspejoDetalles' => function ($query) use ($nota) {
                                    $query->where('materia_siu_id', $nota->materia_id);
                                }])
                                ->where('malla_paraguay_id', $malla_paraguay->id)->where('malla_siu_id', $malla_siu->id)->first();

                                $carrera = Carrera::where('id', $nota->carrera_id)->first();
                                $acta = ActaEvaluacion::whereHas('alumnos', function ($query) use ($alumno) {
                                    $query->where('alumno_id', $alumno->id);
                                })->where('carrera_id', $matriculacion->carrera_id)->where('materia_id', $malla_espejo->mallaEspejoDetalles->first()->materia_paraguay_id)->where('semestre_id', $nota->semestre_id)->first(); //obtenemos el acta de evaluacion
                                if ($acta) {
                                    $nota->fecha = $acta->fecha_evaluacion; //agregamos la fecha de evaluacion a la nota
                                }
                            }

                            // $acta_detalle = ActaEvaluacionAlumno::where('alumno_id', $nota->alumno_id)->where('calificacion', $alumno_nota->calificacion)->first(); //obtenemos el detalle del acta
                            // if ($acta_detalle) {
                            //     $acta = ActaEvaluacion::findOrFail($acta_detalle->acta_evaluacion_id); //obtenemos el acta
                            //     if ($acta) { //si el acta existe ejecuta lo siguiente
                            //         $nota->fecha = $acta->fecha_evaluacion; //agregamos a la nota la fecha de evaluacion
                            //     }
                            // }
                        }

                        // $acta = ActaEvaluacion::findOrFail($acta_detalle->acta_evaluacion_id); //obtenemos el acta
                        // if ($acta) { //si el acta existe ejecuta lo siguiente
                        //     $nota->fecha = $acta->fecha_evaluacion; //agregamos a la nota la fecha de evaluacion
                        // }
                    }
                    $semestres_romanos->push(['numero' => $nota->semestre_materia]);
                    $semestres->push(['numero' => $nota->semestre_materia]);
                } else { //se realiza lo siguiente si es para paraguay
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
            }

            if ($tipo == 'SIU') { //para el caso de certificado SIU
                $fecha_hoy = Carbon::now(); //obtenemos la fecha de hoy para el footer
                $this->convertirNumerosSemestresARomanos($semestres_romanos); //convertimos los numeros de los semestres a romanos con la funcion declarada al final
                $pdf = Pdf::loadView('certificados_estudios/pdf_siu', compact('empresa', 'fecha_hoy', 'alumno', 'alumno_notas', 'carrera', 'semestres', 'semestres_romanos'));
                $pdf->setPaper('A4');

                return $pdf->stream('certificado_estudios_' . $nota->alumno->numero_documento . '.pdf');
            } else { //caso de certificado de Paraguay
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
            }

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

            if ($tipo != 'ESPEJO') {
                $semestres = collect(); //seteamos la coleccion de semestres para luego enviar al front
                $semestres_romanos = collect(); //seteamos la coleccion de semestres para luego enviar al front
                $promedios_generales = collect(); //seteamos la coleccion de promedios generales para calcular el promedio general
                $carga_horaria_general = 0; //seteamos la carga horaria general para luego ir sumando y devolver al front
                $notas_all = $alumno->alumnoNotas; //seteamos una variable con las notas del alumno
                foreach ($alumno->alumnoNotas as $key => $nota) { //recorremos las notas del alumno
                    if ($nota->carrera->programa_id != 4 && $tipo != 'SIU') { //caso para certificado de Paraguay
                        $alumno_notas = $notas_all->filter(function ($item) use ($nota, $tipo) {
                            return ($item->carrera->programa_id != 4 && $tipo != 'SIU'); //filtra y devuelve solamente las materias de Paraguay
                        });
                    } else if ($nota->carrera->programa_id == 4 && $tipo == 'SIU') { //caso para certificado de SIU
                        $alumno_notas = $notas_all->filter(function ($item) use ($nota, $tipo) {
                            return ($item->carrera->programa_id == 4 && $tipo == 'SIU'); //filtra y devuelve solamente las materias SIU
                        });
                    }

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
                    if ($tipo == 'SIU') { //para el caso de SIU
                        foreach ($notas as $k => $nota) {
                            $matriculacion = Matriculacion::where('alumno_id', $nota->alumno_id)->where('semestre_id', $nota->semestre_id)->where('carrera_siu_id', $nota->carrera_id)->first(); //obtenemos la matriculacion del alumno
                            if ($matriculacion) { //si la matriculacion existe
                                $alumno_nota = AlumnoNota::where('alumno_id', $matriculacion->alumno_id)->where('carrera_id', $matriculacion->carrera_siu_id)->where('materia_id', $nota->materia_id)->where('semestre_id', $matriculacion->semestre_id)->first(); //obtenemos la nota del alumno

                                $acta = ActaEvaluacion::whereHas('alumnos', function ($query) use ($alumno) {
                                    $query->where('alumno_id', $alumno->id);
                                })->where('carrera_id', $nota->carrera_id)->where('materia_id', $nota->materia_id)->where('semestre_id', $nota->semestre_id)->where('tipo', substr($nota->evaluacion, 0, 1))->first(); //obtenemos el acta de evaluacion
                                if ($acta) { //si el acta existe
                                    $nota->fecha = $acta->fecha_evaluacion; //agregamos la fecha de evaluacion a la nota
                                } else {
                                    $malla_paraguay = Malla::where('carrera_id', $matriculacion->carrera_id)->where('estado', 'AC')->first();
                                    $malla_siu = Malla::where('carrera_id', $matriculacion->carrera_siu_id)->where('estado', 'AC')->first();

                                    $malla_espejo = MallaEspejo::whereHas('mallaEspejoDetalles', function ($query) use ($nota) {
                                        $query->where('materia_siu_id', $nota->materia_id);
                                    })->with(['mallaEspejoDetalles' => function ($query) use ($nota) {
                                        $query->where('materia_siu_id', $nota->materia_id);
                                    }])
                                    ->where('malla_paraguay_id', $malla_paraguay->id)->where('malla_siu_id', $malla_siu->id)->first();

                                    $carrera = Carrera::where('id', $nota->carrera_id)->first();

                                    if ($malla_espejo) {
                                        $malla_espejo_detalles = MallaEspejoDetalle::where('malla_espejo_id', $malla_espejo->id)->where('materia_siu_id', $nota->materia_id)->count();

                                        if ($malla_espejo_detalles > 0) {
                                            $acta = ActaEvaluacion::whereHas('alumnos', function ($query) use ($alumno) {
                                                $query->where('alumno_id', $alumno->id);
                                            })->where('carrera_id', $matriculacion->carrera_id)->where('materia_id', $malla_espejo->mallaEspejoDetalles->first()->materia_paraguay_id)->where('semestre_id', $nota->semestre_id)->first(); //obtenemos el acta de evaluacion

                                            if ($acta) {
                                                $nota->fecha = $acta->fecha_evaluacion; //agregamos la fecha de evaluacion a la nota
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $semestres_romanos->push(['numero' => $nota->semestre_materia]);
                        $semestres->push(['numero' => $nota->semestre_materia]);
                    } else { //se realiza lo siguiente si es para paraguay
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
                }

                if ($tipo == 'SIU') { //para el caso de certificado SIU
                    $fecha_hoy = Carbon::now(); //obtenemos la fecha de hoy para el footer
                    $this->convertirNumerosSemestresARomanos($semestres_romanos); //convertimos los numeros de los semestres a romanos con la funcion declarada al final
                    $pdf = Pdf::loadView('pantallas_alumnos/informes_academicos/pdf_siu', compact('empresa', 'fecha_hoy', 'alumno', 'alumno_notas', 'carrera', 'semestres', 'semestres_romanos'));
                    $pdf->setPaper('A4');

                    return $pdf->stream('informe_academico_' . $nota->alumno->numero_documento . '.pdf');
                } else { //caso de certificado de Paraguay
                    $promedio_general = $promedios_generales->avg(); //generamos el promedio general
                    $formatter = new NumeroALetras(); //llamamos a la funcion
                    Carbon::setLocale('es'); //seteamos nuestra fecha en español
                    $fecha_hoy = Carbon::now(); //obtenemos la fecha de hoy

                    $malla = Malla::with('mallaDetalles')->where('carrera_id', $carrera->id)->first(); //obtenemos la malla con sus detalles para comparar

                    $alumno_notas_check = AlumnoNota::where('alumno_id', $alumno->id)->where('carrera_id', $carrera->id)->where('calificacion', '>', '1')->get();

                    $pdf = Pdf::loadView('pantallas_alumnos/informes_academicos/pdf_paraguay', compact('empresa', 'alumno', 'carrera', 'semestres', 'promedio_general', 'fecha_hoy', 'alumno_notas'));
                    $pdf->setPaper('A4');

                    return $pdf->stream('informe_academico_' . $nota->alumno->numero_documento . '.pdf');
                }
            } else {
                $fecha_hoy = Carbon::now(); //obtenemos la fecha de hoy para el footer

                $matriculacion = Matriculacion::where('alumno_id', $alumno->id)->orderBy('id', 'desc')->first();
                if (!$matriculacion->carrera_siu_id) {
                    return back()->with('error-message', 'El alumno no se encuentra inscripto en SIU.');
                }

                $alumno->carrera_paraguay = $matriculacion->carrera->nombre_fantasia;
                $alumno->carrera_siu = $matriculacion->carreraSiu->nombre_fantasia;

                $malla_paraguay = Malla::with(['mallaDetalles' => function ($query) {
                    $query->orderBy('semestre', 'asc')
                        ->with(['materia' => function ($q) {
                            $q->orderBy('nombre_fantasia', 'asc');
                        }]);
                }])->where('carrera_id', $matriculacion->carrera_id)->where('estado', 'AC')->orderBy('id', 'asc')->first();
                $malla_siu = Malla::with(['mallaDetalles' => function ($query) {
                    $query->orderBy('semestre', 'asc')
                        ->with(['materia' => function ($q) {
                            $q->orderBy('nombre_fantasia', 'asc');
                        }]);
                }])->where('carrera_id', $matriculacion->carrera_siu_id)->where('estado', 'AC')->orderBy('id', 'asc')->first();

                $carrera_paraguay = $malla_paraguay->carrera;
                $cantidad_materias_paraguay = $malla_paraguay->mallaDetalles->count();

                $carrera_siu = $malla_siu->carrera;
                $cantidad_materias_siu = $malla_siu->mallaDetalles->count();

                $espejos_siu_ids = [];
                foreach ($malla_paraguay->mallaDetalles as $malla_paraguay_detalle) {
                    $espejo = MallaEspejoDetalle::whereHas('mallaEspejo', function ($query) use ($malla_paraguay, $malla_siu) {
                        $query->where('malla_paraguay_id', $malla_paraguay->id)
                            ->where('malla_siu_id', $malla_siu->id)
                            ->where('estado', 'AC');
                    })->where('materia_paraguay_id', $malla_paraguay_detalle->materia_id)->first();

                    if ($espejo) {
                        $malla_paraguay_detalle->semestre_materia_siu = $espejo->mallaEspejo->mallaSiu->mallaDetalles()->where('materia_id', $espejo->materia_siu_id)->first()->semestre;
                        $malla_paraguay_detalle->materia_siu_id = $espejo->materia_siu_id;
                        $malla_paraguay_detalle->materia_siu = $espejo->materiaSiu->nombre_fantasia;
                        $malla_paraguay_detalle->espejo = true;
                        $espejos_siu_ids[] = $espejo->materia_siu_id;
                    }
                }

                $equivalencias = collect();
                $paraguay_sin_espejo = collect();
                $siu_sin_espejo = collect();

                foreach ($malla_paraguay->mallaDetalles as $malla_detalle) {
                    $nota_paraguay = $alumno->alumnoNotas->where('materia_id', $malla_detalle->materia_id)->first();
                    $nota_siu = $alumno->alumnoNotas->where('materia_id', $malla_detalle->materia_siu_id)->first();

                    $periodo = '---';
                    if ($nota_paraguay) {
                        $periodo = $nota_paraguay->semestre->nombre;
                    }
                    if ($nota_siu) {
                        $periodo = $nota_siu->semestre->nombre;
                    }

                    $semestre_orden = $malla_detalle->semestre ? $malla_detalle->semestre : ($malla_detalle->semestre_materia_siu ? $malla_detalle->semestre_materia_siu : 999);

                    $datos = [
                        'semestre_materia_paraguay' => $malla_detalle->semestre ? $malla_detalle->semestre : '---',
                        'materia_paraguay' => $malla_detalle->materia->nombre_real ? $malla_detalle->materia->nombre_real : '---',
                        'nota_paraguay' => $nota_paraguay ? $nota_paraguay->calificacion : '---',
                        'semestre_materia_siu' => $malla_detalle->semestre_materia_siu ? $malla_detalle->semestre_materia_siu : '---',
                        'materia_siu' => $malla_detalle->materia_siu ? $malla_detalle->materia_siu : '---',
                        'nota_siu' => $nota_siu ? $nota_siu->calificacion : '---',
                        'periodo' => $periodo,
                        'estado' => $malla_detalle->espejo,
                        'semestre_orden' => $semestre_orden
                    ];

                    if ($malla_detalle->espejo) {
                        $equivalencias->push($datos);
                    } else {
                        $paraguay_sin_espejo->push($datos);
                    }
                }

                foreach ($malla_siu->mallaDetalles as $malla_siu_detalle) {
                    if (!in_array($malla_siu_detalle->materia_id, $espejos_siu_ids)) {
                        $nota_siu = $alumno->alumnoNotas->where('materia_id', $malla_siu_detalle->materia_id)->first();
                        $periodo = $nota_siu ? $nota_siu->semestre->nombre : '---';
                        $semestre_orden = $malla_siu_detalle->semestre ? $malla_siu_detalle->semestre : 999;

                        $datos = [
                            'semestre_materia_paraguay' => '---',
                            'materia_paraguay' => '---',
                            'nota_paraguay' => '---',
                            'semestre_materia_siu' => $malla_siu_detalle->semestre ? $malla_siu_detalle->semestre : '---',
                            'materia_siu' => $malla_siu_detalle->materia->nombre_fantasia ? $malla_siu_detalle->materia->nombre_fantasia : '---',
                            'nota_siu' => $nota_siu ? $nota_siu->calificacion : '---',
                            'periodo' => $periodo,
                            'estado' => false,
                            'semestre_orden' => $semestre_orden
                        ];

                        $siu_sin_espejo->push($datos);
                    }
                }

                // Combinamos equivalencias primero
                $espejos = $equivalencias;

                // Emparejamos Paraguay sin espejo con SIU sin espejo
                $max_sin_espejo = max($paraguay_sin_espejo->count(), $siu_sin_espejo->count());
                for ($i = 0; $i < $max_sin_espejo; $i++) {
                    $p = $paraguay_sin_espejo->get($i, [
                        'semestre_materia_paraguay' => '---',
                        'materia_paraguay' => '---',
                        'nota_paraguay' => '---',
                        'semestre_orden' => 999
                    ]);
                    $s = $siu_sin_espejo->get($i, [
                        'semestre_materia_siu' => '---',
                        'materia_siu' => '---',
                        'nota_siu' => '---',
                        'periodo' => '---',
                        'semestre_orden' => 999
                    ]);

                    $periodo_combinado = $p['periodo'] ?? '---';
                    if ($s['periodo'] !== '---') {
                        $periodo_combinado = $s['periodo'];
                    }

                    $datos = [
                        'semestre_materia_paraguay' => $p['semestre_materia_paraguay'],
                        'materia_paraguay' => $p['materia_paraguay'],
                        'nota_paraguay' => $p['nota_paraguay'],
                        'semestre_materia_siu' => $s['semestre_materia_siu'],
                        'materia_siu' => $s['materia_siu'],
                        'nota_siu' => $s['nota_siu'],
                        'periodo' => $periodo_combinado,
                        'estado' => false,
                        'semestre_orden' => min($p['semestre_orden'], $s['semestre_orden'])
                    ];

                    $espejos->push($datos);
                }

                //Ordenamos por semestre
                $espejos = $espejos->sortBy('semestre_orden')->values();

                $pdf = Pdf::loadView('pantallas_alumnos/informes_academicos/pdf_espejo', compact('empresa', 'alumno', 'carrera_paraguay', 'carrera_siu', 'fecha_hoy', 'espejos', 'cantidad_materias_paraguay', 'cantidad_materias_siu'));
                $pdf->setPaper('A4');

                return $pdf->stream('informe_academico_' . $alumno->numero_documento . '.pdf');
            }

        // } catch (\Exception $e) {
        //     return redirect()->route('pantallas_alumnos.index', $id)->with('error-message', 'Ocurrió un error al generar el informe académico. Si el error persiste, contacte al administrador.');
        // }
    }

    //funcion para convertir numeros enteros a romandos
    private function numerosRomanos($number)
    {
        //seteamos las comparaciones
        $map = [
            'M'  => 1000,
            'CM' => 900,
            'D'  => 500,
            'CD' => 400,
            'C'  => 100,
            'XC' => 90,
            'L'  => 50,
            'XL' => 40,
            'X'  => 10,
            'IX' => 9,
            'V'  => 5,
            'IV' => 4,
            'I'  => 1
        ];

        $result = ''; //seteamos el resultado en vacio
        foreach ($map as $roman => $value) {
            // Determinar el numero de veces que el valor puede caber en el numeor
            $matches = intval($number / $value);
            // Agregamos el numero romano correspondiente
            $result .= str_repeat($roman, $matches);
            // Disminuimos el numero por el valor decimal equivalente
            $number = $number % $value;
        }

        return $result;
    }

    //funcion para convertir los numeros de semestres en el collection semestres
    private function convertirNumerosSemestresARomanos(&$semestres)
    {
        $semestres = $semestres->map(function($semestre) {
            $semestre['numero'] = $this->numerosRomanos($semestre['numero']); //convertimos el numero a decimal 1 a 1
            return $semestre;
        });
    }
}
