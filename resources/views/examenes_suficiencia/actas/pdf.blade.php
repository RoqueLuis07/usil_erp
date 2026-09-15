@can('generar_actas_examenes_suficiencia')
    @extends('layouts.pdf.reportes')
    @section('css')
        <style>
            .titulo {
                width: 100%;
                text-align: center;
                margin-top: 20px;
            }
            #programa {
                margin-bottom: -1px;
            }
            #subtitulos {
                width: 100%;
                border-collapse: collapse;
                font-size: 12px;
            }
            #subtitulos td {
                width: 50%;
                vertical-align: bottom;
            }
            #lista {
                width: 100%;
                margin-top: 1cm;
                border: 1px solid black;
            }
            #lista thead{
                text-align: center;
                background-color: #002663;
                color: white;
                font-style: bold;
                font-size: 12px;
                padding: 10px;
            }
            #lista td{
                font-size: 12px;
                border: 1px solid black;
                padding: 5px;
            }
            #firma_aclaracion {
                position: fixed;
                width: 90%;
                height: auto;
                display: flex;
                align-items: center;
                justify-content: center;
                text-align: center;
                margin-top: 3cm;
            }
            #firma {
                width: 200px;
                height: auto;
                display: inline-block;
                vertical-align: top;
                text-align: center;
                border-top: 1px solid black;
                margin-right: 50px;
            }
            #aclaracion {
                display: inline-block;
                vertical-align: top;
                width: 200px;
                height: auto;
                text-align: center;
                border-top: 1px solid black;
                margin-left: 50px;
                position: relative;
            }
            #nombre_docente {
                position: absolute;
                top: -50px;
                left: 0;
                width: 95%;
                padding: 5px;
            }
        </style>
    @endsection

    @section('pdf_content')
        <div class="col-12 titulo mb-3">
            <p><b>{{$titulo}}</b></p>
        </div>
        <table id="subtitulos">
            <tr>
                <td>
                    <br><br>
                    <b>Acta N°:</b> {{$numero}}
                    <br><br>
                    <b>Asignatura:</b> {{$materia->nombre_real}}
                    <br><br>
                    <b>Oportunidad:</b> {{$oportunidad}}
                    <br><br>
                    <b>Examinadores:</b> {{$prenombre_docente}} {{$docente->primer_nombre}} {{$docente->primer_apellido}}
                </td>
                <td>
                    <b>Fecha de evaluación:</b> {{\Carbon\Carbon::parse($fecha_evaluacion)->format('d/m/Y')}}
                    <br><br>
                    <b>Periodo Lectivo:</b> {{$semestre->nombre}}
                    <br><br>
                    <b>Seccion:</b> {{$seccion}}
                    <br>
                    <b>Turno:</b> {{$turno}}
                </td>
            </tr>
        </table>
        <table id="lista">
            <thead>
                <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 30%">Apellido y Nombre</th>
                    <th style="width: 15%">N° Documento</th>
                    <th style="width: 15%">Examen Final <br> 100</th>
                    <th style="width: 15%">Calificación Final</th>
                    <th style="width: 20%">Firma</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($alumnos as $key => $detalle)
                    <tr>
                        <td>{{$key + 1}}</td>
                        <td>
                            {{ trim($detalle->primer_apellido . ($detalle->segundo_apellido ? ' ' . $detalle->segundo_apellido : '')) }},
                            {{$detalle->primer_nombre}}
                            @if ($detalle->segundo_nombre)
                                {{$detalle->segundo_nombre}}
                            @endif
                            @if ($detalle->tercer_nombre)
                                {{$detalle->tercer_nombre}}
                            @endif
                        </td>
                        <td class="text-center">{{$detalle->numero_documento}}</td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div id="firma_aclaracion">
            <div id="firma">
                <div style="font-size: 14px"><b>Firma del Docente</b></div>
            </div>
            <div id="aclaracion">
                <div style="font-size: 14px"><b>Aclaración</b></div>
                <div id="nombre_docente">{{$prenombre_docente}} {{$docente->primer_nombre}} {{$docente->primer_apellido}}</div>
            </div>
        </div>
    @endsection
@endcan
