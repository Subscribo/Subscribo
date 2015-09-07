<?php

namespace Subscribo\ApiServerJob\Jobs\Regular;

use Subscribo\ApiServerJob\Jobs\AbstractJob;
use Subscribo\ApiServerJob\Jobs\Regular\ServiceMaintenance;
use Subscribo\ModelCore\Models\Service;
use Psr\Log\LoggerInterface;
use Illuminate\Foundation\Bus\DispatchesJobs;

/**
 * Class HourlyMaintenance
 *
 * @package Subscribo\ApiServerJob
 */
class HourlyMaintenance extends AbstractJob
{
    use DispatchesJobs;

    /**
     * @param LoggerInterface $log
     */
    public function handle(LoggerInterface $log)
    {
        $log->info('Hourly maintenance job starting');

        $services = Service::all();
        foreach ($services as $service) {
            $job = new ServiceMaintenance($service);
            $this->dispatch($job);
            $log->info("Service '".$service->identifier."' maintenance added.");
        }

        $log->info('Hourly maintenance job finished');
    }
}
