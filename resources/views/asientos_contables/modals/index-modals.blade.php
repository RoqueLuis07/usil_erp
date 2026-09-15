@foreach ($asientos as $detalle)
    <!-- destroyModal -->
        @can('eliminar_asientos_contables')
            <div class="modal fade flip" id="destroyModal-{{$detalle->id}}" tabindex="-1" aria-labelledby="destroyModal-{{$detalle->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form id="destroy-form-{{$detalle->id}}" action="{{route('asientos_contables.destroy', $detalle->asiento->id)}}" method="delete">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de eliminar el asiento contable N° {{$detalle->id}}?</h4>
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

<!-- renumerarModal -->
{{-- @can('renumerar_asientos_contables') --}}
    <div class="modal fade flip" id="renumerarModal" tabindex="-1" aria-labelledby="renumerarModal" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="renumerar-form" action="{{route('asientos_contables.renumerar')}}" method="post">
                    @csrf
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title" id="renumerarModal">Renumerar Asientos Contables</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 mb-3 text-center">
                                <label class="form-label" for="fecha">Fecha<span class="text-danger">(*)</span></label>
                                <div class="form-icon right">
                                    <input type="text" class="flatpickr form-control form-control-icon @error('fecha') is-invalid @enderror" id="fecha" name="fecha" value="{{old('fecha')}}" placeholder="Seleccionar...">
                                    <i class="ri-calendar-2-line" id="calendar-icon"></i>
                                    @error('fecha')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="cancel-btn">Cerrar</button>
                        <button type="button" class="btn btn-success" id="renumerar-btn" data-url="{{ route('asientos_contables.renumerar') }}">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
{{-- @endcan --}}
<!-- /renumerarModal -->
