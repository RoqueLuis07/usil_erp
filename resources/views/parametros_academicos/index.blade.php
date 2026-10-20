@can('ver_parametros_academicos')
    @extends('layouts.master-academic')
    @section('title') Parámetros @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Parámetros @endslot
        @endcomponent

        <div class="row mt-3">
            <div class="col-lg-12">
                <div class="row justify-content-center">
                    <div class="col-12"><h6 class="text-muted text-uppercase mt-3 mb-2" style="font-size: 12px; letter-spacing: .04em;">Extensión</h6></div>
                        @can('ver_tipos_extensiones_universitarias')
                            <div class="col-lg-2 col-xxl-2">
                                <div class="card card-body text-center">
                                    <div class="avatar-sm mx-auto mb-3">
                                        <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                            <i class="bi bi-card-list"></i>
                                        </div>
                                    </div>
                                    <h4 class="card-title">Tipos de Actividades</h4>
                                    <a type="button" href="{{route('tipos_extensiones_universitarias.index')}}" class="btn btn-info">Visualizar</a>
                                </div>
                            </div>
                        @endcan
                        @can('ver_requerimientos_extensiones_universitarias')
                            <div class="col-lg-2 col-xxl-2">
                                <div class="card card-body text-center">
                                    <div class="avatar-sm mx-auto mb-3">
                                        <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                            <i class="bi bi-asterisk"></i>
                                        </div>
                                    </div>
                                    <h4 class="card-title">Requerimientos</h4>
                                    <a type="button" href="{{route('requerimientos_extensiones_universitarias.show')}}" class="btn btn-info">Visualizar</a>
                                </div>
                            </div>
                        @endcan
                    <div class="col-12"><h6 class="text-muted text-uppercase mt-3 mb-2" style="font-size: 12px; letter-spacing: .04em;">Estructura académica</h6></div>
                        @can('ver_facultades')
                            <div class="col-lg-2 col-xxl-2">
                                <div class="card card-body text-center">
                                    <div class="avatar-sm mx-auto mb-3">
                                        <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                            <i class="bi bi-buildings"></i>
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
                        @can('ver_materias')
                            <div class="col-lg-2 col-xxl-2">
                                <div class="card card-body text-center">
                                    <div class="avatar-sm mx-auto mb-3">
                                        <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                            <i class="bi bi-journal-text"></i>
                                        </div>
                                    </div>
                                    <h4 class="card-title">Materias</h4>
                                    <a type="button" href="{{route('materias.index')}}" class="btn btn-info">Visualizar</a>
                                </div>
                            </div>
                        @endcan
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
                    <div class="col-12"><h6 class="text-muted text-uppercase mt-3 mb-2" style="font-size: 12px; letter-spacing: .04em;">Personas</h6></div>
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
                        @can('ver_formas_conocimientos')
                            <div class="col-lg-2 col-xxl-2">
                                <div class="card card-body text-center">
                                    <div class="avatar-sm mx-auto mb-3">
                                        <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                            <i class="bi bi-megaphone"></i>
                                        </div>
                                    </div>
                                    <h4 class="card-title">Formas de Conocer la Universidad</h4>
                                    <a type="button" href="{{route('formas_conocimientos.index')}}" class="btn btn-info">Visualizar</a>
                                </div>
                            </div>
                        @endcan
                    <div class="col-12"><h6 class="text-muted text-uppercase mt-3 mb-2" style="font-size: 12px; letter-spacing: .04em;">Ubicación</h6></div>
                        @can('ver_nacionalidades')
                            <div class="col-lg-2 col-xxl-2">
                                <div class="card card-body text-center">
                                    <div class="avatar-sm mx-auto mb-3">
                                        <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                            <i class="bi bi-flag"></i>
                                        </div>
                                    </div>
                                    <h4 class="card-title">Nacionalidades</h4>
                                    <a type="button" href="{{route('nacionalidades.index')}}" class="btn btn-info">Visualizar</a>
                                </div>
                            </div>
                        @endcan
                        @can('ver_departamentos_paraguay')
                            <div class="col-lg-2 col-xxl-2">
                                <div class="card card-body text-center">
                                    <div class="avatar-sm mx-auto mb-3">
                                        <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                            <i class="bi bi-map"></i>
                                        </div>
                                    </div>
                                    <h4 class="card-title">Departamentos</h4>
                                    <a type="button" href="{{route('departamentos_paraguay.index')}}" class="btn btn-info">Visualizar</a>
                                </div>
                            </div>
                        @endcan
                        @can('ver_ciudades')
                            <div class="col-lg-2 col-xxl-2">
                                <div class="card card-body text-center">
                                    <div class="avatar-sm mx-auto mb-3">
                                        <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                            <i class="bi bi-geo-alt"></i>
                                        </div>
                                    </div>
                                    <h4 class="card-title">Ciudades</h4>
                                    <a type="button" href="{{route('ciudades.index')}}" class="btn btn-info">Visualizar</a>
                                </div>
                            </div>
                        @endcan
                        @can('ver_barrios')
                            <div class="col-lg-2 col-xxl-2">
                                <div class="card card-body text-center">
                                    <div class="avatar-sm mx-auto mb-3">
                                        <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                            <i class="bi bi-signpost-2"></i>
                                        </div>
                                    </div>
                                    <h4 class="card-title">Barrios</h4>
                                    <a type="button" href="{{route('barrios.index')}}" class="btn btn-info">Visualizar</a>
                                </div>
                            </div>
                        @endcan
                </div>
            </div>
        </div>

    @endsection

    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
    @endsection
@endcan
