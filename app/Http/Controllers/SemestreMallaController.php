<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\Models\Semestre;
use App\Models\SemestreMalla;


class SemestreMallaController extends Controller
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

    public function update(Request $request, $id)
    {
        $this->authorize('editar_parametros_carreras_periodos');

        $semestre_malla = SemestreMalla::findOrFail($id);
        $semestre = Semestre::findOrFail($semestre_malla->semestre_id);

        $request->validate([
            'coordinador' => ['required', 'numeric'],
            'fecha_inicio_matriculacion' => ['required', 'date'],
            'fecha_fin_matriculacion' => ['required', 'date', 'after_or_equal:fecha_inicio_matriculacion', 'before:' . $semestre->fecha_fin],
        ]);

        DB::beginTransaction();

        try {
            $semestre_malla->coordinador_id = $request->coordinador;
            $semestre_malla->fecha_inicio_matriculacion = $request->fecha_inicio_matriculacion;
            $semestre_malla->fecha_fin_matriculacion = $request->fecha_fin_matriculacion;
            $semestre_malla->save();

            DB::commit();

            return response()->json([
                'message' => 'Los detalles de la carrera ' . $semestre_malla->malla->carrera->nombre_fantasia . ' en este semestre fueron actualizados exitosamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('semestres.index')->with('error-message', $e->getMessage());
        }
    }
}
