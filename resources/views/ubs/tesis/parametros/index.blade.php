@can('ver_parametros_tesis_ubs')
    @extends('layouts.master')
    @section('title') Parámetros de Tesis @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Paramétros de Tesis @endslot
        @endcomponent

        <div class="row mt-5">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Parámetros de Tesis</h4>
                    </div>
                    <div class="card-body">
                        <div class="row justify-content-center">
                            @can('ver_fechas_defensas_tesis_ubs')
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
                                        <a type="button" href="{{route('tesis_parametros_ubs.fechas_defensas_index')}}" class="btn btn-info">Visualizar</a>
                                    </div>
                                </div>
                            @endcan
                            @can('ver_lineas_tesis_ubs')
                                <div class="col-lg-2 col-xxl-2">
                                    <div class="card card-body text-center position-relative">
                                        <div class="d-flex flex-wrap gap-3">
                                            <div class="avatar-sm mx-auto mb-3">
                                                <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                                    <i class="bi bi-card-list"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <h4 class="card-title">Líneas de Tesis</h4>
                                        <a type="button" href="{{route('tesis_parametros_ubs.lineas_index')}}" class="btn btn-info">Visualizar</a>
                                    </div>
                                </div>
                            @endcan
                            @can('ver_bloques_anteproyectos_tesis_ubs')
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
                                        <a type="button" href="{{route('tesis_parametros_ubs.bloques_anteproyectos_index')}}" class="btn btn-info">Visualizar</a>
                                    </div>
                                </div>
                            @endcan
                            @can('ver_bloques_borradores_tesis_ubs')
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
                                        <a type="button" href="{{route('tesis_parametros_ubs.bloques_borradores_index')}}" class="btn btn-info">Visualizar</a>
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
