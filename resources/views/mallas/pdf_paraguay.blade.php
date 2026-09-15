@extends('layouts.pdf.notas')
@section('css')
    <style>
        .titulo {
            width: 100%;
            text-align: center;
            margin-top: 20px;
            font-size: 20px;
        }
        #malla {
            margin-bottom: -1px;
        }
        #primer_parrafo {
            position: fixed;
            width: 100%;
            height: auto;
            display: flex;
            font-size: 12px;
        }
        #tabla {
            width: 100%;
            text-align: center;
            font-size: 12px;
        }
        .nombre_semestre{
            border-bottom: 1px solid black;
        }
        .tabla-malla {
            width: 100%;
            margin: 0 auto;
            margin-bottom: 15px;
        }
        .tabla-malla thead{
            text-align: center;
            background-color: #002663;
            color: white;
            font-style: bold;
            font-size: 12px;
            padding: 10px;
        }
        .tabla-malla td{
            font-size: 10px;
            padding: 5px;
            border: 1px solid black;
        }
        .tabla-malla tfoot{
            text-align: center;
            background-color: #002663;
            color: white;
            font-style: bold;
            font-size: 10px;
            padding: 10px;
        }
        #segundo_parrafo {
            width: 100%;
            text-align: justify;
            font-size: 12px;
        }
    </style>
@endsection

@section('pdf_content')
    <div class="col-12 titulo mb-1">
        <p id="malla"><b>MALLA {{$malla->carrera->nombre_real}}</b></p>
    </div>
    <div class="tabla">
        @foreach ($semestres as $semestre)
            <table class="tabla-malla">
                <thead>
                    <tr class="nombre_semestre">
                        <th colspan="7" style="font-size: 14px;">Semestre {{$semestre}}</th>
                    </tr>
                    <tr>
                        <th style="width: 5%">Ciclo</th>
                        <th style="width: 10%">Código</th>
                        <th style="width: 30%">Asignatura</th>
                        <th style="width: 5%">Horas</th>
                        <th style="width: 10%">Créditos</th>
                        <th style="width: 10%">Área Curricular</th>
                        <th style="width: 30%">Pre-requisito</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @foreach ($malla->mallaDetalles as $detalle)
                        @if ($detalle->semestre == $semestre)
                            <tr>
                                <td>{{$detalle->semestre}}</td>
                                <td>{{$detalle->materia->codigo}}</td>
                                <td>{{$detalle->materia->nombre_real}}</td>
                                <td>{{$detalle->carga_horaria}}</td>
                                <td>{{$detalle->cantidad_creditos}}</td>
                                <td>{{$detalle->area_curricular}}</td>
                                <td>
                                    @php
                                        $encontrado = false;
                                    @endphp
                                    @foreach ($correlatividades as $correlatividad)
                                        @if ($detalle->materia_id == $correlatividad['materia_id'])
                                            {{$correlatividad['correlativa']}}
                                            <br>
                                            @php
                                                $encontrado = true;
                                            @endphp
                                        @endif
                                    @endforeach
                                    @if (!$encontrado)
                                        NINGUNO
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
                <tfoot>
                    @if ($loop->last)
                        <tr>
                            <th colspan="4"></th>
                            <th>{{number_format($malla->mallaDetalles->sum('carga_horaria'), 0, ',', '.')}}</th>
                            <th>{{number_format($malla->mallaDetalles->sum('cantidad_creditos'), 0, ',', '.')}}</th>
                            <th></th>
                        </tr>
                    @else
                        <tr>
                            <th colspan="7" style="background-color:white!important"></th>
                        </tr>
                    @endif
                </tfoot>
            </table>
        @endforeach
    </div>
@endsection
