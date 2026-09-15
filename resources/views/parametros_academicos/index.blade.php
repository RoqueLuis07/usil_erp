@can('ver_parametros_academicos')
    @extends('layouts.master')
    @section('title') Parámetros @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Parámetros @endslot
        @endcomponent

        <div class="row mt-5">
            <div class="col-lg-12">
                <div class="row justify-content-center">
                    @can('ver_programas')
                        <div class="col-lg-2 col-xxl-2">
                            <div class="card card-body text-center">
                                <div class="avatar-sm mx-auto mb-3">
                                    <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                        <i class="bi bi-file-spreadsheet"></i>
                                    </div>
                                </div>
                                <h4 class="card-title">Programas</h4>
                                <a type="button" href="{{route('programas.index')}}" class="btn btn-info">Visualizar</a>
                            </div>
                        </div>
                    @endcan
                    @can('ver_facultades')
                        <div class="col-lg-2 col-xxl-2">
                            <div class="card card-body text-center">
                                <div class="avatar-sm mx-auto mb-3">
                                    <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                        <i class="bi bi-file-spreadsheet"></i>
                                    </div>
                                </div>
                                <h4 class="card-title">Facultades</h4>
                                <a type="button" href="{{route('facultades.index')}}" class="btn btn-info">Visualizar</a>
                            </div>
                        </div>
                    @endcan
                    @can('ver_carreras')
                        <div class="col-lg-2 col-xxl-2">
                            <div class="card card-body text-center">
                                <div class="avatar-sm mx-auto mb-3">
                                    <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                        <i class="bi bi-card-list"></i>
                                    </div>
                                </div>
                                <h4 class="card-title">Carreras</h4>
                                <a type="button" href="{{route('carreras.index')}}" class="btn btn-info">Visualizar</a>
                            </div>
                        </div>
                    @endcan
                    @can('ver_tipos_carreras')
                        <div class="col-lg-2 col-xxl-2">
                            <div class="card card-body text-center">
                                <div class="avatar-sm mx-auto mb-3">
                                    <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                        <i class="bi bi-card-list"></i>
                                    </div>
                                </div>
                                <h4 class="card-title">Tipos de Carreras</h4>
                                <a type="button" href="{{route('tipos_carreras.index')}}" class="btn btn-info">Visualizar</a>
                            </div>
                        </div>
                    @endcan
                    @can('ver_materias')
                        <div class="col-lg-2 col-xxl-2">
                            <div class="card card-body text-center">
                                <div class="avatar-sm mx-auto mb-3">
                                    <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                        <i class="bi bi-card-list"></i>
                                    </div>
                                </div>
                                <h4 class="card-title">Materias</h4>
                                <a type="button" href="{{route('materias.index')}}" class="btn btn-info">Visualizar</a>
                            </div>
                        </div>
                    @endcan
                    @can('ver_materias_suficiencias')
                        <div class="col-lg-2 col-xxl-2">
                            <div class="card card-body text-center position-relative">
                                <div class="d-flex flex-wrap gap-3">
                                    <div class="avatar-sm mx-auto mb-3">
                                        <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                            <i class="bi bi-card-list"></i>
                                        </div>
                                    </div>
                                    <span class="position-absolute top-0 end-90 badge bg-danger-subtle text-danger badge-border mt-2" style="margin-left: -5px">Suficiencia</span>
                                </div>
                                <h4 class="card-title">Materias</h4>
                                <a type="button" href="{{route('materias_suficiencias.index')}}" class="btn btn-info">Visualizar</a>
                            </div>
                        </div>
                    @endcan
                    <div class="row justify-content-center">
                        @can('ver_periodos')
                            <div class="col-lg-2 col-xxl-2">
                                <div class="card card-body text-center">
                                    <div class="avatar-sm mx-auto mb-3">
                                        <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                            <i class="bi bi-calendar-date"></i>
                                        </div>
                                    </div>
                                    <h4 class="card-title">Semestres</h4>
                                    <a type="button" href="{{route('semestres.index')}}" class="btn btn-info">Visualizar</a>
                                </div>
                            </div>
                        @endcan
                        @can('ver_mallas')
                            <div class="col-lg-2 col-xxl-2">
                                <div class="card card-body text-center">
                                    <div class="avatar-sm mx-auto mb-3">
                                        <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                            <i class="bi bi-file-spreadsheet"></i>
                                        </div>
                                    </div>
                                    <h4 class="card-title">Mallas Curriculares</h4>
                                    <a type="button" href="{{route('mallas.index')}}" class="btn btn-info">Visualizar</a>
                                </div>
                            </div>
                        @endcan
                        @can('ver_mallas_espejo')
                            <div class="col-lg-2 col-xxl-2">
                                <div class="card card-body text-center">
                                    <div class="avatar-sm mx-auto mb-3">
                                        <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                            <i class="bi bi-calendar-date"></i>
                                        </div>
                                    </div>
                                    <h4 class="card-title">Mallas Espejo</h4>
                                    <a type="button" href="{{route('mallas_espejos.index')}}" class="btn btn-info">Visualizar</a>
                                </div>
                            </div>
                        @endcan
                        @can('ver_escalas')
                            <div class="col-lg-2 col-xxl-2">
                                <div class="card card-body text-center">
                                    <div class="avatar-sm mx-auto mb-3">
                                        <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                            <i class="bi bi-bar-chart-steps"></i>
                                        </div>
                                    </div>
                                    <h4 class="card-title">Escalas de Notas</h4>
                                    <a type="button" href="{{route('escalas.index')}}" class="btn btn-info">Visualizar</a>
                                </div>
                            </div>
                        @endcan
                        @can('ver_evaluaciones')
                            <div class="col-lg-2 col-xxl-2">
                                <div class="card card-body text-center">
                                    <div class="avatar-sm mx-auto mb-3">
                                        <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                            <i class="bi bi-file-check-fill"></i>
                                        </div>
                                    </div>
                                    <h4 class="card-title">Evaluaciones</h4>
                                    <a type="button" href="{{route('evaluaciones.index')}}" class="btn btn-info">Visualizar</a>
                                </div>
                            </div>
                        @endcan
                        @can('ver_tipos_evaluaciones')
                            <div class="col-lg-2 col-xxl-2">
                                <div class="card card-body text-center">
                                    <div class="avatar-sm mx-auto mb-3">
                                        <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                            <i class="bi bi-card-list"></i>
                                        </div>
                                    </div>
                                    <h4 class="card-title">Tipos de Evaluaciones</h4>
                                    <a type="button" href="{{route('tipos_evaluaciones.index')}}" class="btn btn-info">Visualizar</a>
                                </div>
                            </div>
                        @endcan
                    </div>
                    <div class="row d-flex flex-wrap justify-content-center">
                        @can('ver_modalidades')
                            <div class="col-lg-2 col-xxl-2">
                                <div class="card card-body text-center">
                                    <div class="avatar-sm mx-auto mb-3">
                                        <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                            <i class="bi bi-calendar-date"></i>
                                        </div>
                                    </div>
                                    <h4 class="card-title">Modalidades</h4>
                                    <a type="button" href="{{route('modalidades.index')}}" class="btn btn-info">Visualizar</a>
                                </div>
                            </div>
                        @endcan
                        @can('ver_tipos_extensiones_universitarias')
                            <div class="col-lg-2 col-xxl-2">
                                <div class="card card-body text-center position-relative">
                                    <div class="d-flex flex-wrap gap-3">
                                        <div class="avatar-sm mx-auto mb-3">
                                            <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                                <i class="bi bi-card-list"></i>
                                            </div>
                                        </div>
                                        <span class="position-absolute top-0 end-90 badge bg-danger-subtle text-danger badge-border mt-2" style="margin-left: -5px">Extensión Univ.</span>
                                    </div>
                                    <h4 class="card-title">Tipos de Actividades</h4>
                                    <a type="button" href="{{route('tipos_extensiones_universitarias.index')}}" class="btn btn-info">Visualizar</a>
                                </div>
                            </div>
                        @endcan
                        @can('ver_requerimientos_extensiones_universitarias')
                            <div class="col-lg-2 col-xxl-2">
                                <div class="card card-body text-center position-relative">
                                    <div class="d-flex flex-wrap gap-3">
                                        <div class="avatar-sm mx-auto mb-3">
                                            <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                                <i class="bi bi-asterisk"></i>
                                            </div>
                                        </div>
                                        <span class="position-absolute top-0 end-90 badge bg-danger-subtle text-danger badge-border mt-2" style="margin-left: -5px">Extensión Univ.</span>
                                    </div>
                                    <h4 class="card-title">Requerimientos</h4>
                                    <a type="button" href="{{route('requerimientos_extensiones_universitarias.show')}}" class="btn btn-info">Visualizar</a>
                                </div>
                            </div>
                        @endcan
                        @can('ver_formaciones_academicas')
                            <div class="col-lg-2 col-xxl-2">
                                <div class="card card-body text-center">
                                    <div class="avatar-sm mx-auto mb-3">
                                        <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                            <i class="bi bi-mortarboard"></i>
                                        </div>
                                    </div>
                                    <h4 class="card-title">Formaciones Académicas</h4>
                                    <a type="button" href="{{route('alumnos_formaciones.index')}}" class="btn btn-info">Visualizar</a>
                                </div>
                            </div>
                        @endcan
                        @can('ver_instituciones_educativas')
                            <div class="col-lg-2 col-xxl-2">
                                <div class="card card-body text-center">
                                    <div class="avatar-sm mx-auto mb-3">
                                        <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                            <i class="bi bi-buildings"></i>
                                        </div>
                                    </div>
                                    <h4 class="card-title">Instituciones Educativas</h4>
                                    <a type="button" href="{{route('instituciones_educativas.index')}}" class="btn btn-info">Visualizar</a>
                                </div>
                            </div>
                        @endcan
                        @can('ver_tipos_solicitudes')
                            <div class="col-lg-2 col-xxl-2">
                                <div class="card card-body text-center">
                                    <div class="avatar-sm mx-auto mb-3">
                                        <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                            <i class="bi bi-card-list"></i>
                                        </div>
                                    </div>
                                    <h4 class="card-title">Tipos de Solicitudes</h4>
                                    <a type="button" href="{{route('tipos_solicitudes.index')}}" class="btn btn-info">Visualizar</a>
                                </div>
                            </div>
                        @endcan
                    </div>
                    <div class="row">
                        @can('ver_fechas_desmatriculaciones')
                            <div class="col-lg-2 col-xxl-2">
                                <div class="card card-body text-center">
                                    <div class="avatar-sm mx-auto mb-3">
                                        <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                            <i class="bi bi-card-list"></i>
                                        </div>
                                    </div>
                                    <h4 class="card-title">Fechas de Desmatriculación</h4>
                                    <a type="button" href="{{route('fechas_desmatriculaciones.index')}}" class="btn btn-info">Visualizar</a>
                                </div>
                            </div>
                        @endcan
                        @can('ver_fechas_solicitudes_examenes_suficiencia')
                            <div class="col-lg-2 col-xxl-2">
                                <div class="card card-body text-center">
                                    <div class="avatar-sm mx-auto mb-3">
                                        <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                            <i class="bi bi-card-list"></i>
                                        </div>
                                    </div>
                                    <h4 class="card-title">Fechas de Solicitudes de Examenes de Suficiencia</h4>
                                    <a type="button" href="{{route('examenes_suficiencias_fechas_solicitudes.index')}}" class="btn btn-info">Visualizar</a>
                                </div>
                            </div>
                        @endcan
                        @can('ver_areas_conocimientos')
                            <div class="col-lg-2 col-xxl-2">
                                <div class="card card-body text-center">
                                    <div class="avatar-sm mx-auto mb-3">
                                        <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                            <i class="bi bi-card-list"></i>
                                        </div>
                                    </div>
                                    <h4 class="card-title">Áreas de Conocimiento</h4>
                                    <a type="button" href="{{route('areas_conocimientos.index')}}" class="btn btn-info">Visualizar</a>
                                </div>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>

    @endsection

    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
    @endsection
@endcan
