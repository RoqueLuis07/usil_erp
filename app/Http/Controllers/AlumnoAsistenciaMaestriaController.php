<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\Models\AlumnoAsistenciaUbs;
use App\Models\Curso;
use App\Models\CursoModulo;
use App\Models\Modalidad;
use App\Models\Alumno;
use App\Models\InscripcionModulo;


class AlumnoAsistenciaMaestriaController extends Controller
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

    public function show($curso_id)
    {
        $this->authorize('ver_asistencias_maestrias_ubs');

        try {
            $maestria = Curso::with(['asistencias' => function ($query) {
                $query->orderBy('id', 'desc');
            }], 'modulos')->findOrFail($curso_id);

            $asistencias_fechas = $maestria->asistencias->groupBy(function ($asistencia) {
                return Carbon::parse($asistencia->fecha)->format('Y-m-d');
            });

            $asistencias_generales = $maestria->asistencias->groupBy(function ($asistencia) {
                return $asistencia->alumno_id;
            });

            $porcentajes_asistencia = [];

            foreach ($asistencias_generales as $alumno => $asistencias) {
                $total_asistencias = $asistencias->count();

                $asistencias_presentes = $asistencias->whereIn('estado', ['PR', 'AJ'])->count();
                $porcentaje = ($total_asistencias > 0) ? ($asistencias_presentes / $total_asistencias) * 100 : 0;
                $porcentajes_asistencia[$alumno] = number_format($porcentaje, 2, ',', '.');
            }

            return view('ubs/maestrias/alumnos_asistencias/show')->with(compact('maestria', 'asistencias_fechas', 'asistencias_generales', 'porcentajes_asistencia'));
        } catch (\Exception $e) {
            return redirect()->route('maestrias.index')->with('error-message', $e->getMessage());
        }
    }

    public function create($curso_id, $modulo_id)
    {
        $this->authorize('crear_asistencias_maestrias_ubs');

        try {
            $maestria = CursoModulo::where('curso_id', $curso_id)->where('modulo_id', $modulo_id)->first();
            $modalidades = Modalidad::where('estado', 'AC')->get();

            $alumnos = Alumno::select(['alumnos.*'])
            ->whereHas('inscripcionesModulos', function ($query) use ($curso_id, $modulo_id) {
                $query->where('curso_id', $curso_id)
                    ->where('modulo_id', $modulo_id)
                    ->whereIn('estado', ['MA', 'EC']);
            })
            ->groupBy('alumnos.id')
            ->get();

            return view('ubs/maestrias/alumnos_asistencias/create')->with(compact('maestria', 'alumnos', 'modalidades'));
        } catch (\Exception $e) {
            return redirect()->route('maestrias.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request, $curso_id, $modulo_id, $docente_id)
    {
        $this->authorize('crear_asistencias_maestrias_ubs');

        $request->validate([
            'fecha' => ['required', 'date'],
            'modalidad' => ['required', 'numeric'],

            'detalles' => ['required', 'array'],
            'detalles.*.alumno' => ['required', 'numeric'],
            'detalles.*.estado' => ['required', 'size:2'],
            'detalles.*.observaciones' => 'nullable'
        ]);

        DB::beginTransaction();

        try {
            $cargado = AlumnoAsistenciaUbs::where('curso_id', $curso_id)->where('modulo_id', $modulo_id)->whereDate('fecha', $request->fecha)->exists();
            if ($cargado) {
                return back()->with('error-message', 'No es posible cargar las asistencias en la fecha seleccionada, ya fue cargada previamente.');
            }

            $maestria = Curso::findOrFail($curso_id);

            foreach ($request->detalles as $detalle) {
                $alumno_asistencia = new AlumnoAsistenciaUbs();
                $alumno_asistencia->curso_id = $curso_id;
                $alumno_asistencia->modulo_id = $modulo_id;
                $alumno_asistencia->alumno_id = $detalle['alumno'];
                $alumno_asistencia->docente_id = $docente_id;
                $alumno_asistencia->modalidad_id = $request->modalidad;
                $alumno_asistencia->fecha = $request->fecha;
                $alumno_asistencia->estado = $detalle['estado'];
                $alumno_asistencia->observaciones = removeAccents(Str::upper($detalle['observaciones']));
                $alumno_asistencia->save();

                $inscripcion = InscripcionModulo::where('alumno_id', $alumno_asistencia->alumno_id)->where('curso_id', $alumno_asistencia->curso_id)->where('modulo_id', $alumno_asistencia->modulo_id)->first();
                if ($inscripcion->estado == 'MA') {
                    $inscripcion->estado = 'EC';
                    $inscripcion->save();
                }
            }

            DB::commit();

            if ($request->generado_docente == 'SI') {
                return redirect()->route('pantallas_docentes.index', Auth::id())->with('success-message', 'La asistencia de los alumnos en la maestría ' . $maestria->nombre_fantasia . ' fue creada existosamente.');
            } else {
                return redirect()->route('maestrias.index')->with('success-message', 'La asistencia de los alumnos en la maestría ' . $maestria->nombre_fantasia . ' fue creada existosamente.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('maestrias.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_asistencias_maestrias_ubs');

        DB::beginTransaction();

        try {
            $asistencia = AlumnoAsistenciaUbs::findOrFail($id);
            $asistencia->estado = $request->estado;
            $asistencia->save();

            DB::commit();

            return response()->json([
                'message' => 'La asistencia del alumno ' . $asistencia->alumno->primer_nombre . ' ' . $asistencia->alumno->primer_apellido . ' fue actualizada correctamente.',
            ]);
        } catch (\Exception $e) {
            return redirect()->route('maestrias.index')->with('error-message', $e->getMessage());
        }
    }

    public function update_observacion(Request $request, $id)
    {
        $this->authorize('editar_asistencias_maestrias_ubs');

        DB::beginTransaction();

        try {
            $asistencia = AlumnoAsistenciaUbs::findOrFail($id);
            $asistencia->observaciones = removeAccents(Str::upper($request->observaciones));
            $asistencia->save();

            DB::commit();

            return response()->json([
                'message' => 'La observación de la asistencia del alumno ' . $asistencia->alumno->primer_nombre . ' ' . $asistencia->alumno->primer_apellido . ' fue actualizada correctamente.',
            ]);
        } catch (\Exception $e) {
            return redirect()->route('maestrias.index')->with('error-message', $e->getMessage());
        }
    }
}
