<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\Tests\TestCase;
use Omnipay\PayUnity\Message\CopyAndPayPurchaseRequest;

class CopyAndPayPurchaseRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new CopyAndPayPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize([
            'amount' => '12.35',
            'currency' => 'EUR',
            'returnUrl' => 'https://localhost/redirect/url',
            'brands' => 'VISA MAESTRO MASTER',
        ]);
        $this->request->setTestMode(true);
        $this->request->setSecuritySender('ff80808144d46be50144d4a6f6ce007f');
        $this->request->setTransactionChannel('ff80808144d46be50144d4a732ae0083');
        $this->request->setUserLogin('ff80808144d46be50144d4a6f6cf0081');
        $this->request->setUserPwd('M5Ynx692');
    }

    public function testGetData()
    {
        $data = $this->request->getData();
        $this->assertTrue($this->request->getTestMode());
        $this->assertSame('ff80808144d46be50144d4a6f6ce007f', $data['SECURITY.SENDER']);
        $this->assertSame('ff80808144d46be50144d4a732ae0083', $data['TRANSACTION.CHANNEL']);
        $this->assertSame('INTEGRATOR_TEST', $data['TRANSACTION.MODE']);
        $this->assertSame('ff80808144d46be50144d4a6f6cf0081', $data['USER.LOGIN']);
        $this->assertSame('M5Ynx692', $data['USER.PWD']);
        $this->assertSame('DB', $data['PAYMENT.TYPE']);
        $this->assertSame('12.35', $data['PRESENTATION.AMOUNT']);
        $this->assertSame('EUR', $data['PRESENTATION.CURRENCY']);
    }

    public function testGetReturnUrl()
    {
        $this->assertSame('https://localhost/redirect/url', $this->request->getReturnUrl());
    }

    public function testGetBrands()
    {
        $this->assertSame('VISA MAESTRO MASTER', $this->request->getBrands());
    }

    public function testSetBrands()
    {
        $request = new CopyAndPayPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->assertNull($request->getBrands());
        $request->setBrands('');
        $this->assertSame('', $request->getBrands());
        $request->setBrands([]);
        $this->assertSame([], $request->getBrands());
        $request->setBrands('VISA');
        $this->assertSame('VISA', $request->getBrands());
        $request->setBrands(['VISA']);
        $this->assertSame(['VISA'], $request->getBrands());
        $request->setBrands('VISA MASTER');
        $this->assertSame('VISA MASTER', $request->getBrands());
        $request->setBrands(['VISA', 'MAESTRO', "MASTER"]);
        $this->assertSame(['VISA', "MAESTRO", 'MASTER'], $request->getBrands());
    }

    public function testSetPresentationUsage()
    {
        $request = new CopyAndPayPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->assertNull($request->getPresentationUsage());
        $value = uniqid();
        $this->assertSame($request, $request->setPresentationUsage($value));
        $this->assertSame($value, $request->getPresentationUsage());
    }

    public function testSetIdentificationTransactionId()
    {
        $request = new CopyAndPayPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->assertNull($request->getIdentificationTransactionId());
        $value = uniqid();
        $this->assertSame($request, $request->setIdentificationTransactionId($value));
        $this->assertSame($value, $request->getIdentificationTransactionId());
    }

    public function testSetPaymentMemo()
    {
        $request = new CopyAndPayPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->assertNull($request->getPaymentMemo());
        $value = uniqid();
        $this->assertSame($request, $request->setPaymentMemo($value));
        $this->assertSame($value, $request->getPaymentMemo());
    }
}
