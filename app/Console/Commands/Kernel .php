<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // ==============================================
        // 📂 Indexar NAS Intranet (app:indexar-nasx)
        // ==============================================
        
        // 7:00 AM
        $schedule->command('app:indexar-nasx')
            ->dailyAt('07:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/nas-intranet-07am.log'));

        // 12:00 PM
        $schedule->command('app:indexar-nasx')
            ->dailyAt('12:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/nas-intranet-12pm.log'));

        // 5:00 PM
        $schedule->command('app:indexar-nasx')
            ->dailyAt('17:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/nas-intranet-17pm.log'));

        // ==============================================
        // 📂 Indexar NAS Mercadeo (app:indexar-mercadeo)
        // ==============================================
        
        // 7:00 AM
        $schedule->command('app:indexar-mercadeo')
            ->dailyAt('07:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/nas-mercadeo-07am.log'));

        // 12:00 PM
        $schedule->command('app:indexar-mercadeo')
            ->dailyAt('12:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/nas-mercadeo-12pm.log'));

        // 5:00 PM
        $schedule->command('app:indexar-mercadeo')
            ->dailyAt('17:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/nas-mercadeo-17pm.log'));

        // ==============================================
        // 🆕 Opcional: Ejecutar ambos juntos (con un solo cron)
        // ==============================================
        // $schedule->call(function () {
        //     \Artisan::call('app:indexar-nasx');
        //     \Artisan::call('app:indexar-mercadeo');
        // })->dailyAt('07:00')->withoutOverlapping();

        // ==============================================
        // 🔄 Reindexación completa los domingos a las 3 AM
        // ==============================================
        $schedule->command('app:indexar-nasx --force')
            ->weeklyOn(7, '03:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/nas-intranet-force.log'));

        $schedule->command('app:indexar-mercadeo --force')
            ->weeklyOn(7, '03:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/nas-mercadeo-force.log'));
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