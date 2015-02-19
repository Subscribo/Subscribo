<?php namespace Subscribo\ApiClientAuth\Exceptions;

use Exception;

class ValidationException extends Exception
{
    const DEFAULT_EXCEPTION_CODE = 0;

    const DEFAULT_MESSAGE = 'Validation Error';

    protected $validationErrors = array();

    public function __construct(array $validationErrors, Exception $previous = null, $message = true, $code = true)
    {
        if (true === $code) {
            $code = self::DEFAULT_EXCEPTION_CODE;
        }
        if (true === $message) {
            $message = self::DEFAULT_MESSAGE;
        }
        $this->validationErrors = $validationErrors;
        parent::__construct($message, $code, $previous);
    }

    public function getValidationErrors()
    {
        return $this->validationErrors;
    }

}
