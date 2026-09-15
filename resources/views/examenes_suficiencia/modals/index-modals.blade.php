@foreach ($examenes as $examen)
    <!-- destroyModal -->
    @can('eliminar_examenes_suficiencia')
        <div class="modal fade flip" id="destroyModal-{{$examen->id}}" tabindex="-1" aria-labelledby="destroyModal-{{$examen->id}}" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12 mb-3 text-center">
                                <lord-icon src="https://cdn.lordicon.com/wtnrotmp.json" trigger="in" colors="primary:#c71f16,secondary:#f4a09c" style="width:150px;height:150px"></lord-icon>
                            </div>
                        </div>
                        <form id="destroy-form-{{$examen->id}}" action="{{route('examenes_suficiencia.destroy', $examen->id)}}" method="delete">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12 mb-3 text-center">
                                    <h4>¿Está seguro de eliminar el examen de suficiencia de la materia {{$examen->materia->nombre_fantasia}} del alumno {{$examen->alumno->primer_nombre}} {{$examen->alumno->primer_apellido}}?</h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="hstack gap-2 justify-content-center">
                                    <button type="submit" class="btn btn-danger" data-bs-dismiss="modal">Sí, eliminar!</button>
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcan
    <!-- /destroyModal -->
@endforeach
