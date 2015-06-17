<?php

namespace Omnipay\Klarna\Message;

use Omnipay\Tests\TestCase;
use Omnipay\Klarna\Message\CheckoutAuthorizeResponse;
use Omnipay\Klarna\Message\CheckoutAuthorizeRequest;

class CheckoutAuthorizeResponseTest extends TestCase
{
    public function setUp()
    {
        $this->request = new CheckoutAuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->setTestMode(true);
    }


    public function testEmptyResponse()
    {
        $response = new CheckoutAuthorizeResponse($this->request, []);
        $this->assertNull($response->getCode());
        $this->assertNull($response->getMessage());
        $this->assertNull($response->getWidget());
        $this->assertNull($response->getTransactionToken());
        $this->assertNull($response->getTransactionReference());
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isWaiting());
        $this->assertFalse($response->isTransactionToken());
        $this->assertFalse($response->isCancelled());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());
        $this->assertFalse($response->haveWidget());
        $this->assertSame([], $response->getData());
    }
}
