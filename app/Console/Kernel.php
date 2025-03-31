<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * These cron jobs are scheduled using the system's cron service.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        // ... existing code ...
        
        // Zadania związane z subskrypcjami
        $schedule->command('subscriptions:check-expiring --days=7 --send-emails')
            ->dailyAt('09:00')
            ->appendOutputTo(storage_path('logs/subscriptions-expiring-7days.log'));
            
        $schedule->command('subscriptions:check-expiring --days=3 --send-emails')
            ->dailyAt('09:15')
            ->appendOutputTo(storage_path('logs/subscriptions-expiring-3days.log'));
            
        $schedule->command('subscriptions:check-expiring --days=1 --send-emails')
            ->dailyAt('09:30')
            ->appendOutputTo(storage_path('logs/subscriptions-expiring-1day.log'));
            
        $schedule->command('subscriptions:process-renewals')
            ->dailyAt('01:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/subscriptions-renewals.log'));
            
        $schedule->command('subscriptions:cleanup-expired --days=7')
            ->dailyAt('02:00')
            ->appendOutputTo(storage_path('logs/subscriptions-cleanup.log'));
            
        $schedule->command('report:subscriptions --email=admin@example.com')
            ->weeklyOn(1, '08:00') // Każdy poniedziałek o 8:00
            ->appendOutputTo(storage_path('logs/subscriptions-report.log'));
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

    protected $commands = [
        //
        Commands\CreateManualSubscription::class,
    ];
} 