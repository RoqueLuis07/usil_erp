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
        Schema::create('requerimientos_entregas_tesis', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_inicio_anteproyecto');
            $table->date('fecha_fin_anteproyecto');
            $table->date('fecha_inicio_proyecto');
            $table->date('fecha_fin_proyecto');
            $table->date('fecha_inicio_borrador');
            $table->date('fecha_fin_borrador');
            $table->foreignId('actualizado_por_id')->constrained('usuarios');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requerimientos_entregas_tesis');
    }
};
