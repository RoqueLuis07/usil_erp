@can('crear_proveedores')
    @extends('layouts.master')
    @section('title') Agregar Proveedor @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Proveedores @endslot
            @slot('title') Agregar Proveedor  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('proveedores.store')}}" method="post" id="store-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Nuevo proveedor</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="nombre_fantasia">Nombre Fantasía <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('nombre_fantasia') is-invalid @enderror" id="nombre_fantasia" name="nombre_fantasia" value="{{old('nombre_fantasia')}}">
                                    @error('nombre_fantasia')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="razon_social">Razón Social <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('razon_social') is-invalid @enderror" id="razon_social" name="razon_social" value="{{old('razon_social')}}">
                                    @error('razon_social')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="ruc">R.U.C. <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('ruc') is-invalid @enderror" id="ruc" name="ruc" value="{{old('ruc')}}">
                                    @error('ruc')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="telefono">N° de Teléfono <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('telefono') is-invalid @enderror" id="telefono" name="telefono" value="{{old('telefono')}}">
                                    @error('telefono')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="email">Correo Electrónico <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{old('email')}}">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="categoria">Categoría <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('categoria') is-invalid @enderror" id="categoria" name="categoria" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($categorias as $categoria)
                                            <option value="{{$categoria->id}}" @if (old('categoria') == strval($categoria->id)) selected @endif>{{$categoria->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('categoria')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="direccion">Dirección <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('direccion') is-invalid @enderror" id="direccion" name="direccion" value="{{old('direccion')}}">
                                    @error('direccion')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="departamento_paraguay">Departamento</label>
                                    <select class="selectpicker form-control @error('departamento_paraguay') is-invalid @enderror" id="departamento_paraguay" name="departamento_paraguay" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($departamentos_paraguay as $departamento)
                                            <option value="{{$departamento->id}}" @if (old('departamento_paraguay') == strval($departamento->id)) selected @endif>{{$departamento->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('departamento_paraguay')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="ciudad">Ciudad</label>
                                    <select class="selectpicker form-control @error('ciudad') is-invalid @enderror" id="ciudad" name="ciudad" data-live-search="true" disabled>
                                        <option value="" selected disabled>Seleccionar...</option>

                                    </select>
                                    @error('ciudad')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="observaciones">Observaciones</label>
                                    <textarea class="form-control @error('observaciones') is-invalid @enderror" id="observaciones" name="observaciones" cols="30" rows="3">{{old('observaciones')}}</textarea>
                                    @error('observaciones')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Contacto</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label" for="nombre_contacto">Nombre</label>
                                            <input type="text" class="form-control @error('nombre_contacto') is-invalid @enderror" id="nombre_contacto" name="nombre_contacto" value="{{old('nombre_contacto')}}">
                                            @error('nombre_contacto')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label" for="telefono_contacto">N° de Teléfono</label>
                                            <input type="text" class="form-control @error('telefono_contacto') is-invalid @enderror" id="telefono_contacto" name="telefono_contacto" value="{{old('telefono_contacto')}}">
                                            @error('telefono_contacto')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label" for="email_contacto">Correo Electrónico</label>
                                            <input type="text" class="form-control @error('email_contacto') is-invalid @enderror" id="email_contacto" name="email_contacto" value="{{old('email_contacto')}}">
                                            @error('email_contacto')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Cuenta Bancaria</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-3 mb-3">
                                            <label class="form-label" for="banco">Banco</label>
                                            <select class="selectpicker form-control @error('banco') is-invalid @enderror" id="banco" name="banco" data-live-search="true">
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
                                        <div class="col-lg-3 mb-3">
                                            <label class="form-label" for="numero_cuenta">N° de Cuenta</label>
                                            <input type="text" class="form-control @error('numero_cuenta') is-invalid @enderror" id="numero_cuenta" name="numero_cuenta" value="{{old('numero_cuenta')}}">
                                            @error('numero_cuenta')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 mb-3">
                                            <label class="form-label" for="tipo_cuenta">Tipo de Cuenta</label>
                                            <select class="selectpicker form-control @error('tipo_cuenta') is-invalid @enderror" id="tipo_cuenta" name="tipo_cuenta" data-live-search="true">
                                                <option value="" selected disabled>Seleccionar...</option>
                                                <option value="CC" @if (old('tipo_cuenta') == 'CC') selected @endif>CUENTA CORRIENTE</option>
                                                <option value="CA" @if (old('tipo_cuenta') == 'CA') selected @endif>CAJA DE AHORRO</option>
                                            </select>
                                            @error('tipo_cuenta')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 mb-3">
                                            <label class="form-label" for="moneda">Moneda</label>
                                            <select class="selectpicker form-control @error('moneda') is-invalid @enderror" id="moneda" name="moneda" data-live-search="true">
                                                <option value="" selected disabled>Seleccionar...</option>
                                                @foreach ($monedas as $moneda)
                                                    <option value="{{$moneda->id}}" @if (old('moneda') == strval($moneda->id)) selected @endif>{{$moneda->nombre}}</option>
                                                @endforeach
                                            </select>
                                            @error('moneda')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3 mb-3">
                                            <label class="form-label" for="titular">Titular</label>
                                            <input type="text" class="form-control @error('titular') is-invalid @enderror" id="titular" name="titular" value="{{old('titular')}}">
                                            @error('titular')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 mb-3">
                                            <label class="form-label" for="documento_titular">N° de Documento</label>
                                            <input type="text" class="form-control @error('documento_titular') is-invalid @enderror" id="documento_titular" name="documento_titular" value="{{old('documento_titular')}}">
                                            @error('documento_titular')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-lg-3 mb-3">
                                            <label class="form-label" for="alias_cuenta">Alias</label>
                                            <input type="text" class="form-control @error('alias_cuenta') is-invalid @enderror" id="alias_cuenta" name="alias_cuenta" value="{{old('alias_cuenta')}}">
                                            @error('alias_cuenta')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
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
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        @include('proveedores.scripts.create-scripts')
    @endsection
@endcan
