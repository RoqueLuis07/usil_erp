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
        Schema::create('proyectos_tesis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscripcion_id')->constrained('inscripciones_temas_tesis');
            $table->foreignId('bloque_id')->constrained('bloques_proyectos_tesis');
            $table->integer('numero_bloque');
            $table->foreignId('aprobado_tutor_id')->nullable()->constrained('usuarios');
            $table->timestamp('fecha_aprobado_tutor')->nullable();
            $table->char('estado', 2)->default('PE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyectos_tesis');
    }
};
