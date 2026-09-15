@can('imprimir_horarios_semestres_matriculaciones')
    @extends('layouts.pdf.notas')
    @section('css')
        <style>
            .titulo {
                width: 100%;
                text-align: center;
                margin-top: 20px;
                font-size: 20px;
            }
            #horario {
                margin-bottom: -1px;
            }
            #tabla {
                width: 100%;
                text-align: center;
                font-size: 12px;
            }
            .horarios {
                width: 100%;
                margin: 0 auto;
                margin-bottom: 15px;
                margin-top: 0.5cm;
            }
            .horarios thead{
                text-align: center;
                background-color: #002663;
                color: white;
                font-style: bold;
                font-size: 12px;
                padding: 10px;
            }
            .horarios td{
                font-size: 10px;
                padding: 5px;
                border: 1px solid black;
            }
            .horarios tfoot{
                text-align: center;
                background-color: #002663;
                color: white;
                font-style: bold;
                font-size: 12px;
                padding: 10px;
            }
        </style>
    @endsection

    @section('pdf_content')
        <div class="col-12 titulo mb-1">
            <p id="horario"><b>HORARIOS DE CLASES - {{$periodo_activo->nombre}}</b></p>
        </div>
        <div class="tabla">
            <table class="horarios">
                <thead>
                    <tr>
                        <th></th>
                        @foreach ($dias_semana as $key => $dia)
                            @if ($key != 0 && $key != 6)
                                <th>{{$dia->nombre}}</th>
                            @endif
                        @endforeach
                    </tr>
                </thead>
                <tbody class="text-center">
                    @foreach ($horarios_clases as $key => $hora)
                        @if (!$loop->last)
                            @php
                                $hora_inicio = $hora;
                                $hora_fin = $horarios_clases[$key + 1];
                            @endphp
                            @if (!($hora_inicio->format('H:i') === '14:00' && $hora_fin->format('H:i') === '18:00'))
                                <tr>
                                    <td style="font-style: bold">{{$hora->format('H:i')}} a {{$horarios_clases[$key + 1]->format('H:i')}}</td>
                                    @foreach ($dias_semana as $k => $dia)
                                        @if ($k != 0 && $k != 6)
                                            @php
                                                $materias = [];
                                            @endphp
                                            @foreach ($horarios as $horario)
                                                @php
                                                    $hora_inicio_horario = Carbon\Carbon::createFromFormat('H:i:s', $horario['hora_inicio']);
                                                    $hora_fin_horario = Carbon\Carbon::createFromFormat('H:i:s', $horario['hora_fin']);
                                                @endphp
                                                @if (($hora_inicio_horario->lt($hora_fin) && $hora_fin_horario->gt($hora_inicio)) && $horario['dia'] == $dia->nombre)
                                                    @php
                                                        $materias[] = $horario['materia'];
                                                    @endphp
                                                @endif
                                            @endforeach
                                            <td>{!! !empty($materias) ? implode("<br><br>", $materias) : '-' !!}</td>
                                        @endif
                                    @endforeach
                                </tr>
                            @endif
                        @endif
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6">Total de Materias: {{number_format($horarios->count(), 0, ',', '.')}}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endsection
@endcan
