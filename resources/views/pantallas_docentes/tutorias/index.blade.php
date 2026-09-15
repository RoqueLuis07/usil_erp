@can('ver_tutorias_docentes_pantalla')
    @extends('layouts.master')
    @section('title') Tutorías @endsection
    @section('css')
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
                        <h4 class="card-title mb-0">Tutorías</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="tutorias-list">
                            <div class="row g-4 mb-3">
                                <div class="col-lg-12">
                                    <div class="d-flex justify-content-sm-end">
                                        <div class="search-box ms-2">
                                            <input type="text" class="form-control search" placeholder="Buscar...">
                                            <i class="ri-search-line search-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive table-card mt-3 mb-1">
                                <table class="table align-middle table-nowrap text-center" id="tutorias-list">
                                    <thead class="table-light">
                                        <tr>
											<th>N°</th>
                                            <th class="sort" data-sort="materia">Materia</th>
                                            <th class="sort" data-sort="modalidad">Modalidad</th>
                                            <th class="sort" data-sort="inicio">Fecha Inicio</th>
                                            <th class="sort" data-sort="fin">Fecha Fin</th>
                                            <th>Cant. Alumnos</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
										@forelse ($tutorias as $key => $tutoria)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td class="materia">{{ $tutoria->materia->nombre_fantasia }}</td>
                                                <td class="modalidad">{{ $tutoria->modalidad->nombre }}</td>
                                                <td class="inicio">{{ Carbon\Carbon::parse($tutoria->fecha_inicio)->format('d/m/Y') }}</td>
                                                <td class="fin">{{ Carbon\Carbon::parse($tutoria->fecha_fin)->format('d/m/Y') }}</td>
                                                <td>
                                                    @php
                                                        if ($tutoria->alumnos->count() == 1) {
                                                            $texto = 'alumno';
                                                        } else {
                                                            $texto = 'alumnos';
                                                        }
                                                    @endphp
                                                    {{ $tutoria->alumnos->count() }} {{ $texto }}
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge @if ($tutoria->estado == 'AC')
                                                            bg-success-subtle text-success text-uppercase"> Activo
                                                        @elseif ($tutoria->estado == 'EC')
                                                            bg-warning-subtle text-warning text-uppercase"> En Curso
                                                        @elseif ($tutoria->estado == 'FI')
                                                            bg-danger-subtle text-danger text-uppercase"> Finalizado
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    <a type="button" class="btn btn-sm btn-primary" href="{{route('tutorias_docentes.show', $tutoria->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver Detalles"><i class="ri-eye-fill"></i></a>
                                                    @can('ver_clases_tutorias_docentes_pantalla')
                                                        @if ($tutoria->alumnos->count() > 0 && $tutoria->fecha_inicio <= now() && $tutoria->fecha_fin >= now())
                                                            <a type="button" class="btn btn-sm btn-warning" href="{{route('tutorias_docentes.index_clases', $tutoria->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver Clases Generadas"><i class="ri-book-open-fill"></i></a>
                                                        @endif
                                                    @endcan
                                                    @can('cargar_puntaje_tutorias_docentes_pantalla')
                                                        @if ($tutoria->alumnos->where('cantidad_asistencias', $tutoria->cantidad_clases)->where('estado', 'EC')->count() > 0 || ($tutoria->acta && $tutoria->alumnos->where('estado', 'EC')->count() == 0) && $tutoria->estado != 'FI')
                                                            <a type="button" class="btn btn-sm btn-success" href="{{route('tutorias_docentes.puntajes', $tutoria->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Cargar Evaluacion"><i class="ri-arrow-up-fill"></i></a>
                                                        @endif
                                                    @endcan
                                                </td>
                                            </tr>
                                        @empty
                                            <tr class="text-center">
                                                <td colspan="7">No tienes tutorías asignadas actualmente.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <div class="noresults" style="display: none">
                                    <div class="text-center">
                                        <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" estado="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                        <h5 class="mt-2">Sin resultados.</h5>
                                        <p class="text-muted mb-0">No pudimos encontrar ninguna tutoría según tus parámetros de búsqueda.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
								<div class="col-lg-6">
									<span class="text-muted" id="mostrando"></span>
										<br>
									<span class="text-muted">Total: {{$tutorias->count()}}</span>
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
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        @include('pantallas_docentes.tutorias.scripts.index-scripts')
    @endsection
@endcan
