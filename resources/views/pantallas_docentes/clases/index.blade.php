@can('ver_clases_docentes_pantalla')
    @extends('layouts.master')
    @section('title') Clases Generadas @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('title') BIENVENIDO, {{$docente->primer_nombre}} {{$docente->primer_apellido}} @endslot
        @endcomponent

        @include('pantallas_docentes.scripts.messages-scripts')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Clases Generadas</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="clases-list">
                            <div class="row g-4 mb-3">
                                <div class="row g-4 mb-3">
                                    <div class="col-lg-12">
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <div class="input-group">
                                                    <select class="selectpicker form-control" id="filtro_materia" data-live-search="true">
                                                        <option value="" selected disabled>Filtrar por Materia...</option>
                                                        @foreach ($materias as $materia)
                                                            <option value="{{$materia}}">{{$materia}}</option>
                                                        @endforeach
                                                    </select>
                                                    <button class="btn btn-sm btn-outline-danger" id="delete-materia-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Filtro por Período"><i class="ri-close-line"></i></button>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="input-group">
                                                    <select class="selectpicker form-control" id="filtro_modalidad" data-live-search="true">
                                                        <option value="" selected disabled>Filtrar por Modalidad...</option>
                                                        @foreach ($modalidades as $modalidad)
                                                            <option value="{{$modalidad}}">{{$modalidad}}</option>
                                                        @endforeach
                                                    </select>
                                                    <button class="btn btn-sm btn-outline-danger" id="delete-modalidad-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Filtro por Período"><i class="ri-close-line"></i></button>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="d-flex justify-content-sm-end">
                                                    <div class="search-box ms-2">
                                                        <input type="text" class="form-control search" placeholder="Buscar...">
                                                        <i class="ri-search-line search-icon"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive table-card mt-3 mb-1">
                                <table class="table align-middle table-nowrap text-center" id="clases-list">
                                    <thead class="table-light">
                                        <tr>
											<th>N° Clase</th>
                                            <th class="sort" data-sort="fecha">Fecha y Hora</th>
                                            <th class="sort" data-sort="materia">Materia</th>
                                            <th class="sort" data-sort="modalidad">Modalidad</th>
                                            <th>Tema Desarrollado</th>
                                            <th>Horas de Desarrollo</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
										@php
											$materia_anterior = null;
											$contador_clase = 0;
										@endphp

                                        @forelse ($clases as $key => $clase)
                                            <tr>
												<td>{{$key + 1}}</td>
                                                <td class="fecha">{{\Carbon\Carbon::parse($clase->fecha_hora)->format('d/m/Y H:i')}}</td>
                                                <td class="materia">{{$clase->materia->nombre_fantasia}}</td>
                                                <td class="modalidad">{{$clase->modalidad->nombre}}</td>
                                                <td>{{$clase->tema_desarrollado}}</td>
                                                <td>{{$clase->horas_desarrollo}}</td>
                                                <td>
                                                    <a type="button" class="btn btn-sm btn-primary" href="{{route('pantallas_docentes.show_clases', $clase->id)}}">Ver</a>
                                                    @if ($clase->alumnoAsistencias->count() == 0)
                                                        @can('crear_asistencias_docentes_pantalla')
                                                            <a type="button" class="btn btn-sm btn-success" href="{{route('pantallas_docentes.charge_asistencias', ['usuario' => Auth::id(), 'materia' => $clase->materia_id, 'semestre' => $clase->semestre_id])}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Agregar Asistencias"><i class="ri-add-fill align-middle"></i> Asistencias</a>
                                                        @endcan
                                                    @endif
                                                </td>
                                            </tr>
											@php $materia_anterior = $clase->materia_id; @endphp
                                        @empty
                                            <tr class="text-center">
                                                <td colspan="7">No tienes clases generadas actualmente.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <div class="noresults" style="display: none">
                                    <div class="text-center">
                                        <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" estado="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                        <h5 class="mt-2">Sin resultados.</h5>
                                        <p class="text-muted mb-0">No pudimos encontrar ninguna clase según tus parámetros de búsqueda.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
								<div class="col-lg-6">
									<span class="text-muted" id="mostrando"></span>
										<br>
									<span class="text-muted">Total: {{$clases->count()}}</span>
								</div>
								<div class="col-lg-6 d-flex justify-content-end">
									<div class="pagination-wrap hstack gap-2">
										<a class="page-item pagination-prev disabled"><</a>
										<ul class="pagination listjs-pagination mb-0"></ul>
										<a class="page-item pagination-next">></a>
									</div>
								</div>
							</div>
                        </div>
                    </div><!-- end card -->
                </div>
                <!-- end col -->
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/prismjs/prism.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        @include('pantallas_docentes.clases.scripts.index-scripts')
    @endsection
@endcan
