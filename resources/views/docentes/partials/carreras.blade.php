@php
    $docente = $docente ?? null;
    $seleccionadas = collect(old('carreras', $docente ? $docente->Carreras->pluck('id')->all() : []));
@endphp
<div class="row">
    <div class="col-lg-12">
        <h6 class="text-muted text-uppercase mb-3" style="font-size: 12px; letter-spacing: .04em;">Carreras y facultad <span class="fw-normal">(en qué carreras enseña)</span></h6>
    </div>
    <div class="col-lg-6 mb-3">
        <label class="form-label" for="carreras">Carreras</label>
        <select class="selectpicker form-control" id="carreras" name="carreras[]" multiple data-live-search="true" title="Seleccionar carreras...">
            @foreach ($carreras as $carrera_opcion)
                <option value="{{$carrera_opcion->id}}" data-subtext="{{ optional($carrera_opcion->Facultad)->nombre }}" @if ($seleccionadas->contains($carrera_opcion->id)) selected @endif>{{$carrera_opcion->nombre_fantasia}}</option>
            @endforeach
        </select>
        <p class="text-muted mb-0" style="font-size: 12px">La facultad se deduce de la carrera elegida.</p>
    </div>
</div>
<hr class="mt-0">
