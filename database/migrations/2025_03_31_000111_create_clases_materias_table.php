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
        Schema::create('clases_materias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('docente_id')->constrained('docentes');
            $table->foreignId('carrera_id')->nullable()->constrained('carreras');
            $table->foreignId('materia_id')->constrained('materias');
            $table->foreignId('semestre_id')->constrained('semestres');
            $table->timestamp('fecha_hora');
            $table->string('tema_desarrollado');
            $table->integer('horas_desarrollo');
            $table->foreignId('modalidad_id')->constrained('modalidades');
            $table->text('observaciones')->nullable();
            $table->char('estado', 2)->default('AC');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clases_materias');
    }
};
