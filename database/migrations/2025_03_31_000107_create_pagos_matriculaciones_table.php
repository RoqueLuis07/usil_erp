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
        Schema::create('pagos_matriculaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matriculacion_id')->constrained('matriculaciones');
            $table->foreignId('alumno_id')->constrained('alumnos');
            $table->string('descripcion');
            $table->char('tipo', 2);
            $table->timestamp('fecha_vencimiento');
            $table->foreignId('moneda_id')->constrained('monedas');
            $table->decimal('monto', 11, 2);
            $table->decimal('monto_convenio', 11, 2)->default(0);
            $table->decimal('monto_multa', 11, 2)->default(0);
            $table->decimal('saldo', 11, 2);
            $table->timestamp('fecha_pago')->nullable();
            $table->char('estado', 2)->default('PE');
            $table->foreignId('actualizado_por_id')->nullable()->constrained('usuarios');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos_matriculaciones');
    }
};
