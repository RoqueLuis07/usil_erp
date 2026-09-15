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
                                    <label class="form-label" for="proveedor">Proveedor</label>
                                    <input type="text" class="form-control" value="{{ $orden_compra->proveedor->razon_social }}">
                                    <input type="hidden" name="proveedor" value="{{ $orden_compra->proveedor_id }}">
                                    @error('proveedor')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="timbrado">Timbrado <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('timbrado') is-invalid @enderror" id="timbrado" name="timbrado" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($timbrados as $timbrado)
                                            <option value="{{ $timbrado->id }}" @if (old('timbrado') == strval($timbrado->id)) selected @endif>{{$timbrado->numero}}</option>
                                        @endforeach
                                    </select>
                                    @error('timbrado')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="orden_compra">Orden de Compra</label>
                                    <input type="text" class="form-control" value="N° {{ $orden_compra->id }}" readonly>
                                    <input type="hidden" name="orden_compra" value="{{ $orden_compra->id }}">
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
                                    <label class="form-label" for="moneda">Moneda</label>
                                    <input type="text" class="form-control" value="{{ $orden_compra->moneda->nombre }}" readonly>
                                    <input type="hidden" name="moneda" value="{{ $orden_compra->moneda_id }}">
                                    @error('moneda')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="condicion_compra">Condición</label>
                                    <input type="text" class="form-control" @if ($orden_compra->condicion_compra == 'CO') value="CONTADO" @elseif ($orden_compra->condicion_compra == 'CR') value="CREDITO" @endif readonly>
                                    <input type="hidden" name="condicion_compra" value="{{ $orden_compra->condicion_compra }}">
                                    @error('condicion_compra')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                @if ($orden_compra->credito_a)
                                    <div class="col-lg-2 mb-3" id="div-credito_a">
                                        <label class="form-label" for="credito_a">Crédito a</label>
                                        <input type="text" class="form-control" value="{{ $orden_compra->credito_a }} días">
                                        <input type="hidden" name="credito_a" value="{{ $orden_compra->credito_a }}">
                                        @error('credito_a')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                @endif
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
                                    @foreach ($orden_compra->detalles as $key => $detalle)
                                        <div class="mb-2 fila" id="fila-{{ $key }}">
                                            <div class="row">
                                                <div class="col-lg-2 mb-2 text-center">
                                                    <label class="form-label">Artículo</label>
                                                    <input type="text" class="form-control text-center" value="{{ $detalle->articulo->nombre }}" readonly>
                                                    <input type="hidden" name="detalles[{{ $key }}][articulo]" value="{{ $detalle->articulo_id }}">
                                                </div>
                                                <div class="col-lg-2 mb-2 text-center">
                                                    <label class="form-label">Descripción</label>
                                                    <input type="text" class="form-control text-center" name="detalles[{{ $key }}][descripcion]" value="{{$detalle->descripcion}}" readonly>
                                                </div>
                                                <div class="col-lg-2 mb-2 text-center">
                                                    <label class="form-label">CC1 <span class="text-danger">(*)</span></label>
                                                    <select class="selectpicker form-control centro_costo-{{ $key }} centro_costo @error('detalles.' . $key . '.centro_costo') is-invalid @enderror" id="centro_costo-{{ $key }}" name="detalles[{{ $key }}][centro_costo]" data-live-search="true" data-id="{{ $key }}">
                                                        <option value="" selected disabled>Seleccionar...</option>
                                                        @foreach ($centros_costos as $centro_costo)
                                                            <option value="{{$centro_costo->id}}" @if (old('detalles.' . $key . '.centro_costo') == strval($centro_costo->id)) selected @endif >{{$centro_costo->nombre}}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('detalles.' . $key . '.centro_costo')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 mb-2 text-center">
                                                    <label class="form-label">CC2 <span class="text-danger">(*)</span></label>
                                                    <select class="selectpicker form-control subcentro_costo-{{ $key }} subcentro_costo @error('detalles.' . $key . '.subcentro_costo') is-invalid @enderror" id="subcentro_costo-{{ $key }}" name="detalles[{{ $key }}][subcentro_costo]" data-live-search="true" data-id="{{ $key }}" disabled>
                                                        <option value="" selected disabled>Seleccionar...</option>

                                                    </select>
                                                    @error('detalles.' . $key . '.subcentro_costo')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-1 mb-2 text-center">
                                                    <label class="form-label">Cantidad</label>
                                                    <input type="text" class="form-control text-center" name="detalles[{{ $key }}][cantidad]" value="{{$detalle->cantidad}}" readonly>
                                                </div>
                                                <div class="col-lg-1 mb-2 text-center">
                                                    <label class="form-label">Costo</label>
                                                    <input type="text" class="form-control text-center" value="{{number_format($detalle->precio_costo, 0, ',', '.')}}" readonly>
                                                    <input type="hidden" name="detalles[{{ $key }}][precio_costo]" value="{{ $detalle->precio_costo }}">
                                                </div>
                                                <div class="col-lg-1 mb-2 text-center">
                                                    <label class="form-label">I.V.A. <span class="text-danger">(*)</span></label>
                                                    <select class="selectpicker form-control iva-{{ $key }} iva @error('detalles.' . $key . '.iva') is-invalid @enderror" id="iva-{{ $key }}" name="detalles[{{ $key }}][iva]" data-live-search="true" data-id="{{ $key }}">
                                                        <option value="" selected disabled>Seleccionar...</option>
                                                        <option value="10" @if (old('detalles.' . $key . '.iva') == '10') selected @endif>10%</option>
                                                        <option value="5" @if (old('detalles.' . $key . '.iva') == '5') selected @endif>5%</option>
                                                        <option value="0" @if (old('detalles.' . $key . '.iva' == '0')) selected @endif>EXENTO</option>
                                                    </select>
                                                    @error('detalles.' . $key . '.iva')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-1 mb-2 text-center">
                                                    <label class="form-label">Subtotal</label>
                                                    <input type="text" class="form-control text-center" value="{{ number_format($detalle->precio_costo * $detalle->cantidad, 0, ',', '.') }}" data-id="{{ $key }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
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
        @include('compras.scripts.create-from-orden-scripts')
        @include('compras.scripts.create-detalles-from-orden-scripts')
    @endsection
@endcan
