@extends('layouts.master-academic')
@section('title') Auditoría @endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Administración @endslot
        @slot('href') {{ route('auditoria.index') }} @endslot
        @slot('title') Auditoría @endslot
    @endcomponent

    <form method="get" action="{{ route('auditoria.index') }}" class="card">
        <div class="card-header"><h4 class="card-title mb-0">Filtros</h4></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-lg-3">
                    <label class="form-label">Módulo</label>
                    <select name="modulo" class="form-select">
                        <option value="">Todos</option>
                        @foreach ($modulos as $clase => $nombre)
                            <option value="{{ $clase }}" @selected(request('modulo') == $clase)>{{ $nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2">
                    <label class="form-label">Acción</label>
                    <select name="evento" class="form-select">
                        <option value="">Todas</option>
                        @foreach ($eventos as $clave => $etiqueta)
                            <option value="{{ $clave }}" @selected(request('evento') == $clave)>{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3">
                    <label class="form-label">Usuario</label>
                    <select name="usuario" class="form-select">
                        <option value="">Todos</option>
                        @foreach ($usuarios as $u)
                            <option value="{{ $u->id }}" @selected(request('usuario') == $u->id)>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-6">
                    <label class="form-label">Desde</label>
                    <input type="date" name="desde" class="form-control" value="{{ request('desde') }}">
                </div>
                <div class="col-lg-2 col-6">
                    <label class="form-label">Hasta</label>
                    <input type="date" name="hasta" class="form-control" value="{{ request('hasta') }}">
                </div>
                <div class="col-lg-6">
                    <label class="form-label">Buscar (ID de registro, texto dentro del cambio, URL)</label>
                    <input type="text" name="buscar" class="form-control" value="{{ request('buscar') }}" placeholder="Ej.: 45, un nombre, /alumnos/editar...">
                </div>
                <div class="col-lg-6 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-success">Aplicar</button>
                    <a href="{{ route('auditoria.index') }}" class="btn btn-warning">Limpiar</a>
                </div>
            </div>
        </div>
    </form>

    <div class="card mt-3">
        <div class="card-header"><h4 class="card-title mb-0">Historial de cambios ({{ $auditorias->total() }})</h4></div>
        <div class="card-body">
            <div class="table-responsive ac-tabla-fija">
                <table class="table align-middle table-nowrap mb-0">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Usuario</th>
                            <th>Acción</th>
                            <th>Módulo</th>
                            <th class="text-center">Registro</th>
                            <th class="text-center">Campos cambiados</th>
                            <th>IP</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($auditorias as $a)
                            @php
                                $camposCambiados = count(array_unique(array_merge(array_keys($a->old_values ?? []), array_keys($a->new_values ?? []))));
                                $claseEvento = ['created' => 'bg-success-subtle text-success', 'updated' => 'bg-info-subtle text-info', 'deleted' => 'bg-danger-subtle text-danger', 'restored' => 'bg-warning-subtle text-warning'][$a->event] ?? 'bg-secondary-subtle text-secondary';
                                $etiquetaEvento = $eventos[$a->event] ?? $a->event;
                            @endphp
                            <tr>
                                <td style="font-size:12px;">{{ $a->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ optional($a->user)->name ?? 'Sistema' }}</td>
                                <td><span class="badge {{ $claseEvento }}">{{ $etiquetaEvento }}</span></td>
                                <td>{{ $modulos[$a->auditable_type] ?? class_basename($a->auditable_type) }}</td>
                                <td class="text-center">#{{ $a->auditable_id }}</td>
                                <td class="text-center">{{ $camposCambiados ?: '-' }}</td>
                                <td style="font-size:11.5px;">{{ $a->ip_address }}</td>
                                <td><a href="{{ route('auditoria.show', $a->id) }}" class="btn btn-sm btn-neutro" title="Ver detalle"><i class="ri-eye-line"></i></a></td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted">No hay movimientos para los filtros elegidos.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $auditorias->links() }}</div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
