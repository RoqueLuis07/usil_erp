<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

use App\Models\CentroCostoContable;
use App\Models\SubcentroCostoContable;

class CentroCostoContableController extends Controller
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
        $this->authorize('ver_centros_costos_contables');

        try {
            $centros_costos = CentroCostoContable::orderBy('id', 'desc')->get();
            return view('centros_costos_contables/index')->with(compact('centros_costos'));
        } catch (\Exception $e) {
            return redirect()->route('centros_costos_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_centros_costos_contables');

        try {
            $centro_costo = CentroCostoContable::with('subcentrosCostosContables')->findOrFail($id);
            return view('centros_costos_contables/show')->with(compact('centro_costo'));
        } catch (\Exception $e) {
            return redirect()->route('centros_costos_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_centros_costos_contables');

        try {
            return view('centros_costos_contables/create');
        } catch (\Exception $e) {
            return redirect()->route('centros_costos_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_centros_costos_contables');

        $request->validate([
            'nombre' => ['required', Rule::unique('centros_costos_contables')],
            
            'detalles' => ['required', 'array'],
            'detalles.*.nombre' => ['required'],
        ]);

        DB::beginTransaction();

        try {
            $centro_costo = new CentroCostoContable();
            $centro_costo->nombre = removeAccents(Str::upper($request->nombre));
            $centro_costo->cargado_por_id = Auth::id();
            $centro_costo->save();

            foreach ($request->detalles as $detalle) {
                $subcentro_costo = new SubcentroCostoContable();
                $subcentro_costo->centro_costo_id = $centro_costo->id;
                $subcentro_costo->nombre = removeAccents(Str::upper($detalle['nombre']));
                $subcentro_costo->cargado_por_id = Auth::id();
                $subcentro_costo->save();
            }

            DB::commit();

            return redirect()->route('centros_costos_contables.index')->with('success-message', 'El centro de costo ' . $centro_costo->nombre . ' fue creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('centros_costos_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_centros_costos_contables');

        try {
            $centro_costo = CentroCostoContable::with('subcentrosCostosContables')->findOrFail($id);
            return view('centros_costos_contables/edit')->with(compact('centro_costo'));
        } catch (\Exception $e) {
            return redirect()->route('centros_costos_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_centros_costos_contables');

        $request->validate([
            'nombre' => ['required', Rule::unique('centros_costos_contables')->ignore($id)],
            
            'detalles' => ['required', 'array'],
            'detalles.*.nombre' => ['required'],
        ]);

        DB::beginTransaction();

        try {
            $centro_costo = CentroCostoContable::findOrFail($id);
            $centro_costo->nombre = removeAccents(Str::upper($request->nombre));
            $centro_costo->actualizado_por_id = Auth::id();
            $centro_costo->save();

            //obtener los subcentros y eliminar lo que habia para poder crear de vuelta
            $subcentros_costos = SubcentroCostoContable::where('centro_costo_id', $id)->delete();

            foreach ($request->detalles as $detalle) {
                $subcentro_costo = new SubcentroCostoContable();
                $subcentro_costo->centro_costo_id = $centro_costo->id;
                $subcentro_costo->nombre = removeAccents(Str::upper($detalle['nombre']));
                $subcentro_costo->cargado_por_id = Auth::id();
                $subcentro_costo->save();
            }

            DB::commit();

            return redirect()->route('centros_costos_contables.index')->with('success-message', 'El centro de costo ' . $centro_costo->nombre . ' fue actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('centros_costos_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_centros_costos_contables');

        DB::beginTransaction();

        try {
            $centro_costo = CentroCostoContable::findOrFail($id);
            $centro_costo->actualizado_por_id = Auth::id();
            $centro_costo->estado = 'IN';
            $centro_costo->save();

            DB::commit();

            return redirect()->route('centros_costos_contables.index')->with('error-message','El centro de costo ' . $centro_costo->nombre . ' fue inactivado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('centros_costos_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_centros_costos_contables');

        DB::beginTransaction();

        try {
            $centro_costo = CentroCostoContable::findOrFail($id);
            $centro_costo->actualizado_por_id = Auth::id();
            $centro_costo->estado = 'AC';
            $centro_costo->save();

            DB::commit();

            return redirect()->route('centros_costos_contables.index')->with('success-message','El centro de costo ' . $centro_costo->nombre . ' fue activado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('centros_costos_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_centros_costos_contables');

        DB::beginTransaction();

        try {
            $centro_costo = CentroCostoContable::findOrFail($id);

            $subcentros_costos = SubcentroCostoContable::where('centro_costo_id', $centro_costo->id)->delete();

            $centro_costo->delete();

            DB::commit();

            return redirect()->route('centros_costos_contables.index')->with('success-message','El centro de costo ' . $centro_costo->nombre . ' fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('centros_costos_contables.index')->with('error-message', 'El centro de costo ' . $centro_costo->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('centros_costos_contables.index')->with('error-message', $e->getMessage());
            }
        }
    }

    public function change_estado_subcentro($id)
    {
        $this->authorize('cambiar_estado_subcentros_costos_contables');

        DB::beginTransaction();

        try {
            $subcentro_costo = SubcentroCostoContable::findOrFail($id);
            $subcentro_costo->actualizado_por_id = Auth::id();
            if ($subcentro_costo->estado == 'AC') {
                $subcentro_costo->estado = 'IN';
                $tipo = 'inactivado';
            } else {
                $subcentro_costo->estado = 'AC';
                $tipo = 'activado';
            }
            $subcentro_costo->save();

            $centro_costo = CentroCostoContable::findOrFail($subcentro_costo->centro_costo_id);

            DB::commit();

            return redirect()->route('centros_costos_contables.show', $centro_costo->id)->with('success-message','El subcentro de costo ' . $subcentro_costo->nombre . ' fue ' . $tipo . ' exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            $subcentro_costo = SubcentroCostoContable::findOrFail($id);
            $centro_costo = CentroCostoContable::findOrFail($subcentro_costo->centro_costo_id);

            return redirect()->route('centros_costos_contables.show')->with('error-message', $e->getMessage());
        }
    }
}
