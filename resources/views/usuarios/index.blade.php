@can('ver_usuarios')
    @extends('layouts.master')
    @section('title') Usuarios @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Usuarios @endslot
        @endcomponent

        @include('usuarios.scripts.messages-scripts')
        @include('usuarios.modals.index-modals')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Lista de Usuarios</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div>
                            <div class="row g-4 mb-3">
                                <div class="col-lg-8">
                                    @can('crear_usuarios')
                                        <a type="button" class="btn btn-success" href="{{route('usuarios.create')}}"><i class="ri-add-line align-bottom me-1"></i>Agregar</a>
                                    @endcan
                                </div>
                                <div class="col-lg-4">
									<div class="d-flex justify-content-sm-end">
										<form autocomplete="off" method="POST" action="{{ route('usuarios.index') }}">
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
                                            <th>Correo Electrónico</th>
                                            <th>Rol</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @foreach ($usuarios as $usuario)
                                            <tr>
                                                <td>{{$usuario->id}}</td>
                                                <td>{{$usuario->name}}</td>
                                                <td>{{$usuario->email}}</td>
                                                <td>{{$usuario->rol->name}}</td>
                                                <td>
                                                    <span
                                                        class="badge @if ($usuario->state == 'AC')
                                                            bg-success-subtle text-success text-uppercase"> Activo
                                                        @elseif ($usuario->state == 'IN')
                                                            bg-danger-subtle text-danger text-uppercase"> Inactivo
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#showModal-{{$usuario->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></button>
                                                    @can('editar_usuarios')
                                                        <a type="button" class="btn btn-sm btn-info" href="{{route('usuarios.edit', $usuario->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Editar"><i class="ri-edit-fill"></i></a>
                                                    @endcan
                                                    @if ($usuario->state == 'AC' && Auth::user()->id != $usuario->id)
                                                        @can('inactivar_usuarios')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#unactivateModal-{{$usuario->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Inactivar"><i class="ri-close-fill"></i></button>
                                                        @endcan
                                                    @elseif ($usuario->state == 'IN' && Auth::user()->id != $usuario->id)
                                                        @can('activar_usuarios')
                                                        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#activateModal-{{$usuario->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Activar"><i class="ri-close-fill"></i></button>
                                                        @endcan
                                                    @endif
                                                    @if (Auth::user()->id != $usuario->id)
                                                        @can('eliminar_usuarios')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal-{{$usuario->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"><i class="ri-delete-bin-fill"></i></button>
                                                        @endcan
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="noresults" style="display: none">
                                    <div class="text-center">
                                        <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" state="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                        <h5 class="mt-2">Sin resultados.</h5>
                                        <p class="text-muted mb-0">No pudimos encontrar ningún usuario según tus parámetros de búsqueda.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
								<nav class="justify-content-lg-end">
									{{ $usuarios->links('pagination::bootstrap-5') }}
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
        @include('usuarios.scripts.index-scripts')
    @endsection
@endcan
