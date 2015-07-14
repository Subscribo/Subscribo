<?php namespace Subscribo\RestClient\Exceptions;

use Exception;
use RuntimeException;
use Subscribo\Exception\Interfaces\HttpExceptionInterface;
use Subscribo\Exception\Traits\StatusMessageTrait;
use Subscribo\Exception\Interfaces\ContainDataInterface;
use Subscribo\Exception\Traits\ContainDataTrait;
use Subscribo\Exception\Interfaces\MarkableExceptionInterface;
use Subscribo\Exception\Traits\MarkableExceptionTrait;

/**
 * Class ResponseException
 *
 * Base exception for converting 3xx, 4xx, 5xx responses into an Exception;
 *
 * @package Subscribo\RestClient
 */
class ResponseException extends RuntimeException implements HttpExceptionInterface, ContainDataInterface, MarkableExceptionInterface
{
    use StatusMessageTrait;
    use ContainDataTrait;
    use MarkableExceptionTrait;

    const DEFAULT_MESSAGE = 'Special Response';
    const DEFAULT_EXCEPTION_CODE = 0;

    protected $_containedData = array();

    protected $statusCode;

    protected $headers = array();


    /**
     * @param int|string $statusCode
     * @param string|bool $message
     * @param array $data
     * @param int|bool $code
     * @param Exception $previous
     * @param array $headers
     */
    public function __construct($statusCode, $message = true, array $data = array(), $code = true, Exception $previous = null, array $headers = array())
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->_containedData = $data;
        if (true === $code) {
            $code = $this::DEFAULT_EXCEPTION_CODE;
        }
        if (true === $message) {
            $message = $this::DEFAULT_MESSAGE;
        }
        $this->setStatusMessage($message);
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return int|string
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}
