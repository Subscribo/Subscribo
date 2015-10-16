<?php

namespace Subscribo\ModelBase\Traits;

/**
 * Class SearchableByIdentifierTrait
 * Trait for models with identifier, but not service_id
 *
 * @package Subscribo\ModelBase
 */
trait SearchableByIdentifierTrait
{
    /**
     * @param int|string $identifier ID or identifier
     *
     * @return null|static
     */
    public static function findByIdentifier($identifier)
    {
        if (empty($identifier)) {

            return null;
        }
        if (is_numeric($identifier)) {

            return static::find($identifier);
        }
        $query = static::query();
        $query->where('identifier', $identifier);

        return $query->first();
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int|string $identifier ID or identifier
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByIdentifier($query, $identifier)
    {
        if (is_numeric($identifier)) {
            $query->where($this->getQualifiedKeyName(), '=', $identifier);
        } else {
            $query->where('identifier', '=', $identifier);
        }

        return $query;
    }
}
