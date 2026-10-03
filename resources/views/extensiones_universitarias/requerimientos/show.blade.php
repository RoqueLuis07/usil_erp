@can('ver_requerimientos_extensiones_universitarias')
    @extends('layouts.master-academic')
    @section('title') Requerimientos de Extensión Universitaria @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Extensiones Universitarias @endslot
            @slot('title') Requerimientos  @endslot
        @endcomponent

        @include('extensiones_universitarias.requerimientos.scripts.messages-scripts')

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex flex-wrap">
                        <div class="col-lg-6">
                            <h4 class="card-title mb-0">Requerimientos de graduación por carrera</h4>
                        </div>
                        <div class="col-lg-6 text-end">
                            @can('editar_requerimientos_extensiones_universitarias')
                                <a type="button" class="btn btn-success" href="{{route('requerimientos_extensiones_universitarias.create')}}"><i class="ri-add-line align-bottom me-1"></i>Agregar</a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">La fila <b>General</b> se usa como respaldo para cualquier carrera que no tenga su propio requerimiento definido.</p>
                        <div class="table-responsive table-card mt-3 mb-1">
                            <table class="table align-middle text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>Carrera</th>
                                        <th>Actividades Requeridas</th>
                                        <th>Horas Requeridas</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($requerimientos as $requerimiento)
                                        <tr>
                                            <td>
                                                @if ($requerimiento->carrera_id)
                                                    {{ $requerimiento->carrera->nombre_fantasia ?? 'Carrera eliminada' }}
                                                @else
                                                    <span class="badge bg-info">General</span>
                                                @endif
                                            </td>
                                            <td>{{ $requerimiento->actividades_requeridas }}</td>
                                            <td>{{ number_format($requerimiento->horas_requeridas, 2, ',', '.') }} horas</td>
                                            <td>
                                                @can('editar_requerimientos_extensiones_universitarias')
                                                    <a href="{{route('requerimientos_extensiones_universitarias.edit', $requerimiento->id)}}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Editar"><i class="ri-pencil-fill"></i></a>
                                                    <button class="btn btn-sm btn-danger destroy-btn" data-id="{{ $requerimiento->id }}" data-bs-toggle="tooltip" title="Eliminar"><i class="ri-delete-bin-fill"></i></button>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 text-end mb-3">
                        <a type="button" class="btn btn-danger" href="{{route('extensiones_universitarias.index')}}">Volver</a>
                    </div>
                </div>
            </div>
        </div>

        @can('editar_requerimientos_extensiones_universitarias')
            @foreach ($requerimientos as $requerimiento)
                <form id="destroy-form-{{ $requerimiento->id }}" action="{{route('requerimientos_extensiones_universitarias.destroy', $requerimiento->id)}}" method="post" class="d-none">
                    @csrf
                    @method('delete')
                </form>
            @endforeach
        @endcan
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
        <script>
            document.querySelectorAll('.destroy-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const id = btn.getAttribute('data-id');
                    Swal.fire({
                        title: '¿Está seguro de eliminar este requerimiento?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar!',
                        cancelButtonText: 'Cancelar',
                        customClass: { confirmButton: 'btn btn-success me-2', cancelButton: 'btn btn-light' },
                        buttonsStyling: false
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            document.getElementById('destroy-form-' + id).submit();
                        }
                    });
                });
            });
        </script>
        @include('extensiones_universitarias.requerimientos.scripts.show-scripts')
    @endsection
@endcan
