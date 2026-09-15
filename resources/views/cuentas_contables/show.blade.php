@can('ver_cuentas_contables')
    @extends('layouts.master')
    @section('title') Ver Cuenta Contable @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Cuentas Contables @endslot
            @slot('title') Ver Cuenta Contable  @endslot
        @endcomponent

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-12">
                                <h4 class="card-title mb-0">Visualizar cuenta contable</h4>
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="cuenta">N° de Cuenta</label>
                                    <input type="text" class="form-control" id="cuenta" value="{{$cuenta_contable->cuenta}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="nombre">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" value="{{$cuenta_contable->nombre}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="padre">Cuenta Padre</label>
                                    <input type="text" class="form-control" id="padre" @if ($cuenta_contable->padre_id) value="{{$cuenta_contable->padre->cuenta}} - {{$cuenta_contable->padre->nombre}}" @endif readonly>
                                </div>
                                <div class="col-lg-1 mb-3 text-center">
                                    <label class="form-label" for="nivel">Nivel</label>
                                    <input type="text" class="form-control text-center" value="{{$cuenta_contable->nivel}}" readonly>
                                </div>
                                <div class="col-lg-1 mb-3 text-center">
                                    <div>
                                        <label class="form-label" for="imputable">Imputable ?</span></label>
                                    </div>
                                    <div class="btn-group @error('imputable') is-invalid @enderror" role="group">
                                        <input type="radio" class="btn-check imputable1" id="imputable1" @if ($cuenta_contable->imputable == false) checked @endif disabled>
                                        <label class="btn btn-outline-danger" for="imputable1">No</label>
                                        <input type="radio" class="btn-check imputable2" id="imputable2" @if ($cuenta_contable->imputable == true) checked @endif disabled>
                                        <label class="btn btn-outline-success" for="imputable2">Si</label>
                                    </div>
                                    @error('imputable')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-1 mb-3 text-center">
                                    <label class="form-label" for="estado">Estado</label>
                                    <input type="text" class="form-control fw-bold text-center @if ($cuenta_contable->estado == 'AC') text-success @elseif ($cuenta_contable->estado == 'IN') text-danger @endif" @if ($cuenta_contable->estado == 'AC') value="ACTIVO" @elseif ($cuenta_contable->estado == 'IN') value="INACTIVO" @endif readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="card">
                            <div class="card-header d-flex flex-wrap">
                                <div class="col-lg-12">
                                    <h4 class="card-title mb-0">Árbol de la Cuenta Contable</h4>
                                </div>
                            </div>
                            <!-- end card header -->
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12 mb-3">
                                        <ul>
                                            @foreach ($padres as $padre)
                                                <li class="list-group-item" @if($padre->nivel != 1 ) style="margin-left: {{$padre->nivel}}em !important" @endif>{{$padre->cuenta}} - {{$padre->nombre}}</li>
                                            @endforeach
                                                <li class="list-group-item fw-bold text-danger" style="margin-left: {{$cuenta_contable->nivel}}em !important">{{$cuenta_contable->cuenta}} - {{$cuenta_contable->nombre}}</li>
                                            @if ($cuenta_contable->hijos->isNotEmpty())
                                                <ul>
                                                    @include('cuentas_contables.subcuentas', ['cuentas_contables' => $cuenta_contable->hijos])
                                                </ul>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="cargado_por">Cargado por:</label>
                            <br>
                            {{$cuenta_contable->cargadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($cuenta_contable->created_at)->format('d/m/Y H:i:s')}}
                        </div>
                        @if ($cuenta_contable->actualizado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="cargado_por">Última actualización hecha por:</label>
                                <br>
                                {{$cuenta_contable->actualizadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($cuenta_contable->updated_at)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('cuentas_contables.index')}}">Volver</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
    @endsection
@endcan
