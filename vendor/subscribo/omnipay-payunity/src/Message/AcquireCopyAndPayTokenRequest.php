<?php namespace Omnipay\PayUnity\Message;

use Omnipay\PayUnity\Message\AbstractRequest;
use Omnipay\PayUnity\Message\AcquireCopyAndPayTokenResponse;
use Omnipay\Common\Message\RequestInterface;



class AcquireCopyAndPayTokenRequest extends AbstractRequest
{
    protected $liveEndpointUrl = 'https://ctpe.net/frontend/GenerateToken';

    protected $testEndpointUrl = 'https://test.ctpe.net/frontend/GenerateToken';

    public function getData()
    {

    }

    protected function getEndpointUrl()
    {
        return $this->getTestMode() ? $this->testEndpointUrl : $this->liveEndpointUrl;
    }

    protected function makeOmnipayResponse(RequestInterface $omnipayRequest, $data)
    {
        return new AcquireCopyAndPayTokenResponse($omnipayRequest, $data);
    }

}
