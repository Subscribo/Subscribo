<?php

namespace Subscribo\ApiServerCommon\Jobs\Regular;

use Subscribo\ApiServerCommon\Jobs\AbstractJob;
use Subscribo\ApiServerCommon\Jobs\Regular\ServiceMaintenance;
use Subscribo\ModelCore\Models\Service;
use Psr\Log\LoggerInterface;
use Illuminate\Foundation\Bus\DispatchesJobs;

/**
 * Class HourlyMaintenance
 *
 * @package Subscribo\ApiServerCommon
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
