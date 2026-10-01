@extends('layouts.master-academic')
@section('title') Detalle de Auditoría @endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Administración @endslot
        @slot('href') {{ route('auditoria.index') }} @endslot
        @slot('title') Detalle de Auditoría @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">Datos del movimiento</h4></div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-lg-2">
                            <label class="form-label">Fecha</label>
                            <input type="text" class="form-control" value="{{ $auditoria->created_at->format('d/m/Y H:i:s') }}" readonly>
                        </div>
                        <div class="col-lg-3">
                            <label class="form-label">Usuario</label>
                            <input type="text" class="form-control" value="{{ optional($auditoria->user)->name ?? 'Sistema' }}" readonly>
                        </div>
                        <div class="col-lg-2">
                            <label class="form-label">Acción</label>
                            <input type="text" class="form-control" value="{{ $eventos[$auditoria->event] ?? $auditoria->event }}" readonly>
                        </div>
                        <div class="col-lg-2">
                            <label class="form-label">Módulo</label>
                            <input type="text" class="form-control" value="{{ $modulo }}" readonly>
                        </div>
                        <div class="col-lg-1">
                            <label class="form-label">Registro</label>
                            <input type="text" class="form-control" value="#{{ $auditoria->auditable_id }}" readonly>
                        </div>
                        <div class="col-lg-2">
                            <label class="form-label">IP</label>
                            <input type="text" class="form-control" value="{{ $auditoria->ip_address }}" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <label class="form-label">URL</label>
                            <input type="text" class="form-control" value="{{ $auditoria->url }}" readonly style="font-size:12px;">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0">Cambios ({{ count($campos) }} {{ count($campos) == 1 ? 'campo' : 'campos' }})</h4></div>
                <div class="card-body">
                    @if (count($campos))
                        <div class="table-responsive">
                            <table class="table align-middle table-nowrap mb-0">
                                <thead>
                                    <tr>
                                        <th style="width:20%">Campo</th>
                                        <th style="width:40%">Antes</th>
                                        <th style="width:40%">Después</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($campos as $campo)
                                        @php
                                            $valorAntes = $antes[$campo] ?? null;
                                            $valorDespues = $despues[$campo] ?? null;
                                            $cambio = array_key_exists($campo, $antes) && array_key_exists($campo, $despues) && $valorAntes != $valorDespues;
                                        @endphp
                                        <tr>
                                            <td class="ac-mono" style="font-size:12px;">{{ $campo }}</td>
                                            <td class="{{ $cambio ? 'bg-danger-subtle' : '' }}" style="font-size:12.5px; word-break:break-word;">
                                                {{ is_bool($valorAntes) ? ($valorAntes ? 'true' : 'false') : ($valorAntes ?? '—') }}
                                            </td>
                                            <td class="{{ $cambio ? 'bg-success-subtle' : '' }}" style="font-size:12.5px; word-break:break-word;">
                                                {{ is_bool($valorDespues) ? ($valorDespues ? 'true' : 'false') : ($valorDespues ?? '—') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <span class="text-muted">Este movimiento no registró campos (puede ser un evento sin cambios de valor).</span>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 text-end mb-3">
                    <a href="{{ route('auditoria.index') }}" class="btn btn-neutro">Volver</a>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
