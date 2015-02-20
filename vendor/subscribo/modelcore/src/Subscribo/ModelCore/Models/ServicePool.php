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
     * @param int $serviceId
     * @return bool
     */
    public static function isInPool($pools, $serviceId)
    {
        $pools = (is_array($pools) or ($pools instanceof Traversable)) ? $pools : [$pools];
        foreach ($pools as $pool) {
            if ($pool->containService($serviceId)) {
                return true;
            }
        }
        return false;
    }

    public function containService($serviceId)
    {
        $serviceId = intval($serviceId);
        foreach ($this->services as $service) {
            if ($serviceId === $service->id) {
                return true;
            }
        }
        return false;
    }

}
