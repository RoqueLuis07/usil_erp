<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */

    protected $commands = [
        'App\Console\Commands\InactivarAnulacionesCorrelatividades',
        'App\Console\Commands\InactivarSemestres',
    ];

    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('inactivar:anulaciones_correlatividades')
                    ->daily() //se ejecuta todos los dias a la medianoche
                    ->timezone('America/Asuncion')
                    ->onFailure(function () {
                        Log::info('Inactivar anulaciones de correlatividades falló.');
                    });

        $schedule->command('inactivar:semestres')
                    ->everySecond()
                    // ->daily() //se ejecuta todos los dias a la medianoche
                    ->timezone('America/Asuncion')
                    ->onFailure(function () {
                        Log::info('Inactivar semestres falló.');
                    });
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
