@can('ver_clases_tutorias')
    @extends('layouts.master')
    @section('title') Clases de Tutoría @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Clases de Tutoría @endslot
        @endcomponent

        @include('tutorias.clases.scripts.messages-scripts')
        @include('tutorias.clases.modals.index-modals')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Lista de Clases de Tutoría</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="clases-list">
                            <div class="row g-4 mb-3">
                                <div class="col-lg-8">
                                    @can('crear_clases_tutorias')
                                        @if ($tutoria->estado != 'FI' && $tutoria->clases->count() < $tutoria->cantidad_clases)
                                            <a type="button" class="btn btn-success" href="{{route('tutorias_clases.create', $tutoria->id)}}"><i class="ri-add-line align-bottom me-1"></i>Agregar</a>
                                        @endif
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
                                <table class="table align-middle table-nowrap" id="clases-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="sort" data-sort="fecha">Fecha y Hora</th>
                                            <th class="sort" data-sort="materia">Materia</th>
                                            <th class="sort" data-sort="docente">Docente</th>
                                            <th class="sort" data-sort="modalidad">Modalidad</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @foreach ($clases as $clase)
                                            <tr>
                                                <td class="fecha">{{\Carbon\Carbon::parse($clase->fecha_hora)->format('d/m/Y H:i:s')}}</td>
                                                <td class="materia">{{$clase->tutoria->materia->nombre_fantasia}}</td>
                                                <td class="docente">{{$clase->tutoria->docente->primer_nombre}} {{$clase->tutoria->docente->primer_apellido}}</td>
                                                <td class="modalidad">{{$clase->modalidad->nombre}}</td>
                                                <td>
                                                    <a type="button" class="btn btn-sm btn-primary" href="{{route('tutorias_clases.show', $clase->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></a>
                                                    @can('eliminar tutorias clases')
                                                        @if ($tutoria->estado != 'FI')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal-{{$clase->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"><i class="ri-delete-bin-fill"></i></button>
                                                        @endif
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
                                        <p class="text-muted mb-0">No pudimos encontrar ningúna clase según tus parámetros de búsqueda.</p>
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
        @include('tutorias.clases.scripts.index-scripts')
    @endsection
@endcan
