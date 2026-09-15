@can('ver_clientes')
    @extends('layouts.master')
    @section('title') Ver Cliente @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Clientes @endslot
            @slot('title') Ver Cliente  @endslot
        @endcomponent

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-12">
                                <h4 class="card-title mb-0">Visualizar cliente</h4>
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="nombre">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" value="{{$cliente->nombre}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="razon_social">Razón Social</label>
                                    <input type="text" class="form-control" id="razon_social" value="{{$cliente->razon_social}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="numero_documento">N° de Documento</label>
                                    <input type="text" class="form-control" id="numero_documento" value="{{$cliente->numero_documento}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="celular">N° de Celular</label>
                                    <input type="text" class="form-control" id="celular" value="{{$cliente->celular}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="telefono">N° de Teléfono</label>
                                    <input type="text" class="form-control" id="telefono" value="{{$cliente->telefono}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="email">Correo Electrónico</label>
                                    <input type="text" class="form-control" id="email" value="{{$cliente->email}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="nacionalidad">Nacionalidad</label>
                                    <input type="text" class="form-control" id="nacionalidad" @if ($cliente->nacionalidad_id) value="{{$cliente->nacionalidad->nombre}}" @endif readonly>
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="direccion">Dirección</label>
                                    <input type="text" class="form-control" id="direccion" value="{{$cliente->direccion}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="departamento">Departamento</label>
                                    <input type="text" class="form-control" id="departamento" @if ($cliente->departamento_id) value="{{$cliente->departamento->nombre}}" @endif readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="ciudad">Ciudad</label>
                                    <input type="text" class="form-control" id="ciudad" @if ($cliente->ciudad_id) value="{{$cliente->ciudad->nombre}}" @endif readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="barrio">Barrio</label>
                                    <input type="text" class="form-control" id="barrio" @if ($cliente->barrio_id) value="{{$cliente->barrio->nombre}}" @endif readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="sexo">Sexo</label>
                                    <input type="text" class="form-control" id="sexo" @if ($cliente->sexo_id) value="{{$cliente->sexo->nombre}}" @endif readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha_nacimiento">Fecha de Nacimiento</label>
                                    <input type="text" class="form-control" id="fecha_nacimiento" value="{{$cliente->fecha_nacimiento}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="estado_civil">Estado Civil</label>
                                    <input type="text" class="form-control" id="estado_civil" @if ($cliente->estado_civil_id) value="{{$cliente->estadoCivil->nombre}}" @endif readonly>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="observaciones">Observaciones</label>
                                    <textarea class="form-control" id="observaciones" cols="30" rows="3">{{$cliente->observaciones}}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <ul class="nav nav-pills arrow-navtabs nav-info nav-justified bg-light gap-2 mb-4" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link active align-middle" data-bs-toggle="tab" href="#tablistDatosLaborales" role="tab" aria-selected="true">Datos Laborales</a>
                                        </li>
                                    </ul>
                                    {{-- Tab panes --}}
                                    <div class="tab-content">
                                        <div class="tab-pane active show" id="tablistDatosLaborales" role="tabpanel">
                                            <div class="row">
                                                <div class="col-lg-4 mb-3">
                                                    <label class="form-label" for="empresa">Empresa</label>
                                                    <input type="text" class="form-control" id="empresa" @if ($cliente->dato_laboral_id) value="{{$cliente->datoLaboral->empresa}}" @endif readonly>
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="cargo">Cargo</label>
                                                    <input type="text" class="form-control" id="cargo" @if ($cliente->dato_laboral_id) value="{{$cliente->datoLaboral->cargo}}" @endif readonly>
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="telefono_laboral">N° de Teléfono</label>
                                                    <input type="text" class="form-control" id="telefono_laboral" @if ($cliente->dato_laboral_id) value="{{$cliente->datoLaboral->telefono}}" @endif readonly>
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="celular_laboral">N° de Celular</label>
                                                    <input type="text" class="form-control" id="celular_laboral" @if ($cliente->dato_laboral_id) value="{{$cliente->datoLaboral->celular}}" @endif readonly>
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="email_laboral">Correo Eletrónico</label>
                                                    <input type="text" class="form-control" id="email_laboral" @if ($cliente->dato_laboral_id) value="{{$cliente->datoLaboral->email}}" @endif readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- /Tab panes --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="cargado_por">Cargado por:</label>
                            <br>
                            {{$cliente->cargadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($cliente->created_at)->format('d/m/Y H:i:s')}}
                        </div>
                        @if ($cliente->actualizado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="cargado_por">Última actualización hecha por:</label>
                                <br>
                                {{$cliente->actualizadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($cliente->updated_at)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('clientes.index')}}">Volver</a>
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
