@can('ver_proveedores')
    @extends('layouts.master')
    @section('title') Ver Proveedor @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Proveedores @endslot
            @slot('title') Ver Proveedor  @endslot
        @endcomponent

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Visualizar proveedor</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="nombre_fantasia">Nombre Fantasía</label>
                                    <input type="text" class="form-control" id="nombre_fantasia"value="{{$proveedor->nombre_fantasia}}" name="nombre_fantasia" value="{{old('nombre_fantasia')}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="razon_social">Razón Social</label>
                                    <input type="text" class="form-control" id="razon_social" value="{{$proveedor->razon_social}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="ruc">R.U.C.</label>
                                    <input type="text" class="form-control" id="ruc" value="{{$proveedor->ruc}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="telefono">N° de Teléfono</label>
                                    <input type="text" class="form-control" id="telefono" value="{{$proveedor->telefono}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="email">Correo Electrónico</label>
                                    <input type="text" class="form-control" id="email" value="{{$proveedor->email}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="categoria">Categoría</label>
                                    <input type="text" class="form-control" id="categoria" @if ($proveedor->categoria_id) value="{{$proveedor->categoria->nombre}}" @endif readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="direccion">Dirección</label>
                                    <input type="text" class="form-control" id="direccion" value="{{$proveedor->direccion}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="departamento_paraguay">Departamento</label>
                                    <input type="text" class="form-control" id="departamento_paraguay" @if ($proveedor->departamento_id) value="{{$proveedor->departamento->nombre}}" @endif readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="ciudad">Ciudad</label>
                                    <input type="text" class="form-control" id="ciudad" @if ($proveedor->ciudad_id) value="{{$proveedor->ciudad->nombre}}" @endif readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="observaciones">Observaciones</label>
                                    <textarea class="form-control" id="observaciones" cols="30" rows="3" readonly>{{$proveedor->observaciones}}</textarea>
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
                                            <input type="text" class="form-control" id="nombre_contacto" value="{{$proveedor->nombre_contacto}}" readonly>
                                        </div>
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label" for="telefono_contacto">N° de Teléfono</label>
                                            <input type="text" class="form-control" id="telefono_contacto" value="{{$proveedor->telefono_contacto}}" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label" for="email_contacto">Correo Electrónico</label>
                                            <input type="text" class="form-control" id="email_contacto" value="{{$proveedor->email_contacto}}" readonly>
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
                                            <input type="text" class="form-control" id="banco" @if ($proveedor->banco_id) value="{{$proveedor->banco->nombre}}" @endif readonly>
                                        </div>
                                        <div class="col-lg-3 mb-3">
                                            <label class="form-label" for="numero_cuenta">N° de Cuenta</label>
                                            <input type="text" class="form-control" id="numero_cuenta" value="{{$proveedor->numero_cuenta}}" readonly>
                                        </div>
                                        <div class="col-lg-3 mb-3">
                                            <label class="form-label" for="tipo_cuenta">Tipo de Cuenta</label>
                                            <input type="text" class="form-control" id="tipo_cuenta" @if ($proveedor->tipo_cuenta == 'CC') value="CUENTA CORRIENTE" @elseif ($proveedor->tipo_cuenta == 'CA') value="CAJA DE AHORRO" @endif readonly>
                                        </div>
                                        <div class="col-lg-3 mb-3">
                                            <label class="form-label" for="moneda">Moneda</label>
                                            <input type="text" class="form-control" id="moneda" @if ($proveedor->moneda_id) value="{{$proveedor->moneda->nombre}}" @endif readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-3 mb-3">
                                            <label class="form-label" for="titular">Titular</label>
                                            <input type="text" class="form-control" id="titular" value="{{$proveedor->titular}}" readonly>
                                        </div>
                                        <div class="col-lg-3 mb-3">
                                            <label class="form-label" for="documento_titular">N° de Documento</label>
                                            <input type="text" class="form-control" id="documento_titular" value="{{$proveedor->documento_titular}}" readonly>
                                        </div>
                                        <div class="col-lg-3 mb-3">
                                            <label class="form-label" for="alias_cuenta">Alias</label>
                                            <input type="text" class="form-control" id="alias_cuenta" value="{{$proveedor->alias_cuenta}}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="cargado_por">Cargado por:</label>
                            <br>
                            {{$proveedor->cargadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($proveedor->created_at)->format('d/m/Y H:i:s')}}
                        </div>
                        @if ($proveedor->actualizado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="cargado_por">Última actualización hecha por:</label>
                                <br>
                                {{$proveedor->actualizadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($proveedor->updated_at)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('proveedores.index')}}">Volver</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
    @endsection
@endcan
