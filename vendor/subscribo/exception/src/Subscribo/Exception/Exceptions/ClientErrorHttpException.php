<?php namespace  Subscribo\Exception\Exceptions;

use Exception;
use Subscribo\Exception\Interfaces\HttpClientErrorInterface;

/**
 * Class ClientErrorHttpException
 *
 * Base class for all 4xx Http Errors
 *
 * @package Subscribo\Exception
 */
class ClientErrorHttpException extends HttpException implements HttpClientErrorInterface {

    public function __construct($statusCode = 400, $message = null, $data = array(), $code = 0, Exception $previous = null, array $headers = array())
    {
        parent::__construct($statusCode, $message, $data, $code, $previous, $headers);
    }
}
