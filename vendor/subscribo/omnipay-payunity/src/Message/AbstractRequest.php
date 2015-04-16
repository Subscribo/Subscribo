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


    public function sendData($data)
    {
        $url = $this->getEndpointUrl();
        $request = RequestFactory::make($url, $data);
        $response = $this->sendHttpMessage($request, true);
        $responseData = ResponseParser::extractDataFromResponse($response);
        $this->response = $this->makeOmnipayResponse($responseData);
        return $this->response;
    }
}
