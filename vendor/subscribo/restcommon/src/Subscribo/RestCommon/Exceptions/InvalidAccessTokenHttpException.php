<?php namespace Subscribo\RestCommon\Exceptions;

use Exception;
use Subscribo\Exception\Exceptions\ClientErrorHttpException;
use Subscribo\Exception\Interfaces\HttpExceptionInterface;

class InvalidAccessTokenHttpException extends ClientErrorHttpException implements HttpExceptionInterface {

    const SERVER_STATUS_CODE = 472;
    const SERVER_EXCEPTION_CODE = 20;

    public function __construct($message = true, $data = array(), $code = true, Exception $previous = null, array $headers = array())
    {
        if (true === $code) {
            $code = self::SERVER_EXCEPTION_CODE;
        }
        if (true === $message) {
            $message = "Unauthorized. Access token invalid.";
        }
        parent::__construct(self::SERVER_STATUS_CODE, $message, $data, $code, $previous, $headers);
    }

    public function getStatusMessage()
    {
        return 'Invalid Access Token';
    }

}