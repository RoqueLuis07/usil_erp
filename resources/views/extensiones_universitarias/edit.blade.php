@can('editar_extensiones_universitarias')
    @extends('layouts.master')
    @section('title') Editar Extensión Universitaria @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Extensión Universitarias @endslot
            @slot('title') Editar Extensión Universitaria  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('extensiones_universitarias.update', $extension->id)}}" method="post" id="update-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Actualizar extensión universitaria</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-9 mb-3">
                                    <label class="form-label" for="nombre_proyecto">Nombre del Proyecto <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('nombre_proyecto') is-invalid @enderror" id="nombre_proyecto" name="nombre_proyecto" value="{{old('nombre_proyecto', $extension->nombre)}}">
                                    @error('nombre_proyecto')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="docente">Responsable <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('docente') is-invalid @enderror" id="docente" name="docente" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($docentes as $docente)
                                            <option value="{{$docente->id}}" @if (old('docente') == strval($docente->id) || $extension->docente_id == strval ($docente->id)) selected @endif data-subtext="{{$docente->numero_documento}}">{{$docente->primer_nombre}} {{$docente->primer_apellido}}</option>
                                        @endforeach
                                    </select>
                                    @error('docente')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="tipo_extension">Tipo de Actividad <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('tipo_extension') is-invalid @enderror" id="tipo_extension" name="tipo_extension">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($tipos_extensiones as $tipo_extension)
                                            <option value="{{$tipo_extension->id}}" @if (old('tipo_extension') == strval($tipo_extension->id) || $extension->tipo_extension_id == strval ($tipo_extension->id)) selected @endif>{{$tipo_extension->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('tipo_extension')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="cantidad_horas_proyecto">Horas del Proyecto <span class="text-danger">(*)</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control text-center @error('cantidad_horas_proyecto') is-invalid @enderror" id="cantidad_horas_proyecto" name="cantidad_horas_proyecto" value="{{old('cantidad_horas_proyecto', number_format($extension->cantidad_horas, 2, ',', '.'))}}">
                                        <span class="input-group-text">horas</span>
                                        @error('cantidad_horas_proyecto')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
							<div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha_inicio">Fecha de Inicio <span class="text-danger">(*)</span></label>
                                    <div class="form-icon right">
                                        <input type="text" class="flatpickr form-control form-control-icon @error('fecha_inicio') is-invalid @enderror" id="fecha_inicio" name="fecha_inicio" value="{{old('fecha_inicio', $extension->fecha_inicio)}}" placeholder="Seleccionar...">
                                        <i class="ri-calendar-2-line" id="calendar-icon"></i>
                                        @error('fecha_inicio')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha_fin">Fecha de Fin <span class="text-danger">(*)</span></label>
                                    <div class="form-icon right">
                                        <input type="text" class="flatpickr form-control form-control-icon @error('fecha_fin') is-invalid @enderror" id="fecha_fin" name="fecha_fin" value="{{old('fecha_fin', $extension->fecha_fin)}}" placeholder="Seleccionar...">
                                        <i class="ri-calendar-2-line" id="calendar-icon"></i>
                                        @error('fecha_fin')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <div>
                                        <label class="form-label" for="tiene_certificado">Certificado ? <span class="text-danger">(*)</span></label>
                                    </div>
                                    <div class="btn-group @error('tiene_certificado') is-invalid @enderror" role="group">
                                        <input type="radio" class="btn-check tiene_certificado1" id="tiene_certificado1" name="tiene_certificado" value="false" @if (old('tiene_certificado') == 'false' || $extension->tiene_certificado == false) checked @endif>
                                        <label class="btn btn-outline-danger" for="tiene_certificado1">No</label>
                                        <input type="radio" class="btn-check tiene_certificado2" id="tiene_certificado2" name="tiene_certificado" value="true" @if (old('tiene_certificado') == 'true' || $extension->tiene_certificado == true) checked @endif>
                                        <label class="btn btn-outline-success" for="tiene_certificado2">Si</label>
                                    </div>
                                    @error('tiene_certificado')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Alumnos Participantes</h4>
                                </div>
                                <div class="card-body">
                                    @foreach ($extension->extensionUniversitariaDetalles as $key => $detalle)
                                        <div class="mb-2 fila" id="fila-{{$key}}">
                                            <div class="row d-flex flex-wrap justify-content-center">
                                                <div class="col-lg-4 col-sm-12 mb-2 text-center" id="div-alumno-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-alumno">Alumno <span class="text-danger">(*)</span></label> @endif
                                                    <select class="selectpicker form-control alumno-{{$key}} alumno @error('detalles.'. $key . '.alumno') is-invalid @enderror" id="alumno-{{$key}}" name="detalles[{{$key}}][alumno]" data-live-search="true" data-id="{{$key}}">
                                                        <option value="" selected disabled>Seleccionar...</option>
                                                        @foreach ($alumnos as $alumno)
                                                            <option value="{{$alumno->id}}" @if (old('detalles.{{$key}}.alumno') == strval($alumno->id) || $detalle->alumno_id == strval($alumno->id)) selected @endif data-subtext="{{$alumno->numero_documento}}">{{$alumno->primer_nombre}} {{$alumno->primer_apellido}}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('detalles.'. $key . '.alumno')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 col-sm-2 text-center">
                                                    @if ($key == 0) <label class="form-label label-acciones">Acciones</label> @endif
                                                    <div class="align-middle" id="acciones-{{$key}}">
                                                        @if ($key == 0 && !$loop->last)
                                                            <button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-{{$key}}" data-id="{{$key}}"><i class="ri-subtract-fill"></i></button> {{-- primero y no ultimo --}}
                                                        @elseif ($key > 0 && !$loop->last)
                                                            <button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-{{$key}}" data-id="{{$key}}"><i class="ri-subtract-fill"></i></button>  {{-- no primero y no ultimo --}}
                                                        @elseif ($key > 0 && $loop->last)
                                                            <button type="button" class="btn btn-icon btn-danger btn-erase" id="btn-erase-{{$key}}" data-id="{{$key}}"><i class="ri-subtract-fill"></i></button> {{-- no primero y ultimo --}}
                                                            <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-{{$key}}" data-id="{{$key}}"><i class="ri-add-fill"></i></button>
                                                        @elseif ($key == 0 && $loop->last)
                                                            <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-{{$key}}" data-id="{{$key}}"><i class="ri-add-fill"></i></button> {{-- primero y ultimo --}}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div id="alumno-fila">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <button type="button" class="btn btn-info me-2" id="clean-btn">Vaciar</button>
                            <button type="button" class="btn btn-danger me-2" id="cancel-btn">Cancelar</button>
                            <button type="button" class="btn btn-success" id="update-btn">Actualizar</button>
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
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        @include('extensiones_universitarias.scripts.edit-scripts')
        @include('extensiones_universitarias.scripts.edit-detalles-scripts')
    @endsection
@endcan
