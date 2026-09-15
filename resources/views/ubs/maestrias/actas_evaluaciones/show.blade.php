@can('ver_actas_maestrias_ubs')
    @extends('layouts.master')
    @section('title') Ver Acta de Evaluación @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <style>
            @media screen and (max-width: 600px) {
                #div-subir {
                    margin-left: 2.6em;
                    margin-top: -0.5em;
                }
            }
        </style>
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Acta de Evaluaciones @endslot
            @slot('title') Ver Acta de Evaluación @endslot
        @endcomponent

        @include('ubs.maestrias.scripts.messages-scripts')
        @include('ubs.maestrias.actas_evaluaciones.modals.show-modals')

        <div class="row">
            <form id="update-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar acta de evaluación de {{$acta_evaluacion->curso->nombre_fantasia}} - Módulo {{Str::title($acta_evaluacion->modulo->nombre_fanstasia)}}</h4>
                            </div>
                            <div class="col-lg-6 text-end" id="div-subir">
                                <div class="d-flex justify-content-end">
                                    @if ($acta_evaluacion->ubicacion_adjunto)
                                        <a type="button" class="btn btn-warning me-2" href="{{asset($acta_evaluacion->ubicacion_adjunto)}}" target="_blank">Ver Acta Subido</a>
                                        @can('eliminar_adjunto_actas_maestrias_ubs')
                                            <a type="button" class="btn btn-danger" id="eliminar-adjunto-acta-btn">Eliminar Acta Subido</a>
                                        @endcan
                                    @else
                                        @can('subir_adjunto_actas_maestrias_ubs')
                                            <button type="button" class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#subirActaModal" id="subir-acta-btn">Subir Acta</button>
                                        @endcan
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="numero_acta">N° de Acta</label>
                                    <input type="text" class="form-control text-center" id="numero_acta" value="{{str_pad($acta_evaluacion->numero_acta, 7, 0, STR_PAD_LEFT)}}" readonly>
                                </div>
                                <div class="col-lg-1 mb-3">
                                    <label class="form-label" for="seccion">Sección</label>
                                    <input type="text" class="form-control text-center" id="seccion" value="{{$acta_evaluacion->seccion}}" readonly>
                                </div>
                                <div class="col-lg-1 mb-3">
                                    <label class="form-label" for="turno">Turno</label>
                                    <input type="text" class="form-control text-center" id="turno" @if ($acta_evaluacion->seccion == 'M') value="MAÑANA" @elseif ($acta_evaluacion->seccion == 'T') value="TARDE" @elseif ($acta_evaluacion->seccion == 'N') value="NOCHE" @endif readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha_evaluacion">Fecha de Evaluación</label>
                                    <input type="text" class="form-control text-center" id="fecha_evaluacion" value="{{\Carbon\Carbon::parse($acta_evaluacion->fecha_evaluacion)->format('d/m/Y')}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="evaluacion">Evaluación</label>
                                    <input type="text" class="form-control" id="evaluacion" @if ($acta_evaluacion->tipo == 'O') value="ORDINARIO" @elseif ($acta_evaluacion->tipo == 'C') value="COMPLEMENTARIO" @elseif ($acta_evaluacion->tipo == 'E') value="EXTRAORDINARIO" @endif readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="curso">Curso</label>
                                    <input type="text" class="form-control" id="curso" value="{{$acta_evaluacion->curso->nombre_fantasia}}" readonly>
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="modulo">Módulo</label>
                                    <input type="text" class="form-control" id="modulo" value="{{$acta_evaluacion->modulo->nombre_fantasia}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="acta_evaluacion">Docente</label>
                                    <input type="text" class="form-control" id="acta_evaluacion" value="{{$acta_evaluacion->docente->primer_nombre}} {{$acta_evaluacion->docente->primer_apellido}}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div id="lista_alumnos-list">
                                    <div class="card-header d-flex flex-wrap justify-content-between">
                                        <div class="col-lg-8">
                                            <h4 class="card-title mb-0">Lista de Alumnos</h4>
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
                                    <div class="card-body">
                                        <div class="table-responsive table-card mt-3 mb-1">
                                            <table class="table align-middle table-nowrap text-center" id="lista_alumnos-list">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="sort" data-sort="numero_documento">N° de Documento</th>
                                                        <th class="sort" data-sort="alumno">Alumno</th>
                                                        <th class="sort" data-sort="evaluacion_continua">Evaluación Continua (60)</th>
                                                        <th class="sort" data-sort="examen_final">Examen Final (40)</th>
                                                        <th class="sort" data-sort="calificacion">Calificación</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="list form-check-all">
                                                    @foreach ($acta_evaluacion->alumnos as $lista_alumno)
                                                        <tr>
                                                            <td class="numero_documento">{{$lista_alumno->alumno->numero_documento}}</td>
                                                            <td class="alumno">
                                                                {{ trim($lista_alumno->alumno->primer_apellido . ($lista_alumno->alumno->segundo_apellido ? ' ' . $lista_alumno->alumno->segundo_apellido : '')) }},
                                                                {{$lista_alumno->alumno->primer_nombre}}
                                                                @if ($lista_alumno->alumno->segundo_nombre)
                                                                    {{$lista_alumno->alumno->segundo_nombre}}
                                                                @endif
                                                                @if ($lista_alumno->alumno->tercer_nombre)
                                                                    {{$lista_alumno->alumno->tercer_nombre}}
                                                                @endif
                                                            </td>
                                                            <td class="evaluacion_continua">{{$lista_alumno->puntos_obtenidos}}</td>
                                                            <td class="examen_final">{{$lista_alumno->puntos_examen}}</td>
                                                            <td class="calificacion">{{$lista_alumno->calificacion}}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <div class="noresults" style="display: none">
                                                <div class="text-center">
                                                    <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" estado="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                                    <h5 class="mt-2">Sin resultados.</h5>
                                                    <p class="text-muted mb-0">No pudimos encontrar ningún alumno según tus parámetros de búsqueda.</p>
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
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="cargado_por">Generado por:</label>
                            <br>
                            {{$acta_evaluacion->generadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($acta_evaluacion->created_at)->format('d/m/Y H:i:s')}}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('actas_evaluaciones_ubs.index', $acta_evaluacion->curso_id)}}">Volver</a>
                        </div>
                    </div>
                </div>
            </form>
            <form action="{{route('actas_evaluaciones_ubs.eliminar_acta', $acta_evaluacion->id)}}" method="delete" id="eliminar-acta-form">
                @csrf
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        @include('ubs.maestrias.actas_evaluaciones.scripts.show-scripts')
    @endsection
@endcan
