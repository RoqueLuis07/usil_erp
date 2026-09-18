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
        Schema::table('requerimientos_extensiones_universitarias', function (Blueprint $table) {
            // Nullable: una fila con carrera_id null es el requerimiento "general"
            // (fallback cuando una carrera no tiene su propio requerimiento
            // definido). Antes de esto solo podía existir una fila global única.
            $table->foreignId('carrera_id')->nullable()->after('id')->constrained('carreras', 'id', 'req_ext_univ_carrera_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requerimientos_extensiones_universitarias', function (Blueprint $table) {
            $table->dropForeign('req_ext_univ_carrera_id_foreign');
            $table->dropColumn('carrera_id');
        });
    }
};
