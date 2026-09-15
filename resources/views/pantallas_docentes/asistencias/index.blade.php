@can('crear_asistencias_docentes_pantalla')
    @extends('layouts.master')
    @section('title') Asistencias Alumnos @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('title') BIENVENIDO, {{$docente->primer_nombre}} {{$docente->primer_apellido}} @endslot
        @endcomponent

        @include('pantallas_docentes.scripts.messages-scripts')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Asistencias Alumnos</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="asistencias-list">
                            <div class="row g-4 mb-3">
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
                            </div>
                            <div class="table-responsive table-card mt-3 mb-1">
                                <table class="table align-middle table-nowrap text-center" id="asistencias-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="sort" data-sort="materia">Materia</th>
                                            <th class="sort" data-sort="periodo">Período</th>
											<th class="sort" data-sort="cantidad_alumnos">Cant. Alumnos</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @forelse ($materias as $materia)
                                            <tr>
                                                <td class="materia">{{$materia->nombre_fantasia}}</td>
                                                <td class="periodo">{{$semestre->nombre}}</td>
                                                <td class="cantidad_alumnos">{{$materia->alumnos->count()}}</td>
                                                <td>
                                                    <a type="button" class="btn btn-sm btn-primary" href="{{route('pantallas_docentes.show_asistencias_alumnos', $materia->id)}}">Ver</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr class="text-center">
                                                <td>No tienes materias en el semestre activo.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <div class="noresults" style="display: none">
                                    <div class="text-center">
                                        <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" estado="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                        <h5 class="mt-2">Sin resultados.</h5>
                                        <p class="text-muted mb-0">No pudimos encontrar ninguna materia según tus parámetros de búsqueda.</p>
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
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        @include('pantallas_docentes.asistencias.scripts.index-scripts')
    @endsection
@endcan
