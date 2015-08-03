<?php

namespace Subscribo\ModelCore\Models;

use Subscribo\ModelCore\Models\SalesOrder;
use Subscribo\ModelCore\Models\TransactionGatewayConfiguration;
use Subscribo\ModelBase\Traits\HasHashTrait;

/**
 * Model Transaction
 *
 * Model class for being changed and used in the application
 */
class Transaction extends \Subscribo\ModelCore\Bases\Transaction
{
    use HasHashTrait;

    public static function generateFromSalesOrder(SalesOrder $salesOrder, TransactionGatewayConfiguration $transactionGatewayConfiguration = null, $origin = null)
    {
        $instance = static::makeFromSalesOrder($salesOrder, $transactionGatewayConfiguration, $origin);
        $instance->save();

        return $instance;
    }

    /**
     * @param SalesOrder $salesOrder
     * @param TransactionGatewayConfiguration|null $transactionGatewayConfiguration
     * @param string|null $origin
     * @return static
     */
    public static function makeFromSalesOrder(SalesOrder $salesOrder, TransactionGatewayConfiguration $transactionGatewayConfiguration = null, $origin = null)
    {
        /** @var Transaction $instance */
        $instance = static::makeWithHash();
        $instance->salesOrder()->associate($salesOrder);
        $instance->accountId = $salesOrder->accountId;
        $instance->serviceId = $salesOrder->serviceId;
        $instance->currencyId = $salesOrder->currencyId;
        $instance->amount = $salesOrder->grossSum;
        $instance->direction = static::DIRECTION_RECEIVE;
        $instance->type = static::TYPE_STANDARD;
        $instance->stage = static::STAGE_PLANNED;
        $instance->status = static::STATUS_NO_RESPONSE_YET;
        $instance->origin = $origin;
        if ($transactionGatewayConfiguration) {
            $instance->transactionGatewayConfiguration()->associate($transactionGatewayConfiguration);
        }

        return $instance;
    }
}
