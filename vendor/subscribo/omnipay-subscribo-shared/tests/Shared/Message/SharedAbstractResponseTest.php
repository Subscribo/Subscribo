<?php

namespace Subscribo\Omnipay\Shared\Message;

use Omnipay\Tests\TestCase;

class SharedAbstractResponseTest extends TestCase
{
    public function setUp()
    {
        $this->response = $this->getMockForAbstractClass('Subscribo\\Omnipay\\Shared\\Message\\AbstractResponse', [$this->getMockRequest(), []]);
    }

    public function testIsTransactionToken()
    {
        $this->assertFalse($this->response->isTransactionToken());
    }

    public function testGetTransactionToken()
    {
        $this->assertNull($this->response->getTransactionToken());
    }

    public function testIsWaiting()
    {
        $this->assertFalse($this->response->isWaiting());
    }

    public function testHaveWidget()
    {
        $this->assertFalse($this->response->haveWidget());
    }

    public function testGetWidget()
    {
        $this->assertNull($this->response->getWidget());
    }
}
