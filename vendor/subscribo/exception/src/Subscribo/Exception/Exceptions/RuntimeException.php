<?php namespace Subscribo\Exception\Exceptions;

use Subscribo\Exception\Interfaces\MarkableExceptionInterface;
use Subscribo\Exception\Traits\MarkableExceptionTrait;

/**
 * Class RuntimeException
 *
 * Runtime Exception which implements MarkableExceptionInterface
 *
 * @package Subscribo\Exception
 */
class RuntimeException extends \RuntimeException implements MarkableExceptionInterface
{
    use MarkableExceptionTrait;
}
