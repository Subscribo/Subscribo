<?php namespace Subscribo\Exception\Exceptions;

use Exception;

/**
 * Class BadRequestHttpException
 *
 * Exception for 400 Http Errors
 * 
 * @package Subscribo\Exception
 */
class BadRequestHttpException extends ClientErrorHttpException {

    public function __construct($message = null, $data = array(), $code = 0, Exception $previous = null, array $headers = array())
    {
        parent::__construct(400, $message, $data, $code, $previous, $headers);
    }
}

