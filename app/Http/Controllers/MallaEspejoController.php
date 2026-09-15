<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

use App\Models\MallaEspejo;
use App\Models\MallaEspejoDetalle;
use App\Models\Malla;
use App\Models\MallaDetalle;
use App\Models\Escala;


class MallaEspejoController extends Controller
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
        $this->authorize('ver_mallas_espejo');

        try {
            $mallas_espejos = MallaEspejo::orderBy('id', 'asc')->get();
            return view('mallas_espejos/index')->with(compact('mallas_espejos'));
        } catch (\Exception $e) {
            return redirect()->route('mallas_espejos.index')->with('error-message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $this->authorize('ver_mallas_espejo');

        try {
            $malla_espejo = MallaEspejo::with('mallaEspejoDetalles')->findOrFail($id);

            return view('mallas_espejos/show')->with(compact('malla_espejo'));
        } catch (\Exception $e) {
            return redirect()->route('mallas_espejos.index')->with('error-message', $e->getMessage());
        }
    }

    public function create()
    {
        $this->authorize('crear_mallas_espejo');

        DB::beginTransaction();

        try {
            $mallas_paraguay = Malla::where('tipo_malla_id', 1)->where('estado', 'AC')->get();
            $mallas_siu = Malla::where('tipo_malla_id', 2)->where('estado', 'AC')->get();

            return view('mallas_espejos/create')->with(compact('mallas_paraguay', 'mallas_siu'));
        } catch (\Exception $e) {
            return redirect()->route('mallas_espejos.index')->with('error-message', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $this->authorize('crear_mallas_espejo');

        DB::beginTransaction();

        $request->validate([
            'malla_paraguay' => ['required', 'numeric'],
            'malla_siu' => ['required', 'numeric', Rule::unique('mallas_espejos' , 'malla_siu_id')
                ->where(fn ($query) => $query->where('malla_siu_id', $request->malla_siu)
                ->where('malla_paraguay_id', $request->malla_paraguay))],
            //El de arriba verifica que el conjunto de malla paraguay y malla siu no existan,
            'detalles' => ['required', 'array'],
            'detalles.*.materia_paraguay' => ['required', 'numeric'],
            'detalles.*.materia_siu' => ['required', 'numeric'],
        ]);

        try {
            $malla_espejo = new MallaEspejo();
            $malla_espejo->malla_paraguay_id = $request->malla_paraguay;
            $escala_paraguay = Escala::where('programa_id', $malla_espejo->mallaParaguay->carrera->programa_id)->first();
            $malla_espejo->escala_paraguay_id = $escala_paraguay->id;
            $malla_espejo->malla_siu_id = $request->malla_siu;
            $escala_siu = Escala::where('programa_id', $malla_espejo->mallaSiu->carrera->programa_id)->first();
            $malla_espejo->escala_siu_id = $escala_siu->id;
            $malla_espejo->cargado_por_id = Auth::id();
            $malla_espejo->save();

            foreach ($request->detalles as $detalle) {
                $malla_espejo_detalle = new MallaEspejoDetalle();
                $malla_espejo_detalle->malla_espejo_id = $malla_espejo->id;
                $malla_espejo_detalle->materia_paraguay_id = $detalle['materia_paraguay'];
                $malla_espejo_detalle->materia_siu_id = $detalle['materia_siu'];
                $malla_espejo_detalle->save();
            }

            DB::commit();

            return redirect()->route('mallas_espejos.index')->with('success-message', 'La malla espejo fue creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('mallas_espejos.index')->with('error-message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $this->authorize('editar_mallas_espejo');

        DB::beginTransaction();

        try {
            $malla_espejo = MallaEspejo::findOrFail($id);
            $malla_paraguay = Malla::findOrFail($malla_espejo->malla_paraguay_id);
            $materias_paraguay = MallaDetalle::with('materia')->where('malla_id', $malla_paraguay->id)->get();
            $malla_siu = Malla::findOrFail($malla_espejo->malla_siu_id);
            $materias_siu = MallaDetalle::with('materia')->where('malla_id', $malla_siu->id)->get();

            return view('mallas_espejos/edit')->with(compact('malla_espejo', 'materias_paraguay', 'materias_siu'));
        } catch (\Exception $e) {
            return redirect()->route('mallas_espejos.index')->with('error-message', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $this->authorize('editar_mallas_espejo');

        DB::beginTransaction();

        $request->validate([
            'detalles' => ['required', 'array'],
            'detalles.*.materia_paraguay' => ['required', 'numeric'],
            'detalles.*.materia_siu' => ['required', 'numeric'],
        ]);

        try {
            $malla_espejo = MallaEspejo::findOrFail($id);
            $malla_espejo->actualizado_por_id = Auth::id();
            $malla_espejo->save();

            $malla_espejo_detalle = MallaEspejoDetalle::where('malla_espejo_id', $malla_espejo->id)->delete();
            foreach ($request->detalles as $detalle) {
                $malla_espejo_detalle = new MallaEspejoDetalle();
                $malla_espejo_detalle->malla_espejo_id = $malla_espejo->id;
                $malla_espejo_detalle->materia_paraguay_id = $detalle['materia_paraguay'];
                $malla_espejo_detalle->materia_siu_id = $detalle['materia_siu'];
                $malla_espejo_detalle->save();
            }

            DB::commit();

            return redirect()->route('mallas_espejos.index')->with('success-message', 'La malla espejo fue actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('mallas_espejos.index')->with('error-message', $e->getMessage());
        }
    }

    public function unactivate($id)
    {
        $this->authorize('inactivar_mallas_espejo');

        DB::beginTransaction();

        try {
            $malla_espejo = MallaEspejo::findOrFail($id);
            $malla_espejo->estado = 'IN';
            $malla_espejo->actualizado_por_id = Auth::id();
            $malla_espejo->save();

            DB::commit();

            return redirect()->route('mallas_espejos.index')->with('error-message', 'La malla espejo fue inactivada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('mallas_espejos.index')->with('error-message', $e->getMessage());
        }
    }

    public function activate($id)
    {
        $this->authorize('activar_mallas_espejo');

        DB::beginTransaction();

        try {
            $malla_espejo = MallaEspejo::findOrFail($id);
            $malla_espejo->estado = 'AC';
            $malla_espejo->actualizado_por_id = Auth::id();
            $malla_espejo->save();

            DB::commit();

            return redirect()->route('mallas_espejos.index')->with('success-message', 'La malla espejo fue activada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('mallas_espejos.index')->with('error-message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $this->authorize('eliminar_mallas_espejo');

        DB::beginTransaction();

        try {
            $malla_espejo = MallaEspejo::findOrFail($id);
            $malla_espejo_detalle = MallaEspejoDetalle::where('malla_espejo_id', $malla_espejo->id)->delete();
            $malla_espejo->delete();

            DB::commit();

            return redirect()->route('mallas_espejos.index')->with('error-message', 'La malla espejo fue eliminada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('mallas_espejos.index')->with('error-message', $e->getMessage());
        }
    }

    public function get_materias($malla)
    {
        DB::beginTransaction();

        try {
            $malla = Malla::findOrFail($malla);
            $malla_detalles = MallaDetalle::join('materias', 'mallas_detalles.materia_id', '=', 'materias.id')
                                        ->where('mallas_detalles.malla_id', $malla->id)
                                        ->orderBy('mallas_detalles.semestre')
                                        ->orderBy('materias.nombre_fantasia')
                                        ->select('materias.*')
                                        ->get();

            return response()->json([
                'materias' => $malla_detalles,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('mallas_espejos.index')->with('error-message', $e->getMessage());
        }
    }
}
