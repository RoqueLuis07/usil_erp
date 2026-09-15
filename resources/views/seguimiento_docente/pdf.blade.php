@can('ver_materias_clases')
    @extends('layouts.pdf.reportes')
    @section('css')
        <style>
            .titulo {
                width: 100%;
                text-align: center;
                margin-top: 20px;
            }
            .filtros {
                width: 100%;
            }
            .tabla {
                width: 100%;
                font-size: 11px;
            }
            .tabla th {
                font-weight: bold;
                background-color: #002663;
                color: white;
            }
            .tabla td {
                padding-left: 5px;
            }
            .text-center {
                text-align: center;
            }
            .border {
                border: 1px solid black;
            }
            .b-top {
                border-top: 1px solid black;
            }
            .b-right {
                border-right: 1px solid black;
            }
            .b-left {
                border-left: 1px solid black;
            }
            .b-bottom {
                border-bottom: 1px solid black;
            }
        </style>
    @endsection

    @section('pdf_content')
        <div class="col-12 titulo mb-3">
            <p><b>{{$titulo}}</b></p>
        </div>
        <div class="col-12 mb-3">
            <table class="filtros">
                <tbody>
                    <tr>
                        <td width="33%"><b>Mes:</b> {{Str::upper(\Carbon\Carbon::now($filtro_mes)->translatedFormat('F'))}}</td>
                        <td width="33%"><b>Período:</b> {{$filtro_periodo}}</td>
                        @if ($filtro_programa != 'ninguno')
                            <td width="33%"><b>Programa:</b> {{$filtro_programa}}</td>
                        @endif
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div>
            @foreach ($dias_semana as $dia)
                @foreach ($fechas as $key => $fecha)
                    @if ($key == $dia->id)
                        @php
                            $colspan = count($fecha);
                        @endphp
                    @endif
                @endforeach
                <table class="tabla mb-2">
                    <thead>
                        <tr>
                            <th class="b-top b-left b-right b-bottom text-center" colspan="5" width="65%">{{$dia->nombre}}</th>
                            <th class="b-top b-left b-right b-bottom text-center" colspan="{{$colspan}}" width="35%">{{$mes}}</th>
                        </tr>
                        <tr>
                            <th class="b-left b-right b-bottom text-center" width="5%">Carrera</th>
                            <th class="b-right b-bottom text-center" width="20%">Docente</th>
                            <th class="b-right b-bottom text-center" width="20%">Curso</th>
                            <th class="b-right b-bottom text-center" width="15%">Sesión</th>
                            <th class="b-right b-bottom text-center" width="5%">Sala</th>
                            @foreach ($fechas as $key => $fecha)
                                @if ($key == $dia->id)
                                    @foreach ($fecha as $index => $value)
                                        <th class="text-center b-right" width="5%"><b>{{$value}}</b></th>
                                    @endforeach
                                @endif
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($semestre_materias as $semestre_materia)
                            @if ($dia->id == $semestre_materia->dia_semana_id)
                                <tr>
                                    <td class="text-center b-left b-bottom b-right" style="padding: 0!important;">{{$semestre_materia->carrera}}</td>
                                    <td class="b-bottom b-right">{{$semestre_materia->docente}}</td>
                                    <td class="b-bottom b-right">{{Str::title($semestre_materia->materia)}}</td>
                                    <td class="b-bottom b-right">{{Str::title($dia->nombre)}} de {{\Carbon\Carbon::parse($semestre_materia->hora_inicio)->format('H:i')}} a {{\Carbon\Carbon::parse($semestre_materia->hora_fin)->format('H:i')}}hs.</td>
                                    <td class="b-bottom b-right text-center" style="padding: 0!important;">{{trim($semestre_materia->aula)}}</td>
                                    @foreach ($fechas as $key => $fecha)
                                        @if ($key == $dia->id)
                                            @foreach ($fecha as $value)
                                                @php
                                                    $clase_existente = $clases_materias->first(function ($clase) use ($value, $semestre_materia) {
                                                        return $clase->materia_id == $semestre_materia->materia_id
                                                            && \Carbon\Carbon::parse($clase->fecha_hora)->day == $value;
                                                    });
                                                @endphp

                                                @if ($clase_existente)
                                                    @if ($clase_existente->alumnoAsistencias->count() > 0)
                                                        <td  class="b-bottom b-right" style="background-color: green;"></td>
                                                    @elseif ($clase_existente->alumnoAsistencias->count() == 0)
                                                        <td  class="b-bottom b-right" style="background-color: yellow;"></td>
                                                    @endif
                                                @else
                                                    <td  class="b-bottom b-right" style="background-color: red;"></td>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            @endforeach

            {{-- @foreach ($dias_semana as $dia)
                @foreach ($fechas as $key => $fecha)
                    @if ($key == $dia->id)
                        @php
                            $colspan = count($fecha);
                        @endphp
                    @endif
                @endforeach
                <table class="table align-middle table-bordered text-center">
                    <thead class="table-light">
                        <tr>
                            <th colspan="5" width="65%">{{$dia->nombre}}</th>
                            <th colspan="{{$colspan}}" width="35%">{{$mes}}</th>
                        </tr>
                        <tr>
                            <th width="5%">Carrera</th>
                            <th width="20%">Docente</th>
                            <th width="20%">Curso</th>
                            <th width="15%">Sesión</th>
                            <th width="5%">Sala</th>
                            @foreach ($fechas as $key => $fecha)
                                @if ($key == $dia->id)
                                    @foreach ($fecha as $index => $value)
                                        <th width="5%">{{$value}}</th>
                                    @endforeach
                                @endif
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="form-check-all">
                        @foreach ($semestre_materias as $semestre_materia)
                            @if ($dia->id == $semestre_materia->dia_semana_id)
                                <tr>
                                    <td>{{$semestre_materia->carrera}}</td>
                                    <td>{{$semestre_materia->docente}}</td>
                                    <td>{{Str::title($semestre_materia->materia)}}</td>
                                    <td>{{Str::title($dia->nombre)}} de {{\Carbon\Carbon::parse($semestre_materia->hora_inicio)->format('H:i')}} a {{\Carbon\Carbon::parse($semestre_materia->hora_fin)->format('H:i')}} Hs.</td>
                                    <td>{{trim($semestre_materia->aula)}}</td>
                                    @foreach ($fechas as $key => $fecha)
                                        @if ($key == $dia->id)
                                            @foreach ($fecha as $value)
                                                @php
                                                    $clase_existente = $clases_materias->first(function ($clase) use ($value, $semestre_materia) {
                                                        return $clase->materia_id == $semestre_materia->materia_id
                                                            && \Carbon\Carbon::parse($clase->fecha_hora)->day == $value;
                                                    });
                                                @endphp

                                                @if ($clase_existente)
                                                    @if ($clase_existente->alumnoAsistencias->count() > 0)
                                                        <td class="bg-success">
                                                            <div class="btn-group" role="group">
                                                                <button id="botones" type="button" class="btn btn-sm btn-success dropdown-toggle" data-bs-toggle="dropdown"></button>
                                                                <div class="dropdown-menu" aria-labelledby="botones">
                                                                    <a class="dropdown-item" href="{{ route('clases_materias.show', $clase_existente->id) }}">Visualizar</a>
                                                                    @can('eliminar_materias_clases')
                                                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#destroyModal-{{$clase_existente->id}}">Eliminar</a>
                                                                    @endcan
                                                                </div>
                                                            </div>
                                                        </td>
                                                    @elseif ($clase_existente->alumnoAsistencias->count() == 0)
                                                        <td class="bg-warning">
                                                            <div class="btn-group" role="group">
                                                                <button id="botones" type="button" class="btn btn-sm btn-warning dropdown-toggle" data-bs-toggle="dropdown"></button>
                                                                <div class="dropdown-menu" aria-labelledby="botones">
                                                                    <a class="dropdown-item" href="{{ route('clases_materias.show', $clase_existente->id) }}">Visualizar</a>
                                                                    @can('crear_alumnos_asistencias_materias_semestres')
                                                                        <a class="dropdown-item" href="{{ route('alumnos_asistencias.create', ['materia' => $clase_existente->materia_id, 'semestre' => $clase_existente->semestre_id, 'docente' => $clase_existente->docente_id]) }}">+ Asistencias</a>
                                                                    @endcan
                                                                    @can('eliminar_materias_clases')
                                                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#destroyModal-{{$clase_existente->id}}">Eliminar</a>
                                                                    @endcan
                                                                </div>
                                                            </div>
                                                        </td>
                                                    @endif
                                                @else
                                                    <td class="bg-danger" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Sin clase"></td>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            @endforeach --}}
        </div>
    @endsection
@endcan
