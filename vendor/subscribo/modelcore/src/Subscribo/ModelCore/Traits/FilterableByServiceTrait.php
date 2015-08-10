<?php

namespace Subscribo\ModelCore\Traits;

use InvalidArgumentException;
use Subscribo\ModelCore\Models\Service;

/**
 * Trait FilterableByServiceIdTrait
 *
 * Trait for models with serviceId
 *
 * @package Subscribo\ModelBase\Traits
 *
 * @method \Illuminate\Database\Eloquent\Builder byService() public static byService(null|int|Service $service, bool $addCommon) Scope method filtering query results by service(id)
 */
trait FilterableByServiceTrait
{
    /**
     * @param null|int|Service $service
     * @param bool $addCommon
     * @return \Illuminate\Database\Eloquent\Collection|static
     */
    public static function getAllByService($service, $addCommon = false)
    {
        return static::byService($service, $addCommon)->get();
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param null|int|Service $service Service or it's ID. Null for common (shared among services) items
     * @param bool $addCommon Whether to add results without service_id specified (null)
     * @return \Illuminate\Database\Eloquent\Builder
     * @throws \InvalidArgumentException
     */
    public function scopeByService($query, $service, $addCommon = false)
    {
        $query->where('service_id', static::extractServiceIdFromService($service));
        if ($service and $addCommon) {
            $query->orWhereNull('service_id');
        }

        return $query;
    }

    /**
     * @param null|int|Service $service Service or it's ID or null
     * @return int|null|string
     * @throws \InvalidArgumentException
     */
    protected static function extractServiceIdFromService($service)
    {
        if (is_null($service)) {

            return null;
        } elseif (is_numeric($service)) {

            return intval($service);
        } elseif ($service instanceof Service) {

            return $service->id;
        } else {

            throw new InvalidArgumentException('Specified service is of wrong type');
        }
    }
}
