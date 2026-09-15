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
        Schema::create('pagos_tutorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutoria_id')->constrained('tutorias');
            $table->foreignId('alumno_id')->constrained('alumnos');
            $table->string('descripcion');
            $table->date('vencimiento');
            $table->decimal('monto', 11, 2);
            $table->timestamp('fecha_pago')->nullable();
            $table->decimal('saldo', 11, 2);
            $table->char('estado', 2)->default('PE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos_tutorias');
    }
};
