@can('ver_reportes_academicos_fechas_examenes')
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
            .text-center {
                text-align: center;
                padding-left: 0!important;
            }
        </style>
    @endsection

    @section('pdf_content')
        <table class="tabla">
            <thead>
                <tr>
                    <th colspan="7">
                        Fechas de Exámenes
                        <br>
                        {{$programa->nombre}}
                    </th>
                </tr>
                <tr class="titulos">
                    <td>Período</td>
                    <td>Docente</td>
                    <td>Curso</td>
                    <td>Examen Parcial</td>
                    <td>Examen Ordinario</td>
                    <td>Examen Complementario</td>
                    <td>Examen Extraordinario</td>
                </tr>
            </thead>
            <tbody>
                @foreach ($fechas as $fecha)
                    <tr>
                        <td class="text-center">{{$semestre->nombre}}</td>
                        <td>{{$fecha['docente']}}</td>
                        <td>{{$fecha['materia']}}</td>
                        <td class="text-center">{{$fecha['parcial']}}</td>
                        <td class="text-center">{{$fecha['ordinario']}}</td>
                        <td class="text-center">{{$fecha['complementario']}}</td>
                        <td class="text-center">{{$fecha['extraordinario']}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endsection
@endcan
