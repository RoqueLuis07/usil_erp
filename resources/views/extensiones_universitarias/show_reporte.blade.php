{{-- @can('generar_reportes_extensiones_universitarias') --}}
    @extends('layouts.master')
    @section('title') Reporte de Extensión Universitaria @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Extensiones Universitarias @endslot
            @slot('title') Reporte de Extensión Universitaria  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('extensiones_universitarias.generate_reporte')}}" method="get" target="_blank" id="generate-reporte-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Filtros Aplicados</h4>
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                @if ($proyecto != '')
                                    <div class="col-lg-9 mb-3">
                                        <label class="form-label" for="proyecto">Proyecto</label>
                                        <input type="text" class="form-control" id="proyecto" value="{{ $proyecto->nombre }}" readonly>
                                        <input type="hidden" name="proyecto" value="{{$proyecto->id}}">
                                    </div>
                                @endif
                                @if ($periodo != '')
                                    <div class="col-lg-3 mb-3">
                                        <label class="form-label" for="periodo">Período</label>
                                        <input type="text" class="form-control" id="periodo" name="periodo" value="{{ $periodo }}" readonly>
                                    </div>
                                @endif
                            </div>
                            <div class="row">
                                @if ($tipo_extension != '')
                                    <div class="col-lg-6 mb-3">
                                        <label class="form-label" for="tipo_extension">Tipo de Actividad</label>
                                        <input type="text" class="form-control" id="tipo_extension" value="{{ $tipo_extension->nombre }}" readonly>
                                        <input type="hidden" name="tipo_extension" value="{{$tipo_extension->id}}">
                                    </div>
                                @endif
                                @if ($alumno != '')
                                    <div class="col-lg-6 mb-3">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <label class="form-label" for="alumno">Alumno</label>
                                                <input type="text" class="form-control" id="alumno" value="{{ $alumno->primer_nombre }} {{ $alumno->primer_apellido }}" readonly>
                                                <input type="hidden" name="alumno" value="{{$alumno->id}}">
                                            </div>
                                            <div class="col-lg-6">
                                                <label class="form-label" for="documento_alumno">N° Documento</label>
                                                <input type="text" class="form-control" id="documento_alumno" value="{{ $alumno->numero_documento }}" readonly>
                                            </div>
                                        </div>
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
                                                    <th>Proyecto</th>
                                                    <th>Tipo de Actividad</th>
                                                    <th>Responsable</th>
                                                    <th>Horas Totales</th>
                                                    @if ($alumno != '')
                                                        <th>Horas Cumplidas</th>
                                                    @endif
                                                    <th>Periodo</th>
                                                    @if ($alumno == '')
                                                        <th>Cant. Alumnos</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody class="list form-check-all">
                                                @foreach ($extensiones as $extension)
                                                    <tr>
                                                        <td>{{$extension->nombre}}</td>
                                                        <td>{{$extension->tipoExtension->nombre}}</td>
                                                        <td>{{$extension->docente->primer_nombre}} {{$extension->docente->primer_apellido}} - {{$extension->docente->numero_documento}}</td>
                                                        <td>{{number_format($extension->cantidad_horas, 2, ',', '.')}}
                                                            @if ($extension->cantidad_horas == 1)
                                                                hora
                                                            @else
                                                                horas
                                                            @endif
                                                        @if ($alumno != '')
                                                            </td>
                                                            <td>{{number_format($extension->extensionUniversitariaDetalles->where('alumno_id', $alumno->id)->sum('cantidad_horas'), 2, ',', '.')}}
                                                                @if ($extension->extensionUniversitariaDetalles->where('alumno_id', $alumno->id)->sum('cantidad_horas') == 1)
                                                                    hora
                                                                @else
                                                                    horas
                                                                @endif
                                                            </td>
                                                        @endif
                                                        <td>{{$extension->periodo}}</td>
                                                        @if ($alumno == '')
                                                            <td>
                                                                {{$extension->extensionUniversitariaDetalles->count()}}
                                                                @if ($extension->extensionUniversitariaDetalles->count() == 1)
                                                                    alumno
                                                                @else
                                                                    alumnos
                                                                @endif
                                                            </td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
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
                            <button type="button" class="btn btn-success" id="generate-reporte-btn">Generar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        @include('extensiones_universitarias.scripts.show_reporte-scripts')
    @endsection
{{-- @endcan --}}
