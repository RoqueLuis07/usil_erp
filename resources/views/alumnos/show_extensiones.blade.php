@can('ver_extensiones_alumnos')
    @extends('layouts.master')
    @section('title') Ver Extensiones Universitarias @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Alumnos @endslot
            @slot('title') Ver Extensiones Universitarias  @endslot
        @endcomponent

        <div class="row d-flex flex-wrap justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Datos del Alumno</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4 mb-3">
                                <label class="form-label" for="alumno">Alumno</label>
                                <input class="form-control" type="text" id="alumno" value="{{ $alumno->primer_nombre }} {{ $alumno->primer_apellido }}" readonly>
                            </div>
                            <div class="col-lg-2 mb-3">
                                <label class="form-label" for="alumno">N° Documento</label>
                                <input class="form-control" type="text" id="alumno" value="{{ $alumno->numero_documento }}" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 mb-3 text-end">
                                <a type="button" class="btn btn-danger me-2" href="{{route('alumnos.index')}}">Volver</a>
                                <button type="button" class="btn btn-success" id="generar-reporte-btn">Generar PDF</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Visualizar exteiones universitarias</h4>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-pills arrow-navtabs nav-info nav-justified bg-light gap-2 mb-4" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" data-bs-toggle="tab" href="#tipos" role="tab" aria-selected="false">Resumen por Tipo de Actividad</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#todos" role="tab" aria-selected="true">Actividades</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active show" id="tipos" role="tabpanel">
                                <div id="tipos-list">
                                    <div class="table-responsive table-card mt-3 mb-1">
                                        <table class="table align-middle table-nowrap" id="tipos-list">
                                            <thead class="table-light text-center">
                                                <tr>
                                                    <th>Tipo de Actividad</th>
                                                    <th>Cant. Realizada</th>
                                                    <th>Totales</th>
                                                    <th>Realizadas</th>
                                                    <th>Acreditadas</th>
                                                </tr>
                                            </thead>
                                            <tbody class="list form-check-all text-center">
                                                @forelse ($tipos_actividades as $tipo)
                                                    <tr>
                                                        <td>{{$tipo->nombre}}</td>
                                                        @php
                                                            if ($extensiones->count() != 1) {
                                                                $texto = 'actividades';
                                                            } else {
                                                                $texto = 'actividad';
                                                            }
                                                        @endphp
                                                        <td>
                                                            @switch($tipo->id)
                                                                @case(1)
                                                                    @php
                                                                        if ($cantidad_realizada_1 != 1) {
                                                                            $texto = 'actividades';
                                                                        } else {
                                                                            $texto = 'actividad';
                                                                        }
                                                                    @endphp
                                                                    {{number_format($cantidad_realizada_1, 0, ',', '.')}} {{$texto}}
                                                                    @break
                                                                @case(2)
                                                                    @php
                                                                        if ($cantidad_realizada_2 != 1) {
                                                                            $texto = 'actividades';
                                                                        } else {
                                                                            $texto = 'actividad';
                                                                        }
                                                                    @endphp
                                                                    {{number_format($cantidad_realizada_2, 0, ',', '.')}} {{$texto}}
                                                                    @break
                                                                @case(3)
                                                                    @php
                                                                        if ($cantidad_realizada_3 != 1) {
                                                                            $texto = 'actividades';
                                                                        } else {
                                                                            $texto = 'actividad';
                                                                        }
                                                                    @endphp
                                                                    {{number_format($cantidad_realizada_3, 0, ',', '.')}} {{$texto}}
                                                                    @break
                                                                @case(4)
                                                                    @php
                                                                        if ($cantidad_realizada_4 != 1) {
                                                                            $texto = 'actividades';
                                                                        } else {
                                                                            $texto = 'actividad';
                                                                        }
                                                                    @endphp
                                                                    {{number_format($cantidad_realizada_4, 0, ',', '.')}} {{$texto}}
                                                                    @break
                                                                @default
                                                                    @break
                                                            @endswitch
                                                        </td>
                                                        @php
                                                            if ($tipo->maxima_cantidad_horas != 1) {
                                                                $texto = 'horas';
                                                            } else {
                                                                $texto = 'hora';
                                                            }
                                                        @endphp
                                                        <td>{{number_format($tipo->maxima_cantidad_horas, 0, ',', '.')}} {{$texto}}</td>
                                                        <td>
                                                            @switch($tipo->id)
                                                                @case(1)
                                                                    @php
                                                                        if ($horas_realizadas_1 != 1) {
                                                                            $texto = 'horas';
                                                                        } else {
                                                                            $texto = 'hora';
                                                                        }
                                                                    @endphp
                                                                    {{number_format($horas_realizadas_1, 0, ',', '.')}} {{$texto}}
                                                                    @break
                                                                @case(2)
                                                                    @php
                                                                        if ($horas_realizadas_2 != 1) {
                                                                            $texto = 'horas';
                                                                        } else {
                                                                            $texto = 'hora';
                                                                        }
                                                                    @endphp
                                                                    {{number_format($horas_realizadas_2, 0, ',', '.')}} {{$texto}}
                                                                    @break
                                                                @case(3)
                                                                    @php
                                                                        if ($horas_realizadas_3 != 1) {
                                                                            $texto = 'horas';
                                                                        } else {
                                                                            $texto = 'hora';
                                                                        }
                                                                    @endphp
                                                                    {{number_format($horas_realizadas_3, 0, ',', '.')}} {{$texto}}
                                                                    @break
                                                                @case(4)
                                                                    @php
                                                                        if ($horas_realizadas_4 != 1) {
                                                                            $texto = 'horas';
                                                                        } else {
                                                                            $texto = 'hora';
                                                                        }
                                                                    @endphp
                                                                    {{number_format($horas_realizadas_4, 0, ',', '.')}} {{$texto}}
                                                                    @break
                                                                @default
                                                                    @break
                                                            @endswitch
                                                        </td>
                                                        <td>
                                                            @switch($tipo->id)
                                                                @case(1)
                                                                    @php
                                                                        if ($horas_acreditadas_1 != 1) {
                                                                            $texto = 'horas';
                                                                        } else {
                                                                            $texto = 'hora';
                                                                        }
                                                                    @endphp
                                                                    {{number_format($horas_acreditadas_1, 0, ',', '.')}} {{$texto}}
                                                                    @break
                                                                @case(2)
                                                                    @php
                                                                        if ($horas_acreditadas_2 != 1) {
                                                                            $texto = 'horas';
                                                                        } else {
                                                                            $texto = 'hora';
                                                                        }
                                                                    @endphp
                                                                    {{number_format($horas_acreditadas_2, 0, ',', '.')}} {{$texto}}
                                                                    @break
                                                                @case(3)
                                                                    @php
                                                                        if ($horas_acreditadas_3 != 1) {
                                                                            $texto = 'horas';
                                                                        } else {
                                                                            $texto = 'hora';
                                                                        }
                                                                    @endphp
                                                                    {{number_format($horas_acreditadas_3, 0, ',', '.')}} {{$texto}}
                                                                    @break
                                                                @case(4)
                                                                    @php
                                                                        if ($horas_acreditadas_4 != 1) {
                                                                            $texto = 'horas';
                                                                        } else {
                                                                            $texto = 'hora';
                                                                        }
                                                                    @endphp
                                                                    {{number_format($horas_acreditadas_4, 0, ',', '.')}} {{$texto}}
                                                                    @break
                                                                @default
                                                                    @break
                                                            @endswitch
                                                        </td>
                                                    </tr>
                                                @empty

                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="todos" role="tabpanel">
                                <div id="extensiones-list">
                                    <div class="row g-4 mb-3">
                                        <div class="col-lg-8">
                                            <div class="d-flex">
                                                <div class="search-box ms-2">
                                                    <input type="text" class="form-control search" placeholder="Buscar...">
                                                    <i class="ri-search-line search-icon"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="d-flex justify-content-end">
                                                <div class="pagination-wrap hstack gap-2">
                                                    <ul class="pagination listjs-pagination mb-0"></ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive table-card mt-3 mb-1">
                                        <table class="table align-middle table-nowrap" id="extensiones-list">
                                            <thead class="table-light text-center">
                                                <tr>
                                                    <th class="sort" data-sort="evento">Evento</th>
                                                    <th class="sort" data-sort="responsable">Responsable</th>
                                                    <th>Totales</th>
                                                    <th>Cumplidas</th>
                                                    <th>Período</th>
                                                    <th>Certificado</th>
                                                </tr>
                                            </thead>
                                            <tbody class="list form-check-all text-center">
                                                @forelse ($extensiones as $extension)
                                                    <tr>
                                                        <td class="evento">{{$extension->extensionUniversitaria->nombre}}</td>
                                                        <td>{{$extension->extensionUniversitaria->docente->primer_nombre}} {{$extension->extensionUniversitaria->docente->primer_apellido}}</td>
                                                        @php
                                                            if ($extension->extensionUniversitaria->cantidad_horas != 1) {
                                                                $texto = 'horas';
                                                            } else {
                                                                $texto = 'hora';
                                                            }
                                                        @endphp
                                                        <td>{{number_format($extension->extensionUniversitaria->cantidad_horas, 0, ',', '.')}} {{$texto}}</td>
                                                        @php
                                                            if ($extension->cantidad_horas != 1) {
                                                                $texto = 'horas';
                                                            } else {
                                                                $texto = 'hora';
                                                            }
                                                        @endphp
                                                        <td>{{number_format($extension->cantidad_horas, 0, ',', '.')}} {{$texto}}</td>
                                                        <td>{{$extension->periodo}}</td>
                                                        <td>
                                                            @if ($extension->extensionUniversitaria->tiene_certificado)
                                                                @if ($extension->url_certificado)
                                                                    <a type="button" class="btn btn-sm btn-primary" href="{{asset($extension->url_certificado)}}" target="_blank" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Visualizar"><i class="ri-eye-fill"></i></a>
                                                                @else
                                                                    <button type="button" class="btn btn-sm btn-danger" disabled><i class="ri-close-fill"></i></button>
                                                                @endif
                                                            @else
                                                                N/A
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr class="text-center">
                                                        <td colspan="6">El alumno no cuenta con extensiones universitarias realizadas.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                        <div class="noresults" style="display: none">
                                            <div class="text-center">
                                                <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" state="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                                <h5 class="mt-2">Sin resultados.</h5>
                                                <p class="text-muted mb-0">No pudimos encontrar ninguna extensión universitaria según tus parámetros de búsqueda.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- end card -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Resumen</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4 mb-3">
                                @php
                                    if ($horas_requeridas != 1) {
                                        $texto = 'horas';
                                    } else {
                                        $texto = 'hora';
                                    }
                                @endphp
                                <span class="text-muted">Horas Requeridas: </span> <span class="fw-bold">{{number_format($horas_requeridas, 0, ',', '.')}} {{$texto}}</span>
                            </div>
                            <div class="col-lg-4 mb-3">
                                @php
                                    if ($extensiones->sum('cantidad_horas') != 1) {
                                        $texto = 'horas';
                                    } else {
                                        $texto = 'hora';
                                    }
                                @endphp
                                <span class="text-muted">Horas Realizadas: </span> <span class="fw-bold">{{number_format($extensiones->sum('cantidad_horas'), 0, ',', '.')}} {{$texto}}</span>
                            </div>
                            <div class="col-lg-4 mb-3">
                                @php
                                    if ($horas_acreditadas != 1) {
                                        $texto = 'horas';
                                    } else {
                                        $texto = 'hora';
                                    }
                                @endphp
                                <span class="text-muted">Horas Acreditadas: </span> <span class="fw-bold">{{number_format($horas_acreditadas, 0, ',', '.')}} {{$texto}}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 mb-3">
                                @php
                                    if ($actividades_requeridas != 1) {
                                        $texto = 'actividades';
                                    } else {
                                        $texto = 'actividad';
                                    }
                                @endphp
                                <span class="text-muted">Actividades Requeridas: </span> <span class="fw-bold">{{number_format($actividades_requeridas, 0, ',', '.')}} {{$texto}}</span>
                            </div>
                            <div class="col-lg-4 mb-3">
                                @php
                                    if (($actividades_realizadas) != 1) {
                                        $texto = 'actividades';
                                    } else {
                                        $texto = 'actividad';
                                    }
                                @endphp
                                <span class="text-muted">Actividades Realizadas: </span> <span class="fw-bold">{{number_format(($actividades_realizadas), 0, ',', '.')}} {{$texto}}</span>
                            </div>
                        </div>
                    </div>
                </div><!-- end card -->
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
        <form action="{{route('alumnos.reporte_extensiones', $alumno->id)}}" method="get" id="generar-reporte-form" target="_blank">
            @csrf
        </form>
    @endsection

    @section('script')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        @include('alumnos.scripts.show_extensiones-scripts')
    @endsection
@endcan
