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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->timestamp('fecha');
            $table->foreignId('alumno_id')->nullable()->constrained('alumnos');
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('punto_impresion_id')->constrained('puntos_impresiones');
            $table->string('numero_factura');
            $table->decimal('monto_total', 11, 2);
            $table->decimal('saldo', 11, 2)->default(0);
            $table->char('forma_pago', 2)->nullable();
            $table->integer('credito_a')->nullable();
            $table->foreignId('descuento_aplicado_id')->nullable()->constrained('convenios');
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
        Schema::dropIfExists('ventas');
    }
};
