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
        Schema::create('mallas_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('malla_id')->constrained('mallas');
            $table->foreignId('materia_id')->constrained('materias');
            $table->boolean('doble_grado');
            $table->integer('semestre');
            $table->integer('carga_horaria');
            $table->integer('cantidad_creditos');
            $table->char('area_curricular', 1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mallas_detalles');
    }
};
