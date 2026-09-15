<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use App\Models\RequerimientoEntregaTesis;

class RequerimientoEntregaTesisController extends Controller
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

    public function show()
    {
        $this->authorize('ver_requerimientos_entregas_tesis');

        try {
            $requerimiento = RequerimientoEntregaTesis::first();
            if ($requerimiento == null) {
                return redirect()->route('requerimientos_entregas_tesis.create');
            } else {
                return view('tesis/requerimientos/show')->with(compact('requerimiento'));
            }
        } catch (\Exception $e) {
            return redirect()->route('tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('editar_requerimientos_entregas_tesis');

        try {
            return view('tesis/requerimientos/create');
        } catch (\Exception $e) {
            return redirect()->route('tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('editar_requerimientos_entregas_tesis');

        $request->validate([
            'fecha_inicio_anteproyecto' => ['required', 'date', 'after_or_equal:today'],
            'fecha_fin_anteproyecto' => ['required', 'date', 'after:fecha_inicio_anteproyecto'],
            'fecha_inicio_proyecto' => ['required', 'date', 'after:fecha_fin_anteproyecto'],
            'fecha_fin_proyecto' => ['required', 'date', 'after:fecha_inicio_proyecto'],
            'fecha_inicio_borrador' => ['required', 'date', 'after:fecha_fin_proyecto'],
            'fecha_fin_borrador' => ['required', 'date']
        ]);

        DB::beginTransaction();

        try {
            $requerimiento = new RequerimientoEntregaTesis();
            $requerimiento->fecha_inicio_anteproyecto = $request->fecha_inicio_anteproyecto;
            $requerimiento->fecha_fin_anteproyecto = $request->fecha_fin_anteproyecto;
            $requerimiento->fecha_inicio_proyecto = $request->fecha_inicio_proyecto;
            $requerimiento->fecha_fin_proyecto = $request->fecha_fin_proyecto;
            $requerimiento->fecha_inicio_borrador = $request->fecha_inicio_borrador;
            $requerimiento->fecha_fin_borrador = $request->fecha_fin_borrador;
            $requerimiento->actualizado_por_id = Auth::id();
            $requerimiento->save();

            DB::commit();

            return redirect()->route('requerimientos_entregas_tesis.show')->with('success-message', 'Los requerimientos de las entregas tesis fueron creados exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_requerimientos_entregas_tesis');

        try {
            $requerimiento = RequerimientoEntregaTesis::findOrFail($id);
            return view('tesis/requerimientos/edit')->with(compact('requerimiento'));
        } catch (\Exception $e) {
            return redirect()->route('tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_requerimientos_entregas_tesis');

        $request->validate([
            'fecha_inicio_anteproyecto' => ['required', 'date', 'after_or_equal:today'],
            'fecha_fin_anteproyecto' => ['required', 'date', 'after:fecha_inicio_anteproyecto'],
            'fecha_inicio_proyecto' => ['required', 'date', 'after:fecha_fin_anteproyecto'],
            'fecha_fin_proyecto' => ['required', 'date', 'after:fecha_inicio_proyecto'],
            'fecha_inicio_borrador' => ['required', 'date', 'after:fecha_fin_proyecto'],
            'fecha_fin_borrador' => ['required', 'date']
        ]);

        DB::beginTransaction();

        try {
            $requerimiento = RequerimientoEntregaTesis::findOrFail($id);
            $requerimiento->fecha_inicio_anteproyecto = $request->fecha_inicio_anteproyecto;
            $requerimiento->fecha_fin_anteproyecto = $request->fecha_fin_anteproyecto;
            $requerimiento->fecha_inicio_proyecto = $request->fecha_inicio_proyecto;
            $requerimiento->fecha_fin_proyecto = $request->fecha_fin_proyecto;
            $requerimiento->fecha_inicio_borrador = $request->fecha_inicio_borrador;
            $requerimiento->fecha_fin_borrador = $request->fecha_fin_borrador;
            $requerimiento->actualizado_por_id = Auth::id();
            $requerimiento->save();

            DB::commit();

            return redirect()->route('requerimientos_entregas_tesis.show')->with('success-message', 'Los requerimientos de las entregas tesis fueron actualizados exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tesis.index')->with('error-message', $e->getMessage());
        }
    }
}
