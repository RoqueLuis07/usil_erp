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
        Schema::create('borradores_tesis_ubs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscripcion_id')->constrained('inscripciones_temas_tesis_ubs');
            $table->foreignId('bloque_id')->constrained('bloques_borradores_tesis_ubs');
            $table->foreignId('modulo_id')->constrained('modulos');
            $table->integer('numero_bloque');
            $table->foreignId('aprobado_tutor_id')->nullable()->constrained('usuarios');
            $table->timestamp('fecha_aprobado_tutor')->nullable();
            $table->foreignId('aprobado_calidad_id')->nullable()->constrained('usuarios');
            $table->timestamp('fecha_aprobado_calidad')->nullable();
            $table->integer('calificacion')->nullable();
            $table->char('estado', 2)->default('PE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borradores_tesis_ubs');
    }
};
