<?php

namespace Omnipay\PayUnity;

use Omnipay\Tests\GatewayTestCase;
use Omnipay\PayUnity\COPYandPAYGateway;


class CopyAndPayGatewayTest extends GatewayTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->gateway = new COPYandPAYGateway($this->getHttpClient(), $this->getHttpRequest());

        $this->options = array(
            'amount' => '10.00',
            'card' => $this->getValidCard(),
        );
    }

    public function testPurchaseSuccess()
    {
        // card numbers ending in even number should be successful
        $this->options['card']['number'] = '4242424242424242';
        $response = $this->gateway->purchase($this->options)->send();

        $this->assertInstanceOf('\Omnipay\Dummy\Message\Response', $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNotEmpty($response->getTransactionReference());
        $this->assertSame('Success', $response->getMessage());
    }

    public function testPurchaseFailure()
    {
        // card numbers ending in odd number should be declined
        $this->options['card']['number'] = '4111111111111111';
        $response = $this->gateway->purchase($this->options)->send();

        $this->assertInstanceOf('\Omnipay\Dummy\Message\Response', $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNotEmpty($response->getTransactionReference());
        $this->assertSame('Failure', $response->getMessage());
    }
}

