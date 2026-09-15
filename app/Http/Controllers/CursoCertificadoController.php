<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

use App\Models\Curso;
use App\Models\Alumno;
use App\Models\InscripcionUbs;
use App\Models\CursoCertificadoGenerado;

class CursoCertificadoController extends Controller
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

    public function show($id)
    {
        $this->authorize('generar_certificados_cursos_ubs');

        try {
            $curso = Curso::findOrFail($id);

            if ($curso->evaluacion == false) { //si no tiene evaluacion solamente controlamos asistencia y pagos
                $subquery = DB::table('inscripciones_ubs')
                // ->leftJoin('pagos_inscripciones_ubs', 'inscripciones_ubs.id', '=', 'pagos_inscripciones_ubs.inscripcion_id')
                ->where('inscripciones_ubs.curso_id', $id) // Filtra por curso
                ->select('inscripciones_ubs.alumno_id')
                // ->selectRaw('COALESCE(SUM(pagos_inscripciones_ubs.saldo), 0) AS total_saldo')
                ->groupBy('inscripciones_ubs.alumno_id');

                $alumnos = Alumno::select('alumnos.id', 'alumnos.primer_nombre', 'alumnos.primer_apellido', 'alumnos.numero_documento')
                    ->join('alumnos_asistencias_ubs', 'alumnos.id', '=', 'alumnos_asistencias_ubs.alumno_id')
                    ->leftJoin('inscripciones_ubs', 'alumnos.id', '=', 'inscripciones_ubs.alumno_id')
                    // ->leftJoinSub($subquery, 'saldos', function ($join) {
                    //     $join->on('alumnos.id', '=', 'saldos.alumno_id');
                    // })
                    ->leftJoin('cursos_certificados_generados', function ($join) use ($id) {
                        $join->on('alumnos.id', '=', 'cursos_certificados_generados.alumno_id')
                             ->where('cursos_certificados_generados.curso_id', '=', $id);
                    })
                    ->where('inscripciones_ubs.curso_id', $id) // Filtra por curso
                    ->whereNull('cursos_certificados_generados.id')
                    ->selectRaw('COUNT(alumnos_asistencias_ubs.id) AS total_asistencias')
                    ->selectRaw('SUM(CASE WHEN alumnos_asistencias_ubs.estado IN (\'PR\', \'AJ\') THEN 1 ELSE 0 END) AS asistencias_positivas')
                    // ->selectRaw('saldos.total_saldo')
                    // ->groupBy('alumnos.id', 'saldos.total_saldo')
                    ->groupBy('alumnos.id', 'alumnos.primer_nombre', 'alumnos.primer_apellido', 'alumnos.numero_documento')
                    ->havingRaw('SUM(CASE WHEN alumnos_asistencias_ubs.estado IN (\'PR\', \'AJ\') THEN 1 ELSE 0 END)::float / COUNT(alumnos_asistencias_ubs.id) >= 0.70')
                    // ->havingRaw('saldos.total_saldo = 0')
                    ->orderBy('alumnos.primer_apellido') // Ordena por primer_apellido
                    ->get();
            } else {
                $subquery = DB::table('inscripciones_ubs')
                // ->leftJoin('pagos_inscripciones_ubs', 'inscripciones_ubs.id', '=', 'pagos_inscripciones_ubs.inscripcion_id')
                ->where('inscripciones_ubs.curso_id', $id) // Filtra por curso
                ->select('inscripciones_ubs.alumno_id')
                // ->selectRaw('COALESCE(SUM(pagos_inscripciones_ubs.saldo), 0) AS total_saldo')
                ->groupBy('inscripciones_ubs.alumno_id');

                $alumnos = Alumno::select('alumnos.id', 'alumnos.primer_nombre', 'alumnos.primer_apellido', 'alumnos.numero_documento')
                    ->join('alumnos_asistencias_ubs', 'alumnos.id', '=', 'alumnos_asistencias_ubs.alumno_id')
                    ->join('alumnos_notas_ubs', 'alumnos.id', '=', 'alumnos_notas_ubs.alumno_id')
                    ->leftJoin('inscripciones_ubs', 'alumnos.id', '=', 'inscripciones_ubs.alumno_id')
                    // ->leftJoinSub($subquery, 'saldos', function ($join) {
                    //     $join->on('alumnos.id', '=', 'saldos.alumno_id');
                    // })
                    ->leftJoin('cursos_certificados_generados', function ($join) use ($id) {
                        $join->on('alumnos.id', '=', 'cursos_certificados_generados.alumno_id')
                             ->where('cursos_certificados_generados.curso_id', '=', $id);
                    })
                    ->where('inscripciones_ubs.curso_id', $id) // Filtra por curso
                    ->whereNull('cursos_certificados_generados.id')
                    ->where('alumnos_notas_ubs.calificacion', '>', 1)
                    ->selectRaw('COUNT(alumnos_asistencias_ubs.id) AS total_asistencias')
                    ->selectRaw('SUM(CASE WHEN alumnos_asistencias_ubs.estado IN (\'PR\', \'AJ\') THEN 1 ELSE 0 END) AS asistencias_positivas')
                    // ->selectRaw('saldos.total_saldo')
                    // ->groupBy('alumnos.id', 'saldos.total_saldo')
                    ->groupBy('alumnos.id', 'alumnos.primer_nombre', 'alumnos.primer_apellido', 'alumnos.numero_documento')
                    ->havingRaw('SUM(CASE WHEN alumnos_asistencias_ubs.estado IN (\'PR\', \'AJ\') THEN 1 ELSE 0 END)::float / COUNT(alumnos_asistencias_ubs.id) >= 0.70')
                    // ->havingRaw('saldos.total_saldo = 0')
                    ->orderBy('alumnos.primer_apellido') // Ordena por primer_apellido
                    ->get();
            }

            foreach ($alumnos as $alumno) {
                $inscripcion = InscripcionUbs::where('alumno_id', $alumno->id)->where('curso_id', $curso->id)->first();
                $alumno->numero_inscripcion = $inscripcion->numero_inscripcion;
            }

            return view('ubs/cursos/show_certificados')->with(compact('curso', 'alumnos'));
        } catch (\Exception $e) {
            return redirect()->route('cursos.index')->with('error-message', $e->getMessage());
        }
    }

    public function generate(Request $request, $id)
    {
        //lo comentado es para generar todos los certificados de una vez en un solo PDF

        $this->authorize('generar_certificados_cursos_ubs');

        $request->validate([
            'detalles' => ['required', 'array'],
            'detalles.*.numero_inscripcion' => ['required', 'numeric'],
            'detalles.*.alumno' => ['required', 'numeric'],
            'detalles.*.numero_orden' => ['required', 'numeric'],
            'detalles.*.pagina' => ['required', 'numeric'],
        ]);

        DB::beginTransaction();

        try {
            $curso = Curso::findOrFail($id);
            // $dia_inicio = Carbon::parse($curso->fecha_apertura)->format('d');
            // $mes_inicio = Carbon::parse($curso->fecha_apertura)->translatedFormat('F');
            // $anho_inicio = Carbon::parse($curso->fecha_apertura)->format('Y');
            // $dia_fin = Carbon::parse($curso->fecha_fin)->format('d');
            // $mes_fin = Carbon::parse($curso->fecha_fin)->translatedFormat('F');
            // $anho_fin = Carbon::parse($curso->fecha_fin)->format('Y');

            // if ($anho_inicio == $anho_fin) {
            //     $curso->inicio = $dia_inicio . ' de ' . $mes_inicio;
            // } else {
            //     $curso->inicio = $dia_inicio . ' de ' . $mes_inicio . ' del ' . $anho_inicio;
            // }
            // $curso->fin = $dia_fin . ' de ' . $mes_fin . ' del ' . $anho_fin;
            // $curso->llamado_anho = $curso->llamado . '.0' . Carbon::parse($curso->fecha_apertura)->format('y') . '.';

            // $nombre_secretaria = 'Raquel Hellmann';
            // $nombre_rector = 'Yan Speranza';
            // $numero_resolucion = '17/20';

            // $datos = collect();
            foreach ($request->detalles as $detalle) {
                // $numero_alumno = str_pad($detalle['numero_inscripcion'], 5, '0', STR_PAD_LEFT);
                $alumno = Alumno::findOrFail($detalle['alumno']);
                // $nombre_alumno = $alumno->primer_nombre;
                // if ($alumno->segundo_nombre) {
                //     $nombre_alumno = $nombre_alumno . ' ' . $alumno->segundo_nombre;
                // }
                // if ($alumno->tercer_nombre) {
                //     $nombre_alumno = $nombre_alumno . ' ' . $alumno->tercer_nombre;
                // }
                // $nombre_alumno = $nombre_alumno . ' ' . $alumno->primer_apellido;
                // if ($alumno->segundo_apellido) {
                //     $nombre_alumno = $nombre_alumno . ' ' . $alumno->segundo_apellido;
                // }


                // $datos_insertar = ['numero_alumno' => $numero_alumno,
                //                    'nombre_alumno' => $nombre_alumno,
                //                    'documento_alumno' => number_format($alumno->numero_documento, 0, ',', '.'),
                //                    'numero_orden' => $detalle['numero_orden'],
                //                    'pagina' => $detalle['pagina']];
                // $datos->push($datos_insertar);

                $certificado_generado = new CursoCertificadoGenerado();
                $certificado_generado->curso_id = $curso->id;
                $certificado_generado->alumno_id = $alumno->id;
                $certificado_generado->numero_inscripcion = $detalle['numero_inscripcion'];
                $certificado_generado->numero_orden = $detalle['numero_orden'];
                $certificado_generado->numero_pagina = $detalle['pagina'];
                $certificado_generado->save();
            }

            DB::commit();

            // $fecha_hoy = Carbon::today()->format('d/m/Y');

            // $pdf = Pdf::loadView('ubs/cursos/pdf_certificados', compact('curso', 'anho_fin', 'datos', 'nombre_secretaria', 'nombre_rector', 'numero_resolucion', 'fecha_hoy'));
			// $pdf->setOptions([
			// 		'dpi' => 300,
			// 		'isRemoteEnabled' => true,
			// 	]);
            // $pdf->setPaper('A4', 'landscape');

            // return $pdf->stream('certificados_' . Str::lower(str_replace(' ', '_', $curso->nombre_real)) . '_' . $curso->llamado . 'llamado.pdf');

            $certificados = CursoCertificadoGenerado::where('curso_id', $curso->id)->where('estado', 'GE')->get();

            return view('ubs/cursos/certificados_generados/index')->with(compact('curso', 'certificados'));

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('cursos.index')->with('error-message', $e->getMessage());
        }
    }
}
