<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\Tests\TestCase;
use Omnipay\PayUnity\Message\CopyAndPayPurchaseRequest;
use Omnipay\PayUnity\Message\CopyAndPayPurchaseResponse;

class CopyAndPayPurchaseResponseTest extends TestCase
{
    public function setUp()
    {
        $this->request = new CopyAndPayPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize([
            'amount' => '12.35',
            'currency' => 'EUR',
        ]);
        $this->request->setTestMode(true);
    }

    public function testTransactionToken()
    {
        $response = new CopyAndPayPurchaseResponse(
            $this->request,
            [
                'transaction' => [
                    'token' => 'A550D17DC663DFA8973CCAB8A117669A.sbg-vm-fe01',
            ],
        ]);
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isWaiting());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());
        $this->assertFalse($response->isCancelled());
        $this->assertTrue($response->isTransactionToken());
        $this->assertTrue($response->haveWidget());
        $this->assertNotEmpty($response->getWidget('de', 'plain', false, ['VISA'], 'https://localhost/redirect/url'));
    }

    public function testEmptyTransactionToken()
    {
        $response = new CopyAndPayPurchaseResponse($this->request, []);
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isWaiting());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isTransparentRedirect());
        $this->assertFalse($response->isCancelled());
        $this->assertFalse($response->isTransactionToken());
        $this->assertFalse($response->haveWidget());
        $this->assertNull($response->getWidgetJavascript('de', 'plain', false));
        $this->assertNull($response->getWidgetForm());
        $this->assertNull($response->getWidget());
    }
}
