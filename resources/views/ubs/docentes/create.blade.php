@can('crear_docentes_ubs')
    @extends('layouts.master')
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
            <form action="{{route('docentes_ubs.store')}}" method="post" id="store-form">
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
                                    <label class="form-label" for="sexo">Sexo {{-- <span class="text-danger">(*)</span> --}}</label>
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
                                    <label class="form-label" for="fecha_nacimiento">Fecha de Nacimiento {{-- <span class="text-danger">(*)</span> --}}</label>
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
                                    <label class="form-label" for="nacionalidad">Nacionalidad {{-- <span class="text-danger">(*)</span> --}}</label>
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
                                    <label class="form-label" for="direccion">Dirección {{-- <span class="text-danger">(*)</span> --}}</label>
                                    <input type="text" class="form-control @error('direccion') is-invalid @enderror" id="direccion" name="direccion" value="{{old('direccion')}}" placeholder="Escriba la dirección particular">
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
                                    <label class="form-label" for="ciudad">Ciudad {{-- <span class="text-danger">(*)</span> --}}</label>
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
                                    <label class="form-label" for="barrio">Barrio {{-- <span class="text-danger">(*)</span> --}}</label>
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
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <ul class="nav nav-pills arrow-navtabs nav-info nav-justified bg-light gap-2 mb-4" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link active align-middle" data-bs-toggle="tab" href="#tablistFormacion" role="tab" aria-selected="true">
                                                Formación Educativa
                                                {{-- <span class="badge bg-danger">Req.</span> --}}
                                            </a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link align-middle" data-bs-toggle="tab" href="#tablistLaboral" role="tab" aria-selected="true">Datos Laborales</a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link align-middle" data-bs-toggle="tab" href="#tablistRedesSociales" role="tab" aria-selected="true">Redes Sociales</a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link align-middle" data-bs-toggle="tab" href="#tablistFacturacion" role="tab" aria-selected="true">Facturacion</a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link align-middle" data-bs-toggle="tab" href="#tablistDatosBancarios" role="tab" aria-selected="true">Datos Bancarios</a>
                                        </li>
                                    </ul>
                                    {{-- Tab panes --}}
                                    <div class="tab-content">
                                        <div class="tab-pane active show" id="tablistFormacion" role="tabpanel">
                                            <div class="row">
                                                <div class="col-lg-4 mb-3">
                                                    <label class="form-label" for="nivel_academico">Nivel Académico {{-- <span class="text-danger">(*)</span> --}}</label>
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
                                                    <label class="form-label" for="capacitacion_didactica">Capacitación Didáctica ? {{-- <span class="text-danger">(*)</span> --}}</label>
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
                                    <div class="tab-content">
                                        <div class="tab-pane" id="tablistLaboral" role="tabpanel">
                                            <div class="row">
                                                <div class="col-lg-4 mb-3">
                                                    <label class="form-label" for="empresa">Empresa</label>
                                                    <input type="text" class="form-control @error('empresa') is-invalid @enderror" id="empresa" name="empresa" value="{{old('empresa')}}" placeholder="Escriba el nombre de la empresa">
                                                    @error('empresa')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="cargo">Cargo</label>
                                                    <input type="text" class="form-control @error('cargo') is-invalid @enderror" id="cargo" name="cargo" value="{{old('cargo')}}" placeholder="Escriba el cargo que ocupa">
                                                    @error('cargo')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="email_laboral">Correo</label>
                                                    <input type="text" class="form-control @error('email_laboral') is-invalid @enderror" id="email_laboral" name="email_laboral" value="{{old('email_laboral')}}" placeholder="Escriba el correo electrónico laboral">
                                                    @error('email_laboral')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="telefono_laboral">N° de Teléfono</label>
                                                    <input type="text" class="form-control @error('telefono_laboral') is-invalid @enderror" id="telefono_laboral" name="telefono_laboral" value="{{old('telefono_laboral')}}" placeholder="Escriba el N° de la empresa">
                                                    @error('telefono_laboral')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="celular_laboral">N° de Celular</label>
                                                    <input type="text" class="form-control @error('celular_laboral') is-invalid @enderror" id="celular_laboral" name="celular_laboral" value="{{old('celular_laboral')}}" placeholder="Escriba el N° de celular">
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
                                        <div class="tab-pane" id="tablistRedesSociales" role="tabpanel">
                                            <div class="row">
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="facebook">Facebook</label>
                                                    <input type="text" class="form-control @error('facebook') is-invalid @enderror" id="facebook" name="facebook" value="{{old('facebook')}}">
                                                    @error('facebook')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="twitter">X (Twitter)</label>
                                                    <input type="text" class="form-control @error('twitter') is-invalid @enderror" id="twitter" name="twitter" value="{{old('twitter')}}">
                                                    @error('twitter')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="instagram">Instagram</label>
                                                    <input type="text" class="form-control @error('instagram') is-invalid @enderror" id="instagram" name="instagram" value="{{old('instagram')}}">
                                                    @error('instagram')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="linkedin">LinkedIn</label>
                                                    <input type="text" class="form-control @error('linkedin') is-invalid @enderror" id="linkedin" name="linkedin" value="{{old('linkedin')}}">
                                                    @error('linkedin')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="tiktok">TikTok</label>
                                                    <input type="text" class="form-control @error('tiktok') is-invalid @enderror" id="tiktok" name="tiktok" value="{{old('tiktok')}}">
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
                                        <div class="tab-pane" id="tablistFacturacion" role="tabpanel">
                                            <div class="row">
                                                <div class="col-lg-4 mb-3">
                                                    <label class="form-label" for="razon_social">Razón Social</label>
                                                    <input type="text" class="form-control @error('razon_social') is-invalid @enderror" id="razon_social" name="razon_social" value="{{old('razon_social')}}">
                                                    @error('razon_social')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="ruc">R.U.C.</label>
                                                    <input type="text" class="form-control @error('ruc') is-invalid @enderror" id="ruc" name="ruc" value="{{old('ruc')}}">
                                                    @error('ruc')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-content">
                                        <div class="tab-pane" id="tablistDatosBancarios" role="tabpanel">
                                            <div class="row">
                                                <div class="col-lg-3 mb-3">
                                                    <label class="form-label" for="banco">Banco</label>
                                                    <select class="form-control selectpicker @error('banco') is-invalid @enderror" id="banco" name="banco" data-live-search="true">
                                                        <option value="" selected disabled>Seleccionar...</option>
                                                        @foreach ($bancos as $banco)
                                                            <option value="{{$banco->id}}" @if (old('banco') == strval($banco->id)) selected @endif>{{$banco->nombre}}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('banco')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="tipo_cuenta_bancaria">Tipo de Cuenta</label>
                                                    <select class="form-control selectpicker @error('tipo_cuenta_bancaria') is-invalid @enderror" id="tipo_cuenta_bancaria" name="tipo_cuenta_bancaria" data-live-search="true">
                                                        <option value="" selected disabled>Seleccionar...</option>
                                                        <option value="CA" @if (old('tipo_cuenta_bancaria') == 'CA') selected @endif>Caja de Ahorro</option>
                                                        <option value="CC" @if (old('tipo_cuenta_bancaria') == 'CC') selected @endif>Cuenta Corriente</option>
                                                    </select>
                                                    @error('tipo_cuenta_bancaria')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="numero_cuenta_bancaria">N° de Cuenta</label>
                                                    <input type="text" class="form-control @error('numero_cuenta_bancaria') is-invalid @enderror" id="numero_cuenta_bancaria" name="numero_cuenta_bancaria" value="{{old('numero_cuenta_bancaria')}}">
                                                    @error('numero_cuenta_bancaria')
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
        @include('ubs.docentes.scripts.create-scripts')
    @endsection
@endcan
