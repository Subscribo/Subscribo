<?php namespace Subscribo\RestClient\Exceptions;

use Exception;
use Subscribo\RestClient\Exceptions\RedirectionException;
use Subscribo\Exception\Interfaces\HttpExceptionInterface;
use Subscribo\Exception\Traits\StatusMessageTrait;
use Subscribo\Exception\Interfaces\ContainDataInterface;

class RedirectionHttpException extends \Subscribo\Exception\Exceptions\RedirectionHttpException implements HttpExceptionInterface
{
    use StatusMessageTrait;

    public function __construct(Exception $previous = null, array $data = null, $statusCode = true, $message = true, $code = true, array $headers = null)
    {
        if (is_null($data)) {
            $data = ($previous instanceof ContainDataInterface) ? $previous->getData() : array();
        }
        if (true === $statusCode) {
            if ($previous instanceof RedirectionException) {
                $statusCode = $previous->getStatusCode();
                $this->setStatusMessage($previous->getStatusMessage());
            } else {
                $statusCode = $this::STATUS_CODE;
            }
        }
        if (is_null($headers)) {
            $headers = ($previous instanceof RedirectionException) ? $previous->getHeaders() : array();
        }
        if (true === $code) {
            $code = $previous ? $previous->getCode() : $this::DEFAULT_EXCEPTION_CODE;
        }
        if (true === $message) {
            $message = $previous ? $previous->getMessage() : $this::DEFAULT_MESSAGE;
        }
        parent::__construct($statusCode, $message, $data, $code, $previous, $headers);
    }
}
