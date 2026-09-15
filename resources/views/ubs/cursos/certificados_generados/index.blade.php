@can('ver_certificados_generados_cursos_ubs')
    @extends('layouts.master')
    @section('title') Certificados Generados @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <style>
            .dropdown-menu-mas {
                background-color: #3C80E6!important;
            }
            .dropdown-item-mas {
                color: white!important;
            }
            .dropdown-item-mas:hover {
                background-color: #9EC4FE!important;
                color: black!important;
            }
        </style>
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Certificados Generados @endslot
        @endcomponent

        @include('ubs.cursos.scripts.messages-scripts')
        @include('ubs.cursos.certificados_generados.modals.index-modals')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Lista de Certificados Generados del Curso {{Str::title($curso->nombre_fantasia)}}</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="certificados-list">
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
                                <table class="table align-middle table-nowrap" id="certificados-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th class="sort" data-sort="alumno">Alumno</th>
                                            <th class="sort" data-sort="numero_documento">N° Documento</th>
                                            <th>N° Orden</th>
                                            <th>N° Página</th>
                                            <th class="sort" data-sort="fecha_generación">Fecha Generación</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @foreach ($certificados as $certificado)
                                            <tr>
                                                <td class="id">{{$certificado->id}}</td>
                                                <td class="alumno">{{$certificado->alumno->primer_nombre}} {{$certificado->alumno->primer_apellido}}</td>
                                                <td class="numero_documento">{{$certificado->alumno->numero_documento}}</td>
                                                <td>{{$certificado->numero_orden}}</td>
                                                <td>{{$certificado->numero_pagina}}</td>
                                                <td class="fecha_generacion">{{\Carbon\Carbon::parse($certificado->created_at)->format('d/m/Y H:i:s')}}</td>
                                                <td>
                                                    @can('regenerar_certificados_cursos_ubs')
                                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#regenerateCertificadoModal-{{$certificado->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Generar Certificado"><i class="ri-printer-fill"></i></button>
                                                    @endcan
                                                    @can('editar_certificados_cursos_ubs')
                                                        <button class="btn btn-sm btn-info edit-btn" data-bs-toggle="modal" data-bs-target="#editCertificadoModal-{{$certificado->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar Certificado" data-id="{{$certificado->id}}"><i class="ri-edit-fill"></i></button>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="noresults" style="display: none">
                                    <div class="text-center">
                                        <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" estado="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                        <h5 class="mt-2">Sin resultados.</h5>
                                        <p class="text-muted mb-0">No pudimos encontrar ningún certificado generado según tus parámetros de búsqueda.</p>
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
        <div class="row">
            <div class="col-lg-12 text-end">
                <a type="button" class="btn btn-danger me-2" href="{{route('cursos.show', $curso->id)}}">Volver</a>
            </div>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/prismjs/prism.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        @include('ubs.cursos.certificados_generados.scripts.index-scripts')
    @endsection
@endcan
