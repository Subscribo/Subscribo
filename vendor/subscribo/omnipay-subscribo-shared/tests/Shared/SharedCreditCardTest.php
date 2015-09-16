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

    public function testGetShippingContactDifferences()
    {
        $card =  new CreditCard;
        $card->setName('John Tester');
        $card->setShippingName(null);
        $card->setMobile('+44 7700 900 222');
        $card->setShippingMobile(0);
        $card->setBillingCity('Wien');
        $card->setShippingCity('Berlin');
        $card->setShippingCountry('de');
        $card->setBillingCountry('at');
        $card->setBillingAddress1('Main street');
        $card->setShippingAddress2('Main street');
        $expectedDifferenceWithoutEmptyAsString = [
            'address2' => 'Main street',
            'city' => 'Berlin',
            'country' => 'de',
            'mobile' => 0,
        ];
        $this->assertSame($expectedDifferenceWithoutEmptyAsString, $card->getShippingContactDifferences());
        $expectedDifferenceWithEmpty = [
            'firstName' => '',
            'lastName' => null,
            'address1' => null,
            'address2' => 'Main street',
            'city' => 'Berlin',
            'country' => 'de',
            'mobile' => 0,
        ];
        $this->assertSame($expectedDifferenceWithEmpty, $card->getShippingContactDifferences(false));
        $expectedDifferenceWithoutEmpty = [
            'address2' => 'Main street',
            'city' => 'Berlin',
            'country' => 'de',
        ];
        $this->assertSame($expectedDifferenceWithoutEmpty, $card->getShippingContactDifferences($card::MODE_FILTER_EMPTY_VALUES));
        $card2 = new CreditCard();
        $card2->setBillingName('Peter Tester')
            ->setBillingTitle('Dr')
            ->setBillingSalutation('Mr.')
            ->setBillingMobile('+44 7700 900 222')
            ->setBillingPhone('+44 1632 960 111')
            ->setBillingFax('+44 1632 960 110')
            ->setBillingAddress1('Other Street 1/1')
            ->setBillingAddress2('Close to park')
            ->setBillingCity('Wien')
            ->setBillingPostCode('1000')
            ->setBillingState('Wien')
            ->setBillingCountry('AT')
            ->setBillingCompany('Very Limited Ltd.');
        $this->assertSame([], $card2->getShippingContactDifferences($card2::MODE_FILTER_EMPTY_WHEN_STRING_VALUES));
        $this->assertSame([], $card2->getShippingContactDifferences($card2::MODE_FILTER_EMPTY_VALUES));
        $expected2 = [
            'title' => null,
            'firstName' => null,
            'lastName' => null,
            'company' => null,
            'address1' => null,
            'address2' => null,
            'city' => null,
            'postcode' => null,
            'state' => null,
            'country' => null,
            'phone' => null,
            'fax' => null,
            'mobile' => null,
            'salutation' => null,
        ];
        $this->assertSame($expected2, $card2->getShippingContactDifferences(''));
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
        $this->assertNull($card->getNationalIdentificationNumber());
        $value = uniqid();
        $this->assertSame($card, $card->setSocialSecurityNumber($value));
        $this->assertSame($value, $card->getSocialSecurityNumber());
        $this->assertSame($value, $card->getNationalIdentificationNumber());
        $this->assertSame($card, $card->setSocialSecurityNumber(null));
        $this->assertNull($card->getSocialSecurityNumber());
        $this->assertNull($card->getNationalIdentificationNumber());
    }

    public function testSetNationalIdentificationNumber()
    {
        $card = new CreditCard();
        $this->assertNull($card->getNationalIdentificationNumber());
        $this->assertNull($card->getSocialSecurityNumber());
        $value = uniqid();
        $this->assertSame($card, $card->setNationalIdentificationNumber($value));
        $this->assertSame($value, $card->getNationalIdentificationNumber());
        $this->assertSame($value, $card->getSocialSecurityNumber());
        $this->assertSame($card, $card->setNationalIdentificationNumber(null));
        $this->assertNull($card->getNationalIdentificationNumber());
        $this->assertNull($card->getSocialSecurityNumber());
    }
}
