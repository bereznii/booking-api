<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\KeyGenerateCommand',
        'App\Console\Commands\UpdateUniqueTestsTableCommand',
        'App\Console\Commands\SyncCentersTableCommand',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('syncTable:centers')->dailyAt('03:00');

        $schedule->command('updateTable:unique_tests')->dailyAt('04:00');
    }
}
