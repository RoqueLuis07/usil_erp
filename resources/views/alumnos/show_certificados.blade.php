@if (Auth::user()->can('ver_certificado_estudios_alumnos') || Auth::user()->can('ver_certificado_estudios_siu_alumnos'))
    @extends('layouts.master-academic')
    @section('title') Ver Certificado de Estudios @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Certificado de Estudios @endslot
            @slot('title') Ver Certificado de Estudios  @endslot
        @endcomponent

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar certificado de estudios</h4>
                            </div>
                            @can('generar_certificado_estudios_alumnos')
                                <div class="col-lg-6 text-end">
                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-info me-2" id="btn-generate-certificado" data-url="{{route('alumnos.generate_certificado', ['id' => $alumno->id, 'tipo' => $tipo])}}">Generar Certificado de Estudios</button>
                                    </div>
                                </div>
                            @endcan
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label" for="alumno">Alumno</label>
                                    <input type="text" class="form-control" id="alumno" value="{{$alumno->primer_nombre}} {{$alumno->primer_apellido}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="numero_documento">N° de Documento</label>
                                    <input type="text" class="form-control" id="numero_documento" value="{{$alumno->numero_documento}}" readonly>
                                </div>
                                <div class="col-lg-2 mb-3">
                                    <label class="form-label" for="sexo">Sexo</label>
                                    <input type="text" class="form-control" id="sexo" value="{{$alumno->sexo->nombre}}" readonly>
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
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Asignaturas</h4>
                                </div>
                                <div class="card-body">
                                        <div class="mb-2">
                                            <div class="row d-flex flex-wrap justify-content-center">
                                                <div class="col-lg-12">
                                                    <div class="accordion custom-accordionwithicon custom-accordion-border accordion-border-box accordion-info" id="acordeon">
                                                        @forelse ($semestres as $key => $semestre)
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header">
                                                                    <button type="button" class="accordion-button collapsed semestre" data-bs-toggle="collapse" data-bs-target="#acordeon-{{$key}}" aria-expanded="false" aria-controls="acordeon-{{$key}}" id="semestre-{{$key}}" data-id="{{$key}}">@if ($tipo == 'SIU') TERM {{$semestre}} @else SEMESTRE {{$semestre}} @endif</button>
                                                                </h2>
                                                                <div class="accordion-collapse collapse" id="acordeon-{{$key}}" aria-labelledby="{{$key}}-acordeon" data-bs-parent="#acordeon">
                                                                    <div class="accordion-body">
                                                                        <div class="row">
                                                                            <div class="col-lg-12">
                                                                                <div class="table-responsive table-card mt-3 mb-1">
                                                                                    <table class="table align-middle table-nowrap text-center" id="semestreTabla-{{$key}}">
                                                                                        <thead class="table-light">
                                                                                            <tr>
                                                                                                @if ($tipo == 'SIU')
                                                                                                    <th>Course Number</th>
                                                                                                    <th>Course Title</th>
                                                                                                    <th>Credit Hours</th>
                                                                                                    <th>Term / Year Taken</th>
                                                                                                    <th>Grade</th>
                                                                                                    <th>Transfer</th>
                                                                                                @else
                                                                                                    <th>Asignatura</th>
                                                                                                    <th>N° Acta</th>
                                                                                                    <th>Fecha</th>
                                                                                                    <th>Calificación</th>
                                                                                                    <th>Periodo de Examen</th>
                                                                                                    <th>Carga Horaria</th>
                                                                                                    <th>Modalidad</th>
                                                                                                @endif
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                            @foreach ($alumno_notas as $nota)
                                                                                                @if ($nota->semestre_materia == $semestre)
                                                                                                    <tr>
                                                                                                        @if ($tipo == 'SIU')
                                                                                                            <td>{{$nota->materia->codigo}}</td>
                                                                                                            <td>{{$nota->materia->nombre_real}}</td>
                                                                                                            <td class="carga_horaria">{{$nota->materia->carga_horaria}}</td>
                                                                                                            <td>{{$nota->fecha}}</td>
                                                                                                            <td>{{$nota->calificacion}}</td>
                                                                                                            <td>--------------</td>
                                                                                                        @else
                                                                                                            <td>{{$nota->materia->nombre_real}}</td>
                                                                                                            <td>{{$nota->numero_acta}}</td>
                                                                                                            <td>{{\Carbon\Carbon::parse($nota->fecha)->format('d/m/Y')}}</td>
                                                                                                            <td>{{$nota->calificacion}} ({{$nota->calificacion_letras}})</td>
                                                                                                            <td class="d-none calificacion">{{$nota->calificacion}}</td>
                                                                                                            <td>{{$nota->evaluacion}}</td>
                                                                                                            <td class="carga_horaria">{{$nota->materia->carga_horaria}}</td>
                                                                                                            <td>{{$nota->carrera->modalidad->nombre}}</td>
                                                                                                        @endif
                                                                                                    </tr>
                                                                                                @endif
                                                                                            @endforeach
                                                                                        </tbody>
                                                                                        <tfoot class="table-light">
                                                                                            <tr>
                                                                                                <th style="text-align: left;">Promedio Académico</th>
                                                                                                <th colspan="6" id="promedio_calificacion-{{$key}}">0</th>
                                                                                            </tr>
                                                                                            <tr>
                                                                                                <th style="text-align: left;">Carga Horaria Total</th>
                                                                                                <th colspan="6" id="total_carga_horaria-{{$key}}">0</th>
                                                                                            </tr>
                                                                                        </tfoot>
                                                                                    </table>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <div class="row d-flex flex-wrap justify-content-center">
                                                                <div class="col-lg-12 col-sm-12 mb-2 text-center">
                                                                    <p>El alumno no cuenta con materias cursadas.</p>
                                                                </div>
                                                            </div>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>
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
        <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
        <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
        @include('alumnos.scripts.show_certificados-scripts')
    @endsection
@endif
