{{-- @can('ver_reportes_horas_docentes') --}}
    @extends('layouts.pdf.reportes')
    @section('css')
        <style>
            .titulo {
                width: 100%;
                text-align: center;
                margin-top: 20px;
            }
            .lista-1 {
                width: 100%;
                margin-top: 1cm;
                border: 1px solid black;
            }
            .lista-1 td{
                font-size: 12px;
                border: 1px solid black;
                padding: 5px;
            }
            .lista-2 {
                width: 100%;
                margin-top: 0.3cm;
                border: 1px solid black;
            }
            .lista-2 thead{
                text-align: center;
                background-color: #002663;
                color: white;
                font-style: bold;
                font-size: 12px;
            }
            .lista-2 tbody td{
                text-align: center;
                font-size: 12px;
                border: 1px solid black;
                padding: 5px;
            }
            .lista-2 tfoot{
                text-align: center;
                background-color: #002663;
                color: white;
                font-style: bold;
                font-size: 12px;
            }
            .lista-3 {
                width: 100%;
                margin-top: 0.3cm;
                border: 1px solid black;
            }
            .lista-3 thead{
                text-align: center;
                background-color: #002663;
                color: white;
                font-style: bold;
                font-size: 12px;
            }
            .lista-3 tbody td{
                text-align: center;
                font-size: 12px;
                border: 1px solid black;
                padding: 5px;
            }
        </style>
    @endsection

    @section('pdf_content')
        <div class="col-12 titulo mb-3">
            <p><b>Asistencia mensual de docentes para honorarios</b></p>
        </div>
        <div>
            <p><b>Fecha Inicio:</b> {{$fecha_inicio->format('d/m/Y')}} - <b>Fecha Fin:</b> {{$fecha_fin->format('d/m/Y')}}</p>
        </div>
        <table class="lista-1">
            <tr>
                <td style="width: 40%"><b>Docente:</b> {{$docente->primer_nombre}} {{$docente->primer_apellido}}</td>
                <td style="width: 10%; text-align:center"><b>Virtual</b></td>
                <td style="width: 10%; text-align:center"><b>Presencial (Teams)</b></td>
                <td style="width: 10%; text-align:center"><b>Presencial</b></td>
            </tr>
            <tr>
                <td><b>N° Documento:</b> {{$docente->numero_documento}}</td>
                <td style="text-align:center">Gs. {{number_format($monto_virtual, 0, ',', '.')}}</td>
                <td style="text-align:center">Gs. {{number_format($monto_teams, 0, ',', '.')}}</td>
                <td style="text-align:center">Gs. {{number_format($monto_presencial, 0, ',', '.')}}</td>
            </tr>
        </table>
        <table class="lista-2">
            <thead>
                <tr>
                    <th colspan="4">Mes: {{$mes}}</th>
                </tr>
                <tr>
                    <th colspan="4">Carrera:{{$carrera}}</th>
                </tr>
                <tr>
                    <th colspan="4">Programa: {{ $programa }}</th>
                </tr>
                <tr style="background-color: white!important; color:black!important;">
                    <th style="width: 40%; text-align:center"><b>Asignatura</th>
                    <th style="width: 20%; text-align:center"><b>Horas Virtuales</b></th>
                    <th style="width: 20%; text-align:center"><b>Horas Presenciales (Teams)</b></th>
                    <th style="width: 20%; text-align:center"><b>Horas Presenciales</b></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($materias as $materia)
                    <tr>
                        <td style="text-align: left!important">{{$materia['materia']}}</td>
                        <td>{{$materia['horas_virtuales']}}</td>
                        <td>{{$materia['horas_teams']}}</td>
                        <td>{{$materia['horas_presenciales']}}</td>
                    </tr>
                @endforeach
                <tr style="background-color: lightgray!important; font-style: bold">
                    <td style="text-align: left!important">Total de Horas</td>
                    <td>{{$total_horas_virtuales}}</td>
                    <td>{{$total_horas_teams}}</td>
                    <td>{{$total_horas_presenciales}}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td>Monto Total</td>
                    <td colspan="3">Gs. {{$monto_total}}</td>
                </tr>
            </tfoot>
        </table>
        @if ($materias->filter(function ($materia) { return $materia['observaciones'] != null; })->isNotEmpty())
            <table class="lista-3">
                <thead>
                    <tr>
                        <th colspan="2">Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="width: 40%"><b>Asignatura</b></td>
                        <td style="width: 60%"><b>Observaciones</b></td>
                    </tr>
                    @foreach ($materias as $materia)
                            @if ($materia['observaciones'] != null)
                                <tr>
                                    <td style="text-align: left!important;">{{$materia['materia']}}</td>
                                    <td style="text-align: left!important">{{$materia['observaciones']}}</td>
                                </tr>
                            @endif
                    @endforeach
                </tbody>
            </table>
        @endif
    @endsection
{{-- @endcan --}}
