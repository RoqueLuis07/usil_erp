@can('ver_dashboard_docentes_pantalla')
    @extends('layouts.master')
    @section('title') Inicio @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
                @slot('title') BIENVENIDO, {{$docente->primer_nombre}} {{$docente->primer_apellido}} @endslot
        @endcomponent

        @include('pantallas_docentes.modals.index-modals')
        @include('pantallas_docentes.scripts.messages-scripts')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title mb-0">Que quieres hacer hoy ?</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row d-flex flex-wrap justify-content-center">
                                            @can('crear_clases_docentes_pantalla')
                                                <div class="col-lg-2">
                                                    <div class="card card-body text-center">
                                                        <div class="avatar-sm mx-auto mb-3">
                                                            <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                                                <i class="bi bi-book-fill"></i>
                                                            </div>
                                                        </div>
                                                        <h4 class="card-title">Clases</h4>
                                                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#generateClaseModal" @if ($periodo_activo == null) disabled @endif>Generar</button>
                                                    </div>
                                                </div>
                                            @endcan
                                            @can('crear_asistencias_docentes_pantalla')
                                                <div class="col-lg-2">
                                                    <div class="card card-body text-center">
                                                        <div class="avatar-sm mx-auto mb-3">
                                                            <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                                                <i class="bi bi-list-check"></i>
                                                            </div>
                                                        </div>
                                                        <h4 class="card-title">Asistencias</h4>
                                                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#chargeAsistenciaModal" @if ($periodo_activo == null) disabled @endif>Cargar</button>
                                                    </div>
                                                </div>
                                            @endcan
                                            @can('crear_evaluaciones_docentes_pantalla')
                                                <div class="col-lg-2">
                                                    <div class="card card-body text-center">
                                                        <div class="avatar-sm mx-auto mb-3">
                                                            <div class="avatar-title bg-info-subtle text-info fs-x1 rounded">
                                                                <i class="bi bi-file-earmark-check"></i>
                                                            </div>
                                                        </div>
                                                        <h4 class="card-title">Evaluación</h4>
                                                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#chargeEvaluacionModal" @if ($periodo_activo == null) disabled @endif>Cargar</button>
                                                    </div>
                                                </div>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row d-flex flex-wrap justify-content-center">
                            @if ($ga == 'SI')
                                @can('ver_horarios_docentes_pantalla')
                                    <div class="col-lg-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="card-title mb-0">Mi Horario @if ($cpel == 'SI') - Grado Americano @endif</h4>
                                        </div>
                                        <div class="card-body">
                                            <div class="row d-flex flex-wrap justify-content-center">
                                                <div class="col-lg-12">
                                                    <div class="table-responsive table-card mt-3 mb-1">
                                                        <table class="table align-middle">
                                                            <thead class="table-light text-center fw-bold">
                                                                <tr>
                                                                    <td></td>
                                                                    @foreach ($dias_semana as $key => $dia)
                                                                        @if ($key != 0 && $key != 6)
                                                                            <td>{{$dia->nombre}}</td>
                                                                        @endif
                                                                    @endforeach
                                                                </tr>
                                                            </thead>
                                                            <tbody class="text-center">
                                                                @foreach ($horarios_clases as $key => $hora)
                                                                    <tr>
                                                                        @if (!$loop->last)
                                                                        @php
                                                                            $hora_inicio = $hora;
                                                                            $hora_fin = $horarios_clases[$key + 1];
                                                                        @endphp
                                                                            <td class="fw-bold">{{$hora->format('H:i')}} a {{$horarios_clases[$key + 1]->format('H:i')}}</td>
                                                                            @foreach ($dias_semana as $k => $dia)
                                                                                @if ($k != 0 && $k != 6)
                                                                                    @php
                                                                                        $materia = null;
                                                                                        $aula = null;
                                                                                    @endphp
                                                                                    @foreach ($horarios as $horario)
                                                                                        @php
                                                                                            $hora_inicio_horario = Carbon\Carbon::createFromFormat('H:i:s', $horario['hora_inicio']);
                                                                                            $hora_fin_horario = Carbon\Carbon::createFromFormat('H:i:s', $horario['hora_fin']);
                                                                                        @endphp
                                                                                        @if (($hora_inicio_horario->lt($hora_fin) && $hora_fin_horario->gt($hora_inicio)) && $horario['dia'] == $dia->nombre)
                                                                                            @php
                                                                                                $materia = $horario['materia'];
                                                                                                $aula = $horario['aula'];
                                                                                            @endphp
                                                                                            @break
                                                                                        @endif
                                                                                    @endforeach
                                                                                    <td>
                                                                                        {{$materia ? $materia : '-'}}
                                                                                        <br>
                                                                                        <span class="badge bg-primary-subtle text-primary">{{$aula ? 'AULA ' . $aula : ''}}</span>
                                                                                    </td>
                                                                                @endif
                                                                            @endforeach
                                                                        @endif
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endcan
                            @endif
                            @if ($cpel == 'SI')
                                @can('ver_horarios_docentes_pantalla')
                                    <div class="col-lg-6">
                                        <div class="card">
                                            <div class="card-header">
                                                <h4 class="card-title mb-0">Mi Horario @if ($ga == 'SI') - CPEL @endif</h4>
                                            </div>
                                            <div class="card-body">
                                                <div class="row d-flex flex-wrap justify-content-center">
                                                    <div class="col-lg-12">
                                                        <div class="table-responsive table-card mt-3 mb-1">
                                                            <table class="table align-middle">
                                                                <thead class="table-light text-center fw-bold">
                                                                    <tr>
                                                                        <td></td>
                                                                        @foreach ($dias_semana as $key => $dia)
                                                                            @if ($key != 0 && $key != 6)
                                                                                <td>{{$dia->nombre}}</td>
                                                                            @endif
                                                                        @endforeach
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="text-center">
                                                                    @foreach ($horarios_clases_cpel as $key => $hora)
                                                                        <tr>
                                                                            @if (!$loop->last)
                                                                            @php
                                                                                $hora_inicio = $hora;
                                                                                $hora_fin = $horarios_clases[$key + 1];
                                                                            @endphp
                                                                                <td class="fw-bold">{{$hora->format('H:i')}} a {{$horarios_clases[$key + 1]->format('H:i')}}</td>
                                                                                @foreach ($dias_semana as $k => $dia)
                                                                                    @if ($k != 0 && $k != 6)
                                                                                        @php
                                                                                            $materia = null;
                                                                                        @endphp
                                                                                        @foreach ($horarios as $horario)
                                                                                            @php
                                                                                                $hora_inicio_horario = Carbon\Carbon::createFromFormat('H:i:s', $horario['hora_inicio']);
                                                                                                $hora_fin_horario = Carbon\Carbon::createFromFormat('H:i:s', $horario['hora_fin']);
                                                                                            @endphp
                                                                                            @if (($hora_inicio_horario->lt($hora_fin) && $hora_fin_horario->gt($hora_inicio)) && $horario['dia'] == $dia->nombre)
                                                                                                @php
                                                                                                    $materia = $horario['materia'];
                                                                                                @endphp
                                                                                                @break
                                                                                            @endif
                                                                                        @endforeach
                                                                                        <td>{{$materia ? $materia : '-'}}</td>
                                                                                    @endif
                                                                                @endforeach
                                                                            @endif
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endcan
                            @else
                                @can('ver_noticias_avisos_docentes_pantalla')
                                    <div class="@if ($cpel != 'si' || $ga != 'si') col-lg-12 @else col-lg-6 @endif mb-3">
                                        <div class="card">
                                            <div class="card-header d-flex flex-wrap" style="margin-bottom: -2em">
                                                <div class="col-lg-6">
                                                    <h4 class="card-title mb-0">Noticias</h4>
                                                </div>
                                                <div class="col-lg-6 text-end">
                                                    <a type="button" class="btn btn-light" href="{{route('pantallas_alumnos.noticias', Auth::id())}}">Ver Todas las Noticias</a>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-lg-12 mb-3">
                                                        <div id="noticias-list">
                                                            <div class="table-responsive table-card mt-3 mb-1">
                                                                <table class="table align-middle table-nowrap" id="noticias-list">
                                                                    <tbody class="list form-check-all">
                                                                        @forelse ($noticias as $noticia)
                                                                            <tr>
                                                                                <td style="width: 10%"><img src="{{asset($noticia->portada)}}" alt="Portada" style="width: 80px; height: 80px;"></td>
                                                                                <td style="width: 80%">{{$noticia->titulo}} @if ($noticia->destacado) <span class="badge bg-warning-subtle text-warning badge-border">IMPORTANTE</span> @endif</td>
                                                                                <td class="text-center" style="width: 10%"><a type="button" class="btn btn-primary" href="{{route('pantallas_alumnos.show_noticia', $noticia->id)}}">Ver Noticia</a></td>
                                                                            </tr>
                                                                        @empty
                                                                            <tr class="text-center">
                                                                                <td colspan="3">No existen noticias nuevas.</td>
                                                                            </tr>
                                                                        @endforelse
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endcan
                            @endif
                        </div>
                        @if ($cpel == 'SI')
                            @can('ver_noticias_avisos_docentes_pantallas')
                                <div class="row">
                                    <div class="col-lg-12 mb-3">
                                        <div class="card">
                                            <div class="card-header d-flex flex-wrap" style="margin-bottom: -2em">
                                                <div class="col-lg-6">
                                                    <h4 class="card-title mb-0">Noticias</h4>
                                                </div>
                                                @if ($noticias->count() != 0)
                                                    <div class="col-lg-6 text-end">
                                                        <a type="button" class="btn btn-light" href="{{route('pantallas_alumnos.noticias', Auth::id())}}">Ver Todas las Noticias</a>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-lg-12 mb-3">
                                                        <div id="noticias-list">
                                                            <div class="table-responsive table-card mt-3 mb-1">
                                                                <table class="table align-middle table-nowrap" id="noticias-list">
                                                                    <tbody class="list form-check-all">
                                                                        @forelse ($noticias as $noticia)
                                                                            <tr>
                                                                                <td style="width: 10%"><img src="{{asset($noticia->portada)}}" alt="Portada" style="width: 80px; height: 80px;"></td>
                                                                                <td style="width: 80%">{{$noticia->titulo}} @if ($noticia->destacado) <span class="badge bg-warning-subtle text-warning badge-border">IMPORTANTE</span> @endif</td>
                                                                                <td class="text-center" style="width: 10%"><a type="button" class="btn btn-primary" href="{{route('pantallas_alumnos.show_noticia', $noticia->id)}}">Ver Noticia</a></td>
                                                                            </tr>
                                                                        @empty
                                                                            <tr class="text-center">
                                                                                <td colspan="3">No existen noticias nuevas.</td>
                                                                            </tr>
                                                                        @endforelse
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endcan
                        @endif
                    </div><!-- end card body -->
                </div>
                <!-- end card -->
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
    @endsection

    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('js/bootstrap-select.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        @include('pantallas_docentes.scripts.index-scripts')
    @endsection
@endcan
