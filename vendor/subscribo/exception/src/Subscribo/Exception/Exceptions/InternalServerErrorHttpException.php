<?php namespace Subscribo\Exception\Exceptions;

use Exception;

/**
 * Class InternalServerErrorHttpException
 *
 * Exception for 500 Errors
 *
 * @package Subscribo\Exception
 */
class InternalServerErrorHttpException extends ServerErrorHttpException {

    public function __construct($message = null, $data = array(), $code = 0, Exception $previous = null, array $headers = array())
    {
        parent::__construct(500, $message, $data, $code, $previous, $headers);
    }
}

