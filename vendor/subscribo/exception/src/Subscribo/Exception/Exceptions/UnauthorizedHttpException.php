<?php namespace Subscribo\Exception\Exceptions;

use Exception;
use Subscribo\Exception\Exceptions\ClientErrorHttpException;


class UnauthorizedHttpException extends ClientErrorHttpException
{
    public function __construct($message = null, array $data = array(), $code = 0, Exception $previous = null, array $headers = array())
    {
        parent::__construct(401, $message, $data, $code, $previous, $headers);
    }

}
