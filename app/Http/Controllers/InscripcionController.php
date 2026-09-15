<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\Empresa;
use App\Models\Inscripcion;
use App\Models\Matriculacion;
use App\Models\PagoMatriculacion;
use App\Models\Alumno;
use App\Models\Semestre;
use App\Models\SemestreMalla;
use App\Models\SemestreMallaMateria;
use App\Models\SemestreMallaMateriaHorario;
use App\Models\InscripcionSemestreMallaMateria;
use App\Models\Malla;
use App\Models\MallaDetalle;
use App\Models\Materia;
use App\Models\DiaSemana;
use App\Models\FechaDesmatriculacion;
use App\Models\AnulacionCorrelatividad;

class InscripcionController extends Controller
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

    public function show($id)
    {
        $this->authorize('ver_inscripciones_matriculaciones');

        try {
            //obtener las matriculaciones con las inscripciones ordenadas
            $matriculacion = Matriculacion::with(['inscripciones' => function ($query) {
                $query->orderBy('id');
            }])->findOrFail($id);
            // $matriculacion = Matriculacion::with(['inscripciones' => function ($query) {
            //     $query->where('estado', '!=', 'CO')
            //         ->orderBy('id');
            // }])->findOrFail($id);

            $malla = Malla::where('carrera_id', $matriculacion->carrera_id)->where('estado', 'AC')->first();
            $semestre_malla = SemestreMalla::where('semestre_id', $matriculacion->semestre_id)->where('malla_id', $malla->id)->where('estado', 'AC')->first();
            $semestre_malla_materias = SemestreMallaMateria::where('semestre_malla_id', $semestre_malla->id)->where('estado', 'AC')->get();

            $pago_matriculacion = PagoMatriculacion::where('matriculacion_id', $id)
                ->whereIn('tipo', ['MA', '1C'])
                ->where('estado', 'CA')
                ->get();
            if (!$pago_matriculacion) {
                return redirect('matriculaciones')->with('error', 'El alumno debe estar al día con sus pagos antes de realizar la inscripción a materias.');
            }
            return view('matriculaciones/inscripciones/show')->with(compact('matriculacion'));
        } catch (\Exception $e) {
            return redirect()->route('matriculaciones.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_inscripciones_matriculaciones');

        // try {
            $matriculacion = Matriculacion::with(['inscripciones' => function ($query) {
                // Ordenar por 'MA' (Activa) primero, luego por el estado normal
                $query->orderByRaw("CASE
                    WHEN estado = 'MA' THEN 1
                    ELSE 0
                END, estado");
            }])->findOrFail($id);
            
            // Verificación de Semestre
            if ($matriculacion->semestre->estado == 'IN') {
                return back()->with('error-message', 'Las inscripciones no puede realizarse. El semestre se encuentra cerrado.');
            }
            
            // Obtener datos necesarios
            $alumno = Alumno::findOrFail($matriculacion->alumno_id);
            $malla = Malla::where('carrera_id', $matriculacion->carrera_id)->where('estado', 'AC')->first();
            
            // Verificaciones de Malla
            if (!$malla) {
                return back()->with('error-message', 'No se encontró la Malla activa para esta carrera.');
            }
            $semestre_malla = SemestreMalla::where('semestre_id', $matriculacion->semestre_id)->where('malla_id', $malla->id)->where('estado', 'AC')->first();
            if (!$semestre_malla) {
                return back()->with('error-message', 'No se encontró el Semestre-Malla activo.');
            }
            
            $semestre_malla_materias = SemestreMallaMateria::with('horarios')->where('semestre_malla_id', $semestre_malla->id)->where('estado', 'AC')->get();
            
            $semestre_malla_materias_ambos = collect();
            foreach ($semestre_malla_materias as $smm) {
                $semestre_malla_materias_ambos->push($smm);
            }
        
            // Lógica para añadir materias de Mallas SIU auxiliares/secundarias
            if ($matriculacion->carrera_siu_id) {
                $malla_siu = Malla::where('carrera_id', $matriculacion->carrera_siu_id)->where('estado', 'AC')->first();
                if ($malla_siu) {
                    $semestre_malla_siu = SemestreMalla::where('semestre_id', $matriculacion->semestre_id)->where('malla_id', $malla_siu->id)->where('estado', 'AC')->first();
                    if ($semestre_malla_siu) {
                        $semestre_malla_materias_siu = SemestreMallaMateria::with('horarios')->where('semestre_malla_id', $semestre_malla_siu->id)->where('estado', 'AC')->get();
                        foreach ($semestre_malla_materias_siu as $smms) {
                            $semestre_malla_materias_ambos->push($smms);
                        }
                    }
                }
            } else {
                $malla_auxiliar_siu = Malla::whereHas('carrera', function ($query) {
                    $query->where('nombre_real', 'LIKE', '%SIU AUXILIAR%');
                })->first();
                if ($malla_auxiliar_siu) {
                    $semestre_malla_siu = SemestreMalla::where('semestre_id', $matriculacion->semestre_id)->where('malla_id', $malla_auxiliar_siu->id)->where('estado', 'AC')->first();
                    if ($semestre_malla_siu) {
                        $semestre_malla_materias_siu = SemestreMallaMateria::with('horarios')->where('semestre_malla_id', $semestre_malla_siu->id)->where('estado', 'AC')->get();
                        foreach ($semestre_malla_materias_siu as $smms) {
                            $semestre_malla_materias_ambos->push($smms);
                        }
                    }
                }
            }
        
            $semestre = Semestre::where('estado', 'AC')->orderBy('id', 'desc')->first();
            if (!$semestre) {
                return back()->with('error-message', 'No se encontró ningún semestre activo actualmente.');
            }
            $anulacion_correlatividad = AnulacionCorrelatividad::where('alumno_id', $alumno->id)->where('estado', 'AC')->where('semestre_id', $semestre->id)->first();
            
            // =======================================================================
            // FUNCIÓN DE VERIFICACIÓN DE SOLAPAMIENTO (CORREGIDA)
            // Se corrigió la asignación de $inicio_2 y $min_inicio_2.
            // =======================================================================
            $verificar_solapamiento = function ($smm1, $smm2) {
                // Si alguna materia no tiene horarios cargados, no hay solapamiento
                if ($smm1->horarios->isEmpty() || $smm2->horarios->isEmpty()) {
                    return false;
                }
        
                foreach ($smm1->horarios as $h1) {
                    foreach ($smm2->horarios as $h2) {
                        if ($h1->dia_semana_id == $h2->dia_semana_id) {
                            
                            // Horario 1
                            $inicio_1 = Carbon::parse($h1->hora_inicio);
                            $fin_1    = Carbon::parse($h1->hora_fin);
                            
                            // Horario 2 (CORRECCIÓN APLICADA AQUÍ)
                            $inicio_2 = Carbon::parse($h2->hora_inicio); // Usar $inicio_2
                            $fin_2    = Carbon::parse($h2->hora_fin);
            
                            // Convertimos todo a minutos del día para comparación
                            $min_inicio_1 = $inicio_1->hour * 60 + $inicio_1->minute;
                            $min_fin_1    = $fin_1->hour * 60 + $fin_1->minute;
                            
                            $min_inicio_2 = $inicio_2->hour * 60 + $inicio_2->minute; // Usar $inicio_2
                            $min_fin_2    = $fin_2->hour * 60 + $fin_2->minute;
            
                            // Logica solapamiento: InicioA < FinB y InicioB < FinA
                            if ($min_inicio_1 < $min_fin_2 && $min_inicio_2 < $min_fin_1) {
                                return true; 
                            }
                        }
                    }
                }
                return false;
            };
            
            // 2. Aplicar filtros de correlatividad y aprobación
            $materias = collect(); 
            
            foreach ($semestre_malla_materias_ambos as $semestre_malla_materia) {
                $materia = $semestre_malla_materia->materia;
                $agregar = false;
        
                if ($anulacion_correlatividad) {
                    $agregar = true; // Si tiene anulación, entra directo
                } else {
                    // Excluir TFG
                    if (!str_contains($materia->nombre_real, 'TRABAJO FINAL DE GRADO') || !str_contains($materia->nombre_fantasia, 'TRABAJO FINAL DE GRADO')) {
                        
                        // Si NO tiene correlativas
                        if (!$materia->correlativas()->exists()) {
                            if (!$materia->estaAprobadaConvalidadaEspejoSuficiencia($alumno->id)) {
                                $agregar = true;
                            }
                        } else {
                            // SI tiene correlativas
                            $correlativasAprobadas = false;
                            foreach ($materia->correlativas as $correlativa) {
                                if ($correlativa->estaAprobadaConvalidadaEspejoSuficiencia($alumno->id)) {
                                    $correlativasAprobadas = true;
                                    break;
                                }
                            }
                            // Si cumple correlativas y NO aprobada
                            if ($correlativasAprobadas && !$materia->estaAprobadaConvalidadaEspejoSuficiencia($alumno->id)) {
                                // Verificamos que no esté duplicada 
                                if (!$materias->contains('id', $semestre_malla_materia->id)) {
                                    $agregar = true;
                                }
                            }
                        }
                    }
                }
        
                if ($agregar) {
                    $semestre_malla_materia->tiene_conflicto = false;
                    $semestre_malla_materia->conflicto = '';
                    $semestre_malla_materia->semestre_materia = '';
                    $materias->push($semestre_malla_materia);
                }
            }
            
            // 3. Obtener Semestre de la Materia y Verificar Solapamiento (Contra toda la lista)
            $materias = $materias->map(function($materia_actual) use ($materias, $verificar_solapamiento, $malla) {
                $malla_detalle_actual = MallaDetalle::where('malla_id', $malla->id)->where('materia_id', $materia_actual->materia_id)->first();
                if ($malla_detalle_actual) {
                    $materia_actual->semestre_materia = $malla_detalle_actual->semestre;
                } else {
                    // Asignar un valor alto si no se encuentra para que se ordene al final o sea obvio.
                    $materia_actual->semestre_materia = 99; 
                }
        
                foreach ($materias as $materia) {
                    if ($materia_actual->id !== $materia->id) {
                        if ($verificar_solapamiento($materia_actual, $materia)) {
                            $materia_actual->tiene_conflicto = true;
                            
                            // Acumular la lista de conflictos
                            if (empty($materia_actual->conflicto)) {
                                $materia_actual->conflicto = $materia->materia->nombre_fantasia;
                            } else {
                                if (!str_contains($materia_actual->conflicto, $materia->materia->nombre_fantasia)) {
                                    $materia_actual->conflicto .= ', ' . $materia->materia->nombre_fantasia;
                                }
                            }
                        }
                    }
                }
                return $materia_actual;
            });
            
            // Ordenar por semestre
            $materias = $materias->sortBy(function($item) {
                return $item->semestre_materia;
            });
        
            // =======================================================================
            // LÓGICA DE PRE-CARGA EDITABLE (Lógica basada en semestre siguiente/mínimo)
            // =======================================================================
        
            // 1. Verificar si el alumno ya tiene inscripciones activas ('MA')
            $inscripciones_reales = $matriculacion->inscripciones->filter(function($i) {
                return $i->estado == 'MA';
            });
            
            // Si NO tiene inscripciones reales activas, preparamos las sugeridas
            if ($inscripciones_reales->isEmpty()) {
                
                $semestre_sugerido = null; 
        
                // Buscar la penúltima Matriculación (historial)
                $ultima_matriculacion_cursada = Matriculacion::where('alumno_id', $alumno->id)
                    ->where('id', '!=', $matriculacion->id) 
                    ->whereIn('estado', ['CO', 'AP']) 
                    ->orderBy('id', 'desc')
                    ->first();
        
                if ($ultima_matriculacion_cursada) {
                    // Hay historial: Calcular semestre siguiente.
                    $inscripciones_anteriores = $ultima_matriculacion_cursada->inscripciones
                        ->where('estado', 'MA'); 
        
                    $semestres_cursados = $inscripciones_anteriores->map(function ($inscripcion) use ($malla) {
                        $malla_detalle = MallaDetalle::where('malla_id', $malla->id)->where('materia_id', $inscripcion->materia_id)->first();
                        return $malla_detalle ? $malla_detalle->semestre : null;
                    })->filter()->unique();
        
                    $max_semestre_cursado = $semestres_cursados->max();
                    if ($max_semestre_cursado) {
                        $semestre_sugerido = $max_semestre_cursado + 1;
                    }
                } 
                
                // --- LÓGICA DE SELECCIÓN FINAL DEL SEMESTRE ---
                
                // Obtener el semestre mínimo disponible en la colección $materias (ya filtrada por correlativas)
                $min_semestre_disponible = $materias->min('semestre_materia');
                
                // Regla: Si no hay historial (is_null) O el semestre sugerido no es viable/válido, usar el mínimo disponible.
                if (is_null($semestre_sugerido) || $semestre_sugerido < $min_semestre_disponible || $materias->where('semestre_materia', $semestre_sugerido)->isEmpty()) {
                     // Si no hay matrícula previa, o no hay materias disponibles en el semestre siguiente, 
                     // usamos el semestre más bajo disponible.
                     $semestre_sugerido = $min_semestre_disponible;
                }
        
                // 2. Filtrar todas las materias que coincidan con el $semestre_sugerido
                $materias_sugeridas_base = $materias->filter(function ($smm) use ($semestre_sugerido) {
                    return $smm->semestre_materia == $semestre_sugerido;
                });
                
                // 3. Aplicar la verificación de Solapamiento ÚNICAMENTE a las materias sugeridas
                $materias_sugeridas_final = collect();
                
                foreach ($materias_sugeridas_base as $materia_actual) {
                    $solapamiento_encontrado = false;
                    
                    // Comparar la materia actual con todas las demás materias SUGERIDAS
                    foreach ($materias_sugeridas_base as $materia_comparar) {
                        // Si la materia actual se solapa con OTRA materia sugerida, se descarta.
                        if ($materia_actual->id !== $materia_comparar->id) {
                            if ($verificar_solapamiento($materia_actual, $materia_comparar)) {
                                $solapamiento_encontrado = true;
                                break;
                            }
                        }
                    }
                    
                    // Agregar SOLAMENTE si NO se encontró solapamiento con OTRA materia sugerida.
                    if (!$solapamiento_encontrado) {
                        $materias_sugeridas_final->push($materia_actual);
                    }
                }
        
                // 4. Crear la colección de objetos "falsos" (simulando inscripciones)
                $inscripciones_preliminares = collect();
        
                foreach($materias_sugeridas_final as $smm) { 
                    $fake_detalle = new \stdClass();
                    $fake_detalle->id = null; 
                    $fake_detalle->materia_id = $smm->materia->id;
                    $fake_detalle->estado = 'MA'; 
                    $fake_detalle->materia = $smm->materia;
                    $fake_detalle->is_suggested = true; 
                    
                    $inscripciones_preliminares->push($fake_detalle);
                }
        
                // 5. Inyectar la colección "falsa" en la relación $matriculacion->inscripciones
                $matriculacion->setRelation('inscripciones', $inscripciones_preliminares);
            } 
            
            return view('matriculaciones/inscripciones/edit')->with(compact('matriculacion', 'materias'));

        // } catch (\Exception $e) {
        //     return redirect()->route('matriculaciones.index')->with('error-message', $e->getMessage());
        // }
        
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_inscripciones_matriculaciones');

        $request->validate([
            'detalles' => ['nullable', 'array'],
            'detalles.*.materia' => ['nullable', 'numeric']
        ]);

        DB::beginTransaction();

        try {
            $matriculacion = Matriculacion::findOrFail($id);
            if ($matriculacion->semestre->estado == 'IN') {
                return back()->with('error-message', 'Las inscripciones no pueden realizarse. El semestre se encuentra cerrado.');
            }
            $malla = Malla::where('carrera_id', $matriculacion->carrera_id)->first();
            $semestre_malla = SemestreMalla::where('semestre_id', $matriculacion->semestre_id)->where('malla_id', $malla->id)->first();
            $semestre_malla_materias = SemestreMallaMateria::where('semestre_malla_id', $semestre_malla->id)->where('estado', 'AC')->get();
            $semestre_malla_materias_ambos = collect();
            foreach ($semestre_malla_materias as $smm) {
                $semestre_malla_materias_ambos->push($smm);
            }
            if ($matriculacion->carrera_siu_id) {
                $malla_siu = Malla::where('carrera_id', $matriculacion->carrera_siu_id)->where('estado', 'AC')->first();
            } else {
                $malla_auxiliar_siu = Malla::whereHas('carrera', function ($query) {
                    $query->where('nombre_real', 'LIKE', '%SIU AUXILIAR%');
                })->first();
                if ($malla_auxiliar_siu) {
                    $semestre_malla_siu = SemestreMalla::where('semestre_id', $matriculacion->semestre_id)->where('malla_id', $malla_auxiliar_siu->id)->where('estado', 'AC')->first();
                    if ($semestre_malla_siu) {
                        $semestre_malla_materias_siu = SemestreMallaMateria::where('semestre_malla_id', $semestre_malla_siu->id)->where('estado', 'AC')->get();
                        foreach ($semestre_malla_materias_siu as $smms) {
                            $semestre_malla_materias_ambos->push($smms);
                        }
                    }
                }
            }

            $materias_obtenidas = collect(); //creamos coleccion para comprobar la existencia
            if ($request->detalles) {
                foreach ($request->detalles as $detalles) {
                    foreach ($semestre_malla_materias_ambos as $semestre_malla_materia) {
                        if ($detalles['materia'] == $semestre_malla_materia->materia_id) {
                            $materias_obtenidas->push($semestre_malla_materia); //insertamos a la coleccion si ya existe
                        }
                    }
                }
            }

            //ordenamos la coleccion obtenida para poder comparar
            $materias_ordenadas = $materias_obtenidas->sortBy(function ($materia) {
                return $materia['materia_id'];
            });

            //traemos solamente las inscripciones con estado matriculado o en curso para hacer la verificacion de borrado
            $inscripciones = Inscripcion::where('matriculacion_id', $matriculacion->id)->whereIn('estado', ['MA'])->orderBy('materia_id', 'asc')->get();
            $materiasInscriptas = $inscripciones->pluck('materia_id');
            if ($inscripciones->count() != 0) { //si ya tiene inscripciones hace lo siguiente
                foreach ($inscripciones as $i) {
                    foreach ($materias_ordenadas as $materia) {
                        $existeRelacion = InscripcionSemestreMallaMateria::where('inscripcion_id', $i->id)
                            ->where('semestre_malla_materia_id', $materia->id)
                            ->exists(); //comprobamos si ya existe, para no duplicar

                        if (!$existeRelacion) { //si no existe en la relacion
                            if (!$materiasInscriptas->contains($materia->materia_id)) { //doble verificacion, en el caso de que agreguemos una mas
                                $inscripcion = new Inscripcion();
                                $inscripcion->fecha = Carbon::now();
                                $inscripcion->matriculacion_id = $matriculacion->id;
                                $inscripcion->materia_id = $materia->materia_id;
                                $inscripcion->docente_id = $materia->docente_id;
                                $inscripcion->estado = 'MA';
                                $inscripcion->cargado_por_id = Auth::id();
                                $inscripcion->alumno_id = $matriculacion->alumno_id;
                                $inscripcion->save();

                                $inscripcion_semestre_malla_materia = new InscripcionSemestreMallaMateria();
                                $inscripcion_semestre_malla_materia->inscripcion_id = $inscripcion->id;
                                $inscripcion_semestre_malla_materia->semestre_malla_materia_id = $materia->id;
                                $inscripcion_semestre_malla_materia->save();

                                $materiasInscriptas->push($materia->materia_id); //insertamos la materia recien agregada a materiasInscriptas, para que la siguiente vez ya no agregue
                            }
                        }
                    }
                }
            } else { //si no tiene inscripciones hace lo siguiente

                foreach ($materias_ordenadas as $materia) {
                    $inscripcion = new Inscripcion();
                    $inscripcion->fecha = Carbon::now();
                    $inscripcion->matriculacion_id = $matriculacion->id;
                    $inscripcion->materia_id = $materia->materia_id;
                    $inscripcion->docente_id = $materia->docente_id;
                    $inscripcion->estado = 'MA';
                    $inscripcion->cargado_por_id = Auth::id();
                    $inscripcion->alumno_id = $matriculacion->alumno_id;
                    $inscripcion->save();

                    $inscripcion_semestre_malla_materia = new InscripcionSemestreMallaMateria();
                    $inscripcion_semestre_malla_materia->inscripcion_id = $inscripcion->id;
                    $inscripcion_semestre_malla_materia->semestre_malla_materia_id = $materia->id;
                    $inscripcion_semestre_malla_materia->save();
                }
            }

            $inscripcionesMateriaIds = $inscripciones->pluck('materia_id')->unique(); //agregamos los ids encontrados en todas las inscripciones para realizar el borrado

            $commonMaterias = collect();
            foreach ($materias_obtenidas as $materiaObtenida) {
                if ($inscripcionesMateriaIds->contains($materiaObtenida->materia_id)) {
                    $index = $inscripcionesMateriaIds->search($materiaObtenida->materia_id); //buscamos la materia del front con lo que tenemos en inscripciones
                    if ($index !== false) {

                        $inscripcionesMateriaIds->forget($index); //si no encuentra se borra del array
                    }
                }
            }

            //borramos lo no encontrado en el front y si no esta aprobado, convalidado o espejo
            foreach ($inscripcionesMateriaIds as $borrar) {
                Inscripcion::where('matriculacion_id', $matriculacion->id)->where('materia_id', $borrar)->delete();
            }

            $semestre = Semestre::where('estado', 'AC')->orderBy('id', 'desc')->first();
            $anulacion_correlatividad = AnulacionCorrelatividad::where('alumno_id', $matriculacion->alumno->id)->where('estado', 'AC')->where('semestre_id', $semestre->id)->first();
            if ($anulacion_correlatividad) {
                $anulacion_correlatividad->estado = 'IN';
                $anulacion_correlatividad->save();
            }

            DB::commit();

            return redirect()->route('inscripciones.show', $id)->with('success-message', 'La inscripción del alumno ' . $matriculacion->alumno->primer_nombre . ' ' . $matriculacion->alumno->primer_apellido . ' fue actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('inscripciones.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('desmatricular_inscripciones_matriculaciones');

        DB::beginTransaction();

        try {
            $inscripcion = Inscripcion::findOrFail($id);
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

            DB::commit();

            return redirect()->route('inscripciones.show', $inscripcion->matriculacion_id)->with('error-message', 'La desmatriculación del alumno ' . $inscripcion->matriculacion->alumno->primer_nombre . ' ' . $inscripcion->matriculacion->alumno->primer_apellido . ' de la materia ' . $inscripcion->materia->nombre_fantasia . ' fue realizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('inscripciones.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('anular_desmatriculacion_inscripciones_matriculaciones');

        DB::beginTransaction();

        try {
            $inscripcion = Inscripcion::findOrFail($id);
            if ($inscripcion->matriculacion->semestre->estado == 'IN') {
                return back()->with('error-message', 'La anulación de la desmatriculación no puede realizarse. El semestre se encuentra cerrado.');
            }
            if ($inscripcion->estado == 'DE') {
                $fecha_desmatriculacion = FechaDesmatriculacion::first();
                if (!$fecha_desmatriculacion) {
                    return back()->with('error-message', 'La anulación de la desmatriculación del alumno ' . $inscripcion->matriculacion->alumno->primer_nombre . ' ' . $inscripcion->matriculacion->alumno->primer_apellido . ' no puede realizarse. Primeramente se deben parametrizar las fechas de desmatriculación.');
                }

                $fecha_hoy = Carbon::today();
                $fecha_inicio_desmatriculacion = Carbon::createFromDate($fecha_hoy->year, $fecha_desmatriculacion->mes_inicio, $fecha_desmatriculacion->dia_inicio)->startOfDay();
                $fecha_fin_desmatriculacion = Carbon::createFromDate($fecha_hoy->year, $fecha_desmatriculacion->mes_fin, $fecha_desmatriculacion->dia_fin)->endOfDay();

                if ($fecha_hoy->between($fecha_inicio_desmatriculacion, $fecha_fin_desmatriculacion)) {
                    $inscripcion->estado = 'MA';
                    $inscripcion->save();
                } else {
                    return back()->with('error-message', 'La anulación de la desmatriculación del alumno ' . $inscripcion->matriculacion->alumno->primer_nombre . ' ' . $inscripcion->matriculacion->alumno->primer_apellido . ' no puede realizarse. La fecha de hoy no se encuentra entre las fechas de desmatriculación parametrizadas.');
                }
            } else {
                if ($inscripcion->estado == 'RE') {
                    $tipo = 'reprobó';
                } else {
                    $tipo = 'aprobó';
                }

                return back()->with('error-message', 'La anulacion de la desmatriculación del alumno ' . $inscripcion->matriculacion->alumno->primer_nombre . ' ' . $inscripcion->matriculacion->alumno->primer_apellido . ' no puede realizarse. El alumno ya ' . $tipo . ' la materia.');
            }


            DB::commit();

            return redirect()->route('inscripciones.show', $inscripcion->matriculacion_id)->with('success-message', 'La matriculación del alumno ' . $inscripcion->matriculacion->alumno->primer_nombre . ' ' . $inscripcion->matriculacion->alumno->primer_apellido . ' de la materia ' . $inscripcion->materia->nombre_fantasia . ' fue realizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('inscripciones.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function show_horarios($id)
    {
        $this->authorize('ver_horarios_inscripciones_matriculaciones');

        try {
            $empresa = Empresa::first();
            Carbon::setLocale('es'); //seteamos nuestra fecha en español
            $fecha_hoy = Carbon::today();
            $dias_semana = DiaSemana::get();
            // $dias_semana = DiaSemana::whereBetween('id', [2,6])->get();
            $horarios_clases = collect();
            $horarios = collect();
            $semestre_malla_materias_ambos = collect();

            $matriculacion = Matriculacion::findOrFail($id);
            $inscripciones = Inscripcion::where('matriculacion_id', $matriculacion->id)->whereIn('estado', ['MA', 'EC'])->get();
            $malla = Malla::where('carrera_id', $matriculacion->carrera_id)->first();
            $semestre_malla = SemestreMalla::where('semestre_id', $matriculacion->semestre_id)->where('malla_id', $malla->id)->first();
            $semestre_malla_materias = SemestreMallaMateria::where('semestre_malla_id', $semestre_malla->id)->get();
            foreach ($semestre_malla_materias as $smm) {
                $semestre_malla_materias_ambos->push($smm);
            }

            if ($matriculacion->carrera_siu_id) {
                $malla_siu = Malla::where('carrera_id', $matriculacion->carrera_siu_id)->first();
                $semestre_malla_siu = SemestreMalla::where('semestre_id', $matriculacion->semestre_id)->where('malla_id', $malla_siu->id)->first();
                $semestre_malla_materias_siu = SemestreMallaMateria::where('semestre_malla_id', $semestre_malla_siu->id)->get();

                foreach ($semestre_malla_materias_siu as $smms) {
                    $semestre_malla_materias_ambos->push($smms);
                }
            } else {
                $malla_auxiliar_siu = Malla::whereHas('carrera', function ($query) {
                    $query->where('nombre_real', 'LIKE', '%SIU AUXILIAR%');
                })->first();
                $semestre_malla_siu = SemestreMalla::where('semestre_id', $matriculacion->semestre_id)->where('malla_id', $malla_auxiliar_siu->id)->where('estado', 'AC')->first();
                $semestre_malla_materias_siu = SemestreMallaMateria::where('semestre_malla_id', $semestre_malla_siu->id)->where('estado', 'AC')->get();
                foreach ($semestre_malla_materias_siu as $smms) {
                    $semestre_malla_materias_ambos->push($smms);
                }
            }

            $alumno = Alumno::findOrFail($matriculacion->alumno_id);
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
            $nombre_alumno = $nombre_completo;
            $documento_alumno = $alumno->numero_documento;
            //fin de funcion

            $semestre = $matriculacion->semestre->nombre;
            $programa = $matriculacion->programa->nombre;
            if ($matriculacion->programa_id == 1) {
                $horarios_clases->push(
                    Carbon::createFromTime(8, 0),
                    Carbon::createFromTime(9, 0),
                    Carbon::createFromTime(10, 0),
                    Carbon::createFromTime(11, 0),
                    Carbon::createFromTime(12, 0),
                    Carbon::createFromTime(13, 0),
                    Carbon::createFromTime(14, 0),
                );
            } elseif ($matriculacion->programa_id == 2) {
                $horarios_clases->push(
                    Carbon::createFromTime(18, 0),
                    Carbon::createFromTime(20, 0),
                    Carbon::createFromTime(22, 0)
                );
            }

            foreach ($inscripciones as $inscripcion) {
                foreach ($semestre_malla_materias_ambos as $semestre_malla_materia) {
                    if ($inscripcion->materia_id == $semestre_malla_materia->materia_id) {
                        $dias_horarios = SemestreMallaMateriaHorario::where('semestre_malla_materia_id', $semestre_malla_materia->id)->get();
                        foreach ($dias_horarios as $dia_horario) {
                            if ($dia_horario->dia_semana_id) {
                                $datos_horarios = [
                                    'materia_id' => $semestre_malla_materia->materia_id,
                                    'materia' => $semestre_malla_materia->materia->nombre_fantasia,
                                    'dia' => $dia_horario->diaSemana->nombre,
                                    'hora_inicio' => $dia_horario->hora_inicio,
                                    'hora_fin' => $dia_horario->hora_fin
                                ];
                                $horarios->push($datos_horarios);
                            } else {
                                return redirect()->route('inscripciones.show', $id)->with('error-message', 'No se puede generar el horario del alumno ' . $matriculacion->alumno->primer_nombre . ' ' . $matriculacion->alumno->primer_apellido . '. Algunas materias no cuentan con un horario asignado.');
                            }
                        }
                    }
                }
            }
            $horarios = $horarios->sortBy(function ($horario) {
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
                return view('matriculaciones/inscripciones/show_horarios')->with(compact('dias_semana', 'matriculacion', 'nombre_alumno', 'documento_alumno', 'semestre', 'programa', 'horarios_clases', 'horarios'));
            } else {
                return redirect()->route('inscripciones.show', $id)->with('error-message', 'El alumno no cuenta con inscripciones activas para generar su horario.');
            }

            return view('matriculaciones/inscripciones/show_horarios')->with(compact('dias_semana', 'matriculacion', 'nombre_alumno', 'documento_alumno', 'semestre', 'programa', 'horarios_clases', 'horarios'));
        } catch (\Exception $e) {
            return redirect()->route('inscripciones.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function pdf_horarios($id)
    {
        $this->authorize('imprimir_horarios_inscripciones_matriculaciones');

        try {
            $empresa = Empresa::first();
            $fecha_hoy = Carbon::now();
            $dias_semana = DiaSemana::get();
            // $dias_semana = DiaSemana::whereBetween('id', [2,6])->get();
            $horarios_clases = collect();
            $horarios = collect();
            $semestre_malla_materias_ambos = collect();
            $existe_siu = 'NO';

            $matriculacion = Matriculacion::findOrFail($id);
            $carrera_py = $matriculacion->carrera->nombre_real;
            $inscripciones = Inscripcion::where('matriculacion_id', $matriculacion->id)->whereIn('estado', ['MA', 'EC'])->get();
            $malla = Malla::where('carrera_id', $matriculacion->carrera_id)->first();
            $semestre_malla = SemestreMalla::where('semestre_id', $matriculacion->semestre_id)->where('malla_id', $malla->id)->first();
            $semestre_malla_materias = SemestreMallaMateria::where('semestre_malla_id', $semestre_malla->id)->get();
            foreach ($semestre_malla_materias as $smm) {
                $semestre_malla_materias_ambos->push($smm);
            }

            if ($matriculacion->carrera_siu_id) {
                $existe_siu = 'SI';
                $carrera_siu = $matriculacion->carreraSiu->nombre_real;
                $malla_siu = Malla::where('carrera_id', $matriculacion->carrera_siu_id)->first();
                $semestre_malla_siu = SemestreMalla::where('semestre_id', $matriculacion->semestre_id)->where('malla_id', $malla_siu->id)->first();
                $semestre_malla_materias_siu = SemestreMallaMateria::where('semestre_malla_id', $semestre_malla_siu->id)->get();

                foreach ($semestre_malla_materias_siu as $smms) {
                    $semestre_malla_materias_ambos->push($smms);
                }
            } else {
                $malla_auxiliar_siu = Malla::whereHas('carrera', function ($query) {
                    $query->where('nombre_real', 'LIKE', '%SIU AUXILIAR%');
                })->first();
                $semestre_malla_siu = SemestreMalla::where('semestre_id', $matriculacion->semestre_id)->where('malla_id', $malla_auxiliar_siu->id)->where('estado', 'AC')->first();
                $semestre_malla_materias_siu = SemestreMallaMateria::where('semestre_malla_id', $semestre_malla_siu->id)->where('estado', 'AC')->get();
                foreach ($semestre_malla_materias_siu as $smms) {
                    $semestre_malla_materias_ambos->push($smms);
                }
            }

            $alumno = Alumno::findOrFail($matriculacion->alumno_id);
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
            $nombre_alumno = $nombre_completo;
            $documento_alumno = $alumno->numero_documento;
            //fin de funcion

            $semestre = $matriculacion->semestre->nombre;
            $programa = $matriculacion->programa->nombre;
            if ($matriculacion->programa_id == 1) {
                $horarios_clases->push(
                    Carbon::createFromTime(8, 0),
                    Carbon::createFromTime(9, 0),
                    Carbon::createFromTime(10, 0),
                    Carbon::createFromTime(11, 0),
                    Carbon::createFromTime(12, 0),
                    Carbon::createFromTime(13, 0),
                    Carbon::createFromTime(14, 0),
                );
            } elseif ($matriculacion->programa_id == 2) {
                $horarios_clases->push(
                    Carbon::createFromTime(18, 0),
                    Carbon::createFromTime(20, 0),
                    Carbon::createFromTime(22, 0)
                );
            }

            foreach ($inscripciones as $inscripcion) {
                foreach ($semestre_malla_materias_ambos as $semestre_malla_materia) {
                    if ($inscripcion->materia_id == $semestre_malla_materia->materia_id) {
                        $dias_horarios = SemestreMallaMateriaHorario::where('semestre_malla_materia_id', $semestre_malla_materia->id)->get();
                        foreach ($dias_horarios as $dia_horario) {
                            if ($dia_horario->dia_semana_id) {
                                $datos_horarios = [
                                    'materia_id' => $semestre_malla_materia->materia_id,
                                    'materia' => $semestre_malla_materia->materia->nombre_fantasia,
                                    'dia' => $dia_horario->diaSemana->nombre,
                                    'hora_inicio' => $dia_horario->hora_inicio,
                                    'hora_fin' => $dia_horario->hora_fin
                                ];
                                $horarios->push($datos_horarios);
                            } else {
                                return redirect()->route('inscripciones.show', $id)->with('error-message', 'No se puede generar el horario del alumno ' . $matriculacion->alumno->primer_nombre . ' ' . $matriculacion->alumno->primer_apellido . '. Algunas materias no cuentan con un horario asignado.');
                            }
                        }
                    }
                }
            }
            $horarios = $horarios->sortBy(function ($horario) {
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
            $total_materias = $inscripciones->count();

            $vencimientos = collect();
            $vencimientos->push([
                'nombre' => '1ra Cuota y Matrícula',
                'fecha' => Carbon::createFromDate($semestre_malla->fecha_inicio_vencimiento_cuota)->translatedFormat('d \d\e F \d\e Y')
            ]);

            $fecha = Carbon::createFromDate($semestre_malla->fecha_inicio_vencimiento_cuota);
            $dia = $semestre_malla->dia_vencimiento_cuota;
            $mes = $fecha->format('m');
            $anho = $fecha->format('y');

            for ($i = 1; $i < $semestre_malla->cantidad_cuotas; $i++) {
                $primera_fecha_vencimiento = Carbon::parse($anho . '-' . $mes . '-' . $dia);
                switch ($i) {
                    case 1:
                        $fecha_vencimiento = $primera_fecha_vencimiento->addMonth();
                        $vencimiento = [
                            'nombre' => '2da Cuota',
                            'fecha' => Carbon::createFromDate($fecha_vencimiento)->translatedFormat('d \d\e F \d\e Y')
                        ];
                        $vencimientos->push($vencimiento);
                        break;
                    case 2:
                        $fecha_vencimiento = $fecha_vencimiento->addMonth();
                        $vencimiento = [
                            'nombre' => '3ra Cuota',
                            'fecha' => Carbon::createFromDate($fecha_vencimiento)->translatedFormat('d \d\e F \d\e Y')
                        ];
                        $vencimientos->push($vencimiento);
                        break;
                    case 3:
                        $fecha_vencimiento = $fecha_vencimiento->addMonth();
                        $vencimiento = [
                            'nombre' => '4ta Cuota',
                            'fecha' => Carbon::createFromDate($fecha_vencimiento)->translatedFormat('d \d\e F \d\e Y')
                        ];
                        $vencimientos->push($vencimiento);
                        break;
                    case 4:
                        $fecha_vencimiento = $fecha_vencimiento->addMonth();
                        $vencimiento = [
                            'nombre' => '5ta Cuota',
                            'fecha' => Carbon::createFromDate($fecha_vencimiento)->translatedFormat('d \d\e F \d\e Y')
                        ];
                        $vencimientos->push($vencimiento);
                        break;
                    case 5:
                        $fecha_vencimiento = $fecha_vencimiento->addMonth();
                        $vencimiento = [
                            'nombre' => '6ta Cuota',
                            'fecha' => Carbon::createFromDate($fecha_vencimiento)->translatedFormat('d \d\e F \d\e Y')
                        ];
                        $vencimientos->push($vencimiento);
                        break;
                    case 6:
                        $fecha_vencimiento = $fecha_vencimiento->addMonth();
                        $vencimiento = [
                            'nombre' => '7ma Cuota',
                            'fecha' => Carbon::createFromDate($fecha_vencimiento)->translatedFormat('d \d\e F \d\e Y')
                        ];
                        $vencimientos->push($vencimiento);
                        break;
                    case 7:
                        $fecha_vencimiento = $fecha_vencimiento->addMonth();
                        $vencimiento = [
                            'nombre' => '8va Cuota',
                            'fecha' => Carbon::createFromDate($fecha_vencimiento)->translatedFormat('d \d\e F \d\e Y')
                        ];
                        $vencimientos->push($vencimiento);
                        break;
                    case 8:
                        $fecha_vencimiento = $fecha_vencimiento->addMonth();
                        $vencimiento = [
                            'nombre' => '9na Cuota',
                            'fecha' => Carbon::createFromDate($fecha_vencimiento)->translatedFormat('d \d\e F \d\e Y')
                        ];
                        $vencimientos->push($vencimiento);
                        break;
                    case 9:
                        $fecha_vencimiento = $fecha_vencimiento->addMonth();
                        $vencimiento = [
                            'nombre' => '10ma Cuota',
                            'fecha' => Carbon::createFromDate($fecha_vencimiento)->translatedFormat('d \d\e F \d\e Y')
                        ];
                        $vencimientos->push($vencimiento);
                        break;
                    case 10:
                        $fecha_vencimiento = $fecha_vencimiento->addMonth();
                        $vencimiento = [
                            'nombre' => '11va Cuota',
                            'fecha' => Carbon::createFromDate($fecha_vencimiento)->translatedFormat('d \d\e F \d\e Y')
                        ];
                        $vencimientos->push($vencimiento);
                        break;
                    case 11:
                        $fecha_vencimiento = $fecha_vencimiento->addMonth();
                        $vencimiento = [
                            'nombre' => '12va Cuota',
                            'fecha' => Carbon::createFromDate($fecha_vencimiento)->translatedFormat('d \d\e F \d\e Y')
                        ];
                        $vencimientos->push($vencimiento);
                        break;
                    default:
                        break;
                }
            }

            if ($existe_siu == 'SI') {
                $pdf = Pdf::loadView('matriculaciones/inscripciones/pdf_horarios', compact('empresa', 'fecha_hoy', 'dias_semana', 'nombre_alumno', 'documento_alumno', 'semestre', 'programa', 'horarios_clases', 'horarios', 'carrera_py', 'carrera_siu', 'total_materias', 'vencimientos', 'existe_siu'));
            } else {
                $pdf = Pdf::loadView('matriculaciones/inscripciones/pdf_horarios', compact('empresa', 'fecha_hoy', 'dias_semana', 'nombre_alumno', 'documento_alumno', 'semestre', 'programa', 'horarios_clases', 'horarios', 'carrera_py', 'total_materias', 'vencimientos', 'existe_siu'));
            }
            $pdf->setPaper('A4', 'landscape');

            return $pdf->stream('horario_' . $documento_alumno . '_' . $semestre . '.pdf');
        } catch (\Exception $e) {
            return redirect()->route('inscripciones.show', $id)->with('error-message', $e->getMessage());
        }
    }
}
