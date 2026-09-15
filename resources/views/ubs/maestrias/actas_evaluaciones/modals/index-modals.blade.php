<!-- addActaModal -->
    @can('crear_actas_maestrias_ubs')
        <div class="modal fade flip" id="addActaModal" tabindex="-1" aria-labelledby="addActaModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h4 class="modal-title">Agregar Acta de Evaluación</h4>
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
<!-- /addActaModal -->

@foreach ($actas_evaluaciones as $acta_evaluacion)
    <!-- destroyModal -->
        @can('eliminar_actas_maestrias_ubs')
            <div class="modal fade flip" id="destroyModal-{{$acta_evaluacion->id}}" tabindex="-1" aria-labelledby="destroyModal-{{$acta_evaluacion->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form id="destroy-form-{{$acta_evaluacion->id}}" action="{{route('actas_evaluaciones_ubs.destroy', $acta_evaluacion->id)}}" method="delete">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de eliminar el acta de evaluación N° {{str_pad($acta_evaluacion->numero_acta, 7, '0', STR_PAD_LEFT)}} del módulo {{$acta_evaluacion->modulo->nombre_fantasia}} de {{$acta_evaluacion->curso->nombre_fantasia}}?</h4>
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
