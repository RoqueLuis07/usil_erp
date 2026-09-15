@can('ver_docentes')
    @extends('layouts.master')
    @section('title') Docentes @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/flatpickr.min.css') }}">
        <style>
            .dropdown-menu-mas {
                background-color: #3C80E6!important;
            }
            .dropdown-item-mas {
                color: white!important;
            }
            .dropdown-item-mas:hover {
                background-color: #9EC4FE!important;
                color: black!important;
            }
        </style>
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Docentes @endslot
        @endcomponent

        @include('docentes.scripts.messages-scripts')
        @include('docentes.modals.index-modals')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Lista de Docentes</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="docentes-list">
                            <div class="row g-4 mb-3">
                                <div class="col-lg-8">
                                    @can('crear_docentes')
                                        <a type="button" class="btn btn-success" href="{{route('docentes.create')}}"><i class="ri-add-line align-bottom me-1"></i>Agregar</a>
                                    @endcan
                                </div>
                                <div class="col-lg-4">
									<div class="d-flex justify-content-sm-end">
										<form autocomplete="off" method="POST" action="{{ route('docentes.index') }}" id="buscar_form">
										@csrf
											<div class="search-box ms-2">
												<input type="text" id="buscar" name="buscar" value="{{$buscar}}" class="form-control search" placeholder="Buscar...">
                                                <input type="hidden" id="filtro_edad_input" name="filtro_edad" value="{{$filtro_edad}}">
                                                <input type="hidden" id="filtro_area_input" name="filtro_area" value="{{$filtro_area}}">
                                                <input type="hidden" id="filtro_didactica_input" name="filtro_didactica" value="{{$filtro_didactica}}">
												<i class="ri-search-line search-icon"></i>
												<button class="btn btn-primary py-0 d-none" type="submit">
												</button>
											</div>
										</form>
									</div>
								</div>
                            </div>
                            <div class="row g-4 mb-3">
                                <div class="col-lg-2">
                                    <div class="input-group">
                                        <select class="selectpicker form-control" id="filtro_edad" data-live-search="true">
                                            <option value="" selected disabled>Filtrar por Edad...</option>
                                            <option value="25-30" @if ($filtro_edad == '25-30') selected @endif>25 - 30</option>
                                            <option value="31-35" @if ($filtro_edad == '31-35') selected @endif>31 - 35</option>
                                            <option value="36-40" @if ($filtro_edad == '36-40') selected @endif>36 - 40</option>
                                            <option value="41" @if ($filtro_edad == '41') selected @endif>+ 41</option>
                                        </select>
                                        <button class="btn btn-sm btn-outline-danger" id="delete-edad-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Filtro por Edad"><i class="ri-close-line"></i></button>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="input-group">
                                        <select class="selectpicker form-control" id="filtro_area" data-live-search="true">
                                            <option value="" selected disabled>Filtrar por Área...</option>
                                            @foreach ($areas_conocimientos as $area_conocimiento)
                                                <option value="{{$area_conocimiento->id}}" @if ($filtro_area == $area_conocimiento->id) selected @endif data-subtext="{{$area_conocimiento->abreviatura}}">{{$area_conocimiento->nombre}}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-sm btn-outline-danger" id="delete-area-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Filtro por Área"><i class="ri-close-line"></i></button>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="input-group">
                                        <select class="selectpicker form-control" id="filtro_didactica" data-live-search="true">
                                            <option value="" selected disabled>Filtrar por Didáctica...</option>
                                            <option value="SI" @if ($filtro_didactica == 'SI') selected @endif>SI</option>
                                            <option value="NO" @if ($filtro_didactica == 'NO') selected @endif>NO</option>
                                        </select>
                                        <button class="btn btn-sm btn-outline-danger" id="delete-didactica-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Filtro por Didáctica"><i class="ri-close-line"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive table-card mt-3 mb-1">
                                <table class="table align-middle table-nowrap" id="docentes-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Documento N°</th>
                                            <th>N° de Teléfonos</th>
                                            <th>Edad</th>
                                            <th class="text-center">Área Conocimiento</th>
                                            <th class="text-center">Didáctica</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @forelse ($docentes as $docente)
                                            <tr>
                                                <td>{{$docente->id}}</td>
                                                <td>{{$docente->primer_nombre}} {{$docente->segundo_nombre}} {{$docente->tercer_nombre}} {{$docente->primer_apellido}} {{$docente->segundo_apellido}}</td>
                                                <td>{{$docente->numero_documento}}</td>
                                                <td>{{$docente->celular}} @if ($docente->telefono) - {{$docente->telefono}} @endif</td>
                                                <td>{{\Carbon\Carbon::createFromDate($docente->fecha_nacimiento)->age}} años</td>
                                                <td class="text-center">@if ($docente->area_conocimiento_id) {{$docente->areaConocimiento->abreviatura}} @endif</td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge @if ($docente->capacitacion_didactica)
                                                            bg-success-subtle text-success text-uppercase">
														<i class="ri-check-fill"></i>
                                                        @else
                                                            bg-danger-subtle text-danger text-uppercase">
														<i class="ri-close-fill"></i>
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge @if ($docente->estado == 'AC')
                                                            bg-success-subtle text-success text-uppercase"> Activo
                                                        @elseif ($docente->estado == 'IN')
                                                            bg-danger-subtle text-danger text-uppercase"> Inactivo
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    <a type="button" class="btn btn-sm btn-primary" href="{{route('docentes.show', $docente->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></a>
                                                    @can('editar_docentes')
                                                        <a type="button" class="btn btn-sm btn-info" href="{{route('docentes.edit', $docente->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-fill"></i></a>
                                                    @endcan
                                                    @if ($docente->estado == 'AC')
                                                        @can('inactivar_docentes')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#unactivateModal-{{$docente->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Inactivar"><i class="ri-close-fill"></i></button>
                                                        @endcan
                                                    @elseif ($docente->estado == 'IN')
                                                        @can('activar_docentes')
                                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#activateModal-{{$docente->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Activar"><i class="ri-close-fill"></i></button>
                                                        @endcan
                                                    @endif
                                                    @can('eliminar_docentes')
                                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal-{{$docente->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"><i class="ri-delete-bin-fill"></i></button>
                                                    @endcan
                                                    @if (Auth::user()->can('ver_legajos_docentes') || Auth::user()->can('ver_reportes_horas_docentes'))
                                                        <div class="btn-group dropdown" role="group">
                                                            <button id="dropdown-mas" type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="ri-add-line align-bottom mb-0"></i> Más
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-mas" aria-labelledby="dropdown-mas">
                                                                @can('ver_legajos_docentes')
                                                                    <li><a class="dropdown-item dropdown-item-mas" href="{{route('docentes.ver_legajo', $docente->id)}}">Legajo</a></li>
                                                                @endcan
                                                                {{-- @can('ver_reportes_horas_docentes') --}}
                                                                    <li><button class="dropdown-item dropdown-item-mas" data-bs-toggle="modal" data-bs-target="#reporteHorasModal-{{$docente->id}}">Reporte Horas GA</button></li>
                                                                {{-- @endcan --}}
                                                            </ul>
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center">
                                                    <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" estado="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                                    <h5 class="mt-2">Sin resultados.</h5>
                                                    <p class="text-muted mb-0">No pudimos encontrar ningún docente según tus parámetros de búsqueda.</p>
                                                </td>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="row">
                                @if ($docentes->hasPages())
                                    <nav class="justify-content-lg-end">
                                        {{ $docentes->links('pagination::bootstrap-5') }}
                                    </nav>
                                @else
                                    <p class="small text-muted">
                                        Mostrando <span class="fw-semibold">{{$docentes->count()}}</span>
                                        a <span class="fw-semibold">{{$docentes->count()}}</span>
                                        de <span class="fw-semibold">{{$docentes->count()}}</span>
                                        resultados
                                    </p>
                                @endif
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
        {{-- <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script> --}}
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script src="{{ URL::asset('js/flatpickr.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/cleave.js/cleave.min.js') }}"></script>
        @include('docentes.scripts.index-scripts')
    @endsection
@endcan
