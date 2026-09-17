@can('ver_catalogo_extensiones_alumnos_pantalla')
    @extends('layouts.master-academic')
    @section('title') Catálogo de Extensión Universitaria @endsection
    @section('css')
        <link rel="stylesheet" href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}">
    @endsection
    @section('content')
        @component('components.breadcrumb')
            @slot('li_1') Extensión Universitaria @endslot
            @slot('title') Catálogo de Proyectos @endslot
        @endcomponent

        <div class="row d-flex flex-wrap justify-content-center">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-header d-flex flex-wrap align-items-center">
                        <div class="col-lg-8">
                            <h4 class="card-title mb-0">Proyectos abiertos a postulación</h4>
                        </div>
                        <div class="col-lg-4 text-end">
                            <a type="button" class="btn btn-danger" href="{{route('pantallas_alumnos.extensiones_universitarias', Auth::id())}}">Volver</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="text-muted" style="font-size: 13px">Se muestran los proyectos aprobados, dentro de fecha, habilitados para tu carrera (o abiertos a todas) y a los que todavía no te postulaste.</p>
                        <div class="table-responsive table-card mt-3 mb-1">
                            <table class="table align-middle table-nowrap">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th>Proyecto</th>
                                        <th>Tipo de Actividad</th>
                                        <th>Responsable</th>
                                        <th>Horas que Otorga</th>
                                        <th>Cupos</th>
                                        <th>Vigencia</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    @forelse ($proyectos as $proyecto)
                                        <tr>
                                            <td class="text-start">{{$proyecto->nombre}}</td>
                                            <td>{{$proyecto->tipoExtension->nombre}}</td>
                                            <td>{{$proyecto->docente->primer_nombre}} {{$proyecto->docente->primer_apellido}}</td>
                                            <td>{{number_format($proyecto->cantidad_horas, 0, ',', '.')}} @if ($proyecto->cantidad_horas != 1) horas @else hora @endif</td>
                                            <td>
                                                @if ($proyecto->cupo_maximo)
                                                    {{$proyecto->cuposDisponibles()}} / {{$proyecto->cupo_maximo}}
                                                @else
                                                    Sin límite
                                                @endif
                                            </td>
                                            <td>
                                                {{\Carbon\Carbon::parse($proyecto->fecha_inicio)->format('d/m/Y')}}
                                                @if ($proyecto->fecha_fin)
                                                    - {{\Carbon\Carbon::parse($proyecto->fecha_fin)->format('d/m/Y')}}
                                                @endif
                                            </td>
                                            <td>
                                                <form action="{{route('pantallas_alumnos.postular_extension_universitaria', [Auth::id(), $proyecto->id])}}" method="post">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('¿Postularte a {{ addslashes($proyecto->nombre) }}?')">Postularme</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7">No hay proyectos abiertos a postulación en este momento.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @section('script')
        <script src="{{ URL::asset('build/js/app.js') }}"></script>
        <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
    @endsection
@endcan
