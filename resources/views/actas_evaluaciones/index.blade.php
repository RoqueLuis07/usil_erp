@can('ver_actas')
    @extends('layouts.master')
    @section('title') Actas de Evaluaciones @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Actas de Evaluaciones @endslot
        @endcomponent

        @include('actas_evaluaciones.scripts.messages-scripts')
        @include('actas_evaluaciones.modals.index-modals')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Lista de Actas de Evaluaciones</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="actas_evaluaciones-list">
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
                                <table class="table align-middle table-nowrap" id="actas_evaluaciones-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="sort text-center" data-sort="numero_acta">N° de Acta</th>
                                            <th class="sort" data-sort="evaluacion">Evaluación</th>
                                            <th class="sort" data-sort="materia">Materia</th>
                                            <th class="sort" data-sort="docente">Docente</th>
                                            <th class="sort" data-sort="carrera">Carrera</th>
                                            <th class="sort" data-sort="semestre">Semestre</th>
                                            <th class="sort" data-sort="fecha">Fecha</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @foreach ($actas_evaluaciones as $acta_evaluacion)
                                            <tr>
                                                <td class="numero_acta text-center">{{str_pad($acta_evaluacion->numero_acta, 7, '0', STR_PAD_LEFT)}}</td>
                                                <td class="evaluacion">
                                                    @if ($acta_evaluacion->tipo == 'O')
                                                        ORDINARIO
                                                    @elseif ($acta_evaluacion->tipo == 'C')
                                                        COMPLEMENTARIO
                                                    @elseif ($acta_evaluacion->tipo == 'E')
                                                        EXTRAORDINARIO
                                                    @endif
                                                </td>
                                                <td class="materia">{{$acta_evaluacion->materia->nombre_fantasia}}</td>
                                                <td class="docente">{{$acta_evaluacion->docente->primer_nombre}} {{$acta_evaluacion->docente->primer_apellido}}</td>
                                                <td class="carrera">{{$acta_evaluacion->carrera->nombre_fantasia}}</td>
                                                <td class="semestre">{{$acta_evaluacion->semestre->nombre}}</td>
                                                <td class="fecha_evaluacion">{{\Carbon\Carbon::parse($acta_evaluacion->fecha_evaluacion)->format('d/m/Y')}}</td>
                                                <td>
                                                    <a type="button" class="btn btn-sm btn-primary" href="{{route('actas_evaluaciones.show', $acta_evaluacion->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></a>
                                                    @can('reimprimir_actas')
                                                        <a type="button" class="btn btn-sm btn-warning" href="{{asset($acta_evaluacion->ubicacion_acta)}}" target="_blank" data-bs-trigger="hover" data-bs-placement="top" title="Reimprimir"><i class="ri-printer-fill"></i></a>
                                                    @endcan
                                                    @can('eliminar_actas')
                                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal-{{$acta_evaluacion->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"><i class="ri-delete-bin-fill"></i></button>
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
                                        <p class="text-muted mb-0">No pudimos encontrar ningún acta de evaluación según tus parámetros de búsqueda.</p>
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
        @include('actas_evaluaciones.scripts.index-scripts')
    @endsection
@endcan
