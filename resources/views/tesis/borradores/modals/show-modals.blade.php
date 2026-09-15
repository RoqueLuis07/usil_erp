@foreach ($tema->borradores as $borrador)
    <!-- approveModal -->
    <div class="modal fade flip" id="approveModal-{{$borrador->id}}" tabindex="-1" aria-labelledby="approveModal-{{$borrador->id}}" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 mb-3 text-center">
                            <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#0a5c15,secondary:#30e849" style="width:150px;height:150px"></lord-icon>
                        </div>
                    </div>
                    <form action="{{route('borradores_tesis.aprobar', $borrador->id)}}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-lg-12 mb-3 text-center">
                                <h4>¿Está seguro de aprobar la entrega {{$borrador->bloque->nombre}} ?</h4>
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
    <!-- /approveModal -->

    <!-- desapproveModal -->
    <div class="modal fade flip" id="desapproveModal-{{$borrador->id}}" tabindex="-1" aria-labelledby="desapproveModal-{{$borrador->id}}" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 mb-3 text-center">
                            <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                        </div>
                    </div>
                    <form action="{{route('borradores_tesis.anular_aprobacion', $borrador->id)}}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-lg-12 mb-3 text-center">
                                <h4>¿Está seguro de anular la aprobación de la entrega {{$borrador->bloque->nombre}} ?</h4>
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
    <!-- /desapproveModal -->
@endforeach
