@can('ver_precios_tutorias')
    @extends('layouts.master')
    @section('title') Precios de Tutorías @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Precios de Tutorías @endslot
        @endcomponent

        @include('tutorias.precios.scripts.messages-scripts')
        @include('tutorias.precios.modals.index-modals')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Lista de Precios de Tutorías</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="bloques-list">
                            <div class="row g-4 mb-3">
                                <div class="col-lg-8">
                                    @can('editar_precios_tutorias')
                                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModal" id="add-btn"><i class="ri-add-line align-bottom me-1"></i>Agregar</button>
                                    @endcan
                                </div>
                                <div class="col-lg-4">
                                    <div class="d-flex justify-content-sm-end">
                                        <div class="search-box ms-2">
                                            <input type="text" class="form-control search" placeholder="Buscar...">
                                            <i class="ri-search-line search-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive table-card mt-3 mb-1">
                                <table class="table align-middle table-nowrap" id="bloques-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th class="sort" data-sort="modalidad">Modalidad</th>
                                            <th class="sort" data-sort="carrera">Carrera</th>
                                            <th class="sort" data-sort="precio">Precio</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">

                                    </tbody>
                                </table>
                                <div class="noresults" style="display: none">
                                    <div class="text-center">
                                        <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" state="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                        <h5 class="mt-2">Sin resultados.</h5>
                                        <p class="text-muted mb-0">No pudimos encontrar ningún precio según tus parámetros de búsqueda.</p>
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
            <div class="col-lg-12 text-end mb-3">
                <a type="button" class="btn btn-danger me-2" href="{{route('tesis.index')}}">Volver</a>
            </div>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/prismjs/prism.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js')}}"></script>
        @include('tutorias.precios.scripts.index-scripts')
    @endsection
@endcan
