@can('ver_pagos_ordenes')
    @extends('layouts.master')
    @section('title') Ver Orden de Pago @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Ordenes de Pagos @endslot
            @slot('title') Ver Orden de Pago @endslot
        @endcomponent

        @include('ordenes_pagos.modals.show-modals')

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar orden de pago</h4>
                            </div>
                            @can('ver_compras')
                                @if ($orden_pago->compra)
                                    <div class="col-lg-6 text-end">
                                        <a type="button" class="btn btn-info" href="{{route('compras.show', $orden_pago->compra->id)}}">Ver Compra</a>
                                    </div>
                                @endif
                            @endcan
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-1 mb-3 text-center">
                                    <label class="form-label" for="numero_orden">N° OP</label>
                                    <input type="text" class="form-control text-center" id="numero_orden" value="{{$orden_pago->id}}" readonly>
                                </div>
                                <div class="col-lg-1 mb-3 text-center">
                                    <label class="form-label" for="fecha">Fecha</label>
                                    <input type="text" class="form-control text-center" id="fecha" value="{{Carbon\Carbon::parse($orden_pago->created_at)->format('d/m/y')}}" readonly>
                                </div>
                                @if ($orden_pago->compra_id)
                                    <div class="col-lg-2 mb-3 text-center">
                                        <label class="form-label" for="numero_factura">Factura de Compra</span></label>
                                        <input type="text" class="form-control text-center" id="numero_factura" value="{{$orden_pago->compra->numero_factura}}" readonly>
                                    </div>
                                @endif
                                <div class="col-lg-3 mb-3 text-center">
                                    <label class="form-label" for="proveedor">Proveedor</span></label>
                                    <input type="text" class="form-control text-center" id="proveedor" value="{{$orden_pago->proveedor->razon_social}}" readonly>
                                </div>
                                <div class="col-lg-1 mb-3 text-center">
                                    <label class="form-label" for="documento_proveedor">R.U.C.</span></label>
                                    <input type="text" class="form-control text-center" id="documento_proveedor" value="{{ $orden_pago->proveedor->ruc }}" readonly>
                                </div>
                                <div class="col-lg-1 mb-3 text-center">
                                    <label class="form-label" for="moneda">Moneda</span></label>
                                    <input type="text" class="form-control text-center" id="moneda" value="{{ $orden_pago->moneda->codigo }}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3 text-center">
                                    <label class="form-label" for="monto_total">Monto Total</label>
                                    <input class="form-control text-center" type="text" id="monto_total" value="{{number_format($orden_pago->monto_total, 0, ',', '.')}}" readonly>
                                </div>
                                <div class="col-lg-1 mb-3 text-center">
                                    <label class="form-label" for="monto_total">Estado</label>
                                    <input class="form-control text-center fw-bold
                                        @if ($orden_pago->estado == 'PE')
                                            text-warning"
                                        @elseif ($orden_pago->estado == 'AP')
                                            text-success"
                                        @elseif ($orden_pago->estado == 'AN')
                                            text-danger"
                                        @endif
                                    type="text" id="monto_total"
                                        @if ($orden_pago->estado == 'PE')
                                            value="PENDIENTE"
                                        @elseif ($orden_pago->estado == 'AP')
                                            value="APROBADO"
                                        @elseif ($orden_pago->estado == 'AN')
                                            value="ANULADO"
                                        @endif
                                    readonly>
                                </div>
                            </div>
                            <div class="row">

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <ul class="nav nav-pills arrow-navtabs nav-info nav-justified bg-light gap-2 mb-4" role="tablist">
                                        @can('ver_pagos')
                                            @if ($orden_pago->pago && $orden_pago->pago->estado == 'PA')
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link active align-middle" data-bs-toggle="tab" href="#tablistPagos" role="tab" aria-selected="true">Pago</a>
                                                </li>
                                            @endif
                                        @endcan
                                        @can('ver_asientos_contables')
                                            @if ($orden_pago->asientoContable && $orden_pago->asientoContable->estado == 'AC')
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link @if (!$orden_pago->pago || $orden_pago->pago->estado != 'PA') active @endif align-middle" data-bs-toggle="tab" href="#tablistAsientoContable" role="tab" aria-selected="true">Asiento Contable</a>
                                                </li>
                                            @endif
                                        @endcan
                                    </ul>
                                    {{-- Tab panes --}}
                                    @can('ver_pagos')
                                        @if ($orden_pago->pago && $orden_pago->pago->estado == 'PA')
                                            <div class="tab-content">
                                                <div class="tab-pane active" id="tablistPagos" role="tabpanel">
                                                    <div class="row d-flex flex-wrap justify-content-center">
                                                        <div class="col-lg-2 mb-3 text-center">
                                                            <label class="form-label" for="fecha_pago">Fecha</label>
                                                            <input type="text" class="form-control text-center" id="fecha_pago" value="{{Carbon\Carbon::parse($orden_pago->pago->fecha)->format('d/m/Y H:i')}}" readonly>
                                                        </div>
                                                        <div class="col-lg-2 mb-3 text-center">
                                                            <label class="form-label" for="forma_pago">Forma de Pago</label>
                                                            <input type="text" class="form-control text-center" id="forma_pago" value="{{$orden_pago->pago->formaPago->nombre}}" readonly>
                                                        </div>
                                                        <div class="col-lg-2 mb-3 text-center">
                                                            <label class="form-label" for="monto">Monto</label>
                                                            @php
                                                                if ($orden_pago->moneda_id == 1) {
                                                                    $decimales = 0;
                                                                } else {
                                                                    $decimales = 2;
                                                                }
                                                            @endphp
                                                            <input type="text" class="form-control text-center" id="monto" value="{{number_format($orden_pago->pago->monto, $decimales, ',', '.')}} {{$orden_pago->moneda->codigo}}" readonly>
                                                        </div>
                                                        @if ($orden_pago->pago->caja_id)
                                                            <div class="col-lg-2 mb-3 text-center">
                                                                <label class="form-label" for="caja">Caja</label>
                                                                <input type="text" class="form-control text-center" id="caja" value="{{$orden_pago->pago->caja->nombre}}" readonly>
                                                            </div>
                                                        @endif
                                                        @if ($orden_pago->pago->cuenta_bancaria_id)
                                                            <div class="col-lg-3 mb-3 text-center">
                                                                <label class="form-label" for="cuenta_bancaria">Cuenta Bancaria</label>
                                                                <input type="text" class="form-control text-center" id="cuenta_bancaria" value="{{$orden_pago->pago->cuentaBancaria->banco->nombre}}" readonly>
                                                            </div>
                                                            <div class="col-lg-2 mb-3 text-center">
                                                                <label class="form-label" for="numero_cuenta_bancaria">N° de Cuenta</label>
                                                                <input type="text" class="form-control text-center" id="numero_cuenta_bancaria" value="{{$orden_pago->pago->cuentaBancaria->numero_cuenta}}" readonly>
                                                            </div>
                                                        @endif
                                                        @if ($orden_pago->forma_pago_id == 7)
                                                            <div class="col-lg-2 mb-3 text-center">
                                                                <label class="form-label" for="numero_cheque">N° de Cheque</label>
                                                                <input type="text" class="form-control text-center" id="numero_cheque" value="{{$orden_pago->pago->numero_cheque}}" readonly>
                                                            </div>
                                                            <div class="col-lg-2 mb-3 text-center">
                                                                <label class="form-label" for="numero_serie_cheque">N° de Serie</label>
                                                                <input type="text" class="form-control text-center" id="numero_serie_cheque" value="{{$orden_pago->pago->numero_serie_cheque}}" readonly>
                                                            </div>
                                                        @endif
                                                        <div class="col-lg-1 mb-3 text-center">
                                                            <label class="form-label" for="acciones">Acción</label>
                                                            <div class="text-center mt-1">
                                                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#showPagoModal-{{$orden_pago->pago->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endcan
                                    @can('ver_asientos_contables')
                                        @if ($orden_pago->asientoContable && $orden_pago->asientoContable->estado == 'AC')
                                            <div class="tab-content">
                                                <div class="tab-pane @if (!$orden_pago->pago || $orden_pago->pago->estado != 'PA') active @endif" id="tablistAsientoContable" role="tabpanel">
                                                    <div class="row d-flex flex-wrap justify-content-center">
                                                        <div class="col-lg-2 mb-3 text-center">
                                                            <label class="form-label" for="fecha">Fecha</label>
                                                            <input type="text" class="form-control text-center" id="fecha" value="{{Carbon\Carbon::parse($orden_pago->asientoContable->fecha)->format('d/m/Y H:i')}}" readonly>
                                                        </div>
                                                        <div class="col-lg-2 mb-3 text-center">
                                                            <label class="form-label" for="origen">Origen</label>
                                                            <input type="text" class="form-control text-center" id="origen" value="{{$orden_pago->asientoContable->origen}}" readonly>
                                                        </div>
                                                        <div class="col-lg-1 mb-3 text-center">
                                                            <label class="form-label" for="moneda">Moneda</label>
                                                            <input type="text" class="form-control text-center" id="moneda" value="{{$orden_pago->asientoContable->moneda->codigo}}" readonly>
                                                        </div>
                                                        <div class="col-lg-2 mb-3 text-center">
                                                            <label class="form-label" for="cotizacion">Cotización</label>
                                                            <input type="text" class="form-control text-center" id="cotizacion" @if ($orden_pago->asientoContable->cotizacion_id) value="{{number_format($orden_pago->asientoContable->cotizacion->compra, 0, ',', '.')}}" @else value="-----" @endif readonly>
                                                        </div>
                                                        <div class="col-lg-1 mb-3 text-center">
                                                            <label class="form-label" for="acciones">Acción</label>
                                                            <div class="text-center mt-1">
                                                                <a type="button" class="btn btn-sm btn-primary" href="{{route('asientos_contables.show', $orden_pago->asientoContable->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver Asiento Completo"><i class="ri-eye-fill"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    @foreach ($orden_pago->asientoContable->detalles as $key => $asiento)
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
                                                            <input type="text" class="form-control text-center" id="total_debe" @if ($orden_pago->asientoContable->detalles->sum('debe') > 0) value="{{number_format($orden_pago->asientoContable->detalles->sum('debe'), 0, ',', '.')}}" @else value="-----" @endif readonly>
                                                        </div>
                                                        <div class="col-lg-2 mb-3 text-center">
                                                            <label class="form-label" for="debe">Total Haber</label>
                                                            <input type="text" class="form-control text-center" id="debe" @if ($orden_pago->asientoContable->detalles->sum('haber')) value="{{number_format($orden_pago->asientoContable->detalles->sum('haber'), 0, ',', '.')}}" @else value="-----" @endif readonly>
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
                                                            <input type="text" class="form-control text-center" id="diferencia" value="{{number_format(($orden_pago->asientoContable->detalles->sum('debe') - $orden_pago->asientoContable->detalles->sum('haber')), 0, ',', '.')}}" readonly>
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
                            {{$orden_pago->cargadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($orden_pago->created_at)->format('d/m/Y H:i:s')}}
                        </div>
                        @if ($orden_pago->aprobado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="cargado_por">Aprobado por:</label>
                                <br>
                                {{$orden_pago->aprobadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($orden_pago->updated_at)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                        @if ($orden_pago->anulado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="cargado_por">Anulado por:</label>
                                <br>
                                {{$orden_pago->anuladoPor->name}}, en fecha: {{\Carbon\Carbon::parse($orden_pago->updated_at)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('ordenes_pagos.index')}}">Volver</a>
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
