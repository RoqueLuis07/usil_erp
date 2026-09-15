@foreach ($maestria->modulos as $detalle)
        <!-- alumnosModuloModal -->
        <div class="modal modal-lg fade flip" id="alumnosModuloModal-{{$detalle->id}}" tabindex="-1" aria-labelledby="alumnosModuloModal-{{$detalle->id}}" role="dialog">
            <div class="modal-dialog modal-dialog-centered  modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h4 class="modal-title">Alumnos del {{Str::title($detalle->modulo->nombre_fantasia)}}</h4>
                    </div>
                    <div class="modal-body">
                        <form>
                            @csrf
                            <div class="row">
                                <div class="col-lg-12 mb-3">
									@forelse ($alumnos_puntajes as $ap)
										@foreach ($ap as $key => $puntaje)
											@if ($puntaje->modulo_id == $detalle->modulo_id)
												<div class="row">
													<div class="col-lg-6 mb-2 text-center">
														@if ($key == 0) <p class="fw-bold">Alumno</p> @endif
														<span>{{$puntaje->alumno->primer_nombre}} {{$puntaje->alumno->primer_apellido}}</span>
													</div>
													<div class="col-lg-3 mb-2 text-center">
														 @if ($key == 0) <p class="fw-bold">Proceso</p> @endif
														 <span>{{$puntaje->puntos_obtenidos}}</span>
													</div>
													<div class="col-lg-3 mb-2 text-end">
														@if ($key == 0) <p class="fw-bold">Acciones</p> @endif
														 <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#updatePuntajeModal-{{$puntaje->id}}">Editar</button>
													</div>
												</div>
												<hr>
											@else	
												<div class="col-lg-12 mb-2 text-center">
													@if ($key == 0) <p>El módulo no cuenta con alumnos inscriptos.</p> @endif
												</div>
											@endif
										@endforeach
									@endforeach
                                </div>
                            </div>
                            <div class="row">
                                <div class="hstack gap-2 justify-content-center">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /alumnosModuloModal -->
@endforeach

@foreach ($alumnos_puntajes as $ap)
	@foreach ($ap as $puntaje)
		<!-- updatePuntajeModal -->
        <div class="modal fade flip" id="updatePuntajeModal-{{$puntaje->id}}" tabindex="-1" aria-labelledby="updatePuntajeModal-{{$detalle->id}}" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h4 class="modal-title">Editar Puntaje</h4>
                    </div>
                    <div class="modal-body">
                        <form id="update-puntaje-form-{{$puntaje->id}}">
                            @csrf
                            <div class="row">
                                <div class="row">
									<div class="col-lg-8 mb-2 text-center">
										<label class="form-label" for="alumno-{{$puntaje->id}}">Alumno</label>
										<input type="text" class="form-control text-center" id="alumno-{{$puntaje->id}}" value="{{$puntaje->alumno->primer_nombre}} {{$puntaje->alumno->primer_apellido}}" readonly>
									</div>
									<div class="col-lg-4 mb-2 text-center">
										 <label class="form-label" for="documento_alumno-{{$puntaje->id}}">N° Documento</label>
										<input type="text" class="form-control text-center" id="documento_alumno-{{$puntaje->id}}" value="{{$puntaje->alumno->numero_documento}}" readonly>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-6 mb-2 text-center">
										 <label class="form-label" for="puntos_obtenidos-{{$puntaje->id}}">Puntaje Anterior</label>
										<input type="text" class="form-control text-center" id="puntos_obtenidos-{{$puntaje->id}}" value="{{$puntaje->puntos_obtenidos}}" readonly>
									</div>
									<div class="col-lg-6 mb-2 text-center">
										 <label class="form-label" for="puntos_nuevo-{{$puntaje->id}}">Puntaje Nuevo</label>
										 <input type="text" class="form-control text-center puntos" id="puntos_nuevo-{{$puntaje->id}}" name="puntos_obtenidos">
									</div>
								</div>
                            </div>
                            <div class="row">
                                <div class="hstack gap-2 justify-content-center">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
									<button type="button" class="btn btn-success update-puntaje-btn" data-id="{{$puntaje->id}}" data-url="{{route('alumnos_notas_ubs.update', $puntaje->id)}}">Actualizar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- /updatePuntajeModal -->
	@endforeach
@endforeach
