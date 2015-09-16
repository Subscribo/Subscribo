<?php

namespace Subscribo\Omnipay\Shared\Message;

use Omnipay\Common\Message\RequestInterface;
use Subscribo\Omnipay\Shared\Message\AbstractResponse;
use Subscribo\Omnipay\Shared\Interfaces\RestfulResponseInterface;

/**
 * Abstract class AbstractRestfulResponse
 *
 * @package Subscribo\OmnipaySubscriboShared
 */
abstract class AbstractRestfulResponse extends AbstractResponse implements RestfulResponseInterface
{
    /**
     * @var int|null
     */
    protected $httpResponseStatusCode;

    /**
     * @param RequestInterface $request
     * @param mixed $data
     * @param null|int $httpResponseStatusCode
     */
    public function __construct(RequestInterface $request, $data, $httpResponseStatusCode = null)
    {
        parent::__construct($request, $data);

        $this->httpResponseStatusCode = $httpResponseStatusCode;
    }

    /**
     * @return int|null
     */
    public function getHttpResponseStatusCode()
    {
        return $this->httpResponseStatusCode;
    }
}
