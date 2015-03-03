<?php namespace Subscribo\RestClient\Exceptions;

use Subscribo\RestClient\Exceptions\ClientErrorException;
use Subscribo\Exception\Interfaces\ValidationErrorsInterface;
use Subscribo\Exception\Traits\ValidationErrorsTrait;

class ValidationErrorsException extends ClientErrorException implements ValidationErrorsInterface
{
    use ValidationErrorsTrait;
}

