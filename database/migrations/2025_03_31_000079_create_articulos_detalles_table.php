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
        Schema::create('articulos_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('articulo_id')->constrained('articulos');
            $table->integer('cantidad_cuotas')->nullable();
            $table->decimal('precio_matricula', 11, 2)->nullable();
            $table->foreignId('cuenta_matricula_id')->nullable()->constrained('cuentas_contables');
            $table->decimal('precio_contado', 11, 2)->nullable();
            $table->foreignId('cuenta_contado_id')->nullable()->constrained('cuentas_contables');
            $table->decimal('precio_cuota', 11, 2)->nullable();
            $table->foreignId('cuenta_cuota_id')->nullable()->constrained('cuentas_contables');
            $table->foreignId('cuenta_descuento_id')->nullable()->constrained('cuentas_contables');
            $table->decimal('precio_multa', 11, 2)->nullable();
            $table->foreignId('cuenta_multa_id')->nullable()->constrained('cuentas_contables');
            $table->integer('dia_vencimiento_cuotas')->nullable();
            $table->date('fecha_vencimiento_primera_cuota')->nullable();
            $table->integer('dias_gracia')->nullable();
            $table->decimal('precio_defensa', 11, 2)->nullable();
            $table->foreignId('cuenta_defensa_id')->nullable()->constrained('cuentas_contables');
            $table->decimal('precio_titulo', 11, 2)->nullable();
            $table->foreignId('cuenta_titulo_id')->nullable()->constrained('cuentas_contables');
            $table->decimal('precio_certificado', 11, 2)->nullable();
            $table->foreignId('cuenta_certificado_id')->nullable()->constrained('cuentas_contables');
            $table->decimal('precio_examen_suficiencia', 11, 2)->nullable();
            $table->foreignId('cuenta_examen_suficiencia_id')->nullable()->constrained('cuentas_contables');
            $table->decimal('precio_constancia_carrera', 11, 2)->nullable();
            $table->foreignId('cuenta_constancia_carrera_id')->nullable()->constrained('cuentas_contables');
            $table->decimal('precio_ultima_compra', 11, 2)->nullable();
            $table->foreignId('cuenta_compra_id')->nullable()->constrained('cuentas_contables');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articulos_detalles');
    }
};
