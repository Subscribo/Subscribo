<?php namespace Subscribo\RestClient\Exceptions;

use Exception;
use Subscribo\RestClient\Exceptions\ResponseException;

/**
 * Class ClientErrorException
 *
 * Base class for converting 4xx responses to exceptions
 *
 * @package Subscribo\RestClient
 */
class ClientErrorException extends ResponseException
{
    const STATUS_CODE = 400;
    const DEFAULT_MESSAGE = 'Client Error';

    public function __construct($statusCode = true, $message = true, array $data = array(), $code = true, Exception $previous = null, array $headers = array())
    {
        if (true === $statusCode) {
            $statusCode = $this::STATUS_CODE;
        }
        parent::__construct($statusCode, $message, $data, $code, $previous, $headers);
    }
}
