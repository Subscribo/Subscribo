<?php namespace Subscribo\RestCommon\Exceptions;

use Exception;
use Subscribo\Exception\Exceptions\ClientErrorHttpException;
use Subscribo\Exception\Interfaces\HttpExceptionInterface;

class NoAccessTokenHttpException extends ClientErrorHttpException implements HttpExceptionInterface {

    const SERVER_STATUS_CODE = 471;
    const SERVER_EXCEPTION_CODE = 10;

    public function __construct($message = true, $data = array(), $code = true, Exception $previous = null, array $headers = array())
    {
        if (true === $code) {
            $code = self::SERVER_EXCEPTION_CODE;
        }
        if (true === $message) {
            $message = "Unauthorized. Access token (header field name '".\Subscribo\RestCommon\RestCommon::ACCESS_TOKEN_HEADER_FIELD_NAME."') not provided.";
        }
        parent::__construct(self::SERVER_STATUS_CODE, $message, $data, $code, $previous, $headers);
    }

    public function getStatusMessage()
    {
        return 'No Access Token';
    }

}