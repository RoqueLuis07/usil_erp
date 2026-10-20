<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Agrega Comercio Exterior y Marketing (Facultad de Ciencias Empresariales). Figuran en la base de estudiantes
 * 2026 pero no tenían carrera cargada, así que sus alumnos no podían importarse. El nombre oficial, la duración
 * y la abreviatura son provisorios (se corrigen en Extensión > Parámetros). Todavía no tienen malla cargada.
 *
 * Idempotente: no toca una carrera que ya existe con ese nombre (sin tildes ni mayúsculas).
 */
return new class extends Migration
{
    private const FACULTAD = 'Facultad de Ciencias Empresariales';

    /** [nombre_fantasia, nombre_real, abreviatura, cantidad_semestres] */
    private const CARRERAS = [
        ['Comercio Exterior', 'Licenciatura en Comercio Exterior', 'CEX', 8],
        ['Marketing', 'Licenciatura en Marketing', 'MKT', 8],
    ];

    public function up(): void
    {
        $cargadoPor = DB::table('usuarios')->orderBy('id')->value('id');
        $facultad = DB::table('facultades')->get()->first(fn ($f) => $this->clave($f->nombre) === $this->clave(self::FACULTAD));
        $modelo = DB::table('carreras')->where('facultad_id', optional($facultad)->id)->orderBy('id')->first()
            ?? DB::table('carreras')->orderBy('id')->first();
        if (!$cargadoPor || !$facultad || !$modelo) {
            return; // base vacía: las carreras base se cargan antes (migración 2026_10_17_000001)
        }
        $ahora = now();
        $existentes = DB::table('carreras')->get();

        foreach (self::CARRERAS as [$fantasia, $real, $abreviatura, $semestres]) {
            $existe = $existentes->first(fn ($c) => in_array($this->clave($fantasia), [$this->clave($c->nombre_fantasia), $this->clave($c->nombre_real)], true)
                || in_array($this->clave($real), [$this->clave($c->nombre_fantasia), $this->clave($c->nombre_real)], true));
            if ($existe) {
                continue;
            }
            DB::table('carreras')->insert([
                'nombre_fantasia' => $fantasia, 'nombre_real' => $real, 'abreviatura' => $abreviatura,
                'programa_id' => $modelo->programa_id, 'facultad_id' => $facultad->id, 'tipo_carrera_id' => $modelo->tipo_carrera_id,
                'doble_grado' => false, 'cantidad_semestres' => $semestres, 'modalidad_id' => $modelo->modalidad_id,
                'cargado_por_id' => $cargadoPor, 'estado' => 'AC', 'created_at' => $ahora, 'updated_at' => $ahora,
            ]);
        }
    }

    public function down(): void
    {
        // No se borra nada: las carreras pueden tener alumnos y actividades asociados.
    }

    private function clave(?string $texto): string
    {
        return preg_replace('/[^a-z0-9]/', '', Str::lower(Str::ascii((string) $texto)));
    }
};
