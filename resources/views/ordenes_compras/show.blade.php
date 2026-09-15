@can('ver_compras_ordenes')
    @extends('layouts.master')
    @section('title') Ver Orden de Compra @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Ordenes de Compras @endslot
            @slot('title') Ver Orden de Compra @endslot
        @endcomponent

        @include('ordenes_compras.modals.show-modals')

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar orden de compra</h4>
                            </div>
                            @can('ver_compras')
                                @if ($orden_compra->compra)
                                    <div class="col-lg-6 text-end">
                                        <a type="button" class="btn btn-info" href="{{route('compras.show', $orden_compra->compra->id)}}">Ver Compra</a>
                                    </div>
                                @endif
                            @endcan
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
                                <div class="col-lg-3 mb-3 text-center">
                                    <label class="form-label" for="proveedor">Proveedor</span></label>
                                    <input type="text" class="form-control text-center" id="proveedor" value="{{$orden_compra->proveedor->razon_social}}" readonly>
                                </div>
                                <div class="col-lg-1 mb-3 text-center">
                                    <label class="form-label" for="documento_proveedor">R.U.C.</span></label>
                                    <input type="text" class="form-control text-center" id="documento_proveedor" value="{{ $orden_compra->proveedor->ruc }}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3 text-center">
                                    <label class="form-label" for="condicion_compra">Condición</span></label>
                                    <input type="text" class="form-control text-center" id="condicion_compra" @if ($orden_compra->condicion_compra == 'CO') value="CONTADO" @else value="CREDITO ({{$orden_compra->credito_a}} dias)" @endif readonly>
                                </div>
                                <div class="col-lg-1 mb-3 text-center">
                                    <label class="form-label" for="moneda">Moneda</span></label>
                                    <input type="text" class="form-control text-center" id="moneda" value="{{ $orden_compra->moneda->codigo }}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3 text-center">
                                    <label class="form-label" for="monto_total">Monto Total</label>
                                    <input class="form-control text-center" type="text" id="monto_total" value="{{number_format($orden_compra->monto_total, 0, ',', '.')}}" readonly>
                                </div>
                                @if ($orden_compra->url_presupuesto)
                                    <div class="col-lg-1 mb-3 text-center">
                                        <label class="form-label" for="presupuesto">Presupuesto</span></label>
                                        <div>
                                            <a type="button" class="btn btn-info" href="{{asset($orden_compra->url_presupuesto)}}" target="_blank">Visualizar</a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="observaciones">Observaciones</label>
                                    <textarea class="form-control" id="observaciones" cols="30" rows="3" readonly>{{$orden_compra->observaciones}}</textarea>
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
                                        <div class="mb-2">
                                            <div class="row d-flex flex-wrap justify-content-center">
                                                <div class="col-lg-3 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="articulo">Artículo</label> @endif
                                                    <input type="text" class="form-control" id="articulo" value="{{$detalle->articulo->nombre}}" readonly>
                                                </div>
                                                <div class="col-lg-3 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="descripcion">Descripción</label> @endif
                                                    <input type="text" class="form-control text-center" id="descripcion" value="{{$detalle->descripcion}}" readonly>
                                                </div>
                                                <div class="col-lg-1 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="cantidad">Cantidad</label> @endif
                                                    <input type="text" class="form-control text-center" id="cantidad" value="{{number_format($detalle->cantidad, 0, ',', '.')}}" readonly>
                                                </div>
                                                <div class="col-lg-2 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="precio_costo">Costo</label> @endif
                                                    <input type="text" class="form-control text-center" id="precio_costo" value="{{number_format($detalle->precio_costo, 0, ',', '.')}}" readonly>
                                                </div>
                                                <div class="col-lg-2 mb-2 text-center">
                                                    @if ($key == 0) <label class="form-label" for="subtotal">Subtotal</label> @endif
                                                    <input type="text" class="form-control text-center" id="subtotal" value="{{number_format(($detalle->subtotal), 0, ',', '.')}}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="cargado_por">Cargado por:</label>
                            <br>
                            {{$orden_compra->cargadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($orden_compra->created_at)->format('d/m/Y H:i:s')}}
                        </div>
                        @if ($orden_compra->actualizado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="cargado_por">Última actualización hecha por:</label>
                                <br>
                                {{$orden_compra->actualizadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($orden_compra->updated_at)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('ordenes_compras.index')}}">Volver</a>
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
