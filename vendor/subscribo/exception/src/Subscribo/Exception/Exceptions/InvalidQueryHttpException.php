<?php namespace Subscribo\Exception\Exceptions;

/**
 * Class InvalidQueryHttpException
 *
 * Class for Url query string or parameters errors
 *
 * @package Subscribo\Exception\Exceptions
 */
class InvalidQueryHttpException extends ValidationErrorsHttpException
{
    const DEFAULT_EXCEPTION_CODE = 20;
    const DEFAULT_MESSAGE = 'Invalid query parameters';
}
