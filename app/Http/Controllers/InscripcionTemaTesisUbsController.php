<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

use App\Models\InscripcionTemaTesisUbs;
use App\Models\Alumno;
use App\Models\LineaTesis;
use App\Models\Docente;
use App\Models\BloqueAnteproyectoTesisUbs;
use App\Models\AnteproyectoTesisUbs;
use App\Models\Empresa;
use App\Models\ActaEvaluacionUbs;

class InscripcionTemaTesisUbsController extends Controller
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

    public function aprobar_tutor($id)
    {
        $this->authorize('aprobar_tutor_tesis_ubs');

        DB::beginTransaction();

        try {
            $tesis = InscripcionTemaTesisUbs::findOrFail($id);
            $tesis->aprobado_tutor_id = Auth::id();
            $tesis->fecha_aprobado_tutor = Carbon::now();
            $tesis->estado = 'AT';
            $tesis->save();

            DB::commit();

            return redirect()->route('tesis_ubs.show', $id)->with('success-message', 'El tema de tesis del alumno ' . $tesis->alumno->primer_nombre . ' ' . $tesis->alumno->primer_apellido . ' fue aprobado por el tutor exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tesis_ubs.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function anular_aprobacion_tutor($id)
    {
        $this->authorize('anular_aprobacion_tutor_tesis_ubs');

        DB::beginTransaction();

        try {
            $tesis = InscripcionTemaTesisUbs::findOrFail($id);
            $tesis->aprobado_tutor_id = null;
            $tesis->fecha_aprobado_tutor = null;
            $tesis->estado = 'PE';
            $tesis->save();

            DB::commit();

            return redirect()->route('tesis_ubs.show', $id)->with('error-message', 'La aprobación del tutor del tema de tesis del alumno ' . $tesis->alumno->primer_nombre . ' ' . $tesis->alumno->primer_apellido . ' fue anulada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tesis_ubs.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function aprobar_calidad($id)
    {
        $this->authorize('aprobar_calidad_tesis_ubs');

        DB::beginTransaction();

        try {
            $tesis = InscripcionTemaTesisUbs::findOrFail($id);
            $tesis->aprobado_calidad_id = Auth::id();
            $tesis->fecha_aprobado_calidad = Carbon::now();
            $tesis->estado = 'AC';
            $tesis->save();

            // $bloques = BloqueAnteproyectoTesisUbs::where('estado', 'AC')->get();
            // foreach ($bloques as $bloque) {
            //     $anteproyecto = new AnteproyectoTesisUbs();
            //     $anteproyecto->inscripcion_id = $tesis->id;
            //     $anteproyecto->bloque_id = $bloque->id;
            //     $anteproyecto->numero_bloque = $bloque->numero;
            //     $anteproyecto->save();
            // }

            DB::commit();

            return redirect()->route('tesis_ubs.show', $id)->with('success-message', 'El tema de tesis del alumno ' . $tesis->alumno->primer_nombre . ' ' . $tesis->alumno->primer_apellido . ' fue aprobado por calidad educativa exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tesis_ubs.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function anular_aprobacion_calidad($id)
    {
        $this->authorize('anular_aprobacion_calidad_tesis_ubs');

        DB::beginTransaction();

        try {
            $tesis = InscripcionTemaTesisUbs::findOrFail($id);
            $tesis->aprobado_calidad_id = null;
            $tesis->fecha_aprobado_calidad = null;
            $tesis->estado = 'AT';
            $tesis->save();

            // $anteproyectos = AnteproyectoTesisUbs::where('inscripcion_id', $tesis->id)->delete();

            DB::commit();

            return redirect()->route('tesis_ubs.show', $id)->with('error-message', 'La aprobación de calidad educativa del tema de tesis del alumno ' . $tesis->alumno->primer_nombre . ' ' . $tesis->alumno->primer_apellido . ' fue anulada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tesis_ubs.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function rechazar($id)
    {
        $this->authorize('rechazar_tesis_ubs');

        DB::beginTransaction();

        try {
            $tesis = InscripcionTemaTesisUbs::findOrFail($id);
            $tesis->rechazado_por_id = Auth::id();
            $tesis->fecha_rechazo = Carbon::now();
            $tesis->estado = 'RE';
            $tesis->save();

            DB::commit();

            return redirect()->route('tesis_ubs.show', $id)->with('error-message', 'El tema de tesis del alumno ' . $tesis->alumno->primer_nombre . ' ' . $tesis->alumno->primer_apellido . ' fue rechazado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tesis_ubs.show', $id)->with('error-message', $e->getMessage());
        }
    }

    public function anular_rechazo($id)
    {
        $this->authorize('anular_rechazo_tesis_ubs');

        DB::beginTransaction();

        try {
            $tesis = InscripcionTemaTesisUbs::findOrFail($id);
            $tesis->rechazado_por_id = null;
            $tesis->fecha_rechazo = null;
            $tesis->estado = 'PE';
            $tesis->save();

            DB::commit();

            return redirect()->route('tesis_ubs.show', $id)->with('error-message', 'El rechazo del tema de tesis del alumno ' . $tesis->alumno->primer_nombre . ' ' . $tesis->alumno->primer_apellido . ' fue anulado exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('tesis_ubs.show', $id)->with('error-message', $e->getMessage());
        }
    }
}
