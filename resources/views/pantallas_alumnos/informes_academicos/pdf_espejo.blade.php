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
                height: 40px;
                font-size: 12px;
            }
            .materias {
                width: 100%;
                border: 1px solid black;
                margin-top: -8px;
                margin-bottom: 20px;
            }
            .materias thead{
                text-align: center;
                background-color: #BFBFBF;
                font-style: bold;
                font-size: 12px;
                padding: 10px;
            }
            .nombre_carrera{
                border-bottom: 1px solid black;
            }
            .materias td{
                font-size: 10px;
                border: 1px solid black;
                padding: 5px;
            }
            .periodo {
                background-color: #BFBFBF;
            }
        </style>
    @endsection

    @section('pdf_content')
        <div class="col-12 titulo mb-1">
            <p id="certificado"><b>INFORME ACADEMICO</b></p>
        </div>
        <table class="alumno">
            <tr>
                <td><b>Apellidos y Nombres:</b> {{$alumno->apellido_nombre}}</td>
                <td><b>Documento N°:</b> {{$alumno->numero_documento}}</td>
            </tr>
        </table>
        <table class="materias">
            <thead>
                <tr class="nombre_carrera">
                    <th colspan="3" style="text-align: center; font-size: 12px;">
                        Malla Nacional ({{ Str::title($carrera_paraguay->nombre_real) }})
                    </th>
                    <th style="border: 1px solid black"></th>
                    <th colspan="3" style="text-align: center; font-size: 12px;">
                        SIU ({{ Str::title($carrera_siu->nombre_real) }})
                    </th>
                </tr>
                <tr>
                    <th style="width: 10%">Semestre</th>
                    <th style="width: 25%">Materia</th>
                    <th style="width: 10%">Calificación</th>
                    <th class="periodo" style="width: 10%; border: 1px solid black">Periodo</th>
                    <th style="width: 10%">Semestre</th>
                    <th style="width: 25%">Materia</th>
                    <th style="width: 10%">Calificación</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($espejos as $espejo)
                    <tr>
                        <td class="text-center">{{$espejo['semestre_materia_paraguay']}}</td>
                        <td class="text-center">{{$espejo['materia_paraguay']}}</td>
                        <td class="text-center">{{$espejo['nota_paraguay']}}</td>
                        <td class="text-center periodo">{{$espejo['periodo']}}</td>
                        @php
                            $text_color = '';
                            if ($espejo['estado']) {
                                $text_color = 'red';
                            }
                        @endphp
                        <td class="text-center" style="color:{{$text_color}}">{{$espejo['semestre_materia_siu']}}</td>
                        <td class="text-center" style="color:{{$text_color}}">{{$espejo['materia_siu']}}</td>
                        <td class="text-center" style="color:{{$text_color}}">{{$espejo['nota_siu']}}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="text-center" style="font-weight:bold;">
                    <td colspan="3">Total de Materias: {{$cantidad_materias_paraguay}}</td>
                    <td colspan="3">Total de Materias: {{$cantidad_materias_siu}}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    @endsection
@endcan
