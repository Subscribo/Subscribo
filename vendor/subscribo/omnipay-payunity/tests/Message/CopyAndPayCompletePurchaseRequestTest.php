<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\Tests\TestCase;
use Omnipay\PayUnity\Message\CopyAndPayCompletePurchaseRequest;
use Omnipay\PayUnity\Message\CopyAndPayPurchaseResponse;

class CopyAndPayCompletePurchaseRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new CopyAndPayCompletePurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->setTestMode(true);
        $this->purchaseResponse = new CopyAndPayPurchaseResponse($this->getMockRequest(),
            ['transaction' => ['token' => '33E47BC8E286B472A1299EAC39F4556D.sbg-vm-fe01']]
        );
    }

    public function testGetData()
    {
        $this->request->fill($this->purchaseResponse);
        $data = $this->request->getData();
        $this->assertSame('33E47BC8E286B472A1299EAC39F4556D.sbg-vm-fe01', $data['transactionToken']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSendingNonArrayData()
    {
        $this->request->sendData(null);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSendingEmptyData()
    {
        $this->request->sendData([]);
    }
}
