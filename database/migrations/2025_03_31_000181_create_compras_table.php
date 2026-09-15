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
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_compra_id')->nullable()->constrained('ordenes_compras');
            $table->date('fecha');
            $table->foreignId('proveedor_id')->constrained('proveedores');
            $table->char('condicion_compra', 2);
            $table->integer('credito_a')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->foreignId('timbrado_id')->constrained('timbrados_proveedores');
            $table->string('numero_factura');
            $table->foreignId('moneda_id')->constrained('monedas');
            $table->foreignId('cotizacion_id')->constrained('cotizaciones');
            $table->foreignId('unidad_negocio_id')->constrained('unidades_negocios_contables');
            $table->foreignId('subunidad_negocio_id')->constraiend('subunidades_negocios_contables');
            $table->decimal('monto_total', 11, 2)->default(0);
            $table->string('url_factura')->nullable();
            $table->text('observaciones')->nullable();
            $table->char('estado', 2)->default('PE');
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
        Schema::dropIfExists('compras');
    }
};
