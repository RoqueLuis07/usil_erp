@can('ver_clases_tutorias_docentes_pantalla')
    @extends('layouts.master')
    @section('title') Clases de Tutoría de {{$tutoria->materia->nombre_fantasia}} @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('title') BIENVENIDO, {{$docente->primer_nombre}} {{$docente->primer_apellido}} @endslot
        @endcomponent

        @include('pantallas_docentes.tutorias.clases.scripts.messages-scripts')

        <div class="row d-flex flex-wrap justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Clases de Tutoría de {{$tutoria->materia->nombre_fantasia}}</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="clases-list">
                            <div class="row g-4 mb-3">
                                <div class="col-lg-8">
                                    @can('crear_clases_tutorias_docentes_pantalla')
                                        @if ($tutoria->estado != 'FI' && $tutoria->clases->count() < $tutoria->cantidad_clases)
                                            <a type="button" class="btn btn-success" href="{{route('tutorias_docentes.create_clases', $tutoria->id)}}"><i class="ri-add-line align-bottom me-1"></i>Agregar</a>
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
                                            <th class="sort" data-sort="modalidad">Modalidad</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @foreach ($clases as $clase)
                                            <tr>
                                                <td class="fecha">{{\Carbon\Carbon::parse($clase->fecha_hora)->format('d/m/Y H:i:s')}}</td>
                                                <td class="modalidad">{{$clase->modalidad->nombre}}</td>
                                                <td>
                                                    <a type="button" class="btn btn-sm btn-primary" href="{{route('tutorias_docentes.show_clases', $clase->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></a>
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
            <div class="col-lg-8 text-center mb-3">
                <a type="button" class="btn btn-danger me-2" href="{{route('tutorias_docentes.index', Auth::id())}}">Volver</a>
            </div>
        </div>
        <!-- end row -->
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/prismjs/prism.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        @include('pantallas_docentes.tutorias.clases.scripts.index-scripts')
    @endsection
@endcan
