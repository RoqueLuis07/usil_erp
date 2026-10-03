@can('ver_dashboard_alumnos_pantalla')
    @extends('layouts.master-academic')
    @section('title') Inicio @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
                @slot('title') BIENVENIDO, {{$alumno->primer_nombre}} {{$alumno->primer_apellido}} @endslot
        @endcomponent

        @include('pantallas_alumnos.scripts.messages-scripts')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <!-- end card header -->
                    <div class="card-body">
                        <div class="row">
                            @if ($pago_pendiente)
                                <div class="col-lg-12">
                                    <div class="alert @if (Carbon\Carbon::today() > $pago_pendiente->fecha_vencimiento) alert-danger @else alert-info @endif" role="alert">
                                        <strong>
                                            @if (Carbon\Carbon::today() > $pago_pendiente->fecha_vencimiento)
                                                Recuerde abonar su pago vencido el {{$pago_pendiente->fecha_vencimiento}} en concepto de {{$pago_pendiente->descripcion}}. Éste cuenta con un saldo de Gs. {{$pago_pendiente->saldo}}.
                                            @else
                                                Su próximo pago vence el {{$pago_pendiente->fecha_vencimiento}} en concepto de {{$pago_pendiente->descripcion}}. El monto es de Gs. {{$pago_pendiente->monto}}.
                                            @endif
                                        </strong>
                                    </div>
                                </div>
                            @else
                                <div class="col-lg-12">
                                    <div class="alert alert-primary">
                                        <strong>No cuenta con ningún pago pendiente.</strong>
                                    </div>
                                </div>
                            @endif
                            @if ($fecha_examen_suficiencia)
                                <div class="col-lg-12">
                                    <div class="alert alert-danger" role="alert">
                                        <h3 class="text-danger" style="font-weight:normal!important">
                                            Examen de Suficiencia habilitado. Recordá que el periodo de inscripción estará disponible desde <strong>{{$fecha_examen_suficiencia->fecha_inicio}}</strong> hasta <strong>{{$fecha_examen_suficiencia->fecha_fin}}</strong>. Tenés tiempo para enviar tu solicitud hasta la fecha límite indicada.
                                        </h3>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                @can('ver_calificaciones_alumnos_pantalla')
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title mb-0">Calificaciones</h4>
                                                </div>
                                                <div class="card-body">
                                                    <div id="notas-list">
                                                        <div class="table-responsive table-card mt-3 mb-1">
                                                            <table class="table align-middle table-nowrap" id="notas-list">
                                                                <thead class="table-light text-center">
                                                                    <tr>
                                                                        <th class="sort" data-sort="semestre">Semestre</th>
                                                                        <th class="sort" data-sort="materia">Materia</th>
                                                                        <th>Calificación</th>
                                                                        <th>Evaluación</th>
                                                                        <th class="sort" data-sort="periodo">Periodo</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="list form-check-all text-center">
                                                                    @forelse ($alumno->alumnoNotas as $nota)
                                                                        <tr>
                                                                            <td class="semestre">{{$nota->semestre_materia}}</td>
                                                                            <td class="materia">{{$nota->materia->nombre_fantasia}}</td>
                                                                            <td>{{$nota->calificacion}}</td>
                                                                            <td>{{$nota->evaluacion}}</td>
                                                                            <td class="periodo">{{$nota->semestre->nombre}}</td>
                                                                        </tr>
                                                                    @empty
                                                                        <tr>
                                                                            <td colspan="5">No cuenta con calificaciones registradas en este semestre.</td>
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
                                @endcan
                                @can('ver_asistencias_alumnos_pantalla')
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title mb-0">Asistencias</h4>
                                                </div>
                                                <div class="card-body">
                                                    <div id="asistencias-list">
                                                        <div class="table-responsive table-card mt-3 mb-1">
                                                            <table class="table align-middle table-nowrap" id="asistencias-list">
                                                                <thead class="table-light text-center">
                                                                    <tr>
                                                                        <th>Materia</th>
                                                                        <th>Total de Clases</th>
                                                                        <th>Horas Totales</th>
                                                                        <th>Clases Asistidas</th>
                                                                        <th>Horas Asistidas</th>
                                                                        <th>% Asistencia</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="list form-check-all text-center">
                                                                        @forelse ($asistencias as $asistencia)
                                                                            <tr>
                                                                                <td>{{$asistencia->materia->nombre_fantasia}}</td>
                                                                                @php
                                                                                    if ($asistencia->total_clases != 1) {
                                                                                        $texto = 'clases';
                                                                                    } else {
                                                                                        $texto = 'clase';
                                                                                    }
                                                                                @endphp
                                                                                <td>{{number_format($asistencia->total_clases, 0, ',' ,'.')}} {{$texto}}</td>
                                                                                @php
                                                                                    if ($asistencia->horas_totales != 1) {
                                                                                        $texto = 'horas';
                                                                                    } else {
                                                                                        $texto = 'hora';
                                                                                    }
                                                                                @endphp
                                                                                <td>{{number_format($asistencia->horas_desarrollo, 0, ',' ,'.')}} {{$texto}}</td>
                                                                                @php
                                                                                    if ($asistencia->total_asistido != 1) {
                                                                                        $texto = 'clases';
                                                                                    } else {
                                                                                        $texto = 'clase';
                                                                                    }
                                                                                @endphp
                                                                                <td>{{number_format($asistencia->total_asistido, 0, ',' ,'.')}} {{$texto}}</td>
                                                                                @php
                                                                                    if ($asistencia->total_clases != 1) {
                                                                                        $texto = 'horas';
                                                                                    } else {
                                                                                        $texto = 'hora';
                                                                                    }
                                                                                @endphp
                                                                                <td>{{number_format($asistencia->horas_asistidas, 0, ',' ,'.')}} {{$texto}}</td>
                                                                                <td>{{number_format($asistencia->porcentaje, 0, ',' ,'.')}} %</td>
                                                                            </tr>
                                                                        @empty
                                                                            <tr>
                                                                                <td colspan="6">No cuenta con asistencias registradas en este semestre.</td>
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
                                @endcan
                            </div>
                            @can('ver_solicitudes_alumnos_pantalla')
                                <div class="col-lg-6">
                                    <div class="card">
                                        <div class="card-header d-flex flex-wrap">
                                            <div class="col-lg-6">
                                                <h4 class="card-title mb-0">Últimas Solicitudes</h4>
                                            </div>
                                            @if ($solicitudes->count() != 0)
                                                <div class="col-lg-6 text-end">
                                                    <a type="button" class="btn btn-light" href="{{route('pantallas_alumnos.solicitudes', Auth::id())}}">Ver Todas las Solicitudes</a>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="card-body">
                                            <div id="solicitudes-list">
                                                <div class="table-responsive table-card mt-3 mb-1">
                                                    <table class="table align-middle table-nowrap" id="solicitudes-list">
                                                        <thead class="table-light text-center">
                                                            <tr>
                                                                <th>Fecha Solicitud</th>
                                                                <th>Solicitud</th>
                                                                <th>Estado</th>
                                                                <th>Fecha Aprobación</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="list form-check-all text-center">
                                                                @forelse ($solicitudes as $solicitud)
                                                                    <tr>
                                                                        <td>{{Carbon\Carbon::parse($solicitud->fecha_solicitud)->format('d/m/Y H:i')}}</td>
                                                                        <td>{{ $solicitud->tipoSolicitud->nombre }}</td>
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
                                                                        <td>{{ Carbon\Carbon::parse($solicitud->fecha_aprobacion)->format('d/m/Y H:i') }}</td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="6">No cuenta con solicitudes registradas en este semestre.</td>
                                                                    </tr>
                                                                @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endcan
                        </div>
                    </div><!-- end card -->
                </div>
                <!-- end col -->
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
        <div class="row d-flex flex-wrap justify-content-center">
            @can('ver_horarios_alumnos_pantalla')
                <div class="col-lg-7 mb-3">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Horario de Clases</h4>
                            </div>
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
                                                                    @if ($alumno->programa_id == 1 || $alumno->programa_id == 2)
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
                                                                    @else
                                                                        <td>-</td>
                                                                    @endif
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
                @if ($tutorias->count() > 0)
                    <div class="col-lg-5 mb-3">
                        <div class="card">
                            <div class="card-header d-flex flex-wrap">
                                <div class="col-lg-6">
                                    <h4 class="card-title mb-0">Horarios de Tutorías</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="accordion custom-accordionwithicon custom-accordion-border accordion-border-box accordion-primary" id="acordeon">
                                    @forelse ($tutorias as $tutoria)
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#acordeon-{{$tutoria->id}}" aria-expanded="false" aria-controls="acordeon-{{$tutoria->id}}" id="tutoria-{{$tutoria->id}}">{{$tutoria->nombre}} <small class="text-muted">&nbsp;{{$tutoria->materia->nombre_fantasia}}</small></button>
                                            </h2>
                                            <div class="accordion-collapse collapse" id="acordeon-{{$tutoria->id}}" aria-labelledby="{{$tutoria->id}}-acordeon" data-bs-parent="#acordeon">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        @foreach ($tutoria->horarios as $key => $horario)
                                                            <li><strong>{{$horario->diaSemana->nombre}}</strong>: {{$horario->hora_inicio}} a {{$horario->hora_fin}}</li>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="row d-flex flex-wrap justify-content-center">
                                            <div class="col-lg-12 col-sm-12 mb-2 text-center">
                                                @if ($alumnos->count() != 0)
                                                    <p>La materia no cuenta con evaluaciones realizadas.</p>
                                                @else
                                                <p>La materia no cuenta con alumnos inscriptos.</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col-lg-5 mb-3">
                        <div class="card">
                            <div class="card-header d-flex flex-wrap">
                                <div class="col-lg-6">
                                    <h4 class="card-title mb-0">Avisos</h4>
                                </div>
                                @if ($avisos->count() != 0)
                                    <div class="col-lg-6 text-end">
                                        <a type="button" class="btn btn-light" href="{{route('pantallas_alumnos.avisos', Auth::id())}}">Ver Todos los Avisos</a>
                                    </div>
                                @endif
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12 mb-3">
                                        <div id="avisos-list">
                                            <div class="table-responsive table-card mt-3 mb-1">
                                                <table class="table align-middle table-nowrap" id="avisos-list">
                                                    <tbody class="list form-check-all">
                                                        @forelse ($avisos as $key => $aviso)
                                                            <tr>
                                                                <td style="width: 10%"><img src="{{asset($aviso->portada)}}" alt="Portada" style="width: 80px; height: 80px;"></td>
                                                                <td style="width: 80%">{{$aviso->titulo}} @if ($aviso->destacado) <span class="badge bg-warning-subtle text-warning badge-border">IMPORTANTE</span> @endif</td>
                                                                <td class="text-center" style="width: 10%"><a type="button" class="btn btn-primary" href="{{route('pantallas_alumnos.show_aviso', $aviso->id)}}">Ver Aviso</a></td>
                                                            </tr>
                                                        @empty
                                                        <tr class="text-center">
                                                            <td colspan="3">No existen avisos nuevos.</td>
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
                @endif
            @endcan
        </div>
        @can('ver_noticias_avisos_alumnos_pantalla')
            <div class="row">
                @if ($tutorias->count() > 0)
                    <div class="col-lg-6 mb-3">
                        <div class="card">
                            <div class="card-header d-flex flex-wrap">
                                <div class="col-lg-6">
                                    <h4 class="card-title mb-0">Avisos</h4>
                                </div>
                                @if ($avisos->count() != 0)
                                    <div class="col-lg-6 text-end">
                                        <a type="button" class="btn btn-light" href="{{route('pantallas_alumnos.avisos', Auth::id())}}">Ver Todos los Avisos</a>
                                    </div>
                                @endif
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12 mb-3">
                                        <div id="avisos-list">
                                            <div class="table-responsive table-card mt-3 mb-1">
                                                <table class="table align-middle table-nowrap" id="avisos-list">
                                                    <tbody class="list form-check-all">
                                                        @forelse ($avisos as $key => $aviso)
                                                            <tr>
                                                                <td style="width: 10%"><img src="{{asset($aviso->portada)}}" alt="Portada" style="width: 80px; height: 80px;"></td>
                                                                <td style="width: 80%">{{$aviso->titulo}} @if ($aviso->destacado) <span class="badge bg-warning-subtle text-warning badge-border">IMPORTANTE</span> @endif</td>
                                                                <td class="text-center" style="width: 10%"><a type="button" class="btn btn-primary" href="{{route('pantallas_alumnos.show_aviso', $aviso->id)}}">Ver Aviso</a></td>
                                                            </tr>
                                                        @empty
                                                        <tr class="text-center">
                                                            <td colspan="3">No existen avisos nuevos.</td>
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
                @endif
                <div class="@if ($tutorias->count() > 0) col-lg-6 @else col-lg-12 @endif mb-3">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
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
    @endsection

    @section('script')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        @include('pantallas_alumnos.scripts.index-scripts')
    @endsection
@endcan
