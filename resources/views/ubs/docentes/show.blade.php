@can('ver_docentes_ubs')
    @extends('layouts.master')
    @section('title') Ver Docente @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Docentes @endslot
            @slot('title') Ver Docente  @endslot
        @endcomponent

        <div class="row">
            <form id="update-form">
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar docente</h4>
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="nombre_completo">Nombre Completo</label>
                                    <input type="text" class="form-control" id="nombre_completo" value="{{$nombre_docente}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="numero_documento">N° de Documento</label>
                                    <input type="text" class="form-control" id="numero_documento" value="{{$docente->numero_documento}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="sexo">Sexo</label>
                                    <input type="text" class="form-control" id="sexo" value="{{$docente->sexo->nombre}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha_nacimiento">Fecha de Nacimiento</label>
                                    <input type="text" class="form-control" id="fecha_nacimiento" value="{{\Carbon\Carbon::parse($docente->fecha_nacimiento)->format('d/m/Y')}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="edad">Edad</label>
                                    <input type="text" class="form-control" id="edad" value="{{\Carbon\Carbon::createFromDate($docente->fecha_nacimiento)->age}} años" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="nacionalidad">Nacionalidad</label>
                                    <input type="text" class="form-control" id="nacionalidad">
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="telefono">N° de Teléfono</label>
                                    <input type="text" class="form-control" id="telefono" value="{{$docente->telefono}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="celular">N° de Celular</label>
                                    <input type="text" class="form-control" id="celular" value="{{$docente->celular}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="email_personal">Correo Personal</label>
                                    <input type="text" class="form-control" id="email_personal" value="{{$docente->email_personal}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="email_institucional">Correo Institucional</label>
                                    <input type="text" class="form-control" id="email_institucional" value="{{$docente->email_institucional}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="direccion">Dirección</label>
                                    <input type="text" class="form-control" id="direccion" value="{{$docente->direccion}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="departamento">Departamento</label>
                                    <input type="text" class="form-control" id="departamento" value="{{$docente->departamento->nombre}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="ciudad">Ciudad</label>
                                    <input type="text" class="form-control" id="ciudad" value="{{$docente->ciudad->nombre}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="barrio">Barrio</label>
                                    <input type="text" class="form-control" id="barrio" value="{{$docente->barrio->nombre}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="usuario">Usuario Asignado</label>
                                    <input class="form-control" type="text" id="usuario" value="{{$docente->usuario->name}} - {{$docente->usuario->rol->name}}" readonly>
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
                                            <a class="nav-link active align-middle" data-bs-toggle="tab" href="#tablistFormacion" role="tab" aria-selected="true">Formación Educativa</a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link align-middle" data-bs-toggle="tab" href="#tablistLaboral" role="tab" aria-selected="true">Datos Laborales</a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link align-middle" data-bs-toggle="tab" href="#tablistRedesSociales" role="tab" aria-selected="true">Redes Sociales</a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link align-middle" data-bs-toggle="tab" href="#tablistFacturacion" role="tab" aria-selected="true">Facturación</a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link align-middle" data-bs-toggle="tab" href="#tablistDatosBancarios" role="tab" aria-selected="true">Datos Bancarios</a>
                                        </li>
                                    </ul>
                                    {{-- Tab panes --}}
                                    <div class="tab-content">
                                        <div class="tab-pane active show" id="tablistFormacion" role="tabpanel">
                                            <div class="row">
                                                <div class="col-lg-4 mb-3">
                                                    <label class="form-label" for="nivel_academico">Nivel Académico</label>
                                                    <input type="text" class="form-control" id="nivel_academico" value="{{$docente->nivelAcademico->nombre}}" readonly>
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="institucion_educativa">Capcaticación Didáctica</label>
                                                    <div class="form-check form-check-success">
                                                        <input type="checkbox" class="form-check-input" @if ($docente->capacitacion_didactica == true) checked @endif disabled style="width:30px; height:30px; margin-left:40px;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-content">
                                        <div class="tab-pane" id="tablistLaboral" role="tabpanel">
                                            <div class="row">
                                                <div class="col-lg-4 mb-3">
                                                    <label class="form-label" for="empresa">Empresa</label>
                                                    <input type="text" class="form-control" id="empresa" @if ($docente->dato_laboral_id) value="{{$docente->datoLaboral->empresa}}" @endif readonly>
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="cargo">Cargo</label>
                                                    <input type="text" class="form-control" id="cargo" @if ($docente->dato_laboral_id) value="{{$docente->datoLaboral->cargo}}" @endif readonly>
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="email_laboral">Correo</label>
                                                    <input type="text" class="form-control" id="email_laboral" @if ($docente->dato_laboral_id) value="{{$docente->datoLaboral->email}}" @endif readonly>
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="telefono_laboral">N° de Teléfono</label>
                                                    <input type="text" class="form-control" id="telefono_laboral" @if ($docente->dato_laboral_id) value="{{$docente->datoLaboral->telefono}}" @endif readonly>
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="celular_laboral">N° de Celular</label>
                                                    <input type="text" class="form-control" id="celular_laboral" @if ($docente->dato_laboral_id) value="{{$docente->datoLaboral->celular}}" @endif readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-content">
                                        <div class="tab-pane" id="tablistRedesSociales" role="tabpanel">
                                            <div class="row">
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="facebook">Facebook</label>
                                                    <input type="text" class="form-control" id="facebook" value="{{$docente->facebook}}"readonly>
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="twitter">X (Twitter)</label>
                                                    <input type="text" class="form-control" id="twitter" value="{{$docente->twitter}}"readonly>
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="instagram">Instagram</label>
                                                    <input type="text" class="form-control" id="instagram" value="{{$docente->instagram}}"readonly>
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="linkedin">Linkedin</label>
                                                    <input type="text" class="form-control" id="linkedin" value="{{$docente->linkedin}}"readonly>
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="tiktok">TikTok</label>
                                                    <input type="text" class="form-control" id="tiktok" value="{{$docente->tiktok}}"readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-content">
                                        <div class="tab-pane" id="tablistFacturacion" role="tabpanel">
                                            <div class="row">
                                                <div class="col-lg-4 mb-3">
                                                    <label class="form-label" for="razon_social">Razón Social</label>
                                                    <input type="text" class="form-control" id="razon_social" value="{{$docente->razon_social}}" readonly>
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="ruc">R.U.C.</label>
                                                    <input type="text" class="form-control" id="ruc" value="{{$docente->ruc}}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-content">
                                        <div class="tab-pane" id="tablistDatosBancarios" role="tabpanel">
                                            <div class="row">
                                                <div class="col-lg-3 mb-3">
                                                    <label class="form-label" for="banco">Banco</label>
                                                    <input type="text" class="form-control" id="banco" @if ($docente->banco_id) value="{{$docente->banco->nombre}}" @endif readonly>
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="tipo_cuenta_bancaria">Tipo de Cuenta</label>
                                                    <input type="text" class="form-control" id="tipo_cuenta_bancaria" @if ($docente->tipo_cuenta_bancaria == 'CA') value="Caja de Ahorro" @elseif ($docente->tipo_cuenta_bancaria == 'CC') value="Cuenta Corriente" @endif readonly>
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="numero_cuenta_bancaria">N° de Cuenta</label>
                                                    <input type="text" class="form-control" id="numero_cuenta_bancaria" value="{{$docente->numero_cuenta_bancaria}}" readonly>
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
                            {{$docente->cargadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($docente->created_at)->format('d/m/Y H:i:s')}}
                        </div>
                        @if ($docente->actualizado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="cargado_por">Última actualización hecha por:</label>
                                <br>
                                {{$docente->actualizadoPor->name}}, en fecha: {{\Carbon\Carbon::parse($docente->updated_at)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('docentes_ubs.index')}}">Volver</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        @include('ubs.docentes.scripts.show-scripts')
    @endsection
@endcan
