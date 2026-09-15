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
        Schema::create('docentes_legajos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('docente_id')->constrained('docentes');
            $table->string('titulo_obtenido')->nullable();
            $table->foreignId('tipo_legajo_id')->constrained('tipos_legajos');
            $table->foreignId('institucion_educativa_id')->nullable()->constrained('instituciones_educativas');
            $table->foreignId('pais_id')->nullable()->constrained('paises');
            $table->string('url_ubicacion');
            $table->string('extension');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('docentes_legajos');
    }
};
