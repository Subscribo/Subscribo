<?php

namespace Omnipay\Klarna;

use Omnipay\Tests\GatewayTestCase;

use Omnipay\Klarna\InvoiceGateway;

class InvoiceGatewayOnlineTest extends GatewayTestCase
{
    public function setUp()
    {
        $this->gateway = new InvoiceGateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setTestMode(true)
            ->setLocale('de_at');

        $this->gateway->setMerchantId(4264);
        $this->gateway->setSharedSecret('s7DVao7g5ylYVvi');

        $this->card = $card = [
            'gender' => 'Male',
            'birthday' => '1960-04-14',
            'firstName' => 'Testperson-at'
        ];
    }

    public function testAuthorize()
    {
        $data = [
            'card' => $this->card,
            'amount' => '1.02',
        ];
        $request = $this->gateway->authorize($data);
        $this->assertInstanceOf('\\Omnipay\\Klarna\\Message\\InvoiceAuthorizeRequest', $request);
        $response = $request->send();
        $this->assertInstanceOf('\\Omnipay\\Klarna\\Message\\InvoiceAuthorizeResponse', $response);
    }

    public function testPurchaseParameters()
    {
        if ($this->gateway->supportsPurchase()) {
            parent::testPurchaseParameters();
        }
    }
}
