<?php

namespace Subscribo\Omnipay\Shared\Traits;

use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Tests\TestCase;
use Subscribo\Omnipay\Shared\Traits\HttpMessageSendingTrait;
use Subscribo\PsrHttpMessageTools\Factories\RequestFactory;
use Guzzle\Plugin\Mock\MockPlugin;
use Subscribo\Omnipay\Shared\Traits\SimpleRestfulRequestTrait;
use Subscribo\Omnipay\Shared\Message\SimpleRestfulResponse;


class SimpleRestfulRequestTraitTest extends TestCase
{
    public function setUp()
    {
        $this->request = new ClassUsingSimpleRestfulRequestTraitForTesting(
            $this->getHttpClient(),
            $this->getHttpRequest()
        );
    }


    public function testSendSimpleGet()
    {
        $this->setMockHttpResponse('successResponse.txt');

        $response = $this->request->send();

        $this->assertInstanceOf('Omnipay\\Common\\Message\\ResponseInterface', $response);
        $this->assertInstanceOf('Subscribo\\Omnipay\\Shared\\Interfaces\\RestfulResponseInterface', $response);
        /** @var $response \Omnipay\Common\Message\ResponseInterface */
        $this->assertTrue($response->isSuccessful());
        $this->assertSame(["result" => "OK"], $response->getData());
        $this->assertInstanceOf('Subscribo\\Omnipay\\Shared\\Interfaces\\RestfulResponseInterface', $response);
        /** @var $response \Subscribo\Omnipay\Shared\Interfaces\RestfulResponseInterface */
        $this->assertSame(200, $response->getHttpResponseStatusCode());
    }

    /**
     * @expectedException \Subscribo\Omnipay\Shared\Exception\TransportErrorHttpMessageSendingException
     * @expectedExceptionMessage Mock queue is empty
     */
    public function testSendEmptyMockQueue()
    {
        $this->getHttpClient()->addSubscriber(new MockPlugin([]));

        $this->request->send();
    }


    public function testSendServerErrorReceived()
    {
        $this->setMockHttpResponse('serverErrorResponse.txt');

        $response = $this->request->send();

        $this->assertInstanceOf('Omnipay\\Common\\Message\\ResponseInterface', $response);
        /** @var $response \Omnipay\Common\Message\ResponseInterface */
        $this->assertFalse($response->isSuccessful());
        $this->assertSame([], $response->getData());
        $this->assertInstanceOf('Subscribo\\Omnipay\\Shared\\Interfaces\\RestfulResponseInterface', $response);
        /** @var $response \Subscribo\Omnipay\Shared\Interfaces\RestfulResponseInterface */
        $this->assertSame(500, $response->getHttpResponseStatusCode());
    }


    public function testSendClientErrorReceived()
    {
        $this->setMockHttpResponse('clientErrorResponse.txt');

        $response = $this->request->send();

        $this->assertInstanceOf('Omnipay\\Common\\Message\\ResponseInterface', $response);
        /** @var $response \Omnipay\Common\Message\ResponseInterface */
        $this->assertFalse($response->isSuccessful());
        $this->assertSame([], $response->getData());
        $this->assertInstanceOf('Subscribo\\Omnipay\\Shared\\Interfaces\\RestfulResponseInterface', $response);
        /** @var $response \Subscribo\Omnipay\Shared\Interfaces\RestfulResponseInterface */
        $this->assertSame(404, $response->getHttpResponseStatusCode());
    }


    public function testSendOtherResponse()
    {
        $this->setMockHttpResponse('otherResponse.txt');

        $response = $this->request->send();

        $this->assertInstanceOf('Omnipay\\Common\\Message\\ResponseInterface', $response);
        /** @var $response \Omnipay\Common\Message\ResponseInterface */
        $this->assertFalse($response->isSuccessful());
        $this->assertSame([], $response->getData());
        $this->assertInstanceOf('Subscribo\\Omnipay\\Shared\\Interfaces\\RestfulResponseInterface', $response);
        /** @var $response \Subscribo\Omnipay\Shared\Interfaces\RestfulResponseInterface */
        $this->assertSame(100, $response->getHttpResponseStatusCode());
    }


    public function testSendBadRequestResponse()
    {
        $this->setMockHttpResponse('badRequestResponse.txt');

        $response = $this->request->send();

        $this->assertInstanceOf('Omnipay\\Common\\Message\\ResponseInterface', $response);
        /** @var $response \Omnipay\Common\Message\ResponseInterface */
        $this->assertFalse($response->isSuccessful());
        $this->assertSame(['error' => 'Wrong input'], $response->getData());
        $this->assertInstanceOf('Subscribo\\Omnipay\\Shared\\Interfaces\\RestfulResponseInterface', $response);
        /** @var $response \Subscribo\Omnipay\Shared\Interfaces\RestfulResponseInterface */
        $this->assertSame(400, $response->getHttpResponseStatusCode());
    }


    public function testProtectedMethods()
    {
        $data = ['some key' => 'some value'];
        $this->assertSame($data, $this->request->testGetHttpRequestData($data));
        $this->assertSame([], $this->request->testGetHttpRequestQueryParameters($data));
        $this->assertSame([], $this->request->testGetHttpRequestHeaders($data));
        $this->assertNull($this->request->testGetHttpRequestMethod($data));
        $this->assertSame('Response placeholder', $this->request->testProcessHttpResponse('Response placeholder'));
        $responseMock = $this->getMockForAbstractClass('Psr\\Http\\Message\\ResponseInterface', [], '', false);
        $this->assertSame($responseMock, $this->request->testProcessHttpResponse($responseMock));
    }
}


class ClassUsingSimpleRestfulRequestTraitForTesting extends AbstractRequest
{
    use SimpleRestfulRequestTrait;

    public function getData()
    {
        return ['some' => 'value'];
    }


    protected function getEndpointUrl()
    {
        return 'http://some.api.example/path/to/endpoint';
    }


    protected function createResponse($data, $httpStatusCode)
    {
        return new SimpleRestfulResponse($this, $data, $httpStatusCode);
    }


    public function testGetHttpRequestData($data)
    {
        return $this->getHttpRequestData($data);
    }


    public function testGetHttpRequestQueryParameters($data)
    {
        return $this->getHttpRequestQueryParameters($data);
    }


    public function testGetHttpRequestHeaders($data)
    {
        return $this->getHttpRequestHeaders($data);
    }


    public function testGetHttpRequestMethod($data)
    {
        return $this->getHttpRequestMethod($data);
    }


    public function testProcessHttpResponse($response)
    {
        return $this->processHttpResponse($response);
    }
}
