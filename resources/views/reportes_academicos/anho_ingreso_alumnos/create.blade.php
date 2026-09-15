@can('ver_reportes_academicos_alumnos_ingresos')
    @extends('layouts.master')
    @section('title') Reportes Académicos @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Reportes Académicos @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('reportes_academicos.pdf_alumnos_ingresos')}}" method="get" target="_blank" id="store-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Generar Reporte de Año de Ingreso de Alumnos</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row d-flex flex-wrap justify-content-center">
                                <div class="col-lg-2 mb-3 me-3 text-center">
                                    <label class="form-label" for="semestre_inicio">Período Inicio <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('semestre_inicio') is-invalid @enderror" id="semestre_inicio" name="semestre_inicio" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($semestres as $semestre)
                                            <option value="{{ $semestre->id }}" @if (old('semestre_inicio') == strval($semestre->id)) selected @endif>{{$semestre->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('semestre_inicio')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3 me-3 text-center">
                                    <label class="form-label" for="semestre_fin">Período Fin <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('semestre_fin') is-invalid @enderror" id="semestre_fin" name="semestre_fin">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($semestres as $semestre)
                                            <option value="{{ $semestre->id }}" @if (old('semestre_fin') == strval($semestre->id)) selected @endif>{{$semestre->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('semestre_fin')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <button type="button" class="btn btn-danger me-2" id="cancel-btn">Cancelar</button>
                            <button type="button" class="btn btn-success" id="generate-btn">Generar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        @include('reportes_academicos.anho_ingreso_alumnos.scripts.create-scripts')
    @endsection
@endcan
