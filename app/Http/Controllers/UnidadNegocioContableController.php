<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

use App\Models\UnidadNegocioContable;
use App\Models\SubunidadNegocioContable;

class UnidadNegocioContableController extends Controller
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
        $this->authorize('ver_unidades_negocios_contables');

        try {
            $unidades_negocios = UnidadNegocioContable::orderBy('id', 'desc')->get();
            return view('unidades_negocios_contables/index')->with(compact('unidades_negocios'));
        } catch (\Exception $e) {
            return redirect()->route('unidades_negocios_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_unidades_negocios_contables');

        try {
            $unidad_negocio = UnidadNegocioContable::with('subcentrosCostosContables')->findOrFail($id);
            return view('unidades_negocios_contables/show')->with(compact('unidad_negocio'));
        } catch (\Exception $e) {
            return redirect()->route('unidades_negocios_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_unidades_negocios_contables');

        try {
            return view('unidades_negocios_contables/create');
        } catch (\Exception $e) {
            return redirect()->route('unidades_negocios_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_unidades_negocios_contables');

        $request->validate([
            'nombre' => ['required', Rule::unique('unidades_negocios_contables')],
            
            'detalles' => ['required', 'array'],
            'detalles.*.nombre' => ['required'],
        ]);

        DB::beginTransaction();

        try {
            $unidad_negocio = new UnidadNegocioContable();
            $unidad_negocio->nombre = removeAccents(Str::upper($request->nombre));
            $unidad_negocio->cargado_por_id = Auth::id();
            $unidad_negocio->save();

            foreach ($request->detalles as $detalle) {
                $subunidad_negocio = new SubunidadNegocioContable();
                $subunidad_negocio->unidad_id = $unidad_negocio->id;
                $subunidad_negocio->nombre = removeAccents(Str::upper($detalle['nombre']));
                $subunidad_negocio->cargado_por_id = Auth::id();
                $subunidad_negocio->save();
            }

            DB::commit();

            return redirect()->route('unidades_negocios_contables.index')->with('success-message', 'El centro de costo ' . $unidad_negocio->nombre . ' fue creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('unidades_negocios_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_unidades_negocios_contables');

        try {
            $unidad_negocio = UnidadNegocioContable::with('subcentrosCostosContables')->findOrFail($id);
            return view('unidades_negocios_contables/edit')->with(compact('unidad_negocio'));
        } catch (\Exception $e) {
            return redirect()->route('unidades_negocios_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_unidades_negocios_contables');

        $request->validate([
            'nombre' => ['required', Rule::unique('unidades_negocios_contables')->ignore($id)],
            
            'detalles' => ['required', 'array'],
            'detalles.*.nombre' => ['required'],
        ]);

        DB::beginTransaction();

        try {
            $unidad_negocio = UnidadNegocioContable::findOrFail($id);
            $unidad_negocio->nombre = removeAccents(Str::upper($request->nombre));
            $unidad_negocio->actualizado_por_id = Auth::id();
            $unidad_negocio->save();

            //obtener los subcentros y eliminar lo que habia para poder crear de vuelta
            $subunidades_negocios = SubunidadNegocioContable::where('unidad_id', $id)->delete();

            foreach ($request->detalles as $detalle) {
                $subunidad_negocio = new SubunidadNegocioContable();
                $subunidad_negocio->unidad_id = $unidad_negocio->id;
                $subunidad_negocio->nombre = removeAccents(Str::upper($detalle['nombre']));
                $subunidad_negocio->cargado_por_id = Auth::id();
                $subunidad_negocio->save();
            }

            DB::commit();

            return redirect()->route('unidades_negocios_contables.index')->with('success-message', 'El centro de costo ' . $unidad_negocio->nombre . ' fue actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('unidades_negocios_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_unidades_negocios_contables');

        DB::beginTransaction();

        try {
            $unidad_negocio = UnidadNegocioContable::findOrFail($id);
            $unidad_negocio->actualizado_por_id = Auth::id();
            $unidad_negocio->estado = 'IN';
            $unidad_negocio->save();

            DB::commit();

            return redirect()->route('unidades_negocios_contables.index')->with('error-message','El centro de costo ' . $unidad_negocio->nombre . ' fue inactivado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('unidades_negocios_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_unidades_negocios_contables');

        DB::beginTransaction();

        try {
            $unidad_negocio = UnidadNegocioContable::findOrFail($id);
            $unidad_negocio->actualizado_por_id = Auth::id();
            $unidad_negocio->estado = 'AC';
            $unidad_negocio->save();

            DB::commit();

            return redirect()->route('unidades_negocios_contables.index')->with('success-message','El centro de costo ' . $unidad_negocio->nombre . ' fue activado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('unidades_negocios_contables.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_unidades_negocios_contables');

        DB::beginTransaction();

        try {
            $unidad_negocio = UnidadNegocioContable::findOrFail($id);

            $subunidades_negocios = SubunidadNegocioContable::where('unidad_id', $unidad_negocio->id)->delete();

            $unidad_negocio->delete();

            DB::commit();

            return redirect()->route('unidades_negocios_contables.index')->with('success-message','El centro de costo ' . $unidad_negocio->nombre . ' fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('unidades_negocios_contables.index')->with('error-message', 'El centro de costo ' . $unidad_negocio->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('unidades_negocios_contables.index')->with('error-message', $e->getMessage());
            }
        }
    }

    public function change_estado_subunidad($id)
    {
        $this->authorize('cambiar_estado_subunidades_negocios_contables');

        DB::beginTransaction();

        try {
            $subunidad_negocio = SubunidadNegocioContable::findOrFail($id);
            $subunidad_negocio->actualizado_por_id = Auth::id();
            if ($subunidad_negocio->estado == 'AC') {
                $subunidad_negocio->estado = 'IN';
                $tipo = 'inactivado';
            } else {
                $subunidad_negocio->estado = 'AC';
                $tipo = 'activado';
            }
            $subunidad_negocio->save();

            $unidad_negocio = UnidadNegocioContable::findOrFail($subunidad_negocio->unidad_id);

            DB::commit();

            return redirect()->route('unidades_negocios_contables.show', $unidad_negocio->id)->with('success-message','El subcentro de costo ' . $subunidad_negocio->nombre . ' fue ' . $tipo . ' exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            $subunidad_negocio = SubunidadNegocioContable::findOrFail($id);
            $unidad_negocio = UnidadNegocioContable::findOrFail($subunidad_negocio->unidad_id);

            return redirect()->route('unidades_negocios_contables.show')->with('error-message', $e->getMessage());
        }
    }
}
