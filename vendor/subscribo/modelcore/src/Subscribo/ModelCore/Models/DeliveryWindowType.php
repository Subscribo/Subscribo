<?php

namespace Subscribo\ModelCore\Models;

use Subscribo\ModelCore\Models\Service;
use Subscribo\ModelCore\Traits\SearchableByIdentifierAndServiceTrait;

/**
 * Model DeliveryWindowType
 *
 * Model class for being changed and used in the application
 */
class DeliveryWindowType extends \Subscribo\ModelCore\Bases\DeliveryWindowType
{
    use SearchableByIdentifierAndServiceTrait;

    /**
     * @param Service|int $service
     * @param bool $isUsual
     * @return DeliveryWindowType[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getAllUsualByService($service, $isUsual = true)
    {
        return static::byService($service)->onlyUsual($isUsual)->get();
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param bool $isUsual
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOnlyUsual($query, $isUsual = true)
    {
        $query->where('is_usual', $isUsual);

        return $query;
    }

}
