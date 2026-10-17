{{-- Lista de participantes de una actividad, agrupada por estado de la postulación:
     pendientes arriba (son las que requieren acción), luego aceptados y por último rechazados.
     Todas las filas comparten las mismas columnas para que queden alineadas.
     Requiere: $extension y $puede_gestionar_alumnos. --}}
@php
    $ordenados = $extension->extensionUniversitariaDetalles->sortBy(function ($d) {
        return Str::lower($d->alumno->primer_apellido . ' ' . $d->alumno->primer_nombre);
    });
    $grupos = [
        ['titulo' => 'Postulaciones pendientes', 'clase' => 'warning', 'lista' => $ordenados->where('estado', 'PE')->values()],
        ['titulo' => 'Aceptados', 'clase' => 'success', 'lista' => $ordenados->where('estado', 'AC')->values()],
        ['titulo' => 'Rechazados', 'clase' => 'danger', 'lista' => $ordenados->where('estado', 'RE')->values()],
    ];
    $mostrar_certificado = $extension->estado == 'IN' && $extension->tiene_certificado;
@endphp

@foreach ($grupos as $grupo)
    @if ($grupo['lista']->count())
        <div class="mb-4">
            <div class="mb-2">
                <span class="badge bg-{{ $grupo['clase'] }}-subtle text-{{ $grupo['clase'] }} text-uppercase">{{ $grupo['titulo'] }} · {{ $grupo['lista']->count() }}</span>
            </div>
            <div class="row d-flex justify-content-center">
                <div class="col-5 col-lg-2 mb-1 text-center"><label class="form-label">N° Documento</label></div>
                <div class="col-5 col-lg-3 mb-1 text-center"><label class="form-label">Alumno</label></div>
                <div class="col-3 col-lg-2 mb-1 text-center"><label class="form-label">Cant. Horas</label></div>
                @if ($mostrar_certificado)
                    <div class="col-lg-2 mb-1 text-center"><label class="form-label">Certificado</label></div>
                @endif
                <div class="col-5 col-lg-2 mb-1 text-center"><label class="form-label">Postulación</label></div>
                @if ($puede_gestionar_alumnos)
                    <div class="col-5 col-lg-2 mb-1 text-center"><label class="form-label">Acciones</label></div>
                @endif
            </div>
            @foreach ($grupo['lista'] as $detalle)
                <div class="row d-flex justify-content-center align-items-center mb-2">
                    <div class="col-5 col-lg-2 text-center">
                        <input type="text" class="form-control text-center" value="{{ $detalle->alumno->numero_documento }}" readonly>
                    </div>
                    <div class="col-5 col-lg-3 text-center">
                        <input type="text" class="form-control text-center" value="{{ $detalle->alumno->primer_nombre }} {{ $detalle->alumno->primer_apellido }}" readonly>
                    </div>
                    <div class="col-3 col-lg-2 text-center">
                        <input type="text" class="form-control text-center"
                            value="@if ($detalle->cantidad_horas){{ number_format($detalle->cantidad_horas, 2, ',', '.') }} {{ $detalle->cantidad_horas == 1 ? 'hora' : 'horas' }}@elseif ($detalle->estado == 'AC')0 horas @else — @endif" readonly>
                    </div>
                    @if ($mostrar_certificado)
                        <div class="col-lg-2 text-center">
                            @if ($detalle->url_certificado)
                                <a type="button" class="btn btn-sm btn-info" href="{{ asset($detalle->url_certificado) }}" target="_blank"><i class="ri-eye-fill"></i></a>
                            @else
                                <button type="button" class="btn btn-sm btn-danger" disabled><i class="ri-close-fill"></i></button>
                            @endif
                        </div>
                    @endif
                    <div class="col-5 col-lg-2 text-center">
                        <span class="badge @if ($detalle->estado == 'AC') bg-success-subtle text-success @elseif ($detalle->estado == 'RE') bg-danger-subtle text-danger @else bg-warning-subtle text-warning @endif">
                            @if ($detalle->estado == 'AC') Aceptada @elseif ($detalle->estado == 'RE') Rechazada @else Pendiente @endif
                        </span>
                    </div>
                    @if ($puede_gestionar_alumnos)
                        <div class="col-5 col-lg-2 text-center text-nowrap">
                            @if ($detalle->estado == 'PE')
                                <form action="{{ route('extensiones_universitarias.aprobar_postulacion', $detalle->id) }}" method="post" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Aceptar postulación"><i class="ri-check-fill"></i></button>
                                </form>
                                <form action="{{ route('extensiones_universitarias.rechazar_postulacion', $detalle->id) }}" method="post" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Rechazar postulación"><i class="ri-close-fill"></i></button>
                                </form>
                            @endif
                            <form action="{{ route('extensiones_universitarias.quitar_alumno', $detalle->id) }}" method="post" class="d-inline form-quitar-alumno"
                                data-nombre="{{ $detalle->alumno->primer_nombre }} {{ $detalle->alumno->primer_apellido }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Quitar de la actividad"><i class="ri-delete-bin-line"></i></button>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
@endforeach

@if ($ordenados->isEmpty())
    <span class="text-muted">Todavía no hay alumnos en esta actividad.</span>
@endif
