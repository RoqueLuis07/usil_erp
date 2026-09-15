<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

use App\Models\Malla;
use App\Models\Carrera;
use App\Models\TipoMalla;
use App\Models\MallaDetalle;
use App\Models\Materia;
use App\Models\Empresa;
use App\Models\Correlatividad;


class MallaController extends Controller
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
        $this->authorize('ver_mallas');

        try {
            $mallas = Malla::with('mallaDetalles')->orderBy('id', 'desc')->get();
            return view('mallas/index')->with(compact('mallas'));
        } catch (\Exception $e) {
            return redirect()->route('mallas.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_mallas');

        try {
            $malla = Malla::with(['mallaDetalles' => function ($query) {
                $query->orderBy('semestre')
                ->orderBy('materia_id');
            }])->findOrFail($id);
            return view('mallas/show')->with(compact('malla'));
        } catch (\Exception $e) {
            return redirect()->route('mallas.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_mallas');

        try {
            $carreras = Carrera::where('estado', 'AC')->get();
            $tipos_mallas = TipoMalla::where('estado', 'AC')->get();
            $materias = Materia::where('estado', 'AC')->orderBy('nombre_fantasia', 'asc')->get();
            return view('mallas/create')->with(compact('carreras', 'tipos_mallas', 'materias'));
        } catch (\Exception $e) {
            return redirect()->route('mallas.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_mallas');

        $array = $request->input('detalles'); //obtener datos del request
        $lastIndex = is_array($array) ? count($array) - 1 : null; //longitud del array

        $request->validate([
            'carrera' => ['required', 'numeric'],
            'tipo_malla' => ['required', 'numeric'],
            // 'tipo_malla' => ['required', 'numeric', Rule::unique('mallas', 'tipo_malla_id')->where(fn ($query) => $query->where('tipo_malla_id', $request->tipo_malla)->where('carrera_id', $request->carrera))],
            //El de arriba verifica que el conjunto de carrera y tipo_malla na existan
            'detalles' => ['required', 'array'],
            'detalles.*.materia' => ['required', 'numeric'],
            'detalles.*.doble_grado' => 'required',
            'detalles.*.semestre' => ['required', 'numeric', 'min:1', 'max:12'],
            'detalles.*.carga_horaria' => ['required', 'numeric', 'min:1', 'max:200'],
            'detalles.*.cantidad_creditos' => ['required', 'numeric', 'min:1', 'max:200'],
            'detalles.*.area_curricular' => ['required', 'min:1', 'max:1']
        ]);

        DB::beginTransaction();

        try {
            $malla = new Malla();
            $malla->carrera_id = $request->carrera;
            $malla->tipo_malla_id = $request->tipo_malla;
            $malla->cargado_por_id = Auth::id();
            $malla->save();

            foreach ($request->detalles as $detalle) { //utilizar el array obtenido arriba
                $malla_detalle = new MallaDetalle();
                $malla_detalle->malla_id = $malla->id;
                $malla_detalle->materia_id = $detalle['materia'];
                $malla_detalle->semestre = $detalle['semestre'];
                $malla_detalle->carga_horaria = $detalle['carga_horaria'];
                $malla_detalle->cantidad_creditos = $detalle['cantidad_creditos'];
                $malla_detalle->doble_grado = $detalle['doble_grado'];
                $malla_detalle->area_curricular = $detalle['area_curricular'];
                $malla_detalle->save();
            }

            DB::commit();

            return redirect()->route('mallas.index')->with('success-message', 'La malla de la carrera ' . $malla->carrera->nombre_fantasia . ' fue creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('mallas.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_mallas');

        try {
            $malla = Malla::with(['mallaDetalles' => function ($query) {
                $query->orderBy('semestre')
                ->orderBy('materia_id');
            }])->findOrFail($id);
            $carreras = Carrera::where('estado', 'AC')->get();
            $tipos_mallas = TipoMalla::where('estado', 'AC')->get();
            $materias = Materia::where('estado', 'AC')->orderBy('nombre_fantasia', 'asc')->get();
            return view('mallas/edit')->with(compact('malla', 'carreras', 'tipos_mallas', 'materias'));
        } catch (\Exception $e) {
            return redirect()->route('mallas.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_mallas');

        $request->validate([
            'carrera' => ['required', 'numeric'],
            'tipo_malla' => ['required', 'numeric'],
            // 'tipo_malla' => ['required', 'numeric', Rule::unique('mallas', 'tipo_malla_id')->where(fn ($query) => $query->where('tipo_malla_id', $request->tipo_malla)->where('carrera_id', $request->carrera))->ignore($id)],
            //El de arriba verifica que el conjunto de carrera y tipo_malla na existan
            'detalles' => ['required', 'array'],
            'detalles.*.materia' => ['required', 'numeric'],
            'detalles.*.doble_grado' => 'required',
            'detalles.*.semestre' => ['required', 'numeric', 'min:1', 'max:12'],
            'detalles.*.carga_horaria' => ['required', 'numeric', 'min:1', 'max:200'],
            'detalles.*.cantidad_creditos' => ['required', 'numeric', 'min:1', 'max:200'],
            'detalles.*.area_curricular' => ['required', 'min:1', 'max:1']
        ]);

        DB::beginTransaction();

        try {
            $malla = Malla::findOrFail($id);
            $malla->carrera_id = $request->carrera;
            $malla->tipo_malla_id = $request->tipo_malla;
            $malla->actualizado_por_id = Auth::id();
            $malla->save();

            //obtener el detalle de la malla y eliminar lo que habia para poder crear de vuelta
            $malla_detalles = MallaDetalle::where('malla_id', $id)->delete();

            foreach ($request->detalles as $detalle) { //utilizar el array obtenido arriba
                $malla_detalle = new MallaDetalle();
                $malla_detalle->malla_id = $malla->id;
                $malla_detalle->materia_id = $detalle['materia'];
                $malla_detalle->semestre = $detalle['semestre'];
                $malla_detalle->carga_horaria = $detalle['carga_horaria'];
                $malla_detalle->cantidad_creditos = $detalle['cantidad_creditos'];
                $malla_detalle->doble_grado = $detalle['doble_grado'];
                $malla_detalle->area_curricular = $detalle['area_curricular'];
                $malla_detalle->save();
            }

            DB::commit();

            return redirect()->route('mallas.index')->with('success-message', 'La malla de la carrera ' . $malla->carrera->nombre_fantasia . ' fue actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('mallas.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_mallas');

        DB::beginTransaction();

        try {
            $malla = Malla::findOrFail($id);
            $malla->actualizado_por_id = Auth::id();
            $malla->estado = 'IN';
            $malla->save();

            DB::commit();

            return redirect()->route('mallas.index')->with('error-message','La malla de la carrera ' . $malla->carrera->nombre_fantasia . ' fue inactivada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('mallas.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_mallas');

        DB::beginTransaction();

        try {
            $malla = Malla::findOrFail($id);
            $malla->actualizado_por_id = Auth::id();
            $malla->estado = 'AC';
            $malla->save();

            DB::commit();

            return redirect()->route('mallas.index')->with('success-message','La malla de la carrera ' . $malla->carrera->nombre_fantasia . ' fue activada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('mallas.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_mallas');

        DB::beginTransaction();

        try {
            $malla = Malla::findOrFail($id);

            $malla_detalles = MallaDetalle::where('malla_id', $malla->id)->delete();

            $malla->delete();

            DB::commit();

            return redirect()->route('mallas.index')->with('success-message','La malla de la carrera ' . $malla->carrera->nombre_fantasia . ' fue eliminada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();

            //Verifica si el error es por Foreign key violation / Integrity constraint violation
            if (stripos($e->getMessage(), 'Foreign key violation')) {
                return redirect()->route('mallas.index')->with('error-message', 'La malla de la carrera ' . $malla->carrera->nombre_fantasia . ' no se puede eliminar. Está siendo utilizado por otro registro dentro del sistema.');
            } else {
                return redirect()->route('mallas.index')->with('error-message', $e->getMessage());
            }
        }
    }

    public function pdf($id)
    {
        $this->authorize('imprimir_mallas');

        try {
            $empresa = Empresa::first();
            $fecha_hoy = Carbon::now();
            $semestres = collect();
            $correlatividades = collect();

            $malla = Malla::with('mallaDetalles')->findOrFail($id);
            $cantidad_semestres = $malla->carrera->cantidad_semestres;
            for ($i=1; $i <= $cantidad_semestres ; $i++) {
                $semestres->push($i);
            }

            $correlatividades_all = Correlatividad::orderBy('materia_id', 'asc')->get();
            foreach ($malla->mallaDetalles as $detalle) {
                foreach ($correlatividades_all as $correlatividad) {
                    if ($detalle->materia_id == $correlatividad->materia_id) {
                        $correlatividades->push(['materia_id' => $detalle->materia_id,
                                                'correlativa' => $correlatividad->correlativa->nombre_real]);
                    }
                }
            }

            if ($malla->programa_id != 4) {
                $pdf = Pdf::loadView('mallas/pdf_paraguay', compact('empresa', 'fecha_hoy', 'semestres', 'malla', 'correlatividades'));
            } else {
                $pdf = Pdf::loadView('mallas/pdf_siu', compact('empresa', 'fecha_hoy', 'malla'));
            }
            $pdf->setPaper('A4');

            return $pdf->stream('malla_' . $malla->carrera->nombre_real . '.pdf');

        } catch (\Exception $e) {
            return redirect()->route('mallas.show', $id)->with('error-message', $e->getMessage());
        }
    }
}
