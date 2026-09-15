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
        Schema::create('alumnos_puntajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos');
            $table->foreignId('materia_id')->constrained('materias');
            $table->foreignId('carrera_id')->constrained('carreras');
            $table->foreignId('semestre_id')->constrained('semestres');
            $table->foreignId('evaluacion_id')->constrained('evaluaciones');
            $table->integer('puntos_obtenidos')->nullable();
            $table->text('observacion')->nullable();
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
        Schema::dropIfExists('alumnos_puntajes');
    }
};
