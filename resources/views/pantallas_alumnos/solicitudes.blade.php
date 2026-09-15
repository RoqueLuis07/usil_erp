@can('ver_solicitudes_alumnos_pantalla')
    @extends('layouts.master')
    @section('title') Mis Solicitudes @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('title') BIENVENIDO, {{$alumno->primer_nombre}} {{$alumno->primer_apellido}} @endslot
        @endcomponent

        @include('index.scripts.messages-scripts')

        <div class="row d-flex flex-wrap justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Mis Solicitudes</h4>
                    </div>
                    <div class="card-body">
                        <div id="solicitudes-list">
                            <div class="row g-4 mb-3">
                                <div class="col-lg-3">
                                    <div class="input-group">
                                        <button class="btn btn-outline-success" id="add-estado-filter-btn" data-id="PE">Mostrar Solo Pendientes</button>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <div class="d-flex justify-content-end">
                                        <div class="search-box ms-2">
                                            <input type="text" class="form-control search" placeholder="Buscar...">
                                            <i class="ri-search-line search-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive table-card mt-3 mb-1">
                                <table class="table align-middle table-nowrap" id="solicitudes-list">
                                    <thead class="table-light text-center">
                                        <tr>
                                            <th class="sort" data-sort="fecha">Fecha de Solicitud</th>
                                            <th class="sort" data-sort="solicitud">Solicitud</th>
                                            <th class="sort" data-sort="estado">Estado</th>
                                            <th class="sort" data-sort="fecha_pago">Fecha Pago</th>
                                            <th class="sort" data-sort="fecha_entrega">Fecha Entrega</th>
											<th class="sort" data-sort="fecha_examen">Fecha Examen</th>
											<th class="sort" data-sort="hora_examen">Hora Examen</th>
											<th class="sort" data-sort="aula_examen">Aula Examen</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all text-center">
                                        @forelse ($solicitudes as $solicitud)
                                            <tr>
                                                <td class="fecha">{{Carbon\Carbon::parse($solicitud->fecha_solicitud)->format('d/m/Y H:i')}}</td>
                                                <td class="solicitud">{{$solicitud->tipoSolicitud->nombre}}</td>
                                                <td class="estado d-none">{{$solicitud->estado}}</td>
                                                <td>
                                                    <span
                                                        class="badge @if ($solicitud->estado == 'PE')
                                                            bg-danger-subtle text-danger text-uppercase"> Pendiente
                                                        @elseif ($solicitud->estado == 'AP')
                                                            bg-primary-subtle text-primary text-uppercase"> Aprobado
                                                        @elseif ($solicitud->estado == 'PA')
                                                            bg-warning-subtle text-warning text-uppercase"> Pagado
                                                        @elseif ($solicitud->estado == 'GE')
                                                            bg-warning-subtle text-warning text-uppercase"> Generado
                                                        @elseif ($solicitud->estado == 'PR')
                                                            bg-info-subtle text-info text-uppercase"> Para Retiro
                                                        @elseif ($solicitud->estado == 'EN')
                                                            bg-success-subtle text-success text-uppercase"> Entregado
                                                            @elseif ($solicitud->estado == 'RE')
                                                            bg-danger-subtle text-danger text-uppercase"> Rechazado
                                                        @endif
                                                    </span>
                                                </td>
                                                <td class="fecha_pago">
                                                    @if ($solicitud->fecha_pago)
                                                        {{Carbon\Carbon::parse($solicitud->fecha_pago)->format('d/m/Y H:i')}}
                                                    @endif
                                                </td>
                                                <td class="fecha_entrega">
                                                    @if ($solicitud->fecha_entrega)
                                                        {{Carbon\Carbon::parse($solicitud->fecha_entrega)->format('d/m/Y H:i')}}
													@else
														N/A
                                                    @endif
                                                </td>
												@if ($solicitud->tipo_solicitud_id == 2)
													<td class="fecha_examen">{{Carbon\Carbon::parse($solicitud->fecha_examen)->format('d/m/Y')}}</td>
													<td class="hora_examen">{{Carbon\Carbon::parse($solicitud->fecha_examen)->format('H:i')}}</td>
													<td class="aula_examen">{{$solicitud->aula_examen}}</td>
												@else
													<td>N/A</td>
													<td>N/A</td>
													<td>N/A</td>
												@endif
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8">No tienes solicitudes registradas.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <div class="noresults" style="display: none">
                                    <div class="text-center">
                                        <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" state="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                        <h5 class="mt-2">Sin resultados.</h5>
                                        <p class="text-muted mb-0">No pudimos encontrar ninguna solicitud según tus parámetros de búsqueda.</p>
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
                </div><!-- end card -->
                <div class="row">
                    <div class="col-lg-12 text-center mb-3">
                        <a type="button" class="btn btn-danger me-2" href="{{route('root')}}">Volver</a>
                    </div>
                </div>
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
    @endsection

    @section('script')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        @include('pantallas_alumnos.scripts.solicitudes-scripts')
    @endsection
@endcan
