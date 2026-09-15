@can('ver_cobros_docentes_pantalla')
    @extends('layouts.master')
    @section('title') Cobros @endsection
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
                        <h4 class="card-title mb-0">Cobros</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="cobros-list">
                            <div class="row g-4 mb-3">
                                <div class="row g-4 mb-3 d-flex justify-content-end">
                                    <div class="col-lg-2">
                                        <div class="search-box ms-2">
                                            <input type="text" class="form-control search" placeholder="Buscar...">
                                            <i class="ri-search-line search-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive table-card mt-3 mb-1">
                                <table class="table align-middle table-nowrap text-center" id="cobros-list">
                                    <thead class="table-light">
                                        <tr>
											<th>N° Cobro</th>
                                            <th class="sort" data-sort="fecha_inicio">Fecha Inicio</th>
                                            <th class="sort" data-sort="fecha_fin">Fecha Fin</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @forelse ($cobros as $key => $cobro)
                                            <tr>
												<td>{{$key + 1}}</td>
                                                <td class="fecha_inicio">{{\Carbon\Carbon::parse($cobro->fecha_inicio)->format('d/m/Y')}}</td>
                                                <td class="fecha_inicio">{{\Carbon\Carbon::parse($cobro->fecha_fin)->format('d/m/Y')}}</td>
                                                <td>
                                                    <a type="button" class="btn btn-sm btn-primary" href="{{asset($cobro->url_ubicacion)}}" target="_blank">Ver</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr class="text-center">
                                                <td colspan="7">No existen cobros registrados actualmente.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <div class="noresults" style="display: none">
                                    <div class="text-center">
                                        <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" estado="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                        <h5 class="mt-2">Sin resultados.</h5>
                                        <p class="text-muted mb-0">No pudimos encontrar ningún cobro según tus parámetros de búsqueda.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
								<div class="col-lg-6">
									<span class="text-muted" id="mostrando"></span>
										<br>
									<span class="text-muted">Total: {{$cobros->count()}}</span>
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
        @include('pantallas_docentes.scripts.cobros-scripts')
    @endsection
@endcan
