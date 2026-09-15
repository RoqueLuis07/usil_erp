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
        Schema::create('alumnos_asistencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clase_materia_id')->constrained('clases_materias');
            $table->foreignId('materia_id')->constrained('materias');
            $table->foreignId('semestre_id')->constrained('semestres');
            $table->foreignId('alumno_id')->constrained('alumnos');
            $table->integer('horas_desarrollo');
            $table->char('estado', 2);
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumnos_asistencias');
    }
};
