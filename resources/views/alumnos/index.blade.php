@can('ver_alumnos')
    @extends('layouts.master-academic')
    @section('title') Alumnos @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
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
            @slot('title') Alumnos @endslot
        @endcomponent

        @include('alumnos.scripts.messages-scripts')
        @include('alumnos.modals.index-modals')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Lista de Alumnos</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="alumnos-list">
                            <div class="row g-4 mb-3">
                                <div class="col-lg-8">
                                    @can('crear_alumnos')
                                        <a type="button" class="btn btn-success" href="{{route('alumnos.create')}}"><i class="ri-add-line align-bottom me-1"></i>Agregar</a>
                                    @endcan
                                </div>
								<div class="col-lg-4">
									<div class="d-flex justify-content-sm-end">
										<form autocomplete="off" method="POST" action="{{ route('alumnos.index') }}" id="buscar_form">
										@csrf
											<div class="search-box ms-2">
												<input type="text" id="buscar" name="buscar" value="{{$buscar}}" class="form-control search" placeholder="Buscar...">
                                                <input type="hidden" id="filtro_ingreso_input" name="filtro_ingreso" value="{{$filtro_ingreso}}">
                                                <input type="hidden" id="filtro_edad_input" name="filtro_edad" value="{{$filtro_edad}}">
                                                <input type="hidden" id="filtro_programa_input" name="filtro_programa" value="{{$filtro_programa}}">
                                                <input type="hidden" id="filtro_periodo_input" name="filtro_periodo" value="{{$filtro_periodo}}">
                                                <input type="hidden" id="filtro_correo_input" name="filtro_correo" value="{{$filtro_correo}}">
                                                <input type="hidden" id="filtro_ubs_input" name="filtro_ubs" value="{{$filtro_ubs}}">
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
                                        <select class="selectpicker form-control" id="filtro_ingreso" data-live-search="true">
                                            <option value="" selected disabled>Filtrar por Ingreso...</option>
                                            @foreach ($semestres as $semestre)
                                                <option value="{{$semestre->id}}" @if ($filtro_ingreso == $semestre->id) selected @endif>{{$semestre->nombre}}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-sm btn-outline-danger" id="delete-ingreso-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Filtro por Ingreso"><i class="ri-close-line"></i></button>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="input-group">
                                        <select class="selectpicker form-control" id="filtro_edad" data-live-search="true">
                                            <option value="" selected disabled>Filtrar por Edad...</option>
                                            <option value="15-20" @if ($filtro_edad == '15-20') selected @endif>15 - 20</option>
                                            <option value="21-25" @if ($filtro_edad == '21-25') selected @endif>21 - 25</option>
                                            <option value="26-30" @if ($filtro_edad == '26-30') selected @endif>26 - 30</option>
                                            <option value="31" @if ($filtro_edad == '31') selected @endif>+ 31</option>
                                        </select>
                                        <button class="btn btn-sm btn-outline-danger" id="delete-edad-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Filtro por Edad"><i class="ri-close-line"></i></button>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="input-group">
                                        <select class="selectpicker form-control" id="filtro_programa" data-live-search="true">
                                            <option value="" selected disabled>Filtrar por Programa...</option>
                                            @foreach ($programas as $programa)
                                                <option value="{{$programa->id}}" @if ($filtro_programa == $programa->id) selected @endif>{{$programa->nombre}}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-sm btn-outline-danger" id="delete-programa-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Filtro por Programa"><i class="ri-close-line"></i></button>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="input-group">
                                        <select class="selectpicker form-control" id="filtro_periodo" data-live-search="true">
                                            <option value="" selected disabled>Filtrar por Período...</option>
                                            @foreach ($semestres as $semestre)
                                                <option value="{{$semestre->id}}" @if ($filtro_periodo == $semestre->id) selected @endif>{{$semestre->nombre}}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-sm btn-outline-danger" id="delete-periodo-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Filtro por Período"><i class="ri-close-line"></i></button>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="input-group">
                                        <select class="selectpicker form-control" id="filtro_correo" data-live-search="true">
                                            <option value="" selected disabled>Filtrar por Correo...</option>
                                            <option value="SI" @if ($filtro_correo == 'SI') selected @endif>CARGADO</option>
                                            <option value="NO" @if ($filtro_correo == 'NO') selected @endif>NO CARGADO</option>
                                        </select>
                                        <button class="btn btn-sm btn-outline-danger" id="delete-correo-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Filtro por Correo"><i class="ri-close-line"></i></button>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="input-group">
                                        <select class="selectpicker form-control" id="filtro_ubs" data-live-search="true">
                                            <option value="" selected disabled>Filtrar por UBS...</option>
                                            <option value="GRADO" @if ($filtro_ubs == 'GRADO') selected @endif>GRADO</option>
                                            <option value="UBS" @if ($filtro_ubs == 'UBS') selected @endif>UBS</option>
                                        </select>
                                        <button class="btn btn-sm btn-outline-danger" id="delete-ubs-filter-btn" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar Filtro por UBS"><i class="ri-close-line"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive table-card mt-3 mb-1">
								<table class="table align-middle table-nowrap">
                                    <thead class="table-light">
										<tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Documento N°</th>
                                            <th>N° de Teléfonos</th>
                                            <th>Edad</th>
                                            <th>Ingreso</th>
                                            <th>Programa</th>
                                            <th>Periodo</th>
											<th class="text-center">Correo</th>
											<th class="text-center">UBS</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @forelse ($alumnos as $alumno)
                                            <tr>
                                                <td>{{$alumno->id}}</td>
                                                <td>{{$alumno->primer_nombre}} {{$alumno->segundo_nombre}} {{$alumno->tecer_nombre}} {{$alumno->primer_apellido}} {{$alumno->segundo_apellido}}</td>
                                                <td>{{$alumno->numero_documento}}</td>
                                                <td>{{$alumno->celular}} @if ($alumno->telefono) - {{$alumno->telefono}} @endif</td>
                                                <td>{{\Carbon\Carbon::createFromDate($alumno->fecha_nacimiento)->age}} años</td>
                                                <td>{{$alumno->ingreso}}</td>
                                                <td>{{$alumno->programa}}</td>
												<td>{{$alumno->periodo}}</td>
												<td class="text-center">
                                                    <span
                                                        class="badge @if ($alumno->email_institucional)
                                                            bg-success-subtle text-success text-uppercase">
														<i class="ri-check-fill"></i>
                                                        @else
                                                            bg-danger-subtle text-danger text-uppercase">
														<i class="ri-close-fill"></i>
                                                        @endif
                                                    </span>
                                                </td>
												<td class="text-center">
                                                    <span
                                                        class="badge @if ($alumno->ubs)
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
                                                        class="badge @if ($alumno->estado == 'AC')
                                                            bg-success-subtle text-success text-uppercase"> Activo
                                                        @elseif ($alumno->estado == 'IN')
                                                            bg-danger-subtle text-danger text-uppercase"> Inactivo
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    <a type="button" class="btn btn-sm btn-primary" href="{{route('alumnos.show', $alumno->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></a>
                                                    @can('editar_alumnos')
                                                        <a type="button" class="btn btn-sm btn-info" href="{{route('alumnos.edit', $alumno->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-fill"></i></a>
                                                    @endcan
                                                    @if ($alumno->estado == 'AC')
                                                        @can('inactivar_alumnos')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#unactivateModal-{{$alumno->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Inactivar"><i class="ri-close-fill"></i></button>
                                                        @endcan
                                                    @elseif ($alumno->estado == 'IN')
                                                        @can('activar_alumnos')
                                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#activateModal-{{$alumno->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Activar"><i class="ri-close-fill"></i></button>
                                                        @endcan
                                                    @endif
                                                    @can('eliminar_alumnos')
                                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal-{{$alumno->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"><i class="ri-delete-bin-fill"></i></button>
                                                    @endcan
                                                    @if (Auth::user()->can('ver_legajos_alumnos') || Auth::user()->can('ver_notas_alumnos') || Auth::user()->can('ver_asistencias_alumnos') || Auth::user()->can('ver_certificado_estudios_alumnos'))
                                                        <div class="btn-group dropdown" role="group">
                                                            <button id="dropdown-mas" type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="ri-add-line align-bottom mb-0"></i> Más
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-mas" aria-labelledby="dropdown-mas">
                                                                @can('ver_legajos_alumnos')
                                                                    <li><a class="dropdown-item dropdown-item-mas" href="{{route('alumnos.ver_legajo', $alumno->id)}}">Legajo</a></li>
                                                                @endcan
                                                                @can('ver_notas_alumnos')
                                                                    <li><a class="dropdown-item dropdown-item-mas" href="{{route('alumnos.show_notas', $alumno->id)}}">Notas</a></li>
                                                                    <li><a class="dropdown-item dropdown-item-mas" href="{{route('alumnos.show_notas_espejo', $alumno->id)}}">Espejo</a></li>
                                                                @endcan
                                                                @can('ver_asistencias_alumnos')
                                                                    <li><a class="dropdown-item dropdown-item-mas" href="{{route('alumnos.show_asistencias', $alumno->id)}}">Asistencias</a></li>
                                                                @endcan
                                                                @can('ver_extensiones_alumnos')
                                                                    <li><a class="dropdown-item dropdown-item-mas" href="{{route('alumnos.show_extensiones', $alumno->id)}}">Extensiones</a></li>
                                                                @endcan
                                                                @can('ver_certificado_estudios_alumnos')
                                                                    @if (!$alumno->url_certificado_estudio)
																		<li><a class="dropdown-item dropdown-item-mas" href="{{route('certificados_estudios.show', ['id' => $alumno->id, 'tipo' => 'PY'])}}">Cert. Estudios</a></li>
																	@else
																		<li><a class="dropdown-item dropdown-item-mas" href="{{asset($alumno->url_certificado_estudio)}}" target="_blank">Cert. Estudios</a></li>
																	@endif
                                                                @endcan
                                                                @can('ver_certificado_estudios_siu_alumnos')
                                                                    @if (!$alumno->url_certificado_estudio_siu)
																		<li><a class="dropdown-item dropdown-item-mas" href="{{route('certificados_estudios.show', ['id' => $alumno->id, 'tipo' => 'SIU'])}}">Cert. Estudios SIU</a></li>
																	@else
																		<li><a class="dropdown-item dropdown-item-mas" href="{{asset($alumno->url_certificado_estudio_siu)}}" target="_blank">Cert. Estudios SIU</a></li>
																	@endif
                                                                @endcan
                                                            </ul>
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="12" class="text-center">
                                                    <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" estado="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                                    <h5 class="mt-2">Sin resultados.</h5>
                                                    <p class="text-muted mb-0">No pudimos encontrar ningún alumno según tus parámetros de búsqueda.</p>
                                                </td>
                                        @endforelse
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

                            <div class="row">
                                @if ($alumnos->hasPages())
                                    <nav class="justify-content-lg-end">
                                        {{ $alumnos->links('pagination::bootstrap-5') }}
                                    </nav>
                                @else
                                    <p class="small text-muted">
                                        Mostrando <span class="fw-semibold">{{$alumnos->count()}}</span>
                                        a <span class="fw-semibold">{{$alumnos->count()}}</span>
                                        de <span class="fw-semibold">{{$alumnos->count()}}</span>
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
        @include('alumnos.scripts.index-scripts')
    @endsection
@endcan
