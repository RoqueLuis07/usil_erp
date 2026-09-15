@foreach ($tipos_extensiones as $tipo_extension)
    <!-- showModal -->
    <div class="modal fade flip" id="showModal-{{$tipo_extension->id}}" tabindex="-1" aria-labelledby="showModal-{{$tipo_extension->id}}" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title" id="showModal-{{$tipo_extension->id}}">Ver Tipo de Extensión Universitaria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <label class="form-label" for="nombre">Nombre</label>
                            <input type="text" class="form-control" id="nombre" value="{{$tipo_extension->nombre}}" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <label class="form-label" for="maxima_cantidad_horas">Max. Horas</label>
                            @php
                                if ($tipo_extension->maxima_cantidad_horas != 1) {
                                    $texto = 'horas';
                                } else {
                                    $texto = 'hora';
                                }
                            @endphp
                            <input type="text" class="form-control" id="maxima_cantidad_horas" value="{{number_format($tipo_extension->maxima_cantidad_horas, 2, ',', '.')}} {{$texto}}" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <label class="form-label" for="estado">Estado</label>
                            <input type="text" class="form-control" id="estado" @if ($tipo_extension->estado == 'AC') value="ACTIVO" @elseif ($tipo_extension->estado == 'IN') value="INACTIVO" @endif readonly>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div class="col-lg-5 mb-3">
                            <label class="form-label" for="cargado_por">Cargado por:</label>
                            <br>
                            <textarea class="form-control" cols="30" rows="3" readonly>{{$tipo_extension->cargadoPor->name}} {{\Carbon\Carbon::parse($tipo_extension->created_at)->format('d/m/Y H:i:s')}}</textarea>
                        </div>
                        @if ($tipo_extension->actualizado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="cargado_por">Última actualización hecha por:</label>
                                <br>
                                <textarea class="form-control" cols="30" rows="3" readonly>{{$tipo_extension->actualizadoPor->name}} {{\Carbon\Carbon::parse($tipo_extension->updated_at)->format('d/m/Y H:i:s')}}</textarea>
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
        @can('inactivar_tipos_extensiones_universitarias')
            <div class="modal fade flip" id="unactivateModal-{{$tipo_extension->id}}" tabindex="-1" aria-labelledby="unactivateModal-{{$tipo_extension->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form id="unactivate-form-{{$tipo_extension->id}}" action="{{route('tipos_extensiones_universitarias.unactivate', $tipo_extension->id)}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de inactivar el tipo de extensión {{$tipo_extension->nombre}}?</h4>
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
        @can('activar_tipos_extensiones_universitarias')
            <div class="modal fade flip" id="activateModal-{{$tipo_extension->id}}" tabindex="-1" aria-labelledby="activateModal-{{$tipo_extension->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#0a5c15,secondary:#30e849" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form id="activate-form-{{$tipo_extension->id}}" action="{{route('tipos_extensiones_universitarias.activate', $tipo_extension->id)}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de activar el tipo de extensión {{$tipo_extension->nombre}}?</h4>
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
        @can('eliminar_tipos_extensiones_universitarias')
            <div class="modal fade flip" id="destroyModal-{{$tipo_extension->id}}" tabindex="-1" aria-labelledby="destroyModal-{{$tipo_extension->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form id="destroy-form-{{$tipo_extension->id}}" action="{{route('tipos_extensiones_universitarias.destroy', $tipo_extension->id)}}" method="delete">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de eliminar el tipo de extensión {{$tipo_extension->nombre}}?</h4>
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
