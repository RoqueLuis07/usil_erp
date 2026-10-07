{{--  @can('generar_reportes_extensiones_universitarias') --}}
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
            <p><b>Reporte de Extensiones Universitarias</b></p>
        </div>
@php
    $total_horas = collect($extensiones)->sum('cantidad_horas');
    $total_cumplidas = $alumno != '' ? collect($extensiones)->sum(function ($e) use ($alumno) {
        return $e->extensionUniversitariaDetalles->where('alumno_id', $alumno->id)->sum('cantidad_horas');
    }) : 0;
    $total_alumnos = collect($extensiones)->sum(function ($e) {
        return $e->extensionUniversitariaDetalles->count();
    });
@endphp
        <table class="lista">
            <thead>
                <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 30%">Proyecto</th>
                    <th style="width: 10%">Tipo de Actividad</th>
                    <th style="width: 15%">Responsable</th>
                    <th style="width: 10%">Horas Totales</th>
                    @if ($alumno != '')
                        <th style="width: 10%">Horas Cumplidas</th>
                    @endif
                    <th style="width: 10%">Período</th>
                    @if ($alumno == '')
                        <th style="width: 10%">Cant. Alumno</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($extensiones as $key => $extension)
                    <tr>
                        <td class="text-center">{{$key + 1}}</td>
                        <td class="text-center">{{$extension->nombre}}</td>
                        <td class="text-center">{{$extension->tipoExtension->nombre}}</td>
                        <td class="text-center">{{$extension->docente ? $extension->docente->primer_nombre . ' ' . $extension->docente->primer_apellido : 'Sin asignar'}}</td>
                        <td class="text-center">{{number_format($extension->cantidad_horas, 2, ',', '.')}}
                            @if ($extension->cantidad_horas == 1)
                                hora
                            @else
                                horas
                            @endif
                        </td>
                        @if ($alumno != '')
                            <td class="text-center">{{number_format($extension->extensionUniversitariaDetalles->where('alumno_id', $alumno->id)->sum('cantidad_horas'), 2, ',', '.')}}
                                @if ($extension->extensionUniversitariaDetalles->where('alumno_id', $alumno->id)->sum('cantidad_horas') == 1)
                                    hora
                                @else
                                    horas
                                @endif
                            </td>
                        @endif
                        <td class="text-center">{{$extension->periodo}}</td>
                        @if ($alumno == '')
                            <td class="text-center">
                                {{$extension->extensionUniversitariaDetalles->count()}}
                                @if ($extension->extensionUniversitariaDetalles->count() == 1)
                                    alumno
                                @else
                                    alumnos
                                @endif
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="fw-bold">
                    <td colspan="4" class="text-right" style="font-weight:bold;">TOTAL</td>
                    <td class="text-center" style="font-weight:bold;">{{number_format($total_horas, 2, ',', '.')}} {{ $total_horas == 1 ? 'hora' : 'horas' }}</td>
                    @if ($alumno != '')
                        <td class="text-center" style="font-weight:bold;">{{number_format($total_cumplidas, 2, ',', '.')}} {{ $total_cumplidas == 1 ? 'hora' : 'horas' }}</td>
                    @endif
                    <td></td>
                    @if ($alumno == '')
                        <td class="text-center" style="font-weight:bold;">{{ $total_alumnos }} {{ $total_alumnos == 1 ? 'alumno' : 'alumnos' }}</td>
                    @endif
                </tr>
            </tfoot>
        </table>
        <table class="lista">
            @php
                $colspan = 0;
                if ($proyecto != '') {
                    $colspan += 1;
                }
                if ($periodo != '') {
                    $colspan += 1;
                }
                if ($tipo_extension != '') {
                    $colspan += 1;
                }
                if ($alumno != '') {
                    $colspan += 1;
                }
            @endphp
            <thead>
                <tr>
                    <th colspan="{{ $colspan }}">Filtros Aplicados</th>
                </tr>
            </thead>
            <tr>
                @if ($proyecto != '')
                    <td style="width: 35%">
                        <b>Proyecto:</b> {{$proyecto->nombre}}
                    </td>
                @endif
                @if ($periodo != '')
                    <td style="width: 15%"><b>Período:</b> {{$periodo}}</td>
                @endif
                @if ($tipo_extension != '')
                    <td style="width: 25%"><b>Tipo de Actividad:</b> {{$tipo_extension->nombre}}</td>
                @endif
                @if ($alumno != '')
                    <td style="width: 35%">
                        <b>Alumno:</b> {{ $alumno->primer_apellido }} {{$alumno->primer_nombre}}
                        <br>
                        <b>N° Documento:</b> {{$alumno->numero_documento}}
                    </td>
                @endif
            </tr>
        </table>
    @endsection
{{-- @endcan --}}
