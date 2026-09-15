@can('ver_materias_alumnos_pantalla')
    @extends('layouts.master')
    @section('title') Mis Materias @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('title') BIENVENIDO, {{$alumno->primer_nombre}} {{$alumno->primer_apellido}} @endslot
        @endcomponent

        @include('index.scripts.messages-scripts')
        @include('pantallas_alumnos.modals.materias-modals')

        <div class="row d-flex flex-wrap justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Mis Materias</h4>
                    </div>
                    <div class="card-body">
                        <div id="materias-list">
                            <div class="row g-4 mb-3">
                                <div class="col-lg-12">
                                    <div class="d-flex justify-content-end">
                                        <div class="search-box ms-2">
                                            <input type="text" class="form-control search" placeholder="Buscar materia...">
                                            <i class="ri-search-line search-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive table-card mt-3 mb-1">
                                <table class="table align-middle table-nowrap" id="materias-list">
                                    <thead class="table-light text-center">
                                        <tr>
                                            <th class="sort" data-sort="materia">Materia</th>
                                            <th class="sort" data-sort="docente">Docente</th>
                                            @can('ver_asistencias_alumnos_pantalla')
                                                <th>Asistencia</th>
                                            @endcan
                                            @can('ver_puntajes_alumnos_pantalla')
                                                <th>Evaluación Continua</th>
                                            @endcan
                                            @can('ver_notas_alumnos_pantalla')
                                                <th>Nota Final</th>
                                            @endcan
                                            @can('ver_planes_clases_alumnos_pantalla')
                                                <th>Plan de Clase</th>
                                            @endcan
                                            @can('ver_programas_clases_alumnos_pantalla')
                                                <th>Programa de Estudio</th>
                                            @endcan
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all text-center">
                                        @forelse ($inscripciones as $inscripcion)
                                            <tr>
                                                <td class="materia">{{$inscripcion->materia->nombre_fantasia}}</td>
                                                <td class="docente">{{$inscripcion->docente->primer_nombre}} {{$inscripcion->docente->primer_apellido}}</td>
                                                @can('ver_asistencias_alumnos_pantalla')
                                                    <td>
                                                        @if ($inscripcion->porcentaje_asistencia !== null)
                                                            {{$inscripcion->porcentaje_asistencia}}%
                                                        @else
                                                            ---
                                                        @endif
                                                    </td>
                                                @endcan
                                                @can('ver_puntajes_alumnos_pantalla')
                                                    <td>
                                                        @if ($inscripcion->puntaje_proceso || $inscripcion->puntaje_parcial || $inscripcion->puntaje_recuperatorio || $inscripcion->puntaje_ordinario || $inscripcion->puntaje_complementario || $inscripcion->puntaje_extraordinario)
                                                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#puntajesModal-{{$inscripcion->id}}">Ver Puntajes</button>
                                                        @else
                                                            ---
                                                        @endif
                                                    </td>
                                                @endcan
                                                @can('ver_notas_alumnos_pantalla')
                                                    <td>
                                                        @if ($inscripcion->nota_ordinario !== null)
                                                            {{$inscripcion->nota_ordinario}}
                                                        @elseif ($inscripcion->nota_complementario !== null)
                                                            {{$inscripcion->nota_complementario}}
                                                        @elseif ($inscripcion->nota_extraordinario !== null)
                                                            {{$inscripcion->nota_extraordinario}}
                                                        @else
                                                            ---
                                                        @endif
                                                    </td>
                                                @endcan
                                                @can('ver_planes_clases_alumnos_pantalla')
                                                    <td>
                                                        @if ($inscripcion->url_plan_clase)
                                                            <a href="{{asset($inscripcion->url_plan_clase)}}" target="_blank" class="btn btn-sm btn-info">Ver Plan</a>
                                                        @else
                                                            ---
                                                        @endif
                                                    </td>
                                                @endcan
                                                @can('ver_programas_clases_alumnos_pantalla')
                                                    <td>
                                                        @if ($inscripcion->url_programa_clase)
                                                            <a href="{{asset($inscripcion->url_programa_clase)}}" target="_blank" class="btn btn-sm btn-info">Ver Programa</a>
                                                        @else
                                                            ---
                                                        @endif
                                                    </td>
                                                @endcan
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6">No tienes inscripciones activas.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <div class="noresults" style="display: none">
                                    <div class="text-center">
                                        <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" state="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
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
                    </div>
                </div><!-- end card -->
                <div class="row">
                    <div class="col-lg-12 text-center mb-3">
                        <a type="button" class="btn btn-danger me-2" href="{{route('root')}}">Volver</a>
                    </div>
                </div>
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
    @endsection

    @section('script')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        @include('pantallas_alumnos.scripts.materias-scripts')
    @endsection
@endcan
