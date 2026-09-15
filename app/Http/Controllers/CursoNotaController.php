<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\Models\Curso;
use App\Models\AlumnoNotaUbs;
use App\Models\Escala;

class CursoNotaController extends Controller
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
        $this->authorize('ver_notas_cursos_ubs');

        try {
            $curso = Curso::with('inscripciones', 'notas')->findOrFail($id);
            $escala = Escala::with('escalaDetalles')->findOrFail(5); //obtenemos la escala de maestrias
            return view('ubs/cursos/notas/show')->with(compact('curso', 'escala'));
        } catch (\Exception $e) {
            return redirect()->route('cursos.index')->with('error-message', $e->getMessage());
        }
    }

    public function create($id)
    {
        $this->authorize('crear_notas_cursos_ubs');

        try {
            $curso = Curso::with(['inscripciones' => function ($query) {
                $query->where('estado', 'AC');
            }])->findOrFail($id);
            return view('ubs/cursos/notas/create')->with(compact('curso'));
        } catch (\Exception $e) {
            return redirect()->route('cursos.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request,  $id)
    {
        $this->authorize('crear_notas_cursos_ubs');

        $request->validate([
            'detalles' => ['required', 'array'],
            'detalles.*.alumno' => ['required', 'numeric'],
            'detalles.*.estado' => ['required'],
            'detalles.*.puntaje_obtenido' => ['nullable', 'required_if:detalles.*.estado,PR', 'numeric', 'min:0', 'max:100']
        ]);

        DB::beginTransaction();

        try {
            $curso = Curso::findOrFail($id);

            foreach ($request->detalles as $detalle) {
                $nota = new AlumnoNotaUbs();
                $nota->alumno_id = $detalle['alumno'];
                $nota->curso_id = $curso->id;
                $nota->evaluacion_id = 2;
                if ($detalle['estado'] == 'AU') {
                    $nota->calificacion = 'AUSENTE';
                    $nota->evaluacion = 'N/A';
                } else {
                    $nota->puntaje_obtenido = $detalle['puntaje_obtenido'];
                    $escala = Escala::with('escalaDetalles')->findOrFail(5); //obtenemos la escala de maestrias
                    foreach ($escala->escalaDetalles as $escala_detalle) {
                        if ($nota->puntaje_obtenido >= $escala_detalle->punto_minimo && $nota->puntaje_obtenido <= $escala_detalle->punto_maximo) {
                            $nota->calificacion = $escala_detalle->nota;
                        }
                    }
                    $nota->evaluacion = 'ORDINARIO';
                }
                $nota->save();
            }

            DB::commit();

            return redirect()->route('cursos_notas_ubs.show', $id)->with('success-message', 'Los puntajes del curso ' . $curso->nombre_fantasia . ' fueron cargados exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('cursos_notas_ubs.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_notas_cursos_ubs');

        $request->validate([
            'puntaje_obtenido' => ['required', 'numeric', 'min:0', 'max:100']
        ]);

        DB::beginTransaction();

        try {
            $nota = AlumnoNotaUbs::findOrFail($id);
            $nota->puntaje_obtenido = $request->puntaje_obtenido;
            $escala = Escala::with('escalaDetalles')->findOrFail(5); //obtenemos la escala de maestrias
            foreach ($escala->escalaDetalles as $escala_detalle) {
                if ($nota->puntaje_obtenido >= $escala_detalle->punto_minimo && $nota->puntaje_obtenido <= $escala_detalle->punto_maximo) {
                    $nota->calificacion = $escala_detalle->nota;
                }
            }
            $nota->save();

            DB::commit();

            return response()->json([
                'message' => 'La nota del alumno ' . $nota->alumno->primer_nombre . ' ' . $nota->alumno->primer_apellido . ' fue actualizada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            $nota = AlumnoNotaUbs::findOrFail($id);
            $curso = Curso::findOrFail($nota->curso_id);
            return redirect()->route('cursos_notas_ubs.show', $curso->id)->with('error-message', $e->getMessage());
        }
    }
}
