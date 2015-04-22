<?php

namespace Omnipay\PayUnity\Message;

use InvalidArgumentException;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\PayUnity\Message\AbstractRequest;
use Omnipay\PayUnity\Message\CopyAndPayPurchaseResponse;
use Omnipay\PayUnity\Message\CopyAndPayCompletePurchaseResponse;
use Subscribo\PsrHttpTools\Factories\RequestFactory;
use Subscribo\PsrHttpTools\Parsers\ResponseParser;


class CopyAndPayCompletePurchaseRequest extends AbstractRequest
{
    protected $liveEndpointUrl = 'https://ctpe.net/frontend/GetStatus';

    protected $testEndpointUrl = 'https://test.ctpe.net/frontend/GetStatus';


    public function setTransactionToken($value)
    {
        return $this->setParameter('transactionToken', $value);
    }

    public function getTransactionToken()
    {
        return $this->getParameter('transactionToken');
    }

    public function fill(CopyAndPayPurchaseResponse $purchaseResponse)
    {
        return $this->setTransactionToken($purchaseResponse->getTransactionToken());
    }

    protected function getEndpointUrl()
    {
        return $this->getTestMode() ? $this->testEndpointUrl : $this->liveEndpointUrl;
    }

    protected function createResponse($data)
    {
        return new CopyAndPayCompletePurchaseResponse($this, $data);
    }

    public function getData()
    {
        $transactionToken = $this->getTransactionToken();
        if (empty($transactionToken)) {
            $transactionToken = $this->httpRequest->query->get('token');
        }
        if (empty($transactionToken)) {
            throw new InvalidRequestException('Token has not been provided as parameter, neither found in httpRequest');
        }
        return ['transactionToken' => $transactionToken];
    }

    public function sendData($data)
    {
        if (( ! is_array($data)) or empty($data['transactionToken'])) {
            throw new InvalidArgumentException('Provided data should be an array containing transactionToken key');
        }
        $uriSuffix = ';jsessionid='.urlencode($data['transactionToken']);
        $url = $this->getEndpointUrl().$uriSuffix;
        $request = RequestFactory::make($url);
        $response = $this->sendHttpMessage($request, true);
        $responseData = ResponseParser::extractDataFromResponse($response);
        $this->response = $this->createResponse($responseData);
        return $this->response;
    }
}
