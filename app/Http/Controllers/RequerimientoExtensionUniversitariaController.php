<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use App\Models\RequerimientoExtensionUniversitaria;
use App\Models\Carrera;

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
     * Listado de requerimientos: uno "general" (carrera_id null, usado como
     * respaldo) y opcionalmente uno por carrera. Antes solo podía existir
     * una única fila global para todo el sistema.
     */
    public function show()
    {
        $this->authorize('ver_requerimientos_extensiones_universitarias');

        try {
            $requerimientos = RequerimientoExtensionUniversitaria::with('carrera')
                ->orderByRaw('carrera_id IS NULL DESC')
                ->orderBy('carrera_id')
                ->get();

            if ($requerimientos->count() == 0) {
                return redirect()->route('requerimientos_extensiones_universitarias.create');
            }

            return view('extensiones_universitarias/requerimientos/show')->with(compact('requerimientos'));
        } catch (\Exception $e) {
            return redirect()->route('parametros_academicos.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('editar_requerimientos_extensiones_universitarias');

        try {
            $carreras = Carrera::where('estado', 'AC')
                ->whereDoesntHave('RequerimientoExtensionUniversitaria')
                ->orderBy('nombre_fantasia', 'asc')
                ->get();
            $hay_general = RequerimientoExtensionUniversitaria::whereNull('carrera_id')->exists();
            return view('extensiones_universitarias/requerimientos/create')->with(compact('carreras', 'hay_general'));
        } catch (\Exception $e) {
            return redirect()->route('parametros_academicos.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('editar_requerimientos_extensiones_universitarias');

        $request->validate([
            'carrera' => ['nullable', 'numeric', 'exists:carreras,id'],
            'actividades_requeridas' => ['required', 'numeric', 'min:1'],
            'horas_requeridas' => ['required', 'numeric', 'min:1'],
        ]);

        DB::beginTransaction();

        try {
            $existe = RequerimientoExtensionUniversitaria::where('carrera_id', $request->carrera)->exists();
            if ($existe) {
                DB::rollback();
                $mensaje = $request->carrera ? 'Esa carrera ya tiene un requerimiento definido.' : 'Ya existe un requerimiento general.';
                return back()->with('error-message', $mensaje)->withInput();
            }

            $requerimiento = new RequerimientoExtensionUniversitaria();
            $requerimiento->carrera_id = $request->carrera;
            $requerimiento->actividades_requeridas = $request->actividades_requeridas;
            $requerimiento->horas_requeridas = $request->horas_requeridas;
            $requerimiento->cargado_por_id = Auth::id();
            $requerimiento->save();

            DB::commit();

            return redirect()->route('requerimientos_extensiones_universitarias.show')->with('success-message', 'El requerimiento de extensión universitaria fue creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('parametros_academicos.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_requerimientos_extensiones_universitarias');

        try {
            $requerimiento = RequerimientoExtensionUniversitaria::with('carrera')->findOrFail($id);
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

            return redirect()->route('requerimientos_extensiones_universitarias.show')->with('success-message', 'El requerimiento de extensión universitaria fue actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('parametros_academicos.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('editar_requerimientos_extensiones_universitarias');

        try {
            $requerimiento = RequerimientoExtensionUniversitaria::findOrFail($id);
            $requerimiento->delete();

            return redirect()->route('requerimientos_extensiones_universitarias.show')->with('success-message', 'El requerimiento de extensión universitaria fue eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('requerimientos_extensiones_universitarias.show')->with('error-message', $e->getMessage());
        }
    }
}
