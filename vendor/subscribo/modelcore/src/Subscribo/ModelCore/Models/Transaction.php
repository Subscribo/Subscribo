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


    public function getProcessingDataAttribute($value)
    {
        return json_decode($value, true);
    }


    public function setProcessingDataAttribute($value)
    {
        $this->attributes['processing_data'] = json_encode($value);
    }


    public function changeStage($stage, $status = self::STATUS_NO_RESPONSE_YET, $timestamp = null, $message = null, $code = null)
    {
        $this->stage = $stage;
        $this->status = $status;
        $sentTimestamp = false;
        $receiveTimestamp = false;
        $finalizeTimestamp = false;
        $revisitTimestamp = false;
        if (isset($timestamp)) {
            $timeStampTypes = is_array($timestamp) ? $timestamp : explode(',', $timestamp);
            foreach($timeStampTypes as $timeStampType) {
                switch($timeStampType) {
                    case 'sent':
                        $sentTimestamp = true;
                        break;
                    case 'receive':
                        $receiveTimestamp = true;
                        break;
                    case 'finalize':
                        $finalizeTimestamp = true;
                        break;
                    case 'revisit':
                        $revisitTimestamp = true;
                        break;
                }
            }
        }
        switch ($stage) {
            case static::STAGE_PREPARATION_REQUESTED:
            case static::STAGE_AUTHORIZATION_REQUESTED:
            case static::STAGE_AUTHORIZATION_COMPLETING_REQUESTED:
            case static::STAGE_CAPTURE_REQUESTED:
            case static::STAGE_CHARGE_REQUESTED:
            case static::STAGE_CHARGE_COMPLETING_REQUESTED:
                if (is_null($timestamp)) {
                    $sentTimestamp = true;
                }
                break;
            case static::STAGE_PREPARATION_RESPONSE_RECEIVED:
            case static::STAGE_AUTHORIZATION_RESPONSE_RECEIVED:
            case static::STAGE_AUTHORIZATION_COMPLETING_RESPONSE_RECEIVED:
            case static::STAGE_CAPTURE_RESPONSE_RECEIVED:
            case static::STAGE_CHARGE_RESPONSE_RECEIVED:
            case static::STAGE_CHARGE_COMPLETING_RESPONSE_RECEIVED:
                if (is_null($timestamp)) {
                    $receiveTimestamp = true;
                }
                break;
            case static::STAGE_FINISHED:
                if (is_null($timestamp)) {
                    $finalizeTimestamp = true;
                }
                break;
            case static::STAGE_REVERTED:
                if (is_null($timestamp)) {
                    $revisitTimestamp = true;
                }
                break;
        }
        if ($sentTimestamp) {
            $this->lastRequestSentOn = date('Y-m-d H:i:s');
            if (empty($this->firstRequestSentOn)) {
                $this->firstRequestSentOn = date('Y-m-d H:i:s');
            }
        }
        if ($receiveTimestamp) {
            $this->lastResponseReceivedOn = date('Y-m-d H:i:s');
            if (empty($this->firstResponseReceivedOn)) {
                $this->firstResponseReceivedOn = date('Y-m-d H:i:s');
            }
        }
        if ($finalizeTimestamp) {
            $this->finalizedOn = date('Y-m-d H:i:s');
        }
        if ($revisitTimestamp) {
            $this->revisitedOn = date('Y-m-d H:i:s');
        }
        if ($message) {
            $this->message = $message;
        }
        if (isset($code)) {
            $this->code = $code;
        }
        $this->save();

        return $this;
    }
}
