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
        Schema::create('saldos_cuentas_contables_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saldo_cuenta_contable_id')->constrained('saldos_cuentas_contables', 'id', 'saldos_cta_contables_det_saldo_cta_id_foreign');
            $table->integer('mes');
            $table->integer('anho');
            $table->decimal('debe', 11, 2)->nullable();
            $table->decimal('haber', 11, 2)->nullable();
            $table->foreignId('venta_id')->nullable()->constrained('ventas');
            // $table->foreignId('compra_id')->nullable()->constrained('compras');
            $table->foreignId('movimiento_caja_id')->nullable()->constrained('movimientos_cajas');
            $table->foreignId('movimiento_banco_id')->nullable()->constrained('movimientos_bancos');
            $table->foreignId('movimiento_caja_banco_id')->nullable()->constrained('movimientos_cajas_bancos', 'id', 'saldos_cta_contables_det_mov_caja_banco_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saldos_cuentas_contables_detalles');
    }
};
