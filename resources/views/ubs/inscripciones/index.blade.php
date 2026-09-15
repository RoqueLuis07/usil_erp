@can('ver_inscripciones_ubs')
    @extends('layouts.master')
    @section('title') Inscripciones @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inicio @endslot
            @slot('title') Inscripciones @endslot
        @endcomponent

        @include('ubs.inscripciones.scripts.messages-scripts')
        @include('ubs.inscripciones.modals.index-modals')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Lista de Inscripciones</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div>
                            <div class="row g-4 mb-3">
                                <div class="col-lg-8">
                                    @can('crear_inscripciones_ubs')
                                        <a type="button" class="btn btn-success" href="{{route('inscripciones_ubs.create')}}"><i class="ri-add-line align-bottom me-1"></i>Agregar</a>
                                    @endcan
                                </div>
                                <div class="col-lg-4">
                                    <div class="d-flex justify-content-sm-end">
                                        <form autocomplete="off" method="POST" action="{{ route('inscripciones_ubs.index') }}">
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
                                            <th>Fecha</th>
                                            <th>Alumno</th>
                                            <th>Curso</th>
                                            <th>Llamado</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @foreach ($inscripciones as $inscripcion)
                                            <tr>
                                                <td>{{$inscripcion->id}}</td>
                                                <td>{{\Carbon\Carbon::parse($inscripcion->fecha)->format('d/m/Y H:i:s')}}</td>
                                                <td>{{$inscripcion->alumno->primer_nombre}} {{$inscripcion->alumno->primer_apellido}} - {{$inscripcion->alumno->numero_documento}}</td>
                                                <td>{{$inscripcion->curso->nombre_fantasia}}</td>
                                                @php
                                                    $llamado = $inscripcion->curso->llamado;
                                                    if ($llamado == 1 || $llamado == 3) {
                                                           $tipo = 'er';
                                                    } elseif ($llamado == 2) {
                                                        $tipo = 'do';
                                                    } elseif ($llamado == 4 || $llamado == 5 || $llamado == 6) {
                                                        $tipo = 'to';
                                                    } elseif ($llamado == 7 || $llamado == 10) {
                                                        $tipo = 'mo';
                                                    } elseif ($llamado == 8) {
                                                        $tipo = 'vo';
                                                    } elseif ($llamado == 9) {
                                                        $tipo = 'no';
                                                    }
                                                @endphp
                                                <td>
                                                    <span class="badge bg-info-subtle text-info text-uppercase">{{$inscripcion->curso->llamado}}{{$tipo}} llamado</span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge @if ($inscripcion->estado == 'AC')
                                                            bg-success-subtle text-success text-uppercase"> Activo
                                                        @elseif ($inscripcion->estado == 'IN')
                                                            bg-danger-subtle text-danger text-uppercase"> Inactivo
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    <a type="button" class="btn btn-sm btn-primary" href="{{route('inscripciones_ubs.show', $inscripcion->id)}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Ver"><i class="ri-eye-fill"></i></a>
                                                    @if ($inscripcion->estado == 'AC')
                                                        @can('inactivar_inscripciones_ubs')
                                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#unactivateModal-{{$inscripcion->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Inactivar"><i class="ri-close-fill"></i></button>
                                                        @endcan
                                                    @elseif ($inscripcion->estado == 'IN')
                                                        @can('activar_inscripciones_ubs')
                                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#activateModal-{{$inscripcion->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Activar"><i class="ri-close-fill"></i></button>
                                                        @endcan
                                                    @endif
                                                    @can('eliminar_inscripciones_ubs')
                                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#destroyModal-{{$inscripcion->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"><i class="ri-delete-bin-fill"></i></button>
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
                                        <p class="text-muted mb-0">No pudimos encontrar ningúna inscripción según tus parámetros de búsqueda.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
								<nav class="justify-content-lg-end">
                                    {{ $inscripciones->links('pagination::bootstrap-5') }}
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
        @include('ubs.inscripciones.scripts.index-scripts')
    @endsection
@endcan
