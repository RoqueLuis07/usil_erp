@foreach ($movimientos_bancos as $movimiento)
    <!-- showModal -->
    <div class="modal fade flip" id="showModal-{{$movimiento->id}}" tabindex="-1" aria-labelledby="showModal-{{$movimiento->id}}" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title" id="showModal-{{$movimiento->id}}">Ver Movimiento de Cuenta Bancaria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="fecha">Fecha</label>
                            <input type="text" class="form-control" id="fecha" value="{{Carbon\Carbon::parse($movimiento->fecha)->format('d/m/Y H:i:s')}}" readonly>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="tipo_movimiento">Tipo de Mov.</label>
                            <input type="text" class="form-control" id="tipo_movimiento" value="{{$movimiento->tipoMovimiento->nombre}}" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-5 mb-3">
                            <label class="form-label" for="cuenta_bancaria_origen">Cuenta Origen</label>
                            <input type="text" class="form-control" id="cuenta_bancaria_origen" @if ($movimiento->cuenta_bancaria_origen_id) value="{{$movimiento->cuentaBancariaOrigen->banco->nombre}} - {{$movimiento->cuentaBancariaOrigen->numero_cuenta}}" @else value="---" @endif readonly>
                        </div>
                        <div class="col-lg-2 text-center" style="margin-top: 35px;">
                            <span>
                                @if ($movimiento->sentido == 'I')
                                    <h3 class="ri-arrow-left-line"></h3>
                                @else
                                    <h3 class="ri-arrow-right-line"></h3>
                                @endif
                            </span>
                        </div>
                        <div class="col-lg-5 mb-3">
                            <label class="form-label" for="cuenta_bancaria_destino">Cuenta Destino</label>
                            <input type="text" class="form-control" id="cuenta_bancaria_destino" @if ($movimiento->cuenta_bancaria_destino_id) value="{{$movimiento->cuentaBancariaDestino->banco->nombre}} - {{$movimiento->cuentaBancariaDestino->numero_cuenta}}" @else value="---" @endif readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="monto">Monto</label>
                            <input type="text" class="form-control" id="monto"
                                @if ($movimiento->cuenta_bancaria_origen_id)
                                    @if ($movimiento->cuentaBancariaOrigen->moneda_id == 1)
                                        value="{{number_format($movimiento->monto, 0, ',', '.')}} {{$movimiento->cuentaBancariaOrigen->moneda->codigo}}"
                                    @else
                                        value="{{number_format($movimiento->monto, 2, ',', '.')}} {{$movimiento->cuentaBancariaOrigen->moneda->codigo}}"
                                    @endif
                                @elseif ($movimiento->cuenta_bancaria_destino_id)
                                    @if ($movimiento->cuentaBancariaDestino->moneda_id == 1)
                                        value="{{number_format($movimiento->monto, 0, ',', '.')}} {{$movimiento->cuentaBancariaDestino->moneda->codigo}}"
                                    @else
                                        value="{{number_format($movimiento->monto, 2, ',', '.')}} {{$movimiento->cuentaBancariaDestino->moneda->codigo}}"
                                    @endif
                                @endif
                            readonly>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="estado">Estado</label>
                            <input type="text" class="form-control" id="estado" @if ($movimiento->estado == 'PE') value="PENDIENTE" @elseif ($movimiento->estado == 'AP') value="APROBADO" @elseif ($movimiento->estado == 'RE') value="RECHAZADO" @elseif ($movimiento->estado == 'IN') value="ANULADO" @endif readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <label class="form-label" for="motivo">Motivo</label>
                            <textarea class="form-control" id="motivo" cols="30" rows="5" readonly>{{$movimiento->motivo}}</textarea>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div class="col-lg-5 mb-3">
                            <label class="form-label" for="cargado_por">Creado por:</label>
                            <br>
                            <textarea class="form-control" cols="30" rows="3" readonly>{{$movimiento->creadoPor->name}} {{\Carbon\Carbon::parse($movimiento->created_at)->format('d/m/Y H:i:s')}}</textarea>
                        </div>
                        @if ($movimiento->aprobado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="aprobado_por">Aprobado por:</label>
                                <br>
                                <textarea class="form-control" cols="30" rows="3" readonly>{{$movimiento->aprobadoPor->name}} {{\Carbon\Carbon::parse($movimiento->updated_at)->format('d/m/Y H:i:s')}}</textarea>
                            </div>
                        @endif
                        @if ($movimiento->rechazado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="rechazado_por">Rechazado por:</label>
                                <br>
                                <textarea class="form-control" cols="30" rows="3" readonly>{{$movimiento->rechazadoPor->name}} {{\Carbon\Carbon::parse($movimiento->updated_at)->format('d/m/Y H:i:s')}}</textarea>
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

    <!-- rejectModal -->
        @can('rechazar_movimientos_cuentas')
            <div class="modal fade flip" id="rejectModal-{{$movimiento->id}}" tabindex="-1" aria-labelledby="rejectModal-{{$movimiento->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form id="reject-form-{{$movimiento->id}}" action="{{route('movimientos_bancos.reject', $movimiento->id)}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de rechazar el movimiento N° {{$movimiento->id}}?</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="hstack gap-2 justify-content-center">
                                        <button type="submit" class="btn btn-danger" data-bs-dismiss="modal">Sí, rechazar!</button>
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    <!-- /rejectModal -->

    <!-- unrejectModal -->
        @can('anular_rechazo_cuentas')
            <div class="modal fade flip" id="unrejectModal-{{$movimiento->id}}" tabindex="-1" aria-labelledby="unrejectModal-{{$movimiento->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#0a5c15,secondary:#30e849" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form id="unreject-form-{{$movimiento->id}}" action="{{route('movimientos_bancos.unreject', $movimiento->id)}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de anular el rechazo el movimiento N° {{$movimiento->id}}?</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="hstack gap-2 justify-content-center">
                                        <button type="submit" class="btn btn-success" data-bs-dismiss="modal">Sí, anular!</button>
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    <!-- /unrejectModal -->

    <!-- approveModal -->
        @can('aprobar_movimientos_cuentas')
            <div class="modal fade flip" id="approveModal-{{$movimiento->id}}" tabindex="-1" aria-labelledby="approveModal-{{$movimiento->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#0a5c15,secondary:#30e849" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form id="approve-form-{{$movimiento->id}}" action="{{route('movimientos_bancos.approve', $movimiento->id)}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de aprobar el movimiento N° {{$movimiento->id}}?</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="hstack gap-2 justify-content-center">
                                        <button type="submit" class="btn btn-success" data-bs-dismiss="modal">Sí, aprobar!</button>
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    <!-- /approveModal -->

    <!-- unapproveModal -->
        @can('anular_aprobacion_movimientos_cuentas')
            <div class="modal fade flip" id="unapproveModal-{{$movimiento->id}}" tabindex="-1" aria-labelledby="unapproveModal-{{$movimiento->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form id="unapprove-form-{{$movimiento->id}}" action="{{route('movimientos_bancos.unapprove', $movimiento->id)}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de anular la aprobación el movimiento N° {{$movimiento->id}}?</h4>
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
    <!-- /unapproveModal -->

    <!-- destroyModal -->
        @can('eliminar_movimientos_cuentas')
            <div class="modal fade flip" id="destroyModal-{{$movimiento->id}}" tabindex="-1" aria-labelledby="destroyModal-{{$movimiento->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form id="destroy-form-{{$movimiento->id}}" action="{{route('movimientos_bancos.destroy', $movimiento->id)}}" method="delete">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de eliminar el movimiento N° {{$movimiento->id}}?</h4>
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
