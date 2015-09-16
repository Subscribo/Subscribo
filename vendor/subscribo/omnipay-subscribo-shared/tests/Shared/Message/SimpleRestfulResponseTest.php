<?php

namespace Subscribo\Omnipay\Shared\Message;

use Omnipay\Tests\TestCase;
use Subscribo\Omnipay\Shared\Message\SimpleRestfulResponse;

class SimpleRestfulResponseTest extends TestCase
{
    public function testEmptyResponse()
    {
        $response = new SimpleRestfulResponse($this->getMockRequest(), null);

        $this->assertNull($response->getData());
        $this->assertNull($response->getHttpResponseStatusCode());
        $this->assertNull($response->getTransactionToken());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getCode());
        $this->assertNull($response->getMessage());
        $this->assertNull($response->getWidget());

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isCancelled());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());
        $this->assertFalse($response->isWaiting());
        $this->assertFalse($response->isTransactionToken());
        $this->assertFalse($response->haveWidget());
    }
}
