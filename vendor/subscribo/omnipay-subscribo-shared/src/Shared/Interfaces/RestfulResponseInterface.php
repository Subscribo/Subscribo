<?php

namespace Subscribo\Omnipay\Shared\Interfaces;

/**
 * Interface RestfulResponseInterface
 *
 * @package Subscribo\OmnipaySubscriboShared
 */
interface RestfulResponseInterface
{
    /**
     * @return int|null
     */
    public function getHttpResponseStatusCode();
}
