@can('ver_inscripciones_tesis')
    @extends('layouts.master')
    @section('title') Temas de Trabajo Final de Grado @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Trabajos Finales de Grado @endslot
            @slot('title') Temas de Trabajo Final de Grado @endslot
        @endcomponent

        @include('tesis.inscripciones_temas.scripts.messages-scripts')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Lista de Temas de Trabajo Final de Grado</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="inscripciones-list">
                            <div class="row g-4 mb-3">
                                <div class="col-lg-8">
                                    @can('crear_inscripciones_tesis')
                                        <a type="button" class="btn btn-success" href="{{route('inscripciones_temas_tesis.create')}}"><i class="ri-add-line align-bottom me-1"></i>Agregar</a>
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
                                <table class="table align-middle table-nowrap" id="inscripciones-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th class="sort" data-sort="fecha">Fecha</th>
                                            <th class="sort" data-sort="alumno">Alumno</th>
                                            <th class="sort" data-sort="alumno_documento">N° Documento</th>
                                            <th class="sort" data-sort="carrera">Carrera</th>
                                            <th>Tipo / Área / Línea</th>
                                            <th>Tema</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @foreach ($inscripciones as $inscripcion)
                                            <tr>
                                                <td>{{$inscripcion->id}}</td>
                                                <td class="fecha">{{\Carbon\Carbon::parse($inscripcion->fecha)->format('d/m/Y H:i:s')}}</td>
                                                <td class="alumno">{{$inscripcion->alumno->primer_nombre}} {{$inscripcion->alumno->primer_apellido}}</td>
                                                <td class="alumno_documento">{{$inscripcion->alumno->numero_documento}}</td>
                                                <td class="carrera">{{$inscripcion->carrera->nombre_fantasia}}</td>
                                                <td>{{$inscripcion->tipo->nombre}} / {{$inscripcion->area->nombre}} / {{$inscripcion->linea->nombre}}</td>
                                                <td>{{$inscripcion->tema}}</td>
                                                <td>
                                                    <span
                                                        class="badge @if ($inscripcion->estado == 'PE')
                                                            bg-danger-subtle text-danger text-uppercase"> Pendiente
                                                        @elseif ($inscripcion->estado == 'RE')
                                                            bg-danger-subtle text-danger text-uppercase"> Rechazado
                                                        @elseif ($inscripcion->estado == 'AT')
                                                            bg-success-subtle text-success text-uppercase"> Ap. Tutor
                                                        @elseif ($inscripcion->estado == 'AC')
                                                            bg-success-subtle text-success text-uppercase"> Ap. Coord.
                                                        @elseif ($inscripcion->estado == 'EC')
                                                            bg-warning-subtle text-warning text-uppercase"> Ant. En Curso
                                                        @elseif ($inscripcion->estado == 'PC')
                                                            bg-warning-subtle text-warning text-uppercase"> Proy. En Curso
                                                        @elseif ($inscripcion->estado == 'BC')
                                                            bg-warning-subtle text-warning text-uppercase"> Borr. En Curso
                                                        @elseif ($inscripcion->estado == 'AA')
                                                            bg-success-subtle text-success text-uppercase"> Ap. Ant.
                                                        @elseif ($inscripcion->estado == 'AP')
                                                            bg-success-subtle text-success text-uppercase"> Ap. Proyecto
                                                        @elseif ($inscripcion->estado == 'AB')
                                                            bg-success-subtle text-success text-uppercase"> Ap. Borrador
                                                        @elseif ($inscripcion->estado == 'PA')
                                                            bg-success-subtle text-success text-uppercase"> Pagado
                                                        @elseif ($inscripcion->estado == 'FE')
                                                            bg-warning-subtle text-warning text-uppercase"> Defensa
                                                        @elseif ($inscripcion->estado == 'EN')
                                                            bg-success-subtle text-success text-uppercase"> Aprobado
                                                        @elseif ($inscripcion->estado == 'RR')
                                                            bg-danger-subtle text-danger text-uppercase"> Reprobado
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    <a type="button" class="btn btn-sm btn-primary" href="{{route('inscripciones_temas_tesis.show', $inscripcion->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></a>
                                                    @can('editar_inscripciones_tesis')
                                                        @if ($inscripcion->estado == 'PE')
                                                            <a type="button" class="btn btn-sm btn-info" href="{{route('inscripciones_temas_tesis.edit', $inscripcion->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-fill"></i></a>
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
                                        <p class="text-muted mb-0">No pudimos encontrar ningún tema de trabajo final de grado según tus parámetros de búsqueda.</p>
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
        @include('tesis.inscripciones_temas.scripts.index-scripts')
    @endsection
@endcan
