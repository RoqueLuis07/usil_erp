@foreach ($centro_costo->subcentrosCostosContables as $detalle)
    <!-- changeEstado -->
        @can('inactivar_subcentros_costos_contables')
            <div class="modal fade flip" id="changeEstado-{{$detalle->id}}" tabindex="-1" aria-labelledby="changeEstado-{{$detalle->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    @if ($detalle->estado == 'AC')
                                        <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                                    @elseif ($detalle->estado == 'IN')
                                        <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#0a5c15,secondary:#30e849" style="width:150px;height:150px"></lord-icon>
                                    @endif
                                </div>
                            </div>
                            <form id="changeEstado-form-{{$detalle->id}}" action="{{route('subcentros_costos_contables.change_estado', $detalle->id)}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        @if ($detalle->estado == 'AC')
                                            <h4>¿Está seguro de inactivar el subcentro de costo {{$detalle->nombre}}?</h4>
                                        @elseif ($detalle->estado == 'IN')
                                            <h4>¿Está seguro de activar el subcentro de costo {{$detalle->nombre}}?</h4>
                                        @endif
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
    <!-- /changeEstado -->
@endforeach
