<?php namespace Subscribo\Auth\Exceptions;

use Subscribo\Exception\Interfaces\MarkableExceptionInterface;
use Subscribo\Exception\Traits\MarkableExceptionTrait;

/**
 * Class InvalidArgumentException
 *
 * @package Subscribo\RestCommon
 */
class InvalidArgumentException extends \InvalidArgumentException implements MarkableExceptionInterface
{
    use MarkableExceptionTrait;
}

