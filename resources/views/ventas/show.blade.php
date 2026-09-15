@can('ver_ventas')
    @extends('layouts.master')
    @section('title') Ver Venta @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Ventas @endslot
            @slot('title') Ver Venta  @endslot
        @endcomponent

        @include('ventas.modals.show-modals')

        <div class="row">
            <form>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-12">
                                <h4 class="card-title mb-0">Visualizar venta</h4>
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha">Fecha</label>
                                    <input type="text" class="form-control text-center" id="fecha" value="{{Carbon\Carbon::parse($venta->fecha)->format('d/m/Y H:i:s')}}" readonly>
                                </div>
                                @if ($venta->alumno_id)
                                    <div class="col-lg-3 mb-3">
                                        <label class="form-label" for="alumno">Alumno</label>
                                        <input type="text" class="form-control" id="alumno" @if ($venta->alumno_id) value="{{$venta->alumno->primer_nombre}} {{$venta->alumno->primer_apellido}} - {{$venta->alumno->numero_documento}}" @endif readonly>
                                    </div>
                                @endif
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="cliente">Cliente</label>
                                    <input type="text" class="form-control" id="cliente" value="{{$venta->cliente->nombre}} - {{$venta->cliente->numero_documento}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="numero_factura">N° de Factura</label>
                                    <input type="text" class="form-control text-center" id="numero_factura" value="{{$venta->numero_factura}}" readonly>
                                </div>
                                <div class="col-lg-2">
                                    <label class="form-label" for="tipo">Tipo</label>
                                    <input type="text" class="form-control text-center" id="tipo" @if($venta->forma_pago == 'CO') value="CONTADO" @else value="CREDITO" @endif readonly>
                                </div>
                                <div class="col-lg-2">
                                    <label class="form-label" for="estado">Estado</label>
                                    <input type="text" class="form-control text-center" id="estado" @if($venta->estado == 'AC') value="FACTURADO" @elseif ($venta->estado == 'PE') value="A COBRAR" @elseif ($venta->estado == 'CO') value="COBRADO" @elseif ($venta->estado == 'AN') value="ANULADO" @elseif ($venta->estado == 'NC') value="NOTA DE CREDITO" @endif readonly>
                                </div>
                            </div>
                            <div class="row d-flex flex-wrap @if ($venta->descuento_aplicado_id) justify-content-between @else justify-content-end @endif">
                                @if ($venta->descuento_aplicado_id)
                                    <div class="col-lg-2 mb-3">
                                        <label class="form-label" for="descuento_aplicado">Descuento Aplicado</label>
                                        <input type="text" class="form-control text-center" id="descuento_aplicado" value="{{$venta->descuentoAplicado->nombre}}" readonly>
                                    </div>
                                @endif
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="monto_total">Monto Total</label>
                                    <input type="text" class="form-control text-center" id="monto_total" value="{{number_format($venta->monto_total, 0, ',', '.')}}" readonly>
                                </div>
                                @if ($venta->forma_pago == 'CR')
                                    <div class="col-lg-2 mb-3">
                                        <label class="form-label" for="saldo">Saldo</label>
                                        <input type="text" class="form-control text-center" id="saldo" value="{{number_format($venta->saldo, 0, ',', '.')}}" readonly>
                                    </div>
                                @endif
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
                                        @can('ver_cobros')
                                            @if ($venta->cobros->count() > 0)
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link align-middle" data-bs-toggle="tab" href="#tablistCobros" role="tab" aria-selected="true">Cobros</a>
                                                </li>
                                            @endif
                                        @endcan
                                        @can('ver_recibos')
                                            @if ($venta->forma_pago == 'CR' && $recibos->count() > 0)
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link align-middle" data-bs-toggle="tab" href="#tablistRecibo" role="tab" aria-selected="true">Recibo</a>
                                                </li>
                                            @endif
                                        @endcan
                                        @can('ver_asientos_contables')
                                            @if ($venta->asientoContabl && $venta->asientoContable->estado == 'AC')
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link align-middle" data-bs-toggle="tab" href="#tablistAsientoContable" role="tab" aria-selected="true">Asiento Contable</a>
                                                </li>
                                            @endif
                                        @endcan
                                    </ul>
                                    {{-- Tab panes --}}
                                    <div class="tab-content">
                                        <div class="tab-pane active show" id="tablistDetalles" role="tabpanel">
                                            @foreach ($venta->ventaDetalles as $key => $detalle)
                                                <div class="row d-flex flex-wrap justify-content-center">
                                                    <div class="col-lg-3 mb-3 text-center">
                                                        @if ($key == 0) <label class="form-label" for="descripcion">Descripción</label>@endif
                                                        <input type="text" class="form-control text-center" id="descripcion" value="{{$detalle->descripcion}}" readonly>
                                                    </div>
                                                    <div class="col-lg-1 mb-3 text-center">
                                                        @if ($key == 0) <label class="form-label" for="monto_bruto">Monto</label>@endif
                                                        <input type="text" class="form-control text-center" id="monto_bruto" value="{{number_format($detalle->monto_bruto, 0, ',', '.')}}" readonly>
                                                    </div>
                                                    <div class="col-lg-1 mb-3 text-center">
                                                        @if ($key == 0) <label class="form-label" for="descuento">Descuento</label>@endif
                                                        <input type="text" class="form-control text-center" id="descuento" value="{{number_format($detalle->descuento, 0, ',', '.')}}" readonly>
                                                    </div>
                                                    <div class="col-lg-1 mb-3 text-center">
                                                        @if ($key == 0) <label class="form-label" for="monto_neto">Pagado</label>@endif
                                                        <input type="text" class="form-control text-center" id="monto_neto" value="{{number_format($detalle->monto_neto, 0, ',', '.')}}" readonly>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @can('ver_cobros')
                                        @if ($venta->cobros->count() > 0)
                                            <div class="tab-content">
                                                <div class="tab-pane" id="tablistCobros" role="tabpanel">
                                                    @foreach ($venta->cobros as $key => $cobro)
                                                        <div class="row d-flex flex-wrap justify-content-center">
                                                            <div class="col-lg-2 mb-3 text-center">
                                                                @if ($key == 0) <label class="form-label" for="fecha_cobro">Fecha</label> @endif
                                                                <input type="text" class="form-control text-center" id="fecha_cobro" value="{{Carbon\Carbon::parse($cobro->fecha)->format('d/m/Y H:i')}}" readonly>
                                                            </div>
                                                            <div class="col-lg-2 mb-3 text-center">
                                                                @if ($key == 0) <label class="form-label" for="forma_pago">Forma de Pago</label> @endif
                                                                <input type="text" class="form-control text-center" id="forma_pago" value="{{$cobro->formaPago->nombre}}" readonly>
                                                            </div>
                                                            <div class="col-lg-1 mb-3 text-center">
                                                                @if ($key == 0) <label class="form-label" for="monto">Monto</label> @endif
                                                                <input type="text" class="form-control text-center" id="monto" value="{{number_format($cobro->monto, 0, ',', '.')}}" readonly>
                                                            </div>
                                                            <div class="col-lg-2 mb-3 text-center">
                                                                @if ($key == 0) <label class="form-label" for="caja">Caja</label> @endif
                                                                <input type="text" class="form-control text-center" id="caja" value="{{$cobro->caja->nombre}}" readonly>
                                                            </div>
                                                            <div class="col-lg-2 mb-3 text-center">
                                                                @if ($key == 0) <label class="form-label" for="banco_origen">Banco Origen</label> @endif
                                                                <input type="text" class="form-control text-center" id="banco_origen" @if ($cobro->banco_id) value="{{$cobro->banco->nombre}}" @else value="-----" @endif readonly>
                                                            </div>
                                                            <div class="col-lg-2 mb-3 text-center">
                                                                @if ($key == 0) <label class="form-label" for="cuenta_bancaria">Cuenta Destino</label> @endif
                                                                <input type="text" class="form-control text-center" id="cuenta_bancaria" @if ($cobro->cuenta_bancaria_id) value="{{$cobro->cuentaBancaria->banco->nombre}} - {{$cobro->cuentaBancaria->numero_cuenta}}" @else value="-----" @endif readonly>
                                                            </div>
                                                            <div class="col-lg-1 mb-3 text-center">
                                                                @if ($key == 0) <label class="form-label" for="acciones">Acción</label> @endif
                                                                <div class="text-center mt-1">
                                                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#showCobroModal-{{$cobro->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @endcan
                                    @can('ver_recibos')
                                        @if ($recibos->count() > 0)
                                            <div class="tab-content">
                                                <div class="tab-pane" id="tablistRecibo" role="tabpanel">
                                                    @foreach ($recibos as $key => $recibo)
                                                        <div class="row d-flex flex-wrap justify-content-center">
                                                            <div class="col-lg-2 mb-3 text-center">
                                                                @if ($key == 0) <label class="form-label" for="fecha_recibo">Fecha</label> @endif
                                                                <input type="text" class="form-control text-center" id="fecha_recibo" value="{{Carbon\Carbon::parse($recibo->fecha)->format('d/m/Y H:i')}}" readonly>
                                                            </div>
                                                            <div class="col-lg-2 mb-3 text-center">
                                                                @if ($key == 0) <label class="form-label" for="numero_recibo">N°</label> @endif
                                                                <input type="text" class="form-control text-center" id="numero_recibo" value="{{str_pad($recibo->numero, 7, '0', STR_PAD_LEFT)}}" readonly>
                                                            </div>
                                                            <div class="col-lg-2 mb-3 text-center">
                                                                @if ($key == 0) <label class="form-label" for="concepto">Concepto</label> @endif
                                                                <textarea class="form-control" id="concepto" cols="30" rows="3" readonly>{{$recibo->concepto}}</textarea>
                                                            </div>
                                                            <div class="col-lg-2 mb-3 text-center">
                                                                @if ($key == 0) <label class="form-label" for="forma_pago_recibo">Forma de Pago</label> @endif
                                                                <input type="text" class="form-control text-center" id="forma_pago_recibo" value="{{$recibo->formaPago->nombre}}" readonly>
                                                            </div>
                                                            <div class="col-lg-2 mb-3 text-center">
                                                                @if ($key == 0) <label class="form-label" for="monto_recibo">Monto</label> @endif
                                                                @foreach ($recibo->detalles as $detalle)
                                                                    @if ($venta->id == $detalle->venta_id)
                                                                        <input type="text" class="form-control text-center" id="monto_recibo" value="{{number_format($detalle->monto, 0, ',', '.')}}" readonly>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                            <div class="col-lg-1 mb-3 text-center">
                                                                <label class="form-label" for="acciones">Acción</label>
                                                                <div class="text-center mt-1">
                                                                    <a type="button" class="btn btn-sm btn-primary" href="{{route('recibos.show', $recibo->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver Recibo Completo"><i class="ri-eye-fill"></i></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @endcan
                                    @can('ver_asientos_contables')
                                        @if ($venta->asientoContable && $venta->asientoContable->estado == 'AC')
                                            <div class="tab-content">
                                                <div class="tab-pane" id="tablistAsientoContable" role="tabpanel">
                                                    <div class="row d-flex flex-wrap justify-content-center">
                                                        <div class="col-lg-2 mb-3 text-center">
                                                            <label class="form-label" for="fecha">Fecha</label>
                                                            <input type="text" class="form-control text-center" id="fecha" value="{{Carbon\Carbon::parse($venta->asientoContable->fecha)->format('d/m/Y H:i')}}" readonly>
                                                        </div>
                                                        <div class="col-lg-2 mb-3 text-center">
                                                            <label class="form-label" for="origen">Origen</label>
                                                            <input type="text" class="form-control text-center" id="origen" value="{{$venta->asientoContable->origen}}" readonly>
                                                        </div>
                                                        <div class="col-lg-1 mb-3 text-center">
                                                            <label class="form-label" for="moneda">Moneda</label>
                                                            <input type="text" class="form-control text-center" id="moneda" value="{{$venta->asientoContable->moneda->codigo}}" readonly>
                                                        </div>
                                                        <div class="col-lg-2 mb-3 text-center">
                                                            <label class="form-label" for="cotizacion">Cotización</label>
                                                            <input type="text" class="form-control text-center" id="cotizacion" @if ($venta->asientoContable->cotizacion_id) value="{{number_format($venta->asientoContable->cotizacion->venta, 0, ',', '.')}}" @else value="-----" @endif readonly>
                                                        </div>
                                                        <div class="col-lg-1 mb-3 text-center">
                                                            <label class="form-label" for="acciones">Acción</label>
                                                            <div class="text-center mt-1">
                                                                <a type="button" class="btn btn-sm btn-primary" href="{{route('asientos_contables.show', $venta->asientoContable->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver Asiento Completo"><i class="ri-eye-fill"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    @foreach ($venta->asientoContable->detalles as $key => $asiento)
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
                                                            <input type="text" class="form-control text-center" id="total_debe" @if ($venta->asientoContable->detalles->sum('debe') > 0) value="{{number_format($venta->asientoContable->detalles->sum('debe'), 0, ',', '.')}}" @else value="-----" @endif readonly>
                                                        </div>
                                                        <div class="col-lg-2 mb-3 text-center">
                                                            <label class="form-label" for="debe">Total Haber</label>
                                                            <input type="text" class="form-control text-center" id="debe" @if ($venta->asientoContable->detalles->sum('haber')) value="{{number_format($venta->asientoContable->detalles->sum('haber'), 0, ',', '.')}}" @else value="-----" @endif readonly>
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
                                                            <input type="text" class="form-control text-center" id="diferencia" value="{{number_format(($venta->asientoContable->detalles->sum('debe') - $venta->asientoContable->detalles->sum('haber')), 0, ',', '.')}}" readonly>
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
                            {{$venta->cargadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($venta->created_at)->format('d/m/Y H:i:s')}}
                        </div>
                        @if ($venta->anulado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="anulado_por">Anulado por:</label>
                                <br>
                                {{$venta->anuladoPor->name}}, en fecha: {{\Carbon\Carbon::parse($venta->updated_at)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('ventas.index')}}">Volver</a>
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
