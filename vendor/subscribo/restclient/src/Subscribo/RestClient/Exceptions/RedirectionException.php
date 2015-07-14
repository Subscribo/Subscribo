<?php namespace Subscribo\RestClient\Exceptions;

use Exception;
use Subscribo\RestClient\Exceptions\ResponseException;

/**
 * Class RedirectionException
 *
 * Base class for converting 3xx responses to exceptions
 *
 * @package Subscribo\RestClient
 */
class RedirectionException extends ResponseException
{
    const STATUS_CODE = 300;
    const DEFAULT_MESSAGE = 'Redirect';

    /**
     * @param int|string|bool $statusCode
     * @param string|bool $message
     * @param array $data
     * @param int|bool $code
     * @param Exception $previous
     * @param array|null $headers
     */
    public function __construct($statusCode = true, $message = true, array $data = array(), $code = true, Exception $previous = null, array $headers = null)
    {
        if (true === $statusCode) {
            $statusCode = $this::STATUS_CODE;
        }
        if (is_null($headers)) {
            $headers = array();
        }
        parent::__construct($statusCode, $message, $data, $code, $previous, $headers);
    }
}
