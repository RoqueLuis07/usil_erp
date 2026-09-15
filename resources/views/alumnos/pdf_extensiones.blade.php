@can('ver_extensiones_alumnos')
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
        <div class="page-break">
            <div class="col-12 titulo mb-3">
                <p><b>Reporte de Extensiones Universitarias</b></p>
            </div>
            <table>
                <tr>
                    <td><b>Alumno:</b> {{$alumno->primer_nombre}} {{$alumno->primer_apellido}}</td>
                </tr>
                <tr>
                    <td><b>N° Documento:</b> {{$alumno->numero_documento}}</td>
                </tr>
            </table>
            <table class="lista">
                <thead>
                    <tr>
                        <th style="width: 3%">#</th>
                        <th style="width: 35%">Proyecto</th>
                        <th style="width: 20%">Responsable</th>
                        <th style="width: 5%">Horas Totales</th>
                        <th style="width: 5%">Horas Cumplidas</th>
                        <th style="width: 5%">Período</th>
                        <th style="width: 10%">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($extensiones as $key => $extension)
                        @php
                            switch ($extension->extensionUniversitaria->estado) {
                                case ('PE'):
                                    $estado = 'PENDIENTE';
                                    $color = '#FFFBDF';
                                    break;
                                case ('AP'):
                                    $estado = 'APROBADO';
                                    $color = '#DEF3F8';
                                    break;
                                case ('IN'):
                                    $estado = 'INFORMADO';
                                    $color = '#DEF3F8';
                                    break;
                                case ('RE'):
                                    $estado = 'RECHAZADO';
                                    $color = '#FFEAE8';
                                    break;
                                case ('CO'):
                                    $estado = 'COMPLETO';
                                    $color = '#E7EAFF';
                                    break;
                                case ('FI'):
                                    $estado = 'FINALIZADO';
                                    $color = '#DEFBE8';
                                    break;
                                default:
                                    $estado = '';
                                    $color = '';
                                    break;
                            }
                        @endphp
                        <tr style="background-color: {{$color}};">
                            <td>{{$key + 1}}</td>
                            <td>{{$extension->extensionUniversitaria->nombre}}</td>
                            <td>{{$extension->extensionUniversitaria->docente->primer_nombre}} {{$extension->extensionUniversitaria->docente->primer_apellido}}</td>
                            @php
                                if ($extension->extensionUniversitaria->cantidad_horas != 1) {
                                    $texto = 'horas';
                                } else {
                                    $texto = 'hora';
                                }
                            @endphp
                            <td class="text-center">{{number_format($extension->extensionUniversitaria->cantidad_horas, 0, ',', '.')}} {{$texto}}</td>
                            @php
                                if ($extension->cantidad_horas != 1) {
                                    $texto = 'horas';
                                } else {
                                    $texto = 'hora';
                                }
                            @endphp
                            <td class="text-center">{{number_format($extension->cantidad_horas, 0, ',', '.')}} {{$texto}}</td>
                            <td class="text-center">{{$extension->periodo}}</td>
                            <td class="text-center">
                                {{$estado}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div>
            <div class="col-12 titulo mb-3">
                <p><b>Resumen</b></p>
            </div>
            <table class="lista">
                <thead>
                    <tr>
                        <th style="width: 40%">Tipo de Actividad</th>
                        <th style="width: 20%">Cant. Realizada</th>
                        <th style="width: 10%">Horas Totales</th>
                        <th style="width: 10%">Horas Realizadas</th>
                        <th style="width: 10%">Horas Acreditadas</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($tipos_actividades as $tipo)
                    <tr>
                        <td>{{$tipo->nombre}}</td>
                        @php
                            if ($extensiones->count() != 1) {
                                $texto = 'actividades';
                            } else {
                                $texto = 'actividad';
                            }
                        @endphp
                        <td class="text-center">
                            @switch($tipo->id)
                                @case(1)
                                    @php
                                        if ($cantidad_realizada_1 != 1) {
                                            $texto = 'actividades';
                                        } else {
                                            $texto = 'actividad';
                                        }
                                    @endphp
                                    {{number_format($cantidad_realizada_1, 0, ',', '.')}} {{$texto}}
                                    @break
                                @case(2)
                                    @php
                                        if ($cantidad_realizada_2 != 1) {
                                            $texto = 'actividades';
                                        } else {
                                            $texto = 'actividad';
                                        }
                                    @endphp
                                    {{number_format($cantidad_realizada_2, 0, ',', '.')}} {{$texto}}
                                    @break
                                @case(3)
                                    @php
                                        if ($cantidad_realizada_3 != 1) {
                                            $texto = 'actividades';
                                        } else {
                                            $texto = 'actividad';
                                        }
                                    @endphp
                                    {{number_format($cantidad_realizada_3, 0, ',', '.')}} {{$texto}}
                                    @break
                                @case(4)
                                    @php
                                        if ($cantidad_realizada_4 != 1) {
                                            $texto = 'actividades';
                                        } else {
                                            $texto = 'actividad';
                                        }
                                    @endphp
                                    {{number_format($cantidad_realizada_4, 0, ',', '.')}} {{$texto}}
                                    @break
                                @default
                                    @break
                            @endswitch
                        </td>
                        @php
                            if ($tipo->maxima_cantidad_horas != 1) {
                                $texto = 'horas';
                            } else {
                                $texto = 'hora';
                            }
                        @endphp
                        <td class="text-center">{{number_format($tipo->maxima_cantidad_horas, 0, ',', '.')}} {{$texto}}</td>
                        <td class="text-center">
                            @switch($tipo->id)
                                @case(1)
                                    @php
                                        if ($horas_realizadas_1 != 1) {
                                            $texto = 'horas';
                                        } else {
                                            $texto = 'hora';
                                        }
                                    @endphp
                                    {{number_format($horas_realizadas_1, 0, ',', '.')}} {{$texto}}
                                    @break
                                @case(2)
                                    @php
                                        if ($horas_realizadas_2 != 1) {
                                            $texto = 'horas';
                                        } else {
                                            $texto = 'hora';
                                        }
                                    @endphp
                                    {{number_format($horas_realizadas_2, 0, ',', '.')}} {{$texto}}
                                    @break
                                @case(3)
                                    @php
                                        if ($horas_realizadas_3 != 1) {
                                            $texto = 'horas';
                                        } else {
                                            $texto = 'hora';
                                        }
                                    @endphp
                                    {{number_format($horas_realizadas_3, 0, ',', '.')}} {{$texto}}
                                    @break
                                @case(4)
                                    @php
                                        if ($horas_realizadas_4 != 1) {
                                            $texto = 'horas';
                                        } else {
                                            $texto = 'hora';
                                        }
                                    @endphp
                                    {{number_format($horas_realizadas_4, 0, ',', '.')}} {{$texto}}
                                    @break
                                @default
                                    @break
                            @endswitch
                        </td>
                        <td class="text-center">
                            @switch($tipo->id)
                                @case(1)
                                    @php
                                        if ($horas_acreditadas_1 != 1) {
                                            $texto = 'horas';
                                        } else {
                                            $texto = 'hora';
                                        }
                                    @endphp
                                    {{number_format($horas_acreditadas_1, 0, ',', '.')}} {{$texto}}
                                    @break
                                @case(2)
                                    @php
                                        if ($horas_acreditadas_2 != 1) {
                                            $texto = 'horas';
                                        } else {
                                            $texto = 'hora';
                                        }
                                    @endphp
                                    {{number_format($horas_acreditadas_2, 0, ',', '.')}} {{$texto}}
                                    @break
                                @case(3)
                                    @php
                                        if ($horas_acreditadas_3 != 1) {
                                            $texto = 'horas';
                                        } else {
                                            $texto = 'hora';
                                        }
                                    @endphp
                                    {{number_format($horas_acreditadas_3, 0, ',', '.')}} {{$texto}}
                                    @break
                                @case(4)
                                    @php
                                        if ($horas_acreditadas_4 != 1) {
                                            $texto = 'horas';
                                        } else {
                                            $texto = 'hora';
                                        }
                                    @endphp
                                    {{number_format($horas_acreditadas_4, 0, ',', '.')}} {{$texto}}
                                    @break
                                @default
                                    @break
                            @endswitch
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endsection
@endcan
