<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use App\Models\Correlatividad;
use App\Models\Materia;


class CorrelatividadController extends Controller
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
        $this->authorize('ver_correlatividades_materias');

        try {
            $materia = Materia::with('correlativas')->findOrFail($id);
            return response()->json([
                'materia' => $materia,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_correlatividades_materias');

        try {
            $materia = Materia::with('correlativas')->findOrFail($id);
            $materias = Materia::where('id', '!=', $id)->where('estado', 'AC')->orderBy('nombre_fantasia', 'asc')->get();
            return view('correlatividades/edit')->with(compact('materia', 'materias'));
        } catch (\Exception $e) {
            return redirect()->route('materias.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_correlatividades_materias');

        $request->validate([
            'materia' => ['required', 'numeric'],
            'detalles.*.materia' => ['nullable', 'numeric', 'different:materia'],
        ]);

        DB::beginTransaction();

        try {
            $correlatividad = Correlatividad::where('materia_id', $id)->delete();
            if ($request->detalles != []) {
                foreach ($request->detalles as $detalle) { //utilizar el array obtenido arriba
                    $correlatividad = new Correlatividad();
                    $correlatividad->materia_id = $id;
                    $correlatividad->correlativa_id = $detalle['materia'];
                    $correlatividad->actualizado_por_id = Auth::id();
                    $correlatividad->save();
                }
            }

            DB::commit();

            return redirect()->route('materias.index')->with('success-message', 'La correlatividad de la materia ' . $correlatividad->materia->nombre_fantasia . ' fue actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('materias.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('editar_correlatividades_materias');

        DB::beginTransaction();

        try {
            $correlatividad = Correlatividad::where('materia_id', $id)->delete();

            DB::commit();

            return redirect()->route('materias.index')->with('error-message', 'Las correlatividades de la materia ' . $correlatividad->materia->nombre_fantasia . ' fueron eliminadas exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('materias.index')->with('error-message', $e->getMessage());
        }

    }
}
