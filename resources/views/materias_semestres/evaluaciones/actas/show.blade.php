@can('ver_actas_evaluaciones_materias_semestres')
    @extends('layouts.master')
    @section('title') Ver Acta de Examen @endsection
    @section('content')
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
        @component('components.breadcrumb')
            @slot('li_1') Evaluaciones de {{$materia->nombre_fantasia}} - {{$semestre->nombre}} @endslot
            @slot('title') Ver Acta de Examen @endslot
        @endcomponent

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar acta de examen</h4>
                            </div>
                            <div class="col-lg-6 text-end">
                                <div class="d-flex justify-content-end">
                                    @can('generar_actas_evaluaciones_materias_semestres')
                                        @if ($cantidad_actas < 3 && $alumnos->count() > 0)
                                            @php
                                                if ($cantidad_actas == 0) {
                                                    $tipo = 4;
                                                } else if ($cantidad_actas == 1) {
                                                    $tipo = 5;
                                                } else if ($cantidad_actas == 2) {
                                                    $tipo = 6;
                                                }
                                            @endphp
                                            <button type="button" class="btn btn-success me-2" id="generar-acta-btn" data-url="{{route('actas_evaluaciones.generar_actas', ['materia' => $materia->id, 'semestre' => $semestre->id, 'carrera' => $carrera->id, 'tipo' => $tipo])}}">Generar Acta de {{$tipo_examen}}</button>
                                        @endif
                                    @endcan
                                </div>
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="materia">Materia</label>
                                    <input type="text" class="form-control" id="materia" value="{{$materia->nombre_fantasia}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="carrera">Carrera</label>
                                    <input type="text" class="form-control" id="carrera" value="{{$carrera->nombre_fantasia}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="materia">Docente</label>
                                    <input type="text" class="form-control" id="materia" @if($semestre_malla_materia->docente_id) value="{{$semestre_malla_materia->docente->primer_nombre}} {{$semestre_malla_materia->docente->primer_apellido}}" @endif readonly>
                                </div>
                                <div class="col-lg-3 mb-3 d-flex flex-wrap justify-content-end">
                                    <div class="col-lg-6 text-center">
                                        <label class="form-label" for="semestre">Semestre</label>
                                        <input type="text" class="form-control text-center" id="semestre" value="{{$semestre->nombre}}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header d-flex flex-wrap justify-content-between">
                                    <div class="col-lg-6">
                                        <h4 class="card-title mb-0">Alumnos Habilitados</h4>
                                    </div>
                                    <div class="col-lg-6 d-flex justify-content-end">
                                        Cantidad de Alumnos Habilitados: &nbsp;&nbsp; <b>{{$alumnos->count()}}</b>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row d-flex flex-wrap justify-content-center">
                                        <div class="col-lg-6">
                                            <table class="table align-middle table-nowrap text-center">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>N° de Documento</th>
                                                        <th>Alumno</th>
                                                        <th>Puntos Acumulados</th>
														<th>% Asistencia</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($alumnos as $alumno)
                                                        <tr>
                                                            <td>{{$alumno->numero_documento}}</td>
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
                                                            <td>{{$alumno->puntos_obtenidos}}</td>
															<td>{{number_format($alumno->asistencia_obtenida, 2, ',', '.')}} %</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="3">No hay alumnos habilitados para generar un acta.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('materias_evaluaciones.show', ['materia' => $materia->id, 'semestre' => $semestre->id, 'carrera' => $carrera->id])}}">Volver</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        @include('materias_semestres.evaluaciones.actas.scripts.show-scripts')
    @endsection
@endcan
