@can('ver_reportes_academicos_salas_clases')
    @extends('layouts.pdf.notas')
    @section('css')
        <style>
            .tabla {
                width: 100%;
                border: 1px solid black;
                margin-top: 1em;
            }
            .tabla thead {
                text-align: center;
                background-color: #BFBFBF;
                font-style: bold;
                font-size: 12px;
                padding: 10px;
                background-color: #002663;
                color: white;
            }
            .tabla td {
                font-size: 10px;
                border: 1px solid black;
                padding-left: 5px;
            }
            .dia {
                text-align: center;
                background-color: #002663;
                color: white;
                font-weight: bold;
                padding-left: 0!important;
            }
            .titulos {
                text-align: center;
                background-color: #002663;
                color: white;
                padding-left: 0!important;
            }
        </style>
    @endsection

    @section('pdf_content')
        <table class="tabla">
            <thead>
                <tr>
                    <th colspan="5">
                        Salas de Clases {{$programa->nombre}}
                        <br>
                        {{ $semestre->nombre }}
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($dias_semana as $dia)
                    <tr>
                        <td class="dia" colspan="5">{{$dia->nombre}}</td>
                    </tr>
                    <tr class="titulos">
                        <td>Docente</td>
                        <td>Curso</td>
                        <td>Sesión</td>
                        <td>Sala</td>
                        <td>HyFlex</td>
                    </tr>
                    @foreach ($horarios as $horario)
                        @if ($horario['dia_semana_id'] == $dia->id)
                            <tr>
                                <td>{{$horario['docente']}}</td>
                                <td>{{$horario['materia']}}</td>
                                <td>{{Str::title($dia->nombre)}} {{$horario['hora_inicio']}} a {{$horario['hora_fin']}}hs.</td>
                                <td style="text-align: center">{{$horario['aula']}}</td>
                                <td style="text-align: center">{{$horario['hyflex']}}</td>
                            </tr>
                        @endif
                    @endforeach
                @endforeach
            </tbody>
        </table>
    @endsection
@endcan
