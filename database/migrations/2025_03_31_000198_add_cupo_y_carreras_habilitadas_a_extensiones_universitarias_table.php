<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('extensiones_universitarias', function (Blueprint $table) {
            $table->unsignedInteger('cupo_maximo')->nullable()->after('cantidad_horas');
        });

        // Carreras habilitadas para postularse a cada proyecto. Sin filas para
        // un proyecto dado significa "abierto a todas las carreras".
        Schema::create('extension_universitaria_carreras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('extension_universitaria_id')->constrained('extensiones_universitarias', 'id', 'ext_univ_carreras_ext_univ_id_foreign')->cascadeOnDelete();
            $table->foreignId('carrera_id')->constrained('carreras', 'id', 'ext_univ_carreras_carrera_id_foreign')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['extension_universitaria_id', 'carrera_id'], 'ext_univ_carreras_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extension_universitaria_carreras');
        Schema::table('extensiones_universitarias', function (Blueprint $table) {
            $table->dropColumn('cupo_maximo');
        });
    }
};
