<?php namespace Subscribo\ModelCore\Models;


/**
 * Model PaymentConfiguration
 *
 * Model class for being changed and used in the application
 */
class PaymentConfiguration extends \Subscribo\ModelCore\Bases\PaymentConfiguration
{
    /**
     * @param $serviceId
     * @param null $countryId
     * @param null $currencyId
     * @param bool $withPaymentMethod
     * @return \Illuminate\Database\Eloquent\Collection|static|PaymentConfiguration[]
     */
    public static function findByAttributes($serviceId, $countryId = null, $currencyId = null, $withPaymentMethod = false)
    {
        $mainQuery = static::query();
        if ($withPaymentMethod) {
            $mainQuery->with('paymentMethod');
            $mainQuery->with('paymentMethod.translations');
        }
        $mainQuery->where('service_id', $serviceId);
        if ($countryId) {
            $mainQuery->where(function ($query) use ($countryId) {
                $query->whereNull('country_id');
                $query->orWhere('country_id', $countryId);
            });
        } else {
            $mainQuery->whereNull('country_id');
        }
        if ($currencyId) {
            $mainQuery->where(function ($query) use ($currencyId) {
                $query->whereNull('currency_id');
                $query->orWhere('currency_id', $currencyId);
            });
        } else {
            $mainQuery->whereNull('currency_id');
        }

        return $mainQuery->get();
    }

}
