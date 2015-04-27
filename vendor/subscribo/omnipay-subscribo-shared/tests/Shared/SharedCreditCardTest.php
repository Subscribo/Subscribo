<?php

namespace Subscribo\Omnipay\Shared;

require_once __DIR__ . "/../../vendor/omnipay/common/tests/Omnipay/Common/CreditCardTest.php";

use Omnipay\Common\CreditCardTest;

/**
 * Class SharedCreditCardTest Testing Subscribo\Omnipay\Shared\CreditCard
 *
 * @package Subscribo\OmnipaySubscriboShared
 */
class SharedCreditCardTest extends CreditCardTest
{
    public function setUp()
    {
        $this->card = new CreditCard([
            'number' => '4111111111111111',
            'expiryMonth' => '4',
            'expiryYear' => date('Y')+2,
        ]);
    }

    public function testSetBillingMobile()
    {
        $card = new CreditCard();
        $this->assertEmpty($card->getMobile());
        $this->assertEmpty($card->getBillingMobile());
        $this->assertEmpty($card->getShippingMobile());
        $value = uniqid();
        $this->assertSame($card, $card->setBillingMobile($value));
        $this->assertSame($value, $card->getMobile());
        $this->assertEmpty($card->getShippingMobile());
        $this->assertSame($value, $card->getBillingMobile());
        $this->assertSame($card, $card->setBillingMobile(null));
        $this->assertEmpty($card->getMobile());
        $this->assertEmpty($card->getBillingMobile());
        $this->assertEmpty($card->getShippingMobile());
    }

    public function testSetShippingMobile()
    {
        $card = new CreditCard();
        $this->assertEmpty($card->getMobile());
        $this->assertEmpty($card->getBillingMobile());
        $this->assertEmpty($card->getShippingMobile());
        $value = uniqid();
        $this->assertSame($card, $card->setShippingMobile($value));
        $this->assertEmpty($card->getMobile());
        $this->assertEmpty($card->getBillingMobile());
        $this->assertSame($value, $card->getShippingMobile());
        $this->assertSame($card, $card->setShippingMobile(null));
        $this->assertEmpty($card->getMobile());
        $this->assertEmpty($card->getBillingMobile());
        $this->assertEmpty($card->getShippingMobile());
    }

    public function testSetMobile()
    {
        $card = new CreditCard();
        $this->assertEmpty($card->getMobile());
        $this->assertEmpty($card->getBillingMobile());
        $this->assertEmpty($card->getShippingMobile());
        $value = uniqid();
        $this->assertSame($card, $card->setMobile($value));
        $this->assertSame($value, $card->getMobile());
        $this->assertSame($value, $card->getBillingMobile());
        $this->assertSame($value, $card->getShippingMobile());
        $this->assertSame($card, $card->setMobile(null));
        $this->assertEmpty($card->getMobile());
        $this->assertEmpty($card->getBillingMobile());
        $this->assertEmpty($card->getShippingMobile());
    }

    public function testSetBillingSalutation()
    {
        $card = new CreditCard();
        $this->assertEmpty($card->getSalutation());
        $this->assertEmpty($card->getBillingSalutation());
        $this->assertEmpty($card->getShippingSalutation());
        $value = uniqid();
        $this->assertSame($card, $card->setBillingSalutation($value));
        $this->assertSame($value, $card->getSalutation());
        $this->assertEmpty($card->getShippingSalutation());
        $this->assertSame($value, $card->getBillingSalutation());
        $this->assertSame($card, $card->setBillingSalutation(null));
        $this->assertEmpty($card->getSalutation());
        $this->assertEmpty($card->getBillingSalutation());
        $this->assertEmpty($card->getShippingSalutation());
    }

    public function testSetShippingSalutation()
    {
        $card = new CreditCard();
        $this->assertEmpty($card->getSalutation());
        $this->assertEmpty($card->getBillingSalutation());
        $this->assertEmpty($card->getShippingSalutation());
        $value = uniqid();
        $this->assertSame($card, $card->setShippingSalutation($value));
        $this->assertEmpty($card->getSalutation());
        $this->assertEmpty($card->getBillingSalutation());
        $this->assertSame($value, $card->getShippingSalutation());
        $this->assertSame($card, $card->setShippingSalutation(null));
        $this->assertEmpty($card->getSalutation());
        $this->assertEmpty($card->getBillingSalutation());
        $this->assertEmpty($card->getShippingSalutation());
    }

    public function testSetSalutation()
    {
        $card = new CreditCard();
        $this->assertEmpty($card->getSalutation());
        $this->assertEmpty($card->getBillingSalutation());
        $this->assertEmpty($card->getShippingSalutation());
        $value = uniqid();
        $this->assertSame($card, $card->setSalutation($value));
        $this->assertSame($value, $card->getSalutation());
        $this->assertSame($value, $card->getBillingSalutation());
        $this->assertSame($value, $card->getShippingSalutation());
        $this->assertSame($card, $card->setSalutation(null));
        $this->assertEmpty($card->getSalutation());
        $this->assertEmpty($card->getBillingSalutation());
        $this->assertEmpty($card->getShippingSalutation());
    }

    public function testSetIdentificationDocumentNumber()
    {
        $card = new CreditCard();
        $this->assertEmpty($card->getIdentificationDocumentNumber());
        $value = uniqid();
        $this->assertSame($card, $card->setIdentificationDocumentNumber($value));
        $this->assertSame($value, $card->getIdentificationDocumentNumber());
        $this->assertSame($card, $card->setIdentificationDocumentNumber(''));
        $this->assertEmpty($card->getIdentificationDocumentNumber());
    }

    public function testSetIdentificationDocumentType()
    {
        $card = new CreditCard();
        $this->assertEmpty($card->getIdentificationDocumentType());
        $value = uniqid();
        $this->assertSame($card, $card->setIdentificationDocumentType($value));
        $this->assertSame($value, $card->getIdentificationDocumentType());
        $this->assertSame($card, $card->setIdentificationDocumentType(null));
        $this->assertEmpty($card->getIdentificationDocumentType());
    }

    public function testSetSocialSecurityNumber()
    {
        $card = new CreditCard();
        $this->assertEmpty($card->getSocialSecurityNumber());
        $value = uniqid();
        $this->assertSame($card, $card->setSocialSecurityNumber($value));
        $this->assertSame($value, $card->getSocialSecurityNumber());
        $this->assertSame($card, $card->setSocialSecurityNumber(null));
        $this->assertNull($card->getSocialSecurityNumber());
    }
}
