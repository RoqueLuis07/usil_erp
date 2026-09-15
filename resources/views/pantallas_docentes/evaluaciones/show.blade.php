@can('crear_asistencias_docentes_pantalla')
    @extends('layouts.master')
    @section('title') Puntajes Alumnos @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('title') BIENVENIDO, {{$docente->primer_nombre}} {{$docente->primer_apellido}} @endslot
        @endcomponent

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <h4 class="card-title mb-0">Puntajes Alumnos</h4>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="materia">Materia</label>
                                    <input type="text" class="form-control" id="materia" value="{{$materia->nombre_fantasia}}" readonly>
                                </div>
                                <div class="col-lg-8 mb-3">
									<div class="row d-flex justify-content-end">
										<div class="col-lg-2 text-center">
											<label class="form-label" for="periodo">Período</label>
											<input type="text" class="form-control text-center" id="periodo" value="{{$semestre->nombre}}" readonly>
										</div>
									</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div id="asistencias-list">
                                    <div class="card-header d-flex flex-wrap justify-content-between">
                                        <div class="col-lg-8">
                                            <h4 class="card-title mb-0">Puntajes</h4>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="d-flex justify-content-sm-end">
                                                <div class="search-box ms-2">
                                                    <input type="text" class="form-control search" placeholder="Buscar...">
                                                    <i class="ri-search-line search-icon"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive table-card mt-3 mb-1">
                                            <table class="table align-middle table-nowrap text-center" id="asistencias-list">
                                                <thead class="table-light">
                                                    <tr>
														<th>Alumno</th>
														<th>N° Documento</th>
                                                        @foreach ($evaluaciones as $evaluacion)
															<th>{{Str::title($evaluacion->nombre)}}</th>
														@endforeach
														<th>Calificación Final</th>
													</tr>
                                                </thead>
                                                <tbody class="list form-check-all">
                                                    @foreach($puntajes as $alumno_id => $puntajes_alumnos)
														@php
															$alumno = $puntajes_alumnos->first()->alumno;
															$puntajes_map = $puntajes_alumnos->pluck('puntos_obtenidos', 'evaluacion_id');
															$calificaciones_map = $puntajes_alumnos->pluck('calificacion', 'evaluacion_id');
														@endphp
														<tr>
															<td>{{ $alumno->primer_nombre }} {{ $alumno->primer_apellido }}</td>
															<td>{{ $alumno->numero_documento }}</td>
															
															@foreach ($evaluaciones as $evaluacion)
																<td>{{ $puntajes_map[$evaluacion->id] ?? '' }}</td>
															@endforeach
															
															<td>
															@foreach ($evaluaciones as $evaluacion)
																	@if (!is_null($calificaciones_map[$evaluacion->id] ?? null))
																		{{ $calificaciones_map[$evaluacion->id] ?? '' }}
																	@endif
															@endforeach
															</td>
														</tr>
													@endforeach
                                                </tbody>
                                            </table>
                                            <div class="noresults" style="display: none">
                                                <div class="text-center">
                                                    <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" estado="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                                    <h5 class="mt-2">Sin resultados.</h5>
                                                    <p class="text-muted mb-0">No pudimos encontrar ningún alumno según tus parámetros de búsqueda.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <div class="pagination-wrap hstack gap-2">
                                                <a class="page-item pagination-prev disabled"><</a>
                                                <ul class="pagination listjs-pagination mb-0"></ul>
                                                <a class="page-item pagination-next">></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('pantallas_docentes.puntajes_alumnos', Auth::id())}}">Volver</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
    @endsection
@endcan
