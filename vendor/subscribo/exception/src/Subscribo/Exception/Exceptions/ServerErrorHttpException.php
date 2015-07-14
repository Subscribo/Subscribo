<?php namespace  Subscribo\Exception\Exceptions;

use Exception;
use Subscribo\Exception\Interfaces\MarkableExceptionInterface;
use Subscribo\Exception\Traits\MarkableExceptionTrait;

/**
 * Class ServerErrorHttpException
 *
 * Base class for all 5xx Http Errors
 *
 * @package Subscribo\Exception
 */
class ServerErrorHttpException extends HttpException implements MarkableExceptionInterface {
    use MarkableExceptionTrait;

    public function __construct($statusCode = 500, $message = null, array $data = array(), $code = 0, Exception $previous = null, array $headers = array())
    {
        parent::__construct($statusCode, $message, $data, $code, $previous, $headers);
    }

}
