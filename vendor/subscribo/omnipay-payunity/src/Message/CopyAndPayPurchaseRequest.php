<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\PayUnity\Message\AbstractRequest;
use Omnipay\PayUnity\Message\CopyAndPayPurchaseResponse;

class CopyAndPayPurchaseRequest extends AbstractRequest
{
    protected $liveEndpointUrl = 'https://ctpe.net/frontend/GenerateToken';

    protected $testEndpointUrl = 'https://test.ctpe.net/frontend/GenerateToken';

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
        $this->validate('securitySender', 'transactionChannel', 'transactionMode', 'userLogin', 'userPwd', 'amount');
        $result = [
            'SECURITY.SENDER' => $this->getParameter('securitySender'),
            'TRANSACTION.CHANNEL' => $this->getParameter('transactionChannel'),
            'TRANSACTION.MODE' => $this->getParameter('transactionMode'),
            'USER.LOGIN'  => $this->getParameter('userLogin'),
            'USER.PWD'   => $this->getParameter('userPwd'),
            'PAYMENT.TYPE' => 'DB',
            'PRESENTATION.AMOUNT' => $this->getAmount(),
            'PRESENTATION.CURRENCY' => $this->getCurrency(),
        ];
        return $result;
    }
}
