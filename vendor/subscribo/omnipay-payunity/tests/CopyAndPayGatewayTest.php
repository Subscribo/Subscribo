<?php

use Omnipay\Omnipay;
use Omnipay\Tests\GatewayTestCase;

use Omnipay\PayUnity\COPYandPAYGateway;

class CopyAndPayGatewayTest extends GatewayTestCase
{
    public function testAcquireToken()
    {
        /** @var COPYandPAYGateway $gateway */
        $gateway = Omnipay::create('PayUnity\\COPYandPAY');
        $gateway->setTestMode(true);
        $this->assertTrue($gateway->getTestMode());
        $gateway->acquireToken()->send();
    }

    public function testGetName()
    {
        /** @var COPYandPAYGateway $gateway */
        $gateway = Omnipay::create('PayUnity\\COPYandPAY');
        $gateway->setTestMode(true);
        $this->assertTrue($gateway->getTestMode());
        $this->assertSame('PayUnity', $gateway->getName());
    }




}
