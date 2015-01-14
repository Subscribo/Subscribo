<?php namespace Subscribo\Exception\Exceptions;

use Exception;

/**
 * Class NotFoundHttpException
 *
 * Exception for 404 Http Errors
 *
 * @package Subscribo\Exception
 */
class NotFoundHttpException extends ClientErrorHttpException {

    public function __construct($message = null, $data = array(), $code = 0, Exception $previous = null, array $headers = array())
    {
        parent::__construct(404, $message, $data, $code, $previous, $headers);
    }
}

