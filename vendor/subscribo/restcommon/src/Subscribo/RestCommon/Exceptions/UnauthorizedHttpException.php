<?php namespace Subscribo\RestCommon\Exceptions;

use Exception;
use Subscribo\Support\Arr;

class UnauthorizedHttpException extends \Subscribo\Exception\Exceptions\UnauthorizedHttpException
{
    const SERVER_EXCEPTION_CODE = 100;

    const DEFAULT_WWW_AUTHENTICATE_HEADER_CONTENT = 'SubscriboDigest';

    public function __construct($message = true, array $data = array(), $code = true, Exception $previous = null, array $headers = null)
    {
        if (true === $message) {
            $message = 'Unauthorized';
        }
        if (is_null($headers)) {
            $headers = ['WWW-Authenticate' => self::DEFAULT_WWW_AUTHENTICATE_HEADER_CONTENT];
        }
        if (true === $code) {
            $code = self::SERVER_EXCEPTION_CODE;
        }
        parent::__construct($message, $data, $code, $previous, $headers);
    }
}
