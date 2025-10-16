<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void{
        // Auto-publish scheduled resource files (Good News)
        $schedule->command('resourcefile:publish-scheduled')
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground()
            ->sendOutputTo(storage_path('logs/scheduled-publish.log'));
        
        // Auto-publish scheduled resources (Latest Sermon)
        $schedule->command('resource:publish-scheduled')
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground()
            ->sendOutputTo(storage_path('logs/scheduled-publish.log'));

        // Auto-publish scheduled news (News Feed)
        $schedule->command('news:publish-scheduled')
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground()
            ->sendOutputTo(storage_path('logs/scheduled-publish.log'));
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