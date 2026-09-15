@can('ver_tesis')
    @extends('layouts.master')
    @section('title') Trabajos Finales de Grado @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Trabajos Finales de Grado @endslot
        @endcomponent

        <div class="row mt-5">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Trabajos Finales de Grado</h4>
                    </div>
                    <div class="card-body">
                        <div class="row justify-content-center">
                            @can('ver_inscripciones_tesis')
                                <div class="col-lg-2 col-xxl-2">
                                    <div class="card card-body text-center position-relative">
                                        <div class="d-flex flex-wrap gap-3">
                                            <div class="avatar-sm mx-auto mb-3">
                                                <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                                    <i class="bi bi-card-list"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <h4 class="card-title">Temas</h4>
                                        <a type="button" href="{{route('inscripciones_temas_tesis.index')}}" class="btn btn-info">Visualizar</a>
                                    </div>
                                </div>
                            @endcan
                            @can('ver_anteproyectos_tesis')
                                <div class="col-lg-2 col-xxl-2">
                                    <div class="card card-body text-center position-relative">
                                        <div class="d-flex flex-wrap gap-3">
                                            <div class="avatar-sm mx-auto mb-3">
                                                <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                                    <i class="bi bi-card-list"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <h4 class="card-title">Anteproyectos</h4>
                                        <a type="button" href="{{route('anteproyectos_tesis.index')}}" class="btn btn-info">Visualizar</a>
                                    </div>
                                </div>
                            @endcan
                            @can('ver_proyectos_tesis')
                                <div class="col-lg-2 col-xxl-2">
                                    <div class="card card-body text-center position-relative">
                                        <div class="d-flex flex-wrap gap-3">
                                            <div class="avatar-sm mx-auto mb-3">
                                                <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                                    <i class="bi bi-card-list"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <h4 class="card-title">Proyectos</h4>
                                        <a type="button" href="{{route('proyectos_tesis.index')}}" class="btn btn-info">Visualizar</a>
                                    </div>
                                </div>
                            @endcan
                            @can('ver_borradores_tesis')
                                <div class="col-lg-2 col-xxl-2">
                                    <div class="card card-body text-center position-relative">
                                        <div class="d-flex flex-wrap gap-3">
                                            <div class="avatar-sm mx-auto mb-3">
                                                <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                                    <i class="bi bi-card-list"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <h4 class="card-title">Borradores</h4>
                                        <a type="button" href="{{route('borradores_tesis.index')}}" class="btn btn-info">Visualizar</a>
                                    </div>
                                </div>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Parámetros</h4>
                    </div>
                    <div class="card-body">
                        <div class="row justify-content-center">
                            @can('ver_fechas_defensas_tesis')
                                <div class="col-lg-2 col-xxl-2">
                                    <div class="card card-body text-center position-relative">
                                        <div class="d-flex flex-wrap gap-3">
                                            <div class="avatar-sm mx-auto mb-3">
                                                <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                                    <i class="bi bi-card-list"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <h4 class="card-title">Fechas de Defensa</h4>
                                        <a type="button" href="{{route('fechas_defensas_tesis.index')}}" class="btn btn-info">Visualizar</a>
                                    </div>
                                </div>
                            @endcan
                            @can('ver_tipos_tesis')
                                <div class="col-lg-2 col-xxl-2">
                                    <div class="card card-body text-center position-relative">
                                        <div class="d-flex flex-wrap gap-3">
                                            <div class="avatar-sm mx-auto mb-3">
                                                <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                                    <i class="bi bi-card-list"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <h4 class="card-title">Tipos de T.F.G</h4>
                                        <a type="button" href="{{route('tipos_tesis.index')}}" class="btn btn-info">Visualizar</a>
                                    </div>
                                </div>
                            @endcan
                            @can('ver_areas_tesis')
                                <div class="col-lg-2 col-xxl-2">
                                    <div class="card card-body text-center position-relative">
                                        <div class="d-flex flex-wrap gap-3">
                                            <div class="avatar-sm mx-auto mb-3">
                                                <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                                    <i class="bi bi-card-list"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <h4 class="card-title">Áreas de T.F.G</h4>
                                        <a type="button" href="{{route('areas_tesis.index')}}" class="btn btn-info">Visualizar</a>
                                    </div>
                                </div>
                            @endcan
                            @can('ver_lineas_tesis')
                                <div class="col-lg-2 col-xxl-2">
                                    <div class="card card-body text-center position-relative">
                                        <div class="d-flex flex-wrap gap-3">
                                            <div class="avatar-sm mx-auto mb-3">
                                                <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                                    <i class="bi bi-card-list"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <h4 class="card-title">Líneas de T.F.G</h4>
                                        <a type="button" href="{{route('lineas_tesis.index')}}" class="btn btn-info">Visualizar</a>
                                    </div>
                                </div>
                            @endcan
                        </div>
                        <div class="row justify-content-center">
                            @can('ver_requerimientos_entregas_tesis')
                                <div class="col-lg-2 col-xxl-2">
                                    <div class="card card-body text-center position-relative">
                                        <div class="d-flex flex-wrap gap-3">
                                            <div class="avatar-sm mx-auto mb-3">
                                                <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                                    <i class="bi bi-card-list"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <h4 class="card-title">Fechas de Entregas</h4>
                                        <a type="button" href="{{route('requerimientos_entregas_tesis.show')}}" class="btn btn-info">Visualizar</a>
                                    </div>
                                </div>
                            @endcan
                            @can('ver_bloques_anteproyectos_tesis')
                                <div class="col-lg-2 col-xxl-2">
                                    <div class="card card-body text-center position-relative">
                                        <div class="d-flex flex-wrap gap-3">
                                            <div class="avatar-sm mx-auto mb-3">
                                                <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                                    <i class="bi bi-card-list"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <h4 class="card-title">Bloques de Anteproyectos</h4>
                                        <a type="button" href="{{route('bloques_anteproyectos_tesis.index')}}" class="btn btn-info">Visualizar</a>
                                    </div>
                                </div>
                            @endcan
                            @can('ver_bloques_proyectos_tesis')
                                <div class="col-lg-2 col-xxl-2">
                                    <div class="card card-body text-center position-relative">
                                        <div class="d-flex flex-wrap gap-3">
                                            <div class="avatar-sm mx-auto mb-3">
                                                <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                                    <i class="bi bi-card-list"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <h4 class="card-title">Bloques de Proyectos</h4>
                                        <a type="button" href="{{route('bloques_proyectos_tesis.index')}}" class="btn btn-info">Visualizar</a>
                                    </div>
                                </div>
                            @endcan
                            @can('ver_bloques_borradores_tesis')
                                <div class="col-lg-2 col-xxl-2">
                                    <div class="card card-body text-center position-relative">
                                        <div class="d-flex flex-wrap gap-3">
                                            <div class="avatar-sm mx-auto mb-3">
                                                <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                                    <i class="bi bi-card-list"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <h4 class="card-title">Bloques de Borradores</h4>
                                        <a type="button" href="{{route('bloques_borradores_tesis.index')}}" class="btn btn-info">Visualizar</a>
                                    </div>
                                </div>
                            @endcan
                            @can('ver_rubricas_tesis')
                                <div class="col-lg-2 col-xxl-2">
                                    <div class="card card-body text-center position-relative">
                                        <div class="d-flex flex-wrap gap-3">
                                            <div class="avatar-sm mx-auto mb-3">
                                                <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                                    <i class="bi bi-card-list"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <h4 class="card-title">Rúbricas</h4>
                                        <a type="button" href="{{route('rubricas_tesis.index')}}" class="btn btn-info">Visualizar</a>
                                    </div>
                                </div>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endsection

    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
    @endsection
@endcan
