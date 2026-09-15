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
        Schema::create('movimientos_cajas_bancos', function (Blueprint $table) {
            $table->id();
            $table->timestamp('fecha');
            $table->foreignId('caja_id')->constrained('cajas');
            $table->foreignId('cuenta_bancaria_id')->constrained('cuentas_bancarias');
            $table->char('sentido', 1);
            $table->decimal('monto', 11, 2);
            $table->foreignId('tipo_movimiento_id')->constrained('tipos_movimientos');
            $table->text('motivo');
            $table->char('estado', 2)->default('PE');
            $table->foreignId('creado_por_id')->constrained('usuarios');
            $table->foreignId('actualizado_por_id')->nullable()->constrained('usuarios');
            $table->foreignId('aprobado_por_id')->nullable()->constrained('usuarios');
            $table->foreignId('rechazado_por_id')->nullable()->constrained('usuarios');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_cajas_bancos');
    }
};
