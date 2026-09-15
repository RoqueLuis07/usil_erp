<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

use App\Models\TipoCurso;


class TipoCursoController extends Controller
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
        $this->authorize('ver_tipos_cursos_ubs');

        return view('ubs/tipos_cursos.index');
    }

    public function index_ajax()
    {
        $this->authorize('ver_tipos_cursos_ubs');

        try {
            $tipos_cursos = TipoCurso::orderBy('nombre', 'asc')->get();
            return response()->json([
                'tipos_cursos' => $tipos_cursos,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_cursos.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_tipos_cursos_ubs');

        try {
            $tipo_curso = TipoCurso::findOrFail($id);
            return response()->json([
                'tipo_curso' => $tipo_curso,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_cursos.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_tipos_cursos_ubs');

        DB::beginTransaction();

            $request->validate([
                'nombre' => ['required', Rule::unique('tipos_cursos')],
            ]);

            try {
                $tipo_curso = new TipoCurso();
                $tipo_curso->nombre = removeAccents(Str::upper($request->nombre));
                $tipo_curso->cargado_por_id = Auth::id();
                $tipo_curso->save();

                DB::commit();

                $tipos_cursos = TipoCurso::get();
                return response()->json([
                    'message' => 'El tipo de curso ' . $tipo_curso->nombre . ' fue creado exitosamente.',
                    'tipos_cursos' => $tipos_cursos,
                    'selected' => $tipo_curso,
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('tipos_cursos.index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_tipos_cursos_ubs');

        try {
            $tipo_curso = TipoCurso::findOrFail($id);
            return response()->json([
                'tipo_curso' => $tipo_curso,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_cursos.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_tipos_cursos_ubs');

        DB::beginTransaction();

        $request->validate([
            'nombre' => ['required', Rule::unique('tipos_cursos')->ignore($id)],
        ]);

        try {
            $tipo_curso = TipoCurso::findOrFail($id);
            $tipo_curso->nombre = removeAccents(Str::upper($request->nombre));
            $tipo_curso->actualizado_por_id = Auth::id();
            $tipo_curso->save();

            DB::commit();

            $tipos_cursos = TipoCurso::get();
            return response()->json([
                'message' => 'El tipo de curso ' . $tipo_curso->nombre . ' fue actualizado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tipos_cursos.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_unactivate($id)
    {
        $this->authorize('inactivar_tipos_cursos_ubs');

        try {
            $tipo_curso = TipoCurso::findOrFail($id);
            return response()->json([
                'tipo_curso' => $tipo_curso,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_cursos.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_tipos_cursos_ubs');

        DB::beginTransaction();

        try {
            $tipo_curso = TipoCurso::findOrFail($id);
            $tipo_curso->actualizado_por_id = Auth::id();
            $tipo_curso->estado = 'IN';
            $tipo_curso->save();

            DB::commit();

            return response()->json([
                'message' => 'El tipo de curso ' . $tipo_curso->nombre . ' fue inactivado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tipos_cursos.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_activate($id)
    {
        $this->authorize('activar_tipos_cursos_ubs');

        try {
            $tipo_curso = TipoCurso::findOrFail($id);
            return response()->json([
                'tipo_curso' => $tipo_curso,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_cursos.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_tipos_cursos_ubs');

        DB::beginTransaction();

        try {
            $tipo_curso = TipoCurso::findOrFail($id);
            $tipo_curso->actualizado_por_id = Auth::id();
            $tipo_curso->estado = 'AC';
            $tipo_curso->save();

            DB::commit();

            return response()->json([
                'message' => 'El tipo de curso ' . $tipo_curso->nombre . ' fue activado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tipos_cursos.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_destroy($id)
    {
        $this->authorize('eliminar_tipos_cursos_ubs');

        try {
            $tipo_curso = TipoCurso::findOrFail($id);
            return response()->json([
                'tipo_curso' => $tipo_curso,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tipos_cursos.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_tipos_cursos_ubs');

        DB::beginTransaction();

        try {
            $tipo_curso = TipoCurso::findOrFail($id);
            $tipo_curso->delete();

            DB::commit();

            return response()->json([
                'message' => 'El tipo de curso ' . $tipo_curso->nombre . ' fue eliminado exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('tipos_cursos.index')->with('error-message', 'El tipo de curso ' . $tipo_curso->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('tipos_cursos.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
