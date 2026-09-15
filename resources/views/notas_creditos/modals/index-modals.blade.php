@foreach ($notas_creditos as $nota_credito)
    <!-- showModal -->
        <div class="modal fade flip" id="showModal-{{$nota_credito->id}}" tabindex="-1" aria-labelledby="showModal-{{$nota_credito->id}}" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title" id="showModal-{{$nota_credito->id}}">Ver Nota de Crédito</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6 mb-3 text-center">
                                <label class="form-label" for="fecha">Fecha</label>
                                <input type="text" class="form-control text-center" id="fecha" value="{{Carbon\Carbon::parse($nota_credito->fecha)->format('d/m/Y H:i')}}" readonly>
                            </div>
                            <div class="col-lg-6 mb-3 text-center">
                                <label class="form-label" for="numero_nota_credito">N° Nota de Crédito</label>
                                <input type="text" class="form-control text-center" id="numero_nota_credito" value="{{$nota_credito->numero_nota_credito}}" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-8 mb-3 text-center">
                                <label class="form-label" for="cliente">Cliente</label>
                                <input type="text" class="form-control text-center" id="cliente" value="{{$nota_credito->cliente->nombre}}" readonly>
                            </div>
                            <div class="col-lg-4 mb-3 text-center">
                                <label class="form-label" for="documento_cliente">N° Documento</label>
                                <input type="text" class="form-control text-center" id="documento_cliente" value="{{$nota_credito->cliente->numero_documento}}" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-8 mb-3">
                                <label class="form-label" for="descripcion">Descripción</label>
                                <textarea class="form-control" id="descripcion" cols="30" rows="1" readonly>{{$nota_credito->descripcion}}</textarea>
                            </div>
                            <div class="col-lg-4 mb-3">
                                <label class="form-label" for="pago_reversado">Pago Reversado</label>
                                <input type="text" class="form-control text-center fw-bold @if ($nota_credito->pago_reversado) text-success @else text-danger @endif" id="pago_reversado" @if ($nota_credito->pago_reversado) value="SI" @else value="NO" @endif readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 mb-3 text-center">
                                <label class="form-label" for="monto">Monto Total</label>
                                <input type="text" class="form-control text-center" id="monto" value="{{number_format($nota_credito->monto_total, 0,',', '.')}}" readonly>
                            </div>
                            <div class="col-lg-6 mb-3 text-center">
                                <label class="form-label" for="estado">Estado</label>
                                <input type="text" class="form-control text-center" id="estado" @if ($nota_credito->estado == 'AC') value="ACTIVO" @elseif ($nota_credito->estado == 'IN') value="INACTIVO" @elseif ($nota_credito->estado == 'UT') value="UTILIZADO" @endif readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 mb-3 text-center">
                                <label class="form-label" for="venta">N° Factura Aplicada</label>
                                <div class="input-group">
                                    <input type="text" class="form-control text-center" id="venta" value="{{$nota_credito->venta->numero_factura}}" readonly>
                                    @can('ver_ventas')
                                        <a type="button" class="btn btn-primary" href="{{route('ventas.show', $nota_credito->venta_id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver Venta"><i class="ri-eye-fill align-bottom"></i></a>
                                    @endcan
                                </div>
                            </div>
                            <div class="col-lg-6 mb-3 text-center">
                                <label class="form-label" for="venta_cobrada">N° Factura Cobrada</label>
                                <div class="input-group">
                                    <input type="text" class="form-control text-center" id="venta_cobrada" @if ($nota_credito->estado == 'UT') value="{{$nota_credito->cobro->venta->numero_factura}}" @elseif ($nota_credito->estado == 'IN') value="N/A" @else value="PENDIENTE" @endif readonly>
                                    @if ($nota_credito->estado == 'UT')
                                        @can('ver_ventas')
                                            <a type="button" class="btn btn-primary" href="{{route('ventas.show', $nota_credito->cobro->venta_id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver Venta"><i class="ri-eye-fill align-bottom"></i></a>
                                        @endcan
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div class="col-lg-5 mb-3">
                                <label class="form-label" for="cargado_por">Creado por:</label>
                                <br>
                                <textarea class="form-control" cols="30" rows="3" readonly>{{$nota_credito->cargadoPor->name}} {{\Carbon\Carbon::parse($nota_credito->created_at)->format('d/m/Y H:i:s')}}</textarea>
                            </div>
                            @if ($nota_credito->anulado_por_id)
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="aprobado_por">Anulado por:</label>
                                    <br>
                                    <textarea class="form-control" cols="30" rows="3" readonly>{{$nota_credito->anuladoPor->name}} {{\Carbon\Carbon::parse($nota_credito->updated_at)->format('d/m/Y H:i:s')}}</textarea>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    <!-- /showModal -->

    <!-- unactivateModal -->
        @can('anular_notas_creditos')
            <div class="modal fade flip" id="unactivateModal-{{$nota_credito->id}}" tabindex="-1" aria-labelledby="unactivateModal-{{$nota_credito->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form id="unactivate-form-{{$nota_credito->id}}" action="{{route('notas_creditos.unactivate', $nota_credito->id)}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de anular la nota de crédito {{$nota_credito->numero_nota_credito}}?</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="hstack gap-2 justify-content-center">
                                        <button type="submit" class="btn btn-danger" data-bs-dismiss="modal">Sí, anular!</button>
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    <!-- /unactivateModal -->

    <!-- destroyModal -->
        @can('eliminar_notas_creditos')
            <div class="modal fade flip" id="destroyModal-{{$nota_credito->id}}" tabindex="-1" aria-labelledby="destroyModal-{{$nota_credito->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form id="destroy-form-{{$nota_credito->id}}" action="{{route('notas_creditos.destroy', $nota_credito->id)}}" method="delete">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de eliminar la nota de crédito {{$nota_credito->numero_nota_credito}}?</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="hstack gap-2 justify-content-center">
                                        <button type="submit" class="btn btn-danger" data-bs-dismiss="modal">Sí, eliminar!</button>
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    <!-- /destroyModal -->
@endforeach
