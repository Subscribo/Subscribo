<?php

namespace Subscribo\Omnipay\Shared\Exception;

use PHPUnit_Framework_TestCase;
use Zend\Diactoros\Response;
use Subscribo\Omnipay\Shared\Exception\NotSuccessfulResponseHttpMessageSendingException;

class NotSuccessfulResponseHttpMessageSendingExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testMakeIfResponseNotSuccessful()
    {
        $response = (new Response())->withStatus(400);
        $exception = NotSuccessfulResponseHttpMessageSendingException::makeIfResponseNotSuccessful($response, NotSuccessfulResponseHttpMessageSendingException::MODE_CLIENT);
        $this->assertInstanceOf('Subscribo\\Omnipay\\Shared\\Exception\\NotSuccessfulResponseHttpMessageSendingException', $exception);
        $this->assertSame($response, $exception->getResponse());
    }
}

