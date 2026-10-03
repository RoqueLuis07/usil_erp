<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * - Tipos de actividad: columna `linea_numero` (1..5). Cada tipo es a la vez
 *   su "Línea de extensión" y determina la "Problemática" sugerida (misma
 *   numeración), según el listado oficial entregado por Extensión.
 * - Extensiones: problemática, presupuesto, cantidad de beneficiados,
 *   materia vinculada y encuesta de satisfacción (todos opcionales, así que
 *   las actividades ya cargadas no se ven afectadas).
 * - Se cargan/actualizan los 5 tipos con su tope de horas por alumno. Los
 *   tipos que ya existían (p. ej. "Voluntariado") se reutilizan por nombre
 *   para no cambiarles el id; "Extensión académica" queda como está porque
 *   las actividades importadas dependen de ese tipo.
 */
return new class extends Migration
{
    private const TIPOS = [
        1 => ['Desarrollo Comunitario', 20],
        2 => ['Capacitación', 30],
        3 => ['Difusión', 5],
        4 => ['Actividades artísticas y/o culturales', 15],
        5 => ['Voluntariado', 10],
    ];

    public function up(): void
    {
        if (!Schema::hasColumn('tipos_extensiones_universitarias', 'linea_numero')) {
            Schema::table('tipos_extensiones_universitarias', function (Blueprint $table) {
                $table->unsignedTinyInteger('linea_numero')->nullable()->after('maxima_cantidad_horas');
            });
        }

        if (!Schema::hasColumn('extensiones_universitarias', 'presupuesto')) {
            Schema::table('extensiones_universitarias', function (Blueprint $table) {
                $table->unsignedTinyInteger('problematica')->nullable()->after('tipo_extension_id');
                $table->decimal('presupuesto', 15, 2)->nullable()->after('cantidad_horas');
                $table->unsignedInteger('cantidad_beneficiados')->nullable()->after('presupuesto');
                $table->foreignId('materia_id')->nullable()->after('cantidad_beneficiados')
                    ->constrained('materias', 'id', 'ext_univ_materia_id_foreign');
                $table->boolean('encuesta_satisfaccion')->nullable()->after('tiene_certificado');
            });
        }

        $normalizar = fn ($texto) => Str::lower(Str::ascii(trim($texto)));
        $cargadoPorId = DB::table('usuarios')->orderBy('id')->value('id');
        $existentes = DB::table('tipos_extensiones_universitarias')->get();

        foreach (self::TIPOS as $linea => [$nombre, $horas]) {
            $existente = $existentes->first(fn ($t) => $normalizar($t->nombre) === $normalizar($nombre));

            if ($existente) {
                DB::table('tipos_extensiones_universitarias')->where('id', $existente->id)->update([
                    'maxima_cantidad_horas' => $horas,
                    'linea_numero' => $linea,
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('tipos_extensiones_universitarias')->insert([
                    'nombre' => $nombre,
                    'maxima_cantidad_horas' => $horas,
                    'linea_numero' => $linea,
                    'estado' => 'AC',
                    'cargado_por_id' => $cargadoPorId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Las actividades que ya tienen un tipo con línea reciben la
        // problemática equivalente (las importadas, de "Extensión académica",
        // quedan sin problemática hasta que se reclasifiquen).
        DB::table('extensiones_universitarias')
            ->join('tipos_extensiones_universitarias', 'tipos_extensiones_universitarias.id', '=', 'extensiones_universitarias.tipo_extension_id')
            ->whereNotNull('tipos_extensiones_universitarias.linea_numero')
            ->whereNull('extensiones_universitarias.problematica')
            ->update(['extensiones_universitarias.problematica' => DB::raw('tipos_extensiones_universitarias.linea_numero')]);
    }

    public function down(): void
    {
        Schema::table('extensiones_universitarias', function (Blueprint $table) {
            $table->dropForeign('ext_univ_materia_id_foreign');
            $table->dropColumn(['problematica', 'presupuesto', 'cantidad_beneficiados', 'materia_id', 'encuesta_satisfaccion']);
        });
        Schema::table('tipos_extensiones_universitarias', function (Blueprint $table) {
            $table->dropColumn('linea_numero');
        });
        // Los tipos cargados se dejan: pueden tener actividades asociadas.
    }
};
