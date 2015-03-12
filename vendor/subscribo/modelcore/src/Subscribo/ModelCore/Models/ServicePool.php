<?php namespace Subscribo\ModelCore\Models;

use Subscribo\ModelCore\Models\Service;
use Traversable;

/**
 * Model ServicePool - auxiliary model, allowing grouping of services, allowing user to use same customer for different services
 *
 * Model class for being changed and used in the application
 */
class ServicePool extends \Subscribo\ModelCore\Bases\ServicePool
{

    /**
     * @param ServicePool[]|ServicePool $pools
     * @param Service|int|string $service
     * @return bool
     */
    public static function isInPool($pools, $service)
    {
        $serviceId = ($service instanceof Service) ? $service->id : $service;
        $pools = (is_array($pools) or ($pools instanceof Traversable)) ? $pools : [$pools];
        foreach ($pools as $pool) {
            if ($pool->containService($serviceId)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param int|string $serviceId
     * @return bool
     */
    public function containService($serviceId)
    {
        $serviceId = strval($serviceId);
        foreach ($this->services as $service) {
            if (strval($service->id) === $serviceId) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Service|int $service1
     * @param Service|int $service2
     * @return bool|null
     */
    public static function servicesAreInSamePool($service1, $service2)
    {
        if (empty($service1) or empty($service2)) {
            return null;
        }
        $service1 = ($service1 instanceof Service) ? $service1 : Service::with('servicePools')->find($service1);
        $pools = $service1->servicePools;
        return static::isInPool($pools, $service2);
        //todo refactor more effectively
    }
}
