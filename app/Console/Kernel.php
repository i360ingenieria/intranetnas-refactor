<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\IndexarNasx;
use App\Console\Commands\IndexarMercadeo;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     */
    protected $commands = [
        IndexarNasx::class,
        IndexarMercadeo::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // 📂 NAS Intranet
        $schedule->command(IndexarNasx::class)
            ->dailyAt('07:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/nas-intranet-07am.log'));

        $schedule->command(IndexarNasx::class)
            ->dailyAt('12:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/nas-intranet-12pm.log'));

        $schedule->command(IndexarNasx::class)
            ->dailyAt('17:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/nas-intranet-17pm.log'));

        // 📂 NAS Mercadeo
        $schedule->command(IndexarMercadeo::class)
            ->dailyAt('07:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/nas-mercadeo-07am.log'));

        $schedule->command(IndexarMercadeo::class)
            ->dailyAt('12:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/nas-mercadeo-12pm.log'));

        $schedule->command(IndexarMercadeo::class)
            ->dailyAt('17:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/nas-mercadeo-17pm.log'));

        // 🔄 Reindexación forzada (domingos 3am)
        $schedule->command(IndexarNasx::class . ' --force')
            ->weeklyOn(7, '03:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/nas-intranet-force.log'));

        $schedule->command(IndexarMercadeo::class . ' --force')
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