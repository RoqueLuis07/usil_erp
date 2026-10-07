@php
    $alumno = $alumno ?? null;
    $anio_actual = now()->year;
@endphp
<div class="row">
    <div class="col-lg-12">
        <h6 class="text-muted text-uppercase mb-3" style="font-size: 12px; letter-spacing: .04em;">Datos académicos <span class="fw-normal">(necesarios para participar de proyectos de extensión)</span></h6>
    </div>
    <div class="col-lg-4 mb-3">
        <label class="form-label" for="carrera">Carrera</label>
        <select class="selectpicker form-control @error('carrera') is-invalid @enderror" id="carrera" name="carrera" data-live-search="true">
            <option value="" selected>Seleccionar...</option>
            @foreach ($carreras as $carrera_opcion)
                <option value="{{$carrera_opcion->id}}"
                    data-facultad="{{ optional($carrera_opcion->Facultad)->nombre }}"
                    data-duracion="{{ $carrera_opcion->cantidad_semestres }}"
                    @if (old('carrera', optional($alumno)->carrera_id) == $carrera_opcion->id) selected @endif>{{$carrera_opcion->nombre_fantasia}}</option>
            @endforeach
        </select>
        <div class="form-text" style="font-size: 12px">Si se deja vacío, el alumno no podrá postularse ni participar de proyectos de extensión.</div>
        @error('carrera')
            <span class="invalid-feedback" role="alert"><strong>{{$message}}</strong></span>
        @enderror
    </div>
    <div class="col-lg-3 mb-3">
        <label class="form-label" for="facultad_carrera">Facultad</label>
        <input type="text" class="form-control" id="facultad_carrera" value="{{ optional(optional(optional($alumno)->Carrera)->Facultad)->nombre }}" placeholder="Se completa según la carrera" readonly>
    </div>
    <div class="col-lg-2 mb-3">
        <label class="form-label" for="anho_ingreso">Año de Ingreso</label>
        <input type="number" class="form-control text-center @error('anho_ingreso') is-invalid @enderror" id="anho_ingreso" name="anho_ingreso" min="1990" max="{{$anio_actual}}" value="{{ old('anho_ingreso', optional($alumno)->anho_ingreso) }}" placeholder="{{$anio_actual}}">
        @error('anho_ingreso')
            <span class="invalid-feedback" role="alert"><strong>{{$message}}</strong></span>
        @enderror
    </div>
    <div class="col-lg-1 mb-3">
        <label class="form-label" for="semestre_ingreso">Período</label>
        <select class="form-select @error('semestre_ingreso') is-invalid @enderror" id="semestre_ingreso" name="semestre_ingreso">
            <option value="1" @if (old('semestre_ingreso', optional($alumno)->semestre_ingreso ?: 1) == 1) selected @endif>1.º</option>
            <option value="2" @if (old('semestre_ingreso', optional($alumno)->semestre_ingreso) == 2) selected @endif>2.º</option>
        </select>
    </div>
    <div class="col-lg-2 mb-3">
        <label class="form-label" for="semestre_calculado">Semestre actual</label>
        <input type="text" class="form-control text-center fw-bold" id="semestre_calculado" value="{{ optional($alumno)->semestre_actual ? optional($alumno)->semestre_actual . '.º semestre' : '' }}" placeholder="Se calcula solo" readonly>
    </div>
</div>
<hr class="mt-0">
