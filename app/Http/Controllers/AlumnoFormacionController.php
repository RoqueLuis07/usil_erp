<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

use App\Models\AlumnoFormacion;


class AlumnoFormacionController extends Controller
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
        $this->authorize('ver_formaciones_academicas');

        return view('alumnos_formaciones.index');
    }

    public function index_ajax()
    {
        $this->authorize('ver_formaciones_academicas');

        try {
            $alumnos_formaciones = AlumnoFormacion::orderBy('nombre', 'asc')->get();
            return response()->json([
                'alumnos_formaciones' => $alumnos_formaciones,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('formaciones_academicas.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_formaciones_academicas');

        try {
            $alumno_formacion = AlumnoFormacion::findOrFail($id);
            return response()->json([
                'alumno_formacion' => $alumno_formacion,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('formaciones_academicas.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_formaciones_academicas');

        DB::beginTransaction();

            $request->validate([
                'nombre_formacion_academica' => ['required', Rule::unique('alumnos_formaciones', 'nombre')],
            ]);

            try {
                $alumno_formacion = new AlumnoFormacion();
                $alumno_formacion->nombre = removeAccents(Str::upper($request->nombre_formacion_academica));
                $alumno_formacion->save();

                DB::commit();

                $alumnos_formaciones = AlumnoFormacion::get();
                return response()->json([
                    'message' => 'La formación académica' . $alumno_formacion->nombre . ' fue creada exitosamente.',
                    'alumnos_formaciones' => $alumnos_formaciones,
                    'selected' => $alumno_formacion,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('formaciones_academicas.index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_formaciones_academicas');

        try {
            $alumno_formacion = AlumnoFormacion::findOrFail($id);
            return response()->json([
                'alumno_formacion' => $alumno_formacion,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('formaciones_academicas.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_formaciones_academicas');

        DB::beginTransaction();

        $request->validate([
            'nombre_formacion_academica' => ['required', Rule::unique('alumnos_formaciones', 'nombre')->ignore($id)],
        ]);

        try {
            $alumno_formacion = AlumnoFormacion::findOrFail($id);
            $alumno_formacion->nombre = removeAccents(Str::upper($request->nombre_formacion_academica));
            $alumno_formacion->save();

            DB::commit();

            $alumnos_formaciones = AlumnoFormacion::get();
            return response()->json([
                'message' => 'La formación académica' . $alumno_formacion->nombre . ' fue actualizada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('formaciones_academicas.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_unactivate($id)
    {
        $this->authorize('inactivar_formaciones_academicas');

        try {
            $alumno_formacion = AlumnoFormacion::findOrFail($id);
            return response()->json([
                'alumno_formacion' => $alumno_formacion,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('formaciones_academicas.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_formaciones_academicas');

        DB::beginTransaction();

        try {
            $alumno_formacion = AlumnoFormacion::findOrFail($id);
            $alumno_formacion->estado = 'IN';
            $alumno_formacion->save();

            DB::commit();

            return response()->json([
                'message' => 'La formación académica' . $alumno_formacion->nombre . ' fue inactivada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('formaciones_academicas.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_activate($id)
    {
        $this->authorize('activar_formaciones_academicas');

        try {
            $alumno_formacion = AlumnoFormacion::findOrFail($id);
            return response()->json([
                'alumno_formacion' => $alumno_formacion,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('formaciones_academicas.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_formaciones_academicas');

        DB::beginTransaction();

        try {
            $alumno_formacion = AlumnoFormacion::findOrFail($id);
            $alumno_formacion->estado = 'AC';
            $alumno_formacion->save();

            DB::commit();

            return response()->json([
                'message' => 'La formación académica' . $alumno_formacion->nombre . ' fue activada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('formaciones_academicas.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_formaciones_academicas');

        try {
            $alumno_formacion = AlumnoFormacion::findOrFail($id);
            return response()->json([
                'alumno_formacion' => $alumno_formacion,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('formaciones_academicas.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_formaciones_academicas');

        DB::beginTransaction();

        try {
            $alumno_formacion = AlumnoFormacion::findOrFail($id);
            $alumno_formacion->delete();

            DB::commit();

            return response()->json([
                'message' => 'La formación académica' . $alumno_formacion->nombre . ' fue eliminada exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('formaciones_academicas.index')->with('error-message', 'La formación académica' . $alumno_formacion->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('formaciones_academicas.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
