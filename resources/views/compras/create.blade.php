@can('crear_compras')
    @extends('layouts.master')
    @section('title') Agregar Compra @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Compras @endslot
            @slot('title') Agregar Compra  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('compras.store')}}" method="post" id="store-form" enctype="multipart/form-data">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Nueva compra</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-2 mb-3 text-center">
                                    <label class="form-label" for="fecha">Fecha de Factura <span class="text-danger">(*)</span></label>
                                    <div class="form-icon right">
                                        <input type="text" class="flatpickr form-control form-control-icon text-center @error('fecha') is-invalid @enderror" id="fecha" name="fecha" value="{{old('fecha')}}" placeholder="Seleccionar...">
                                        <i class="ri-calendar-2-line" id="calendar-icon"></i>
                                        @error('fecha')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="proveedor">Proveedor <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('proveedor') is-invalid @enderror" id="proveedor" name="proveedor" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($proveedores as $proveedor)
                                            <option value="{{$proveedor->id}}" @if (old('proveedor') == strval($proveedor->id)) selected @endif data-subtext="{{ $proveedor->ruc }}">{{$proveedor->razon_social}}</option>
                                        @endforeach
                                    </select>
                                    @error('proveedor')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="timbrado">Timbrado <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('timbrado') is-invalid @enderror" id="timbrado" name="timbrado" data-live-search="true" disabled>
                                        <option value="" selected disabled>Seleccionar...</option>

                                    </select>
                                    @error('timbrado')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="orden_compra">Orden de Compra <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('orden_compra') is-invalid @enderror" id="orden_compra" name="orden_compra" data-live-search="true" disabled>
                                        <option value="" selected disabled>Seleccionar...</option>

                                    </select>
                                    @error('orden_compra')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3 text-center">
                                    <label class="form-label" for="numero_factura">N° Factura <span class="text-danger">(*)</span></label>
                                    <input type="text" class="form-control @error('numero_factura') is-invalid @enderror" id="numero_factura" name="numero_factura" value="{{old('numero_factura')}}">
                                    @error('numero_factura')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="moneda">Moneda <span class="text-danger">(*)</span></label>
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
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="condicion_compra">Condición <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('condicion_compra') is-invalid @enderror" id="condicion_compra" name="condicion_compra">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        <option value="CO" @if (old('condicion_compra') == 'CO') selected @endif>CONTADO</option>
                                        <option value="CR" @if (old('condicion_compra') == 'CR') selected @endif>CREDITO</option>
                                    </select>
                                    @error('condicion_compra')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3 @if (old('condicion_compra') != 'CR') d-none @endif" id="div-credito_a">
                                    <label class="form-label" for="credito_a">Crédito a <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('credito_a') is-invalid @enderror" id="credito_a" name="credito_a" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        <option value="7" @if (old('credito_a') == '7') selected @endif>7 DIAS</option>
                                        <option value="15" @if (old('credito_a') == '15') selected @endif>15 DIAS</option>
                                        <option value="30" @if (old('credito_a') == '30') selected @endif>30 DIAS</option>
                                        <option value="45" @if (old('credito_a') == '45') selected @endif>45 DIAS</option>
                                        <option value="60" @if (old('credito_a') == '60') selected @endif>60 DIAS</option>
                                        <option value="90" @if (old('credito_a') == '90') selected @endif>90 DIAS</option>
                                        <option value="120" @if (old('credito_a') == '120') selected @endif>120 DIAS</option>
                                    </select>
                                    @error('credito_a')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="unidad_negocio">Unidad de Negocio <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('unidad_negocio') is-invalid @enderror" id="unidad_negocio" name="unidad_negocio" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($unidades_negocios as $unidad_negocio)
                                            <option value="{{ $unidad_negocio->id }}" @if (old('unidad_negocio') == strval($unidad_negocio->id)) selected @endif>{{$unidad_negocio->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('unidad_negocio')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="subunidad_negocio">Subunidad de Negocio <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('subunidad_negocio') is-invalid @enderror" id="subunidad_negocio" name="subunidad_negocio" data-live-search="true" disabled>
                                        <option value="" selected disabled>Seleccionar...</option>

                                    </select>
                                    @error('subunidad_negocio')
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
                            <div class="row">
                                <div class="col-lg-12 d-flex flex-wrap justify-content-between">
                                    <div class="col-lg-5" id="div-adjunto">
                                        <label class="form-label" for="adjunto">Factura</label>
                                        <div class="input-group custom-file-button">
                                            <input type="file" class="form-control @error('adjunto') is-invalid @enderror" id="adjunto" name="adjunto" accept="image/jpeg,image/png,application/pdf" onchange="readURL(this);">
                                            <button type="button" class="btn btn-outline-danger" id="eliminar-adjunto" disabled><i class="ri-delete-bin-fill align-bottom me-2"></i>Eliminar Archivo</button>
                                            @error('adjunto')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <p class="text-muted">Se aceptan archivos del tipo <code>.jpg</code>, <code>.png</code>, <code>.pdf</code> y con un tamaño máximo de <code>5mb</code>.</p>
                                    </div>
                                    <div class="col-lg-2 text-center">
                                        <label class="form-label" for="monto_total">Monto Total</label>
                                        <input class="form-control text-center" type="text" id="monto_total" value="0" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Detalles de la Compra</h4>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2 fila" id="fila-0">
                                        <div class="row">
                                            <div class="col-lg-2 mb-2 text-center" id="div-articulo-0">
                                                <label class="form-label label-articulo">Artículo <span class="text-danger">(*)</span></label>
                                                <select class="selectpicker form-control articulo-0 articulo @error('detalles.0.articulo') is-invalid @enderror" id="articulo-0" name="detalles[0][articulo]" data-live-search="true" data-id="0" @if (old('orden_compra')) style="pointer-events: none" @endif>
                                                    <option value="" selected disabled>Seleccionar...</option>
                                                    @foreach ($articulos as $articulo)
                                                        <option value="{{$articulo->id}}" @if (old('detalles.0.articulo') == strval($articulo->id)) selected @endif data-subtext="{{$articulo->codigo}}">{{$articulo->nombre}}</option>
                                                    @endforeach
                                                </select>
                                                @error('detalles.0.articulo')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 mb-2 text-center" id="div-descripcion-0">
                                                <label class="form-label label-descripcion">Descripción</label>
                                                <input type="text" class="form-control text-center descripcion-0 descripcion @error('detalles.0.descripcion') is-invalid @enderror" id="descripcion-0" name="detalles[0][descripcion]" value="{{old('detalles.0.descripcion')}}" data-id="0" @if (old('orden_compra')) readonly @endif>
                                                @error('detalles.0.descripcion')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-1 mb-2 text-center" id="div-centro_costo-0">
                                                <label class="form-label label-centro_costo">CC1 <span class="text-danger">(*)</span></label>
                                                <select class="selectpicker form-control centro_costo-0 centro_costo @error('detalles.0.centro_costo') is-invalid @enderror" id="centro_costo-0" name="detalles[0][centro_costo]" data-live-search="true" data-id="0" @if (old('orden_compra')) readonly @endif>
                                                    <option value="" selected disabled>Seleccionar...</option>
                                                    @foreach ($centros_costos as $centro_costo)
                                                        <option value="{{$centro_costo->id}}" @if (old('detalles.0.centro_costo') == strval($centro_costo->id)) selected @endif >{{$centro_costo->nombre}}</option>
                                                    @endforeach
                                                </select>
                                                @error('detalles.0.centro_costo')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-1 mb-2 text-center" id="div-subcentro_costo-0">
                                                <label class="form-label label-subcentro_costo">CC2 <span class="text-danger">(*)</span></label>
                                                <select class="selectpicker form-control subcentro_costo-0 subcentro_costo @error('detalles.0.subcentro_costo') is-invalid @enderror" id="subcentro_costo-0" name="detalles[0][subcentro_costo]" data-live-search="true" data-id="0" disabled>
                                                    <option value="" selected disabled>Seleccionar...</option>

                                                </select>
                                                @error('detalles.0.subcentro_costo')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-1 mb-2 text-center" id="div-cantidad-0">
                                                <label class="form-label label-cantidad">Cantidad <span class="text-danger">(*)</span></label>
                                                <input type="text" class="form-control text-center cantidad-0 cantidad @error('detalles.0.cantidad') is-invalid @enderror" id="cantidad-0" name="detalles[0][cantidad]" value="{{old('detalles.0.cantidad')}}" data-id="0" placeholder="1" @if (old('orden_compra')) readonly @endif>
                                                @error('detalles.0.cantidad')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-1 mb-2 text-center" id="div-precio_costo-0">
                                                <label class="form-label label-precio_costo">Costo <span class="text-danger">(*)</span></label>
                                                <input type="text" class="form-control text-center precio_costo-0 precio_costo @error('detalles.0.precio_costo') is-invalid @enderror" id="precio_costo-0" name="detalles[0][precio_costo]" value="{{old('detalles.0.precio_costo')}}" data-id="0" placeholder="100.000" @if (old('orden_compra')) readonly @endif>
                                                @error('detalles.0.precio_costo')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-1 mb-2 text-center" id="div-iva-0">
                                                <label class="form-label label-iva">I.V.A. <span class="text-danger">(*)</span></label>
                                                <select class="selectpicker form-control iva-0 iva @error('detalles.0.iva') is-invalid @enderror" id="iva-0" name="detalles[0][iva]" data-live-search="true" data-id="0" @if (old('orden_compra')) readonly @endif>
                                                    <option value="" selected disabled>Seleccionar...</option>
                                                    <option value="10" @if (old('detalles.0.iva') == '10') selected @endif>10%</option>
                                                    <option value="5" @if (old('detalles.0.iva') == '5') selected @endif>5%</option>
                                                    <option value="0" @if (old('detalles.0.iva' == '0')) selected @endif>EXENTO</option>
                                                </select>
                                                @error('detalles.0.iva')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-2 mb-2 text-center" id="div-subtotal-0">
                                                <label class="form-label label-subtotal">Subtotal</label>
                                                <input type="text" class="form-control text-center subtotal-0 subtotal @error('detalles.0.subtotal') is-invalid @enderror" id="subtotal-0" data-id="0" placeholder="0" readonly>
                                                @error('detalles.0.subtotal')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{$message}}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="col-lg-1 col-sm-2 text-center">
                                                <label class="form-label label-acciones">Acciones</label>
                                                <div class="align-middle" id="acciones-0">
                                                    <button type="button" class="btn btn-icon btn-success btn-add" id="btn-add-0" data-id="0"><i class="ri-add-fill"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="articulo-fila">

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
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        @include('compras.scripts.create-scripts')
        @include('compras.scripts.create-detalles-scripts')
    @endsection
@endcan
