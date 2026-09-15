<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\Tutoria;
use App\Models\TutoriaHorario;
use App\Models\TutoriaAlumno;
use App\Models\TutoriaPrecio;
use App\Models\PagoTutoria;
use App\Models\Alumno;
use App\Models\Materia;
use App\Models\Docente;
use App\Models\Carrera;
use App\Models\Semestre;
use App\Models\DiaSemana;
use App\Models\Modalidad;
use App\Models\Matriculacion;

class TutoriaController extends Controller
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
        $this->authorize('ver_tutorias');
        try {
            $tutorias = Tutoria::with('alumnos')->orderBy('id', 'desc')->get();

            return view('tutorias.index')->with(compact('tutorias'));
        } catch (\Exception $e) {
            return redirect()->route('tutorias.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_tutorias');

        try {
            $tutoria = Tutoria::with('alumnos', 'horarios')->findOrFail($id);

            return view('tutorias.show')->with(compact('tutoria'));
        } catch (\Exception $e) {
            return redirect()->route('tutorias.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_tutorias');

        try {
            $materias = Materia::where('estado', 'AC')->orderBy('nombre_fantasia')->get();
            $hoy = Carbon::today();
            $semestre = Semestre::whereDate('fecha_inicio', '<=', $hoy)
                            ->whereDate('fecha_fin', '>=', $hoy)
                            ->where('estado', 'AC')
                            ->orderBy('id', 'asc')
                            ->first();
            if (!$semestre) {
                return back()->with('error-message', 'No se puede crear una nueva tutoría. No existe un semestre activo.');
            }
            $docentes = Docente::where('estado', 'AC')->orderBy('primer_nombre')->get();
            $modalidades = Modalidad::where('estado', 'AC')->get();
            $dias = DiaSemana::whereBetween('id', [2,6])->get();

            return view('tutorias.create')->with(compact('materias', 'semestre', 'docentes', 'modalidades', 'dias'));
        } catch (\Exception $e) {
            return redirect()->route('tutorias.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_tutorias');

        DB::beginTransaction();

            $request->validate([
                'materia' => ['required', 'numeric'],
                'docente' => ['required', 'numeric'],
                'modalidad' => ['required', 'numeric'],
                'semestre' => ['required', 'numeric'],
                'fecha_inicio' => ['required', 'date'],
                'fecha_fin' => ['required', 'date', 'after:fecha_inicio'],
                'cantidad_clases' => ['required', 'numeric', 'min:1', 'max:10'],
                'cantidad_horas' => ['required', 'numeric', 'min:1', 'max:20'],

                'detalles' => ['required', 'array'],
                'detalles.*.dia' => ['required', 'numeric'],
                'detalles.*.hora_inicio' => ['required', 'date_format:H:i'],
                'detalles.*.hora_fin' => ['required', 'date_format:H:i',
                    function ($attribute, $value, $fail) {
                        $hora_inicio = request()->input(str_replace('hora_fin', 'hora_inicio', $attribute));

                        if ($hora_inicio) {
                            $inicio = Carbon::createFromFormat('H:i', $hora_inicio);
                            $fin = Carbon::createFromFormat('H:i', $value);

                            if ($fin->diffInHours($inicio) > 2) {
                                $fail('La clase no puede durar mas de 2 horas.');
                            }
                        }
                    }
                ]
            ]);

            try {
                $tutoria = new Tutoria();
                $tutoria->materia_id = $request->materia;
                $tutoria->docente_id = $request->docente;
                $tutoria->modalidad_id = $request->modalidad;
                $tutoria->semestre_id = $request->semestre;
                $tutoria->fecha_inicio = $request->fecha_inicio;
                $tutoria->fecha_fin = $request->fecha_fin;
                $tutoria->cantidad_clases = $request->cantidad_clases;
                $tutoria->cantidad_horas = $request->cantidad_horas;
                $tutoria->cargado_por_id = Auth::id();
                $tutoria->save();

                foreach ($request->detalles as $detalle) {
                    $horario = new TutoriaHorario();
                    $horario->tutoria_id = $tutoria->id;
                    $horario->dia_semana_id = $detalle['dia'];
                    $horario->hora_inicio = $detalle['hora_inicio'];
                    $horario->hora_fin = $detalle['hora_fin'];
                    $horario->save();
                }

                DB::commit();

                return redirect()->route('tutorias.index')->with('success-message', 'La tutoría de la materia ' . $tutoria->materia->nombre_fantasia . ' fue creada existosamente.');
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('tutorias.index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_tutorias');

        try {
            $tutoria = Tutoria::findOrFail($id);
            if ($tutoria->semestre->estado == 'IN') {
                return back()->with('error-message', 'La tutoria no puede actualizarse. El semestre se encuentra cerrado.');
            }

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

                return back()->with('error-message', 'La tutoría no se puede editar. Ya se encuentra ' . $tipo . '.');
            }

            $docentes = Docente::where('estado', 'AC')->get();

            return view('tutorias/edit')->with(compact('tutoria', 'docentes'));
        } catch (\Exception $e) {
            return redirect()->route('tutorias.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_tutorias');

        DB::beginTransaction();

            $request->validate([
                'docente' => ['required', 'numeric'],
                'fecha_inicio' => ['required', 'date'],
                'fecha_fin' => ['required', 'date', 'after:fecha_inicio'],
                'cantidad_clases' => ['required', 'numeric', 'min:1'],
            ]);

            try {
                $tutoria = Tutoria::with('horarios')->findOrFail($id);
                if ($tutoria->semestre->estado == 'IN') {
                    return back()->with('error-message', 'La tutoria no puede actualizarse. El semestre se encuentra cerrado.');
                }
                $old_clases = $tutoria->cantidad_clases;

                $tutoria->docente_id = $request->docente;
                $tutoria->fecha_inicio = $request->fecha_inicio;
                $tutoria->fecha_fin = $request->fecha_fin;
                $tutoria->cantidad_clases = $request->cantidad_clases;
                $tutoria->actualizado_por_id = Auth::id();

                if ($tutoria->horarios->count() > 0 && $old_clases != $request->cantidad_clases) {
                    $total_horas = 0;
                    for ($i=0; $i < $tutoria->cantidad_clases; $i++) {
                        foreach ($tutoria->horarios as $horario) {
                            $hora_inicio = Carbon::createFromFormat('H:i:s', $horario->hora_inicio);
                            $hora_fin = Carbon::createFromFormat('H:i:s', $horario->hora_fin);

                            $diferencia = $hora_inicio->diffInHours($hora_fin);
                            $total_horas = $total_horas + $diferencia;
                        }
                    }
                    $tutoria->cantidad_horas = $total_horas;
                }
                $tutoria->save();

                DB::commit();

                return redirect()->route('tutorias.index')->with('success-message', 'La tutoría de la materia ' . $tutoria->materia->nombre_fantasia . ' fue actualizada existosamente.');
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('tutorias.index')->with('error-message', $e->getMessage());
            }
    }

    public function edit_alumnos($id)
    {
        $this->authorize('editar_alumnos_tutorias');

        try {
            $tutoria = Tutoria::with('alumnos')->findOrFail($id);
            if ($tutoria->semestre->estado == 'IN') {
                return back()->with('error-message', 'Los alumnos de la tutoria no pueden actualizarse. El semestre se encuentra cerrado.');
            }

            if (!$tutoria->docente_id || !$tutoria->fecha_inicio || !$tutoria->fecha_fin || !$tutoria->cantidad_clases) {
                return back()->with('error-message', 'Debe cargar todos los datos de la tutoría para poder editar los alumnos.');
            }

            $materia_id = $tutoria->materia_id;
            $alumnos = Alumno::where('estado', 'AC')->get();
            // $alumnos = Alumno::select(['alumnos.*'])
            // ->join('matriculaciones as m', 'alumnos.id', '=', 'm.alumno_id')
            // ->whereHas('matriculaciones', function ($query) use ($materia_id) {
            //     $query->whereHas('inscripciones', function ($query) use ($materia_id) {
            //             $query->where('materia_id', $materia_id)
            //                 ->where('estado', '!=','AP');
            //         });
            // })
            // ->groupBy('alumnos.id')
            // ->havingRaw('COUNT(m.id) >= 2')
            // ->orderBy('primer_nombre')
            // ->get();

            if ($alumnos->count() == 0) {
                return back()->with('error-message', 'No existen alumnos aptos para inscribir a la tutoría.');
            }

            return view('tutorias/edit_alumnos')->with(compact('tutoria', 'alumnos'));
        } catch (\Exception $e) {
            return redirect()->route('tutorias.index')->with('error-message', $e->getMessage());
        }
    }

    public function update_alumnos(Request $request, $id)
    {
        $this->authorize('editar_alumnos_tutorias');

        DB::beginTransaction();

        $request->validate([
            'detalles' => ['required', 'array'],
            'detalles.*.alumno' => ['required', 'numeric'],
            'detalles.*.carrera' => ['required', 'numeric']
        ]);

        try {
            $tutoria = Tutoria::findOrFail($id);
            if ($tutoria->semestre->estado == 'IN') {
                return back()->with('error-message', 'Los alumnos de la tutoria no pueden actualizarse. El semestre se encuentra cerrado.');
            }

            if ($tutoria->alumnos->count() > 0) {
                $alumnos_front = collect();

                foreach ($request->detalles as $detalle) {
                    $alumnos_front->push($detalle['alumno']);
                }

                $alumnos = $tutoria->alumnos;

                $alumnos_eliminar = $alumnos->filter(function ($alumno) use ($alumnos_front) {
                    return !$alumnos_front->contains($alumno->alumno_id);
                });

                $alumnos_agregar = $alumnos->filter(function ($alumno) use ($alumnos_front) {
                    return $alumnos_front->contains($alumno->alumno_id);
                });

                foreach ($alumnos_eliminar as $eliminar) {
                    if ($eliminar->estado == 'IN') {
                        $pago = PagoTutoria::where('tutoria_id', $tutoria->id)->where('alumno_id', $eliminar->alumno_id)->first();
                        if ($pago && $pago->estado == 'PE') {
                            $pago->delete();
                        }
                        $eliminar->delete();
                    } else {
                        return back()->with('error-message', 'El alumno ' . $eliminar->alumno->primer_nombre . ' ' . $eliminar->alumno->primer_apellido . ' no se puede eliminar. Su tutoría ya se encuentra abonada.');
                    }
                }

                foreach ($alumnos_agregar as $agregar) {
                    $new_alumno = new TutoriaAlumno();
                    $new_alumno->tutoria_id = $tutoria->id;
                    $new_alumno->alumno_id = $detalle['alumno'];
                    $new_alumno->carrera_id = $detalle['carrera'];
                    $new_alumno->save();

                    $carrera_alumno = Matriculacion::where('semestre_id', $tutoria->semestre_id)->where('alumno_id', $agregar->alumno_id)->where('estado', 'AC')->orderBy('id', 'desc')->first()->carrera_id;
                    // $tutoria_precio = TutoriaPrecio::where('modalidad_id', $tutoria->modalidad_id)->where('carrera_id', $carrera_alumno)->first();

                    // $pago_tutoria = new PagoTutoria();
                    // $pago_tutoria->tutoria_id = $tutoria->id;
                    // $pago_tutoria->descripcion = 'TUTORIA ' . $tutoria->materia->nombre_fantasia . ' - ' . $tutoria->semestre->nombre;
                    // $pago_tutoria->vencimiento = Carbon::now();
                    // $pago_tutoria->monto = $tutoria_precio->articulo->precio_contado;
                    // $pago_tutoria->saldo = $tutoria_precio->articulo->precio_contado;
                    // $pago_tutoria->alumno_id = $agregar->alumno_id;
                    // $pago_tutoria->save();
                }
            } else {
                foreach ($request->detalles as $detalle) {
                    $new_alumno = new TutoriaAlumno();
                    $new_alumno->tutoria_id = $tutoria->id;
                    $new_alumno->alumno_id = $detalle['alumno'];
                    $new_alumno->carrera_id = $detalle['carrera'];
                    $new_alumno->save();

                    $carrera_alumno = Matriculacion::where('semestre_id', $tutoria->semestre_id)->where('alumno_id', $detalle['alumno'])->where('estado', 'AC')->orderBy('id', 'desc')->first()->carrera_id;
                    // $tutoria_precio = TutoriaPrecio::where('modalidad_id', $tutoria->modalidad_id)->where('carrera_id', $carrera_alumno)->first();

                    // $pago_tutoria = new PagoTutoria();
                    // $pago_tutoria->tutoria_id = $tutoria->id;
                    // $pago_tutoria->descripcion = 'TUTORIA ' . $tutoria->materia->nombre_fantasia . ' - ' . $tutoria->semestre->nombre;
                    // $pago_tutoria->vencimiento = Carbon::now();
                    // $pago_tutoria->monto = $tutoria_precio->articulo->detalle->precio_contado;
                    // $pago_tutoria->saldo = $tutoria_precio->articulo->detalle->precio_contado;
                    // $pago_tutoria->alumno_id = $detalle['alumno'];
                    // $pago_tutoria->save();
                }
            }

            DB::commit();

            return redirect()->route('tutorias.show', $id)->with('success-message', 'Los alumnos de la tutoría de la materia ' . $tutoria->materia->nombre_fantasia . ' fueron actualizados exitosamente');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tutorias.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit_horario($id)
    {
        $this->authorize('editar_horarios_tutorias');

        try {
            $tutoria = Tutoria::with('horarios')->findOrFail($id);
            if ($tutoria->semestre->estado == 'IN') {
                return back()->with('error-message', 'Los horarios de la tutoria no pueden actualizarse. El semestre se encuentra cerrado.');
            }

            if (!$tutoria->docente_id || !$tutoria->fecha_inicio || !$tutoria->fecha_fin || !$tutoria->cantidad_clases) {
                return back()->with('error-message', 'Debe cargar todos los datos de la tutoría para poder editar el horario.');
            }

            if ($tutoria->estado == 'AC') {
                $dias = DiaSemana::whereBetween('id', [2,6])->get();

                return view('tutorias/edit_horarios')->with(compact('tutoria', 'dias'));
            } else {
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

                return back()->with('error-message', 'No se puede editar el horario. La tutoría ya se encuentra ' . $tipo . '.');
            }

        } catch (\Exception $e) {
            return redirect()->route('tutorias.index')->with('error-message', $e->getMessage());
        }
    }

    public function update_horario(Request $request, $id)
    {
        $this->authorize('editar_horarios_tutorias');

        DB::beginTransaction();

        $request->validate([
            'detalles' => ['required', 'array'],
            'detalles.*.hora_inicio' => ['required', 'date_format:H:i'],
            'detalles.*.hora_fin' => ['required', 'date_format:H:i',
                function ($attribute, $value, $fail) {
                    $hora_inicio = request()->input(str_replace('hora_fin', 'hora_inicio', $attribute));

                    if ($hora_inicio) {
                        $inicio = Carbon::createFromFormat('H:i', $hora_inicio);
                        $fin = Carbon::createFromFormat('H:i', $value);

                        if ($fin->diffInHours($inicio) > 2) {
                            $fail('La clase no puede durar mas de 2 horas.');
                        }
                    }
                }
            ]
        ]);

        try {
            $tutoria = Tutoria::findOrFail($id);
            if ($tutoria->semestre->estado == 'IN') {
                return back()->with('error-message', 'Los horarios de la tutoria no pueden actualizarse. El semestre se encuentra cerrado.');
            }
            $tutoria->cantidad_horas = $request->cantidad_horas;
            $tutoria->save();

            $old_horario = TutoriaHorario::where('tutoria_id', $tutoria->id)->exists();

            if ($old_horario) {
                TutoriaHorario::where('tutoria_id', $tutoria->id)->delete();
            }

            foreach ($request->detalles as $detalle) {
                $horario = new TutoriaHorario();
                $horario->tutoria_id = $tutoria->id;
                $horario->dia_semana_id = $detalle['dia'];
                $horario->hora_inicio = $detalle['hora_inicio'];
                $horario->hora_fin = $detalle['hora_fin'];
                $horario->save();
            }

            DB::commit();

            return redirect()->route('tutorias.show', $id)->with('success-message', 'El horario de la tutoría de la materia ' . $tutoria->materia->nombre_fantasia . ' fueron actualizados exitosamente');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tutorias.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_carrera($id)
    {
        $this->authorize('crear_tutorias');

        try {
            $matriculacion = Matriculacion::with('carrera')->where('alumno_id', $id)->where('estado', 'AC')->orderBy('id', 'desc')->first();

            return response()->json([
                'carrera' => $matriculacion->carrera,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tutorias.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_tutorias');

        DB::beginTransaction();

        try {
            $tutoria = Tutoria::findOrFail($id);
            if ($tutoria->semestre->estado == 'IN') {
                return back()->with('error-message', 'La tutoria no puede eliminarse. El semestre se encuentra cerrado.');
            }

            $pagados = TutoriaAlumno::where('tutoria_id', $tutoria->id)->where('estado', '!=', 'IN')->count();

            if ($pagados != 0) {
                return back()->with('La tutoría no se puede eliminar. Ya está pagada por un alumno.');
            }

            if ($tutoria->estado == 'AC') {
                TutoriaAlumno::where('tutoria_id', $tutoria->id)->delete();
                PagoTutoria::where('tutoria_id', $tutoria->id)->delete();
                $tutoria->delete();
            } else {
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

                return back()->with('La tutoría no se puede eliminar. Se encuentra ' . $tipo . '.');
            }

            DB::commit();

            return redirect()->route('tutorias.index')->with('error-message', 'La tutoría fue eliminada exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('tutorias.index')->with('error-message', 'La tutoría no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('tutorias.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
