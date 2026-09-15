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
        Schema::create('extensiones_universitarias_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('extension_universitaria_id')->constrained('extensiones_universitarias', 'id', 'ext_univ_detalles_ext_univ_id_foreign');
            $table->foreignId('alumno_id')->constrained('alumnos');
            $table->decimal('cantidad_horas', 11, 2)->nullable();
            $table->string('url_certificado')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extensiones_universitarias_detalles');
    }
};
