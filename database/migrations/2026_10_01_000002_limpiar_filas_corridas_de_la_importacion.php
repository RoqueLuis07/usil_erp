<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Limpia un puñado de filas de la planilla original que tenían las columnas
 * corridas (texto de la actividad metido en nombre/apellido, fecha real en
 * otra celda, carrera en la celda de semestre). La importación
 * (2026_09_30_000001) no detectó este corrimiento puntual porque el texto
 * corrido igual pasaba el filtro de "parece un nombre válido", así que
 * creó 6 alumnos que en realidad son basura (fragmentos del título de la
 * actividad, no personas), y 4 actividades quedaron con fecha de 1900
 * (el número que caía en fecha_actividad no era una fecha real).
 *
 * Detectado y verificado a mano contra el Excel original tras una revisión
 * pre-demo pedida por el usuario.
 */
return new class extends Migration
{
    private const DOCUMENTOS_BASURA = [
        'EXT-IMP-00035', 'EXT-IMP-00036', 'EXT-IMP-00037', 'EXT-IMP-00038', 'EXT-IMP-00039', 'EXT-IMP-00217',
    ];

    public function up(): void
    {
        $alumnoIds = DB::table('alumnos')->whereIn('numero_documento', self::DOCUMENTOS_BASURA)->pluck('id');
        if ($alumnoIds->isNotEmpty()) {
            DB::table('alumnos_extensiones')->whereIn('alumno_id', $alumnoIds)->delete();
            DB::table('extensiones_universitarias_detalles')->whereIn('alumno_id', $alumnoIds)->delete();
            DB::table('alumnos')->whereIn('id', $alumnoIds)->delete();
        }

        DB::table('extensiones_universitarias')
            ->where('fecha_inicio', '<', '2015-01-01')
            ->update(['fecha_inicio' => null, 'fecha_fin' => null]);
    }

    public function down(): void
    {
        // No se revierte: son correcciones de datos corruptos, no hay valor anterior útil.
    }
};
