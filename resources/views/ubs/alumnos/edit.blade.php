@can('editar_alumnos_ubs')
    @extends('layouts.master')
    @section('title') Editar Alumno @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Alumnos @endslot
            @slot('title') Editar Alumno  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('alumnos.update', $alumno->id)}}" method="post" id="update-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Actualizar alumno</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="primer_nombre_alumno">Primer Nombre <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('primer_nombre_alumno') is-invalid @enderror" id="primer_nombre_alumno" name="primer_nombre_alumno" value="{{old('primer_nombre_alumno', $alumno->primer_nombre)}}" placeholder="Escriba el nombre">
                                    @error('primer_nombre_alumno')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="segundo_nombre_alumno">Segundo Nombre</label>
                                    <input type="text" class="form-control @error('segundo_nombre_alumno') is-invalid @enderror" id="segundo_nombre_alumno" name="segundo_nombre_alumno" value="{{old('segundo_nombre_alumno', $alumno->segundo_nombre)}}" placeholder="Escriba el nombre">
                                    @error('segundo_nombre_alumno')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="tercer_nombre_alumno">Tercer Nombre</label>
                                    <input type="text" class="form-control @error('tercer_nombre_alumno') is-invalid @enderror" id="tercer_nombre_alumno" name="tercer_nombre_alumno" value="{{old('tercer_nombre_alumno', $alumno->tercer_nombre)}}" placeholder="Escriba el nombre">
                                    @error('tercer_nombre_alumno')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="primer_apellido_alumno">Primer Apellido <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('primer_apellido_alumno') is-invalid @enderror" id="primer_apellido_alumno" name="primer_apellido_alumno" value="{{old('primer_apellido_alumno', $alumno->primer_apellido)}}" placeholder="Escriba el apellido">
                                    @error('primer_apellido_alumno')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="segundo_apellido_alumno">Segundo Apellido</label>
                                    <input type="text" class="form-control @error('segundo_apellido_alumno') is-invalid @enderror" id="segundo_apellido_alumno" name="segundo_apellido_alumno" value="{{old('segundo_apellido_alumno', $alumno->segundo_apellido)}}" placeholder="Escriba el apellido">
                                    @error('segundo_apellido_alumno')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="numero_documento">N° de Documento <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('numero_documento') is-invalid @enderror" id="numero_documento" name="numero_documento" value="{{old('numero_documento', $alumno->numero_documento)}}" placeholder="Escriba el N° de documento">
                                    @error('numero_documento')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="nacionalidad">Nacionalidad {{-- <span class="text-danger">(*)</span> --}}</label>
                                    <select class="form-control @error('nacionalidad') is-invalid @enderror" id="nacionalidad" name="nacionalidad[]" data-live-search="true" multiple title="Seleccionar...">

                                    </select>
                                    @error('nacionalidad')
                                        <span class="invalid-feedback" role="alert" id="error-select">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="sexo">Sexo {{-- <span class="text-danger">(*)</span> --}}</label>
                                    <select class="selectpicker form-control @error('sexo') is-invalid @enderror" id="sexo" name="sexo">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($sexos as $sexo)
                                            <option value="{{$sexo->id}}" @if (old('sexo') == strval($sexo->id) || $alumno->sexo_id == strval($sexo->id)) selected @endif>{{$sexo->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('sexo')
                                        <span class="invalid-feedback" role="alert" id="error-select">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha_nacimiento">Fecha de Nacimiento {{-- <span class="text-danger">(*)</span> --}}</label>
                                    <div class="form-icon right">
                                        <input type="text" class="flatpickr form-control form-control-icon @error('fecha_nacimiento') is-invalid @enderror" id="fecha_nacimiento" name="fecha_nacimiento" value="{{old('fecha_nacimiento', $alumno->fecha_nacimiento)}}" placeholder="Seleccionar...">
                                        <i class="ri-calendar-2-line" id="calendar-icon"></i>
                                        @error('fecha_nacimiento')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="email_personal">Correo Personal <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('email_personal') is-invalid @enderror" id="email_personal" name="email_personal" value="{{old('email_personal', $alumno->email_personal)}}" placeholder="Escriba el correo electrónico">
                                    @error('email_personal')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="email_institucional">Correo Institucional</label>
                                    <input type="text" class="form-control @error('email_institucional') is-invalid @enderror" id="email_institucional" name="email_institucional" value="{{old('email_institucional', $alumno->email_institucional)}}" placeholder="Escriba el correo electrónico">
                                    @error('email_institucional')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="telefono">N° de Teléfono</label>
                                    <input type="text" class="form-control @error('telefono') is-invalid @enderror" id="telefono" name="telefono" value="{{old('telefono', $alumno->telefono)}}" placeholder="Escriba el N° de línea baja">
                                    @error('telefono')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="celular">N° de Celular <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('celular') is-invalid @enderror" id="celular" name="celular" value="{{old('celular', $alumno->celular)}}" placeholder="Escriba el N° de celular">
                                    @error('celular')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="direccion">Dirección {{-- <span class="text-danger">(*)</span> --}}</label>
                                    <input type="text" class="form-control @error('direccion') is-invalid @enderror" id="direccion" name="direccion" value="{{old('direccion', $alumno->direccion)}}" placeholder="Escriba la dirección particular">
                                    @error('direccion')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="departamento">Departamento {{-- <span class="text-danger">(*)</span> --}}</label>
                                    <select class="selectpicker form-control @error('departamento') is-invalid @enderror" id="departamento" name="departamento" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($departamentos_paraguay as $departamento)
                                            <option value="{{$departamento->id}}" @if (old('departamento') == strval($departamento->id) || $alumno->departamento_id == strval($departamento->id)) selected @endif>{{$departamento->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('departamento')
                                        <span class="invalid-feedback" role="alert" id="error-select">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="ciudad">Ciudad {{-- <span class="text-danger">(*)</span> --}}</label>
                                    <input type="hidden" id="ciudad_input" value="{{$alumno->ciudad_id}}">
                                    <select class="selectpicker form-control @error('ciudad') is-invalid @enderror" id="ciudad" name="ciudad" data-live-search="true" disabled>
                                        <option value="" selected disabled>Seleccionar...</option>
                                    </select>
                                    @error('ciudad')
                                        <span class="invalid-feedback" role="alert" id="error-select">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="barrio">Barrio {{-- <span class="text-danger">(*)</span> --}}</label>
                                    <input type="hidden" id="barrio_input" value="{{$alumno->barrio_id}}">
                                    <select class="selectpicker form-control @error('barrio') is-invalid @enderror" id="barrio" name="barrio" data-live-search="true" disabled>
                                        <option value="" selected disabled>Seleccionar...</option>
                                    </select>
                                    @error('barrio')
                                        <span class="invalid-feedback" role="alert" id="error-select">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="forma_conocimiento">Forma de Conocer USIL</label>
                                    <select class="selectpicker form-control @error('forma_conocimiento') is-invalid @enderror" id="forma_conocimiento" name="forma_conocimiento" value="{{old('forma_conocimiento')}}" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($formas_conocimientos as $forma_conocimiento)
                                            <option value="{{$forma_conocimiento->id}}" @if (old('forma_conocimiento') == strval($forma_conocimiento->id) || $alumno->forma_conocimiento_id == strval($forma_conocimiento->id)) selected @endif>{{$forma_conocimiento->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('forma_conocimiento')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="link_crm">Link CRM</label>
                                    <input type="text" class="form-control @error('link_crm') is-invalid @enderror" id="link_crm" name="link_crm" value="{{old('link_crm', $alumno->link_crm)}}">
                                    @error('link_crm')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="observaciones">Observaciones</label>
                                    <textarea class="form-control" id="observaciones @error('observaciones') is-invalid @enderror" name="observaciones" cols="30" rows="3">{{old('observaciones', $alumno->observaciones)}}</textarea>
                                    @error('observaciones')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="usuario">Usuario Asignado</label>
                                    <select class="selectpicker form-control @error('usuario') is-invalid @enderror" id="usuario" name="usuario" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($usuarios as $usuario)
                                            <option value="{{$usuario->id}}" @if (old('usuario') == strval($usuario->id) || $alumno->usuario_id == strval($usuario->id)) selected @endif data-subtext="{{$usuario->rol->name}}">{{$usuario->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('usuario')
                                        <span class="invalid-feedback" role="alert" id="error-select">
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
                                    <ul class="nav nav-pills arrow-navtabs nav-info nav-justified bg-light gap-2 mb-4" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link active align-middle" data-bs-toggle="tab" href="#tablistLaboral" role="tab" aria-selected="true">Datos Laborales
                                                <span class="badge bg-danger">Req.</span>
                                            </a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link align-middle" data-bs-toggle="tab" href="#tablistFormacion" role="tab" aria-selected="true">
                                                Formación Educativa
                                                {{-- <span class="badge bg-danger">Req.</span> --}}
                                            </a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link align-middle" data-bs-toggle="tab" href="#tablistRedesSociales" role="tab" aria-selected="true">Redes Sociales</a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link align-middle" data-bs-toggle="tab" href="#tablistFamiliar1" role="tab" aria-selected="true">Familiar 1</a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link align-middle" data-bs-toggle="tab" href="#tablistFamiliar2" role="tab" aria-selected="true">Familiar 2</a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link align-middle" data-bs-toggle="tab" href="#tablistFacturacion" role="tab" aria-selected="true">Facturación</a>
                                        </li>
                                    </ul>
                                    {{-- Tab panes --}}
                                    <div class="tab-content">
                                        <div class="tab-pane active show" id="tablistLaboral" role="tabpanel">
                                            <div class="row">
                                                <div class="col-lg-4 mb-3">
                                                    <label class="form-label" for="empresa">Empresa <span class="text-danger">(*)</span></label>
                                                    @if ($alumno->dato_laboral_id)
                                                        <input type="text" class="form-control @error('empresa') is-invalid @enderror" id="empresa" name="empresa" value="{{old('empresa', $alumno->datoLaboral->empresa)}}" placeholder="Escriba el nombre de la empresa">
                                                    @else
                                                        <input type="text" class="form-control @error('empresa') is-invalid @enderror" id="empresa" name="empresa" value="{{old('empresa')}}" placeholder="Escriba el nombre de la empresa">
                                                    @endif
                                                    @error('empresa')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="cargo">Cargo <span class="text-danger">(*)</span></label>
                                                    @if ($alumno->dato_laboral_id)
                                                        <input type="text" class="form-control @error('cargo') is-invalid @enderror" id="cargo" name="cargo" value="{{old('cargo', $alumno->datoLaboral->cargo)}}" placeholder="Escriba el cargo que ocupa">
                                                    @else
                                                        <input type="text" class="form-control @error('cargo') is-invalid @enderror" id="cargo" name="cargo" value="{{old('cargo')}}" placeholder="Escriba el cargo que ocupa">
                                                    @endif
                                                    @error('cargo')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="email_laboral">Correo <span class="text-danger">(*)</span></label>
                                                    @if ($alumno->dato_laboral_id)
                                                        <input type="text" class="form-control @error('email_laboral') is-invalid @enderror" id="email_laboral" name="email_laboral" value="{{old('email_laboral', $alumno->datoLaboral->email)}}" placeholder="Escriba el correo electrónico laboral">
                                                    @else
                                                        <input type="text" class="form-control @error('email_laboral') is-invalid @enderror" id="email_laboral" name="email_laboral" value="{{old('email_laboral')}}" placeholder="Escriba el correo electrónico laboral">
                                                    @endif
                                                    @error('email_laboral')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="telefono_laboral">N° de Teléfono</label>
                                                    @if ($alumno->dato_laboral_id)
                                                        <input type="text" class="form-control @error('telefono_laboral') is-invalid @enderror" id="telefono_laboral" name="telefono_laboral" value="{{old('telefono_laboral', $alumno->datoLaboral->telefono)}}" placeholder="Escriba el N° de la empresa">
                                                    @else
                                                        <input type="text" class="form-control @error('telefono_laboral') is-invalid @enderror" id="telefono_laboral" name="telefono_laboral" value="{{old('telefono_laboral')}}" placeholder="Escriba el N° de la empresa">
                                                    @endif
                                                    @error('telefono_laboral')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="celular_laboral">N° de Celular <span class="text-danger">(*)</span></label>
                                                    @if ($alumno->dato_laboral_id)
                                                        <input type="text" class="form-control @error('celular_laboral') is-invalid @enderror" id="celular_laboral" name="celular_laboral" value="{{old('celular_laboral', $alumno->datoLaboral->celular)}}" placeholder="Escriba el N° de celular">
                                                    @else
                                                        <input type="text" class="form-control @error('celular_laboral') is-invalid @enderror" id="celular_laboral" name="celular_laboral" value="{{old('celular_laboral')}}" placeholder="Escriba el N° de celular">
                                                    @endif
                                                    @error('celular_laboral')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-content">
                                        <div class="tab-pane" id="tablistFormacion" role="tabpanel">
                                            <div class="row">
                                                <div class="col-lg-4 mb-3">
                                                    <label class="form-label" for="formacion">Formación {{-- <span class="text-danger">(*)</span> --}}</label>
                                                    <select class="selectpicker form-control @error('formacion') is-invalid @enderror" id="formacion" name="formacion" data-live-search="true">
                                                        <option value="" selected disabled>Seleccionar...</option>
                                                        @foreach ($alumnos_formaciones as $formacion)
                                                            <option value="{{$formacion->id}}" @if (old('formacion') == strval($formacion->id) || $alumno->formacion_id == strval($formacion->id)) selected @endif>{{$formacion->nombre}}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('formacion')
                                                        <span class="invalid-feedback" role="alert" id="error-select">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-4 mb-3">
                                                    <label class="form-label" for="institucion_educativa">Institución Educativa {{-- <span class="text-danger">(*)</span> --}}</label>
                                                    <div class="d-flex flex-row bd-highlight">
                                                        <select class="selectpicker form-control @error('institucion_educativa') is-invalid @enderror" id="institucion_educativa" name="institucion_educativa" data-live-search="true">
                                                            <option value="" selected disabled>Seleccionar...</option>
                                                            <optgroup label="Universidades">
                                                                @foreach ($instituciones_educativas as $institucion_educativa)
                                                                        @if ($institucion_educativa->tipo == 'UN')
                                                                            <option value="{{$institucion_educativa->id}}" @if (old('institucion_educativa') == strval($institucion_educativa->id) || $alumno->institucion_educativa_id == strval($institucion_educativa->id)) selected @endif>{{$institucion_educativa->nombre}}</option>
                                                                        @endif
                                                                @endforeach
                                                            </optgroup>
                                                            <optgroup label="Colegios">
                                                                @foreach ($instituciones_educativas as $institucion_educativa)
                                                                        @if ($institucion_educativa->tipo == 'CO')
                                                                            <option value="{{$institucion_educativa->id}}" @if (old('institucion_educativa') == strval($institucion_educativa->id) || $alumno->institucion_educativa_id == strval($institucion_educativa->id)) selected @endif>{{$institucion_educativa->nombre}}</option>
                                                                        @endif
                                                                @endforeach
                                                            </optgroup>
                                                        </select>
                                                        @error('institucion_educativa')
                                                            <span class="invalid-feedback" role="alert" id="error-select">
                                                                <strong>{{$message}}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <label class="form-label" for="anho_egreso_educativo">Año de Egreso</label>
                                                    <input type="text" class="form-control text-center @error('anho_egreso_educativo') is-invalid @enderror" id="anho_egreso_educativo" name="anho_egreso_educativo" value="{{old('anho_egreso_educativo', $alumno->anho_egreso_educativo)}}" placeholder="2020">
                                                    @error('anho_egreso_educativo')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-content">
                                        <div class="tab-pane" id="tablistRedesSociales" role="tabpanel">
                                            <div class="row">
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="facebook">Facebook</label>
                                                    <input type="text" class="form-control @error('facebook') is-invalid @enderror" id="facebook" name="facebook" value="{{old('facebook', $alumno->facebook)}}">
                                                    @error('facebook')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="twitter">X (Twitter)</label>
                                                    <input type="text" class="form-control @error('twitter') is-invalid @enderror" id="twitter" name="twitter" value="{{old('twitter', $alumno->twitter)}}">
                                                    @error('twitter')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="instagram">Instagram</label>
                                                    <input type="text" class="form-control @error('instagram') is-invalid @enderror" id="instagram" name="instagram" value="{{old('instagram', $alumno->instagram)}}">
                                                    @error('instagram')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="linkedin">LinkedIn</label>
                                                    <input type="text" class="form-control @error('linkedin') is-invalid @enderror" id="linkedin" name="linkedin" value="{{old('linkedin', $alumno->linkedin)}}">
                                                    @error('linkedin')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="tiktok">TikTok</label>
                                                    <input type="text" class="form-control @error('tiktok') is-invalid @enderror" id="tiktok" name="tiktok" value="{{old('tiktok', $alumno->tiktok)}}">
                                                    @error('tiktok')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-content">
                                        <div class="tab-pane" id="tablistFamiliar1" role="tabpanel">
                                            <div class="row">
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="primer_nombre_familiar1">Primer Nombre</label>
                                                    @if ($alumno->familiar_uno_id)
                                                        <input type="text" class="form-control @error('primer_nombre_familiar1') is-invalid @enderror" id="primer_nombre_familiar1" name="primer_nombre_familiar1" value="{{old('primer_nombre_familiar1', $alumno->familiarUno->primer_nombre)}}" placeholder="Escriba el nombre">
                                                    @else
                                                        <input type="text" class="form-control @error('primer_nombre_familiar1') is-invalid @enderror" id="primer_nombre_familiar1" name="primer_nombre_familiar1" value="{{old('primer_nombre_familiar1')}}" placeholder="Escriba el nombre">
                                                    @endif
                                                    @error('primer_nombre_familiar1')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="segundo_nombre_familiar1">Segundo Nombre</label>
                                                    @if ($alumno->familiar_uno_id)
                                                        <input type="text" class="form-control @error('segundo_nombre_familiar1') is-invalid @enderror" id="segundo_nombre_familiar1" name="segundo_nombre_familiar1" value="{{old('segundo_nombre_familiar1', $alumno->familiarUno->segundo_nombre)}}" placeholder="Escriba el nombre">
                                                    @else
                                                        <input type="text" class="form-control @error('segundo_nombre_familiar1') is-invalid @enderror" id="segundo_nombre_familiar1" name="segundo_nombre_familiar1" value="{{old('segundo_nombre_familiar1')}}" placeholder="Escriba el nombre">
                                                    @endif
                                                    @error('segundo_nombre_familiar1')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="tercer_nombre_familiar1">Tercer Nombre</label>
                                                    @if ($alumno->familiar_uno_id)
                                                        <input type="text" class="form-control @error('tercer_nombre_familiar1') is-invalid @enderror" id="tercer_nombre_familiar1" name="tercer_nombre_familiar1" value="{{old('tercer_nombre_familiar1', $alumno->familiarUno->tercer_nombre)}}" placeholder="Escriba el nombre">
                                                    @else
                                                        <input type="text" class="form-control @error('tercer_nombre_familiar1') is-invalid @enderror" id="tercer_nombre_familiar1" name="tercer_nombre_familiar1" value="{{old('tercer_nombre_familiar1')}}" placeholder="Escriba el nombre">
                                                    @endif
                                                    @error('tercer_nombre_familiar1')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="primer_apellido_familiar1">Primer Apellido</label>
                                                    @if ($alumno->familiar_uno_id)
                                                        <input type="text" class="form-control @error('primer_apellido_familiar1') is-invalid @enderror" id="primer_apellido_familiar1" name="primer_apellido_familiar1" value="{{old('primer_apellido_familiar1', $alumno->familiarUno->primer_apellido)}}" placeholder="Escriba el apellido">
                                                    @else
                                                        <input type="text" class="form-control @error('primer_apellido_familiar1') is-invalid @enderror" id="primer_apellido_familiar1" name="primer_apellido_familiar1" value="{{old('primer_apellido_familiar1')}}" placeholder="Escriba el apellido">
                                                    @endif
                                                    @error('primer_apellido_familiar1')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="segundo_apellido_familiar1">Segundo Apellido</label>
                                                    @if ($alumno->familiar_uno_id)
                                                        <input type="text" class="form-control @error('segundo_apellido_familiar1') is-invalid @enderror" id="segundo_apellido_familiar1" name="segundo_apellido_familiar1" value="{{old('segundo_apellido_familiar1', $alumno->familiarUno->segundo_apellido)}}" placeholder="Escriba el apellido">
                                                    @else
                                                        <input type="text" class="form-control @error('segundo_apellido_familiar1') is-invalid @enderror" id="segundo_apellido_familiar1" name="segundo_apellido_familiar1" value="{{old('segundo_apellido_familiar1')}}" placeholder="Escriba el apellido">
                                                    @endif
                                                    @error('segundo_apellido_familiar1')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="relacion_familiar1">Relación Familiar</label>
                                                    <select class="selectpicker form-control @error('relacion_familiar1') is-invalid @enderror" id="relacion_familiar1" name="relacion_familiar1">
                                                        <option value="" selected disabled>Seleccionar...</option>
                                                        @foreach ($relaciones_familiares as $relacion_familiar)
                                                            @if ($alumno->familiar_uno_id)
                                                                <option value="{{$relacion_familiar->id}}" @if (old('relacion_familiar1') == strval($relacion_familiar->id) || $alumno->familiarUno->relacion_id == strval($relacion_familiar->id)) selected @endif>{{$relacion_familiar->nombre}}</option>
                                                            @else
                                                                <option value="{{$relacion_familiar->id}}" @if (old('relacion_familiar1') == strval($relacion_familiar->id)) selected @endif>{{$relacion_familiar->nombre}}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                    @error('relacion_familiar1')
                                                        <span class="invalid-feedback" role="alert" id="error-select">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="celular_familiar1">N° de Celular</label>
                                                    @if ($alumno->familiar_uno_id)
                                                        <input type="text" class="form-control @error('celular_familiar1') is-invalid @enderror" id="celular_familiar1" name="celular_familiar1" value="{{old('celular_familiar1', $alumno->familiarUno->celular)}}" placeholder="Escriba el N° de celular">
                                                    @else
                                                        <input type="text" class="form-control @error('celular_familiar1') is-invalid @enderror" id="celular_familiar1" name="celular_familiar1" value="{{old('celular_familiar1')}}" placeholder="Escriba el N° de celular">
                                                    @endif
                                                    @error('celular_familiar1')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-4 mb-3">
                                                    <label class="form-label" for="email_familiar1">Correo</label>
                                                    @if ($alumno->familiar_uno_id)
                                                        <input type="text" class="form-control @error('email_familiar1') is-invalid @enderror" id="email_familiar1" name="email_familiar1" value="{{old('email_familiar1', $alumno->familiarUno->email)}}" placeholder="Escriba el correo electrónico">
                                                    @else
                                                        <input type="text" class="form-control @error('email_familiar1') is-invalid @enderror" id="email_familiar1" name="email_familiar1" value="{{old('email_familiar1')}}" placeholder="Escriba el correo electrónico">
                                                    @endif
                                                    @error('email_familiar1')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-content">
                                        <div class="tab-pane" id="tablistFamiliar2" role="tabpanel">
                                            <div class="row">
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="primer_nombre_familiar2">Primer Nombre</label>
                                                    @if ($alumno->familiar_dos_id)
                                                        <input type="text" class="form-control @error('primer_nombre_familiar2') is-invalid @enderror" id="primer_nombre_familiar2" name="primer_nombre_familiar2" value="{{old('primer_nombre_familiar2', $alumno->familiarDos->primer_nombre)}}" placeholder="Escriba el nombre">
                                                    @else
                                                        <input type="text" class="form-control @error('primer_nombre_familiar2') is-invalid @enderror" id="primer_nombre_familiar2" name="primer_nombre_familiar2" value="{{old('primer_nombre_familiar2')}}" placeholder="Escriba el nombre">
                                                    @endif
                                                    @error('primer_nombre_familiar2')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="segundo_nombre_familiar2">Segundo Nombre</label>
                                                    @if ($alumno->familiar_dos_id)
                                                        <input type="text" class="form-control @error('segundo_nombre_familiar2') is-invalid @enderror" id="segundo_nombre_familiar2" name="segundo_nombre_familiar2" value="{{old('segundo_nombre_familiar2', $alumno->familiarDos->segundo_nombre)}}" placeholder="Escriba el nombre">
                                                    @else
                                                        <input type="text" class="form-control @error('segundo_nombre_familiar2') is-invalid @enderror" id="segundo_nombre_familiar2" name="segundo_nombre_familiar2" value="{{old('segundo_nombre_familiar2')}}" placeholder="Escriba el nombre">
                                                    @endif
                                                    @error('segundo_nombre_familiar2')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="tercer_nombre_familiar2">Tercer Nombre</label>
                                                    @if ($alumno->familiar_dos_id)
                                                        <input type="text" class="form-control @error('tercer_nombre_familiar2') is-invalid @enderror" id="tercer_nombre_familiar2" name="tercer_nombre_familiar2" value="{{old('tercer_nombre_familiar2', $alumno->familiarDos->tercer_nombre)}}" placeholder="Escriba el nombre">
                                                    @else
                                                        <input type="text" class="form-control @error('tercer_nombre_familiar2') is-invalid @enderror" id="tercer_nombre_familiar2" name="tercer_nombre_familiar2" value="{{old('tercer_nombre_familiar2')}}" placeholder="Escriba el nombre">
                                                    @endif
                                                    @error('tercer_nombre_familiar2')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="primer_apellido_familiar2">Primer Apellido</label>
                                                    @if ($alumno->familiar_dos_id)
                                                        <input type="text" class="form-control @error('primer_apellido_familiar2') is-invalid @enderror" id="primer_apellido_familiar2" name="primer_apellido_familiar2" value="{{old('primer_apellido_familiar2', $alumno->familiarDos->primer_apellido)}}" placeholder="Escriba el apellido">
                                                    @else
                                                        <input type="text" class="form-control @error('primer_apellido_familiar2') is-invalid @enderror" id="primer_apellido_familiar2" name="primer_apellido_familiar2" value="{{old('primer_apellido_familiar2')}}" placeholder="Escriba el apellido">
                                                    @endif
                                                    @error('primer_apellido_familiar2')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="segundo_apellido_familiar2">Segundo Apellido</label>
                                                    @if ($alumno->familiar_dos_id)
                                                        <input type="text" class="form-control @error('segundo_apellido_familiar2') is-invalid @enderror" id="segundo_apellido_familiar2" name="segundo_apellido_familiar2" value="{{old('segundo_apellido_familiar2', $alumno->familiarDos->segundo_apellido)}}" placeholder="Escriba el apellido">
                                                    @else
                                                        <input type="text" class="form-control @error('segundo_apellido_familiar2') is-invalid @enderror" id="segundo_apellido_familiar2" name="segundo_apellido_familiar2" value="{{old('segundo_apellido_familiar2')}}" placeholder="Escriba el apellido">
                                                    @endif
                                                    @error('segundo_apellido_familiar2')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="relacion_familiar2">Relación Familiar</label>
                                                    <select class="selectpicker form-control @error('relacion_familiar2') is-invalid @enderror" id="relacion_familiar2" name="relacion_familiar2">
                                                        <option value="" selected disabled>Seleccionar...</option>
                                                        @foreach ($relaciones_familiares as $relacion_familiar)
                                                            @if ($alumno->familiar_dos_id)
                                                                <option value="{{$relacion_familiar->id}}" @if (old('relacion_familiar2') == strval($relacion_familiar->id) || $alumno->familiarDos->relacion_id == strval($relacion_familiar->id)) selected @endif>{{$relacion_familiar->nombre}}</option>
                                                            @else
                                                                <option value="{{$relacion_familiar->id}}" @if (old('relacion_familiar2') == strval($relacion_familiar->id)) selected @endif>{{$relacion_familiar->nombre}}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                    @error('relacion_familiar2')
                                                        <span class="invalid-feedback" role="alert" id="error-select">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="celular_familiar2">N° de Celular</label>
                                                    @if ($alumno->familiar_dos_id)
                                                        <input type="text" class="form-control @error('celular_familiar2') is-invalid @enderror" id="celular_familiar2" name="celular_familiar2" value="{{old('celular_familiar2', $alumno->familiarDos->celular)}}" placeholder="Escriba el N° de celular">
                                                    @else
                                                        <input type="text" class="form-control @error('celular_familiar2') is-invalid @enderror" id="celular_familiar2" name="celular_familiar2" value="{{old('celular_familiar2')}}" placeholder="Escriba el N° de celular">
                                                    @endif
                                                    @error('celular_familiar2')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-4 mb-3">
                                                    <label class="form-label" for="email_familiar2">Correo</label>
                                                    @if ($alumno->familiar_dos_id)
                                                        <input type="text" class="form-control @error('email_familiar2') is-invalid @enderror" id="email_familiar2" name="email_familiar2" value="{{old('email_familiar2', $alumno->familiarDos->email)}}" placeholder="Escriba el correo electrónico">
                                                    @else
                                                        <input type="text" class="form-control @error('email_familiar2') is-invalid @enderror" id="email_familiar2" name="email_familiar2" value="{{old('email_familiar2')}}" placeholder="Escriba el correo electrónico">
                                                    @endif
                                                    @error('email_familiar2')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-content">
                                        <div class="tab-pane" id="tablistFacturacion" role="tabpanel">
                                            @foreach ($alumno->alumnoClientes as $key => $cliente)
                                                <div class="mb-2 fila" id="fila-{{$key}}">
                                                    <div class="row d-flex flex-wrap justify-content-center">
                                                        <div class="col-lg-2 mb-2 text-center" id="div-cliente-{{$key}}">
                                                            @if ($key == 0) <label class="form-label label-cliente">Cliente</label> @endif
                                                            <select class="selectpicker form-control cliente-{{$key}} cliente @error('clientes.'. $key . '.cliente') is-invalid @enderror" id="cliente-{{$key}}" name="clientes[{{$key}}][cliente]" data-live-search="true" data-id="{{$key}}">
                                                                <option value="" selected disabled>Seleccionar...</option>
                                                                @foreach ($clientes as $cl)
                                                                    <option value="{{$cl->id}}" @if (old('clientes.{{$key}}.cliente') == strval($cl->id) || $cliente->cliente_id == strval($cl->id)) selected @endif data-subtext="{{$cl->numero_documento}}">{{$cl->nombre}}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('clientes.'. $key . '.cliente')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{$message}}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                        <div class="col-lg-2 mb-2 me-3 text-center" id="div-razon_social-{{$key}}">
                                                            @if ($key == 0) <label class="form-label label-razon_social">Razón Social</label> @endif
                                                            <input type="text" class="form-control text-center razon_social-{{$key}} razon_social @error('clientes.'. $key . '.razon_social') is-invalid @enderror" id="clientes[{{$key}}][razon_social]" name="clientes[{{$key}}][razon_social]" value="{{old('clientes.' . $key . '.razon_social', $cliente->cliente->razon_social)}}" data-id="{{$key}}" readonly>
                                                            @error('clientes.'. $key . '.razon_social')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{$message}}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                        <div class="col-lg-2 mb-2 me-3 text-center" id="div-numero_documento-{{$key}}">
                                                            @if ($key == 0) <label class="form-label label-numero_documento">N° de Documento</label> @endif
                                                            <input type="text" class="form-control text-center numero_documento-{{$key}} numero_documento @error('clientes.'. $key . '.numero_documento') is-invalid @enderror" id="clientes[{{$key}}][numero_documento]" name="clientes[{{$key}}][numero_documento]" value="{{old('clientes.' . $key . '.numero_documento', $cliente->cliente->numero_documento)}}" data-id="{{$key}}" readonly>
                                                            @error('clientes.'. $key . '.numero_documento')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{$message}}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                        <div class="col-lg-1 mb-2 me-3 text-center" id="div-es_principal-{{$key}}">
                                                            @if ($key == 0)
                                                                <div class="div-label-es_principal-{{$key}}">
                                                                    <label class="form-label label-es_principal">Es Principal ?</label>
                                                                </div>
                                                            @endif
                                                            <div class="btn-group @error('clientes.'. $key . '.es_principal') is-invalid @enderror" role="group">
                                                                <input type="radio" class="btn-check es_principal1 es_principal1-{{$key}}" id="clientes[{{$key}}][es_principal1]" name="clientes[{{$key}}][es_principal]" value="false" @if (old('clientes.' . $key . '.es_principal') == 'false' || $cliente->es_principal == false) checked @endif data-id="{{$key}}">
                                                                <label class="btn btn-outline-danger" for="clientes[{{$key}}][es_principal1]">No</label>
                                                                <input type="radio" class="btn-check es_principal2 es_principal2-{{$key}}" id="clientes[{{$key}}][es_principal2]" name="clientes[{{$key}}][es_principal]" value="true" @if (old('clientes.' . $key . '.es_principal') == 'true' || $cliente->es_principal == true) checked @endif data-id="{{$key}}">
                                                                <label class="btn btn-outline-success" for="clientes[{{$key}}][es_principal2]">Sí</label>
                                                            </div>
                                                            <input type="hidden" class="es_principal-{{$key}}" @if ($errors->any()) value="{{old('clientes.' . $key . '.es_principal')}}" @elseif ($cliente->es_principal == false) value="false" @elseif ($cliente->es_principal == true) value="true" @endif>
                                                            @error('clientes.'. $key . '.es_principal')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{$message}}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                        <div class="col-lg-1 col-sm-2 text-center">
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
                                            <div id="cliente-fila">

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
                            <button type="button" class="btn btn-success" id="update-btn" data-id="{{$alumno->id}}">Actualizar</button>
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
        @include('ubs.alumnos.scripts.edit-scripts')
        @include('ubs.alumnos.scripts.edit-clientes-scripts')
    @endsection
@endcan
