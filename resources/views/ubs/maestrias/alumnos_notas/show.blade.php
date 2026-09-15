@can('ver_notas_maestrias_ubs')
    @extends('layouts.master')
    @section('title') Ver Notas @endsection
    @section('css')
            <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
            <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Maestrías @endslot
            @slot('title') Ver Notas @endslot
        @endcomponent

        @include('ubs.maestrias.scripts.messages-scripts')
        @include('ubs.maestrias.alumnos_notas.modals.show-modals')

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar notas</h4>
                            </div>
                            @can('crear_notas_maestrias_ubs')
                                <div class="col-lg-6 text-end" id="div-cargar" style="margin-bottom: -5em">
                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#moduloNotasModal" id="get-modulos-btn">Nueva Evaluación</button>
                                    </div>
                                </div>
                            @endcan
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-9">
                                        <div class="row">
                                            <div class="col-lg-4 mb-3">
                                                <label class="form-label" for="curso">Curso</label>
                                                <input type="text" class="form-control" id="curso" value="{{$maestria->nombre_fantasia}}" readonly>
                                                <input type="hidden" id="curso_id" value="{{$maestria->id}}">
                                            </div>
                                            <div class="col-lg-2 mb-3">
                                                <label class="form-label" for="modalidad">Modalidad</label>
                                                <input type="text" class="form-control" id="modalidad" value="{{$maestria->modalidad->nombre}}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Notas</h4>
                                </div>
                                <div class="card-body">
                                    <div class="card-body">
                                        <div id="notas-list">
                                            <div class="row mb-3">
                                                <div class="col-lg-4">
                                                    <div class="d-flex justify-content-sm-start">
                                                        <div class="search-box ms-2">
                                                            <input type="text" class="form-control search" placeholder="Buscar...">
                                                            <i class="ri-search-line search-icon"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-8">
                                                    <div class="d-flex justify-content-end">
                                                        <div class="pagination-wrap hstack gap-2">
                                                            <a class="page-item pagination-prev disabled"><</a>
                                                            <ul class="pagination listjs-pagination mb-0"></ul>
                                                            <a class="page-item pagination-next">></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="p-3">
                                                <div class="table-responsive table-card">
                                                    <table class="table align-middle table-nowrap text-center" id="notas-list">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th class="sort" data-sort="alumno">Alumno</th>
                                                                <th class="sort" data-sort="numero_documento">N° Documento</th>
                                                                @foreach ($maestria->modulos as $key => $detalle)
                                                                    <th data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="{{$detalle->modulo->nombre_fantasia}}">Módulo {{$key + 1}}</th>
                                                                @endforeach
                                                            </tr>
                                                        </thead>
                                                        <tbody class="list form-check-all">
                                                            @foreach ($notas as $alumno_id => $notas_alumno)
                                                                @php
                                                                    $alumno = $notas_alumno->first()->alumno;
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ $alumno->primer_nombre }} {{ $alumno->primer_apellido }}</td>
                                                                    <td>{{ number_format($alumno->numero_documento, 0, ',', '.') }}</td>

                                                                    @foreach ($maestria->modulos as $detalle)
                                                                        <td>
                                                                            @php
                                                                                $nota_modulo = $notas_alumno->where('modulo_id', $detalle->modulo_id)->first();
                                                                            @endphp

                                                                            @if ($nota_modulo)
                                                                                {{$nota_modulo->calificacion}}
                                                                                <button type="button" class="btn btn-sm btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#notaDetalleModal-{{$nota_modulo->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver en Detalle">
                                                                                    <i class="ri-eye-fill align-bottom"></i>
                                                                                </button>
                                                                            @else
                                                                                N/A
                                                                            @endif
                                                                        </td>
                                                                    @endforeach
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                    <div class="noresults" style="display: none">
                                                        <div class="text-center">
                                                            <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" estado="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:50px;height:50px"></lord-icon>
                                                            <h5 class="mt-2">Sin resultados.</h5>
                                                            <p class="text-muted mb-0">No pudimos encontrar ningún alumno según tus parámetros de búsqueda.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('maestrias.index')}}">Volver</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        @include('ubs.maestrias.alumnos_notas.scripts.show-scripts')
    @endsection
@endcan
