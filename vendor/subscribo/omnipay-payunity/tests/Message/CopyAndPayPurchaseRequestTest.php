<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\Tests\TestCase;
use Omnipay\PayUnity\Message\CopyAndPayPurchaseRequest;

class CopyAndPayPurchaseRequestTest extends TestCase
{
    public function setUp()
    {
        $this->options = [
            'amount' => '12.35',
            'currency' => 'EUR',
            'returnUrl' => 'https://localhost/redirect/url',
            'clientIp' => '127.0.0.1',
            'brands' => 'VISA MAESTRO MASTER',
            'testMode' => true,
            'securitySender' => 'ff80808144d46be50144d4a6f6ce007f',
            'transactionChannel' => 'ff80808144d46be50144d4a732ae0083',
            'userLogin' => 'ff80808144d46be50144d4a6f6cf0081',
            'userPwd' => 'M5Ynx692',
        ];
        $this->card = $this->getValidCard();
        $this->card['email'] = 'email@example.com';
        $this->card['title'] = 'DR';
        $this->card['salutation'] = 'MR';
        $this->card['gender'] = 'M';
        $this->card['birthday'] = '1974-05-20';
        $this->card['company'] = 'Company Name Inc.';
        $this->card['billingPhone'] = '(+1) 02 345 678';
        $this->card['billingMobile'] = '+123-456-789';
        $this->card['firstName'] = 'John';
        $this->card['lastName'] = 'Tester';
        $this->card['billingAddress1'] = 'Main Street 1';
        $this->card['billingAddress2'] = 'Centre';
        $this->card['billingCity'] = 'New City';
        $this->card['billingPostcode'] = 'AB1 23C';
        $this->card['billingState'] = 'AT12';
        $this->card['billingCountry'] = 'AT';
        $this->card['identificationDocumentType'] = 'PASSPORT';
        $this->card['identificationDocumentNumber'] = 'AB123 456 C7';
        $options = $this->options;
        $options['card'] = $this->card;
        $this->request = new CopyAndPayPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize($options);

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
        $this->assertSame('MR', $data['NAME.SALUTATION']);
        $this->assertSame('DR', $data['NAME.TITLE']);
        $this->assertSame('John', $data['NAME.GIVEN']);
        $this->assertSame('Tester', $data['NAME.FAMILY']);
        $this->assertSame('M', $data['NAME.SEX']);
        $this->assertSame('1974-05-20', $data['NAME.BIRTHDATE']);
        $this->assertSame('Company Name Inc.', $data['NAME.COMPANY']);
        $this->assertStringStartsWith('Main Street 1', $data['ADDRESS.STREET']);
        $this->assertStringEndsWith('Centre', $data['ADDRESS.STREET']);
        $this->assertSame("Main Street 1\nCentre", $data['ADDRESS.STREET']);
        $this->assertSame('New City', $data['ADDRESS.CITY']);
        $this->assertSame('AB1 23C', $data['ADDRESS.ZIP']);
        $this->assertSame('AT12', $data['ADDRESS.STATE']);
        $this->assertSame('AT', $data['ADDRESS.COUNTRY']);
        $this->assertSame('email@example.com', $data['CONTACT.EMAIL']);
        $this->assertSame('(+1) 02 345 678', $data['CONTACT.PHONE']);
        $this->assertSame('+123-456-789', $data['CONTACT.MOBILE']);
        $this->assertSame('127.0.0.1', $data['CONTACT.IP']);
        $this->assertSame('PASSPORT', $data['CUSTOMER.IDENTIFICATION.PAPER']);
        $this->assertSame('AB123 456 C7', $data['CUSTOMER.IDENTIFICATION.VALUE']);
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

    public function testSetPaymentMemo()
    {
        $request = new CopyAndPayPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->assertNull($request->getPaymentMemo());
        $value = uniqid();
        $this->assertSame($request, $request->setPaymentMemo($value));
        $this->assertSame($value, $request->getPaymentMemo());
    }
}
