@can('ver_docentes_ubs')
    @extends('layouts.master')
    @section('title') Docentes @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Docentes @endslot
        @endcomponent

        @include('ubs.docentes.scripts.messages-scripts')
        @include('ubs.docentes.modals.index-modals')

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
                                    @can('crear_docentes_ubs')
                                        <a type="button" class="btn btn-success" href="{{route('docentes_ubs.create')}}"><i class="ri-add-line align-bottom me-1"></i>Agregar</a>
                                    @endcan
                                </div>
                                <div class="col-lg-4">
									<div class="d-flex justify-content-sm-end">
										<form autocomplete="off" method="POST" action="{{ route('docentes_ubs.index') }}">
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
                                <table class="table align-middle table-nowrap" id="docentes-list">
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
                                        @foreach ($docentes as $docente)
                                            <tr>
                                                <td>{{$docente->id}}</td>
                                                <td>{{$docente->primer_nombre}} {{$docente->segundo_nombre}} {{$docente->tercer_nombre}} {{$docente->primer_apellido}} {{$docente->segundo_apellido}}</td>
                                                <td>{{$docente->numero_documento}}</td>
                                                <td>{{$docente->celular}} @if ($docente->telefono) - {{$docente->telefono}} @endif</td>
                                                <td>{{\Carbon\Carbon::createFromDate($docente->fecha_nacimiento)->age}} años</td>
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
                                                    <a type="button" class="btn btn-sm btn-primary" href="{{route('docentes_ubs.show', $docente->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></a>
                                                    @can('ver_legajos_docentes_ubs')
                                                        <a class="btn btn-sm btn-warning" href="{{route('docentes_ubs.ver_legajo', $docente->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Legajo"><i class="ri-folder-fill"></i></a>
                                                    @endcan
                                                    @can('editar_docentes_ubs')
                                                        <a type="button" class="btn btn-sm btn-info" href="{{route('docentes_ubs.edit', $docente->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-fill"></i></a>
                                                    @endcan
                                                    @if ($docente->estado == 'AC')
                                                        @can('inactivar_docentes_ubs')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#unactivateModal-{{$docente->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Inactivar"><i class="ri-close-fill"></i></button>
                                                        @endcan
                                                    @elseif ($docente->estado == 'IN')
                                                        @can('activar_docentes_ubs')
                                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#activateModal-{{$docente->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Activar"><i class="ri-close-fill"></i></button>
                                                        @endcan
                                                    @endif
                                                    @can('eliminar_docentes_ubs')
                                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal-{{$docente->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"><i class="ri-delete-bin-fill"></i></button>
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
                                        <p class="text-muted mb-0">No pudimos encontrar ningún docente según tus parámetros de búsqueda.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
								<nav class="justify-content-lg-end">
									{{ $docentes->links('pagination::bootstrap-5') }}
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
        @include('ubs.docentes.scripts.index-scripts')
    @endsection
@endcan
