<?php

namespace Subscribo\ModelCore\Models;

use Illuminate\Database\Eloquent\Collection;
use Subscribo\ModelCore\Traits\FilterableByServiceTrait;
use Subscribo\ModelBase\Traits\SearchableByIdentifierTrait;

/**
 * Model SubscriptionPlan
 *
 * Model class for being changed and used in the application
 */
class SubscriptionPlan extends \Subscribo\ModelCore\Bases\SubscriptionPlan
{
    use FilterableByServiceTrait;
    use SearchableByIdentifierTrait;

    /**
     * @param Service|int $service
     * @param array $with
     * @return Collection|SubscriptionPlan[]
     */
    public static function getForServiceWith($service, array $with = [])
    {
        $query = static::addWithToQuery(static::query()->byService($service), $with);

        return $query->get();
    }

    /**
     * @param int|string $identifier ID or identifier
     * @param array $with
     * @return SubscriptionPlan|null
     */
    public static function findByIdentifierWith($identifier, array $with = [])
    {
        $query = static::addWithToQuery(static::query()->byIdentifier($identifier), $with);

        return $query->first();
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $with
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private static function addWithToQuery($query, array $with = [])
    {
        if (in_array('translations', $with, true)) {
            $query = $query->withTranslations();
        }
        if (in_array('products', $with, true)) {
            $query = $query->with('products');
        }

        return $query;
    }
}
