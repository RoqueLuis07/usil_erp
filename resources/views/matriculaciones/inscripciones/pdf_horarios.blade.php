@can('imprimir_horarios_inscripciones_matriculaciones')
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
            #primer_parrafo {
                position: fixed;
                width: 100%;
                height: auto;
                display: flex;
                font-size: 12px;
            }
            #programa_alumno {
                width: 500px;
                height: auto;
                display: inline-block;
                vertical-align: top;
            }
            #carreras {
                display: inline-block;
                vertical-align: top;
                width: 420px;
                height: auto;
                position: relative;
            }
            #tabla {
                width: 100%;
                text-align: center;
                font-size: 12px;
            }
            .horarios {
                width: 80%;
                margin: 0 auto;
                margin-bottom: 15px;
                margin-top: 2.5cm;
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
            #segundo_parrafo {
                width: 100%;
                text-align: justify;
                font-size: 12px;
            }
            #lista_observaciones {
                margin-top: -10px;
            }
            #firmas {
                position: fixed;
                width: 90%;
                height: auto;
                display: flex;
                align-items: center;
                justify-content: center;
                text-align: center;
                font-size: 14px;
                font-weight: bold;
                margin-top: 1.5cm;
            }
            #fecha {
                width: 200px;
                height: auto;
                display: inline-block;
                vertical-align: top;
                text-align: center;
                border-top: 1px solid black;
                margin-right: 50px;
            }
            #fecha_hoy {
                position: absolute;
                top: -30px;
                left: 0;
                width: 21%;
                padding: 5px;
            }
            #firma {
                display: inline-block;
                vertical-align: top;
                width: 200px;
                height: auto;
                text-align: center;
                border-top: 1px solid black;
                margin-left: 50px;
                position: relative;
            }
        </style>
    @endsection

    @section('pdf_content')
        <div class="col-12 titulo mb-1">
            <p id="horario"><b>HORARIO DE CLASES</b></p>
        </div>
        <div id="primer_parrafo">
            <div id="programa_alumno">
                <p>
                    <b>Nombre y Apellido:</b> {{$nombre_alumno}}
                    <br>
                    <b>N° de Documento:</b> {{number_format($documento_alumno, 0, ',', '.')}}
                </p>
            </div>
            <div id="carreras">
                <p>
                    <b>Programa:</b> {{$programa}}
                    <br>
                    <b>Semestre:</b> {{$semestre}}
                    <br>
                    <b>Carrera:</b> {{$carrera_py}}
                    @if ($existe_siu == 'SI')
                        <br>
                        <b>Carrera SIU:</b> {{$carrera_siu}}
                    @endif
                </p>
            </div>
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
                        <tr>
                            @if (!$loop->last)
                                @php
                                    $hora_inicio = $hora;
                                    $hora_fin = $horarios_clases[$key + 1];
                                @endphp
                                @if (!($hora_inicio->format('H:i') === '14:00' && $hora_fin->format('H:i') === '18:00'))
                                    <tr>
                                        <td class="fw-bold">{{$hora->format('H:i')}} a {{$horarios_clases[$key + 1]->format('H:i')}}</td>
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
                                                <td style="1px solid black;">{!! !empty($materias) ? implode("<br>", $materias) : '-' !!}</td>
                                            @endif
                                        @endforeach
                                    </tr>
                                @endif
                            @endif
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6">Total de Materias Matriculadas: {{number_format($total_materias, 0, ',', '.')}}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div id="segundo_parrafo">
            <p>
                <b>Observaciones:</b>
                <ol id="lista_observaciones">
                    <li>Estar al día en el pago de la última cuota del semestre, 48 horas antes del Examen Final.</li>
                    <li>No estar atrasado en ningún compromiso contraído con la Universidad San Ignacio de Loyola.</li>
                    <li>No estar afectado por ninguna medida disciplinaria.</li>
                    <li>Cumplir con el porcentaje de asistencia mínimo, conforme a lo estipulado en la planilla de Cátedra.</li>
                    <li>Cumplir con el porcentaje de evaluación contínua, conforme a lo estipulado en la planilla de Cátedra.</li>
                    <li>
                        <b>Fechas de Vencimiento de Cuotas:</b>
                        <ul>
                            @foreach ($vencimientos as $vencimiento)
                                <li>{{$vencimiento['nombre']}}: {{$vencimiento['fecha']}}</li>
                            @endforeach
                        </ul>
                    </li>
                </ol>
            </p>
            <p>Declaro estar en conocimiento de las observaciones arriba citadas y acepto plenamente todos sus términos.</p>
        </div>
        <div id="firmas">
            <div id="fecha">
                <div>
                    <span id="fecha_hoy">{{Carbon\Carbon::createFromDate($fecha_hoy)->format('d/m/Y')}}</span>
                    <br>
                    Fecha
                </div>
            </div>
            <div id="firma">
                <div>

                    <br>
                    Firma y Aclaración
                </div>
            </div>
        </div>
    @endsection
@endcan
