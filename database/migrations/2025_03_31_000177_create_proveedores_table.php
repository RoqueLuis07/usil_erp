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
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_fantasia');
            $table->string('razon_social');
            $table->string('ruc');
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('direccion')->nullable();
            $table->foreignId('departamento_id')->nullable()->constrained('departamentos_paraguay');
            $table->foreignId('ciudad_id')->nullable()->constrained('ciudades');
            $table->foreignId('categoria_id')->nullable()->constrained('proveedores_categorias');
            $table->string('nombre_contacto')->nullable();
            $table->string('telefono_contacto')->nullable();
            $table->string('email_contacto')->nullable();
            $table->foreignId('banco_id')->nullable()->constrained('bancos');
            $table->string('numero_cuenta')->nullable();
            $table->char('tipo_cuenta', 2)->nullable();
            $table->foreignId('moneda_id')->nullable()->constrained('monedas');
            $table->string('titular')->nullable();
            $table->string('documento_titular')->nullable();
            $table->string('alias_cuenta')->nullable();
            $table->char('estado', 2)->default('AC');
            $table->text('observaciones')->nullable();
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
        Schema::dropIfExists('proveedores');
    }
};
