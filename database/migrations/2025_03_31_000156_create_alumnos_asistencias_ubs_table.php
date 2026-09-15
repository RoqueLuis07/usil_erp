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
        Schema::create('alumnos_asistencias_ubs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curso_id')->constrained('cursos');
            $table->foreignId('modulo_id')->constrained('modulos');
            $table->foreignId('alumno_id')->constrained('alumnos');
            $table->foreignId('docente_id')->constrained('docentes');
            $table->foreignId('modalidad_id')->constrained('modalidades');
            $table->timestamp('fecha');
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
        Schema::dropIfExists('alumnos_asistencias_ubs');
    }
};
