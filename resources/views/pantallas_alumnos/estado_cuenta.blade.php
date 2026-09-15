@can('ver_estado_cuenta_alumnos_pantalla')
    @extends('layouts.master')
    @section('title') Mi Estado de Cuenta @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('title') BIENVENIDO, {{$alumno->primer_nombre}} {{$alumno->primer_apellido}} @endslot
        @endcomponent

        @include('index.scripts.messages-scripts')

        <div class="row d-flex flex-wrap justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Estado de Cuenta</h4>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div id="notas-list">
                            <div class="table-responsive table-card mt-3 mb-1">
                                <table class="table align-middle table-nowrap" id="notas-list">
                                    <thead class="table-light text-center">
                                        <tr>
                                            <th>Concepto</th>
                                            <th>Vencimiento</th>
                                            <th>Monto</th>
                                            <th>Saldo</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all text-center">
                                        @forelse ($pagos as $pago)
                                            <tr>
                                            {{-- <tr @if ($fecha_hoy > $pago->fecha_vencimiento && $pago->estado != 'CA') class="table-danger" @endif> --}}
                                                <td>{{$pago['concepto']}}</td>
                                                <td>{{Carbon\Carbon::parse($pago['vencimiento'])->format('d/m/Y')}}</td>
                                                <td>{{number_format($pago['monto'], 0, ',', '.')}}</td>
                                                <td>{{number_format($pago['saldo'], 0, ',', '.')}}</td>
                                                <td>
                                                    <span
                                                    class="badge @if ($pago['estado'] == 'PE')
                                                        bg-danger-subtle text-danger text-uppercase"> Pendiente
                                                    @elseif ($pago['estado'] == 'PA')
                                                        bg-warning-subtle text-warning text-uppercase"> Pago Parcial
                                                    @elseif ($pago['estado'] == 'CA')
                                                        bg-success-subtle text-success text-uppercase"> Pagado
                                                    @elseif ($pago['estado'] == 'AN')
                                                        bg-danger-subtle text-danger text-uppercase"> Anulado
                                                    @endif
                                                </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr class="text-center">
                                                <td colspan="5">No tienes pagos pendientes.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th class="text-end @if ($pagos->sum('saldo') > 0) text-danger @endif" colspan="3">Saldo Pendiente Total:</th>
                                            <th class="text-center @if ($pagos->sum('saldo') > 0) text-danger @endif">{{number_format($pagos->sum('saldo'), 0, ',', '.')}}</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div><!-- end card -->
                <div class="row">
                    <div class="col-lg-12 text-center mb-3">
                        <a type="button" class="btn btn-danger me-2" href="{{route('root')}}">Volver</a>
                    </div>
                </div>
                <!-- end col -->
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
    @endsection
@endcan
