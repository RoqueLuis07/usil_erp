{{-- Botón para quitar a un alumno (fila de participantes). Requiere $detalle y $key. --}}
<div class="col-2 col-lg-1 mb-2 text-center">
    @if ($key == 0) <label class="form-label">Quitar</label> @endif
    <div>
        <form action="{{ route('extensiones_universitarias.quitar_alumno', $detalle->id) }}" method="post" class="d-inline form-quitar-alumno"
            data-nombre="{{ $detalle->alumno->primer_nombre }} {{ $detalle->alumno->primer_apellido }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Quitar de la actividad"><i class="ri-delete-bin-line"></i></button>
        </form>
    </div>
</div>
