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
        Schema::create('mallas_espejos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('malla_paraguay_id')->constrained('mallas');
            $table->foreignId('escala_paraguay_id')->constrained('escalas');
            $table->foreignId('malla_siu_id')->constrained('mallas');
            $table->foreignId('escala_siu_id')->constrained('escalas');
            $table->char('estado', 2)->default('AC');
            $table->foreignId('cargado_por_id')->constrained('usuarios');
            $table->foreignId('actualizado_por_id')->nullable()->constrained('usuarios');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mallas_espejos');
    }
};
