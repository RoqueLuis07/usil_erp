@can('ver_asientos_contables')
    @extends('layouts.master')
    @section('title') Ver Asiento Contable @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Asientos Contables @endslot
            @slot('title') Ver Asiento Contable  @endslot
        @endcomponent

        <div class="row">
            <form>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar asiento contable</h4>
                            </div>
                            @if ($asiento->venta_id || $asiento->compra_id)
                                <div class="col-lg-6 text-end">
                                    @php
                                        $nombre_boton = null;
                                        $route = null;

                                        if ($asiento->venta_id) {
                                            $nombre_boton = 'Venta';
                                        } elseif ($asiento->compra_id) {
                                            $nombre_boton = 'Compra';
                                        }
                                    @endphp
                                    <a type="button" class="btn btn-info"
                                        @if ($asiento->venta_id)
                                            href="{{route('ventas.show', $asiento->venta_id)}}"
                                        @elseif ($asiento->compra_id)
                                            href="{{route('compras.show', $asiento->compra_id)}}"
                                        @endif>
                                        Ver {{$nombre_boton}}
                                    </a>
                                </div>
                            @endif
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha">Fecha</label>
                                    <input type="text" class="form-control text-center" id="fecha" value="{{Carbon\Carbon::parse($asiento->fecha)->format('d/m/Y H:i:s')}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="id">ID</label>
                                    <input type="text" class="form-control text-center" id="id" value="{{$asiento->id}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="numero">N° Asiento</label>
                                    <input type="text" class="form-control text-center" id="numero" value="{{$asiento->numero}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="origen">Origen</label>
                                    <input type="text" class="form-control" id="origen" value="{{$asiento->origen}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="moneda">Moneda</label>
                                    <input type="text" class="form-control" id="moneda" value="{{$asiento->moneda->nombre}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="cotizacion">Cotización</label>
                                    @if ($asiento->origen == 'VENTA')
                                        <input type="text" class="form-control text-center" id="cotizacion" @if ($asiento->cotizacion_id) value="{{number_format($asiento->cotizacion->venta, 0, ',', '.')}}" @else value="-----" @endif readonly>
                                    @elseif ($asiento->origen = 'COMPRA')
                                        <input type="text" class="form-control text-center" id="cotizacion" @if ($asiento->cotizacion_id) value="{{number_format($asiento->cotizacion->compra, 0, ',', '.')}}" @else value="-----" @endif readonly>
                                    @else
                                        <input type="text" class="form-control text-center" id="cotizacion" value="-----" readonly>
                                    @endif
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="unidad_negocio">Unidad de Negocio</label>
                                    <input type="text" class="form-control" id="unidad_negocio" value="{{$asiento->unidadNegocio->nombre}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="subunidad_negocio">Subunidad de Negocio</label>
                                    <input type="text" class="form-control" id="subunidad_negocio" value="{{$asiento->subunidadNegocio->nombre}}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header mb-0">
                                    <h4 class="card-title">Detalles</h4>
                                </div>
                                <div class="card-body">
                                    @foreach ($asiento->detalles as $key => $detalle)
                                        <div class="row">
                                            <div class="col-lg-1 mb-3 text-center">
                                                @if ($key == 0) <label class="form-label" for="numero">N°</label> @endif
                                                <input type="text" class="form-control text-center" id="numero" value="{{$key + 1}}" readonly>
                                            </div>
                                            <div class="col-lg-3 mb-3 text-center">
                                                @if ($key == 0) <label class="form-label" for="cuenta_contable">Cuenta Contable</label> @endif
                                                <textarea type="text" class="form-control text-center" id="cuenta_contable" cols="30" rows="3" readonly>{{$detalle->cuentaContable->cuenta}} - {{$detalle->cuentaContable->nombre}}</textarea>
                                            </div>
                                            <div class="col-lg-3 mb-3 text-center">
                                                @if ($key == 0) <label class="form-label" for="descripcion">Descripción</label> @endif
                                                <textarea class="form-control text-center" id="descripcion" cols="30" rows="3" readonly>{{$detalle->descripcion}}</textarea>
                                            </div>
                                            <div class="col-lg-1 mb-3 text-center">
                                                @if ($key == 0) <label class="form-label" for="centro_costo">CC1</label> @endif
                                                <textarea type="text" class="form-control text-center" id="centro_costo" cols="30" rows="3" readonly>@if ($detalle->centro_costo_id) {{$detalle->centroCosto->id}} - {{$detalle->centroCosto->nombre}} @else NINGUNO @endif</textarea>
                                            </div>
                                            <div class="col-lg-2 mb-3 text-center">
                                                @if ($key == 0) <label class="form-label" for="subcentro_costo">CC2</label> @endif
                                                <textarea type="text" class="form-control text-center" id="subcentro_costo" cols="30" rows="3" readonly>@if ($detalle->subcentro_costo_id) {{$detalle->subcentroCosto->id}} - {{$detalle->subcentroCosto->nombre}} @else NINGUNO @endif</textarea>
                                            </div>
                                            <div class="col-lg-1 mb-3 text-center">
                                                @if ($key == 0) <label class="form-label" for="debe">Debe</label> @endif
                                                <input type="text" class="form-control text-center" id="debe" @if ($detalle->debe) value="{{number_format($detalle->debe, 0, ',', '.')}}" @else value="-----" @endif readonly>
                                            </div>
                                            <div class="col-lg-1 mb-3 text-center">
                                                @if ($key == 0) <label class="form-label" for="haber">Haber</label> @endif
                                                <input type="text" class="form-control text-center" id="haber" @if ($detalle->haber) value="{{number_format($detalle->haber, 0, ',', '.')}}" @else value="-----" @endif readonly>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="row d-flex-flex-wrap justify-content-end">
                                        <div class="col-lg-1 mb-3 text-center">
                                            <label class="form-label" for="diferencia">Diferencia</label>
                                            <input type="text" class="form-control text-center" id="diferencia" value="{{number_format(($asiento->detalles->sum('debe') - $asiento->detalles->sum('haber')), 0, ',', '.')}}" readonly>
                                        </div>
                                        <div class="col-lg-1 mb-3 text-center">
                                            <label class="form-label" for="total_debe">Total Debe</label>
                                            <input type="text" class="form-control text-center" id="total_debe" @if ($asiento->detalles->sum('debe') > 0) value="{{number_format($asiento->detalles->sum('debe'), 0, ',', '.')}}" @else value="-----" @endif readonly>
                                        </div>
                                        <div class="col-lg-1 mb-3 text-center">
                                            <label class="form-label" for="total_haber">Total Haber</label>
                                            <input type="text" class="form-control text-center" id="total_haber" @if ($asiento->detalles->sum('haber') > 0) value="{{number_format($asiento->detalles->sum('haber'), 0, ',', '.')}}" @else value="-----" @endif readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        @if ($asiento->cargado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="cargado_por">Cargado por:</label>
                                <br>
                                {{$asiento->cargadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($asiento->created_at)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                        @if ($asiento->actualizado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="anulado_por">Anulado por:</label>
                                <br>
                                {{$asiento->anuladoPor->name}}, en fecha: {{\Carbon\Carbon::parse($asiento->updated_at)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('asientos_contables.index')}}">Volver</a>
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
