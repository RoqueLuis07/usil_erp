@can('ver_tesis_ubs')
    @extends('layouts.master')
    @section('title') Tesis @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Tesis @endslot
        @endcomponent

        @include('ubs.tesis.scripts.messages-scripts')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Tesis</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="tesis-list">
                            <div class="row g-4 mb-3">
                                <div class="col-lg-8">
                                    @can('crear_tesis_ubs')
                                        <a type="button" class="btn btn-success" href="{{route('tesis_ubs.create')}}"><i class="ri-add-line align-bottom me-1"></i>Agregar</a>
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
                                <table class="table align-middle table-nowrap" id="tesis-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th class="sort" data-sort="fecha">Fecha</th>
                                            <th class="sort" data-sort="alumno">Alumno</th>
                                            <th class="sort" data-sort="alumno_documento">N° Documento</th>
                                            <th class="sort" data-sort="maestria">Maestría</th>
                                            <th>Línea</th>
                                            <th>Tema</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @foreach ($tesis as $t)
                                            <tr>
                                                <td>{{$t->id}}</td>
                                                <td class="fecha">{{\Carbon\Carbon::parse($t->fecha)->format('d/m/Y H:i:s')}}</td>
                                                <td class="alumno">{{$t->alumno->primer_nombre}} {{$t->alumno->primer_apellido}}</td>
                                                <td class="alumno_documento">{{$t->alumno->numero_documento}}</td>
                                                <td class="maestria">{{$t->curso->nombre_fantasia}} - {{$t->curso->llamado}}° LLAMADO</td>
                                                <td>{{$t->linea->nombre}}</td>
                                                <td>{{$t->tema}}</td>
                                                <td>
                                                    <span
                                                        class="badge @if ($t->estado == 'PE')
                                                            bg-danger-subtle text-danger text-uppercase"> Pendiente
                                                        @elseif ($t->estado == 'RE')
                                                            bg-danger-subtle text-danger text-uppercase"> Rechazado
                                                        @elseif ($t->estado == 'AT')
                                                            bg-success-subtle text-success text-uppercase"> Ap. Tutor
                                                        @elseif ($t->estado == 'AC')
                                                            bg-success-subtle text-success text-uppercase"> Ap. Calidad
                                                        @elseif ($t->estado == 'EC')
                                                            bg-warning-subtle text-warning text-uppercase"> Ant. En Curso
                                                        @elseif ($t->estado == 'BC')
                                                            bg-warning-subtle text-warning text-uppercase"> Borr. En Curso
                                                        @elseif ($t->estado == 'AA')
                                                            bg-success-subtle text-success text-uppercase"> Ap. Ant.
                                                        @elseif ($t->estado == 'AB')
                                                            bg-success-subtle text-success text-uppercase"> Ap. Borrador
                                                        @elseif ($t->estado == 'PA')
                                                            bg-success-subtle text-success text-uppercase"> Pagado
                                                        @elseif ($t->estado == 'FE')
                                                            bg-warning-subtle text-warning text-uppercase"> Defensa
                                                        @elseif ($t->estado == 'EN')
                                                            bg-success-subtle text-success text-uppercase"> Aprobado
                                                        @elseif ($t->estado == 'RR')
                                                            bg-danger-subtle text-danger text-uppercase"> Reprobado
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    <a type="button" class="btn btn-sm btn-primary" href="{{route('tesis_ubs.show', $t->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></a>
                                                    @can('editar_tesis_ubs')
                                                        @if ($t->estado == 'PE')
                                                            <a type="button" class="btn btn-sm btn-info" href="{{route('tesis_ubs.edit', $t->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-fill"></i></a>
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
                                        <p class="text-muted mb-0">No pudimos encontrar ninguna tesis según tus parámetros de búsqueda.</p>
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
        @include('ubs.tesis.scripts.index-scripts')
    @endsection
@endcan
