<?php

namespace Subscribo\TransactionPluginManager\Interfaces;

/**
 * Class InterruptionFacadeInterface
 *
 * @package Subscribo\TransactionPluginManager
 */
interface InterruptionFacadeInterface
{
    /**
     * @return string
     */
    public function getHash();

    /**
     * @return \Subscribo\ModelCore\Models\ActionInterruption
     */
    public function getActionInterruptionModelInstance();
}
