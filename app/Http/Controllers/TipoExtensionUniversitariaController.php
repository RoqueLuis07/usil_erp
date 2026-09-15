<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use App\Models\TipoExtensionUniversitaria;


class TipoExtensionUniversitariaController extends Controller
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
        $this->authorize('ver_tipos_extensiones_universitarias');

        try {
            $tipos_extensiones = TipoExtensionUniversitaria::orderBy('nombre', 'asc')->get();
            return view('extensiones_universitarias/tipos/index')->with(compact('tipos_extensiones'));
        } catch (\Exception $e) {
            return redirect()->route('tipos_extensiones_universitarias.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_tipos_extensiones_universitarias');

        try {
            return view('extensiones_universitarias/tipos/create');
        } catch (\Exception $e) {
            return redirect()->route('tipos_extensiones_universitarias.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_tipos_extensiones_universitarias');

        $request->validate([
            'nombre' => ['required', Rule::unique('tipos_extensiones_universitarias')],
            'maxima_cantidad_horas' => ['required', 'numeric', 'min:1'],
        ]);

        DB::beginTransaction();

        try {
            $tipo_extension = new TipoExtensionUniversitaria();
            $tipo_extension->nombre = removeAccents(Str::upper($request->nombre));
            $tipo_extension->maxima_cantidad_horas = $request->maxima_cantidad_horas;
            $tipo_extension->cargado_por_id = Auth::id();
            $tipo_extension->save();

            DB::commit();

            return redirect()->route('tipos_extensiones_universitarias.index')->with('success-message', 'El tipo de extensión ' . $tipo_extension->nombre . ' fue creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tipos_extensiones_universitarias.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_tipos_extensiones_universitarias');

        try {
            $tipo_extension = TipoExtensionUniversitaria::findOrFail($id);
            return view('extensiones_universitarias/tipos/edit')->with(compact('tipo_extension'));
        } catch (\Exception $e) {
            return redirect()->route('tipos_extensiones_universitarias.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_tipos_extensiones_universitarias');

        $request->validate([
            'nombre' => ['required', Rule::unique('tipos_extensiones_universitarias')->ignore($id)],
            'maxima_cantidad_horas' => ['required', 'numeric', 'min:1'],
        ]);

        DB::beginTransaction();

        try {
            $tipo_extension = TipoExtensionUniversitaria::findOrFail($id);
            $tipo_extension->nombre = removeAccents(Str::upper($request->nombre));
            $tipo_extension->maxima_cantidad_horas = $request->maxima_cantidad_horas;
            $tipo_extension->actualizado_por_id = Auth::id();
            $tipo_extension->save();

            DB::commit();

            return redirect()->route('tipos_extensiones_universitarias.index')->with('success-message', 'El tipo de extensión ' . $tipo_extension->nombre . ' fue actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tipos_extensiones_universitarias.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_tipos_extensiones_universitarias');

        DB::beginTransaction();

        try {
            $tipo_extension = TipoExtensionUniversitaria::findOrFail($id);
            $tipo_extension->actualizado_por_id = Auth::id();
            $tipo_extension->estado = 'IN';
            $tipo_extension->save();

            DB::commit();

            return redirect()->route('tipos_extensiones_universitarias.index')->with('error-message','El tipo de extensión ' . $tipo_extension->nombre . ' fue inactivado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tipos_extensiones_universitarias.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_tipos_extensiones_universitarias');

        DB::beginTransaction();

        try {
            $tipo_extension = TipoExtensionUniversitaria::findOrFail($id);
            $tipo_extension->actualizado_por_id = Auth::id();
            $tipo_extension->estado = 'AC';
            $tipo_extension->save();

            DB::commit();

            return redirect()->route('tipos_extensiones_universitarias.index')->with('success-message','El tipo de extensión ' . $tipo_extension->nombre . ' fue activado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tipos_extensiones_universitarias.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_tipos_extensiones_universitarias');

        DB::beginTransaction();

        try {
            $tipo_extension = TipoExtensionUniversitaria::findOrFail($id);
            $tipo_extension->delete();

            DB::commit();

            return redirect()->route('tipos_extensiones_universitarias.index')->with('success-message','El tipo de extensión ' . $tipo_extension->nombre . ' fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('tipos_extensiones_universitarias.index')->with('error-message', 'El tipo de extensión ' . $tipo_extension->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('tipos_extensiones_universitarias.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
