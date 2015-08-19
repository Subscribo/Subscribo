<?php

namespace Subscribo\TransactionPluginPayUnity\Drivers;

use Subscribo\TransactionPluginManager\Bases\TransactionPluginDriverBase;
use Subscribo\TransactionPluginManager\Interfaces\TransactionFacadeInterface;

/**
 * Class PostDriver
 *
 * @package Subscribo\TransactionPluginPayUnity
 */
class PostDriver extends TransactionPluginDriverBase
{
    const DRIVER_IDENTIFIER = 'subscribo/pay_unity-post';

    public static function getDriverIdentifier()
    {
        return static::DRIVER_IDENTIFIER;
    }

    public function makeProcessor(TransactionFacadeInterface $transaction)
    {
        //todo implement
    }
}
