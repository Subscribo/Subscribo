<?php namespace Subscribo\Exception\Exceptions;

use Subscribo\Exception\Interfaces\MarkableExceptionInterface;
use Subscribo\Exception\Traits\MarkableExceptionTrait;

class InvalidArgumentException extends \InvalidArgumentException implements MarkableExceptionInterface
{
    use MarkableExceptionTrait;
}
