<?php namespace Subscribo\ModelCore\Models;


/**
 * Model TransactionGatewayConfiguration
 *
 * Model class for being changed and used in the application
 *
 * @property array $configuration JSON encoded array - careful - can be saved only whole
 */
class TransactionGatewayConfiguration extends \Subscribo\ModelCore\Bases\TransactionGatewayConfiguration
{
    /**
     * @param int $serviceId
     * @param int|null $countryId
     * @param int|null $currencyId
     * @param int|null $transactionGatewayId
     * @param bool|null $forReceivingPayments
     * @param bool $withTransactionGateway
     * @return \Illuminate\Database\Eloquent\Collection|static|TransactionGatewayConfiguration[]
     */
    public static function getCollectionByAttributes($serviceId, $countryId = null, $currencyId = null, $transactionGatewayId = null, $forReceivingPayments = null, $withTransactionGateway = false)
    {
        $mainQuery = static::query();
        if ($withTransactionGateway) {
            $mainQuery->with('transactionGateway');
            $mainQuery->with('transactionGateway.translations');
        }
        $mainQuery->where('service_id', $serviceId);
        if ($transactionGatewayId) {
            $mainQuery->where('transaction_gateway_id', $transactionGatewayId);
        }
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
        if (isset($forReceivingPayments)) {
            $mainQuery->where('for_receiving_payments', $forReceivingPayments);
        }
        $mainQuery->orderBy('ordering', 'asc');

        return $mainQuery->get();
    }

    /**
     * @param int $serviceId
     * @param int|null $countryId
     * @param int|null $currencyId
     * @param int|null $transactionGatewayId
     * @param bool|null $forReceivingPayments
     * @param bool $withTransactionGateway
     * @return static|TransactionGatewayConfiguration|null
     */
    public static function findByAttributes($serviceId, $countryId = null, $currencyId = null, $transactionGatewayId = null, $forReceivingPayments = null, $withTransactionGateway = false)
    {
        $collection = static::getCollectionByAttributes($serviceId, $countryId, $currencyId, $transactionGatewayId, $forReceivingPayments, $withTransactionGateway);

        return $collection ? $collection->first() : null;
    }

    public function getConfigurationAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setConfigurationAttribute($value)
    {
        $this->attributes['configuration'] = json_encode($value, JSON_BIGINT_AS_STRING);
    }

    /**
     * @return TransactionGatewayConfiguration
     */
    public function acquireRoot()
    {
        $instance = $this;
        while ($instance->parent) {
            $instance = $instance->parent;
        }

        return $instance;
    }
}
