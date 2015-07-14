<?php namespace Subscribo\Exception\Exceptions;

use Subscribo\Exception\Interfaces\MarkableExceptionInterface;
use Subscribo\Exception\Traits\MarkableExceptionTrait;

/**
 * Class InvalidArgumentException
 *
 * InvalidArgument Exception which implements MarkableExceptionInterface
 *
 * @package Subscribo\Exception
 */
class InvalidArgumentException extends \InvalidArgumentException implements MarkableExceptionInterface
{
    use MarkableExceptionTrait;
}
