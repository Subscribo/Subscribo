<?php

namespace Subscribo\ModelCore\Models;

use Subscribo\ModelCore\Traits\FilterableByServiceTrait;


/**
 * Model Delivery
 *
 * Model class for being changed and used in the application
 *
 * @method \Illuminate\Database\Eloquent\Builder availableForOrdering() public static availableForOrdering(int|null $limit) scope limiting the result to those deliveries, which are available for ordering, ordering by start and optionally limiting by $limit count
 */
class Delivery extends \Subscribo\ModelCore\Bases\Delivery
{
    use FilterableByServiceTrait;

    public static function getAvailableForOrderingByService($service, $limit)
    {
        return static::byService($service, false)->availableForOrdering($limit)->get();
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder$query
     * @param null|int $limit
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAvailableForOrdering($query, $limit = null)
    {
        $query->where('is_available_for_ordering', true);
        if ($limit) {
            $query->take($limit);
        }
        $query->orderBy('start');

        return $query;
    }
}
