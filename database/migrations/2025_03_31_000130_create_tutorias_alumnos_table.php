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
        Schema::create('tutorias_alumnos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutoria_id')->constrained('tutorias');
            $table->foreignId('alumno_id')->constrained('alumnos');
            $table->foreignId('carrera_id')->constrained('carreras');
            $table->integer('cantidad_asistencias')->default(0);
            $table->integer('puntos_obtenidos')->nullable();
            $table->string('calificacion')->nullable();
            $table->char('estado', 2)->default('IN');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutorias_alumnos');
    }
};
