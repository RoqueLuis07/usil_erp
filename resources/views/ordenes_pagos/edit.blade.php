@can('editar_compras_ordenes')
    @extends('layouts.master')
    @section('title') Editar Orden de Compra @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Ordenes de Compra @endslot
            @slot('title') Editar Orden de Compra  @endslot
        @endcomponent

        <div class="row">
            <form action="{{route('ordenes_compras.update', $orden_compra->id)}}" method="post" id="update-form" enctype="multipart/form-data">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Actualizar orden de compra</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-1 mb-3 text-center">
                                    <label class="form-label" for="numero_orden">N° OC</label>
                                    <input type="text" class="form-control text-center" id="numero_orden" value="{{$orden_compra->id}}" readonly>
                                </div>
                                <div class="col-lg-1 mb-3 text-center">
                                    <label class="form-label" for="fecha">Fecha</label>
                                    <input type="text" class="form-control text-center" id="fecha" value="{{Carbon\Carbon::parse($orden_compra->created_at)->format('d/m/y')}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="proveedor">Proveedor <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('proveedor') is-invalid @enderror" id="proveedor" name="proveedor" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($proveedores as $proveedor)
                                            <option value="{{$proveedor->id}}" @if (old('proveedor') == strval($proveedor->id) || $orden_compra->proveedor_id == strval($proveedor->id)) selected @endif data-subtext="{{$proveedor->ruc}}">{{$proveedor->razon_social}}</option>
                                        @endforeach
                                    </select>
                                    @error('proveedor')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="moneda">Moneda <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('moneda') is-invalid @enderror" id="moneda" name="moneda" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($monedas as $moneda)
                                            <option value="{{$moneda->id}}" @if (old('moneda') == strval($moneda->id) || $orden_compra->moneda_id == strval($moneda->id)) selected @endif>{{$moneda->nombre}}</option>
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
                                        <option value="CO" @if (old('condicion_compra') == 'CO' || $orden_compra->condicion_compra == 'CO') selected @endif>CONTADO</option>
                                        <option value="CR" @if (old('condicion_compra') == 'CR' || $orden_compra->condicion_compra == 'CR') selected @endif>CREDITO</option>
                                    </select>
                                    @error('condicion_compra')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-2 mb-3 @if (old('condicion_compra') == 'CO' || $orden_compra->condicion_compra == 'CO') d-none @endif" id="div-credito_a">
                                    <label class="form-label" for="credito_a">Crédito a <span class="text-danger">(*)</span></label>
                                    <select class="form-control selectpicker @error('credito_a') is-invalid @enderror" id="credito_a" name="credito_a" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        <option value="7" @if (old('credito_a') == '7' || $orden_compra->credito_a == 7) selected @endif>7 DIAS</option>
                                        <option value="15" @if (old('credito_a') == '15' || $orden_compra->credito_a == 15) selected @endif>15 DIAS</option>
                                        <option value="30" @if (old('credito_a') == '30' || $orden_compra->credito_a == 30) selected @endif>30 DIAS</option>
                                        <option value="45" @if (old('credito_a') == '45' || $orden_compra->credito_a == 45) selected @endif>45 DIAS</option>
                                        <option value="60" @if (old('credito_a') == '60' || $orden_compra->credito_a == 60) selected @endif>60 DIAS</option>
                                        <option value="90" @if (old('credito_a') == '90' || $orden_compra->credito_a == 90) selected @endif>90 DIAS</option>
                                        <option value="120" @if (old('credito_a') == '120' || $orden_compra->credito_a == 120) selected @endif>120 DIAS</option>
                                    </select>
                                    @error('credito_a')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-7 mb-3">
                                    <label class="form-label" for="observaciones">Observaciones</label>
                                    <textarea class="form-control @error('observaciones') is-invalid @enderror" id="observaciones" name="observaciones" cols="30" rows="3">{{old('observaciones', $orden_compra->observaciones)}}</textarea>
                                    @error('observaciones')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 mb-3 d-flex flex-wrap justify-content-between">
                                    <div class="col-lg-5 mb-3" id="div-adjunto">
                                        <label class="form-label" for="adjunto">Presupuesto <span class="text-muted small">Si desea cambiar el presupuesto anterior, debe subir un nuevo archivo para reemplazarlo.</span></label>
                                        <div class="input-group custom-file-button">
                                            @if ($orden_compra->url_presupuesto)
                                                <a type="button" href="{{asset($orden_compra->url_presupuesto)}}" class="btn btn-outline-primary" target="_blank"> <i class="ri-eye-fill align-bottom me-2"></i> Ver Anterior</a>
                                            @endif
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
                                        <input class="form-control text-center" type="text" id="monto_total" value="{{ number_format($orden_compra->monto_total, 0, ',', '.') }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Detalles de la Orden de Compra</h4>
                                </div>
                                <div class="card-body">
                                    @foreach ($orden_compra->detalles as $key => $detalle)
                                        <div class="mb-2 fila" id="fila-{{$key}}">
                                            <div class="row">
                                                <div class="col-lg-3 mb-2 text-center" id="div-articulo-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-articulo">Artículo <span class="text-danger">(*)</span></label> @endif
                                                    <select class="selectpicker form-control articulo-{{$key}} articulo @error('detalles.'. $key . '.articulo') is-invalid @enderror" id="articulo-{{$key}}" name="detalles[{{$key}}][articulo]" data-live-search="true" data-id="{{$key}}">
                                                        <option value="" selected disabled>Seleccionar...</option>
                                                        @foreach ($articulos as $articulo)
                                                            <option value="{{$articulo->id}}" @if (old('detalles.{{$key}}.articulo') == strval($articulo->id) || $detalle->articulo_id == strval($articulo->id)) selected @endif >{{$articulo->nombre}}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('detalles.'. $key . '.articulo')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-3 mb-2 text-center" id="div-descripcion-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-descripcion">Descripción</label> @endif
                                                    <input type="text" class="form-control text-center descripcion-{{$key}} descripcion @error('detalles.'. $key . '.descripcion') is-invalid @enderror" id="detalles[{{$key}}][descripcion]" name="detalles[{{$key}}][descripcion]" value="{{old('detalles.' . $key . '.descripcion', $detalle->descripcion)}}" data-id="{{$key}}">
                                                    @error('detalles.'. $key . '.descripcion')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-1 mb-2 text-center" id="div-cantidad-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-cantidad">Cantidad <span class="text-danger">(*)</span></label> @endif
                                                    <input type="text" class="form-control text-center cantidad-{{$key}} cantidad @error('detalles.'. $key . '.cantidad') is-invalid @enderror" id="detalles[{{$key}}][cantidad]" name="detalles[{{$key}}][cantidad]" value="{{old('detalles.' . $key . '.cantidad', $detalle->cantidad)}}" data-id="{{$key}}" placeholder="1">
                                                    @error('detalles.'. $key . '.cantidad')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 mb-2 text-center" id="div-precio_costo-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-precio_costo">Costo <span class="text-danger">(*)</span></label> @endif
                                                    <input type="text" class="form-control text-center precio_costo-{{$key}} precio_costo @error('detalles.'. $key . '.precio_costo') is-invalid @enderror" id="detalles[{{$key}}][precio_costo]" name="detalles[{{$key}}][precio_costo]" value="{{old('detalles.' . $key . '.precio_costo', $detalle->precio_costo)}}" data-id="{{$key}}" placeholder="100.000">
                                                    @error('detalles.'. $key . '.precio_costo')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{$message}}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-2 mb-2 text-center" id="div-subtotal-{{$key}}">
                                                    @if ($key == 0) <label class="form-label label-subtotal">Subtotal</label> @endif
                                                    <input type="text" class="form-control text-center subtotal-{{$key}} subtotal @error('detalles.'. $key . '.subtotal') is-invalid @enderror" id="detalles[{{$key}}][subtotal]" value="{{old('detalles.' . $key . '.subtotal', number_format($detalle->subtotal, 0, ',', '.'))}}" data-id="{{$key}}" placeholder="0" readonly>
                                                    @error('detalles.'. $key . '.subtotal')
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
                                    <div id="articulo-fila">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
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
        <script src="{{ URL::asset('js/flatpickr.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        @include('ordenes_compras.scripts.edit-scripts')
        @include('ordenes_compras.scripts.edit-detalles-scripts')
    @endsection
@endcan
