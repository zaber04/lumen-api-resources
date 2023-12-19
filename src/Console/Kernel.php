<?php

namespace Zaber04\LumenApiResources\Console;

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
        \Zaber04\LumenApiResources\Console\Commands\MakeDatabaseCommand::class,
        \Zaber04\LumenApiResources\Console\Commands\RouteListCommand::class,
        \Zaber04\LumenApiResources\Console\Commands\VendorPublishCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }
}
