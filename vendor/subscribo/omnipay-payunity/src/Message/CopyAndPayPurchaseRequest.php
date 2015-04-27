<?php

namespace Omnipay\PayUnity\Message;

use Subscribo\Omnipay\Shared\CreditCard;
use Omnipay\PayUnity\Message\AbstractRequest;
use Omnipay\PayUnity\Message\CopyAndPayPurchaseResponse;

class CopyAndPayPurchaseRequest extends AbstractRequest
{
    protected $liveEndpointUrl = 'https://ctpe.net/frontend/GenerateToken';

    protected $testEndpointUrl = 'https://test.ctpe.net/frontend/GenerateToken';

    /**
     * @return null|string|array
     */
    public function getBrands()
    {
        return $this->getParameter('brands');
    }

    /**
     * @param string|array $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setBrands($value)
    {
        return $this->setParameter('brands', $value);
    }

    protected function getEndpointUrl()
    {
        return $this->getTestMode() ? $this->testEndpointUrl : $this->liveEndpointUrl;
    }

    protected function createResponse($data)
    {
        return new CopyAndPayPurchaseResponse($this, $data);
    }

    public function getData()
    {
        $this->validate('securitySender', 'transactionChannel', 'userLogin', 'userPwd', 'amount');
        $transactionMode = $this->getTransactionMode() ?: $this->chooseTransactionMode();
        $paymentType = 'DB';
        $transactionId = $this->getTransactionId();
        $shopperId = $this->getIdentificationShopperId();
        $invoiceId = $this->getIdentificationInvoiceId();
        $bulkId = $this->getIdentificationBulkId();
        $usage = $this->getPresentationUsage();
        $paymentMemo = $this->getPaymentMemo();
        $clientIp = $this->getClientIp();
        $card = $this->getCard();
        $result = [
            'SECURITY.SENDER' => $this->getSecuritySender(),
            'TRANSACTION.CHANNEL' => $this->getTransactionChannel(),
            'TRANSACTION.MODE' => $transactionMode,
            'USER.LOGIN'  => $this->getUserLogin(),
            'USER.PWD'   => $this->getUserPwd(),
            'PAYMENT.TYPE' => $paymentType,
            'PRESENTATION.AMOUNT' => $this->getAmount(),
            'PRESENTATION.CURRENCY' => $this->getCurrency(),
            'PRESENTATION.USAGE' => 'Some usage',
        ];
        if ($transactionId) {
            $result['IDENTIFICATION.TRANSACTIONID'] = $transactionId;
        }
        if ($shopperId) {
            $result['IDENTIFICATION.SHOPPERID'] = $shopperId;
        }
        if ($invoiceId) {
            $result['IDENTIFICATION.INVOICEID'] = $invoiceId;
        }
        if ($bulkId) {
            $result['IDENTIFICATION.BULKID'] = $bulkId;
        }
        if ($usage) {
            $result['PRESENTATION.USAGE'] = $usage;
        }
        if ($paymentMemo) {
            $result['PAYMENT.MEMO'] = $paymentMemo;
        }
        if ($clientIp) {
            $result['CONTACT.IP'] = $clientIp;
        }
        if ($card) {
            $result = $this->addDataFromCard($card, $result);
        }
        return $result;
    }

    protected function chooseTransactionMode()
    {
        return $this->getTestMode() ? 'INTEGRATOR_TEST' : 'LIVE';
    }

    protected function addDataFromCard(CreditCard $card, array $data)
    {
        if ($card->getFirstName()) {
            $data['NAME.GIVEN'] = $card->getFirstName();
        }
        if ($card->getLastName()) {
            $data['NAME.FAMILY'] = $card->getLastName();
        }
        if ($card->getSalutation()) {
            $data['NAME.SALUTATION'] = $card->getSalutation();
        }
        if ($card->getTitle()) {
            $data['NAME.TITLE'] = $card->getTitle();
        }
        if ($card->getGender()) {
            $data['NAME.SEX'] = $card->getGender();
        }
        if ($card->getBirthday()) {
            $data['NAME.BIRTHDATE'] = $card->getBirthday();
        }
        if ($card->getCompany()) {
            $data['NAME.COMPANY'] = $card->getCompany();
        }
        if ($card->getCountry()) {
            $data['ADDRESS.COUNTRY'] = $card->getCountry();
        }
        if ($card->getState()) {
            $data['ADDRESS.STATE'] = $card->getState();
        }
        if ($card->getCity()) {
            $data['ADDRESS.CITY'] = $card->getCity();
        }
        if ($card->getPostcode()) {
            $data['ADDRESS.ZIP'] = $card->getPostcode();
        }
        $street = '';
        if ($card->getAddress1()) {
            $street = $card->getAddress1();
        }
        if ($card->getAddress2()) {
            $street .= "\n".$card->getAddress2();
        }
        $street = trim($street);
        if ($street) {
            $data['ADDRESS.STREET'] = $street;
        }
        if ($card->getEmail()) {
            $data['CONTACT.EMAIL'] = $card->getEmail();
        }
        if ($card->getPhone()) {
            $data['CONTACT.PHONE'] = $card->getPhone();
        }
        if ($card->getMobile()) {
            $data['CONTACT.MOBILE'] = $card->getMobile();
        }
        if ($card->getIdentificationDocumentType() and $card->getIdentificationDocumentNumber()) {
            $data['CUSTOMER.IDENTIFICATION.PAPER'] = $card->getIdentificationDocumentType();
            $data['CUSTOMER.IDENTIFICATION.VALUE'] = $card->getIdentificationDocumentNumber();
        }
        return $data;
    }
}
