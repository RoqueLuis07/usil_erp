<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

use App\Models\RubricaTesisUbs;
use App\Models\RubricaDetalleTesisUbs;
use App\Models\NivelRubricaTesisUbs;
use App\Models\LineaTesisUbs;


class RubricaTesisUbsController extends Controller
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
        //$this->authorize('ver rubricas tesis ubs');

        try {
            $rubricas = RubricaTesisUbs::with('detalles')->orderBy('id', 'desc')->get();
            return view('ubs/tesis/rubricas/index')->with(compact('rubricas'));
        } catch (\Exception $e) {
            return redirect()->route('tesis_parametros_ubs.rubricas_index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        //$this->authorize('ver rubricas tesis ubs');

        try {
            $rubrica = RubricaTesisUbs::with(['detalles' => function ($query) {
                $query->orderBy('id');
            }])->findOrFail($id);
            return view('ubs/tesis/rubricas/show')->with(compact('rubrica'));
        } catch (\Exception $e) {
            return redirect()->route('tesis_parametros_ubs.rubricas_index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        //$this->authorize('crear rubricas tesis ubs');

        try {
            $niveles = NivelRubricaTesisUbs::get();
            $lineas = LineaTesisUbs::where('estado', 'AC')->get();
            return view('ubs/tesis/rubricas/create')->with(compact('niveles', 'lineas'));
        } catch (\Exception $e) {
            return redirect()->route('tesis_parametros_ubs.rubricas_index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        //$this->authorize('crear rubricas tesis ubs');

        $request->validate([
            'nombre' => ['required'],
            'linea' => ['required', 'numeric', Rule::unique('rubricas_tesis_ubs', 'linea_id')],
            'detalles' => ['required', 'array'],
            'detalles.*.nivel' => ['required', 'numeric'],
            'detalles.*.descripcion' => 'required',
            'detalles.*.puntos' => ['required', 'numeric', 'min:1', 'max:5']
        ]);

        DB::beginTransaction();

        try {
            $rubrica = new RubricaTesisUbs();
            $rubrica->nombre = removeAccents(Str::upper($request->nombre));
            $rubrica->linea_id = $request->linea;
            $rubrica->cargado_por_id = Auth::id();
            $rubrica->save();

            foreach ($request->detalles as $detalle) {
                $rubrica_detalle = new RubricaDetalleTesisUbs();
                $rubrica_detalle->rubrica_id = $rubrica->id;
                $rubrica_detalle->nivel_id = $detalle['nivel'];
                $rubrica_detalle->descripcion = removeAccents(Str::upper($detalle['descripcion']));
                $rubrica_detalle->puntos = $detalle['puntos'];
                $rubrica_detalle->save();
            }

            DB::commit();

            return redirect()->route('tesis_parametros_ubs.rubricas_index')->with('success-message', 'La rúbrica fue creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tesis_parametros_ubs.rubricas_index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        //$this->authorize('editar rubricas tesis ubs');

        try {
            $rubrica = RubricaTesisUbs::with(['detalles' => function ($query) {
                $query->orderBy('id');
            }])->findOrFail($id);
            $niveles = NivelRubricaTesisUbs::get();
            $lineas = LineaTesisUbs::where('estado', 'AC')->get();
            return view('ubs/tesis/rubricas/edit')->with(compact('rubrica', 'niveles', 'lineas'));
        } catch (\Exception $e) {
            return redirect()->route('tesis_parametros_ubs.rubricas_index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        //$this->authorize('editar rubricas tesis ubs');

        $request->validate([
            'nombre' => ['required'],
            'linea' => ['required', 'numeric', Rule::unique('rubricas_tesis_ubs', 'linea_id')->ignore($id)],
            'detalles' => ['required', 'array'],
            'detalles.*.nivel' => ['required', 'numeric'],
            'detalles.*.descripcion' => 'required',
            'detalles.*.puntos' => ['required', 'numeric', 'min:1', 'max:5']
        ]);

        DB::beginTransaction();

        try {
            $rubrica = RubricaTesisUbs::findOrFail($id);
            $rubrica->nombre = removeAccents(Str::upper($request->nombre));
            $rubrica->linea_id = $request->linea;
            $rubrica->actualizado_por_id = Auth::id();
            $rubrica->save();

            //obtener el detalle de la malla y eliminar lo que habia para poder crear de vuelta
            $detalles = RubricaDetalleTesisUbs::where('rubrica_id', $id)->delete();

            foreach ($request->detalles as $detalle) { //utilizar el array obtenido arriba
                $rubrica_detalle = new RubricaDetalleTesisUbs();
                $rubrica_detalle->rubrica_id = $rubrica->id;
                $rubrica_detalle->nivel_id = $detalle['nivel'];
                $rubrica_detalle->descripcion = removeAccents(Str::upper($detalle['descripcion']));
                $rubrica_detalle->puntos = $detalle['puntos'];
                $rubrica_detalle->save();
            }

            DB::commit();

            return redirect()->route('tesis_parametros_ubs.rubricas_index')->with('success-message', 'La rúbrica fue actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tesis_parametros_ubs.rubricas_index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        //$this->authorize('inactivar rubricas tesis ubs');

        DB::beginTransaction();

        try {
            $rubrica = RubricaTesisUbs::findOrFail($id);
            $rubrica->actualizado_por_id = Auth::id();
            $rubrica->estado = 'IN';
            $rubrica->save();

            DB::commit();

            return redirect()->route('tesis_parametros_ubs.rubricas_index')->with('error-message','La rúbrica fue inactivada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tesis_parametros_ubs.rubricas_index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        //$this->authorize('activar rubricas tesis ubs');

        DB::beginTransaction();

        try {
            $rubrica = RubricaTesisUbs::findOrFail($id);
            $rubrica->actualizado_por_id = Auth::id();
            $rubrica->estado = 'AC';
            $rubrica->save();

            DB::commit();

            return redirect()->route('tesis_parametros_ubs.rubricas_index')->with('success-message','La rúbrica fue activada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tesis_parametros_ubs.rubricas_index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        //$this->authorize('eliminar rubricas tesis ubs');

        DB::beginTransaction();

        try {
            $rubrica = RubricaTesisUbs::findOrFail($id);

            $rubrica_detalles = RubricaDetalleTesisUbs::where('rubrica_id', $rubrica->id)->delete();

            $rubrica->delete();

            DB::commit();

            return redirect()->route('tesis_parametros_ubs.rubricas_index')->with('success-message','La rúbrica fue eliminada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('tesis_parametros_ubs.rubricas_index')->with('error-message', 'La rúbrica no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('tesis_parametros_ubs.rubricas_index')->with('error-message', $e->getMessage());
            }
        }
    }
}
