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
        Schema::create('notas_creditos', function (Blueprint $table) {
            $table->id();
            $table->timestamp('fecha');
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('punto_impresion_id')->constrained('puntos_impresiones');
            $table->foreignId('venta_id')->constrained('ventas');
            $table->string('numero_nota_credito');
            $table->decimal('monto_total', 11, 2);
            $table->boolean('pago_reversado');
            $table->string('descripcion');
            $table->char('estado', 2)->default('AC');
            $table->foreignId('cargado_por_id')->constrained('usuarios');
            $table->foreignId('anulado_por_id')->nullable()->constrained('usuarios');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notas_creditos');
    }
};
