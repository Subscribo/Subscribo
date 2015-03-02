<?php namespace Subscribo\RestClient\Exceptions;

use Subscribo\RestClient\Exceptions\ClientErrorHttpException;
use Subscribo\Exception\Interfaces\ValidationErrorsInterface;
use Subscribo\Exception\Traits\ValidationErrorsTrait;

class ValidationErrorsHttpException extends ClientErrorHttpException implements ValidationErrorsInterface
{
    use ValidationErrorsTrait;
}

