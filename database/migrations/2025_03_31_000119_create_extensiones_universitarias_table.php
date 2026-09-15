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
        Schema::create('extensiones_universitarias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->decimal('cantidad_horas', 11, 2);
            $table->foreignId('tipo_extension_id')->constrained('tipos_extensiones_universitarias');
            $table->foreignId('docente_id')->nullable()->constrained('docentes');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->string('ubicacion_proyecto');
            $table->string('extension_proyecto');
            $table->string('ubicacion_informe')->nullable();
            $table->string('extension_informe')->nullable();
            $table->boolean('tiene_certificado')->nullable();
            $table->char('estado', 2)->default('PE');
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
        Schema::dropIfExists('extensiones_universitarias');
    }
};
