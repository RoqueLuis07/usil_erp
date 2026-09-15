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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('numero_documento');
            $table->string('razon_social')->nullable();
            $table->foreignId('sexo_id')->nullable()->constrained('sexos');
            $table->date('fecha_nacimiento')->nullable();
            $table->foreignId('estado_civil_id')->nullable()->constrained('estados_civiles');
            $table->foreignId('nacionalidad_id')->nullable()->constrained('nacionalidades');
            $table->string('celular')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('direccion')->nullable();
            $table->foreignId('departamento_id')->nullable()->constrained('departamentos_paraguay');
            $table->foreignId('ciudad_id')->nullable()->constrained('ciudades');
            $table->foreignId('barrio_id')->nullable()->constrained('barrios');
            $table->foreignId('dato_laboral_id')->nullable()->constrained('clientes_datos_laborales');
            $table->text('observaciones')->nullable();
            $table->foreignId('cargado_por_id')->constrained('usuarios');
            $table->foreignId('actualizado_por_id')->nullable()->constrained('usuarios');
            $table->char('estado', 2)->default('AC');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
