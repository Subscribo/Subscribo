<?php

namespace Subscribo\Omnipay\Shared\Interfaces;

/**
 * Interface ResponseCanBeWaitingInterface
 *
 * @package Subscribo\OmnipaySubscriboShared
 */
interface ResponseCanBeWaitingInterface
{
    /**
     * @return bool
     */
    public function isWaiting();

}
