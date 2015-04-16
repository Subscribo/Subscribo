<?php namespace Omnipay\PayUnity\Message;

use Subscribo\Omnipay\Shared\Message\AbstractRequest as Base;
use Subscribo\PsrHttpTools\Factories\RequestFactory;
use Subscribo\PsrHttpTools\Parsers\ResponseParser;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Message\ResponseInterface;

/**
 * Abstract Class AbstractRequest
 *
 * @package Omnipay\PayUnity
 */
abstract class AbstractRequest extends Base
{
    abstract protected function getEndpointUrl();

    /**
     * @param RequestInterface $omnipayRequest
     * @param $data
     * @return ResponseInterface
     */
    abstract protected function makeOmnipayResponse(RequestInterface $omnipayRequest, $data);


    public function sendData($data)
    {
        $url = $this->getEndpointUrl();
        $request = RequestFactory::make($url, $data);
        $response = $this->sendHttpMessage($request, true);
        $responseData = ResponseParser::extractDataFromResponse($response);
        $this->response = $this->makeOmnipayResponse($this, $responseData);
        return $this->response;
    }
}
