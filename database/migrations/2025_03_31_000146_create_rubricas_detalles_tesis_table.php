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
        Schema::create('rubricas_detalles_tesis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rubrica_id')->constrained('rubricas_tesis');
            $table->foreignId('nivel_id')->constrained('niveles_rubricas_tesis');
            $table->text('descripcion');
            $table->integer('puntos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rubricas_detalles_tesis');
    }
};
