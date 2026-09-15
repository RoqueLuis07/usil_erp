@can('generar_actas_tesis_ubs')
    @extends('layouts.pdf.notas')
    @section('css')
        <style>
            .titulo {
                width: 100%;
                margin-top: 20px;
                font-size: 14px;
            }
            #acta {
                margin-bottom: -1px;
            }
            .parrafos {
                width: 100%;
                height: auto;
                font-size: 14px;
                text-align: justify;
            }
            #firmas {
                width: 100%;
                white-space: nowrap;
                overflow: hidden;
                text-align: center;
                margin-top: 3cm;
            }
            .firma {
                width: 31%;
                height: auto;
                display: inline-block;
                vertical-align: top;
                text-align: center;
                border-top: 1px solid black;
                margin-right: 5px;
            }
        </style>
    @endsection

    @section('pdf_content')
        <div class="col-12 titulo mb-1">
            <p id="acta"><b>Acta N°:</b> {{$inscripcion->numero_acta}}</p>
        </div>
        <div class="col-12">
            <p class="parrafos mt-5">
                En la ciudad de Asunción capital de la República del Paraguay a los {{$inscripcion->fecha_defensa->format('d')}} días del mes de
                {{$inscripcion->fecha_defensa->translatedFormat('F')}} de {{$inscripcion->fecha_defensa->format('Y')}}, en la Universidad San Ignacio de Loyola
                se reúnen los miembros de la mesa examinadora nominado según Resolución N° ______________ del Consejo Directivo para evaluar el Trabajo Final de Grado
                titulado “{{Str::title($inscripcion->tema)}}”, {{$inscripcion->del_alumno}} estudiante {{Str::title($inscripcion->nombre_alumno)}}.
            </p>
            <p class="parrafos mt-5">
                Una vez finalizada la Defensa Oral los miembros de la Mesa Examinadora han considerado por unanimidad que {{$inscripcion->prenombre_alumno}}
                estudiante ha obtenido la calificación de ______________ (Nº y Letras).
            </p>
            <p class="parrafos mt-5">
                Siendo las _____________ horas, se da por concluida la Defensa de Trabajo Final de Grado.
            </p>
            <p class="parrafos mt-5">
                Dando fe de lo manifestado, firman los presentes:
            </p>
        </div>
        <div id="firmas">
            <div class="firma">
                <div style="font-size: 14px"><b>Miembro 1</b></div>
            </div>
            <div class="firma">
                <div style="font-size: 14px"><b>Miembro 2</b></div>
            </div>
            <div class="firma">
                <div style="font-size: 14px"><b>Miembro 3</b></div>
            </div>
        </div>
    @endsection
@endcan
