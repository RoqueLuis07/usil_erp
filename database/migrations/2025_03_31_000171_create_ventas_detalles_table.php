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
        Schema::create('ventas_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->nullable()->constrained('ventas');
            $table->integer('cantidad')->nullable();
            $table->string('descripcion');
            $table->decimal('descuento', 11, 2)->default(0);
            $table->decimal('monto_bruto', 11, 2);
            $table->decimal('monto_neto', 11, 2);
            $table->foreignId('pago_matriculacion_id')->nullable()->constrained('pagos_matriculaciones');
            $table->foreignId('pago_inscripcion_ubs_id')->nullable()->constrained('pagos_inscripciones_ubs');
            $table->foreignId('pago_solicitud_id')->nullable()->constrained('pagos_solicitudes');
            $table->foreignId('pago_tesis_id')->nullable()->constrained('pagos_tesis');
            $table->foreignId('pago_tutoria_id')->nullable()->constrained('pagos_tutorias');
            $table->foreignId('pago_tesis_ubs_id')->nullable()->constrained('pagos_tesis_ubs');
            $table->foreignId('articulo_id')->nullable()->constrained('articulos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas_detalles');
    }
};
