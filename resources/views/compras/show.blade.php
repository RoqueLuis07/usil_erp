@can('ver_compras')
    @extends('layouts.master')
    @section('title') Ver Compra @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Compras @endslot
            @slot('title') Ver Compra  @endslot
        @endcomponent

        {{-- @include('compras.modals.show-modals') --}}

        <div class="row">
            <form>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-12">
                                <h4 class="card-title mb-0">Visualizar compra</h4>
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha">Fecha</label>
                                    <input type="text" class="form-control text-center" id="fecha" value="{{Carbon\Carbon::parse($compra->fecha)->format('d/m/Y')}}" readonly>
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="proveedor">Proveedor</label>
                                    <input type="text" class="form-control" id="proveedor" value="{{$compra->proveedor->razon_social}} - {{$compra->proveedor->ruc}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="numero_factura">N° de Factura</label>
                                    <input type="text" class="form-control text-center" id="numero_factura" value="{{$compra->numero_factura}}" readonly>
                                </div>
                                <div class="col-lg-2">
                                    <label class="form-label" for="condicion_compra">Condición</label>
                                    <input type="text" class="form-control text-center" id="condicion_compra" @if($compra->condicion_compra == 'CO') value="CONTADO" @else value="CREDITO ({{ $compra->credito_a }} días)" @endif readonly>
                                </div>
                                @if ($compra->orden_compra_id)
                                    <div class="col-lg-2">
                                        <label class="form-label" for="orden_compra">OC</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control text-center" id="orden_compra" value="N° {{ $compra->orden_compra_id }}" readonly>
                                            <a type="button" class="btn btn-info" href="{{ route('ordenes_compras.show', $compra->orden_compra_id) }}"><i class="ri ri-eye-fill align-bottom"></i></a>
                                        </div>
                                    </div>
                                @endif
                                @if ($compra->url_factura)
                                    <div class="col-lg-1 mb-3 text-center">
                                        <label class="form-label" for="factura">Factura</span></label>
                                        <div>
                                            <a type="button" class="btn btn-info" href="{{asset($compra->url_factura)}}" target="_blank">Visualizar</a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="row d-flex flex-wrap justify-content-end">
                                <div class="col-lg-10">
                                    <div class="col-lg-2">
                                        <label class="form-label" for="estado">Estado</label>
                                        <input type="text" class="form-control text-center" id="estado" @if ($compra->estado == 'PE') value="PENDIENTE" @elseif ($compra->estado == 'CR') value="CREDITO" @elseif ($compra->estado == 'OP') value="ORDEN DE PAGO" @elseif ($compra->estado == 'PA') value="PAGADA" @elseif ($compra->estado == 'AN') value="ANULADA" @endif readonly>
                                    </div>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="monto_total">Monto Total</label>
                                    <input type="text" class="form-control text-center" id="monto_total" value="{{number_format($compra->monto_total, 0, ',', '.')}}" readonly>
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
                                            <a class="nav-link active align-middle" data-bs-toggle="tab" href="#tablistDetalles" role="tab" aria-selected="true">Detalles</a>
                                        </li>
                                        @can('ver_pagos_ordenes')
                                            @if ($compra->ordenesPagos->where('estado', 'PA')->count() > 0)
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link align-middle" data-bs-toggle="tab" href="#tablistOrdenesPagos" role="tab" aria-selected="true">Ordenes de Pagos</a>
                                                </li>
                                            @endif
                                        @endcan
                                        @can('ver_asientos_contables')
                                            @if ($compra->asientoContable && $compra->asientoContable->estado == 'AC')
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link align-middle" data-bs-toggle="tab" href="#tablistAsientoContable" role="tab" aria-selected="true">Asiento Contable</a>
                                                </li>
                                            @endif
                                        @endcan
                                    </ul>
                                    {{-- Tab panes --}}
                                    <div class="tab-content">
                                        <div class="tab-pane active show" id="tablistDetalles" role="tabpanel">
                                            @foreach ($compra->detalles as $key => $detalle)
                                                <div class="row d-flex flex-wrap justify-content-center">
                                                    <div class="col-lg-3 mb-3 text-center">
                                                        @if ($key == 0) <label class="form-label" for="articulo">Artículo</label>@endif
                                                        <input type="text" class="form-control text-center" id="articulo" value="{{$detalle->articulo->nombre}}" readonly>
                                                    </div>
                                                    <div class="col-lg-3 mb-3 text-center">
                                                        @if ($key == 0) <label class="form-label" for="descripcion">Descripción</label>@endif
                                                        <input type="text" class="form-control text-center" id="descripcion" value="@if ($detalle->descripcion) {{$detalle->descripcion}} @else ----- @endif" readonly>
                                                    </div>
                                                    <div class="col-lg-1 mb-3 text-center">
                                                        @if ($key == 0) <label class="form-label" for="cantidad">Cantidad</label>@endif
                                                        <input type="text" class="form-control text-center" id="cantidad" value="{{$detalle->cantidad}}" readonly>
                                                    </div>
                                                    <div class="col-lg-1 mb-3 text-center">
                                                        @if ($key == 0) <label class="form-label" for="precio_costo">Costo</label>@endif
                                                        <input type="text" class="form-control text-center" id="precio_costo" value="{{number_format($detalle->precio_costo, 0, ',', '.')}}" readonly>
                                                    </div>
                                                    <div class="col-lg-1 mb-3 text-center">
                                                        @if ($key == 0) <label class="form-label" for="subtotal">Subtotal</label>@endif
                                                        <input type="text" class="form-control text-center" id="subtotal" value="{{number_format($detalle->precio_costo * $detalle->cantidad, 0, ',', '.')}}" readonly>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @can('ver_pagos_ordenes')
                                        @if ($compra->ordenesPagos->where('estado', 'PA')->count() > 0)
                                            <div class="tab-content">
                                                <div class="tab-pane" id="tablistOrdenesPagos" role="tabpanel">
                                                    @foreach ($compra->ordenesPagos->where('estado', 'PA') as $key => $orden_pago)
                                                        <div class="row d-flex flex-wrap justify-content-center">
                                                            <div class="col-lg-2 mb-3 text-center">
                                                                @if ($key == 0) <label class="form-label" for="fecha_orden_pago">Fecha</label> @endif
                                                                <input type="text" class="form-control text-center" id="fecha_orden_pago" value="{{Carbon\Carbon::parse($orden_pago->fecha)->format('d/m/Y H:i')}}" readonly>
                                                            </div>
                                                            <div class="col-lg-2 mb-3 text-center">
                                                                @if ($key == 0) <label class="form-label" for="numero_orden_pago">N° OP</label> @endif
                                                                <input type="text" class="form-control text-center" id="numero_orden_pago" value="{{$orden_pago->id}}" readonly>
                                                            </div>
                                                            <div class="col-lg-3 mb-3 text-center">
                                                                @if ($key == 0) <label class="form-label" for="concepto">Concepto</label> @endif
                                                                <textarea class="form-control" id="concepto" cols="30" rows="3" readonly>{{$orden_pago->concepto}}</textarea>
                                                            </div>
                                                            <div class="col-lg-2 mb-3 text-center">
                                                                @if ($key == 0) <label class="form-label" for="forma_pago_orden_pago">Forma de Pago</label> @endif
                                                                <input type="text" class="form-control text-center" id="forma_pago_orden_pago" value="{{$orden_pago->formaPago->nombre}}" readonly>
                                                            </div>
                                                            <div class="col-lg-2 mb-3 text-center">
                                                                @if ($key == 0) <label class="form-label" for="monto_orden_pago">Monto</label> @endif
                                                                @php
                                                                    if ($orden_pago->moneda_id == 1) {
                                                                        $decimales = 0;
                                                                    } else {
                                                                        $decimales = 2;
                                                                    }
                                                                @endphp
                                                                <input type="text" class="form-control text-center" id="monto_orden_pago" value="{{number_format($orden_pago->monto, $decimales, ',', '.')}} {{$orden_pago->moneda->codigo}}" readonly>
                                                            </div>
                                                            <div class="col-lg-1 mb-3 text-center">
                                                                <label class="form-label" for="acciones">Acción</label>
                                                                <div class="text-center mt-1">
                                                                    <a type="button" class="btn btn-sm btn-primary" href="{{route('ordenes_pagos.show', $orden_pago->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver OP Completa"><i class="ri-eye-fill"></i></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @endcan
                                    @can('ver_asientos_contables')
                                        @if ($compra->asientoContable && $compra->asientoContable->estado == 'AC')
                                            <div class="tab-content">
                                                <div class="tab-pane" id="tablistAsientoContable" role="tabpanel">
                                                    <div class="row d-flex flex-wrap justify-content-center">
                                                        <div class="col-lg-2 mb-3 text-center">
                                                            <label class="form-label" for="fecha">Fecha</label>
                                                            <input type="text" class="form-control text-center" id="fecha" value="{{Carbon\Carbon::parse($compra->asientoContable->fecha)->format('d/m/Y H:i')}}" readonly>
                                                        </div>
                                                        <div class="col-lg-2 mb-3 text-center">
                                                            <label class="form-label" for="origen">Origen</label>
                                                            <input type="text" class="form-control text-center" id="origen" value="{{$compra->asientoContable->origen}}" readonly>
                                                        </div>
                                                        <div class="col-lg-1 mb-3 text-center">
                                                            <label class="form-label" for="moneda">Moneda</label>
                                                            <input type="text" class="form-control text-center" id="moneda" value="{{$compra->asientoContable->moneda->codigo}}" readonly>
                                                        </div>
                                                        <div class="col-lg-2 mb-3 text-center">
                                                            <label class="form-label" for="cotizacion">Cotización</label>
                                                            <input type="text" class="form-control text-center" id="cotizacion" @if ($compra->asientoContable->cotizacion_id) value="{{number_format($compra->asientoContable->cotizacion->compra, 0, ',', '.')}}" @else value="-----" @endif readonly>
                                                        </div>
                                                        <div class="col-lg-1 mb-3 text-center">
                                                            <label class="form-label" for="acciones">Acción</label>
                                                            <div class="text-center mt-1">
                                                                <a type="button" class="btn btn-sm btn-primary" href="{{route('asientos_contables.show', $compra->asientoContable->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver Asiento Completo"><i class="ri-eye-fill"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    @foreach ($compra->asientoContable->detalles as $key => $asiento)
                                                        <div class="row d-flex flex-wrap justify-content-center">
                                                            <div class="col-lg-2 mb-3 text-center">
                                                                @if ($key == 0) <label class="form-label" for="numero_cuenta">N° Cta. Contable</label> @endif
                                                                <input type="text" class="form-control text-center" id="numero_cuenta" value="{{$asiento->cuentaContable->cuenta}}" readonly>
                                                            </div>
                                                            <div class="col-lg-4 mb-3 text-center">
                                                                @if ($key == 0) <label class="form-label" for="cuenta_contable">Cuenta Contable</label> @endif
                                                                <input type="text" class="form-control text-center" id="cuenta_contable" value="{{$asiento->cuentaContable->nombre}}" readonly>
                                                            </div>
                                                            <div class="col-lg-2 mb-3 text-center">
                                                                @if ($key == 0) <label class="form-label" for="debe">Debe</label> @endif
                                                                <input type="text" class="form-control text-center" id="debe" @if ($asiento->debe) value="{{number_format($asiento->debe, 0, ',', '.')}}" @else value="-----" @endif readonly>
                                                            </div>
                                                            <div class="col-lg-2 mb-3 text-center">
                                                                @if ($key == 0) <label class="form-label" for="debe">Haber</label> @endif
                                                                <input type="text" class="form-control text-center" id="debe" @if ($asiento->haber) value="{{number_format($asiento->haber, 0, ',', '.')}}" @else value="-----" @endif readonly>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    <hr>
                                                    <div class="row d-flex flex-wrap justify-content-center">
                                                        <div class="col-lg-2 mb-3">
                                                        </div>
                                                        <div class="col-lg-4 mb-3">
                                                        </div>
                                                        <div class="col-lg-2 mb-3 text-center">
                                                            <label class="form-label" for="total_debe">Total Debe</label>
                                                            <input type="text" class="form-control text-center" id="total_debe" @if ($compra->asientoContable->detalles->sum('debe') > 0) value="{{number_format($compra->asientoContable->detalles->sum('debe'), 0, ',', '.')}}" @else value="-----" @endif readonly>
                                                        </div>
                                                        <div class="col-lg-2 mb-3 text-center">
                                                            <label class="form-label" for="debe">Total Haber</label>
                                                            <input type="text" class="form-control text-center" id="debe" @if ($compra->asientoContable->detalles->sum('haber')) value="{{number_format($compra->asientoContable->detalles->sum('haber'), 0, ',', '.')}}" @else value="-----" @endif readonly>
                                                        </div>
                                                    </div>
                                                    <div class="row d-flex flex-wrap justify-content-center">
                                                        <div class="col-lg-2 mb-3">
                                                        </div>
                                                        <div class="col-lg-4 mb-3">
                                                        </div>
                                                        <div class="col-lg-2 mb-3">
                                                        </div>
                                                        <div class="col-lg-2 mb-3 text-center">
                                                            <label class="form-label" for="diferencia">Diferencia</label>
                                                            <input type="text" class="form-control text-center" id="diferencia" value="{{number_format(($compra->asientoContable->detalles->sum('debe') - $compra->asientoContable->detalles->sum('haber')), 0, ',', '.')}}" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endcan
                                    {{-- /Tab panes --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="cargado_por">Cargado por:</label>
                            <br>
                            {{$compra->cargadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($compra->created_at)->format('d/m/Y H:i:s')}}
                        </div>
                        @if ($compra->anulado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="anulado_por">Anulado por:</label>
                                <br>
                                {{$compra->anuladoPor->name}}, en fecha: {{\Carbon\Carbon::parse($compra->updated_at)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('compras.index')}}">Volver</a>
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
