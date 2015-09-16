<?php

namespace Subscribo\Omnipay\Shared;

use Omnipay\Tests\GatewayTestCase;
use Subscribo\Omnipay\Shared\AbstractGateway;


class SharedAbstractGatewayTest extends GatewayTestCase
{
    protected $testedClassName = 'Subscribo\\Omnipay\\Shared\\AbstractGateway';

    public function setUp()
    {
        $this->gateway = new ExtendedAbstractGatewayForTesting($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testAttachPsrLogger()
    {
        $logger = $this->getMock('Psr\\Log\\LoggerInterface');
        $this->assertTrue($this->gateway->attachPsrLogger($logger));
        $this->assertNull($this->gateway->attachPsrLogger($logger));
        $this->assertNull($this->gateway->attachPsrLogger($logger));
    }
}

class ExtendedAbstractGatewayForTesting extends AbstractGateway
{
    public function getName()
    {
        return 'Extended Gateway for testing';
    }
}
