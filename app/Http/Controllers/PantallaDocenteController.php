<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\Models\Docente;
use App\Models\Alumno;
use App\Models\Carrera;
use App\Models\ExtensionUniversitaria;
use App\Models\TipoExtensionUniversitaria;

class PantallaDocenteController extends Controller
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

    public function extensiones_universitarias($id)
    {
        $this->authorize('ver_extensiones_docentes_pantalla');

        try {
            $docente = Docente::where('usuario_id', $id)->firstOrFail();
            $extensiones = ExtensionUniversitaria::with('ExtensionUniversitariaDetalles')->where('docente_id', $docente->id)->get();
            $docente->load('Carreras.Facultad');

            $detalles = $extensiones->flatMap(fn ($e) => $e->ExtensionUniversitariaDetalles);
            $resumen = [
                'proyectos' => $extensiones->count(),
                'estudiantes' => $detalles->where('estado', 'AC')->count(),
                'pendientes' => $detalles->where('estado', 'PE')->count(),
                'horas' => $extensiones->sum('cantidad_horas'),
            ];

            return view('pantallas_docentes/extensiones/index')->with(compact('docente', 'extensiones', 'resumen'));
        } catch (\Exception $e) {
            report($e);
            abort(500, $e->getMessage());
        }
    }

    public function create_extensiones_universitarias($id)
    {
        $this->authorize('crear_extensiones_docentes_pantalla');

        try {
            $docente = Docente::where('usuario_id', $id)->first();
            $tipos_extensiones = TipoExtensionUniversitaria::where('estado', 'AC')->get();
            $alumnos = Alumno::where('estado', 'AC')->orderBy('primer_nombre', 'asc')->get();
            $carreras = Carrera::where('estado', 'AC')->orderBy('nombre_fantasia', 'asc')->get();
            $materias = \App\Models\Materia::where('estado', 'AC')->orderBy('nombre_fantasia', 'asc')->get();
            $problematicas = \App\Models\ExtensionUniversitaria::PROBLEMATICAS;
            $nombres_proyectos = \App\Models\ExtensionUniversitaria::select('nombre')->distinct()->orderBy('nombre')->limit(500)->pluck('nombre');
            return view('pantallas_docentes/extensiones/create')->with(compact('docente', 'tipos_extensiones', 'alumnos', 'carreras', 'materias', 'problematicas', 'nombres_proyectos'));
        } catch (\Exception $e) {
            return redirect()->route('pantallas_docentes.extensiones_universitarias', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function show_extensiones_universitarias($id)
    {
        $this->authorize('ver_extensiones_docentes_pantalla');

        $extension = ExtensionUniversitaria::with('extensionUniversitariaDetalles.alumno')->find($id);
        if (!$extension) {
            return redirect()->route('pantallas_docentes.extensiones_universitarias', Auth::id())->with('error-message', 'La actividad no existe.');
        }

        // Cada docente ve y gestiona sus propias actividades; Extensión, cualquiera.
        $docente = Docente::where('usuario_id', Auth::id())->first();
        $es_admin = Auth::user()->hasAnyRole(['ADMINISTRADOR_EXTENSION', 'SUPERADMIN']);
        $es_propietario = $docente && $extension->docente_id === $docente->id;
        if (!$es_admin && !$es_propietario) {
            abort(403);
        }
        if (!$docente) {
            // Administrador sin ficha de docente: usa la pantalla de Extensión.
            return redirect()->route('extensiones_universitarias.show', $extension->id);
        }

        $puede_gestionar_alumnos = Auth::user()->can('gestionar_postulaciones_extensiones_universitarias');
        $alumnos_disponibles = $puede_gestionar_alumnos ? $extension->alumnosDisponibles() : collect();

        return view('pantallas_docentes/extensiones/show')->with(compact('extension', 'docente', 'puede_gestionar_alumnos', 'alumnos_disponibles'));
    }

}
