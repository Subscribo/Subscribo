<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\Tests\TestCase;
use Omnipay\PayUnity\Message\AbstractRequest;
use Guzzle\Plugin\Mock\MockPlugin;
use Guzzle\Plugin\Log\LogPlugin;
use Guzzle\Log\PsrLogAdapter;
use Guzzle\Log\MessageFormatter;
use Guzzle\Http\Message\Response;

class AbstractRequestTest extends TestCase
{
    public function setUp()
    {
        $this->logger = new \Monolog\Logger('UnitTest logger');
        $this->logger->pushHandler(new \Monolog\Handler\RotatingFileHandler(__DIR__.'/../../tmp/logs/unit-tests.log'));

        $logPlugin = new LogPlugin(new PsrLogAdapter($this->logger), MessageFormatter::DEBUG_FORMAT);
        $this->clientMock = new MockPlugin();
        $this->client = $this->getHttpClient();
        $this->client->addSubscriber($logPlugin);
        $this->client->addSubscriber($this->clientMock);
    }


    /**
     * @covers \Omnipay\PayUnity\Message\AbstractRequest::sendData
     * @covers \Omnipay\PayUnity\Message\AbstractRequest::getEndPointUrl
     * @covers \Omnipay\PayUnity\Message\AbstractRequest::createResponse
     */
    public function testSendData()
    {
        $httpResponse = new Response(200, ['Content-Type' => 'application/json'], '{"result":"test"}');
        $this->clientMock->addResponse($httpResponse);

        $url = 'https://localhost/testurl';
        $stub = $this->getMockForAbstractClass(
            '\\Omnipay\\PayUnity\\Message\\AbstractRequest',
            [
                $this->client,
                $this->getHttpRequest(),
            ]
        );
        $responseStub = $this->getMockForAbstractClass(
            '\\Omnipay\\PayUnity\\Message\\AbstractResponse',
            [
                $stub,
                ['result' => 'test'],
            ]
        );
        $stub->expects($this->once())
            ->method('getEndPointUrl')
            ->will($this->returnValue($url));
        $stub->expects($this->once())
            ->method('createResponse')
            ->with(['result' => 'test'])
            ->will($this->returnValue($responseStub));
        $this->assertInstanceOf(
            '\\Omnipay\\PayUnity\\Message\\AbstractResponse',
            $stub->sendData(['testData' => 'someData'])
        );
    }
}
