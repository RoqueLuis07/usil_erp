{{-- Alta directa de un alumno en la actividad. Requiere $extension y $alumnos_disponibles. --}}
<form action="{{ route('extensiones_universitarias.agregar_alumno', $extension->id) }}" method="post" class="row g-2 align-items-end mb-3 pb-3 border-bottom">
    @csrf
    <div class="col-lg-6">
        <label class="form-label" for="agregar-alumno">Agregar alumno a la actividad</label>
        <select class="selectpicker form-control" id="agregar-alumno" name="alumno" data-live-search="true" title="Buscar por nombre o documento..." required>
            @foreach ($alumnos_disponibles as $alumno_disponible)
                @php $completo = $alumno_disponible->tieneDatosParaExtension(); @endphp
                <option value="{{ $alumno_disponible->id }}" @disabled(!$completo)
                    data-subtext="{{ $alumno_disponible->numero_documento }}@if (!$completo) · datos incompletos @endif">{{ $alumno_disponible->primer_nombre }} {{ $alumno_disponible->primer_apellido }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-lg-6">
        <button type="submit" class="btn btn-success"><i class="ri-user-add-line align-bottom me-1"></i>Agregar</button>
        <span class="text-muted ms-2" style="font-size: 12.5px">Se le asignan automáticamente las {{ number_format($extension->cantidad_horas, 2, ',', '.') }} horas de la actividad.</span>
    </div>
</form>
