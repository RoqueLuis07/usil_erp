<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Semestre;
use Carbon\Carbon;

class InactivarSemestres extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inactivar:semestres';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Se inactivan todos los semestres que hayan pasado su fecha de fin.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $fecha_hoy = Carbon::today()->startOfDay();

            $semestres = Semestre::where('estado', 'AC')->where('fecha_fin', '<', $fecha_hoy)->get();
            foreach ($semestres as $semestre) {
                $semestre->estado = 'IN';
                $semestre->save();
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage());
        }
    }
}
