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
        Schema::create('mallas_espejos_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('malla_espejo_id')->constrained('mallas_espejos');
            $table->foreignId('materia_paraguay_id')->constrained('materias');
            $table->foreignId('materia_siu_id')->constrained('materias');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mallas_espejos_detalles');
    }
};
