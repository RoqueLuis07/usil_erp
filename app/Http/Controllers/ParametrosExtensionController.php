<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

use App\Models\Carrera;
use App\Models\Facultad;
use App\Models\Modalidad;
use App\Models\Programa;
use App\Models\TipoCarrera;

/**
 * Parámetros editables del módulo de Extensión: facultades y carreras con
 * solo los datos que Extensión necesita. El alta completa de una carrera del
 * ERP exige programa, tipo, modalidad y números de ley/acta/resolución; acá
 * esos datos legales quedan vacíos y los catálogos obligatorios se completan
 * con un valor por defecto (se pueden refinar después desde Parámetros
 * Académicos).
 */
class ParametrosExtensionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->authorize('gestionar_parametros_extensiones_universitarias');

        $facultades = Facultad::withCount('carreras')->orderBy('nombre')->get();
        $carreras = Carrera::with('Facultad')->orderBy('nombre_fantasia')->get();

        return view('extensiones_universitarias.parametros.index', compact('facultades', 'carreras'));
    }

    public function storeFacultad(Request $request)
    {
        $this->authorize('gestionar_parametros_extensiones_universitarias');

        $request->validate([
            'nombre' => ['required', 'string', 'max:150', Rule::unique('facultades', 'nombre')],
        ], ['nombre.unique' => 'Ya existe una facultad con ese nombre.']);

        $facultad = new Facultad();
        $facultad->nombre = removeAccents(Str::upper($request->nombre));
        $facultad->cargado_por_id = Auth::id();
        $facultad->save();

        return redirect()->route('parametros_extension.index')->with('success-message', 'La facultad ' . $facultad->nombre . ' fue creada.');
    }

    public function updateFacultad(Request $request, $id)
    {
        $this->authorize('gestionar_parametros_extensiones_universitarias');

        $request->validate([
            'nombre' => ['required', 'string', 'max:150', Rule::unique('facultades', 'nombre')->ignore($id)],
        ], ['nombre.unique' => 'Ya existe una facultad con ese nombre.']);

        $facultad = Facultad::findOrFail($id);
        $facultad->nombre = removeAccents(Str::upper($request->nombre));
        $facultad->actualizado_por_id = Auth::id();
        $facultad->save();

        return redirect()->route('parametros_extension.index')->with('success-message', 'La facultad fue actualizada.');
    }

    public function toggleFacultad($id)
    {
        $this->authorize('gestionar_parametros_extensiones_universitarias');

        $facultad = Facultad::findOrFail($id);
        $facultad->estado = $facultad->estado === 'AC' ? 'IN' : 'AC';
        $facultad->actualizado_por_id = Auth::id();
        $facultad->save();

        return redirect()->route('parametros_extension.index')->with('success-message', 'La facultad ' . $facultad->nombre . ($facultad->estado === 'AC' ? ' fue activada.' : ' fue inactivada.'));
    }

    public function storeCarrera(Request $request)
    {
        $this->authorize('gestionar_parametros_extensiones_universitarias');

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'abreviatura' => ['required', 'string', 'max:10'],
            'facultad' => ['required', 'numeric', 'exists:facultades,id'],
            'cantidad_semestres' => ['required', 'integer', 'between:1,20'],
        ]);

        $nombre = removeAccents(Str::upper($datos['nombre']));
        if (Carrera::where('nombre_fantasia', $nombre)->where('facultad_id', $datos['facultad'])->exists()) {
            return back()->withInput()->withErrors(['nombre' => 'Esa carrera ya existe en la facultad elegida.']);
        }

        DB::beginTransaction();
        try {
            $carrera = new Carrera();
            $carrera->nombre_fantasia = $nombre;
            $carrera->nombre_real = $nombre;
            $carrera->abreviatura = removeAccents(Str::upper($datos['abreviatura']));
            $carrera->facultad_id = $datos['facultad'];
            $carrera->cantidad_semestres = $datos['cantidad_semestres'];
            $carrera->doble_grado = false;
            $carrera->programa_id = $this->programaPorDefecto()->id;
            $carrera->tipo_carrera_id = $this->tipoCarreraPorDefecto()->id;
            $carrera->modalidad_id = $this->modalidadPorDefecto()->id;
            $carrera->cargado_por_id = Auth::id();
            $carrera->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error-message', $e->getMessage());
        }

        return redirect()->route('parametros_extension.index')->with('success-message', 'La carrera ' . $carrera->nombre_fantasia . ' fue creada.');
    }

    public function updateCarrera(Request $request, $id)
    {
        $this->authorize('gestionar_parametros_extensiones_universitarias');

        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'abreviatura' => ['required', 'string', 'max:10'],
            'facultad' => ['required', 'numeric', 'exists:facultades,id'],
            'cantidad_semestres' => ['required', 'integer', 'between:1,20'],
        ]);

        $nombre = removeAccents(Str::upper($datos['nombre']));
        $repetida = Carrera::where('nombre_fantasia', $nombre)->where('facultad_id', $datos['facultad'])->where('id', '!=', $id)->exists();
        if ($repetida) {
            return back()->withInput()->withErrors(['nombre' => 'Esa carrera ya existe en la facultad elegida.']);
        }

        $carrera = Carrera::findOrFail($id);
        $carrera->nombre_fantasia = $nombre;
        $carrera->abreviatura = removeAccents(Str::upper($datos['abreviatura']));
        $carrera->facultad_id = $datos['facultad'];
        $carrera->cantidad_semestres = $datos['cantidad_semestres'];
        $carrera->actualizado_por_id = Auth::id();
        $carrera->save();

        return redirect()->route('parametros_extension.index')->with('success-message', 'La carrera fue actualizada.');
    }

    public function toggleCarrera($id)
    {
        $this->authorize('gestionar_parametros_extensiones_universitarias');

        $carrera = Carrera::findOrFail($id);
        $carrera->estado = $carrera->estado === 'AC' ? 'IN' : 'AC';
        $carrera->actualizado_por_id = Auth::id();
        $carrera->save();

        return redirect()->route('parametros_extension.index')->with('success-message', 'La carrera ' . $carrera->nombre_fantasia . ($carrera->estado === 'AC' ? ' fue activada.' : ' fue inactivada.'));
    }

    private function programaPorDefecto(): Programa
    {
        $programa = Programa::where('nombre', 'GRADO')->first();
        if (!$programa) {
            $programa = new Programa();
            $programa->nombre = 'GRADO';
            $programa->cantidad_creditos = 0;
            $programa->duracion = 0;
            $programa->turno = 'M';
            $programa->cargado_por_id = Auth::id();
            $programa->save();
        }
        return $programa;
    }

    private function tipoCarreraPorDefecto(): TipoCarrera
    {
        $tipo = TipoCarrera::where('nombre', 'LICENCIATURA')->first();
        if (!$tipo) {
            $tipo = new TipoCarrera();
            $tipo->nombre = 'LICENCIATURA';
            $tipo->cargado_por_id = Auth::id();
            $tipo->save();
        }
        return $tipo;
    }

    private function modalidadPorDefecto(): Modalidad
    {
        $modalidad = Modalidad::where('nombre', 'PRESENCIAL')->first();
        if (!$modalidad) {
            $modalidad = new Modalidad();
            $modalidad->nombre = 'PRESENCIAL';
            $modalidad->save();
        }
        return $modalidad;
    }
}
