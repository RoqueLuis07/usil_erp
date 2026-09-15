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
use App\Models\Curso;
use App\Models\CursoModulo;
use App\Models\ActaEvaluacionUbs;
use App\Models\ActaEvaluacionAlumnoUbs;
use App\Models\Modulo;
use App\Models\InscripcionUbs;
use App\Models\InscripcionModulo;
use App\Models\AlumnoNotaUbs;
use App\Models\Empresa;
use App\Models\InscripcionTemaTesisUbs;

class CertificadoEstudioUbsController extends Controller
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

    public function show($maestria, $alumno_id)
    {
        $this->authorize('ver_certificados_maestrias_ubs');

        try {
            $alumno = Alumno::with(['notasUbs' => function ($query) use ($maestria) {
                $query->where('curso_id', $maestria);
            }])->findOrFail($alumno_id);

            $semestres = collect(); //creamos la coleccion de semestre para luego retornar al front
            $promedios = collect(); //creamos la coleccion de promedios para luego retornar al front
            $total_carga_horaria = 0; //seteamos la carga horaria por semestre en 0
            $alumno_notas = $alumno->notasUbs; //seteamos una nueva variable con alumnoNotas
            if ($alumno_notas->count() > 0) { //verificamos que existan notas del alumno, si existe hace lo siguiente
                foreach ($alumno_notas as $key => $nota) {
                    //agregamos el semestre del modulo a la nota para mostrar en el front
                    $nota->semestre_modulo = CursoModulo::where('curso_id', $nota->curso_id)->where('modulo_id', $nota->modulo_id)->first()->semestre;
                }

                $notas_agrupadas = $alumno_notas->groupBy('semestre_modulo');
                foreach ($notas_agrupadas as $notas) {
                    $promedios = collect(); //seteamos la coleccion de promedios para calcular los promedios por semestre
                    $total_carga_horaria = 0;

                    foreach ($notas as $nota) {
                        $promedios->push($nota->calificacion);
                        $total_carga_horaria = $total_carga_horaria + $nota->modulo->carga_horaria; //hacemos la suma de cada carga horaria para tener el total por semestre

                        $formatter = new NumeroALetras(); //llamamos a la funcion
                        $nota->calificacion_letras = Str::lower($formatter->toWords($nota->calificacion, 0)); //convertimos la calificacion a letras para el texto

                        //como se guarda el nombre completo de la evaluacion en la nota, lo que hacemos es obtener la primera letra para obtener el acta
                        $tipo_evaluacion = substr($nota->evaluacion, 0, 1); //el primer numero es cuantos espacios queres eliminar y el segundo numero cuanto queres dejar
                        $acta = ActaEvaluacionUbs::where('curso_id', $nota->curso_id)->where('modulo_id', $nota->modulo_id)->where('tipo', $tipo_evaluacion)->first(); //obtenemos el acta
                        if ($acta) { //si el acta existe
                                $curso = Curso::findOrFail($nota->curso_id); //obtenemos el curso
                                //aqui cambiamos el nombre completo de la carrera por su abreviatura
                                $nota->numero_acta = $acta->numero_acta;
                                $nota->fecha = $acta->fecha_evaluacion; //obtenemos la fecha de evaluacion
                        }
                    }
                    //creamos un array para luego insertar al collect de semestre
                    $datos_semestre = ['numero' => $nota->semestre_modulo,
                    'promedio' => $promedios->avg(),
                    'carga_horaria' => $total_carga_horaria];
                    $semestres->push($datos_semestre); //insertamos en semestres el array generado
                }

                //como se guarda el nombre completo de la evaluacion en la nota, lo que hacemos es obtener la primera letra para obtener el acta
                $tipo_evaluacion = substr($nota->evaluacion, 0, 1); //el primer numero es cuantos espacios queres eliminar y el segundo numero cuanto queres dejar

                $maestria_id = $maestria;

                $tesis = InscripcionTemaTesisUbs::where('alumno_id', $alumno_id)->where('curso_id', $maestria)->where('estado', 'EN')->orWhere('estado', 'RE')->first();

                return view('ubs/maestrias/certificados_estudios/show')->with(compact('alumno', 'semestres', 'alumno_notas', 'maestria_id', 'tesis'));
            } else { //si no existen notas del alumno ejecuta lo siguiente
                return redirect()->route('maestrias.index')->with('error-message', 'No se puede visualizar el certificado de estudios. El alumno ' . $alumno->primer_nombre . ' ' . $alumno->primer_apellido . ' no cuenta con módulos aprobados.');
            }
        } catch (\Exception $e) {
            return redirect()->route('maestrias.index')->with('error-message', $e->getMessage());
        }
    }

    public function generar($maestria, $alumno_id)
    {
        $this->authorize('generar_certificados_maestrias_ubs');

        try {
            $empresa = Empresa::first(); //obtenemos los datos de la empresa para el header
            $alumno = Alumno::with(['solicitudes' => function ($query) {
                $query->where('tipo_solicitud_id', '1')
                    ->where('estado', 'AP');
            }])->findOrFail($alumno_id);

            // if ($alumno->solicitudes->count() == 0) {
            //     return back()->with('error-message', 'El certificado de estudios no puede ser generado. No existen solicitudes pendientes del alumno.');
            // }

            // foreach ($alumno->solicitudes as $solicitud) {
            //     if ($solicitud->pagoSolicitud->saldo != 0) {
            //         return back()->with('error-message', 'El certificado de estudios no puede ser generado. El alumno no realizó el pago.');
            //     }
            //     $solicitud->estado = 'GE';
            //     $solicitud->save();
            // }

            $alumno = Alumno::with(['notasUbs' => function ($query) use ($maestria) {
                $query->where('curso_id', $maestria);
            }])->findOrFail($alumno_id);

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
            $promedios_generales = collect(); //seteamos la coleccion de promedios generales para calcular el promedio general
            $carga_horaria_general = 0; //seteamos la carga horaria general para luego ir sumando y devolver al front
            $alumno_notas = $alumno->notasUbs; //seteamos una nueva variable con alumnoNotas
            if ($alumno_notas->count() > 0) { //verificamos que existan notas del alumno, si existe hace lo siguiente
                foreach ($alumno_notas as $key => $nota) {
                    //agregamos el semestre del modulo a la nota para mostrar en el front
                    $nota->semestre_modulo = CursoModulo::where('curso_id', $nota->curso_id)->where('modulo_id', $nota->modulo_id)->first()->semestre;
                }
            }

            $notas_agrupadas = $alumno_notas->groupBy('semestre_modulo');

            foreach ($notas_agrupadas as $key => $notas) {
                $promedios = collect(); //seteamos la coleccion de promedios para calcular los promedios por semestre
                $total_carga_horaria = 0; //seteamos el total de carga horaria por semestre para luego calular

                foreach ($notas as $k => $nota) {
                    $promedios->push($nota->calificacion);
                    $promedios_generales->push($nota->calificacion); //insertamos la calificacion a promedios generales para luego calcular
                    $total_carga_horaria = $total_carga_horaria + $nota->modulo->carga_horaria; //hacemos la suma de cada carga horaria para tener el total por semestre

                    $formatter = new NumeroALetras(); //llamamos a la funcion
                    $nota->calificacion_letras = Str::lower($formatter->toWords($nota->calificacion, 0)); //convertimos la calificacion a letras para el texto

                    //como se guarda el nombre completo de la evaluacion en la nota, lo que hacemos es obtener la primera letra para obtener el acta
                    $tipo_evaluacion = substr($nota->evaluacion, 0, 1); //el primer numero es cuantos espacios queres eliminar y el segundo numero cuanto queres dejar
                    $acta = ActaEvaluacionUbs::where('curso_id', $nota->curso_id)->where('modulo_id', $nota->modulo_id)->where('tipo', $tipo_evaluacion)->first(); //obtenemos el acta
                    if ($acta) { //si el acta existe
                            $curso = Curso::findOrFail($nota->curso_id); //obtenemos el curso
                            $nota->numero_acta = $acta->numero_acta;
                            $nota->fecha = $acta->fecha_evaluacion; //obtenemos la fecha de evaluacion
                    }
                }
                //creamos un array para luego insertar al collect de semestre
                $datos_semestre = ['numero' => $nota->semestre_modulo,
                'promedio' => $promedios->avg(),
                'carga_horaria' => $total_carga_horaria];
                $semestres->push($datos_semestre); //insertamos en semestres el array generado

                $carga_horaria_general = $carga_horaria_general + $total_carga_horaria; //sumamos las cargas horarias del semestre a la carga horaria general
            }

            $promedio_general = $promedios_generales->avg(); //generamos el promedio general
            $formatter = new NumeroALetras(); //llamamos a la funcion
            $promedio_general_letras = Str::lower($formatter->toWords($promedio_general, 2)); //convertimos el promedio general en letras para el texto
            $carga_horaria_general_letras = Str::lower($formatter->toWords($carga_horaria_general, 0)); //convertimos la carga horaria general en letras para el texto
            if (str_contains($promedio_general_letras, 'con')) { //si el promedio tiene decimales hacemos lo siguiente
                $promedio_general_letras = $promedio_general_letras . ' centésimas'; //concatenamos la palabra centesimas al promedio general en letras para obtener el formato necesitado
            }
            Carbon::setLocale('es'); //seteamos nuestra fecha en español
            $fecha_hoy = Carbon::now(); //obtenemos la fecha de hoy

            $curso = Curso::with('modulos')->findOrFail($curso->id); //obtenemos el curso con sus modulos para comparar
            $curso->titulo_alumno = str_replace('MAESTRIA', 'MÁGISTER', $curso->nombre_real);
            $curso->duracion = 2; //obtenemos la duracion de la carrera en años
            $curso->terminado = false; //seteamos que el curso no esta terminado

            $tesis = InscripcionTemaTesisUbs::where('alumno_id', $alumno_id)->where('curso_id', $maestria)->where('estado', 'EN')->first();
            $tesis->calificacion_letras = Str::lower($formatter->toWords($tesis->calificacion, 2)); //convertimos la calificacion en letras para el texto
            $tesis->fecha_defensa = Carbon::parse($tesis->fechaDefensa->fecha);

            if ($curso->modulos->count() == $alumno_notas->count()) { //si el curso se encuentra terminado hace lo siguiente
                if ($tesis) {
                    $curso->terminado = true; //seteamos que la carrera esta terminada
                }
            }

            $pdf = Pdf::loadView('ubs/maestrias/certificados_estudios/pdf', compact('empresa', 'nombre_suscribe', 'nombre_rector', 'alumno', 'curso', 'semestres', 'promedio_general', 'promedio_general_letras', 'carga_horaria_general', 'carga_horaria_general_letras', 'fecha_hoy', 'alumno_notas', 'tesis'));
            $pdf->setPaper('A4');

            return $pdf->stream('certificado_estudios_' . $nota->alumno->numero_documento . '.pdf');

        } catch (\Exception $e) {
            return redirect()->route('certificados_estudios_ubs.show', ['maestria' => $maestria, 'alumno' => $alumno_id])->with('error-message', $e->getMessage());
        }
    }

    public function get_alumnos($id)
    {
        $this->authorize('ver_certificados_maestrias_ubs');

        try {
            $inscripciones = InscripcionUbs::with('alumno')->where('curso_id', $id)->get();

            return response()->json([
                'inscripciones' => $inscripciones,
            ]);

        } catch (\Exception $e) {
            return redirect()->route('maestrias.index')->with('error-message', $e->getMessage());
        }
    }
}
