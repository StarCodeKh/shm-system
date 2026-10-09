<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\SendDailyNotifications::class,
        Commands\SendRecurringPaymentNotification::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        // Generate payables on the 1st of every month at 1 AM
        $schedule->command('payables:generate')
            ->monthlyOn(1, '01:00')
            ->withoutOverlapping();

        // Dispatch pending payments every hour, starting from 2 AM on the 1st
        // This allows a buffer after payable generation and handles retries/newly pending items
        $schedule->command('payments:dispatch')
            ->hourly()
            ->between('02:00', '23:00') // Run during business hours or when queues are less busy
            ->withoutOverlapping();

        // Ensure queue workers are running
        $schedule->command('queue:work --stop-when-empty --queue=payments,default')
            ->everyMinute()
            ->withoutOverlapping();

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
