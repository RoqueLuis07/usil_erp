@can('ver_alumnos')
    @extends('layouts.master-academic')
    @section('title') Ver Alumno @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Alumnos @endslot
            @slot('title') Ver Alumno  @endslot
        @endcomponent

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar alumno</h4>
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <p class="text-muted">Por favor, complete los <code>campos marcados</code> para poder agregar un registro con éxito</p>
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="nombre_completo">Nombre Completo</label>
                                    <input type="text" class="form-control" id="nombre_completo" value="{{$nombre_alumno}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="numero_documento">N° de Documento</label>
                                    <input type="text" class="form-control" id="numero_documento" value="{{$alumno->numero_documento}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="sexo">Sexo</label>
                                    <input type="text" class="form-control" id="sexo" value="{{optional($alumno->sexo)->nombre}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="fecha_nacimiento">Fecha de Nacimiento</label>
                                    <input type="text" class="form-control" id="fecha_nacimiento" value="{{\Carbon\Carbon::parse($alumno->fecha_nacimiento)->format('d/m/Y')}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="edad">Edad</label>
                                    <input type="text" class="form-control" id="edad" value="{{\Carbon\Carbon::createFromDate($alumno->fecha_nacimiento)->age}} años" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="nacionalidad">Nacionalidad</label>
                                    <input type="text" class="form-control" id="nacionalidad">
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="telefono">N° de Teléfono</label>
                                    <input type="text" class="form-control" id="telefono" value="{{$alumno->telefono}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="celular">N° de Celular</label>
                                    <input type="text" class="form-control" id="celular" value="{{$alumno->celular}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="email_personal">Correo Personal</label>
                                    <input type="text" class="form-control" id="email_personal" value="{{$alumno->email_personal}}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label" for="email_institucional">Correo Institucional</label>
                                    <input type="text" class="form-control" id="email_institucional" value="{{$alumno->email_institucional}}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label">Carrera</label>
                                    <input type="text" class="form-control" value="{{ optional($alumno->Carrera)->nombre_fantasia ?? 'Sin carrera cargada' }}" readonly>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label class="form-label">Facultad</label>
                                    <input type="text" class="form-control" value="{{ optional(optional($alumno->Carrera)->Facultad)->nombre }}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label">Ingreso</label>
                                    <input type="text" class="form-control text-center" value="{{ $alumno->ingreso_texto }}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label">Semestre actual</label>
                                    <input type="text" class="form-control text-center fw-bold" value="{{ $alumno->semestre_actual ? $alumno->semestre_actual . '.º semestre' : '' }}" readonly>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="usuario">Usuario Asignado</label>
                                    <input class="form-control" type="text" id="usuario" @if ($alumno->usuario_id) value="{{$alumno->usuario->name}} - {{$alumno->usuario->rol->name}}" @endif readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <div>
                                        <label class="form-label" for="ubs">Alumno UBS</label>
                                    </div>
                                    <div class="btn-group" role="group">
                                        <input type="radio" class="btn-check ubs1" id="ubs1" name="ubs" value="false" @if (old('ubs') == 'false' || $alumno->ubs == false) checked @endif disabled>
                                        <label class="btn btn-outline-danger" for="ubs1">No</label>
                                        <input type="radio" class="btn-check ubs2" id="ubs2" name="ubs" value="true" @if (old('ubs') == 'true' || $alumno->ubs == true) checked @endif disabled>
                                        <label class="btn btn-outline-success" for="ubs2">Si</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label" for="direccion">Dirección</label>
                                    <input type="text" class="form-control" id="direccion" value="{{$alumno->direccion}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="departamento">Departamento</label>
                                    <input type="text" class="form-control" id="departamento" @if ($alumno->departamento_id) value="{{$alumno->departamento->nombre}}" @endif readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="ciudad">Ciudad</label>
                                    <input type="text" class="form-control" id="ciudad" @if ($alumno->ciudad_id) value="{{$alumno->ciudad->nombre}}" @endif readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="barrio">Barrio</label>
                                    <input type="text" class="form-control" id="barrio" @if ($alumno->barrio_id) value="{{$alumno->barrio->nombre}}" @endif readonly>
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
                                            <a class="nav-link align-middle" data-bs-toggle="tab" href="#tablistFamiliar1" role="tab" aria-selected="true">Familiar 1</a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link align-middle" data-bs-toggle="tab" href="#tablistFamiliar2" role="tab" aria-selected="true">Familiar 2</a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link align-middle" data-bs-toggle="tab" href="#tablistLaboral" role="tab" aria-selected="true">Datos Laborales</a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link align-middle" data-bs-toggle="tab" href="#tablistFacturacion" role="tab" aria-selected="true">Facturación</a>
                                        </li>
                                    </ul>
                                    {{-- Tab panes --}}
                                    <div class="tab-content">
                                        <div class="tab-pane active show" id="tablistFormacion" role="tabpanel">
                                            <div class="row">
                                                <div class="col-lg-4 mb-3">
                                                    <label class="form-label" for="formacion">Formación</label>
                                                    <input type="text" class="form-control" id="formacion" @if ($alumno->formacion_id) value="{{$alumno->formacion->nombre}}" @endif readonly>
                                                </div>
                                                <div class="col-lg-4 mb-3">
                                                    <label class="form-label" for="institucion_educativa">Institución Educativa</label>
                                                    <input type="text" class="form-control" id="institucion_educativa" @if ($alumno->institucion_educativa_id) value="{{$alumno->institucionEducativa->nombre}}" @endif readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-content">
                                        <div class="tab-pane" id="tablistFamiliar1" role="tabpanel">
                                            <div class="row">
                                                <div class="col-lg-4 mb-3">
                                                    <label class="form-label" for="nombre_familiar_uno">Nombre Completo</label>
                                                    <input type="text" class="form-control" id="nombre_familiar_uno" value="{{$nombre_familiar_uno}}" readonly>
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="relacion_familiar1">Relación Familiar</label>
                                                    <input type="text" class="form-control" id="relacion_familiar1" @if ($alumno->familiar_uno_id) value="{{$alumno->familiarUno->relacion->nombre}}" @endif readonly>
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="celular_familiar1">N° de Celular</label>
                                                    <input type="text" class="form-control" id="celular_familiar1" @if ($alumno->familiar_uno_id) value="{{$alumno->familiarUno->celular}}" @endif readonly>
                                                </div>
                                                <div class="col-lg-4 mb-3">
                                                    <label class="form-label" for="email_familiar1">Correo</label>
                                                    <input type="text" class="form-control" id="email_familiar1" @if ($alumno->familiar_uno_id) value="{{$alumno->familiarUno->email}}" @endif readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-content">
                                        <div class="tab-pane" id="tablistFamiliar2" role="tabpanel">
                                            <div class="row">
                                                <div class="col-lg-4 mb-3">
                                                    <label class="form-label" for="nombre_familiar_dos">Nombre Completo</label>
                                                    <input type="text" class="form-control" id="nombre_familiar_dos" value="{{$nombre_familiar_dos}}" readonly>
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="relacion_familiar2">Relación Familiar</label>
                                                    <input type="text" class="form-control" id="relacion_familiar2" @if ($alumno->familiar_dos_id) value="{{$alumno->familiarDos->relacion->nombre}}" @endif readonly>
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="celular_familiar2">N° de Celular</label>
                                                    <input type="text" class="form-control" id="celular_familiar2" @if ($alumno->familiar_dos_id) value="{{$alumno->familiarDos->celular}}" @endif readonly>
                                                </div>
                                                <div class="col-lg-4 mb-3">
                                                    <label class="form-label" for="email_familiar2">Correo</label>
                                                    <input type="text" class="form-control" id="email_familiar2" @if ($alumno->familiar_dos_id) value="{{$alumno->familiarDos->email}}" @endif readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-content">
                                        <div class="tab-pane" id="tablistLaboral" role="tabpanel">
                                            <div class="row">
                                                <div class="col-lg-4 mb-3">
                                                    <label class="form-label" for="empresa">Empresa</label>
                                                    <input type="text" class="form-control" id="empresa" @if ($alumno->dato_laboral_id) value="{{$alumno->datoLaboral->empresa}}" @endif readonly>
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="cargo">Cargo</label>
                                                    <input type="text" class="form-control" id="cargo" @if ($alumno->dato_laboral_id) value="{{$alumno->datoLaboral->cargo}}" @endif readonly>
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="email_laboral">Correo</label>
                                                    <input type="text" class="form-control" id="email_laboral" @if ($alumno->dato_laboral_id) value="{{$alumno->datoLaboral->email}}" @endif readonly>
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="telefono_laboral">N° de Teléfono</label>
                                                    <input type="text" class="form-control" id="telefono_laboral" @if ($alumno->dato_laboral_id) value="{{$alumno->datoLaboral->telefono}}" @endif readonly>
                                                </div>
                                                <div class="col-lg-2 mb-3">
                                                    <label class="form-label" for="celular_laboral">N° de Celular</label>
                                                    <input type="text" class="form-control" id="celular_laboral" @if ($alumno->dato_laboral_id) value="{{$alumno->datoLaboral->celular}}" @endif readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-content">
                                        <div class="tab-pane" id="tablistFacturacion" role="tabpanel">
                                            @foreach ($alumno->alumnoClientes as $key => $cliente)
                                                <div class="row d-flex flex-wrap justify-content-center">
                                                    <div class="col-lg-2 mb-3 text-center">
                                                        @if ($key == 0) <label class="form-label" for="nombre_cliente">Cliente</label> @endif
                                                        <input type="text" class="form-control text-center" id="nombre_cliente" value="{{$cliente->cliente->nombre}}" readonly>
                                                    </div>
                                                    <div class="col-lg-2 mb-3 text-center">
                                                        @if ($key == 0) <label class="form-label" for="razon_social">Razón Social</label> @endif
                                                        <input type="text" class="form-control text-center" id="razon_social" value="{{$cliente->cliente->razon_social}}" readonly>
                                                    </div>
                                                    <div class="col-lg-2 mb-3 text-center">
                                                        @if ($key == 0) <label class="form-label" for="numero_documento_cliente">N° Documento</label> @endif
                                                        <input type="text" class="form-control text-center" id="numero_documento_cliente" value="{{$cliente->cliente->numero_documento}}" readonly>
                                                    </div>
                                                    <div class="col-lg-1 text-center">
                                                        @if ($key == 0)
                                                            <div>
                                                                <label class="form-label">Es Principal ?</label>
                                                            </div>
                                                        @endif
                                                        <div class="btn-group" role="group">
                                                            <input type="radio" class="btn-check" id="es_principal1" @if ($cliente->es_principal == false) checked @endif disabled>
                                                            <label class="btn btn-outline-danger" for="es_principal1">No</label>
                                                            <input type="radio" class="btn-check" id="es_principal2" @if ($cliente->es_principal == true) checked @endif disabled>
                                                            <label class="btn btn-outline-success" for="es_principal2">Si</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
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
                            {{optional($alumno->cargadoPor)->name}}, en fecha: {{\Carbon\Carbon::parse($alumno->created_at)->format('d/m/Y H:i:s')}}
                        </div>
                        @if ($alumno->actualizado_por_id)
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="cargado_por">Última actualización hecha por:</label>
                                <br>
                                {{optional($alumno->actualizadoPor)->name}}, en fecha: {{\Carbon\Carbon::parse($alumno->updated_at)->format('d/m/Y H:i:s')}}
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-end mb-3">
                            <a type="button" class="btn btn-danger me-2" href="{{route('alumnos.index')}}">Volver</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        @include('alumnos.scripts.show-scripts')
    @endsection
@endcan
