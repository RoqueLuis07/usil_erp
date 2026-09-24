@can('crear_docentes')
    @extends('layouts.master-academic')
    @section('title') Agregar Docente @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Docentes @endslot
            @slot('title') Agregar Docente  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('docentes.store')}}" method="post" id="store-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Nuevo docente</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="primer_nombre_docente">Primer Nombre <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('primer_nombre_docente') is-invalid @enderror" id="primer_nombre_docente" name="primer_nombre_docente" value="{{old('primer_nombre_docente')}}" placeholder="Escriba el nombre">
                                    @error('primer_nombre_docente')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="segundo_nombre_docente">Segundo Nombre</label>
                                    <input type="text" class="form-control @error('segundo_nombre_docente') is-invalid @enderror" id="segundo_nombre_docente" name="segundo_nombre_docente" value="{{old('segundo_nombre_docente')}}" placeholder="Escriba el nombre">
                                    @error('segundo_nombre_docente')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="tercer_nombre_docente">Tercer Nombre</label>
                                    <input type="text" class="form-control @error('tercer_nombre_docente') is-invalid @enderror" id="tercer_nombre_docente" name="tercer_nombre_docente" value="{{old('tercer_nombre_docente')}}" placeholder="Escriba el nombre">
                                    @error('tercer_nombre_docente')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="primer_apellido_docente">Primer Apellido <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('primer_apellido_docente') is-invalid @enderror" id="primer_apellido_docente" name="primer_apellido_docente" value="{{old('primer_apellido_docente')}}" placeholder="Escriba el apellido">
                                    @error('primer_apellido_docente')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="segundo_apellido_docente">Segundo Apellido</label>
                                    <input type="text" class="form-control @error('segundo_apellido_docente') is-invalid @enderror" id="segundo_apellido_docente" name="segundo_apellido_docente" value="{{old('segundo_apellido_docente')}}" placeholder="Escriba el apellido">
                                    @error('segundo_apellido_docente')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="numero_documento">N° de Documento <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('numero_documento') is-invalid @enderror" id="numero_documento" name="numero_documento" value="{{old('numero_documento')}}" placeholder="Escriba el N° de documento">
                                    @error('numero_documento')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="sexo">Sexo <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('sexo') is-invalid @enderror" id="sexo" name="sexo">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($sexos as $sexo)
                                            <option value="{{$sexo->id}}" @if (old('sexo') == strval($sexo->id)) selected @endif>{{$sexo->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('sexo')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha_nacimiento">Fecha de Nacimiento <span class="text-danger">(*)</span></label>
                                    <div class="form-icon right">
                                        <input type="text" class="flatpickr form-control form-control-icon @error('fecha_nacimiento') is-invalid @enderror" id="fecha_nacimiento" name="fecha_nacimiento" value="{{old('fecha_nacimiento')}}" placeholder="Seleccionar...">
                                        <i class="ri-calendar-2-line" id="calendar-icon"></i>
                                        @error('fecha_nacimiento')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="telefono">N° de Teléfono</label>
                                    <input type="text" class="form-control @error('telefono') is-invalid @enderror" id="telefono" name="telefono" value="{{old('telefono')}}" placeholder="Escriba el N° de línea baja">
                                    @error('telefono')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="celular">N° de Celular <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('celular') is-invalid @enderror" id="celular" name="celular" value="{{old('celular')}}" placeholder="Escriba el N° de celular">
                                    @error('celular')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="nacionalidad">Nacionalidad <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control nacionalidad @error('nacionalidad') is-invalid @enderror" id="nacionalidad" name="nacionalidad[]" data-live-search="true" multiple title="Seleccionar...">
                                        @foreach ($nacionalidades as $nacionalidad)
                                            <option value="{{$nacionalidad->id}}" @if (old('nacionalidad') == strval($nacionalidad->id)) selected @endif>{{$nacionalidad->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('nacionalidad')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="email_personal">Correo Personal <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('email_personal') is-invalid @enderror" id="email_personal" name="email_personal" value="{{old('email_personal')}}" placeholder="Escriba el correo electrónico">
                                    @error('email_personal')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="direccion">Dirección <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('direccion') is-invalid @enderror" id="direccion" name="direccion" value="{{old('direccion')}}" placeholder="Escriba la dirección particular">
                                    @error('direccion')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="departamento">Departamento <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('departamento') is-invalid @enderror" id="departamento" name="departamento" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($departamentos_paraguay as $departamento)
                                            <option value="{{$departamento->id}}" @if (old('departamento') == strval($departamento->id)) selected @endif>{{$departamento->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('departamento')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="ciudad">Ciudad <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('ciudad') is-invalid @enderror" id="ciudad" name="ciudad" data-live-search="true" disabled>
                                        <option value="" selected disabled>Seleccionar...</option>
                                    </select>
                                    @error('ciudad')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="barrio">Barrio</label>
                                    <select class="selectpicker form-control @error('barrio') is-invalid @enderror" id="barrio" name="barrio" data-live-search="true" disabled>
                                        <option value="" selected disabled>Seleccionar...</option>
                                    </select>
                                    @error('barrio')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="usuario">Usuario Asignado</label>
                                    <select class="selectpicker form-control @error('usuario') is-invalid @enderror" id="usuario" name="usuario">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($usuarios as $usuario)
                                            <option value="{{$usuario->id}}" @if (old('usuario') == strval($usuario->id)) selected @endif data-subtext="{{$usuario->rol->name}}">{{$usuario->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('usuario')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3 text-center">
                                    <div>
                                        <label class="form-label" for="tutor_tesis">Tutor T.F.G. <span class="text-danger">(*)</span></label>
                                    </div>
                                    <div class="btn-group @error('tutor_tesis') is-invalid @enderror" role="group">
                                        <input type="radio" class="btn-check tutor_tesis1" id="tutor_tesis1" name="tutor_tesis" value="false" @if (old('tutor_tesis') == 'false') checked @endif>
                                        <label class="btn btn-outline-danger" for="tutor_tesis1">No</label>
                                        <input type="radio" class="btn-check tutor_tesis2" id="tutor_tesis2" name="tutor_tesis" value="true" @if (old('tutor_tesis') == 'true') checked @endif>
                                        <label class="btn btn-outline-success" for="tutor_tesis2">Si</label>
                                    </div>
                                    @error('tutor_tesis')
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
                                <div class="card-body">
                                    @include('docentes.partials.carreras')
                                    <ul class="nav nav-pills arrow-navtabs nav-info nav-justified bg-light gap-2 mb-4" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link active align-middle" data-bs-toggle="tab" href="#tablistFormacion" role="tab" aria-selected="true">
                                                Formación Educativa
                                                <span class="badge bg-danger">Req.</span>
                                            </a>
                                        </li>
                                    </ul>
                                    {{-- Tab panes --}}
                                    <div class="tab-content">
                                        <div class="tab-pane active show" id="tablistFormacion" role="tabpanel">
                                            <div class="row">
                                                <div class="col-lg-4 mb-3">
                                                    <label class="form-label" for="nivel_academico">Nivel Académico <span class="text-danger">(*)</span></label>
                                                    <select class="selectpicker form-control @error('nivel_academico') is-invalid @enderror" id="nivel_academico" name="nivel_academico" data-live-search="true">
                                                        <option value="" selected disabled>Seleccionar...</option>
                                                        @foreach ($niveles_academicos as $nivel_academico)
                                                            <option value="{{$nivel_academico->id}}" @if (old('nivel_academico') == strval($nivel_academico->id)) selected @endif>{{$nivel_academico->nombre}}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('nivel_academico')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-4 mb-3">
                                                    <label class="form-label" for="area_conocimiento">Área de Conocimiento <span class="text-danger">(*)</span></label>
                                                    <select class="selectpicker form-control @error('area_conocimiento') is-invalid @enderror" id="area_conocimiento" name="area_conocimiento" data-live-search="true">
                                                        <option value="" selected disabled>Seleccionar...</option>
                                                        @foreach ($areas_conocimientos as $area_conocimiento)
                                                            <option value="{{$area_conocimiento->id}}" @if (old('area_conocimiento') == strval($area_conocimiento->id)) selected @endif data-subtext="{{ $area_conocimiento->abreviatura }}">{{$area_conocimiento->nombre}}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('area_conocimiento')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-4 mb-3">
                                                    <label class="form-label" for="capacitacion_didactica">Capacitación Didáctica ? <span class="text-danger">(*)</span></label>
                                                    <br>
                                                    <div class="btn-group @error('capacitacion_didactica') is-invalid @enderror" role="group">
                                                        <input type="radio" class="btn-check" id="capacitacion_didactica1" name="capacitacion_didactica" value="false" @if (old('capacitacion_didactica') == 'false') checked @endif>
                                                        <label class="btn btn-outline-danger" for="capacitacion_didactica1">No</label>
                                                        <input type="radio" class="btn-check" id="capacitacion_didactica2" name="capacitacion_didactica" value="true" @if (old('capacitacion_didactica') == 'true') checked @endif>
                                                        <label class="btn btn-outline-success" for="capacitacion_didactica2">Sí</label>
                                                    </div>
                                                    @error('capacitacion_didactica')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- /Tab panes --}}
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
        @include('docentes.scripts.create-scripts')
    @endsection
@endcan
