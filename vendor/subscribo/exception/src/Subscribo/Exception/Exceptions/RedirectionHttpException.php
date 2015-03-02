<?php namespace Subscribo\Exception\Exceptions;

use Exception;
use Subscribo\Exception\Interfaces\DoNotReportInterface;

class RedirectionHttpException extends HttpException implements DoNotReportInterface
{
    const STATUS_CODE = 300;
    const DEFAULT_EXCEPTION_CODE = 0;
    const DEFAULT_MESSAGE = 'Redirection';

    public function __construct($statusCode = true, $message = true, array $data = array(), $code = true, Exception $previous = null, array $headers = array())
    {
        if (true === $statusCode) {
            $statusCode = $this::STATUS_CODE;
        }
        if (true === $message) {
            $message = $this::DEFAULT_MESSAGE;
        }
        if (true === $code) {
            $code = $this::DEFAULT_EXCEPTION_CODE;
        }
        parent::__construct($statusCode, $message, $data, $code, $previous, $headers);
    }
}
