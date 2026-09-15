@can('generar_reportes_extensiones_universitarias_carrera_semestre')
    @extends('layouts.master')
    @section('title') Reporte de Extensión Universitaria por Carrera y Semestre @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Extensiones Universitarias @endslot
            @slot('title') Reporte por Carrera y Semestre @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('extensiones_universitarias.generate_reporte_carrera_semestre')}}" method="get" target="_blank" id="generate-reporte-carrera-semestre-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Filtros Aplicados</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if ($carrera != '')
                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label" for="carrera">Carrera</label>
                                        <input type="text" class="form-control" id="carrera" value="{{ $carrera->nombre_fantasia }}" readonly>
                                        <input type="hidden" name="carrera" value="{{$carrera->id}}">
                                    </div>
                                @endif
                                @if ($semestre != '')
                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label" for="semestre">Semestre</label>
                                        <input type="text" class="form-control" id="semestre" value="{{ $semestre->nombre }}" readonly>
                                        <input type="hidden" name="semestre" value="{{$semestre->id}}">
                                    </div>
                                @endif
                                @if ($tipo_extension != '')
                                    <div class="col-lg-4 mb-3">
                                        <label class="form-label" for="tipo_extension">Tipo de Actividad</label>
                                        <input type="text" class="form-control" id="tipo_extension" value="{{ $tipo_extension->nombre }}" readonly>
                                        <input type="hidden" name="tipo_extension" value="{{$tipo_extension->id}}">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive table-card mt-3 mb-1">
                                        <table class="table align-middle text-center">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Carrera</th>
                                                    <th>Semestre</th>
                                                    <th>Cant. Actividades</th>
                                                    <th>Cant. Alumnos</th>
                                                    <th>Horas Totales</th>
                                                </tr>
                                            </thead>
                                            <tbody class="list form-check-all">
                                                @foreach ($reporte as $fila)
                                                    <tr>
                                                        <td>{{ $fila->carrera->nombre_fantasia ?? 'Sin carrera' }}</td>
                                                        <td>{{ $fila->semestre->nombre ?? 'Sin semestre' }}</td>
                                                        <td>{{ $fila->cantidad_actividades }}</td>
                                                        <td>{{ $fila->cantidad_alumnos }}</td>
                                                        <td>{{ number_format($fila->horas_totales, 2, ',', '.') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="text-muted small px-2 pb-2">
                                        La carrera y el semestre se toman de la matriculación del alumno vigente a la fecha de inicio de la actividad. Solo se incluyen actividades finalizadas.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="cargado_por">Generado por:</label>
                            <br>
                            {{$usuario}} el día {{$fecha}} a las {{$hora}}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('extensiones_universitarias.index')}}">Volver</a>
                            <button type="submit" class="btn btn-success">Generar PDF</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
    @endsection
@endcan
