<?php

use Database\Seeders\CatalogosBaseSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Llega también a bases ya sembradas (Railway), donde el entrypoint solo
     * corre db:seed la primera vez. El seeder es idempotente.
     */
    public function up(): void
    {
        (new CatalogosBaseSeeder())->run();
    }

    public function down(): void
    {
        // Datos de referencia: no se borran al revertir.
    }
};
