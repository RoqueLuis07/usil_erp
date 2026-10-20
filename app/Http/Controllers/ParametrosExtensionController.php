<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

use App\Models\Carrera;
use App\Models\Facultad;
use App\Models\Materia;
use App\Models\Modalidad;
use App\Models\Programa;
use App\Models\TipoCarrera;

/**
 * Parámetros editables del módulo de Extensión: facultades y carreras con
 * solo los datos que Extensión necesita. El alta completa de una carrera del
 * ERP exige programa, tipo, modalidad y números de ley/acta/resolución; acá
 * esos datos legales quedan vacíos y los catálogos obligatorios se completan
 * con un valor por defecto (se pueden refinar después desde Parámetros
 * Académicos). También administra las materias y a qué carreras pertenece
 * cada una (las listas de las actividades se filtran con esa relación).
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
        $carreras = Carrera::with('Facultad')->withCount('materias')->orderBy('nombre_fantasia')->get();
        $materias = Materia::with('carreras:id,nombre_fantasia')->orderBy('nombre_fantasia')->get();

        return view('extensiones_universitarias.parametros.index', compact('facultades', 'carreras', 'materias'));
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

    // ------------------------------------------------------------------ eliminar

    public function destroyFacultad($id)
    {
        $this->authorize('gestionar_parametros_extensiones_universitarias');

        $facultad = Facultad::withCount('carreras')->findOrFail($id);
        if ($facultad->carreras_count > 0) {
            return redirect()->route('parametros_extension.index')->with('error-message', 'No se puede eliminar la facultad ' . $facultad->nombre . ': tiene ' . $facultad->carreras_count . ' carrera(s). Mové o eliminá las carreras primero, o inactivá la facultad.');
        }

        return $this->eliminar($facultad, 'La facultad ' . $facultad->nombre . ' fue eliminada.', 'La facultad ' . $facultad->nombre . ' se usa en otros datos: inactivala en vez de eliminarla.');
    }

    public function destroyCarrera($id)
    {
        $this->authorize('gestionar_parametros_extensiones_universitarias');

        $carrera = Carrera::findOrFail($id);

        $uso = array_filter([
            'alumnos' => DB::table('alumnos')->where('carrera_id', $id)->count(),
            'participaciones en actividades' => DB::table('extensiones_universitarias_detalles')->where('carrera_id', $id)->count(),
            'actividades que la habilitan' => DB::table('extension_universitaria_carreras')->where('carrera_id', $id)->count(),
            'docentes' => DB::table('docentes_carreras')->where('carrera_id', $id)->count(),
            'requisitos de extensión' => DB::table('requerimientos_extensiones_universitarias')->where('carrera_id', $id)->count(),
        ]);
        if ($uso) {
            return redirect()->route('parametros_extension.index', ['pestana' => 'carreras'])->with('error-message', 'No se puede eliminar la carrera ' . $carrera->nombre_fantasia . ': se usa en ' . $this->describirUso($uso) . '. Inactivala en vez de eliminarla.');
        }

        return $this->eliminar($carrera, 'La carrera ' . $carrera->nombre_fantasia . ' fue eliminada.', 'La carrera ' . $carrera->nombre_fantasia . ' se usa en otros datos: inactivala en vez de eliminarla.', 'carreras');
    }

    // ------------------------------------------------------------------ materias

    public function storeMateria(Request $request)
    {
        $this->authorize('gestionar_parametros_extensiones_universitarias');

        $datos = $this->validarMateria($request);
        if (Materia::whereRaw('LOWER(nombre_fantasia) = ?', [mb_strtolower($datos['nombre'])])->exists()) {
            return back()->withInput()->withErrors(['nombre' => 'Ya existe una materia con ese nombre.']);
        }

        DB::beginTransaction();
        try {
            $materia = new Materia();
            $materia->nombre_fantasia = $datos['nombre'];
            $materia->nombre_real = $datos['nombre'];
            $materia->codigo = $datos['codigo'] ?? null;
            $materia->carga_horaria = $datos['carga_horaria'] ?? 0;
            $materia->cargado_por_id = Auth::id();
            $materia->save();
            $materia->carreras()->sync($datos['carreras'] ?? []);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error-message', $e->getMessage());
        }

        return redirect()->route('parametros_extension.index', ['pestana' => 'materias'])->with('success-message', 'La materia ' . $materia->nombre_fantasia . ' fue creada.');
    }

    public function updateMateria(Request $request, $id)
    {
        $this->authorize('gestionar_parametros_extensiones_universitarias');

        $datos = $this->validarMateria($request);
        if (Materia::whereRaw('LOWER(nombre_fantasia) = ?', [mb_strtolower($datos['nombre'])])->where('id', '!=', $id)->exists()) {
            return back()->withInput()->withErrors(['nombre' => 'Ya existe otra materia con ese nombre.']);
        }

        DB::beginTransaction();
        try {
            $materia = Materia::findOrFail($id);
            $materia->nombre_fantasia = $datos['nombre'];
            $materia->nombre_real = $datos['nombre'];
            $materia->codigo = $datos['codigo'] ?? null;
            $materia->carga_horaria = $datos['carga_horaria'] ?? 0;
            $materia->actualizado_por_id = Auth::id();
            $materia->save();
            $materia->carreras()->sync($datos['carreras'] ?? []);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error-message', $e->getMessage());
        }

        return redirect()->route('parametros_extension.index', ['pestana' => 'materias'])->with('success-message', 'La materia fue actualizada.');
    }

    public function toggleMateria($id)
    {
        $this->authorize('gestionar_parametros_extensiones_universitarias');

        $materia = Materia::findOrFail($id);
        $materia->estado = $materia->estado === 'AC' ? 'IN' : 'AC';
        $materia->actualizado_por_id = Auth::id();
        $materia->save();

        return redirect()->route('parametros_extension.index', ['pestana' => 'materias'])->with('success-message', 'La materia ' . $materia->nombre_fantasia . ($materia->estado === 'AC' ? ' fue activada.' : ' fue inactivada.'));
    }

    public function destroyMateria($id)
    {
        $this->authorize('gestionar_parametros_extensiones_universitarias');

        $materia = Materia::findOrFail($id);

        $actividades = DB::table('extensiones_universitarias')->where('materia_id', $id)->count();
        if ($actividades) {
            return redirect()->route('parametros_extension.index', ['pestana' => 'materias'])->with('error-message', 'No se puede eliminar la materia ' . $materia->nombre_fantasia . ': está usada en ' . $actividades . ' actividad(es). Inactivala en vez de eliminarla.');
        }

        return $this->eliminar($materia, 'La materia ' . $materia->nombre_fantasia . ' fue eliminada.', 'La materia ' . $materia->nombre_fantasia . ' está usada en actividades: inactivala en vez de eliminarla.', 'materias');
    }

    /** "12 alumnos, 3 docentes" a partir de ['alumnos' => 12, 'docentes' => 3]. */
    private function describirUso(array $uso): string
    {
        return implode(', ', array_map(fn ($etiqueta, $cantidad) => $cantidad . ' ' . $etiqueta, array_keys($uso), $uso));
    }

    private function validarMateria(Request $request): array
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'codigo' => ['nullable', 'string', 'max:50'],
            'carga_horaria' => ['nullable', 'integer', 'min:0', 'max:5000'],
            'carreras' => ['nullable', 'array'],
            'carreras.*' => ['integer', 'exists:carreras,id'],
        ]);
        $datos['nombre'] = trim(preg_replace('/\s+/', ' ', $datos['nombre']));

        return $datos;
    }

    /** Elimina un registro; si otros datos lo usan (clave foránea) lo informa en vez de fallar. */
    private function eliminar($registro, string $exito, string $enUso, ?string $pestana = null)
    {
        $ruta = $pestana ? ['pestana' => $pestana] : [];

        DB::beginTransaction();
        try {
            $registro->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('parametros_extension.index', $ruta)->with('error-message', $enUso);
        }

        return redirect()->route('parametros_extension.index', $ruta)->with('success-message', $exito);
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
