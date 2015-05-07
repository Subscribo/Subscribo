<?php

namespace Omnipay\Klarna;

use Omnipay\Tests\GatewayTestCase;

use Omnipay\Klarna\InvoiceGateway;

class InvoiceGatewayOnlineTest extends GatewayTestCase
{
    public function setUp()
    {
        $this->merchantId = getenv('KLARNA_MERCHANT_ID');
        $this->sharedSecret = getenv('KLARNA_SHARED_SECRET');
        $this->gateway = new InvoiceGateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setTestMode(true)
            ->setLocale('de_at');

        $this->gateway->setMerchantId($this->merchantId);
        $this->gateway->setSharedSecret($this->sharedSecret);

        $this->card = $card = [
            'gender' => 'Male',
            'birthday' => '1960-04-14',
            'firstName' => 'Testperson-at',
            'lastName' => 'Approved',
            'address1' => 'Klarna-Straße 1/2/3',
            'address2' => '',
            'postCode' => '8071',
            'city'     => 'Hausmannstätten',
            'country'  => 'at',
            'phone'    => '0676 2600000',
            'email'    => 'youremail@email.com',
        ];
        $this->shoppingCart = [
            [
                'name' => 'Some Article',
                'identifier' => 'A001',
                'price' => '10.00',
                'description' => 'Just article for testing',
                'quantity' => 2,
                'discountPercent' => '10',
                'taxPercent' => '20',
                'flags' => 5,
            ],
            [
                'name' => 'Another Article',
                'identifier' => 'A002',
                'price' => '10.00',
                'quantity' => 1,
                'description' => 'Another article for testing',
            ]
        ];
    }

    public function testAuthorize()
    {
        $data = [
            'card' => $this->card,
        ];
        $request = $this->gateway->authorize($data);
        $this->assertInstanceOf('\\Omnipay\\Klarna\\Message\\InvoiceAuthorizeRequest', $request);
        $request->setItems($this->shoppingCart);

        if (empty($this->sharedSecret)) {
            $this->markTestSkipped('API credentials not provided, online test skipped.');
        }

        $response = $request->send();
        $this->assertInstanceOf('\\Omnipay\\Klarna\\Message\\InvoiceAuthorizeResponse', $response);
        $this->assertTrue($response->isSuccessful());
        $reservationNumber = $response->getReservationNumber();
        $this->assertNotEmpty($reservationNumber);
        return $reservationNumber;
    }

    public function testPurchaseParameters()
    {
        if ($this->gateway->supportsPurchase()) {
            parent::testPurchaseParameters();
        }
    }
}
