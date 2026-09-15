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
        Schema::create('escalas_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('escala_id')->constrained('escalas');
            $table->integer('punto_minimo');
            $table->integer('punto_maximo');
            $table->string('nota', 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escalas_detalles');
    }
};
