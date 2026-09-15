<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\Models\Semestre;
use App\Models\SemestreMalla;
use App\Models\SemestreMallaMateria;
use App\Models\SemestreMallaMateriaHorario;
use App\Models\Docente;
use App\Models\Malla;
use App\Models\MallaDetalle;
use App\Models\Moneda;


class SemestreController extends Controller
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
        $this->authorize('ver_periodos');

        try {
            $semestres = Semestre::orderBy('id', 'desc')->get();
            return view('semestres/index')->with(compact('semestres'));
        } catch (\Exception $e) {
            return redirect()->route('semestres.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_periodos');

        try {
            $semestre = Semestre::with(['semestreMallas' => function ($query) {
                $query->orderBy('id');
            }])->findOrFail($id);
            $coordinadores = Docente::where('estado', 'AC')->get();
            $monedas = Moneda::where('estado', 'AC')->get();
            return view('semestres/show')->with(compact('semestre', 'coordinadores', 'monedas'));
        } catch (\Exception $e) {
            return redirect()->route('semestres.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_periodos');

        try {
            $mallas = Malla::with([
                'carrera',
                'tipoMalla'
            ])
            ->where('mallas.estado', 'AC')
            ->join('carreras', 'carreras.id', '=', 'mallas.carrera_id')
            ->orderBy('carreras.programa_id', 'asc')
            ->select('mallas.*')
            ->get();
            $coordinadores = Docente::where('estado', 'AC')->get();
            return view('semestres/create')->with(compact('mallas', 'coordinadores'));
        } catch (\Exception $e) {
            return redirect()->route('semestres.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_periodos');

        $array = $request->input('detalles'); //obtener datos del request
        $lastIndex = is_array($array) ? count($array) - 1 : null; //longitud del array

        $hoy = Carbon::today()->toDateString();
        $unMesDesdeHoy = Carbon::today()->addMonth()->toDateString();

        $rules = [
            'nombre_semestre' => ['required', Rule::unique('semestres', 'nombre')],
            // 'fecha_inicio' => ['required', 'date', 'after_or_equal:' . $hoy],
            // 'fecha_fin' => ['required', 'date', 'after_or_equal:' . $unMesDesdeHoy],
            'detalles.*.malla' => ['required', 'numeric'],
            'detalles.*.coordinador' => 'required',
            // 'detalles.*.fecha_inicio_matriculacion' => ['required', 'date', 'before_or_equal:detalles.*.fecha_fin_matriculacion'],
            // 'detalles.*.fecha_fin_matriculacion' => ['required', 'date', 'before_or_equal:fecha_fin', 'after_or_equal:detalles.*.fecha_inicio_matriculacion'],
        ];

        //si el ultimo valor es nulo, lo elimina
        if ($array[$lastIndex]['fecha_inicio_matriculacion'] == null && $array[$lastIndex]['fecha_fin_matriculacion'] == null && $lastIndex != 0) {
            //indicar que la ultima fila puede ser nula
            if ($lastIndex !== null) {
                $rules["detalles.$lastIndex.malla"] = 'nullable';
                $rules["detalles.$lastIndex.coordinador"] = 'nullable';
                $rules["detalles.$lastIndex.fecha_inicio_matriculacion"] = 'nullable';
                $rules["detalles.$lastIndex.fecha_fin_matriculacion"] = 'nullable';
            }
            unset($array[$lastIndex]);
        }

        $request->validate($rules); //validar

        DB::beginTransaction();

        try {
            $semestre = new Semestre();
            $semestre->nombre = removeAccents(Str::upper($request->nombre_semestre));
            $semestre->fecha_inicio = $request->fecha_inicio;
            $semestre->fecha_fin = $request->fecha_fin;
            $semestre->cargado_por_id = Auth::id();
            $semestre->save();

            foreach ($array as $detalle) { //utilizar el array obtenido arriba
                $semestre_malla = new SemestreMalla();
                $semestre_malla->semestre_id = $semestre->id;
                $semestre_malla->malla_id = $detalle['malla'];
                $semestre_malla->coordinador_id = $detalle['coordinador'];
                $semestre_malla->fecha_inicio_matriculacion = $detalle['fecha_inicio_matriculacion'];
                $semestre_malla->fecha_fin_matriculacion = $detalle['fecha_fin_matriculacion'];
                $semestre_malla->save();

                $mallas_detalles = MallaDetalle::where('malla_id', $semestre_malla->malla_id)->get();
                foreach ($mallas_detalles as $malla_detalle) {
                    $semestre_malla_materia = new SemestreMallaMateria();
                    $semestre_malla_materia->semestre_malla_id = $semestre_malla->id;
                    $semestre_malla_materia->materia_id = $malla_detalle->materia_id;
                    $semestre_malla_materia->cargado_por_id = Auth::id();
                    $semestre_malla_materia->save();

                    $semestre_malla_materia_horario = new SemestreMallaMateriaHorario();
                    $semestre_malla_materia_horario->semestre_malla_materia_id = $semestre_malla_materia->id;
                    $semestre_malla_materia_horario->save();
                }
            }

            DB::commit();

            return redirect()->route('semestres.index')->with('success-message', 'El semestre ' . $semestre->nombre . ' fue creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('semestres.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_periodos');

        DB::beginTransaction();

        try {
            $semestre = Semestre::findOrFail($id);
            $semestre->actualizado_por_id = Auth::id();
            $semestre->estado = 'IN';
            $semestre->save();

            DB::commit();

            return redirect()->route('semestres.index')->with('error-message','El semestre ' . $semestre->nombre . ' fue inactivado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('semestres.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_periodos');

        DB::beginTransaction();

        try {
            $semestre = Semestre::findOrFail($id);
            if ($semestre->fecha_fin < Carbon::today()->startOfDay()) {
                return back()->with('error-message', 'El semestre no puede activarse. La fecha de fin es menor a la fecha de hoy.');
            }
            $semestre->actualizado_por_id = Auth::id();
            $semestre->estado = 'AC';
            $semestre->save();

            DB::commit();

            return redirect()->route('semestres.index')->with('success-message','El semestre ' . $semestre->nombre . ' fue activado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('semestres.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_periodos');

        DB::beginTransaction();

        try {
            $semestre = Semestre::findOrFail($id);

            // Obtener las mallas del semestre
            $semestre_mallas = SemestreMalla::where('semestre_id', $id)->get();

            foreach ($semestre_mallas as $semestre_malla) {
                // Obtener las materias del semestre malla
                $semestre_malla_materias = SemestreMallaMateria::where('semestre_malla_id', $semestre_malla->id)->get();

                foreach ($semestre_malla_materias as $semestre_malla_materia) {
                    // Eliminar los horarios de las materias del semestre malla
                    SemestreMallaMateriaHorario::where('semestre_malla_materia_id', $semestre_malla_materia->id)->delete();
                }

                // Eliminar las materias del semestre malla
                SemestreMallaMateria::where('semestre_malla_id', $semestre_malla->id)->delete();
            }

            // Eliminar las mallas del semestre
            SemestreMalla::where('semestre_id', $id)->delete();

            // Eliminar el semestre
            $semestre->delete();

            DB::commit();

            return redirect()->route('semestres.index')->with('success-message','El semestre ' . $semestre->nombre . ' fue eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('semestres.index')->with('error-message', 'El semestre ' . $semestre->nombre . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('semestres.index')->with('error-message', $e->getMessage());
            }
        }
    }
}
