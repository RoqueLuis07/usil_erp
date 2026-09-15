@foreach ($docentes as $docente)
    <!-- unactivateModal -->
        @can('inactivar_docentes')
            <div class="modal fade flip" id="unactivateModal-{{$docente->id}}" tabindex="-1" aria-labelledby="unactivateModal-{{$docente->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form id="unactivate-form-{{$docente->id}}" action="{{route('docentes.unactivate', $docente->id)}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de inactivar el docente {{$docente->primer_nombre}} {{$docente->primer_apellido}}?</h4>
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
        @can('activar_docentes')
            <div class="modal fade flip" id="activateModal-{{$docente->id}}" tabindex="-1" aria-labelledby="activateModal-{{$docente->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#0a5c15,secondary:#30e849" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form id="activate-form-{{$docente->id}}" action="{{route('docentes.activate', $docente->id)}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de activar el docente {{$docente->primer_nombre}} {{$docente->primer_apellido}}?</h4>
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
        @can('eliminar_docentes')
            <div class="modal fade flip" id="destroyModal-{{$docente->id}}" tabindex="-1" aria-labelledby="destroyModal-{{$docente->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form id="destroy-form-{{$docente->id}}" action="{{route('docentes.destroy', $docente->id)}}" method="delete">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de eliminar el docente {{$docente->primer_nombre}} {{$docente->primer_apellido}}?</h4>
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

    <!-- reporteHorasModal -->
        {{-- @can('ver_reportes_horas_docentes') --}}
            <div class="modal fade flip" id="reporteHorasModal-{{$docente->id}}" tabindex="-1" aria-labelledby="reporteHorasModal-{{$docente->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-light p-3">
                            <h5 class="modal-title" id="reporteHorasModal-{{$docente->id}}">Generar Reporte de Horas</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="reporte_horas-form-{{$docente->id}}" action="{{route('docentes.show_horas', $docente->id)}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-6 mb-3 text-center">
                                        <label class="form-label" for="fecha_inicio">Fecha de Inicio <span class="text-danger">(*)</span></label>
                                        <div class="form-icon right">
                                            <input type="text" class="flatpickr form-control form-control-icon text-center @error('fecha_inicio') is-invalid @enderror" id="fecha_inicio" name="fecha_inicio" value="{{old('fecha_inicio')}}" placeholder="Seleccionar...">
                                            <i class="ri-calendar-2-line" id="calendar-icon"></i>
                                            @error('fecha_inicio')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-3 text-center">
                                        <label class="form-label" for="fecha_fin">Fecha de Fin <span class="text-danger">(*)</span></label>
                                        <div class="form-icon right">
                                            <input type="text" class="flatpickr form-control form-control-icon text-center @error('fecha_fin') is-invalid @enderror" id="fecha_fin" name="fecha_fin" value="{{old('fecha_fin')}}" placeholder="Seleccionar...">
                                            <i class="ri-calendar-2-line" id="calendar-icon"></i>
                                            @error('fecha_fin')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{$message}}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 mb-3 text-center">
                                        <label class="form-label" for="monto_virtual">Monto Virtual</label>
                                        <input type="text" class="form-control text-center monto @error('monto_virtual') is-invalid @enderror" id="monto_virtual-{{ $docente->id }}" name="monto_virtual" value="{{ old('monto_virtual') }}" data-id="{{ $docente->id }}">
                                        @error('monto_virtual')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-4 mb-3 text-center">
                                        <label class="form-label" for="monto_teams">Monto Teams</label>
                                        <input type="text" class="form-control text-center monto @error('monto_teams') is-invalid @enderror" id="monto_teams-{{ $docente->id }}" name="monto_teams" value="{{ old('monto_teams') }}" data-id="{{ $docente->id }}">
                                        @error('monto_teams')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-lg-4 mb-3 text-center">
                                        <label class="form-label" for="monto_presencial">Monto Presencial</label>
                                        <input type="text" class="form-control text-center monto @error('monto_presencial') is-invalid @enderror" id="monto_presencial-{{ $docente->id }}" name="monto_presencial" value="{{ old('monto_presencial') }}" data-id="{{ $docente->id }}">
                                        @error('monto_presencial')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{$message}}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="hstack gap-2 justify-content-center">
                                        <button type="submit" class="btn btn-danger" data-bs-dismiss="modal">Generar</button>
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        {{-- @endcan --}}
    <!-- /reporteHorasModal -->
@endforeach
