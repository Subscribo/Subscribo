<?php

namespace Subscribo\TransactionPluginDummy\Drivers;

use Subscribo\TransactionPluginManager\Bases\TransactionPluginDriverBase;
use Subscribo\TransactionPluginManager\Interfaces\TransactionFacadeInterface;
use Subscribo\TransactionPluginManager\Interfaces\TransactionPluginChargeDriverInterface;
use Subscribo\TransactionPluginDummy\Processors\SuccessForAllProcessor;

/**
 * Class SuccessForAllDriver
 * @package Subscribo\TransactionPluginDummy
 */
class SuccessForAllDriver extends TransactionPluginDriverBase implements TransactionPluginChargeDriverInterface
{
    const DRIVER_IDENTIFIER = "dummy/success_for_all";

    /**
     * @return string
     */
    public static function getDriverIdentifier()
    {
        return static::DRIVER_IDENTIFIER;
    }

    /**
     * @param TransactionFacadeInterface $transaction
     * @return SuccessForAllProcessor
     */
    public function makeProcessor(TransactionFacadeInterface $transaction)
    {
        return new SuccessForAllProcessor($this, $transaction);
    }
}
