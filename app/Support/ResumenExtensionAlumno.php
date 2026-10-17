<?php

namespace App\Support;

use App\Models\ExtensionUniversitariaDetalle;
use App\Models\Matriculacion;
use App\Models\RequerimientoExtensionUniversitaria;
use Illuminate\Support\Collection;

/**
 * Avance de Extensión Universitaria de uno o varios alumnos, calculado por
 * tipo de actividad (cualquier cantidad de tipos, no solo los 4 primeros ids).
 *
 * Solo cuentan postulaciones aceptadas ('AC') de actividades finalizadas
 * ('FI'). Las horas acreditadas de cada tipo están topadas por el máximo
 * del tipo (maxima_cantidad_horas); la suma de las acreditadas se compara
 * contra el requisito de la carrera del alumno (o el general, sin carrera).
 */
class ResumenExtensionAlumno
{
    /**
     * @param  Collection  $alumnos  colección de App\Models\Alumno
     * @return array<int, array>  resumen por id de alumno
     */
    public static function para(Collection $alumnos): array
    {
        if ($alumnos->isEmpty()) {
            return [];
        }

        $detalles = ExtensionUniversitariaDetalle::with('extensionUniversitaria.tipoExtension')
            ->whereIn('alumno_id', $alumnos->pluck('id')->all())
            ->where('estado', 'AC')
            ->whereHas('extensionUniversitaria', function ($query) {
                $query->where('estado', 'FI');
            })
            ->get()
            ->groupBy('alumno_id');

        $requerimientos = RequerimientoExtensionUniversitaria::all();
        $requerimientoGeneral = $requerimientos->first(function ($r) {
            return is_null($r->carrera_id);
        });

        // Alumnos sin carrera propia: se usa la de su matriculación más reciente.
        $sinCarrera = $alumnos->filter(function ($a) {
            return !$a->carrera_id;
        })->pluck('id')->all();
        $carreraPorMatriculacion = $sinCarrera
            ? Matriculacion::whereIn('alumno_id', $sinCarrera)->orderByDesc('fecha')->get()->unique('alumno_id')->pluck('carrera_id', 'alumno_id')
            : collect();

        $resumenes = [];
        foreach ($alumnos as $alumno) {
            $porTipo = [];
            foreach ($detalles->get($alumno->id, collect()) as $detalle) {
                $tipo = optional($detalle->extensionUniversitaria)->tipoExtension;
                if (!$tipo) {
                    continue;
                }
                if (!isset($porTipo[$tipo->id])) {
                    $porTipo[$tipo->id] = ['cantidad' => 0, 'horas' => 0, 'acreditadas' => 0, 'maximo' => (float) $tipo->maxima_cantidad_horas];
                }
                $porTipo[$tipo->id]['cantidad']++;
                $porTipo[$tipo->id]['horas'] += (float) $detalle->cantidad_horas;
            }

            $horasRealizadas = 0;
            $horasAcreditadas = 0;
            $actividades = 0;
            foreach ($porTipo as $id => $datos) {
                $porTipo[$id]['acreditadas'] = min($datos['horas'], $datos['maximo']);
                $horasRealizadas += $datos['horas'];
                $horasAcreditadas += $porTipo[$id]['acreditadas'];
                $actividades += $datos['cantidad'];
            }

            $carreraId = $alumno->carrera_id ?: $carreraPorMatriculacion->get($alumno->id);
            $requerimiento = $carreraId
                ? $requerimientos->first(function ($r) use ($carreraId) {
                    return $r->carrera_id == $carreraId;
                })
                : null;
            $requerimiento = $requerimiento ?: $requerimientoGeneral;

            $horasRequeridas = $requerimiento ? (float) $requerimiento->horas_requeridas : null;
            $actividadesRequeridas = $requerimiento ? (int) $requerimiento->actividades_requeridas : null;

            $resumenes[$alumno->id] = [
                'por_tipo' => $porTipo,
                'horas_realizadas' => $horasRealizadas,
                'horas_acreditadas' => $horasAcreditadas,
                'actividades_realizadas' => $actividades,
                'horas_requeridas' => $horasRequeridas,
                'actividades_requeridas' => $actividadesRequeridas,
                // null = no hay requisito definido, no se puede afirmar nada.
                'completo' => $requerimiento
                    ? ($horasAcreditadas >= $horasRequeridas && $actividades >= $actividadesRequeridas)
                    : null,
            ];
        }

        return $resumenes;
    }
}
