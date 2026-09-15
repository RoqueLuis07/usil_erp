@can('ver_anteproyectos_tesis')
    @extends('layouts.master')
    @section('title') Anteproyectos de Trabajos Finales de Grado @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Trabajos Finales de Grado @endslot
            @slot('title') Anteproyectos de Trabajos Finales de Grado @endslot
        @endcomponent

        @include('tesis.anteproyectos.scripts.messages-scripts')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Lista de Anteproyecto de Trabajos Finales de Grado</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="temas-list">
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
                                <table class="table align-middle table-nowrap" id="temas-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th class="sort" data-sort="fecha">Fecha Inscripción</th>
                                            <th class="sort" data-sort="alumno">Alumno</th>
                                            <th class="sort" data-sort="alumno_documento">N° Documento</th>
                                            <th class="sort" data-sort="tutor">Tutor</th>
                                            <th>Tipo / Área / Línea</th>
                                            <th>Tema</th>
                                            <th class="sort" data-sort="anho">Año</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @foreach ($temas as $tema)
                                            <tr>
                                                <td>{{$tema->id}}</td>
                                                <td class="fecha">{{\Carbon\Carbon::parse($tema->fecha)->format('d/m/Y H:i:s')}}</td>
                                                <td class="alumno">{{$tema->alumno->primer_nombre}} {{$tema->alumno->primer_apellido}}</td>
                                                <td class="alumno_documento">{{$tema->alumno->numero_documento}}</td>
                                                <td class="tutor">{{$tema->tutor->primer_nombre}} {{$tema->tutor->primer_apellido}}</td>
                                                <td>{{$tema->tipo->nombre}} / {{$tema->area->nombre}} / {{$tema->linea->nombre}}</td>
                                                <td>{{$tema->tema}}</td>
                                                <td class="anho">{{\Carbon\Carbon::parse($tema->fecha)->format('Y')}}</td>
                                                <td>
                                                    <span
                                                        class="badge @if ($tema->estado == 'AC')
                                                            bg-success-subtle text-success text-uppercase"> Ap. Coord.
                                                        @elseif ($tema->estado == 'EC')
                                                            bg-warning-subtle text-warning text-uppercase"> En Curso
                                                        @elseif ($tema->estado == 'PC')
                                                            bg-warning-subtle text-warning text-uppercase"> Proy. en Curso
                                                        @elseif ($tema->estado == 'BC')
                                                            bg-warning-subtle text-warning text-uppercase"> Borr. en Curso
                                                        @elseif ($tema->estado == 'AA')
                                                            bg-success-subtle text-success text-uppercase"> Aprobado
                                                        @elseif ($tema->estado == 'AP')
                                                            bg-success-subtle text-success text-uppercase"> Ap. Proyecto
                                                        @elseif ($tema->estado == 'AB')
                                                            bg-success-subtle text-success text-uppercase"> Ap. Borrador
                                                        @elseif ($tema->estado == 'PA')
                                                            bg-success-subtle text-success text-uppercase"> Pagado
                                                        @elseif ($tema->estado == 'FE')
                                                            bg-warning-subtle text-warning text-uppercase"> Defensa
                                                        @elseif ($tema->estado == 'RR')
                                                            bg-success-subtle text-success text-uppercase"> Aprobado
                                                        @elseif ($tema->estado == 'RR')
                                                            bg-danger-subtle text-danger text-uppercase"> Reprobado
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    <a type="button" class="btn btn-sm btn-primary" href="{{route('anteproyectos_tesis.show', $tema->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="noresults" style="display: none">
                                    <div class="text-center">
                                        <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" estado="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                        <h5 class="mt-2">Sin resultados.</h5>
                                        <p class="text-muted mb-0">No pudimos encontrar ningún trabajo final de grado según tus parámetros de búsqueda.</p>
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
        @include('tesis.anteproyectos.scripts.index-scripts')
    @endsection
@endcan
