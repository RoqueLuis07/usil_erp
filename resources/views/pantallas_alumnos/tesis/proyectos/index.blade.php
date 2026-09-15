@can('ver_proyectos_tesis_alumnos_pantalla')
    @extends('layouts.master')
    @section('title') Proyectos de T.F.G. @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('title') BIENVENIDO, {{$alumno->primer_nombre}} {{$alumno->primer_apellido}} @endslot
        @endcomponent

        @include('pantallas_alumnos.scripts.messages-scripts')

        <div class="row d-flex flex-wrap justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Proyectos de Trabajos Finales de Grado</h4>
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
                                <table class="table align-middle table-nowrap text-center" id="temas-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="sort" data-sort="fecha">Fecha Inscripción</th>
                                            <th class="sort" data-sort="tutor">Tutor</th>
                                            <th>Tema</th>
                                            <th class="sort" data-sort="anho">Año</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @forelse ($temas as $tema)
                                            <tr>
                                                <td class="fecha">{{\Carbon\Carbon::parse($tema->fecha)->format('d/m/Y H:i')}}</td>
                                                <td class="tutor">{{$tema->tutor->primer_nombre}} {{$tema->tutor->primer_apellido}}</td>
                                                <td>{{$tema->tema}}</td>
                                                <td class="anho">{{\Carbon\Carbon::parse($tema->fecha)->format('Y')}}</td>
                                                <td>
                                                    <span
                                                        class="badge @if ($tema->estado == 'PE')
                                                            bg-danger-subtle text-danger text-uppercase"> Pendiente
                                                        @elseif ($tema->estado == 'RE')
                                                            bg-danger-subtle text-danger text-uppercase"> Rechazado
                                                        @elseif ($tema->estado == 'AT')
                                                            bg-success-subtle text-success text-uppercase"> Ap. Tutor
                                                        @elseif ($tema->estado == 'AC')
                                                            bg-success-subtle text-success text-uppercase"> Ap. Coord.
                                                        @elseif ($tema->estado == 'EC')
                                                            bg-warning-subtle text-warning text-uppercase"> Ant. En Curso
                                                        @elseif ($tema->estado == 'PC')
                                                            bg-warning-subtle text-warning text-uppercase"> Proy. En Curso
                                                        @elseif ($tema->estado == 'BC')
                                                            bg-warning-subtle text-warning text-uppercase"> Borr. En Curso
                                                        @elseif ($tema->estado == 'AA')
                                                            bg-success-subtle text-success text-uppercase"> Ap. Ant.
                                                        @elseif ($tema->estado == 'AP')
                                                            bg-success-subtle text-success text-uppercase"> Ap. Proyecto
                                                        @elseif ($tema->estado == 'AB')
                                                            bg-success-subtle text-success text-uppercase"> Ap. Borrador
                                                        @elseif ($tema->estado == 'PA')
                                                            bg-success-subtle text-success text-uppercase"> Pagado
                                                        @elseif ($tema->estado == 'FE')
                                                            bg-warning-subtle text-warning text-uppercase"> Defensa
                                                        @elseif ($tema->estado == 'EN')
                                                            bg-success-subtle text-success text-uppercase"> Aprobado
                                                        @elseif ($tema->estado == 'RR')
                                                            bg-danger-subtle text-danger text-uppercase"> Reprobado
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    <a type="button" class="btn btn-sm btn-primary" href="{{route('pantallas_alumnos.show_proyectos_tesis', $tema->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6">No cuenta con proyectos aprobados.</td>
                                            </tr>
                                        @endforelse
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
                <a type="button" class="btn btn-danger me-2" href="{{route('pantallas_alumnos.index', Auth::id())}}">Volver</a>
            </div>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/prismjs/prism.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        @include('pantallas_alumnos.tesis.proyectos.scripts.index-scripts')
    @endsection
@endcan
