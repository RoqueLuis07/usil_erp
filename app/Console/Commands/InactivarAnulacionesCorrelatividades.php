<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\AnulacionCorrelatividad;

class InactivarAnulacionesCorrelatividades extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inactivar:anulaciones_correlatividades';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Se inactivan todas las anulaciones de correlatividades activas hasta este momento.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $anulaciones = AnulacionCorrelatividad::where('estado', 'AC')->get();
            foreach ($anulaciones as $anulacion) {
                $anulacion->estado = 'IN';
                $anulacion->save();
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage());
        }
    }
}
