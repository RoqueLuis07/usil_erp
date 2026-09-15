<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('alumnos_puntajes_ubs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos');
            $table->foreignId('curso_id')->constrained('cursos');
            $table->foreignId('modulo_id')->constrained('modulos');
            $table->foreignId('evaluacion_id')->constrained('evaluaciones_ubs');
            $table->integer('puntos_obtenidos');
            $table->text('observaciones')->nullable();
            $table->foreignId('cargado_por_id')->constrained('usuarios');
            $table->foreignId('actualizado_por_id')->nullable()->constrained('usuarios');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumnos_puntajes_ubs');
    }
};
