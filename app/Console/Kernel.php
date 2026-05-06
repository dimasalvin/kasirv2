<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Reset demo database setiap X jam
        if (config('app.demo_mode')) {
            $hours = config('app.demo_reset_hours', 2);
            $schedule->command('demo:reset')
                ->cron("0 */{$hours} * * *")
                ->withoutOverlapping()
                ->runInBackground();
        }
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}
