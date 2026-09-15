@can('editar_inscripciones_tesis')
    @extends('layouts.master')
    @section('title') Editar Tema de Trabajo Final de Grado @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Temas de Trabajo Final de Grado @endslot
            @slot('title') Editar Tema de Trabajo Final de Grado  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('inscripciones_temas_tesis.update', $inscripcion->id)}}" method="post" id="update-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Actualizar tema de trabajo final de grado</h4>
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
                                            <label class="form-label" for="alumno">Alumno</label>
                                            <input type="text" class="form-control" id="alumno" value="{{$inscripcion->alumno->primer_nombre}} {{$inscripcion->alumno->primer_apellido}}" readonly>
                                            <input type="hidden" name="alumno" value="{{$inscripcion->alumno_id}}">
                                        </div>
                                        <div class="col-lg-5 mb-3">
                                            <label class="form-label" for="carrera">Carrera <span class="text-danger">(*)</span></label>
                                            <select class="selectpicker form-control @error('carrera') is-invalid @enderror" id="carrera" name="carrera" data-live-search="true">
                                                <option value="" selected disabled>Seleccionar...</option>
                                                @foreach ($carreras as $carrera)
                                                    <option value="{{$carrera->id}}" @if (old('carrera') == strval($carrera->id) || $inscripcion->carrera_id == strval($carrera->id)) selected @endif data-subtext="{{$carrera->programa->nombre}}">{{$carrera->nombre_fantasia}}</option>
                                                @endforeach
                                            </select>
                                            @error('carrera')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4 mb-3">
                                            <label class="form-label" for="tipo">Tipo <span class="text-danger">(*)</span></label>
                                            <select class="selectpicker form-control @error('tipo') is-invalid @enderror" id="tipo" name="tipo" data-live-search="true">
                                                <option value="" selected disabled>Seleccionar...</option>
                                                @foreach ($tipos as $tipo)
                                                    <option value="{{$tipo->id}}" @if (old('tipo') == strval($tipo->id) || $inscripcion->tipo_id == strval($tipo->id)) selected @endif>{{$tipo->nombre}}</option>
                                                @endforeach
                                            </select>
                                            @error('tipo')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-4 mb-3">
                                            <label class="form-label" for="area">Área <span class="text-danger">(*)</span></label>
                                            <select class="selectpicker form-control @error('area') is-invalid @enderror" id="area" name="area" data-live-search="true">
                                                <option value="" selected disabled>Seleccionar...</option>
                                                @foreach ($areas as $area)
                                                    <option value="{{$area->id}}" @if (old('area') == strval($area->id) || $inscripcion->area_id == strval($area->id)) selected @endif>{{$area->nombre}}</option>
                                                @endforeach
                                            </select>
                                            @error('area')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-4 mb-3">
                                            <label class="form-label" for="linea">Línea <span class="text-danger">(*)</span></label>
                                            <select class="selectpicker form-control @error('linea') is-invalid @enderror" id="linea" name="linea" data-live-search="true">
                                                <option value="" selected disabled>Seleccionar...</option>
                                                @foreach ($lineas as $linea)
                                                    <option value="{{$linea->id}}" @if (old('linea') == strval($linea->id) || $inscripcion->linea_id == $linea->id) selected @endif>{{$linea->nombre}}</option>
                                                @endforeach
                                            </select>
                                            @error('linea')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label" for="tutor">Tutor <span class="text-danger">(*)</span></label>
                                            <select class="selectpicker form-control @error('tutor') is-invalid @enderror" id="tutor" name="tutor" data-live-search="true">
                                                <option value="" selected disabled>Seleccionar...</option>
                                                @foreach ($tutores as $tutor)
                                                    <option value="{{$tutor->id}}" @if (old('tutor') == strval($tutor->id) || $inscripcion->tutor_id == strval($tutor->id)) selected @endif>{{$tutor->primer_nombre}} {{$tutor->segundo_nombre}} {{$tutor->tercer_nombre}} {{$tutor->primer_apellido}} {{$tutor->segundo_apellido}}</option>
                                                @endforeach
                                            </select>
                                            @error('tutor')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label" for="lugar_investigacion">Lugar de Investigación</label>
                                            <input type="text" class="form-control @error('lugar_investigacion') is-invalid @enderror" id="lugar_investigacion" name="lugar_investigacion" value="{{old('lugar_investigacion', $inscripcion->lugar_investigacion)}}">
                                            @error('lugar_investigacion')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 mb-3">
                                            <label class="form-label" for="tema">Tema</label>
                                            <input type="text" class="form-control @error('tema') is-invalid @enderror" id="tema" name="tema" value="{{old('tema', $inscripcion->tema)}}">
                                            @error('tema')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 mb-3">
                                            <label class="form-label" for="justificacion">Importancia o Justificación <span class="text-danger">(*)</span></label>
                                            <textarea class="form-control @error('justificacion') is-invalid @enderror" id="justificacion" name="justificacion" cols="30" rows="5">{{old('justificacion', $inscripcion->justificacion)}}</textarea>
                                            @error('justificacion')
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
                                            <li>Tutor habilitado por la Coordinación de Trabajo Final de Grado USIL.</li>
                                        </ol>
                                        <h6 class="text-info">Importante:</h6>
                                        <ol>
                                            <li>Listado de Tutores: <a class="btn btn-sm btn-info" href="#"><i class="ri-download-line"></i> Tutores</a></li>
                                            <li>Instructivo para Inscripción de Temas de Trabajo Final de Grado: <a class="btn btn-sm btn-info" href="#"><i class="ri-download-line"></i> Instructivo</a></li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <button type="button" class="btn btn-danger me-2" id="cancel-btn">Cancelar</button>
                            <button type="button" class="btn btn-success" id="update-btn" data-id="{{$inscripcion->id}}">Actualizar</button>
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
        @include('tesis.inscripciones_temas.scripts.edit-scripts')
    @endsection
@endcan
