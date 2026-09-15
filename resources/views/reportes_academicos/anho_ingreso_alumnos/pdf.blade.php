@can('ver_reportes_academicos_alumnos_ingresos')
    @extends('layouts.pdf.notas')
    @section('css')
        <style>
            .titulo {
                width: 100%;
                text-align: center;
                margin-top: 20px;
                font-size: 16px;
            }
            #reporte {
                margin-bottom: -1px;
            }
            .filtros {
                width: 100%;
                height: 65px;
                font-size: 12px;
            }
            .tabla {
                width: 100%;
                border: 1px solid black;
                margin-top: -8px;
                margin-bottom: 20px;
            }
            .tabla thead{
                text-align: center;
                background-color: #BFBFBF;
                font-style: bold;
                font-size: 12px;
                padding: 10px;
            }
            .tabla td{
                font-size: 10px;
                border: 1px solid black;
                padding: 5px;
            }
            .tabla tfoot{
                text-align: center;
                background-color: #BFBFBF;
                font-style: bold;
                font-size: 12px;
                padding: 10px;
            }
        </style>
    @endsection

    @section('pdf_content')
        <div class="col-12 titulo mb-1">
            <p id="reporte"><b>Año de Ingreso de Alumnos</b></p>
        </div>
        <table class="filtros">
            <tr>
                <td>
                    <b>Periodo Inicio:</b> {{$semestre_inicio->nombre}}
                    <br>
                    <b>Periodo Fin:</b> {{$semestre_fin->nombre}}
                </td>
            </tr>
        </table>
        <table class="tabla">
            <thead>
                <tr>
                    <th style="width: 15%">N° Documento</th>
                    <th style="width: 30%">Alumno</th>
                    <th style="width: 30%">Carrera</th>
                    <th style="width: 10%">Periodo</th>
                    <th style="width: 15%">Matriculacion</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($matriculaciones as $matriculacion)
                    @php
                        $apellido_nombre = $matriculacion->alumno->primer_apellido;
                            if ($matriculacion->alumno->segundo_apellido) {
                                $apellido_nombre .= ' ' . $matriculacion->alumno->segundo_apellido;
                            }
                            $alumno = $apellido_nombre . ', ' . $matriculacion->alumno->primer_nombre;
                            if ($matriculacion->alumno->segundo_nombre) {
                                $alumno .= ' ' . $matriculacion->alumno->segundo_nombre;
                            }
                            if ($matriculacion->alumno->tercer_nombre) {
                                $alumno .= ' ' . $matriculacion->alumno->tercer_nombre;
                            }
                    @endphp
                    <tr>
                        <td>{{$matriculacion->alumno->numero_documento}}</td>
                        <td>{{$alumno}}</td>
                        <td>{{$matriculacion->carrera->nombre_real}}</td>
                        <td class="text-center">{{$matriculacion->semestre->nombre}}</td>
                        <td class="text-center">{{Carbon\Carbon::parse($matriculacion->fecha)->format('d/m/Y')}}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5"><b>Cantidad de Alumnos:</b> {{$matriculaciones->count()}}</th>
                </tr>
            </tfoot>
        </table>
    @endsection
@endcan
