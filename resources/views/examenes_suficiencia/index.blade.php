@can('ver_examenes_suficiencia')
    @extends('layouts.master')
    @section('title') Exámenes de Suficiencia @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Exámenes de Suficiencia @endslot
        @endcomponent

        @include('examenes_suficiencia.modals.index-modals')
        @include('examenes_suficiencia.scripts.messages-scripts')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Lista de Exámenes de Suficiencia</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="examenes_suficiencia-list">
                            <div class="row g-4 mb-3">
                                <div class="col-lg-8">
                                    @can('crear_examenes_suficiencia')
                                        <a type="button" class="btn btn-success" href="{{route('examenes_suficiencia.create')}}"><i class="ri-add-line align-bottom me-1"></i>Agregar</a>
                                    @endcan
                                    @can('ver_actas_examenes_suficiencia')
                                        <a type="button" class="btn btn-warning" href="{{route('examenes_suficiencia.index_actas')}}">Actas Generados</a>
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
                                <table class="table align-middle table-nowrap" id="examenes_suficiencia-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th class="sort" data-sort="materia">Materia</th>
                                            <th class="sort" data-sort="alumno">Alumno</th>
                                            <th class="sort" data-sort="docente">Docente</th>
                                            <th class="sort" data-sort="fecha">Fecha de Exámen</th>
                                            <th>Aula</th>
                                            <th class="sort" data-sort="semestre">Semestre</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @foreach ($examenes as $examen)
                                            <tr>
                                                <td class="id">{{$examen->id}}</td>
                                                <td class="semestre">{{$examen->materia->nombre_fantasia}}</td>
                                                <td class="alumno">{{$examen->alumno->primer_nombre}} {{$examen->alumno->primer_apellido}}</td>
                                                <td class="programa">{{$examen->docente->primer_nombre}} {{$examen->docente->primer_apellido}}</td>
                                                <td class="fecha">{{Carbon\Carbon::parse($examen->fecha_examen)->format('d/m/y H:i:s')}}</td>
                                                <td>{{$examen->aula_examen}}</td>
                                                <td class="semestre">{{$examen->semestre->nombre}}</td>
                                                <td class="estado">
                                                    <span
                                                        class="badge @if ($examen->estado == 'AC')
                                                            bg-primary-subtle text-primary text-uppercase"> Activo
                                                        @elseif ($examen->estado == 'GE')
                                                            bg-primary-subtle text-primary text-uppercase"> Acta Gen.
                                                        @elseif ($examen->estado == 'AP')
                                                            bg-success-subtle text-success text-uppercase"> Aprobado
                                                        @elseif ($examen->estado == 'RE')
                                                            bg-danger-subtle text-danger text-uppercase"> Reprobado
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    <a type="button" class="btn btn-sm btn-primary" href="{{route('examenes_suficiencia.show', $examen->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></a>
                                                    @if ($examen->estado == 'AC')
                                                        @can('editar_examenes_suficiencia')
                                                            <a type="button" class="btn btn-sm btn-warning" href="{{route('examenes_suficiencia.edit', $examen->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-fill"></i></a>
                                                        @endcan
                                                        @can('eliminar_examenes_suficiencia')
                                                            <a type="button" class="btn btn-sm btn-danger" href="{{route('examenes_suficiencia.destroy', $examen->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"><i class="ri-delete-bin-fill"></i></a>
                                                        @endcan
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="noresults" style="display: none">
                                    <div class="text-center">
                                        <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" estado="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                        <h5 class="mt-2">Sin resultados.</h5>
                                        <p class="text-muted mb-0">No pudimos encontrar ningún examen según tus parámetros de búsqueda.</p>
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
        @include('examenes_suficiencia.scripts.index-scripts')
    @endsection
@endcan
