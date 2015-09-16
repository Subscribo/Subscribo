<?php namespace Subscribo\ModelCore\Models;


/**
 * Model TaxGroup
 *
 * Model class for being changed and used in the application
 */
class TaxGroup extends \Subscribo\ModelCore\Bases\TaxGroup
{
    /**
     * @param $categoryId
     * @param $countryId
     * @return TaxGroup|null
     */
    public static function findByCategoryIdAndCountryId($categoryId, $countryId = null)
    {
        $query = static::query()->where('tax_category_id', $categoryId);
        if (is_null($countryId)) {
            $query->whereNull('country_id');
        } else {
            $query->where('country_id', $countryId);
        }
        $result = $query->first();

        if (is_null($countryId) or $result) {

            return $result;
        }

        return static::query()->where('tax_category_id', $categoryId)->whereNull('country_id')->first();
    }
}
