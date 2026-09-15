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
use App\Models\Programa;
use App\Models\Empresa;

class RptFechaExamenController extends Controller
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
        $this->authorize('ver_reportes_academicos_fechas_examenes');

        try {
            $semestres = Semestre::orderBy('id', 'desc')->get();
            $programas = Programa::whereIn('id', [1, 2, 3, 4, 8])->where('estado', 'AC')->orderBy('nombre', 'asc')->get();

            return view ('reportes_academicos/fechas_examenes/create')->with(compact('semestres', 'programas'));

        } catch (\Exception $e) {
            return redirect()->route('root')->with('error-message', $e->getMessage());
        }
    }

    public function pdf(Request $request)
    {
        $this->authorize('ver_reportes_academicos_fechas_examenes');

        $request->validate([
            'semestre' => ['required', 'numeric'],
            'programa' => ['required', 'numeric'],
        ]);

        try {
            $empresa = Empresa::first(); //obtenemos los datos de la empresa para el header

            $semestre = Semestre::findOrFail($request->semestre);
            $programa = Programa::findOrFail($request->programa);
            Carbon::setLocale('es'); //seteamos nuestra fecha en español
            $fecha_hoy = Carbon::now(); //obtenemos la fecha de hoy

            $fechas = collect();

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

                    $datos = ['docente' => $docente,
                                'materia' => $smm->materia->nombre_fantasia,
                                'parcial' => Carbon::parse($smm->fecha_examen_parcial)->format('d/m/Y'),
                                'ordinario' => Carbon::parse($smm->fecha_examen_ordinario)->format('d/m/Y'),
                                'complementario' => Carbon::parse($smm->fecha_examen_complementario)->format('d/m/Y'),
                                'extraordinario' => Carbon::parse($smm->fecha_examen_extraordinario)->format('d/m/Y')];

                    $fechas->push($datos);
                }
            }


            $fechas = $fechas->sortBy(['materia'])->values();

            $pdf = Pdf::loadView('reportes_academicos/fechas_examenes/pdf', compact('empresa', 'fecha_hoy', 'semestre', 'programa', 'fechas'));
            $pdf->setPaper('A4', 'landscape');

            return $pdf->stream('rpt_fechas_examenes_' . Carbon::now()->format('dmYHis') . '.pdf');

        } catch (\Exception $e) {
            return redirect()->route('reportes_academicos.create_fechas_examenes')->with('error-message', $e->getMessage());
        }
    }
}
