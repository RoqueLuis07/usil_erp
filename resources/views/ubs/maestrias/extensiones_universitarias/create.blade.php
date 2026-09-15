@can('crear_extensiones_ubs')
    @extends('layouts.master')
    @section('title') Agregar Extensiones Universitarias @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
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
            <form action="{{route('extensiones_universitarias_ubs.store')}}" method="post" id="store-form" enctype="multipart/form-data">
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
                                @unlessrole('ALUMNO')
                                    <div class="col-lg-3 mb-3">
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
                                        <a class="btn btn-secondary" href="{{asset('storage/maestrias/extensiones_universitarias/plantillas/propuesta-proyecto.xlsx')}}" download="propuesta-proyecto.xlsx"><i class="ri-download-line align-bottom mb-0 me-2"></i> Descargar</a>
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
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        @include('ubs.maestrias.extensiones_universitarias.scripts.create-scripts')
    @endsection
@endcan
