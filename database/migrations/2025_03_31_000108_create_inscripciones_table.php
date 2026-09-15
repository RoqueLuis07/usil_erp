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
        Schema::create('inscripciones', function (Blueprint $table) {
            $table->id();
            $table->timestamp('fecha')->nullable();
            $table->foreignId('matriculacion_id')->constrained('matriculaciones');
            $table->foreignId('materia_id')->nullable()->constrained('materias');
            $table->foreignId('alumno_id')->constrained('alumnos');
            $table->foreignId('docente_id')->nullable()->constrained('docentes');
            $table->char('estado', 2)->nullable();
            $table->foreignId('cargado_por_id')->nullable()->constrained('usuarios');
            $table->foreignId('actualizado_por_id')->nullable()->constrained('usuarios');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscripciones');
    }
};
