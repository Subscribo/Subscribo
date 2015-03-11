<?php namespace Subscribo\Exception\Exceptions;

use Exception;
use Subscribo\Exception\Exceptions\ClientErrorHttpException;

/**
 * Class ForbiddenHttpException
 *
 * Exception for HTTP status 403 Forbidden
 *
 * @package Subscribo\Exception
 */
class ForbiddenHttpException extends ClientErrorHttpException
{
    public function __construct($message = null, array $data = array(), $code = 0, Exception $previous = null, array $headers = array())
    {
        parent::__construct(403, $message, $data, $code, $previous, $headers);
    }

}
