<?php

namespace App\Traits;

use App\Models\Matriculacion;

trait ResuelveCarreraSemestreAlumno
{
    /**
     * Resuelve la carrera/semestre del alumno para el snapshot en
     * extensiones_universitarias_detalles, usando la matriculación vigente
     * a la fecha de referencia (fecha_inicio de la actividad) y, si no
     * existe ninguna que coincida, la matriculación más reciente del alumno.
     * Un alumno puede tener varias matriculaciones en el tiempo, y ni la
     * actividad de extensión ni el alumno guardan carrera/semestre directo.
     */
    private function resolverCarreraSemestreAlumno($alumnoId, $fechaReferencia)
    {
        $matriculacion = Matriculacion::where('alumno_id', $alumnoId)
            ->whereHas('Semestre', function ($query) use ($fechaReferencia) {
                $query->where('fecha_inicio', '<=', $fechaReferencia)
                    ->where('fecha_fin', '>=', $fechaReferencia);
            })
            ->first();

        if (!$matriculacion) {
            $matriculacion = Matriculacion::where('alumno_id', $alumnoId)
                ->orderByDesc('fecha')
                ->first();
        }

        return [
            'carrera_id' => $matriculacion->carrera_id ?? null,
            'semestre_id' => $matriculacion->semestre_id ?? null,
        ];
    }
}
