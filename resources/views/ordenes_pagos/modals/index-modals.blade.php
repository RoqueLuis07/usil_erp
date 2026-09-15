@foreach ($ordenes_pagos as $orden_pago)
    <!-- payModal -->
        @can('pagar_pagos_ordenes')
            @if ($orden_pago->pago)
                <div class="modal fade flip" id="payModal-{{$orden_pago->id}}" tabindex="-1" aria-labelledby="payModal-{{$orden_pago->id}}" role="dialog">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#0a5c15,secondary:#30e849" style="width:150px;height:150px"></lord-icon>
                                    </div>
                                </div>
                                <form id="pay-form-{{$orden_pago->id}}" action="{{route('pagos.store', $orden_pago->pago->id)}}" method="post">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-12 mb-3 text-center">
                                            <h4>¿Está seguro de realizar el pago de la OP N° {{$orden_pago->id}} del proveedor {{$orden_pago->proveedor->razon_social}}?</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="hstack gap-2 justify-content-center">
                                            <button type="submit" class="btn btn-success" data-bs-dismiss="modal">Sí, realizar!</button>
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endcan
    <!-- /payModal -->

    <!-- unactivateModal -->
        @can('inactivar_pagos_ordenes')
            <div class="modal fade flip" id="unactivateModal-{{$orden_pago->id}}" tabindex="-1" aria-labelledby="unactivateModal-{{$orden_pago->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form id="unactivate-form-{{$orden_pago->id}}" action="{{route('ordenes_pagos.unactivate', $orden_pago->id)}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de anular la OP N° {{$orden_pago->id}} del proveedor {{$orden_pago->proveedor->razon_social}}?</h4>
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

    <!-- activateModal -->
        @can('activar_pagos_ordenes')
            <div class="modal fade flip" id="activateModal-{{$orden_pago->id}}" tabindex="-1" aria-labelledby="activateModal-{{$orden_pago->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#0a5c15,secondary:#30e849" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form id="activate-form-{{$orden_pago->id}}" action="{{route('ordenes_pagos.activate', $orden_pago->id)}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de desanular la OP N° {{$orden_pago->id}} del proveedor {{$orden_pago->proveedor->razon_social}}?</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="hstack gap-2 justify-content-center">
                                        <button type="submit" class="btn btn-success" data-bs-dismiss="modal">Sí, desanular!</button>
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    <!-- /activateModal -->

    <!-- unapproveModal -->
        @can('desaprobar_pagos_ordenes')
            <div class="modal fade flip" id="unapproveModal-{{$orden_pago->id}}" tabindex="-1" aria-labelledby="unapproveModal-{{$orden_pago->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form id="unapprove-form-{{$orden_pago->id}}" action="{{route('ordenes_pagos.unapprove', $orden_pago->id)}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de desaprobar la OP N° {{$orden_pago->id}} del proveedor {{$orden_pago->proveedor->razon_social}}?</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="hstack gap-2 justify-content-center">
                                        <button type="submit" class="btn btn-danger" data-bs-dismiss="modal">Sí, desaprobar!</button>
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

    <!-- approveModal -->
        @can('aprobar_pagos_ordenes')
            <div class="modal fade flip" id="approveModal-{{$orden_pago->id}}" tabindex="-1" aria-labelledby="approveModal-{{$orden_pago->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#0a5c15,secondary:#30e849" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form id="approve-form-{{$orden_pago->id}}" action="{{route('ordenes_pagos.approve', $orden_pago->id)}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de aprobar la OP N° {{$orden_pago->id}} del proveedor {{$orden_pago->proveedor->razon_social}}?</h4>
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

    <!-- destroyModal -->
        @can('eliminar_pagos_ordenes')
            <div class="modal fade flip" id="destroyModal-{{$orden_pago->id}}" tabindex="-1" aria-labelledby="destroyModal-{{$orden_pago->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form id="destroy-form-{{$orden_pago->id}}" action="{{route('ordenes_pagos.destroy', $orden_pago->id)}}" method="delete">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de eliminar la OP N° {{$orden_pago->id}} del proveedor {{$orden_pago->proveedor->razon_social}}?</h4>
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
