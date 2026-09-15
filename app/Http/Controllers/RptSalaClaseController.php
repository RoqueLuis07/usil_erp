<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

use App\Models\Semestre;
use App\Models\SemestreMalla;
use App\Models\SemestreMallaMateria;
use App\Models\SemestreMallaMateriaHorario;
use App\Models\Programa;
use App\Models\DiaSemana;
use App\Models\Empresa;

class RptSalaClaseController extends Controller
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
        $this->authorize('ver_reportes_academicos_salas_clases');

        try {
            $semestres = Semestre::orderBy('id', 'desc')->get();
            $programas = Programa::whereIn('id', [1, 2, 3, 4, 8])->where('estado', 'AC')->orderBy('nombre', 'asc')->get();

            return view ('reportes_academicos/salas_clases/create')->with(compact('semestres', 'programas'));

        } catch (\Exception $e) {
            return redirect()->route('root')->with('error-message', $e->getMessage());
        }
    }

    public function pdf(Request $request)
    {
        $this->authorize('ver_reportes_academicos_salas_clases');

        $request->validate([
            'semestre' => ['required', 'numeric'],
            'programa' => ['required', 'numeric']
        ]);

        try {
            $empresa = Empresa::first(); //obtenemos los datos de la empresa para el header

            $semestre = Semestre::findOrFail($request->semestre);
            $programa = Programa::findOrFail($request->programa);
            Carbon::setLocale('es'); //seteamos nuestra fecha en español
            $fecha_hoy = Carbon::now(); //obtenemos la fecha de hoy

            $dias_semana = DiaSemana::whereBetween('id', [2, 6])->get();

            $horarios = collect();

            $semestre_mallas = SemestreMalla::whereHas('malla', function ($query) use ($programa) {
                $query->whereHas('carrera', function ($q) use ($programa) {
                    $q->where('programa_id', $programa->id);
                });
            })
            ->where('semestre_id', $semestre->id)
            ->get();

            foreach ($semestre_mallas as $sm) {
                $semestre_malla_materias = SemestreMallaMateria::where('semestre_malla_id', $sm->id)->where('estado', 'AC')->get();
                foreach ($semestre_malla_materias as $smm) {
                    //funcion para concatenar el nombre completo del docente ordenado por apellido nombre
                    $nombre_apellido = $smm->docente->primer_nombre;    
                    if ($smm->docente->segundo_nombre) {
                        $nombre_apellido .= ' ' . $smm->docente->segundo_nombre;
                    }
                    if ($smm->docente->tercer_nombre) {
                        $nombre_apellido .= ' ' . $smm->docente->tercer_nombre;
                    }
                        $docente = $nombre_apellido . ' ' . $smm->docente->primer_apellido;
                    if ($smm->docente->segundo_apellido) {
                        $docente .= ' ' . $smm->docente->segundo_apellido;
                    }
                    //fin de funcion

                    $hyflex = '---';
                    if ($smm->aula <= 2) {
                        $hyflex = 'Sí';
                    }


                    $semestre_malla_materia_horarios = SemestreMallaMateriaHorario::where('semestre_malla_materia_id', $smm->id)->get();
                    foreach ($semestre_malla_materia_horarios as $smmh) {
                        $datos = ['docente' => $docente,
                                  'materia' => $smm->materia->nombre_fantasia,
                                  'dia_semana_id' => $smmh->dia_semana_id,
                                  'hora_inicio' => Carbon::parse($smmh->hora_inicio)->format('H:i'),
                                  'hora_fin' => Carbon::parse($smmh->hora_fin)->format('H:i'),
                                  'aula' => $smm->aula,
                                  'hyflex' => $hyflex];

                        $horarios->push($datos);
                    }
                }
            }

            $horarios = $horarios->sortBy(['materia','hora_inicio',])->values();

            $pdf = Pdf::loadView('reportes_academicos/salas_clases/pdf', compact('empresa', 'fecha_hoy', 'semestre', 'programa', 'dias_semana', 'horarios'));
            $pdf->setPaper('A4');

            return $pdf->stream('rpt_salas_clases_' . Carbon::now()->format('dmYHis') . '.pdf');

        } catch (\Exception $e) {
            return redirect()->route('reportes_academicos.create_salas_clases')->with('error-message', $e->getMessage());
        }
    }
}
