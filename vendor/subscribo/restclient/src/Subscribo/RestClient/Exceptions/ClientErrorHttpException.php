<?php namespace Subscribo\RestClient\Exceptions;

use Subscribo\Exception\Interfaces\HttpExceptionInterface;
use Subscribo\Exception\Traits\StatusMessageTrait;

class ClientErrorHttpException extends \Subscribo\Exception\Exceptions\ClientErrorHttpException implements HttpExceptionInterface {
    use StatusMessageTrait;
}
