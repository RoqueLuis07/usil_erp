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
        Schema::create('noticias_avisos', function (Blueprint $table) {
            $table->id();
            $table->char('tipo', 2);
            $table->timestamp('fecha_hora_publicacion');
            $table->string('titulo');
            $table->text('descripcion');
            $table->string('portada')->nullable();
            $table->boolean('destacado');
            $table->char('estado', 2)->default('ES');
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
        Schema::dropIfExists('noticias_avisos');
    }
};
