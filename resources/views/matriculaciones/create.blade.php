@can('crear_matriculaciones')
    @extends('layouts.master')
    @section('title') Agregar Matriculación @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Matriculaciones @endslot
            @slot('title') Agregar Matriculación  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('matriculaciones.store')}}" method="post" id="store-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Nueva matriculación</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="alumno">Alumno <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('alumno') is-invalid @enderror" id="alumno" name="alumno" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($alumnos as $alumno)
                                            <option value="{{$alumno->id}}" @if (old('alumno') == strval($alumno->id)) selected @endif data-subtext="{{$alumno->numero_documento}}">{{$alumno->primer_nombre}} {{$alumno->primer_apellido}}</option>
                                        @endforeach
                                    </select>
                                    @error('alumno')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="semestre">Semestre <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('semestre') is-invalid @enderror" id="semestre" name="semestre" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($semestres as $semestre)
                                            <option value="{{$semestre->id}}" @if (old('semestre') == strval($semestre->id)) selected @endif>{{$semestre->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('semestre')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="programa">Programa <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('programa') is-invalid @enderror" id="programa" name="programa" data-live-search="true" disabled>
                                        <option value="" selected disabled>Seleccionar...</option>

                                    </select>
                                    @error('programa')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="carrera">Carrera <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('carrera') is-invalid @enderror" id="carrera" name="carrera" data-live-search="true" disabled>
                                        <option value="" selected disabled>Seleccionar...</option>

                                    </select>
                                    @error('carrera')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3 d-none" id="div-carrera_siu">
                                    <label class="form-label" for="carrera_siu" id="label-carrera_siu">Carrera SIU</label>
                                    <select class="selectpicker form-control @error('carrera_siu') is-invalid @enderror" id="carrera_siu" name="carrera_siu" data-live-search="true" disabled>
                                        <option value="" selected disabled>Seleccionar...</option>

                                    </select>
                                    @error('carrera_siu')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 text-center">
                                    <label class="form-label">Tipo de Pago <span class="text-danger">(*)</span></label>
                                    <div class="btn-group @error('tipo_pago') is-invalid @enderror" role="group" id="div-tipo_pago">
                                        <input type="radio" class="btn-check tipo_pago" id="tipo_pago1" name="tipo_pago" value="CO" @if (old('tipo_pago') == 'CO') checked @endif>
                                        <label class="btn btn-outline-info" for="tipo_pago1">Contado</label>
                                        <input type="radio" class="btn-check tipo_pago" id="tipo_pago2" name="tipo_pago" value="CR" @if (old('tipo_pago') == 'CR') checked @endif>
                                        <label class="btn btn-outline-info" for="tipo_pago2">Crédito</label>
                                    </div>
                                    @error('tipo_pago')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <input type="hidden" id="validacion" name="validacion" value="0">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="convenio">Convenio</label>
                                    <select class="selectpicker form-control @error('convenio') is-invalid @enderror" id="convenio" name="convenio" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($convenios as $convenio)
                                            <option value="{{ $convenio->id }}" @if (old('convenio') == strval($convenio->id)) selected @endif>{{$convenio->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('convenio')
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
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('js/moment.min.js') }}"></script>
        @include('matriculaciones.scripts.create-scripts')
    @endsection
@endcan
