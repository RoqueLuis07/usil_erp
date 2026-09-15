@foreach ($carreras as $carrera)
    <!-- showModal -->
    <div class="modal fade flip" id="showModal-{{$carrera->id}}" tabindex="-1" aria-labelledby="showModal-{{$carrera->id}}" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title" id="showModal-{{$carrera->id}}">Ver Carrera</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <label class="form-label" for="nombre_fantasia">Nombre Fantasía</label>
                            <input type="text" class="form-control" id="nombre_fantasia" value="{{$carrera->nombre_fantasia}}" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <label class="form-label" for="nombre_real">Nombre Real</label>
                            <input type="text" class="form-control" id="nombre_real" value="{{$carrera->nombre_real}}" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="programa">Programa</label>
                            <input type="text" class="form-control" id="programa" value="{{$carrera->programa->nombre}}" readonly>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="abreviatura">Abreviatura</label>
                            <input type="text" class="form-control" id="abreviatura" value="{{$carrera->abreviatura}}" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <label class="form-label" for="facultad">Facultad</label>
                            <input type="text" class="form-control" id="facultad" value="{{$carrera->facultad->nombre}}" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="cantidad_semestres">Cantidad de Semestres</label>
                            <input type="text" class="form-control" id="cantidad_semestres" value="{{$carrera->cantidad_semestres}}" readonly>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="modalidad">Modalidad</label>
                            <input type="text" class="form-control" id="modalidad" value="{{$carrera->modalidad->nombre}}" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 mb-3">
                            <label class="form-label" for="numero_ley">N° de Ley</label>
                            <input type="text" class="form-control" id="numero_ley" value="{{$carrera->numero_ley}}" readonly>
                        </div>
                        <div class="col-lg-3 mb-3">
                            <label class="form-label" for="numero_acta">N° de Acta</label>
                            <input type="text" class="form-control" id="numero_acta" value="{{$carrera->numero_acta}}" readonly>
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="numero_resolucion_cones">N° de Res. CONES</label>
                            <input type="text" class="form-control" id="numero_resolucion_cones" value="{{$carrera->numero_resolucion_cones}}" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="tipo_carrera">Tipo de Carrera</label>
                            <input type="text" class="form-control" id="tipo_carrera" value="{{$carrera->tipoCarrera->nombre}}" readonly>
                        </div>
                        <div class="col-lg-3 mb-3">
                            <label class="form-label" for="doble_grado">Doble Grado</label>
                            <div class="form-check form-check-success align-center">
                                <input type="checkbox" class="form-check-input" @if ($carrera->doble_grado == true) checked @endif disabled>
                            </div>
                        </div>
                        <div class="col-lg-3 mb-3">
                            <label class="form-label" for="estado">Estado</label>
                            <input type="text" class="form-control" id="estado" @if ($carrera->estado == 'AC') value="ACTIVO" @elseif ($carrera->estado == 'IN') value="INACTIVO" @endif readonly>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div class="col-lg-5 mb-3">
                            <label class="form-label" for="cargado_por">Cargado por:</label>
                            <br>
                            <textarea class="form-control" cols="30" rows="3" readonly>{{$carrera->cargadoPor->name}} {{\Carbon\Carbon::parse($carrera->created_at)->format('d/m/Y H:i:s')}}</textarea>
                        </div>
                        @if ($carrera->actualizado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="cargado_por">Última actualización hecha por:</label>
                                <br>
                                <textarea class="form-control" cols="30" rows="3" readonly>{{$carrera->actualizadoPor->name}} {{\Carbon\Carbon::parse($carrera->updated_at)->format('d/m/Y H:i:s')}}</textarea>
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
        @can('inactivar_carreras')
            <div class="modal fade flip" id="unactivateModal-{{$carrera->id}}" tabindex="-1" aria-labelledby="unactivateModal-{{$carrera->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form id="unactivate-form-{{$carrera->id}}" action="{{route('carreras.unactivate', $carrera->id)}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de inactivar la carrera {{$carrera->nombre_fantasia}}?</h4>
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
        @can('activar_carreras')
            <div class="modal fade flip" id="activateModal-{{$carrera->id}}" tabindex="-1" aria-labelledby="activateModal-{{$carrera->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#0a5c15,secondary:#30e849" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form id="activate-form-{{$carrera->id}}" action="{{route('carreras.activate', $carrera->id)}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de activar la carrera {{$carrera->nombre_fantasia}}?</h4>
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
        @can('eliminar_carreras')
            <div class="modal fade flip" id="destroyModal-{{$carrera->id}}" tabindex="-1" aria-labelledby="destroyModal-{{$carrera->id}}" role="dialog">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                                </div>
                            </div>
                            <form id="destroy-form-{{$carrera->id}}" action="{{route('carreras.destroy', $carrera->id)}}" method="delete">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12 mb-3 text-center">
                                        <h4>¿Está seguro de eliminar la carrera {{$carrera->nombre_fantasia}}?</h4>
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
