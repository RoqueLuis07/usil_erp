@can('ver_actas_examenes_suficiencia')
    @extends('layouts.master')
    @section('title') Actas Exámenes de Suficiencia @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Exámenes de Suficiencia @endslot
            @slot('title') Actas Exámenes de Suficiencia @endslot
        @endcomponent

        @include('examenes_suficiencia.actas.modals.index-modals')
        @include('examenes_suficiencia.scripts.messages-scripts')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Lista de Actas de Exámenes de Suficiencia</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="actas-list">
                            <div class="row g-4 mb-3">
                                <div class="col-lg-8">
                                    @can('generar_actas_examenes_suficiencia')
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#generateActaModal"><i class="ri-add-line align-bottom me-1"></i>Acta</button>
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
                                <table class="table align-middle table-nowrap" id="actas-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th class="sort" data-sort="numero_acta">N° Acta</th>
                                            <th class="sort" data-sort="materia">Materia</th>
                                            <th class="sort" data-sort="carrera">Carrera</th>
                                            <th class="sort" data-sort="docente">Docente</th>
                                            <th class="sort" data-sort="fecha">Fecha</th>
                                            <th class="sort" data-sort="semestre">Semestre</th>
                                            <th>Cant. Alumnos</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @foreach ($actas as $acta)
                                            <tr>
                                                <td class="id">{{$acta->id}}</td>
                                                <td class="numero_acta">{{$acta->numero_acta}}</td>
                                                <td class="materia">{{$acta->materia->nombre_fantasia}}</td>
                                                <td class="carrera">{{$acta->carrera->nombre_fantasia}}</td>
                                                <td class="docente">{{$acta->docente->primer_nombre}} {{$acta->docente->primer_apellido}}</td>
                                                <td class="fecha">{{Carbon\Carbon::parse($acta->fecha_evaluacion)->format('d/m/y')}}</td>
                                                <td class="semestre">{{$acta->semestre->nombre}}</td>
                                                <td>{{$acta->alumnos->count()}}</td>
                                                <td>
                                                    <a type="button" class="btn btn-sm btn-primary" href="{{route('examenes_suficiencia.show_acta', $acta->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></a>
                                                    @can('ver_actas_examenes_suficiencia')
                                                        <a type="button" class="btn btn-sm btn-warning" href="{{asset($acta->ubicacion_acta)}}" target="_blank" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Reimprimir"><i class="ri-printer-fill"></i></a>
                                                    @endcan
                                                    @can('eliminar_actas_examenes_suficiencia')
                                                        <a type="button" class="btn btn-sm btn-danger" href="{{route('examenes_suficiencia.destroy_acta', $acta->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"><i class="ri-delete-bin-fill"></i></a>
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
                                        <p class="text-muted mb-0">No pudimos encontrar ningún acta según tus parámetros de búsqueda.</p>
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
                <!-- end row -->
                <div class="row">
                    <div class="col-lg-12 text-end mb-3">
                        <a type="button" class="btn btn-danger me-2" href="{{route('examenes_suficiencia.index')}}">Volver</a>
                    </div>
                </div>
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
        @include('examenes_suficiencia.actas.scripts.index-scripts')
    @endsection
@endcan
