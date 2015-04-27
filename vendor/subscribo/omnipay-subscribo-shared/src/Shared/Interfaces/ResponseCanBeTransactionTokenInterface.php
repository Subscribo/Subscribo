<?php

namespace Subscribo\Omnipay\Shared\Interfaces;

/**
 * Interface ResponseCanBeTransactionTokenInterface
 *
 * @package Subscribo\OmnipaySubscriboShared
 */
interface ResponseCanBeTransactionTokenInterface
{
    /**
     * @return bool
     */
    public function isTransactionToken();

    /**
     * @return string|null
     */
    public function getTransactionToken();

}
