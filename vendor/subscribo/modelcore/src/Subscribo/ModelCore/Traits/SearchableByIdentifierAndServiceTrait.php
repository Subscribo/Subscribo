<?php

namespace Subscribo\ModelCore\Traits;

use Subscribo\ModelCore\Traits\FilterableByServiceTrait;
use Subscribo\ModelCore\Models\Service;

/**
 * Trait SearchableByIdentifierTrait
 * Trait for models with identifier and service_id
 *
 * @package Subscribo\ModelBase
 */
trait SearchableByIdentifierAndServiceTrait
{
    use FilterableByServiceTrait;

    /**
     * @param int|string $identifier ID or identifier
     * @param int|null|Service $service
     * @param bool $alsoCommon
     * @return null|static
     */
    public static function findByIdentifierAndService($identifier, $service, $alsoCommon = false)
    {
        if (empty($identifier)) {

            return null;
        }
        if (empty($serviceId) and empty($alsoCommon)) {

            return null;
        }
        $serviceId = static::extractServiceIdFromService($service);

        if (is_numeric($identifier)) {
            $instance = static::find($identifier);
        } else {
            $mainQuery = static::query();
            $mainQuery->where('identifier', $identifier);
            if (empty($serviceId)) {
                $mainQuery->whereNull('service_id');
            } elseif ($alsoCommon) {
                $mainQuery->where(function ($query) use ($serviceId) {
                    $query->whereNull('service_id');
                    $query->orWhere('service_id', $serviceId);
                });
            } else {
                $mainQuery->where('service_id', $serviceId);
            }
            $instance = $mainQuery->first();
        }
        if (empty($instance)) {

            return null;
        }
        if ($instance->serviceId === $serviceId) {

            return $instance;
        }

        if ($alsoCommon and is_null($instance->serviceId)) {

            return $instance;
        }

        return null;
    }
}
