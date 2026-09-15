@can('crear_evaluaciones')
    @extends('layouts.master')
    @section('title') Agregar Evaluación @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Evaluaciones @endslot
            @slot('title') Agregar Evaluación  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('evaluaciones.store')}}" method="post" id="store-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Nueva evaluación</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="nombre_evaluacion">Nombre de la Evaluación <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('nombre_evaluacion') is-invalid @enderror" id="nombre_evaluacion" name="nombre_evaluacion" value="{{old('nombre_evaluacion')}}">
                                    @error('nombre_evaluacion')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="tipo_evaluacion">Tipo de Evaluación <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('tipo_evaluacion') is-invalid @enderror" id="tipo_evaluacion" name="tipo_evaluacion">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($tipos_evaluaciones as $tipo_evaluacion)
                                            <option value="{{$tipo_evaluacion->id}}" @if (old('tipo_evaluacion') == strval($tipo_evaluacion->id)) selected @endif>{{$tipo_evaluacion->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('tipo_evaluacion')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="puntos">Puntos Posibles <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control text-center @error('puntos') is-invalid @enderror" id="puntos" name="puntos" placeholder="100" value="{{old('puntos')}}">
                                    @error('puntos')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="valor_porcentual">Valor Porcentual <span class="text-danger">(*)</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control text-center @error('valor_porcentual') is-invalid @enderror" id="valor_porcentual" name="valor_porcentual" placeholder="60" value="{{old('valor_porcentual')}}">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    @error('valor_porcentual')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="puntaje_minimo_requerido">Puntaje Min. Req. <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control text-center @error('puntaje_minimo_requerido') is-invalid @enderror" id="puntaje_minimo_requerido" name="puntaje_minimo_requerido" placeholder="0" value="{{old('puntaje_minimo_requerido')}}">
                                    @error('puntaje_minimo_requerido')
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
                            <button type="button" class="btn btn-info me-2" id="clean-btn">Vaciar</button>
                            <button type="button" class="btn btn-danger me-2" id="cancel-btn">Cancelar</button>
                            <button type="button" class="btn btn-success" id="save-btn">Guardar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="{{ URL::asset('js/flatpickr.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        @include('evaluaciones.scripts.create-scripts')
    @endsection
@endcan
