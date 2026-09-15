@can('ver_alumnos_ubs')
    @extends('layouts.master')
    @section('title') Alumnos @endsection
    @section('css')
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

        @include('ubs.alumnos.scripts.messages-scripts')
        @include('ubs.alumnos.modals.index-modals')

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
                                    @can('crear_alumnos_ubs')
                                        <a type="button" class="btn btn-success" href="{{route('alumnos_ubs.create')}}"><i class="ri-add-line align-bottom me-1"></i>Agregar</a>
                                    @endcan
                                </div>
                                <div class="col-lg-4">
									<div class="d-flex justify-content-sm-end">
										<form autocomplete="off" method="POST" action="{{ route('alumnos_ubs.index') }}">
										@csrf
											<div class="search-box ms-2">
												<input type="text" id="buscar" name="buscar" value="{{$buscar}}" class="form-control search" placeholder="Buscar...">
												<i class="ri-search-line search-icon"></i>
												<button class="btn btn-primary py-0 d-none" type="submit">
												</button>
											</div>
										</form>
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
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @foreach ($alumnos as $alumno)
                                            <tr>
                                                <td>{{$alumno->id}}</td>
                                                <td>{{$alumno->primer_nombre}} {{$alumno->segundo_nombre}} {{$alumno->tecer_nombre}} {{$alumno->primer_apellido}} {{$alumno->segundo_apellido}}</td>
                                                <td>{{$alumno->numero_documento}}</td>
                                                <td>{{$alumno->celular}} @if ($alumno->telefono) - {{$alumno->telefono}} @endif</td>
                                                <td>{{\Carbon\Carbon::createFromDate($alumno->fecha_nacimiento)->age}} años</td>
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
                                                    <a type="button" class="btn btn-sm btn-primary" href="{{route('alumnos_ubs.show', $alumno->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></a>
                                                    @can('ver_legajos_alumnos_ubs')
                                                        <a class="btn btn-sm btn-warning" href="{{route('alumnos_ubs.ver_legajo', $alumno->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Legajo"><i class="ri-file-copy-fill"></i></a>
                                                    @endcan
                                                    @can('editar_alumnos_ubs')
                                                        <a type="button" class="btn btn-sm btn-info" href="{{route('alumnos_ubs.edit', $alumno->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-fill"></i></a>
                                                    @endcan
                                                    @if ($alumno->estado == 'AC')
                                                        @can('inactivar_alumnos_ubs')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#unactivateModal-{{$alumno->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Inactivar"><i class="ri-close-fill"></i></button>
                                                        @endcan
                                                    @elseif ($alumno->estado == 'IN')
                                                        @can('activar_alumnos_ubs')
                                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#activateModal-{{$alumno->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Activar"><i class="ri-close-fill"></i></button>
                                                        @endcan
                                                    @endif
                                                    @can('eliminar_alumnos_ubs')
                                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal-{{$alumno->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"><i class="ri-delete-bin-fill"></i></button>
                                                    @endcan
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

                            <div class="row">
								<nav class="justify-content-lg-end">
									{{ $alumnos->links('pagination::bootstrap-5') }}
								</nav>
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
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        @include('ubs.alumnos.scripts.index-scripts')
    @endsection
@endcan
