@extends('layouts.master-academic')
@section('title') Carga Masiva @endsection
@section('css')
    <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Gestión Académica @endslot
        @slot('href') {{ route('alumnos.index') }} @endslot
        @slot('title') Carga Masiva de Alumnos y Docentes @endslot
    @endcomponent

    @include('extensiones_universitarias.scripts.messages-scripts')

    @if ($resultado)
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    Resultado de {{ $resultado['modo'] === 'validar' ? 'la validación (no se guardó nada)' : 'la importación' }}
                    — {{ $resultado['tipo'] === 'alumnos' ? 'Alumnos' : 'Docentes' }}
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

    <div class="row">
        @php
            $bloques = [
                ['alumnos', 'Alumnos', 'crear_alumnos', 'Carrera y año de ingreso son obligatorios: de ahí salen la facultad y el semestre.'],
                ['docentes', 'Docentes', 'crear_docentes', 'Las carreras se separan con el signo | (por ejemplo Informática|Derecho). Nivel académico, didáctica y tutor son opcionales.'],
            ];
        @endphp
        @foreach ($bloques as [$tipo, $titulo, $permiso, $ayuda])
            @can($permiso)
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h4 class="card-title mb-0 flex-grow-1">{{ $titulo }}</h4>
                            <a href="{{ route('importaciones.plantilla', $tipo) }}" class="btn btn-sm btn-warning"><i class="ri-download-line align-bottom me-1"></i>Descargar plantilla</a>
                        </div>
                        <div class="card-body">
                            <p class="text-muted" style="font-size: 13px">{{ $ayuda }}</p>
                            <form action="{{ route('importaciones.procesar', $tipo) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <label class="form-label" for="archivo-{{ $tipo }}">Archivo CSV</label>
                                <input type="file" class="form-control mb-3" id="archivo-{{ $tipo }}" name="archivo" accept=".csv,text/csv" required>
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
                    <div class="fw-bold mb-1">Nacionalidades</div>
                    @foreach ($nacionalidades as $nacionalidad)<div>{{ $nacionalidad->nombre }}</div>@endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
@endsection
