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


class MaestriaModuloController extends Controller
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
        $this->authorize('editar_modulos_en_maestrias_ubs');

        try {
            $maestria = Curso::findOrFail($id);
            $modulos = Modulo::where('es_maestria', true)->where('estado', 'AC')->get();
            $docentes = Docente::where('ubs', true)->where('estado', 'AC')->get();
            return view('ubs/maestrias/edit_modulos')->with(compact('maestria', 'modulos', 'docentes'));
        } catch (\Exception $e) {
            return redirect()->route('maestrias.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_modulos_en_maestrias_ubs');

        $request->validate([
            'nuevo' => 'required',

            'detalles' => ['required', 'array'],
            'detalles.*.semestre' => ['required', 'numeric', 'min:1', 'max:4'],
            'detalles.*.modulo' => ['required', 'numeric'],
            'detalles.*.docente' => ['required', 'numeric']
        ]);

        DB::beginTransaction();

        try {
            $maestria = Curso::findOrFail($id);

            if ($request->nuevo == 'NO') {
                CursoModulo::where('curso_id', $maestria->id)->delete();
                $tipo = 'editados';
            } else {
                $tipo = 'agregados';
            }


            $numero_orden = 1;
            foreach ($request->detalles as $detalle) {
                $maestria_modulo = new CursoModulo();
                $maestria_modulo->curso_id = $maestria->id;
                $maestria_modulo->semestre = $detalle['semestre'];
                $maestria_modulo->modulo_id = $detalle['modulo'];
                $maestria_modulo->docente_id = $detalle['docente'];
                $maestria_modulo->orden = $numero_orden;
                $maestria_modulo->save();

                $numero_orden = $numero_orden + 1;
            }

            DB::commit();

            return redirect()->route('maestrias.index')->with('success-message', 'Los módulos del maestria ' . $maestria->nombre_fantasia . ' fueron ' . $tipo . ' exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('maestrias.index')->with('error-message', $e->getMessage());
        }
    }
}
