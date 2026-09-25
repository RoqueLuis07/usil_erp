@can('ver_extensiones_alumnos_pantalla')
    @extends('layouts.master-academic')
    @section('title') Extensión Universitaria @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('title') BIENVENIDO, {{$alumno->primer_nombre}} {{$alumno->primer_apellido}} @endslot
        @endcomponent

        @include('pantallas_alumnos.modals.extensiones_universitarias-modals')
        @include('index.scripts.messages-scripts')

        <div class="row d-flex flex-wrap justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex flex-wrap align-items-center">
                        <div class="col-lg-6">
                            <h4 class="card-title mb-0">Extensión Universitaria</h4>
                        </div>
                        @can('ver_catalogo_extensiones_alumnos_pantalla')
                            <div class="col-lg-6 text-end">
                                <a type="button" class="btn btn-success" href="{{route('pantallas_alumnos.catalogo_extensiones_universitarias', Auth::id())}}"><i class="ri-search-line align-bottom me-1"></i>Explorar Catálogo</a>
                            </div>
                        @endcan
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-pills arrow-navtabs nav-info nav-justified bg-light gap-2 mb-4" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" data-bs-toggle="tab" href="#todos" role="tab" aria-selected="true">Actividades</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#postulaciones" role="tab" aria-selected="false">Mis Postulaciones</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#tipos" role="tab" aria-selected="false">Resumen por Tipo de Actividad</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active show" id="todos" role="tabpanel">
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
                                                    <th>Horas Cumplidas</th>
                                                    <th>Horas Totales</th>
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
                                                            if ($extension->cantidad_horas != 1) {
                                                                $texto = 'horas';
                                                            } else {
                                                                $texto = 'hora';
                                                            }
                                                        @endphp
                                                        <td>{{number_format($extension->cantidad_horas, 0, ',', '.')}} {{$texto}}</td>
                                                        @php
                                                            if ($extension->extensionUniversitaria->cantidad_horas != 1) {
                                                                $texto = 'horas';
                                                            } else {
                                                                $texto = 'hora';
                                                            }
                                                        @endphp
                                                        <td>{{number_format($extension->extensionUniversitaria->cantidad_horas, 0, ',', '.')}} {{$texto}}</td>
                                                        <td>{{$extension->periodo}}</td>
                                                        <td>
                                                            @if ($extension->extensionUniversitaria->estado == 'IN' && $extension->extensionUniversitaria->tiene_certificado)
                                                                @if (!$extension->url_certificado)
                                                                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#subirAdjuntoModal-{{$extension->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Adjuntar"><i class="ri-upload-line"></i></button>
                                                                @else
                                                                    <a type="button" class="btn btn-sm btn-primary" href="{{asset($extension->url_certificado)}}" target="_blank" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Visualizar"><i class="ri-eye-fill"></i></a>
                                                                    <button type="button" class="btn btn-sm btn-danger delete-adjunto-btn" data-id="{{$extension->id}}" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Eliminar"><i class="ri-delete-bin-fill"></i></button>
                                                                    <form action="{{route('pantallas_alumnos.eliminar_certificado_extensiones_universitarias', $extension->id)}}" method="delete" id="delete-adjunto-form-{{$extension->id}}">
                                                                        @csrf
                                                                    </form>
                                                                @endif
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr class="text-center">
                                                        <td colspan="6">No tienes extensiones universitarias realizadas.</td>
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
                            <div class="tab-pane" id="postulaciones" role="tabpanel">
                                <div class="table-responsive table-card mt-3 mb-1">
                                    <table class="table align-middle table-nowrap">
                                        <thead class="table-light text-center">
                                            <tr>
                                                <th>Proyecto</th>
                                                <th>Responsable</th>
                                                <th>Fecha de Postulación</th>
                                                <th>Estado</th>
                                                <th>Motivo de Rechazo</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-center">
                                            @forelse ($postulaciones as $postulacion)
                                                <tr>
                                                    <td>{{$postulacion->extensionUniversitaria->nombre}}</td>
                                                    <td>{{$postulacion->extensionUniversitaria->docente->primer_nombre}} {{$postulacion->extensionUniversitaria->docente->primer_apellido}}</td>
                                                    <td>{{ ($postulacion->fecha_postulacion ?? $postulacion->created_at) ? \Carbon\Carbon::parse($postulacion->fecha_postulacion ?? $postulacion->created_at)->format('d/m/Y H:i') : '-' }}</td>
                                                    <td>
                                                        <span class="badge @if ($postulacion->estado == 'AC') bg-success-subtle text-success @elseif ($postulacion->estado == 'RE') bg-danger-subtle text-danger @else bg-warning-subtle text-warning @endif">
                                                            @if ($postulacion->estado == 'AC') Aceptada @elseif ($postulacion->estado == 'RE') Rechazada @else Pendiente @endif
                                                        </span>
                                                    </td>
                                                    <td>{{$postulacion->motivo_rechazo ?? '-'}}</td>
                                                    <td>
                                                        @if ($postulacion->estado == 'PE')
                                                            <form action="{{route('pantallas_alumnos.cancelar_postulacion_extension_universitaria', [Auth::id(), $postulacion->id])}}" method="post">
                                                                @csrf
                                                                @method('delete')
                                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Cancelar esta postulación?')">Cancelar</button>
                                                            </form>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6">Todavía no te postulaste a ningún proyecto.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane" id="tipos" role="tabpanel">
                                <div id="tipos-list">
                                    <div class="table-responsive table-card mt-3 mb-1">
                                        <table class="table align-middle table-nowrap" id="tipos-list">
                                            <thead class="table-light text-center">
                                                <tr>
                                                    <th>Tipo de Actividad</th>
                                                    <th>Cant. Realizada</th>
                                                    <th>Máximo</th>
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
                        </div>
                    </div>
                </div><!-- end card -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-1">Resumen</h4>
                        <div class="text-muted" style="font-size: 12.5px">
                            Carrera: <b>{{ optional($alumno->Carrera)->nombre_fantasia ?? 'sin cargar' }}</b>
                            · Facultad: <b>{{ optional(optional($alumno->Carrera)->Facultad)->nombre ?? '-' }}</b>
                            · Ingreso: <b>{{ $alumno->ingreso_texto ?? '-' }}</b>
                            · Semestre actual: <b>{{ $alumno->semestre_actual ? $alumno->semestre_actual . '.º' : '-' }}</b>
                        </div>
                        @php
                            $porcentaje_horas = $horas_requeridas > 0 ? min(100, round($horas_acreditadas / $horas_requeridas * 100)) : 0;
                        @endphp
                        <div class="mt-2" style="font-size: 12px">
                            <div class="d-flex justify-content-between"><span>Avance de horas acreditadas</span><span class="ac-mono">{{ number_format($horas_acreditadas, 0, ',', '.') }} / {{ number_format($horas_requeridas, 0, ',', '.') }} h · {{ $porcentaje_horas }}%</span></div>
                            <div style="height: 8px; background: #eef2f3;"><div style="height: 8px; width: {{ $porcentaje_horas }}%; background: var(--ac-accent);"></div></div>
                        </div>
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
        @include('pantallas_alumnos.scripts.extensiones_universitarias-scripts')
    @endsection
@endcan
