@can('generar_actas_evaluaciones_materias_semestres')
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
            <p id="programa"><b>{{$carrera->programa->nombre}}</b></p>
            <p><b>{{$titulo}}</b></p>
        </div>
        <table id="subtitulos">
            <tr>
                <td>
                    <b>Carrera:</b> {{$carrera->nombre_real}}
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
                    <b>Semestre:</b> {{$semestre_materia}}
                    <br>
                    <b>Seccion:</b> {{$carrera->programa->turno}}
                    <br>
                    <b>Turno:</b> @if ($carrera->programa->turno == 'M') MAÑANA @elseif ($carrera->programa->turno == 'T') TARDE @elseif ($carrera->programa->turno == 'N') NOCHE @endif
                </td>
            </tr>
        </table>
        <table id="lista">
            <thead>
                <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 30%">Apellido y Nombre</th>
                    <th style="width: 10%">N° Documento</th>
                    <th style="width: 15%">Evaluación Continua</th>
                    <th style="width: 10%">Examen Final <br> 40</th>
                    <th style="width: 10%">Calificación Final</th>
                    <th style="width: 20%">Firma</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($alumnos as $key => $alumno)
                    <tr>
                        <td>{{$key + 1}}</td>
                        <td>
                            {{ trim($alumno->primer_apellido . ($alumno->segundo_apellido ? ' ' . $alumno->segundo_apellido : '')) }},
                            {{$alumno->primer_nombre}}
                            @if ($alumno->segundo_nombre)
                                {{$alumno->segundo_nombre}}
                            @endif
                            @if ($alumno->tercer_nombre)
                                {{$alumno->tercer_nombre}}
                            @endif
                        </td>
                        <td class="text-center">{{$alumno->numero_documento}}</td>
                        <td class="text-center">{{$alumno->puntos_obtenidos}}</td>
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
