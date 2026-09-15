<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use App\Models\Carrera;
use App\Models\Programa;
use App\Models\Facultad;
use App\Models\TipoCarrera;
use App\Models\Modalidad;


class CarreraController extends Controller
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
        $this->authorize('ver_carreras');

        try {
            $carreras = Carrera::orderBy('nombre_fantasia', 'asc')->get();
            return view('carreras/index')->with(compact('carreras'));
        } catch (\Exception $e) {
            return redirect()->route('carreras.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_carreras');

        try {
            $programas = Programa::where('id', '<', 5)->orWhere('id', 8)->where('estado', 'AC')->get();
            $facultades = Facultad::get();
            $tipos_carreras = TipoCarrera::where('estado', 'AC')->get();
            $modalidades = Modalidad::where('estado', 'AC')->get();
            return view('carreras/create')->with(compact('programas', 'facultades', 'tipos_carreras', 'modalidades'));
        } catch (\Exception $e) {
            return redirect()->route('carreras.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_carreras');

        $request->validate([
            'nombre_fantasia' => ['required', Rule::unique('carreras')->where(fn ($query) => $query->where('nombre_fantasia', $request->nombre_fantasia)->where('programa_id', $request->programa))],
            //El de arriba verifica que el conjunto de programa_id y nombre no existan
            'nombre_real' => ['required', Rule::unique('carreras')->where(fn ($query) => $query->where('nombre_real', $request->nombre_real)->where('programa_id', $request->programa))],
            //El de arriba verifica que el conjunto de programa_id y nombre no existan
            'programa' => ['required', 'numeric'],
            'facultad' => ['required', 'numeric'],
            'tipo_carrera' => ['required', 'numeric'],
            'doble_grado' => 'required',
            'cantidad_semestres' => ['required', 'numeric'],
            'modalidad' => ['required', 'numeric'],
            'numero_ley' => ['required', Rule::unique('carreras')],
            'numero_acta' => ['required', Rule::unique('carreras')],
            'numero_resolucion_cones' => ['required', Rule::unique('carreras')],
            'abreviatura' => ['required', 'max:3'],
        ]);

        DB::beginTransaction();

        try {
            $carrera = new Carrera();
            $carrera->nombre_fantasia = removeAccents(Str::upper($request->nombre_fantasia));
            $carrera->nombre_real = removeAccents(Str::upper($request->nombre_real));
            $carrera->programa_id = $request->programa;
            $carrera->facultad_id = $request->facultad;
            $carrera->tipo_carrera_id = $request->tipo_carrera;
            $carrera->doble_grado = $request->doble_grado;
            $carrera->cantidad_semestres = $request->cantidad_semestres;
            $carrera->modalidad_id = $request->modalidad;
            $carrera->numero_ley = removeAccents(Str::upper($request->numero_ley));
            $carrera->numero_acta = removeAccents(Str::upper($request->numero_acta));
            $carrera->numero_resolucion_cones = removeAccents(Str::upper($request->numero_resolucion_cones));
            $carrera->abreviatura = removeAccents(Str::upper($request->abreviatura));
            $carrera->cargado_por_id = Auth::id();
            $carrera->save();

            DB::commit();

            return redirect()->route('carreras.index')->with('success-message', 'La carrera ' . $carrera->nombre_fantasia . ' fue creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('carreras.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_carreras');

        try {
            $carrera = Carrera::findOrFail($id);
            $programas = Programa::where('id', '<', 5)->orWhere('id', 8)->where('estado', 'AC')->get();
            $facultades = Facultad::get();
            $tipos_carreras = TipoCarrera::where('estado', 'AC')->get();
            $modalidades = Modalidad::where('estado', 'AC')->get();
            return view('carreras/edit')->with(compact('carrera', 'programas', 'facultades', 'tipos_carreras', 'modalidades'));
        } catch (\Exception $e) {
            return redirect()->route('carreras.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_carreras');

        $request->validate([
            'nombre_fantasia' => ['required', Rule::unique('carreras')->where(fn ($query) => $query->where('nombre_fantasia', $request->nombre_fantasia)->where('programa_id', $request->programa))->ignore($id)],
            //El de arriba verifica que el conjunto de programa_id y nombre no existan
            'nombre_real' => ['required', Rule::unique('carreras')->where(fn ($query) => $query->where('nombre_real', $request->nombre_real)->where('programa_id', $request->programa))->ignore($id)],
            //El de arriba verifica que el conjunto de programa_id y nombre no existan
            'programa' => ['required', 'numeric'],
            'facultad' => ['required', 'numeric'],
            'tipo_carrera' => ['required', 'numeric'],
            'doble_grado' => 'required',
            'cantidad_semestres' => ['required', 'numeric'],
            'modalidad' => ['required', 'numeric'],
            'numero_ley' => ['required', Rule::unique('carreras')->ignore($id)],
            'numero_acta' => ['required', Rule::unique('carreras')->ignore($id)],
            'numero_resolucion_cones' => ['required', Rule::unique('carreras')->ignore($id)],
            'abreviatura' => ['required', 'max:3'],
        ]);

        DB::beginTransaction();

        try {
            $carrera = Carrera::findOrFail($id);
            $carrera->nombre_fantasia = removeAccents(Str::upper($request->nombre_fantasia));
            $carrera->nombre_real = removeAccents(Str::upper($request->nombre_real));
            $carrera->programa_id = $request->programa;
            $carrera->facultad_id = $request->facultad;
            $carrera->tipo_carrera_id = $request->tipo_carrera;
            $carrera->doble_grado = $request->doble_grado;
            $carrera->cantidad_semestres = $request->cantidad_semestres;
            $carrera->modalidad_id = $request->modalidad;
            $carrera->numero_ley = removeAccents(Str::upper($request->numero_ley));
            $carrera->numero_acta = removeAccents(Str::upper($request->numero_acta));
            $carrera->numero_resolucion_cones = removeAccents(Str::upper($request->numero_resolucion_cones));
            $carrera->abreviatura = removeAccents(Str::upper($request->abreviatura));
            $carrera->actualizado_por_id = Auth::id();
            $carrera->save();

            DB::commit();

            return redirect()->route('carreras.index')->with('success-message', 'La carrera ' . $carrera->nombre_fantasia . ' fue actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('carreras.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_carreras');

        DB::beginTransaction();

        try {
            $carrera = Carrera::findOrFail($id);
            $carrera->actualizado_por_id = Auth::id();
            $carrera->estado = 'IN';
            $carrera->save();

            DB::commit();

            return redirect()->route('carreras.index')->with('error-message','La carrera ' . $carrera->nombre_fantasia . ' fue inactivada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('carreras.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_carreras');

        DB::beginTransaction();

        try {
            $carrera = Carrera::findOrFail($id);
            $carrera->actualizado_por_id = Auth::id();
            $carrera->estado = 'AC';
            $carrera->save();

            DB::commit();

            return redirect()->route('carreras.index')->with('success-message','La carrera ' . $carrera->nombre_fantasia . ' fue activada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('carreras.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_carreras');

        DB::beginTransaction();

        try {
            $carrera = Carrera::findOrFail($id);
            $carrera->delete();

            DB::commit();

            return redirect()->route('carreras.index')->with('success-message','La carrera ' . $carrera->nombre_fantasia . ' fue eliminada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('carreras.index')->with('error-message', 'La carrera ' . $carrera->nombre_fantasia . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('carreras.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
