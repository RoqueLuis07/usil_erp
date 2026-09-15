<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use App\Models\CursoModulo;
use App\Models\Curso;
use App\Models\Modulo;
use App\Models\Docente;


class CursoModuloController extends Controller
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

    public function edit($id)
    {
        $this->authorize('editar_modulos_cursos_ubs');

        try {
            $curso = Curso::findOrFail($id);
            $modulos = Modulo::where('es_maestria', false)->where('estado', 'AC')->get();
            $docentes = Docente::where('ubs', true)->where('estado', 'AC')->get();
            return view('ubs/cursos/edit_modulos')->with(compact('curso', 'modulos', 'docentes'));
        } catch (\Exception $e) {
            return redirect()->route('cursos.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_modulos_cursos_ubs');

        $request->validate([
            'nuevo' => 'required',

            'detalles' => ['required', 'array'],
            'detalles.*.modulo' => ['required', 'numeric'],
            'detalles.*.docente' => ['required', 'numeric']
        ]);

        DB::beginTransaction();

        try {
            $curso = Curso::findOrFail($id);

            if ($request->nuevo == 'NO') {
                CursoModulo::where('curso_id', $curso->id)->delete();
                $tipo = 'editados';
            } else {
                $tipo = 'agregados';
            }

            $numero_orden = 1;
            foreach ($request->detalles as $detalle) {
                $curso_modulo = new CursoModulo();
                $curso_modulo->curso_id = $curso->id;
                $curso_modulo->modulo_id = $detalle['modulo'];
                $curso_modulo->docente_id = $detalle['docente'];
                $curso_modulo->orden = $numero_orden;
                $curso_modulo->save();

                $numero_orden = $numero_orden + 1;
            }

            DB::commit();

            return redirect()->route('cursos.index')->with('success-message', 'Los módulos del curso ' . $curso->nombre_fantasia . ' fueron ' . $tipo . ' exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('cursos.index')->with('error-message', $e->getMessage());
        }
    }
}
