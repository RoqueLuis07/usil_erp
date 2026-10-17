@extends('layouts.master-academic')
@section('title') Carga Masiva @endsection
@section('css')
    <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Gestión Académica @endslot
        @slot('href') {{ route('alumnos.index') }} @endslot
        @slot('title') Carga Masiva de Alumnos, Docentes y Actividades @endslot
    @endcomponent

    @include('extensiones_universitarias.scripts.messages-scripts')

    @if ($resultado)
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    Resultado de {{ $resultado['modo'] === 'validar' ? 'la validación (no se guardó nada)' : 'la importación' }}
                    — {{ ['alumnos' => 'Alumnos', 'docentes' => 'Docentes', 'extensiones' => 'Actividades (una fila por alumno participante)'][$resultado['tipo']] }}
                </h4>
            </div>
            <div class="card-body">
                <div class="row text-center mb-3">
                    <div class="col"><div class="fs-4 fw-bold">{{ $resultado['total'] }}</div><div class="text-muted">Filas leídas</div></div>
                    <div class="col"><div class="fs-4 fw-bold text-success">{{ $resultado['creados'] }}</div><div class="text-muted">{{ $resultado['modo'] === 'validar' ? 'Se crearían' : 'Creados' }}</div></div>
                    <div class="col"><div class="fs-4 fw-bold text-info">{{ $resultado['actualizados'] }}</div><div class="text-muted">{{ $resultado['modo'] === 'validar' ? 'Se actualizarían' : 'Actualizados' }}</div></div>
                    <div class="col"><div class="fs-4 fw-bold text-danger">{{ count($resultado['errores']) }}</div><div class="text-muted">Con errores</div></div>
                </div>
                @if (!empty($resultado['ignoradas']))
                    <p class="text-muted" style="font-size: 12px">Columnas ignoradas por no ser parte de la plantilla: {{ implode(', ', $resultado['ignoradas']) }}.</p>
                @endif
                @if (count($resultado['errores']))
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead><tr><th style="width:80px">Fila</th><th style="width:140px">Documento</th><th>Problema</th></tr></thead>
                            <tbody>
                                @foreach ($resultado['errores'] as $error)
                                    <tr><td class="text-center">{{ $error['fila'] }}</td><td>{{ $error['documento'] }}</td><td>{{ $error['mensaje'] }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="text-muted mt-2 mb-0" style="font-size: 12px">Corregí esas filas en el archivo y volvé a cargarlo: las que ya se importaron se actualizan, no se duplican.</p>
                @else
                    <div class="alert alert-success mb-0">Todas las filas están correctas.</div>
                @endif
            </div>
        </div>
    @endif

    @if ($reversion)
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    {{ $reversion['modo'] === 'simular' ? 'Simulación de reversión (no se deshizo nada)' : 'Reversión realizada' }}
                </h4>
            </div>
            <div class="card-body">
                <div class="row text-center mb-3">
                    <div class="col"><div class="fs-4 fw-bold text-danger">{{ $reversion['eliminados'] }}</div><div class="text-muted">{{ $reversion['modo'] === 'simular' ? 'Se eliminarían' : 'Eliminados' }}</div></div>
                    <div class="col"><div class="fs-4 fw-bold text-info">{{ $reversion['restaurados'] }}</div><div class="text-muted">{{ $reversion['modo'] === 'simular' ? 'Se restaurarían' : 'Restaurados' }}</div></div>
                    <div class="col"><div class="fs-4 fw-bold">{{ $reversion['ya_no_existian'] }}</div><div class="text-muted">Ya no existían</div></div>
                    <div class="col"><div class="fs-4 fw-bold text-warning">{{ count($reversion['no_revertidos']) }}</div><div class="text-muted">No se pueden deshacer</div></div>
                </div>
                @if (count($reversion['no_revertidos']))
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead><tr><th style="width:240px">Registro</th><th>Motivo</th></tr></thead>
                            <tbody>
                                @foreach (array_slice($reversion['no_revertidos'], 0, 100) as $fila)
                                    <tr><td>{{ $fila['registro'] }}</td><td>{{ $fila['motivo'] }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if (count($reversion['no_revertidos']) > 100)
                        <p class="text-muted mt-2 mb-0" style="font-size: 12px">Se muestran los primeros 100.</p>
                    @endif
                @else
                    <div class="alert alert-success mb-0">Todo se puede deshacer sin conflictos.</div>
                @endif
            </div>
        </div>
    @endif

    <div class="row">
        @php
            $bloques = [
                ['alumnos', 'Alumnos', 'crear_alumnos', 'Carrera y año de ingreso son obligatorios: de ahí salen la facultad y el semestre.'],
                ['docentes', 'Docentes', 'crear_docentes', 'Las carreras se separan con el signo | (por ejemplo Informática|Derecho). Nivel académico, didáctica y tutor son opcionales.'],
                ['extensiones', 'Actividades de extensión', 'crear_extensiones_universitarias', 'Una fila por alumno y actividad: las filas con el mismo nombre y fecha forman una sola actividad. Los alumnos tienen que estar cargados antes (se buscan por documento). Tipo de actividad vacío = Extensión académica.'],
            ];
        @endphp
        @foreach ($bloques as [$tipo, $titulo, $permiso, $ayuda])
            @can($permiso)
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h4 class="card-title mb-0 flex-grow-1">{{ $titulo }}</h4>
                            <a href="{{ route('importaciones.plantilla', $tipo) }}" class="btn btn-sm btn-warning"><i class="ri-download-line align-bottom me-1"></i>Descargar plantilla</a>
                        </div>
                        <div class="card-body">
                            <p class="text-muted" style="font-size: 13px">{{ $ayuda }}</p>
                            <form action="{{ route('importaciones.procesar', $tipo) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <label class="form-label" for="archivo-{{ $tipo }}">Archivo Excel (.xlsx) o CSV</label>
                                <input type="file" class="form-control mb-2" id="archivo-{{ $tipo }}" name="archivo" accept=".xlsx,.csv,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <label class="form-label mb-0" for="parte-{{ $tipo }}" style="font-size: 12.5px">Parte (hoja "{{ ["alumnos" => "Alumnos", "docentes" => "Docentes", "extensiones" => "Actividades"][$tipo] }} N", solo Excel)</label>
                                    <input type="number" class="form-control" id="parte-{{ $tipo }}" name="parte" value="1" min="1" max="50" style="width: 80px">
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" name="modo" value="validar" class="btn btn-warning">Solo validar</button>
                                    <button type="submit" name="modo" value="importar" class="btn btn-success">Importar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endcan
        @endforeach
    </div>

    @can('revertir_importaciones')
        <div class="card">
            <div class="card-header"><h4 class="card-title mb-0">Historial de importaciones y reversión</h4></div>
            <div class="card-body">
                <p class="text-muted" style="font-size: 13px">Cada "Importar" queda registrado. Primero usá <b>Ver qué se deshace</b> (no cambia nada) y recién después <b>Revertir</b>: lo que la carga creó se elimina y lo que cambió vuelve a como estaba. Se revierte de la más reciente a la más antigua; si un registro ya lo usan datos posteriores, queda y se informa.</p>
                @if (count($lotes))
                    <div class="table-responsive">
                        <table class="table align-middle mb-0" style="font-size: 12.5px">
                            <thead><tr><th>Fecha</th><th>Usuario</th><th>Tipo</th><th class="text-center">Creados</th><th class="text-center">Cambiados</th><th>Detalle</th><th style="width:230px">Acciones</th></tr></thead>
                            <tbody>
                                @foreach ($lotes as $lote)
                                    <tr>
                                        <td>{{ $lote['inicio']->format('d/m/Y H:i') }}</td>
                                        <td>{{ $usuariosLotes[$lote['user_id']] ?? '—' }}</td>
                                        <td>{{ ['alumnos' => 'Alumnos', 'docentes' => 'Docentes', 'extensiones' => 'Actividades'][$lote['tipo']] }}</td>
                                        <td class="text-center">{{ $lote['creados'] }}</td>
                                        <td class="text-center">{{ $lote['actualizados'] }}</td>
                                        <td>
                                            @foreach ($lote['modelos'] as $modelo => $eventos)
                                                <div>{{ \Illuminate\Support\Str::headline($modelo) }}: {{ $eventos['created'] ?? 0 }} nuevos, {{ $eventos['updated'] ?? 0 }} cambiados</div>
                                            @endforeach
                                        </td>
                                        <td>
                                            <form action="{{ route('importaciones.revertir') }}" method="post" class="d-flex gap-1 form-revertir" data-fecha="{{ $lote['inicio']->format('d/m/Y H:i') }}">
                                                @csrf
                                                <input type="hidden" name="desde" value="{{ $lote['desde'] }}">
                                                <input type="hidden" name="hasta" value="{{ $lote['hasta'] }}">
                                                <button type="submit" name="modo" value="simular" class="btn btn-sm btn-warning">Ver qué se deshace</button>
                                                <button type="submit" name="modo" value="revertir" class="btn btn-sm btn-danger">Revertir</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <span class="text-muted">Todavía no hay importaciones registradas, o ya se revirtieron todas.</span>
                @endif
            </div>
        </div>
    @endcan

    <div class="card">
        <div class="card-header"><h4 class="card-title mb-0">Valores válidos para la plantilla</h4></div>
        <div class="card-body">
            <p class="text-muted" style="font-size: 13px">Los nombres se comparan sin distinguir mayúsculas ni tildes. Ciudad y barrio se crean solos si no existen (el departamento sí tiene que existir). Fechas: dd/mm/aaaa. Sexo: Masculino / Femenino.</p>
            <div class="row" style="font-size: 12.5px">
                <div class="col-lg-4 mb-3">
                    <div class="fw-bold mb-1">Carreras</div>
                    @forelse ($carreras as $carrera)
                        <div>{{ $carrera->nombre_fantasia }} <span class="text-muted">— {{ optional($carrera->Facultad)->nombre }}</span></div>
                    @empty
                        <span class="text-danger">No hay carreras: cargalas en Facultades y Carreras.</span>
                    @endforelse
                </div>
                <div class="col-lg-3 mb-3">
                    <div class="fw-bold mb-1">Departamentos</div>
                    @foreach ($departamentos as $departamento)<div>{{ $departamento->nombre }}</div>@endforeach
                </div>
                <div class="col-lg-2 mb-3">
                    <div class="fw-bold mb-1">Nivel académico</div>
                    @foreach ($niveles as $nivel)<div>{{ $nivel->nombre }}</div>@endforeach
                </div>
                <div class="col-lg-3 mb-3">
                    <div class="fw-bold mb-1">Tipos de actividad</div>
                    @foreach ($tipos as $tipo_actividad)<div>{{ $tipo_actividad->nombre }}</div>@endforeach
                    <div class="fw-bold mb-1 mt-3">Nacionalidades</div>
                    @foreach ($nacionalidades as $nacionalidad)<div>{{ $nacionalidad->nombre }}</div>@endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script>
        // Confirmación antes de revertir de verdad (la simulación no la necesita).
        document.querySelectorAll('.form-revertir').forEach(function (formulario) {
            formulario.addEventListener('submit', function (evento) {
                if (evento.submitter && evento.submitter.value === 'revertir' && !formulario.dataset.confirmado) {
                    evento.preventDefault();
                    Swal.fire({
                        title: '¿Revertir la importación del ' + formulario.dataset.fecha + '?',
                        text: 'Se eliminará lo que esa carga creó y se restaurará lo que cambió. Conviene haber visto antes "Ver qué se deshace".',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, revertir',
                        cancelButtonText: 'Cancelar',
                        customClass: {confirmButton: 'btn btn-danger me-2', cancelButton: 'btn btn-light'},
                        buttonsStyling: false,
                    }).then(function (resultado) {
                        if (resultado.isConfirmed) {
                            formulario.dataset.confirmado = '1';
                            var oculto = document.createElement('input');
                            oculto.type = 'hidden'; oculto.name = 'modo'; oculto.value = 'revertir';
                            formulario.appendChild(oculto);
                            formulario.submit();
                        }
                    });
                }
            });
        });
    </script>
@endsection
