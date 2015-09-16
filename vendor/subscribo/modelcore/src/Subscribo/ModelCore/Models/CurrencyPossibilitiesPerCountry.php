<?php namespace Subscribo\ModelCore\Models;


/**
 * Model CurrencyPossibilitiesPerCountry
 *
 * Model class for being changed and used in the application
 */
class CurrencyPossibilitiesPerCountry extends \Subscribo\ModelCore\Bases\CurrencyPossibilitiesPerCountry
{
    /**
     * @param int $serviceId
     * @param int|null $countryId
     * @return array
     */
    public static function provideCurrencyIdsForServiceIdAndCountryId($serviceId, $countryId = null)
    {
        return static::extractCurrencyIds(static::findByServiceIdAndCountryId($serviceId, $countryId));
    }

    /**
     * @param int $serviceId
     * @param int|null $countryId
     * @return int|null
     */
    public static function provideDefaultCurrencyIdForServiceIdAndCountryId($serviceId, $countryId = null)
    {
        $instance = static::findDefaultForServiceIdAndCountryId($serviceId, $countryId);

        return $instance ? $instance->currencyId : null;
    }

    /**
     * @param int $serviceId
     * @param int|null $countryId
     * @return \Illuminate\Database\Eloquent\Collection|static[]|CurrencyPossibilitiesPerCountry[]
     */
    public static function findByServiceIdAndCountryId($serviceId, $countryId = null)
    {
        $result = static::buildGeneralQuery($serviceId, $countryId)->get();

        if (is_null($countryId) or $result->count()) {

            return $result;
        }

        return static::buildGeneralQuery($serviceId, null)->get();
    }

    /**
     * @param int $serviceId
     * @param int|null $countryId
     * @return \Illuminate\Database\Eloquent\Model|null|static|CurrencyPossibilitiesPerCountry
     */
    public static function findDefaultForServiceIdAndCountryId($serviceId, $countryId = null)
    {
        $query = static::buildGeneralQuery($serviceId, $countryId);
        $query->where('is_default', true);
        $result = $query->first();
        if ($result) {

            return $result;
        }
        $query2 = static::buildGeneralQuery($serviceId, $countryId);

        $result2 = $query2->first();

        if ($result2) {

            return $result;
        }
        $query3 = static::buildGeneralQuery($serviceId, null);

        return $query3->first();
    }

    /**
     * @param CurrencyPossibilitiesPerCountry[] $instances
     * @return array
     */
    public static function extractCurrencyIds($instances)
    {
        if (empty($instances)) {

            return [];
        }
        $result = [];
        foreach ($instances as $instance) {
            $result[] = $instance->currency_id;
        }
        return $result;
    }

    /**
     * @param int $serviceId
     * @param int|null $countryId
     * @param bool $alsoEverywhere
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function buildGeneralQuery($serviceId, $countryId = null, $alsoEverywhere = false)
    {
        $query = static::query();
        $query->where('service_id', $serviceId);
        if (is_null($countryId)) {
            $query->whereNull('country_id');

            return $query;
        }
        if ($alsoEverywhere) {
            $query->where(function ($q) use ($countryId) {
                $q->whereNull('country_id');
                $q->orWhere('country_id', $countryId);
            });
        } else {
            $query->where('country_id', $countryId);
        }

        return $query;
    }
}
