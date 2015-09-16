<?php

namespace Subscribo\Omnipay\Shared\Exception;

use Exception;
use Subscribo\Omnipay\Shared\Exception\HttpMessageSendingException;
use Psr\Http\Message\ResponseInterface;
use Subscribo\Omnipay\Shared\Exception\ClientErrorResponseHttpMessageSendingException;
use Subscribo\Omnipay\Shared\Exception\ServerErrorResponseHttpMessageSendingException;

/**
 * Class NotSuccessfulResponseHttpMessageSendingException
 *
 * @package Subscribo\OmnipaySubscriboShared
 */
class NotSuccessfulResponseHttpMessageSendingException extends HttpMessageSendingException
{
    const MODE_SERVER = 'server';
    const MODE_CLIENT = 'client';
    const MODE_ALL = true;
    const MODE_NONE = false;

    /** @var ResponseInterface  */
    protected $response;

    public function __construct($message = "", $code = 0, Exception $previous = null, ResponseInterface $response = null)
    {
        $this->response = $response;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param ResponseInterface $response
     * @return static|NotSuccessfulResponseHttpMessageSendingException
     */
    public static function makeFromResponse(ResponseInterface $response)
    {
        $message = $response->getReasonPhrase();
        $code = 0;
        return new static($message, $code, null, $response);
    }

    /**
     * @param ResponseInterface $response
     * @param bool|string $mode
     * @param bool $throwAutomatically
     * @return null|NotSuccessfulResponseHttpMessageSendingException|ClientErrorResponseHttpMessageSendingException|ServerErrorResponseHttpMessageSendingException
     * @throws NotSuccessfulResponseHttpMessageSendingException|ClientErrorResponseHttpMessageSendingException|ServerErrorResponseHttpMessageSendingException
     */
    public static function makeIfResponseNotSuccessful(ResponseInterface $response, $mode = true, $throwAutomatically = false)
    {
        if (empty($mode)) {
            return null;
        }
        $statusCode = $response->getStatusCode();
        if ($statusCode >= 200 and $statusCode <= 299) {
            return null;
        }
        $exception = null;
        if ($statusCode >= 400 and $statusCode <= 499) {
            if ((self::MODE_CLIENT === $mode) or (self::MODE_ALL === $mode)) {
                $exception = ClientErrorResponseHttpMessageSendingException::makeFromResponse($response);
            }
        } elseif ($statusCode >= 500 and $statusCode <= 599) {
            if ((self::MODE_SERVER === $mode) or (self::MODE_ALL === $mode)) {
                $exception = ServerErrorResponseHttpMessageSendingException::makeFromResponse($response);
            }
        } else {
            if (self::MODE_ALL === $mode) {
                $exception = self::makeFromResponse($response);
            }
        }
        if (empty($exception)) {
            return null;
        }
        if ($throwAutomatically) {
            throw $exception;
        }
        return $exception;
    }
}
