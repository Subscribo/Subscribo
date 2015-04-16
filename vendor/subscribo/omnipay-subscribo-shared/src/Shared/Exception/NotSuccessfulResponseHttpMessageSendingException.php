<?php namespace Subscribo\Omnipay\Shared\Exception;

use Exception;
use Subscribo\Omnipay\Shared\Exception\HttpMessageSendingException;
use Psr\Http\Message\ResponseInterface;

/**
 * Class NotSuccessfulResponseHttpMessageSendingException
 *
 * @package Subscribo\OmnipaySubscriboShared
 */
class NotSuccessfulResponseHttpMessageSendingException extends HttpMessageSendingException
{
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
}
