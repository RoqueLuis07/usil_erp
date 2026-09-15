@can('ver_salarios_docentes')
    @extends('layouts.master')
    @section('title') Salarios de Docentes @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Salarios de Docentes @endslot
        @endcomponent

        @include('docentes.salarios.scripts.messages-scripts')
        @include('docentes.salarios.modals.index-modals')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Lista de Salarios de Docentes</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="docentes-list">
                            <div class="row g-4 mb-3">
                                <div class="col-lg-12">
									<div class="d-flex justify-content-sm-end">
										<form autocomplete="off" method="POST" action="{{ route('docentes_salarios.index') }}">
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
                                <table class="table align-middle table-nowrap" id="salarios-list">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Docente</th>
                                            <th>N° Documento</th>
                                            <th>Fecha Inicio</th>
                                            <th>Fecha Fin</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @foreach ($salarios as $salario)
                                            <tr>
                                                <td>{{$salario->id}}</td>
                                                <td>{{$salario->docente->primer_nombre}} {{$salario->docente->segundo_nombre}} {{$salario->docente->tercer_nombre}} {{$salario->docente->primer_apellido}} {{$salario->docente->segundo_apellido}}</td>
                                                <td>{{$salario->docente->numero_documento}}</td>
                                                <td>{{Carbon\Carbon::parse($salario->fecha_inicio)->format('d/m/Y')}}</td>
                                                <td>{{Carbon\Carbon::parse($salario->fecha_fin)->format('d/m/Y')}}</td>
                                                <td>
                                                    <span
                                                        class="badge @if ($salario->estado == 'PE')
                                                            bg-warning-subtle text-warning text-uppercase"> Pendiente
                                                        @elseif ($salario->estado == 'AP')
                                                            bg-success-subtle text-success text-uppercase"> Aprobado
                                                        @elseif ($salario->estado == 'RE')
                                                            bg-danger-subtle text-danger text-uppercase"> Rechazado
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    <a class="btn btn-sm btn-primary" href="{{ asset($salario->url_ubicacion) }}" target="_blank" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Visualizar"><i class="ri-eye-fill"></i></a>
                                                    @if ($salario->estado == 'PE')
                                                        @can('aprobar_salarios_docentes')
                                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal-{{$salario->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Aprobar"><i class="ri-check-fill"></i></button>
                                                        @endcan
                                                        @can('rechazar_salarios_docentes')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal-{{$salario->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Rechazar"><i class="ri-close-fill"></i></button>
                                                        @endcan
                                                    @elseif ($salario->estado == 'AP')
                                                        @can('anular_aprobacion_salarios_docentes')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#unapproveModal-{{$salario->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Anular Aprobación"><i class="ri-close-fill"></i></button>
                                                        @endcan
                                                    @elseif ($salario->estado == 'RE')
                                                        @can('anular_rechazo_salarios_docentes')
                                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#unrejectModal-{{$salario->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Anular Rechazo"><i class="ri-close-fill"></i></button>
                                                        @endcan
                                                        @can('eliminar_salarios_docentes')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal-{{$salario->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"><i class="ri-delete-bin-fill"></i></button>
                                                        @endcan
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="noresults" style="display: none">
                                    <div class="text-center">
                                        <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" estado="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                        <h5 class="mt-2">Sin resultados.</h5>
                                        <p class="text-muted mb-0">No pudimos encontrar ningún salario docente según tus parámetros de búsqueda.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
								<nav class="justify-content-lg-end">
									{{ $salarios->links('pagination::bootstrap-5') }}
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
        @include('docentes.salarios.scripts.index-scripts')
    @endsection
@endcan
