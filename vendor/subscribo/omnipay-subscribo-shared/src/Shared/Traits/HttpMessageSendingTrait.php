<?php namespace Subscribo\Omnipay\Shared\Traits;

use Exception;
use Subscribo\Omnipay\Shared\Exception\TransportErrorHttpMessageSendingException;
use Subscribo\Omnipay\Shared\Exception\ClientErrorResponseHttpMessageSendingException;
use Subscribo\Omnipay\Shared\Exception\ServerErrorResponseHttpMessageSendingException;
use Subscribo\Omnipay\Shared\Exception\NotSuccessfulResponseHttpMessageSendingException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Ivory\HttpAdapter\GuzzleHttpAdapter;

/**
 * Trait HttpMessageSendingTrait intended for use with Message/SomeRequest type of classes
 * This trait is expecting that class using it have property httpClient of type Guzzle\Http\ClientInterface
 *
 * @package Subscribo\OmnipaySubscriboShared
 */
trait HttpMessageSendingTrait
{
    /**
     * @param RequestInterface $request
     * @param bool|string $throwExceptionMode - true for any non-success response, 'client' for client errors only, 'server' for server errors only
     * @return ResponseInterface
     * @throws TransportErrorHttpMessageSendingException
     * @throws ClientErrorResponseHttpMessageSendingException
     * @throws ServerErrorResponseHttpMessageSendingException
     * @throws NotSuccessfulResponseHttpMessageSendingException
     */
    protected function sendHttpMessage(RequestInterface $request, $throwExceptionMode = false)
    {
        try {
            $client = $this->httpClient;
            $adapter = new GuzzleHttpAdapter($client);
            $response = $adapter->sendRequest($request);
        } catch (Exception $e) {
            throw new TransportErrorHttpMessageSendingException($e->getMessage(), $e->getCode(), $e);
        }
        NotSuccessfulResponseHttpMessageSendingException::makeIfResponseNotSuccessful($response, $throwExceptionMode, true);

        return $response;
    }
}
