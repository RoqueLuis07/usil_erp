<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use App\Models\RequerimientoExtensionUniversitaria;


class RequerimientoExtensionUniversitariaController extends Controller
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
        $this->authorize('ver_requerimientos_extensiones_universitarias');

        try {
            $requerimiento = RequerimientoExtensionUniversitaria::first();
            if ($requerimiento == null) {
                return redirect()->route('requerimientos_extensiones_universitarias.create');
            } else {
                return view('extensiones_universitarias/requerimientos/show')->with(compact('requerimiento'));
            }
        } catch (\Exception $e) {
            return redirect()->route('parametros_academicos.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('editar_requerimientos_extensiones_universitarias');

        try {
            return view('extensiones_universitarias/requerimientos/create');
        } catch (\Exception $e) {
            return redirect()->route('parametros_academicos.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('editar_requerimientos_extensiones_universitarias');

        $request->validate([
            'actividades_requeridas' => ['required', 'numeric', 'min:1'],
            'horas_requeridas' => ['required', 'numeric', 'min:1'],
        ]);

        DB::beginTransaction();

        try {
            $requerimiento = new RequerimientoExtensionUniversitaria();
            $requerimiento->actividades_requeridas = $request->actividades_requeridas;
            $requerimiento->horas_requeridas = $request->horas_requeridas;
            $requerimiento->cargado_por_id = Auth::id();
            $requerimiento->save();

            DB::commit();

            return redirect()->route('requerimientos_extensiones_universitarias.show')->with('success-message', 'Los requerimientos de las extensiones universitarias fueron creados exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('parametros_academicos.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_requerimientos_extensiones_universitarias');

        try {
            $requerimiento = RequerimientoExtensionUniversitaria::findOrFail($id);
            return view('extensiones_universitarias/requerimientos/edit')->with(compact('requerimiento'));
        } catch (\Exception $e) {
            return redirect()->route('parametros_academicos.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_requerimientos_extensiones_universitarias');

        $request->validate([
            'actividades_requeridas' => ['required', 'numeric', 'min:1'],
            'horas_requeridas' => ['required', 'numeric', 'min:1'],
        ]);

        DB::beginTransaction();

        try {
            $requerimiento = RequerimientoExtensionUniversitaria::findOrFail($id);
            $requerimiento->actividades_requeridas = $request->actividades_requeridas;
            $requerimiento->horas_requeridas = $request->horas_requeridas;
            $requerimiento->actualizado_por_id = Auth::id();
            $requerimiento->save();

            DB::commit();

            return redirect()->route('requerimientos_extensiones_universitarias.show')->with('success-message', 'Los requerimientos de las extensiones universitarias fueron actualizados exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('parametros_academicos.index')->with('error-message', $e->getMessage());
        }
    }
}
