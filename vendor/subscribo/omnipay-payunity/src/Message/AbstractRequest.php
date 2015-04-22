<?php namespace Omnipay\PayUnity\Message;

use Subscribo\Omnipay\Shared\Message\AbstractRequest as Base;
use Subscribo\PsrHttpTools\Factories\RequestFactory;
use Subscribo\PsrHttpTools\Parsers\ResponseParser;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\PayUnity\Traits\DefaultGatewayParametersGettersAndSettersTrait;


/**
 * Abstract Class AbstractRequest
 *
 * @package Omnipay\PayUnity
 */
abstract class AbstractRequest extends Base
{
    use DefaultGatewayParametersGettersAndSettersTrait;

    abstract protected function getEndpointUrl();

    /**
     * @param $data
     * @return ResponseInterface
     */
    abstract protected function createResponse($data);

    /**
     * @return string
     */
    public function getPresentationUsage()
    {
        return $this->getParameter('presentationUsage');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setPresentationUsage($value)
    {
        return $this->setParameter('presentationUsage', $value);
    }

    /**
     * @return string
     */
    public function getPaymentMemo()
    {
        return $this->getParameter('paymentMemo');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setPaymentMemo($value)
    {
        return $this->setParameter('paymentMemo', $value);
    }


    /**
     * @param mixed $data
     * @return ResponseInterface
     */
    public function sendData($data)
    {
        $url = $this->getEndpointUrl();
        $request = RequestFactory::make($url, $data);
        $response = $this->sendHttpMessage($request, true);
        $responseData = ResponseParser::extractDataFromResponse($response);
        $this->response = $this->createResponse($responseData);
        return $this->response;
    }
}
