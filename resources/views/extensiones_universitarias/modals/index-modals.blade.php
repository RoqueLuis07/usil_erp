<div class="modal modal-lg fade flip" id="showReporteModal" tabindex="-1" aria-labelledby="showReporteModal" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light p-3">
                <h5 class="modal-title" id="showReporteModal">Visualizar Reporte de Extensión Universitaria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{route('extensiones_universitarias.show_reporte')}}" method="get" id="showReporte-form">
                    @csrf
                    <div class="row">
                        <div class="col-lg-9 mb-3">
                            <label class="form-label" for="proyecto">Proyecto</label>
                            <select class="selectpicker form-control" id="proyecto" name="proyecto" data-live-search="true">
                                <option value="" selected disabled>Seleccionar...</option>
                                @foreach ($extensiones as $extension)
                                    <option value="{{ $extension->id }}">{{ $extension->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3 mb-3">
                            <label class="form-label" for="periodo">Período</label>
                            <select class="selectpicker form-control" id="periodo" name="periodo" data-live-search="true">
                                <option value="" selected disabled>Seleccionar...</option>
                                @foreach ($periodos as $periodo)
                                    <option value="{{ $periodo }}">{{ $periodo}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="tipo_extension">Tipo de Actividad</label>
                            <select class="selectpicker form-control" id="tipo_extension" name="tipo_extension" data-live-search="true">
                                <option value="" selected disabled>Seleccionar...</option>
                                @foreach ($tipos_extensiones as $tipo)
                                    <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="alumno">Alumno</label>
                            <select class="selectpicker form-control" id="alumno" name="alumno" data-live-search="true">
                                <option value="" selected disabled>Seleccionar...</option>
                                @foreach ($alumnos as $alumno)
                                    <option value="{{ $alumno->id }}" data-subtext="{{$alumno->numero_documento}}">{{ $alumno->primer_nombre }} {{$alumno->primer_apellido}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="cancel-btn">Cerrar</button>
                <button type="button" class="btn btn-warning" id="show-reporte-btn">Visualizar</button>
            </div>
        </div>
    </div>
</div>

@foreach ($extensiones as $extension)
    <!-- approveModal -->
        @can('aprobar_extensiones_universitarias')
            <div class="modal fade flip" id="approveModal-{{$extension->id}}" tabindex="-1" aria-labelledby="approveModal-{{$extension->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#0a5c15,secondary:#30e849" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form id="approve-form-{{$extension->id}}" action="{{route('extensiones_universitarias.approve', $extension->id)}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de aprobar la extensión universitaria {{$extension->nombre}}?</h4>
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
        @can('anular_aprobacion_extensiones_universitarias')
            <div class="modal fade flip" id="unapproveModal-{{$extension->id}}" tabindex="-1" aria-labelledby="unapproveModal-{{$extension->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form id="unapprove-form-{{$extension->id}}" action="{{route('extensiones_universitarias.unapprove', $extension->id)}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de desaprobar la extensión universitaria {{$extension->nombre}}?</h4>
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

    <!-- rejectModal -->
        @can('rechazar_extensiones_universitarias')
            <div class="modal fade flip" id="rejectModal-{{$extension->id}}" tabindex="-1" aria-labelledby="rejectModal-{{$extension->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form id="reject-form-{{$extension->id}}" action="{{route('extensiones_universitarias.reject', $extension->id)}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de rechazar la extensión universitaria {{$extension->nombre}}?</h4>
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
        @can('anular_rechazo_extensiones_universitarias')
            <div class="modal fade flip" id="unrejectModal-{{$extension->id}}" tabindex="-1" aria-labelledby="unrejectModal-{{$extension->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#0a5c15,secondary:#30e849" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form id="reject-form-{{$extension->id}}" action="{{route('extensiones_universitarias.unreject', $extension->id)}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de anular el rechazo de la extensión universitaria {{$extension->nombre}}?</h4>
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

    <!-- finishModal -->
        @can('finalizar_extensiones_universitarias')
            <div class="modal fade flip" id="finishModal-{{$extension->id}}" tabindex="-1" aria-labelledby="finishModal-{{$extension->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#0a5c15,secondary:#30e849" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form id="finish-form-{{$extension->id}}" action="{{route('extensiones_universitarias.finish', $extension->id)}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de finalizar la extensión universitaria {{$extension->nombre}}?</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="hstack gap-2 justify-content-center">
                                        <button type="submit" class="btn btn-success" data-bs-dismiss="modal">Sí, finalizar!</button>
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    <!-- /finishModal -->

    <!-- destroyModal -->
        @can('eliminar_extensiones_universitarias')
            <div class="modal fade flip" id="destroyModal-{{$extension->id}}" tabindex="-1" aria-labelledby="destroyModal-{{$extension->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form id="destroy-form-{{$extension->id}}" action="{{route('extensiones_universitarias.destroy', $extension->id)}}" method="delete">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de eliminar el tipo de extensión {{$extension->nombre}}?</h4>
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
