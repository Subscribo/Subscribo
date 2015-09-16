<?php

namespace Subscribo\TransactionPluginPayUnity\Drivers;

use Subscribo\TransactionPluginManager\Bases\TransactionPluginDriverBase;
use Subscribo\TransactionPluginManager\Interfaces\TransactionFacadeInterface;
use Subscribo\TransactionPluginPayUnity\Processors\PostProcessor;
use Subscribo\TransactionPluginManager\Interfaces\TransactionPluginChargeDriverInterface;

/**
 * Class PostDriver
 *
 * @package Subscribo\TransactionPluginPayUnity
 */
class PostDriver extends TransactionPluginDriverBase implements TransactionPluginChargeDriverInterface
{
    const DRIVER_IDENTIFIER = 'subscribo/pay_unity-post';

    /**
     * @return string
     */
    public static function getDriverIdentifier()
    {
        return static::DRIVER_IDENTIFIER;
    }

    /**
     * @param TransactionFacadeInterface $transaction
     * @return PostProcessor
     */
    public function makeProcessor(TransactionFacadeInterface $transaction)
    {
        return new PostProcessor($this, $transaction);
    }
}
