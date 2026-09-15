<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

use App\Models\RubricaTesis;
use App\Models\RubricaDetalleTesis;
use App\Models\NivelRubricaTesis;
use App\Models\TipoTesis;


class RubricaTesisController extends Controller
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
        $this->authorize('ver_rubricas_tesis');

        try {
            $rubricas = RubricaTesis::with('detalles')->orderBy('id', 'desc')->get();
            return view('tesis/rubricas/index')->with(compact('rubricas'));
        } catch (\Exception $e) {
            return redirect()->route('rubricas_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_rubricas_tesis');

        try {
            $rubrica = RubricaTesis::with(['detalles' => function ($query) {
                $query->orderBy('id');
            }])->findOrFail($id);
            return view('tesis/rubricas/show')->with(compact('rubrica'));
        } catch (\Exception $e) {
            return redirect()->route('rubricas_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_rubricas_tesis');

        try {
            $niveles = NivelRubricaTesis::get();
            $tipos = TipoTesis::where('estado', 'AC')->get();
            return view('tesis/rubricas/create')->with(compact('niveles', 'tipos'));
        } catch (\Exception $e) {
            return redirect()->route('rubricas_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_rubricas_tesis');

        $request->validate([
            'nombre' => ['required'],
            'tipo' => ['required', 'numeric', Rule::unique('rubricas_tesis', 'tipo_id')],
            'detalles' => ['required', 'array'],
            'detalles.*.nivel' => ['required', 'numeric'],
            'detalles.*.descripcion' => 'required',
            'detalles.*.puntos' => ['required', 'numeric', 'min:1', 'max:5']
        ]);

        DB::beginTransaction();

        try {
            $rubrica = new RubricaTesis();
            $rubrica->nombre = removeAccents(Str::upper($request->nombre));
            $rubrica->tipo_id = $request->tipo;
            $rubrica->cargado_por_id = Auth::id();
            $rubrica->save();

            foreach ($request->detalles as $detalle) {
                $rubrica_detalle = new RubricaDetalleTesis();
                $rubrica_detalle->rubrica_id = $rubrica->id;
                $rubrica_detalle->nivel_id = $detalle['nivel'];
                $rubrica_detalle->descripcion = removeAccents(Str::upper($detalle['descripcion']));
                $rubrica_detalle->puntos = $detalle['puntos'];
                $rubrica_detalle->save();
            }

            DB::commit();

            return redirect()->route('rubricas_tesis.index')->with('success-message', 'La rúbrica fue creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('rubricas_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_rubricas_tesis');

        try {
            $rubrica = RubricaTesis::with(['detalles' => function ($query) {
                $query->orderBy('id');
            }])->findOrFail($id);
            $niveles = NivelRubricaTesis::get();
            $tipos = TipoTesis::where('estado', 'AC')->get();
            return view('tesis/rubricas/edit')->with(compact('rubrica', 'niveles', 'tipos'));
        } catch (\Exception $e) {
            return redirect()->route('rubricas_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_rubricas_tesis');

        $request->validate([
            'nombre' => ['required'],
            'tipo' => ['required', 'numeric', Rule::unique('rubricas_tesis', 'tipo_id')->ignore($id)],
            'detalles' => ['required', 'array'],
            'detalles.*.nivel' => ['required', 'numeric'],
            'detalles.*.descripcion' => 'required',
            'detalles.*.puntos' => ['required', 'numeric', 'min:1', 'max:5']
        ]);

        DB::beginTransaction();

        try {
            $rubrica = RubricaTesis::findOrFail($id);
            $rubrica->nombre = removeAccents(Str::upper($request->nombre));
            $rubrica->tipo_id = $request->tipo;
            $rubrica->actualizado_por_id = Auth::id();
            $rubrica->save();

            //obtener el detalle de la malla y eliminar lo que habia para poder crear de vuelta
            $detalles = RubricaDetalleTesis::where('rubrica_id', $id)->delete();

            foreach ($request->detalles as $detalle) { //utilizar el array obtenido arriba
                $rubrica_detalle = new RubricaDetalleTesis();
                $rubrica_detalle->rubrica_id = $rubrica->id;
                $rubrica_detalle->nivel_id = $detalle['nivel'];
                $rubrica_detalle->descripcion = removeAccents(Str::upper($detalle['descripcion']));
                $rubrica_detalle->puntos = $detalle['puntos'];
                $rubrica_detalle->save();
            }

            DB::commit();

            return redirect()->route('rubricas_tesis.index')->with('success-message', 'La rúbrica fue actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('rubricas_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_rubricas_tesis');

        DB::beginTransaction();

        try {
            $rubrica = RubricaTesis::findOrFail($id);
            $rubrica->actualizado_por_id = Auth::id();
            $rubrica->estado = 'IN';
            $rubrica->save();

            DB::commit();

            return redirect()->route('rubricas_tesis.index')->with('error-message','La rúbrica fue inactivada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('rubricas_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_rubricas_tesis');

        DB::beginTransaction();

        try {
            $rubrica = RubricaTesis::findOrFail($id);
            $rubrica->actualizado_por_id = Auth::id();
            $rubrica->estado = 'AC';
            $rubrica->save();

            DB::commit();

            return redirect()->route('rubricas_tesis.index')->with('success-message','La rúbrica fue activada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('rubricas_tesis.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_rubricas_tesis');

        DB::beginTransaction();

        try {
            $rubrica = RubricaTesis::findOrFail($id);

            $rubrica_detalles = RubricaDetalleTesis::where('rubrica_id', $rubrica->id)->delete();

            $rubrica->delete();

            DB::commit();

            return redirect()->route('rubricas_tesis.index')->with('success-message','La rúbrica fue eliminada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('rubricas_tesis.index')->with('error-message', 'La rúbrica no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('rubricas_tesis.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
