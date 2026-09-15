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
        Schema::create('rubricas_alumnos_detalles_tesis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rubrica_id')->constrained('rubricas_alumnos_tesis');
            $table->foreignId('rubrica_detalle_id')->constrained('rubricas_detalles_tesis');
            $table->integer('puntos_obtenidos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rubricas_alumnos_detalles_tesis');
    }
};
