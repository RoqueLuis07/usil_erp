<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use App\Models\ModuloCorrelatividad;
use App\Models\Modulo;


class ModuloCorrelatividadController extends Controller
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
        $this->authorize('ver_correlatividades_modulos_maestrias_ubs');

        try {
            $modulo = Modulo::with('correlativas')->findOrFail($id);
            return response()->json([
                'modulo' => $modulo,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_correlatividades_modulos_maestrias_ubs');

        try {
            $modulo = Modulo::with('correlativas')->findOrFail($id);
            $modulos = Modulo::where('es_maestria', true)->where('id', '!=', $id)->where('estado', 'AC')->orderBy('nombre_fantasia', 'asc')->get();
            return view('ubs/modulos_maestrias/correlatividades/edit')->with(compact('modulo', 'modulos'));
        } catch (\Exception $e) {
            return redirect()->route('modulos_maestrias.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_correlatividades_modulos_maestrias_ubs');

        $request->validate([
            'modulo' => ['required', 'numeric'],
            'detalles.*.modulo' => ['nullable', 'numeric', 'different:modulo'],
        ]);

        DB::beginTransaction();

        try {
            $correlatividad = ModuloCorrelatividad::where('modulo_id', $id)->delete();
            if ($request->detalles != []) {
                foreach ($request->detalles as $detalle) { //utilizar el array obtenido arriba
                    $correlatividad = new ModuloCorrelatividad();
                    $correlatividad->modulo_id = $id;
                    $correlatividad->correlativa_id = $detalle['modulo'];
                    $correlatividad->cargado_por_id = Auth::id();
                    $correlatividad->actualizado_por_id = Auth::id();
                    $correlatividad->save();
                }
            }

            DB::commit();

            return redirect()->route('modulos_maestrias.index')->with('success-message', 'La correlatividad del módulo ' . $correlatividad->modulo->nombre_fantasia . ' fue actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('modulos_maestrias.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('editar_correlatividades_modulos_maestrias_ubs');

        DB::beginTransaction();

        try {
            $correlatividad = ModuloCorrelatividad::where('modulo_id', $id)->delete();

            DB::commit();

            return redirect()->route('modulos_maestrias.index')->with('error-message', 'Las correlatividades del módulo ' . $correlatividad->modulo->nombre_fantasia . ' fueron eliminadas exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('modulos_maestrias.index')->with('error-message', $e->getMessage());
        }

    }
}
