<!-- addClaseModal -->
    @can('crear_clases_maestrias_ubs')
        <div class="modal fade flip" id="addClaseModal" tabindex="-1" aria-labelledby="addClaseModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h4 class="modal-title">Agregar Clase</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 mb-3">
                                <input type="hidden" id="curso_id" value="{{$curso->id}}">
                                <label class="form-label" for="modulo">Módulo <span class="text-danger">(*)</span></label>
                                <select class="selectpicker form-control" id="modulo" name="modulo" data-live-search="true">
                                    <option value="" selected disabled>Seleccionar...</option>
                                    @foreach ($curso->modulos as $detalle)
                                        <option value="{{$detalle->modulo->id}}" data-subtext="{{$detalle->modulo->nombre_real}}">{{$detalle->modulo->nombre_fantasia}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="hstack gap-2 justify-content-center">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                <button type="button" class="btn btn-success" id="add-btn">Crear</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan
<!-- /addClaseModal -->

@foreach ($clases_maestrias as $clase)
    <!-- destroyModal -->
        @can('eliminar_clases_maestrias_ubs')
            <div class="modal fade flip" id="destroyModal-{{$clase->id}}" tabindex="-1" aria-labelledby="destroyModal-{{$clase->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form id="destroy-form-{{$clase->id}}" action="{{route('clases_maestrias.destroy', $clase->id)}}" method="delete">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de eliminar la clase de {{$clase->curso->nombre_fantasia}} del módulo {{$clase->modulo->nombre_fantasia}} de la fecha {{\Carbon\Carbon::parse($clase->fecha_hora)->format('d/m/Y H:i:s')}}?</h4>
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
