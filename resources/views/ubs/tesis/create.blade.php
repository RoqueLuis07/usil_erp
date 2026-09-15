@can('crear_tesis_ubs')
    @extends('layouts.master')
    @section('title') Inscripción de Tesis @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Tesis @endslot
            @slot('title') Inscripción de Tesis  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('tesis_ubs.store')}}" method="post" id="store-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Formulario de Inscripción</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-lg-2 mb-3">
                                            <label class="form-label" for="fecha">Fecha</label>
                                            <input type="text" class="form-control" id="fecha" value="{{\Carbon\Carbon::now()->format('d/m/Y')}}" readonly>
                                        </div>
                                        <div class="col-lg-5 mb-3">
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
                                        <div class="col-lg-5 mb-3">
                                            <label class="form-label" for="maestria">Maestría <span class="text-danger">(*)</span></label>
                                            <select class="selectpicker form-control @error('maestria') is-invalid @enderror" id="maestria" name="maestria" data-live-search="true">
                                                <option value="" selected disabled>Seleccionar...</option>
                                                @foreach ($maestrias as $maestria)
                                                    <option value="{{$maestria->id}}" @if (old('maestria') == strval($maestria->id)) selected @endif data-subtext="{{$maestria->programa->nombre}}">{{$maestria->nombre_fantasia}}</option>
                                                @endforeach
                                            </select>
                                            @error('maestria')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4 mb-3">
                                            <label class="form-label" for="linea">Línea <span class="text-danger">(*)</span></label>
                                            <select class="selectpicker form-control @error('linea') is-invalid @enderror" id="linea" name="linea" data-live-search="true">
                                                <option value="" selected disabled>Seleccionar...</option>
                                                @foreach ($lineas as $linea)
                                                    <option value="{{$linea->id}}" @if (old('linea') == strval($linea->id)) selected @endif>{{$linea->nombre}}</option>
                                                @endforeach
                                            </select>
                                            @error('linea')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label" for="tutor">Tutor <span class="text-danger">(*)</span></label>
                                            <select class="selectpicker form-control @error('tutor') is-invalid @enderror" id="tutor" name="tutor" data-live-search="true">
                                                <option value="" selected disabled>Seleccionar...</option>
                                                @foreach ($tutores as $tutor)
                                                    <option value="{{$tutor->id}}" @if (old('tutor') == strval($tutor->id)) selected @endif data-subtext="{{$tutor->numero_documento}}">{{$tutor->primer_nombre}} {{$tutor->segundo_nombre}} {{$tutor->tercer_nombre}} {{$tutor->primer_apellido}} {{$tutor->segundo_apellido}}</option>
                                                @endforeach
                                            </select>
                                            @error('tutor')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">

                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 mb-3">
                                            <label class="form-label" for="tema">Tema <span class="text-danger">(*)</span></label>
                                            <input type="text" class="form-control @error('tema') is-invalid @enderror" id="tema" name="tema" value="{{old('tema')}}">
                                            @error('tema')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 mb-3">
                                            <label class="form-label" for="titulo">Título <span class="text-danger">(*)</span></label>
                                            <textarea class="form-control @error('titulo') is-invalid @enderror" id="titulo" name="titulo" cols="30" rows="5">{{old('titulo')}}</textarea>
                                            @error('titulo')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="alert alert-info" role="alert">
                                        <h6 class="text-info">Observación</h6>
                                        <strong>El estudiante deberá cumplir los siguientes requerimientos para inscripción y aprobación de tema de Trabajo Final de Grado:</strong>
                                        <ol>
                                            <li>Documento de Admisión al día.</li>
                                            <li>Tutor habilitado por Calidad Educativa de Tesis USIL.</li>
                                        </ol>
                                        <h6 class="text-info">Importante:</h6>
                                        <ol>
                                            <li>Listado de Tutores: <a class="btn btn-sm btn-info" href="#"><i class="ri-download-line"></i> Tutores</a></li>
                                            <li>Instructivo para Inscripción de Tesis: <a class="btn btn-sm btn-info" href="#"><i class="ri-download-line"></i> Instructivo</a></li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
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
        @include('ubs.tesis.scripts.create-scripts')
    @endsection
@endcan
