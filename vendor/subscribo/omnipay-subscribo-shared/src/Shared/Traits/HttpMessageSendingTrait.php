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
 * Trait HttpMessageSendingTrait
 * This trait is expecting that class using it have property httpClient of type Guzzle\Http\ClientInterface
 *
 * @package Subscribo\OmnipaySubscriboShared
 */
trait HttpMessageSendingTrait
{
    /**
     * @param RequestInterface $request
     * @param null|bool|string $throwExceptions - true for any non-success response, 'client' for client errors only, 'server' for server errors only
     * @return ResponseInterface
     * @throws TransportErrorHttpMessageSendingException
     * @throws ClientErrorResponseHttpMessageSendingException
     * @throws ServerErrorResponseHttpMessageSendingException
     * @throws NotSuccessfulResponseHttpMessageSendingException
     */
    protected function sendHttpMessage(RequestInterface $request, $throwExceptions = null)
    {
        try {
            $client = $this->httpClient;
            $adapter = new GuzzleHttpAdapter($client);
            $response = $adapter->sendRequest($request);
        } catch (Exception $e) {
            throw new TransportErrorHttpMessageSendingException($e->getMessage(), $e->getCode(), $e);
        }
        $statusCode = $response->getStatusCode();
        if ($statusCode >= 200 and $statusCode <= 299) {
            return $response;
        }
        if ($statusCode >= 400 and $statusCode <= 499) {
            if (true === $throwExceptions or ('client' === $throwExceptions)) {
                throw ClientErrorResponseHttpMessageSendingException::makeFromResponse($response);
            }
        }
        if ($statusCode >= 500 and $statusCode <= 599) {
            if (true === $throwExceptions or ('server' === $throwExceptions)) {
                throw ServerErrorResponseHttpMessageSendingException::makeFromResponse($response);
            }
        }
        if (true === $throwExceptions) {
            throw NotSuccessfulResponseHttpMessageSendingException::makeFromResponse($response);
        }
        return $response;
    }
}
