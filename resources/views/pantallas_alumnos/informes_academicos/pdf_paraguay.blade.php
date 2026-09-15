@can('ver_informes_academicos_alumnos_pantalla')
    @extends('layouts.pdf.notas')
    @section('css')
        <style>
            .titulo {
                width: 100%;
                text-align: center;
                margin-top: 20px;
                font-size: 16px;
            }
            #certificado {
                margin-bottom: -1px;
            }
            .alumno {
                width: 100%;
                height: 65px;
                font-size: 12px;
            }
            .semestres {
                width: 100%;
                border: 1px solid black;
                margin-top: -8px;
                margin-bottom: 20px;
            }
            .semestres thead{
                text-align: center;
                background-color: #BFBFBF;
                font-style: bold;
                font-size: 12px;
                padding: 10px;
            }
            .nombre_semestre{
                border-bottom: 1px solid black;
            }
            .semestres td{
                font-size: 10px;
                border: 1px solid black;
                padding: 5px;
            }
            #totales{
                font-size: 12px;
                font-weight: bold;
                margin-top: -12px;
                width: 100%;
                align-content: right;
                align-items: right;
                text-align: right;
                align-self: right;
                padding-right: 3em;
            }
        </style>
    @endsection

    @section('pdf_content')
        <div class="col-12 titulo mb-1">
            <p id="certificado"><b>INFORME ACADEMICO</b></p>
        </div>
        <table class="alumno">
            <tr>
                <td><b>Carrera:</b> {{$carrera->nombre_real}}</td>
            </tr>
            <tr>
                <td><b>Apellidos y Nombres:</b> {{$alumno->apellido_nombre}}</td>
                <td><b>Documento N°:</b> {{$alumno->numero_documento}}</td>
            </tr>
        </table>
        @foreach ($semestres as $semestre)
            <table class="semestres">
                <thead>
                    <tr class="nombre_semestre">
                        <th colspan="5" style="text-align: left; font-size: 12px;">Semestre {{$semestre['numero']}}
                            @if ($semestre['cantidad_materias'] == $alumno_notas->where('semestre_materia', $semestre['numero'])->count())
                                (Completo)
                            @else
                                (Incompleto)
                            @endif
                        </th>
                    </tr>
                    <tr>
                        <th style="width: 40%">Asignatura</th>
                        <th style="width: 15%">Fecha</th>
                        <th style="width: 15%">N° Acta</th>
                        <th style="width: 15%">Puntaje</th>
                        <th style="width: 15%">Calificación</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($alumno_notas as $nota)
                        @if ($nota->semestre_materia == $semestre['numero'])
                            <tr>
                                <td>{{$nota->materia->nombre_real}}</td>
                                <td class="text-center">{{\Carbon\Carbon::parse($nota->fecha)->format('d/m/Y')}}</td>
                                <td class="text-center">{{$nota->numero_acta}}</td>
                                <td class="text-center">{{$nota->puntaje_total}}</td>
                                <td class="text-center">{{$nota->calificacion}} ({{$nota->calificacion_letras}})</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        @endforeach
        <table id="totales">
            <tbody>
                <tr>
                    <td>PROMEDIO: {{number_format($promedio_general, 2, ',', '.')}}</td>
                </tr>
            </tbody>
        </table>
    @endsection
@endcan
