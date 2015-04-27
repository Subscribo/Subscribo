<?php

namespace Omnipay\PayUnity\Message;

use Omnipay\Tests\TestCase;
use Omnipay\PayUnity\Message\AbstractRequest;
use Guzzle\Plugin\Mock\MockPlugin;
use Guzzle\Plugin\Log\LogPlugin;
use Guzzle\Log\PsrLogAdapter;
use Guzzle\Log\MessageFormatter;
use Guzzle\Http\Message\Response;
use Subscribo\Omnipay\Shared\Helpers\GuzzleClientHelper;

class AbstractRequestTest extends TestCase
{
    public function setUp()
    {
        $logger = new \Monolog\Logger('UnitTest logger');
        $logger->pushHandler(new \Monolog\Handler\RotatingFileHandler(__DIR__.'/../../tmp/logs/unit-tests.log'));
        GuzzleClientHelper::addPsrLoggerToClient($this->getHttpClient(), $logger);
    }


    /**
     * @covers \Omnipay\PayUnity\Message\AbstractRequest::sendData
     * @covers \Omnipay\PayUnity\Message\AbstractRequest::getEndPointUrl
     * @covers \Omnipay\PayUnity\Message\AbstractRequest::createResponse
     */
    public function testSendData()
    {
        $this->setMockHttpResponse('simpleAbstractSuccess.txt');

        $url = 'https://nonexistent.localhost/testurl';
        $requestStub = $this->getMockForAbstractClass(
            '\\Omnipay\\PayUnity\\Message\\AbstractRequest',
            [
                $this->getHttpClient(),
                $this->getHttpRequest(),
            ]
        );
        $responseStub = $this->getMockForAbstractClass(
            '\\Omnipay\\PayUnity\\Message\\AbstractResponse',
            [
                $requestStub,
                ['result' => 'test'],
            ]
        );
        $requestStub->expects($this->once())
            ->method('getEndPointUrl')
            ->will($this->returnValue($url));
        $requestStub->expects($this->once())
            ->method('createResponse')
            ->with(['result' => 'test'])
            ->will($this->returnValue($responseStub));
        $this->assertInstanceOf(
            '\\Omnipay\\PayUnity\\Message\\AbstractResponse',
            $requestStub->sendData(['testData' => 'someData'])
        );
    }
}
