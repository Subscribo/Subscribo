<?php

namespace Subscribo\TransactionPluginPayUnity\Drivers;

use Subscribo\TransactionPluginManager\Bases\TransactionPluginDriverBase;
use Subscribo\TransactionPluginManager\Interfaces\TransactionFacadeInterface;
use Subscribo\TransactionPluginManager\Interfaces\TransactionPluginChargeDriverInterface;
use Subscribo\TransactionPluginPayUnity\Processors\CopyAndPayProcessor;

/**
 * Class CopyAndPayDriver
 *
 * @package Subscribo\TransactionPluginPayUnity
 */
class CopyAndPayDriver extends TransactionPluginDriverBase implements TransactionPluginChargeDriverInterface
{
    const DRIVER_IDENTIFIER = 'subscribo/pay_unity-copy_and_pay';

    public static function getDriverIdentifier()
    {
        return static::DRIVER_IDENTIFIER;
    }

    public function makeProcessor(TransactionFacadeInterface $transaction)
    {
        return new CopyAndPayProcessor($this, $transaction);
    }
}
