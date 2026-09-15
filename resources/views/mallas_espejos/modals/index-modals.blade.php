@foreach ($mallas_espejos as $malla_espejo)
    <!-- unactivateModal -->
        @can('inactivar_mallas_espejo')
            <div class="modal fade flip" id="unactivateModal-{{$malla_espejo->id}}" tabindex="-1" aria-labelledby="unactivateModal-{{$malla_espejo->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form id="unactivate-form-{{$malla_espejo->id}}" action="{{route('mallas_espejos.unactivate', $malla_espejo->id)}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de inactivar la malla espejo {{$malla_espejo->mallaParaguay->carrera->nombre_fantasia}} - {{$malla_espejo->mallaSiu->carrera->nombre_fantasia}}?</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="hstack gap-2 justify-content-center">
                                        <button type="submit" class="btn btn-danger" data-bs-dismiss="modal">Sí, inactivar!</button>
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
        @can('activar_mallas_espejo')
            <div class="modal fade flip" id="activateModal-{{$malla_espejo->id}}" tabindex="-1" aria-labelledby="activateModal-{{$malla_espejo->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#0a5c15,secondary:#30e849" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form id="activate-form-{{$malla_espejo->id}}" action="{{route('mallas_espejos.activate', $malla_espejo->id)}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de activar la malla espejo {{$malla_espejo->mallaParaguay->carrera->nombre_fantasia}} - {{$malla_espejo->mallaSiu->carrera->nombre_fantasia}}?</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="hstack gap-2 justify-content-center">
                                        <button type="submit" class="btn btn-success" data-bs-dismiss="modal">Sí, activar!</button>
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

    <!-- destroyModal -->
        @can('eliminar_mallas_espejo')
            <div class="modal fade flip" id="destroyModal-{{$malla_espejo->id}}" tabindex="-1" aria-labelledby="destroyModal-{{$malla_espejo->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form id="destroy-form-{{$malla_espejo->id}}" action="{{route('mallas_espejos.destroy', $malla_espejo->id)}}" method="delete">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de eliminar la malla espejo {{$malla_espejo->mallaParaguay->carrera->nombre_fantasia}} - {{$malla_espejo->mallaSiu->carrera->nombre_fantasia}}?</h4>
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
