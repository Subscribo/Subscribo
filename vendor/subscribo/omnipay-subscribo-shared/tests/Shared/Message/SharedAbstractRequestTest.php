<?php

namespace Subscribo\Omnipay\Shared\Message;

use Omnipay\Tests\TestCase;
use Omnipay\Common\CreditCard;
use Subscribo\Omnipay\Shared\CreditCard as ExtendedCreditCard;
use Subscribo\PsrHttpTools\Factories\RequestFactory;
use Subscribo\Omnipay\Shared\Message\AbstractRequest;
use Guzzle\Plugin\Mock\MockPlugin;



class SharedAbstractRequestTestCase extends TestCase
{
    public function setUp()
    {
        $this->message = RequestFactory::make('http://nonexistent.localhost/path', null, ['param' => 'value']);
        $this->request = new ExtendedAbstractRequestForTesting($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testSendHttpMessageSimpleGet()
    {
        $this->setMockHttpResponse('successResponse.txt');


        $response = $this->request->testMethodSendHttpMessage($this->message, true);

        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response);
        /** @var $response \Psr\Http\Message\ResponseInterface */
        $this->assertSame('1.1', $response->getProtocolVersion());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('OK', $response->getReasonPhrase());
        $this->assertSame('application/json', $response->getHeaderLine('content-type'));
        $response->getBody()->rewind();
        $this->assertJsonStringEqualsJsonString('{"result": "OK"}', $response->getBody()->getContents());
        $response->getBody()->rewind();
        $expectedBody = '{"result":"OK"}'."\n";
        $this->assertSame($expectedBody, $response->getBody()->getContents());
    }


    /**
     * @expectedException \Subscribo\Omnipay\Shared\Exception\TransportErrorHttpMessageSendingException
     * @expectedExceptionMessage Mock queue is empty
     */
    public function testSendHttpMessageEmptyMockQueue()
    {
        $this->getHttpClient()->addSubscriber(new MockPlugin([]));

        $this->request->testMethodSendHttpMessage($this->message, false);
    }

    /**
     * @expectedException \Subscribo\Omnipay\Shared\Exception\ServerErrorResponseHttpMessageSendingException
     * @expectedExceptionMessage Internal Server Error
     */
    public function testSendHttpMessageServerError()
    {
        $this->setMockHttpResponse('serverErrorResponse.txt');

        $this->request->testMethodSendHttpMessage($this->message, true);
    }

    public function testSendHttpMessageServerErrorReceived()
    {
        $this->setMockHttpResponse('serverErrorResponse.txt');

        $response = $this->request->testMethodSendHttpMessage($this->message, null);

        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response);
        /** @var $response \Psr\Http\Message\ResponseInterface  */
        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame('Internal Server Error', $response->getReasonPhrase());
    }

    /**
     * @expectedException \Subscribo\Omnipay\Shared\Exception\ClientErrorResponseHttpMessageSendingException
     * @expectedExceptionMessage Not Found
     */
    public function testSendHttpMessageClientError()
    {
        $this->setMockHttpResponse('clientErrorResponse.txt');

        $this->request->testMethodSendHttpMessage($this->message, true);
    }


    public function testSendHttpMessageClientErrorReceived()
    {
        $this->setMockHttpResponse('clientErrorResponse.txt');

        $response = $this->request->testMethodSendHttpMessage($this->message, 'server');

        $this->assertInstanceOf('Psr\\Http\\Message\\ResponseInterface', $response);
        /** @var $response \Psr\Http\Message\ResponseInterface  */
        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame('Not Found', $response->getReasonPhrase());
    }


    /**
     * @expectedException \Subscribo\Omnipay\Shared\Exception\NotSuccessfulResponseHttpMessageSendingException
     * @expectedExceptionMessage Continue
     */
    public function testSendHttpMessageOtherResponse()
    {
        $this->setMockHttpResponse('otherResponse.txt');

        $this->request->testMethodSendHttpMessage($this->message, true);
    }

    public function testSetCardArray()
    {
        $this->assertNull($this->request->getCard());
        $data = ['card' => ['mobile' => '+11-1111-111']];
        $this->request->initialize($data);
        $card = $this->request->getCard();
        $this->assertNotEmpty($this->request->getCard());
        $this->assertInstanceOf('Omnipay\\Common\\CreditCard', $card);
        $this->assertInstanceOf('Subscribo\\Omnipay\\Shared\\CreditCard', $card);
        $this->assertSame('+11-1111-111', $card->getMobile());
        $this->assertEmpty($card->getNumber());
        $this->request->setCard(null);
        $this->assertNull($this->request->getCard());
    }

    public function testSetCardCreditCard()
    {
        $this->assertNull($this->request->getCard());
        $data = $this->getValidCard();
        $original = new CreditCard($data);
        $this->request->setCard($original);
        $card = $this->request->getCard();
        $this->assertInstanceOf('Omnipay\\Common\\CreditCard', $card);
        $this->assertInstanceOf('Subscribo\\Omnipay\\Shared\\CreditCard', $card);
        $this->assertNotSame($original, $card);
        $this->assertNotEquals($original, $card);
        $this->assertSame($original->getParameters(), $card->getParameters());
        $this->assertSame($original->getNumber(), $card->getNumber());
        $this->assertSame($original->getNumberMasked(), $card->getNumberMasked());
    }

    public function testSetCardExtended()
    {
        $this->assertNull($this->request->getCard());
        $original = new ExtendedCreditCard($this->getValidCard());
        $data = ['card' => $original];
        $this->request->initialize($data);
        $card = $this->request->getCard();
        $this->assertNotEmpty($this->request->getCard());
        $this->assertInstanceOf('Omnipay\\Common\\CreditCard', $card);
        $this->assertInstanceOf('Subscribo\\Omnipay\\Shared\\CreditCard', $card);
        $this->assertSame($original, $card);
        $this->request->setCard(null);
        $this->assertNull($this->request->getCard());

    }
}


class ExtendedAbstractRequestForTesting extends AbstractRequest
{
    public function sendData($data)
    {
    }

    public function getData()
    {
    }

    public function testMethodSendHttpMessage($request, $throwExceptionMode = false)
    {
        return $this->sendHttpMessage($request, $throwExceptionMode);
    }

}
