<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Carrera y período de ingreso guardados en el alumno: hoy solo se
        // deducen de las matriculaciones, que un alumno cargado masivamente
        // (o recién dado de alta) todavía no tiene. La facultad sale de la
        // carrera y el semestre cursando se calcula a partir del ingreso.
        Schema::table('alumnos', function (Blueprint $table) {
            $table->foreignId('carrera_id')->nullable()->after('barrio_id')->constrained('carreras', 'id', 'alumnos_carrera_id_foreign');
            $table->unsignedSmallInteger('anho_ingreso')->nullable()->after('carrera_id');
            $table->unsignedTinyInteger('semestre_ingreso')->nullable()->after('anho_ingreso');
        });

        // Carreras en las que enseña un docente (puede ser más de una). La
        // facultad sale de cada carrera.
        Schema::create('docentes_carreras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('docente_id')->constrained('docentes', 'id', 'docentes_carreras_docente_id_foreign')->cascadeOnDelete();
            $table->foreignId('carrera_id')->constrained('carreras', 'id', 'docentes_carreras_carrera_id_foreign')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['docente_id', 'carrera_id'], 'docentes_carreras_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('docentes_carreras');
        Schema::table('alumnos', function (Blueprint $table) {
            $table->dropForeign('alumnos_carrera_id_foreign');
            $table->dropColumn(['carrera_id', 'anho_ingreso', 'semestre_ingreso']);
        });
    }
};
