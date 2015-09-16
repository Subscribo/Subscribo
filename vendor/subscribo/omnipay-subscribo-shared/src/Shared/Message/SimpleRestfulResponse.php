<?php

namespace Subscribo\Omnipay\Shared\Message;

use Omnipay\Common\Message\RequestInterface;
use Subscribo\Omnipay\Shared\Message\AbstractRestfulResponse;

/**
 * Class SimpleRestfulResponse
 *
 * @package Subscribo\OmnipaySubscriboShared
 */
class SimpleRestfulResponse extends AbstractRestfulResponse
{
    /**
     * @return bool
     */
    public function isSuccessful()
    {
        $statusCode = intval($this->getHttpResponseStatusCode());

        return (($statusCode >= 200) and ($statusCode < 300));
    }
}
