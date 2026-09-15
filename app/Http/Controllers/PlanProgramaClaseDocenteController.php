<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

use App\Models\Docente;
use App\Models\Semestre;
use App\Models\SemestreMalla;
use App\Models\SemestreMallaMateria;

class PlanProgramaClaseDocenteController extends Controller
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
        if ($this->authorize('ver_planes_clases_docentes_pantalla') || $this->authorize('ver_programas_clases_docentes_pantalla')) {
            $semestre = Semestre::where('estado', 'AC')->orderBy('id', 'desc')->first();
            $periodo_activo = $semestre ? $semestre->id : null;
            $docente = Docente::where('usuario_id', Auth::id())->first();

            $materias = collect();

            $semestre_mallas = SemestreMalla::whereHas('semestreMallaMaterias', function ($query) use ($docente) {
                $query->where('docente_id', $docente->id);
            })->with(['semestreMallaMaterias' => function ($query) use ($docente) {
                $query->where('docente_id', $docente->id);
            }])
                ->where('semestre_id', $periodo_activo)
                ->get();

            foreach ($semestre_mallas as $sm) {
                foreach ($sm->semestreMallaMaterias as $smm) {
                    if (!str_contains($smm->materia->nombre_real, 'TRABAJO FINAL DE GRADO') || !str_contains($smm->materia->nombre_fantasia, 'TRABAJO FINAL DE GRADO')) {
                        $materias->push($smm);
                    }
                }
            }

            return view('pantallas_docentes/planes_programas_clases.index')->with(compact('semestre', 'docente', 'materias'));
        } else {
            abort(403);
        }
    }

    public function store_planes_clases(Request $request, $id)
    {
        $this->authorize('adjuntar_planes_clases_docentes_pantalla');

        $request->validate([
            'adjunto' => ['required', 'extensions:pdf'],
        ]);

        try {
            $semestre_malla_materia = SemestreMallaMateria::findOrFail($id);

            $archivo = $request->adjunto;
            $extension = $archivo->getClientOriginalExtension();

            $directorio = 'public/planes_clases';
            // Crear el directorio si no existe
            if (!File::exists($directorio)) {
                File::makeDirectory($directorio, 0755, true);
            }

            $nombre = $semestre_malla_materia->id . '_' . Str::replace('.', '_', $semestre_malla_materia->semestreMalla->semestre->nombre) . '-' . Carbon::now()->format('YmdHis');
            $nombre_archivo = $nombre . '.' . $extension;
            Storage::putFileAs($directorio . '/', $archivo, $nombre_archivo);

            $semestre_malla_materia->url_plan_clasess = Str::replace('public', 'storage', $directorio) . '/' . $nombre_archivo;
            $semestre_malla_materia->save();

            return response()->json([
                'message' => 'El plan de clase de la materia ' . $semestre_malla_materia->materia->nombre_fantasia . ' fue subido exitosamente.',
            ]);
        } catch (\Exception $e) {
            return redirect()->route('planes_programas_clases_docentes.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function store_programas_clases(Request $request, $id)
    {
        $this->authorize('adjuntar_programas_clases_docentes_pantalla');

        $request->validate([
            'adjunto' => ['required', 'extensions:pdf'],
        ]);

        try {
            $semestre_malla_materia = SemestreMallaMateria::findOrFail($id);

            $archivo = $request->adjunto;
            $extension = $archivo->getClientOriginalExtension();

            $directorio = 'public/programas_clases';
            // Crear el directorio si no existe
            if (!File::exists($directorio)) {
                File::makeDirectory($directorio, 0755, true);
            }

            $nombre = $semestre_malla_materia->id . '_' . Str::replace('.', '_', $semestre_malla_materia->semestreMalla->semestre->nombre) . '-' . Carbon::now()->format('YmdHis');
            $nombre_archivo = $nombre . '.' . $extension;
            Storage::putFileAs($directorio . '/', $archivo, $nombre_archivo);

            $semestre_malla_materia->url_programa_clasess = Str::replace('public', 'storage', $directorio) . '/' . $nombre_archivo;
            $semestre_malla_materia->save();

            return response()->json([
                'message' => 'El programa de estudio de la materia ' . $semestre_malla_materia->materia->nombre_fantasia . ' fue subido exitosamente.',
            ]);
        } catch (\Exception $e) {
            return redirect()->route('planes_programas_clases_docentes.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function copy_planes_clases(Request $request, $id)
    {
        $this->authorize('adjuntar_planes_clases_docentes_pantalla');

        try {
            $semestre_malla_materia = SemestreMallaMateria::findOrFail($id);
            $semestre_malla_materia_anterior = SemestreMallaMateria::whereHas('semestreMalla', function ($query) use ($semestre_malla_materia) {
                $query->where('malla_id', $semestre_malla_materia->semestreMalla->malla_id)
                    ->where('semestre_id', '<', $semestre_malla_materia->semestreMalla->semestre_id);
            })->where('materia_id', $semestre_malla_materia->materia_id)
                ->orderBy('id', 'desc')
                ->first();

            if (!$semestre_malla_materia_anterior || !$semestre_malla_materia_anterior->url_plan_clases) {
                return back()->with('error-message', 'No se encontró un plan de clase anterior para la materia ' . $semestre_malla_materia->materia->nombre_fantasia . '.');
            }


            $storage = Storage::disk('public');
            $nombre_viejo = $semestre_malla_materia_anterior->url_plan_clases;
            $nombre_viejo = str_replace(['storage/', 'public/', 'app/'], '', $nombre_viejo);
            if (!$storage->exists($nombre_viejo)) {
                return back()->with('error-message', 'El archivo del plan de clase anterior no existe.');
            }

            $nombre_nuevo = $semestre_malla_materia->id . '_' . Str::replace('.', '_', $semestre_malla_materia->semestreMalla->semestre->nombre) . '-' . Carbon::now()->format('YmdHis');
            $nombre_nuevo = $nombre_nuevo . '.' . pathinfo($semestre_malla_materia_anterior->url_plan_clases, PATHINFO_EXTENSION);
            $nombre_nuevo_archivo = 'planes_clases/' . $nombre_nuevo;
            $storage->copy($nombre_viejo, $nombre_nuevo_archivo);

            $semestre_malla_materia->url_plan_clases = 'storage/planes_clases/' . $nombre_nuevo;
            $semestre_malla_materia->save();

            return redirect()->route('planes_programas_clases_docentes.index', Auth::id())->with('success-message', 'El plan de clase de la materia ' . $semestre_malla_materia->materia->nombre_fantasia . ' fue copiado exitosamente desde el semestre ' . $semestre_malla_materia_anterior->semestreMalla->semestre->nombre . '.');
        } catch (\Exception $e) {
            return redirect()->route('planes_programas_clases_docentes.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function copy_programas_clases(Request $request, $id)
    {
        $this->authorize('adjuntar_programas_clases_docentes_pantalla');

        try {
            $semestre_malla_materia = SemestreMallaMateria::findOrFail($id);
            $semestre_malla_materia_anterior = SemestreMallaMateria::whereHas('semestreMalla', function ($query) use ($semestre_malla_materia) {
                $query->where('malla_id', $semestre_malla_materia->semestreMalla->malla_id)
                    ->where('semestre_id', '<', $semestre_malla_materia->semestreMalla->semestre_id);
            })->where('materia_id', $semestre_malla_materia->materia_id)
                ->orderBy('id', 'desc')
                ->first();

            if (!$semestre_malla_materia_anterior || !$semestre_malla_materia_anterior->url_programa_clases) {
                return back()->with('error-message', 'No se encontró un programa de estudio anterior para la materia ' . $semestre_malla_materia->materia->nombre_fantasia . '.');
            }

            $storage = Storage::disk('public');
            $nombre_viejo = $semestre_malla_materia_anterior->url_programa_clases;
            $nombre_viejo = str_replace(['storage/', 'public/', 'app/'], '', $nombre_viejo);
            if (!$storage->exists($nombre_viejo)) {
                return back()->with('error-message', 'El archivo del programa de estudio anterior no existe.');
            }

            $nombre_nuevo = $semestre_malla_materia->id . '_' . Str::replace('.', '_', $semestre_malla_materia->semestreMalla->semestre->nombre) . '-' . Carbon::now()->format('YmdHis');
            $nombre_nuevo = $nombre_nuevo . '.' . pathinfo($semestre_malla_materia_anterior->url_programa_clases, PATHINFO_EXTENSION);
            $nombre_nuevo_archivo = 'programas_clases/' . $nombre_nuevo;
            $storage->copy($nombre_viejo, $nombre_nuevo_archivo);

            $semestre_malla_materia->url_programa_clases = 'storage/programas_clases/' . $nombre_nuevo;
            $semestre_malla_materia->save();

            return redirect()->route('planes_programas_clases_docentes.index', Auth::id())->with('success-message', 'El programa de estudio de la materia ' . $semestre_malla_materia->materia->nombre_fantasia . ' fue copiado exitosamente desde el semestre ' . $semestre_malla_materia_anterior->semestreMalla->semestre->nombre . '.');
        } catch (\Exception $e) {
            return redirect()->route('planes_programas_clases_docentes.index', Auth::id())->with('error-message', $e->getMessage());
        }
    }

    public function destroy_planes_clases($id)
    {
        $this->authorize('eliminar_planes_clases_docentes_pantalla');

        DB::beginTransaction();

        try {
            $semestre_malla_materia = SemestreMallaMateria::findOrFail($id);
            $ubicacion_archivo = str_replace('storage', 'public', $semestre_malla_materia->url_plan_clases);
            Storage::delete($ubicacion_archivo);

            $semestre_malla_materia->url_plan_clases = null;
            $semestre_malla_materia->save();

            DB::commit();

            return redirect()->route('planes_programas_clases_docentes.index', Auth::id())->with('success-message', 'El plan de clase de la materia ' . $semestre_malla_materia->materia->nombre_fantasia . ' fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('planes_programas_clases_docentes.index', Auth::id())->with('error-message', 'El plan de clase de la materia ' . $semestre_malla_materia->materia->nombre_fantasia . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('planes_programas_clases_docentes.index', Auth::id())->with('error-message', $e->getMessage());
            }
        }
    }

    public function destroy_programas_clases($id)
    {
        $this->authorize('eliminar_programas_clases_docentes_pantalla');

        DB::beginTransaction();

        try {
            $semestre_malla_materia = SemestreMallaMateria::findOrFail($id);
            $ubicacion_archivo = str_replace('storage', 'public', $semestre_malla_materia->url_programa_clases);
            Storage::delete($ubicacion_archivo);

            $semestre_malla_materia->url_programa_clases = null;
            $semestre_malla_materia->save();

            DB::commit();

            return redirect()->route('planes_programas_clases_docentes.index', Auth::id())->with('success-message', 'El programa de estudio de la materia ' . $semestre_malla_materia->materia->nombre_fantasia . ' fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('planes_programas_clases_docentes.index', Auth::id())->with('error-message', 'El programa de estudio de la materia ' . $semestre_malla_materia->materia->nombre_fantasia . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('planes_programas_clases_docentes.index', Auth::id())->with('error-message', $e->getMessage());
            }
        }
    }
}
