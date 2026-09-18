@can('crear_extensiones_universitarias')
    @extends('layouts.master-academic')
    @section('title') Agregar Extensiones Universitarias @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
		<link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
        <style>
            .dropdown-menu-plantillas {
                background-color: #3C80E6!important;
            }
            .dropdown-item-plantillas {
                color: white!important;
            }
            .dropdown-item-plantillas:hover {
                background-color: #9EC4FE!important;
                color: black!important;
            }
        </style>
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Extensiones Universitarias @endslot
            @slot('title') Agregar Extensiones Universitarias  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('extensiones_universitarias.store')}}" method="post" id="store-form" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="generado_docente" value="NO">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Nueva extensión universitaria</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-9 mb-3">
                                    <label class="form-label" for="nombre_proyecto">Nombre del Proyecto <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('nombre_proyecto') is-invalid @enderror" id="nombre_proyecto" name="nombre_proyecto" value="{{old('nombre_proyecto')}}">
                                    @error('nombre_proyecto')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                @unlessrole('ENCARGADO_DOCENTE')
                                    <div class="col-lg-3 mb-3">
                                        <label class="form-label" for="docente">Responsable <span class="text-danger">(*)</span></label>
                                        <select class="selectpicker form-control @error('docente') is-invalid @enderror" id="docente" name="docente" data-live-search="true">
                                            <option value="" selected disabled>Seleccionar...</option>
                                            @foreach ($docentes as $docente)
                                                <option value="{{$docente->id}}" @if (old('docente') == strval($docente->id)) selected @endif data-subtext="{{$docente->numero_documento}}">{{$docente->primer_nombre}} {{$docente->primer_apellido}}</option>
                                            @endforeach
                                        </select>
                                        @error('docente')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                @endunlessrole
                            </div>
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="tipo_extension">Tipo de Actividad <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('tipo_extension') is-invalid @enderror" id="tipo_extension" name="tipo_extension">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($tipos_extensiones as $tipo_extension)
                                            <option value="{{$tipo_extension->id}}" @if (old('tipo_extension') == strval($tipo_extension->id)) selected @endif>{{$tipo_extension->nombre}}</option>
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
                                        <input type="text" class="form-control text-center @error('cantidad_horas_proyecto') is-invalid @enderror" id="cantidad_horas_proyecto" name="cantidad_horas_proyecto" value="{{old('cantidad_horas_proyecto')}}">
                                        <span class="input-group-text">horas</span>
                                        @error('cantidad_horas_proyecto')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-4 mb-3" id="div-proyecto">
                                    <label class="form-label" for="proyecto">Proyecto <span class="text-danger">(*)</span></label>
                                    <div class="input-group custom-file-button">
                                        <input type="file" class="form-control @error('proyecto') is-invalid @enderror" id="proyecto" name="proyecto" accept="application/pdf">
                                        <button type="button" class="btn btn-outline-danger" id="eliminar-proyecto" disabled><i class="ri-delete-bin-fill align-bottom me-2"></i>Eliminar Archivo</button>
                                        @error('proyecto')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <p class="text-muted" style="font-size: 14px">Se aceptan archivos del tipo <code>.pdf</code> y con un tamaño máximo de <code>5mb</code>.</p>
                                </div>
                                <div class="col-lg-2 mb-3 text-center">
                                    <label class="form-label" for="plantilla-proyecto">Plantilla de Ejemplo</label>
                                    <div>
                                        <a class="btn btn-secondary" href="{{asset('storage/extensiones_universitarias/plantillas/propuesta-proyecto.xlsx')}}" download="propuesta-proyecto.docx"><i class="ri-download-line align-bottom mb-0 me-2"></i> Descargar</a>
                                    </div>
                                </div>
                            </div>
							<div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha_inicio">Fecha de Inicio <span class="text-danger">(*)</span></label>
                                    <div class="form-icon right">
                                        <input type="text" class="flatpickr form-control form-control-icon @error('fecha_inicio') is-invalid @enderror" id="fecha_inicio" name="fecha_inicio" value="{{old('fecha_inicio')}}" placeholder="Seleccionar...">
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
                                        <input type="text" class="flatpickr form-control form-control-icon @error('fecha_fin') is-invalid @enderror" id="fecha_fin" name="fecha_fin" value="{{old('fecha_fin')}}" placeholder="Seleccionar...">
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
                                        <input type="radio" class="btn-check tiene_certificado1" id="tiene_certificado1" name="tiene_certificado" value="false" @if (old('tiene_certificado') == 'false') checked @endif>
                                        <label class="btn btn-outline-danger" for="tiene_certificado1">No</label>
                                        <input type="radio" class="btn-check tiene_certificado2" id="tiene_certificado2" name="tiene_certificado" value="true" @if (old('tiene_certificado') == 'true') checked @endif>
                                        <label class="btn btn-outline-success" for="tiene_certificado2">Si</label>
                                    </div>
                                    @error('tiene_certificado')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="cupo_maximo">Cupo Máximo</label>
                                    <input type="text" class="form-control text-center @error('cupo_maximo') is-invalid @enderror" id="cupo_maximo" name="cupo_maximo" value="{{old('cupo_maximo')}}" placeholder="Sin límite">
                                    @error('cupo_maximo')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="carreras_habilitadas">Carreras Habilitadas para Postularse</label>
                                    <select class="selectpicker form-control" id="carreras_habilitadas" name="carreras_habilitadas[]" multiple data-live-search="true" title="Todas las carreras">
                                        @foreach ($carreras as $carrera)
                                            <option value="{{$carrera->id}}" @if (collect(old('carreras_habilitadas'))->contains($carrera->id)) selected @endif>{{$carrera->nombre_fantasia}}</option>
                                        @endforeach
                                    </select>
                                    <p class="text-muted" style="font-size: 12px">Sin seleccionar ninguna, el proyecto queda abierto a postulación de alumnos de cualquier carrera.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Alumnos Participantes</h4>
                                    <p class="text-muted mb-0" style="font-size: 13px">Alta directa (sin pasar por postulación). Una vez aprobado el proyecto, también podés dejar que los alumnos se postulen desde su portal.</p>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2 fila" id="fila-0">
                                        <div class="row d-flex flex-wrap justify-content-center">
                                            <div class="col-lg-4 col-sm-12 mb-2 text-center" id="div-alumno-0">
                                                <label class="form-label label-alumno">Alumno <span class="text-danger">(*)</span></label>
                                                <select class="selectpicker form-control alumno-0 alumno @error('detalles.0.alumno') is-invalid @enderror" id="alumno-0" name="detalles[0][alumno]" data-live-search="true" data-id="0">
                                                    <option value="" selected disabled>Seleccionar...</option>
                                                    @foreach ($alumnos as $alumno)
                                                        <option value="{{$alumno->id}}" @if (old('detalles.0.alumno') == strval($alumno->id)) selected @endif data-subtext="{{$alumno->numero_documento}}">{{$alumno->primer_nombre}} {{$alumno->primer_apellido}}</option>
                                                    @endforeach
                                                </select>
                                                @error('detalles.0.alumno')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 col-sm-2 text-center">
                                                <label class="form-label label-acciones">Acciones</label>
                                                <div class="align-middle" id="acciones-0">
                                                    <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-0" data-id="0"><i class="ri-add-fill"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
		<script src="{{ URL::asset('js/flatpickr.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        @include('extensiones_universitarias.scripts.create-scripts')
        @include('extensiones_universitarias.scripts.create-detalles-scripts')
    @endsection
@endcan
