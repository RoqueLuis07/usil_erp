<!-- generateActaModal -->
    @can('generar_actas_examenes_suficiencia')
        <div class="modal fade flip" id="generateActaModal" tabindex="-1" aria-labelledby="generateActaModal" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light p-3">
                        <h5 class="modal-title" id="showModal">Generar Acta de Evaluación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="generate-acta-form" action="{{route('examenes_suficiencia.generate_acta')}}" method="post" target="_blank">
                            @csrf
                            <div class="row">
                                <div class="col-lg-6 mb-3 text-center">
                                    <label class="form-label" for="materia">Materia <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('materia') is-invalid @enderror" id="materia" name="materia" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($materias as $materia)
                                            <option value="{{$materia->id}}" @if (old('materia') == strval($materia->id)) selected @endif>{{$materia->nombre_fantasia}}</option>
                                        @endforeach
                                    </select>
                                    @error('materia')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-lg-6 mb-3 text-center">
                                    <label class="form-label" for="carrera">Carrera <span class="text-danger">(*)</span></label>
                                    <select class="selectpicker form-control @error('carrera') is-invalid @enderror" id="carrera" name="carrera" data-live-search="true">
                                        <option value="" selected disabled>Seleccionar...</option>
                                        @foreach ($carreras as $carrera)
                                            <option value="{{$carrera->id}}" @if (old('carrera') == strval($carrera->id)) selected @endif data-subtext="{{$carrera->programa->nombre}}">{{$carrera->nombre_fantasia}}</option>
                                        @endforeach
                                    </select>
                                    @error('materia')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{$message}}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="hstack gap-2 justify-content-center">
                                    <button type="button" class="btn btn-success" id="generate-acta-btn">Generar</button>
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan
<!-- /generateActaModal -->
