@if (Auth::user()->can('ver_convalidaciones_externas') || Auth::user()->can('ver_convalidaciones_internas'))
    @extends('layouts.master')
    @section('title') Convalidaciones @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Convalidaciones @endslot
        @endcomponent

        @include('convalidaciones.scripts.messages-scripts')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Lista de Convalidaciones</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="convalidaciones-list">
                            <div class="row g-4 mb-3">
                                <div class="col-lg-12">
                                    <div class="d-flex justify-content-sm-end">
                                        <div class="search-box ms-2">
                                            <input type="text" class="form-control search" placeholder="Buscar...">
                                            <i class="ri-search-line search-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive table-card mt-3 mb-1">
                                <table class="table align-middle table-nowrap" id="convalidaciones-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="sort" data-sort="id">ID</th>
                                            <th class="sort" data-sort="numero_solicitud">N° Solicitud</th>
                                            <th class="sort" data-sort="alumno">Alumno</th>
                                            <th class="sort" data-sort="carrera">Carrera Convalidada</th>
                                            <th class="sort" data-sort="universidad_origen">Universidad Origen</th>
                                            <th class="sort" data-sort="carrera_origen">Carrera Origen</th>
                                            <th class="sort" data-sort="tipo">Tipo</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @foreach ($convalidaciones as $convalidacion)
                                            <tr>
                                                <td class="id">{{$convalidacion->id}}</td>
                                                <td class="numero_solicitud">{{$convalidacion->numero_solicitud}}</td>
                                                <td class="alumno">{{$convalidacion->alumno->primer_nombre}} {{$convalidacion->alumno->primer_apellido}} - {{$convalidacion->alumno->numero_documento}}</td>
                                                <td class="carrera">{{$convalidacion->carrera->nombre_fantasia}}</td>
                                                <td class="universidad_origen">{{$convalidacion->universidadOrigen->nombre}}</td>
                                                <td class="carrera_origen">{{$convalidacion->carrera_origen}}</td>
                                                <td class="tipo">
                                                    @if ($convalidacion->tipo == 'EX')
                                                        EXTERNA
                                                    @elseif ($convalidacion->tipo == 'IN')
                                                        INTERNA
                                                    @endif
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge @if ($convalidacion->estado == 'SO')
                                                            bg-warning-subtle text-warning text-uppercase"> Solicitado
                                                        @elseif ($convalidacion->estado == 'DI')
                                                            bg-primary-subtle text-primary text-uppercase"> Dictaminado
                                                        @elseif ($convalidacion->estado == 'AP')
                                                            bg-success-subtle text-success text-uppercase"> Aprobado
                                                        @elseif ($convalidacion->estado == 'CO')
                                                            bg-success-subtle text-success text-uppercase"> Convalidado
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    <a type="button" class="btn btn-sm btn-primary" href="{{route('convalidaciones.show', $convalidacion->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="noresults" style="display: none">
                                    <div class="text-center">
                                        <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" estado="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                        <h5 class="mt-2">Sin resultados.</h5>
                                        <p class="text-muted mb-0">No pudimos encontrar ninguna convalidación según tus parámetros de búsqueda.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <div class="pagination-wrap hstack gap-2">
                                    <a class="page-item pagination-prev disabled"><</a>
                                    <ul class="pagination listjs-pagination mb-0"></ul>
                                    <a class="page-item pagination-next">></a>
                                </div>
                            </div>
                        </div>
                    </div><!-- end card -->
                </div>
                <!-- end col -->
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/prismjs/prism.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        @include('convalidaciones.scripts.index-scripts')
    @endsection
@endif
