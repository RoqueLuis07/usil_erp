<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\FechaDefensaTesis;


class FechaDefensaTesisController extends Controller
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
        $this->authorize('ver_fechas_defensas_tesis');

        try {
            $fechas = FechaDefensaTesis::orderBy('id', 'asc')->get();

            return view('tesis.fechas_defensas.index')->with(compact('fechas'));
        } catch (\Exception $e) {
            return redirect()->route('fechas_defensas_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_fechas_defensas_tesis');

        try {
            $fecha = FechaDefensaTesis::findOrFail($id);

            return view('tesis.fechas_defensas.show')->with(compact('fecha'));
        } catch (\Exception $e) {
            return redirect()->route('fechas_defensas_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_fechas_defensas_tesis');

        try {
            return view('tesis.fechas_defensas.create');
        } catch (Exception $e) {
            return redirect()->route('fechas_defensas_tesis.index')->with('error-message', $e->getMessage());
        }

    }

    public function store(Request $request)
    {
        $this->authorize('crear_fechas_defensas_tesis');

        DB::beginTransaction();

            $request->validate([
                'fecha' => ['required', 'date'],
                'hora' => ['required', 'date_format:H:i', Rule::unique('fechas_defensas_tesis', 'hora')->where(fn ($query) => $query->where('hora', $request->hora)->where('fecha', $request->fecha))],
                //El de arriba verifica que el conjunto de fecha y hora no existan,
            ]);

            try {
                $fecha_defensa = new FechaDefensaTesis();
                $fecha_defensa->fecha = $request->fecha;
                $fecha_defensa->hora = $request->hora;
                $fecha_defensa->cargado_por_id = Auth::id();
                $fecha_defensa->save();

                DB::commit();

                return redirect()->route('fechas_defensas_tesis.index')->with('success-message', 'La fecha de defensa de trabajo final de grado fue creada exitosamente.');
            } catch (\Exception $e) {
                DB::rollback();
                return redirect()->route('fechas_defensas_tesis.index')->with('error-message', $e->getMessage());
            }
    }

    public function edit($id)
    {
        $this->authorize('editar_fechas_defensas_tesis');

        try {
            $fecha = FechaDefensaTesis::findOrFail($id);
            if ($fecha->estado == 'OC') {
                return back()->with('error-message', 'No se puede editar la fecha de defensa de trabajo final de grado. Ya se encuentra asignada a un alumno');
            }

            return view('tesis.fechas_defensas.edit')->with(compact('fecha'));
        } catch (\Exception $e) {
            return redirect()->route('fechas_defensas_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_fechas_defensas_tesis');

        DB::beginTransaction();

        $request->validate([
            'fecha' => ['required', 'date'],
            'hora' => ['required', 'date_format:H:i', Rule::unique('fechas_defensas_tesis', 'hora')->where(fn ($query) => $query->where('hora', $request->hora)->where('fecha', $request->fecha))->ignore($id)],
            //El de arriba verifica que el conjunto de fecha y hora no existan,
        ]);

        try {
                $fecha_defensa = FechaDefensaTesis::findOrFail($id);
                $fecha_defensa->fecha = $request->fecha;
                $fecha_defensa->hora = $request->hora;
                $fecha_defensa->actualizado_por_id = Auth::id();
                $fecha_defensa->save();

            DB::commit();

            return redirect()->route('fechas_defensas_tesis.index')->with('success-message', 'La fecha de defensa de trabajo final de grado fue actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('fechas_defensas_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_fechas_defensas_tesis');

        DB::beginTransaction();

        try {
            $fecha_defensa = FechaDefensaTesis::findOrFail($id);

            if ($fecha_defensa->estado == 'OC') {
                return back()->with('error-message', 'No se puede eliminar la fecha de defensa de trabajo final de grado. Ya se encuentra asignada a un alumno');
            }

            $fecha_defensa->delete();

            DB::commit();

            return redirect()->route('fechas_defensas_tesis.index')->with('error-message', 'La fecha de defensa de trabajo final de grado fue eliminada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('fechas_defensas_tesis.index')->with('error-message', 'La fecha de defensa no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('fechas_defensas_tesis.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
