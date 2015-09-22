<?php

namespace Subscribo\ApiServerJob\Commands;

use Illuminate\Console\Command;
use Subscribo\ApiServerJob\Jobs\Regular\HourlyMaintenance;
use Illuminate\Foundation\Bus\DispatchesJobs;

/**
 * Class MaintainHourly
 *
 * @package Subscribo\ApiServerJob
 */
class MaintainHourly extends Command
{
    use DispatchesJobs;

    protected $signature = 'maintain:hourly';

    protected $description = 'Force running a hourly maintaining job outside of schedule';

    public function handle()
    {
        $job = new HourlyMaintenance();
        $this->dispatch($job);
        $this->info('Hourly maintenance job dispatched.');
    }
}
