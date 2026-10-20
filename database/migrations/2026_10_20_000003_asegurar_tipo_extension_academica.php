<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * La carga masiva de actividades usa el tipo "Extensión académica" cuando la fila no indica el tipo. Ese tipo lo
 * crea el seeder solo si el catálogo está vacío; esta migración se asegura de que exista en cualquier base.
 *
 * Idempotente: si ya hay un tipo con ese nombre (activo o no), no se toca.
 */
return new class extends Migration
{
    private const NOMBRE = 'Extensión académica';

    public function up(): void
    {
        $cargadoPor = DB::table('usuarios')->orderBy('id')->value('id');
        if (!$cargadoPor) {
            return; // base vacía: lo crea el seeder junto con el primer usuario
        }

        $clave = fn ($t) => Str::lower(Str::ascii(trim((string) $t)));
        $existe = DB::table('tipos_extensiones_universitarias')->get()->contains(fn ($t) => $clave($t->nombre) === $clave(self::NOMBRE));
        if ($existe) {
            return;
        }

        DB::table('tipos_extensiones_universitarias')->insert([
            'nombre' => self::NOMBRE,
            'maxima_cantidad_horas' => 40,
            'estado' => 'AC',
            'cargado_por_id' => $cargadoPor,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        // No se borra nada: puede tener actividades asociadas.
    }
};
