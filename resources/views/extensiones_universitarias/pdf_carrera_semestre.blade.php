@can('generar_reportes_extensiones_universitarias_carrera_semestre')
    @extends('layouts.pdf.reportes')
    @section('css')
        <style>
            .titulo {
                width: 100%;
                text-align: center;
                margin-top: 20px;
            }
            .lista {
                width: 100%;
                margin-top: 1cm;
                border: 1px solid black;
            }
            .lista thead{
                text-align: center;
                background-color: #002663;
                color: white;
                font-style: bold;
                font-size: 12px;
                padding: 10px;
            }
            .lista td{
                font-size: 12px;
                border: 1px solid black;
                padding: 5px;
            }
        </style>
    @endsection

    @section('pdf_content')
        <div class="col-12 titulo mb-3">
            <p><b>Reporte de Extensiones Universitarias por Carrera y Semestre</b></p>
        </div>
        <table class="lista">
            <thead>
                <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 30%">Carrera</th>
                    <th style="width: 20%">Semestre</th>
                    <th style="width: 15%">Cant. Actividades</th>
                    <th style="width: 15%">Cant. Alumnos</th>
                    <th style="width: 15%">Horas Totales</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reporte as $key => $fila)
                    <tr>
                        <td class="text-center">{{$key + 1}}</td>
                        <td class="text-center">{{ $fila->carrera->nombre_fantasia ?? 'Sin carrera' }}</td>
                        <td class="text-center">{{ $fila->semestre->nombre ?? 'Sin semestre' }}</td>
                        <td class="text-center">{{ $fila->cantidad_actividades }}</td>
                        <td class="text-center">{{ $fila->cantidad_alumnos }}</td>
                        <td class="text-center">{{ number_format($fila->horas_totales, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <table class="lista">
            @php
                $colspan = 0;
                if ($carrera != '') { $colspan += 1; }
                if ($semestre != '') { $colspan += 1; }
                if ($tipo_extension != '') { $colspan += 1; }
                if ($colspan == 0) { $colspan = 1; }
            @endphp
            <thead>
                <tr>
                    <th colspan="{{ $colspan }}">Filtros Aplicados</th>
                </tr>
            </thead>
            <tr>
                @if ($carrera != '')
                    <td style="width: 35%"><b>Carrera:</b> {{$carrera->nombre_fantasia}}</td>
                @endif
                @if ($semestre != '')
                    <td style="width: 25%"><b>Semestre:</b> {{$semestre->nombre}}</td>
                @endif
                @if ($tipo_extension != '')
                    <td style="width: 25%"><b>Tipo de Actividad:</b> {{$tipo_extension->nombre}}</td>
                @endif
                @if ($carrera == '' && $semestre == '' && $tipo_extension == '')
                    <td>Sin filtros (todas las carreras y semestres)</td>
                @endif
            </tr>
        </table>
    @endsection
@endcan
