@can('ver_horarios_semestres_matriculaciones')
    @extends('layouts.master')
    @section('title') Ver Horarios del Semestre @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Inscripciones @endslot
            @slot('title') Ver Horarios del Semestre  @endslot
        @endcomponent

        <div class="row">
            <form>
                @csrf
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap">
                            <div class="col-lg-6">
                                <h4 class="card-title mb-0">Visualizar horarios del semestre</h4>
                            </div>
                        </div>
                        <!-- end card header -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-2 mb-3 text-center">
                                    <label class="form-label" for="semestre">Semestre</label>
                                    <input type="text" class="form-control text-center" id="semestre" value="{{$periodo_activo->nombre}}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <div class="card">
                                <div class="card-header d-flex flex-wrap">
                                    <div class="col-lg-6">
                                        <h4 class="card-title mb-0">Horarios</h4>
                                    </div>
                                    <div class="col-lg-6 text-end">
                                        <div class="d-flex justify-content-end">
                                            @can('imprimir_horarios_semestres_matriculaciones')
                                                <a type="button" class="btn btn-warning me-2" href="{{route('matriculaciones.pdf_horarios')}}" target="_blank">Imprimir</a>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row d-flex flex-wrap justify-content-center">
                                        <div class="col-lg-12">
                                            <div class="table-responsive table-card mt-3 mb-1">
                                                <table class="table align-middle">
                                                    <thead class="table-light text-center fw-bold">
                                                        <tr>
                                                            <td></td>
                                                            @foreach ($dias_semana as $key => $dia)
                                                                @if ($key != 0 && $key != 6)
                                                                    <td>{{$dia->nombre}}</td>
                                                                @endif
                                                            @endforeach
                                                        </tr>
                                                    </thead>
                                                    <tbody class="text-center">
                                                        @foreach ($horarios_clases as $key => $hora)
                                                            @if (!$loop->last)
                                                                @php
                                                                    $hora_inicio = $hora;
                                                                    $hora_fin = $horarios_clases[$key + 1];
                                                                @endphp
                                                                @if (!($hora_inicio->format('H:i') === '14:00' && $hora_fin->format('H:i') === '18:00'))
                                                                    <tr>
                                                                        <td class="fw-bold">{{$hora->format('H:i')}} a {{$horarios_clases[$key + 1]->format('H:i')}}</td>
                                                                        @foreach ($dias_semana as $k => $dia)
                                                                            @if ($k != 0 && $k != 6)
                                                                                @php
                                                                                    $materias = [];
                                                                                @endphp
                                                                                @foreach ($horarios as $horario)
                                                                                    @php
                                                                                        $hora_inicio_horario = Carbon\Carbon::createFromFormat('H:i:s', $horario['hora_inicio']);
                                                                                        $hora_fin_horario = Carbon\Carbon::createFromFormat('H:i:s', $horario['hora_fin']);
                                                                                    @endphp
                                                                                    @if (($hora_inicio_horario->lt($hora_fin) && $hora_fin_horario->gt($hora_inicio)) && $horario['dia'] == $dia->nombre)
                                                                                        @php
                                                                                            $materias[] = $horario['materia'];
                                                                                        @endphp
                                                                                    @endif
                                                                                @endforeach
                                                                                <td>{!! !empty($materias) ? implode("<hr style='margin-bottom: -1px; margin-top: -1px'>", $materias) : '-' !!}</td>
                                                                            @endif
                                                                        @endforeach
                                                                    </tr>
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                <div class="noresults" style="display: none">
                                                    <div class="text-center">
                                                        <lord-icon src="https://cdn.lordicon.com/jtkfemwz.json" trigger="in" state="morph-cross" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                                        <h5 class="mt-2">Sin resultados.</h5>
                                                        <p class="text-muted mb-0">No pudimos encontrar ningúna materia según tus parámetros de búsqueda.</p>
                                                    </div>
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
                            <a type="button" class="btn btn-danger me-2" href="{{route('matriculaciones.index')}}">Volver</a>
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
