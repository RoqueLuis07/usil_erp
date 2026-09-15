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
        Schema::create('entregas_anteproyectos_tesis_ubs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anteproyecto_id')->constrained('anteproyectos_tesis_ubs');
            $table->string('url_archivo')->nullable();
            $table->string('extension_archivo')->nullable();
            $table->text('comentario')->nullable();
            $table->integer('numero');
            $table->boolean('entrega')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entregas_anteproyectos_tesis_ubs');
    }
};
