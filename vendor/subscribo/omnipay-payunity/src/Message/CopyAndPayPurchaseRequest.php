<?php

namespace Omnipay\PayUnity\Message;

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
        $transactionMode = $this->getTransactionMode() ?: $this->chooseTransactionMode();
        $paymentType = 'DB';
        $transactionId = $this->getIdentificationTransactionId();
        $shopperId = $this->getIdentificationShopperId();
        $invoiceId = $this->getIdentificationInvoiceId();
        $bulkId = $this->getIdentificationBulkId();
        $usage = $this->getPresentationUsage();
        $paymentMemo = $this->getPaymentMemo();
        $this->validate('securitySender', 'transactionChannel', 'userLogin', 'userPwd', 'amount');
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
        return $result;
    }

    protected function chooseTransactionMode()
    {
        return $this->getTestMode() ? 'INTEGRATOR_TEST' : 'LIVE';
    }
}
