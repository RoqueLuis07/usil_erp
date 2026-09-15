<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

use App\Models\Semestre;
use App\Models\Matriculacion;
use App\Models\Empresa;

class RptAnoIngresoAlumnoController extends Controller
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

    public function create()
    {
        $this->authorize('ver_reportes_academicos_alumnos_ingresos');

        try {
            $semestres = Semestre::orderBy('id', 'desc')->get();

            return view ('reportes_academicos/anho_ingreso_alumnos/create')->with(compact('semestres'));

        } catch (\Exception $e) {
            return redirect()->route('root')->with('error-message', $e->getMessage());
        }
    }

    public function pdf(Request $request)
    {
        $this->authorize('ver_reportes_academicos_alumnos_ingresos');

        $request->validate([
            'semestre_inicio' => ['required', 'numeric'],
            'semestre_fin' => ['required', 'numeric']
        ]);

        try {
            $empresa = Empresa::first(); //obtenemos los datos de la empresa para el header

            $semestre_inicio = Semestre::findOrFail($request->semestre_inicio);
            $semestre_fin = Semestre::findOrFail($request->semestre_fin);

            $matriculaciones = Matriculacion::whereIn('semestre_id', [$semestre_inicio->id, $semestre_fin->id])->get();

            Carbon::setLocale('es'); //seteamos nuestra fecha en español
            $fecha_hoy = Carbon::now(); //obtenemos la fecha de hoy

            $pdf = Pdf::loadView('reportes_academicos/anho_ingreso_alumnos/pdf', compact('empresa', 'semestre_inicio', 'semestre_fin', 'matriculaciones', 'fecha_hoy'));
            $pdf->setPaper('A4');

            return $pdf->stream('rpt_anho_ingreso_alumnos_' . Carbon::now()->format('dmYHis') . '.pdf');

        } catch (\Exception $e) {
            return redirect()->route('reportes_academicos.create_alumnos_ingresos')->with('error-message', $e->getMessage());
        }
    }
}
