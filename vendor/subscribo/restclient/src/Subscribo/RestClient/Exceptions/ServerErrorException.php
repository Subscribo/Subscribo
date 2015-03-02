<?php namespace Subscribo\RestClient\Exceptions;

use Exception;
use Subscribo\RestClient\Exceptions\ResponseException;

/**
 * Class ServerErrorException
 *
 * Base class for converting 5xx responses to exceptions
 *
 * @package Subscribo\RestClient
 */
class ServerErrorException extends ResponseException
{
    const STATUS_CODE = 500;
    const DEFAULT_MESSAGE = 'Server Error';

    public function __construct($statusCode = true, $message = true, array $data = array(), $code = true, Exception $previous = null, array $headers = array())
    {
        if (true === $statusCode) {
            $statusCode = $this::STATUS_CODE;
        }
        parent::__construct($statusCode, $message, $data, $code, $previous, $headers);
    }
}
