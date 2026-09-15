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
        Schema::create('cuentas_bancarias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('banco_id')->constrained('bancos');
            $table->string('numero_cuenta');
            $table->char('tipo_cuenta', 2);
            $table->boolean('acredita_tarjeta');
            $table->decimal('monto', 11, 2)->default(0);
            $table->foreignId('moneda_id')->constrained('monedas');
            $table->string('titular');
            $table->string('documento_titular');
            $table->foreignId('cuenta_ingreso_id')->constrained('cuentas_contables');
            $table->foreignId('cuenta_egreso_id')->constrained('cuentas_contables');
            $table->char('estado', 2)->default('AC');
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
        Schema::dropIfExists('cuentas_bancarias');
    }
};
