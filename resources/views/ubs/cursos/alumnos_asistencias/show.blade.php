@can('ver_asistencias_cursos_ubs')
    @extends('layouts.master')
    @section('title') Ver Asistencias @endsection
    @section('css')
            <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
            <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Cursos @endslot
            @slot('title') Ver Asistencias @endslot
        @endcomponent

        @include('ubs.cursos.scripts.messages-scripts')
        @include('ubs.cursos.alumnos_asistencias.modals.show-modals')

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar asistencias</h4>
                            </div>
                            @can('crear_asistencias_cursos_ubs')
                                @if ($curso->notas->count() == 0)
                                    <div class="col-lg-6 text-end" id="div-cargar" style="margin-bottom: -5em">
                                        <div class="d-flex justify-content-end">
                                            <button type="button" class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#moduloAsistenciasModal" id="get-modulos-btn">Nueva Asistencia</button>
                                        </div>
                                    </div>
                                @endif
                            @endcan
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-lg-9">
                                        <div class="row">
                                            <div class="col-lg-4 mb-3">
                                                <label class="form-label" for="curso">Curso</label>
                                                <input type="text" class="form-control" id="curso" value="{{$curso->nombre_fantasia}}" readonly>
                                                <input type="hidden" id="curso_id" value="{{$curso->id}}">
                                            </div>
                                            <div class="col-lg-2 mb-3">
                                                <label class="form-label" for="modalidad">Modalidad</label>
                                                <input type="text" class="form-control" id="modalidad" value="{{$curso->modalidad->nombre}}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Asistencias</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h6 class="mb-0">General</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-lg-12 mb-3">
                                                            <div id="asistencias-list">
                                                                <div class="row mb-3">
                                                                    <div class="col-lg-4">
                                                                        <div class="d-flex justify-content-sm-start">
                                                                            <div class="search-box ms-2">
                                                                                <input type="text" class="form-control search" placeholder="Buscar...">
                                                                                <i class="ri-search-line search-icon"></i>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-lg-8">
                                                                        <div class="d-flex justify-content-end">
                                                                            <div class="pagination-wrap hstack gap-2">
                                                                                <a class="page-item pagination-prev disabled"><</a>
                                                                                <ul class="pagination listjs-pagination mb-0"></ul>
                                                                                <a class="page-item pagination-next">></a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="p-3">
                                                                    <div class="table-responsive table-card">
                                                                        <table class="table align-middle table-nowrap text-center" id="asistencias-list">
                                                                            <thead class="table-light">
                                                                                <tr>
                                                                                    <th class="sort" data-sort="alumno">Alumno</th>
                                                                                    <th class="sort" data-sort="numero_documento">N° Documento</th>
                                                                                    <th>Porcentaje</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody class="list form-check-all">
                                                                                @foreach ($asistencias_generales as $alumno => $asistencias)
                                                                                    <tr>
                                                                                        <td>{{$asistencias->first()->alumno->primer_nombre}} {{$asistencias->first()->alumno->primer_apellido}}</td>
                                                                                        <td>{{$asistencias->first()->alumno->numero_documento}}</td>
                                                                                        <td>{{$porcentajes_asistencia[$alumno]}} %</td>
                                                                                    </tr>
                                                                                @endforeach
                                                                            </tbody>
                                                                        </table>
                                                                        <div class="noresults" style="display: none">
                                                                            <div class="text-center">
                                                                                <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" estado="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:50px;height:50px"></lord-icon>
                                                                                <h5 class="mt-2">Sin resultados.</h5>
                                                                                <p class="text-muted mb-0">No pudimos encontrar ningún alumno según tus parámetros de búsqueda.</p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h6 class="mb-0">Por Fechas</h6>
                                                </div>
                                                <div class="card-body">
                                                    @if ($curso->asistencias->count() > 0)
                                                        <div class="mb-2">
                                                            <div class="row d-flex flex-wrap justify-content-center">
                                                                <div class="col-lg-12">
                                                                    <div class="accordion custom-accordionwithicon custom-accordion-border accordion-border-box accordion-info" id="acordeon-fecha">
                                                                        @foreach ($asistencias_fechas as $fecha => $asistencias)
                                                                            <div class="accordion-item">
                                                                                <h2 class="accordion-header" id="headingOne">
                                                                                    <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#acordeon-fecha-{{$fecha}}" aria-expanded="false" aria-controls="acordeon-{{$fecha}}" id="asistencia-{{$fecha}}">{{Carbon\Carbon::parse($fecha)->format('d/m/Y')}} &nbsp; <span class="text-muted">Módulo: {{Str::title($asistencias->first()->modulo->nombre_fantasia)}} - {{Str::title($asistencias->first()->modalidad->nombre)}}</span></button>
                                                                                </h2>
                                                                                <div class="accordion-collapse collapse" id="acordeon-fecha-{{$fecha}}" aria-labelledby="acordeon-fecha" data-bs-parent="#acordeon-fecha">
                                                                                    <div class="accordion-body">
                                                                                        <div class="row">
                                                                                            <div class="col-lg-12 mb-3">
                                                                                                <div class="p-3">
                                                                                                    <div class="table-responsive table-card">
                                                                                                        <table class="table align-middle table-nowrap text-center" id="asistencias-fecha-list">
                                                                                                            <thead class="table-light">
                                                                                                                <tr>
                                                                                                                    <th>Alumno</th>
                                                                                                                    <th>N° Documento</th>
                                                                                                                    <th>Estado</th>
                                                                                                                    <th>Observaciones</th>
                                                                                                                </tr>
                                                                                                            </thead>
                                                                                                            <tbody class="list form-check-all">
                                                                                                                @foreach ($asistencias as $asistencia)
                                                                                                                    <tr>
                                                                                                                        <td class="alumno">{{$asistencia->alumno->primer_nombre}} {{$asistencia->alumno->primer_apellido}}</td>
                                                                                                                        <td class="numero_documento">{{$asistencia->alumno->numero_documento}}</td>
                                                                                                                        <td>
                                                                                                                            <button type="button" class="btn
                                                                                                                                @if ($asistencia->estado == 'AU') btn-outline-danger
                                                                                                                                @elseif ($asistencia->estado == 'PR') btn-success
                                                                                                                                @elseif ($asistencia->estado == 'AJ') btn-outline-warning
                                                                                                                                @endif" id="asistencia-{{$asistencia->id}}" data-bs-toggle="modal" data-bs-target="#cambiarAsistenciaModal-{{$asistencia->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Cambiar Asistencia"
                                                                                                                                @if ($asistencia->estado == 'AU') value="AU"
                                                                                                                                @elseif ($asistencia->estado == 'PR') value="PR"
                                                                                                                                @elseif ($asistencia->estado == 'AJ') value="AJ"
                                                                                                                                @endif
                                                                                                                                @if (!Auth::user()->can('editar_asistencias_cursos_ubs'))
                                                                                                                                    disabled
                                                                                                                                @endif>
                                                                                                                                @if ($asistencia->estado == 'AU') Ausente
                                                                                                                                @elseif ($asistencia->estado == 'PR') Presente
                                                                                                                                @elseif ($asistencia->estado == 'AJ') Justificado
                                                                                                                                @endif
                                                                                                                            </button>
                                                                                                                        </td>
                                                                                                                        <td>
                                                                                                                            @if ($asistencia->observaciones)
                                                                                                                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#observacionModal-{{$asistencia->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver Observación"><i class="ri-eye-fill align-bottom"></i></button>
                                                                                                                                @can('editar_asistencias_cursos_ubs')
                                                                                                                                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#changeObservacionModal-{{$asistencia->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar Observación"><i class="ri-edit-fill align-bottom"></i></button>
                                                                                                                                @endcan
                                                                                                                            @else
                                                                                                                                @can('editar_asistencias_cursos_ubs')
                                                                                                                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#changeObservacionModal-{{$asistencia->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Agregar Observación"><i class="ri-add-fill align-bottom"></i></button>
                                                                                                                                @endcan
                                                                                                                            @endif
                                                                                                                        </td>
                                                                                                                    </tr>
                                                                                                                @endforeach
                                                                                                            </tbody>
                                                                                                        </table>
                                                                                                        <div class="noresults" style="display: none">
                                                                                                            <div class="text-center">
                                                                                                                <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" estado="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:50px;height:50px"></lord-icon>
                                                                                                                <h5 class="mt-2">Sin resultados.</h5>
                                                                                                                <p class="text-muted mb-0">No pudimos encontrar ningún alumno según tus parámetros de búsqueda.</p>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="col-lg-12 text-center">
                                                            <h6>El curso no cuenta con asistencias cargadas actualmente.</h6>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('cursos.index')}}">Volver</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        @include('ubs.cursos.alumnos_asistencias.scripts.show-scripts')
    @endsection
@endcan
