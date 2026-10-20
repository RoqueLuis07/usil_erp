<?php

namespace App\Traits;

use App\Models\Alumno;
use App\Models\Semestre;

trait ResuelveCarreraSemestreAlumno
{
    /**
     * Resuelve la carrera y el semestre (período) del alumno para el snapshot guardado en
     * extensiones_universitarias_detalles: la carrera es la que tiene cargada el alumno y el
     * período es el semestre cuyas fechas contienen la fecha de referencia (fecha de inicio
     * de la actividad), o el más reciente que ya empezó si ninguno la contiene.
     */
    private function resolverCarreraSemestreAlumno($alumnoId, $fechaReferencia)
    {
        $semestre = Semestre::where('fecha_inicio', '<=', $fechaReferencia)
            ->where('fecha_fin', '>=', $fechaReferencia)
            ->orderByDesc('id')
            ->first();

        if (!$semestre) {
            $semestre = Semestre::where('fecha_inicio', '<=', $fechaReferencia)->orderByDesc('fecha_inicio')->first();
        }

        return [
            'carrera_id' => optional(Alumno::find($alumnoId))->carrera_id,
            'semestre_id' => optional($semestre)->id,
        ];
    }
}
