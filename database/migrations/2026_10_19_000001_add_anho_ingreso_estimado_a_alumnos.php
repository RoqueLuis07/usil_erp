<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Marca los años de ingreso que se calcularon (por ejemplo, a partir del semestre que cursaba el alumno)
 * y todavía no fueron confirmados con coordinación. Es solo un aviso: el año se puede editar siempre.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->boolean('anho_ingreso_estimado')->default(false)->after('semestre_ingreso');
        });
    }

    public function down(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->dropColumn('anho_ingreso_estimado');
        });
    }
};
