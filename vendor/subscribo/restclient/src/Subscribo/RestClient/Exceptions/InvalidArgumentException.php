<?php namespace Subscribo\RestClient\Exceptions;

use Subscribo\Exception\Interfaces\MarkableExceptionInterface;
use Subscribo\Exception\Traits\MarkableExceptionTrait;

/**
 * Class InvalidArgumentException
 *
 * @package Subscribo\RestClient
 */
class InvalidArgumentException extends \InvalidArgumentException implements MarkableExceptionInterface
{
    use MarkableExceptionTrait;
}
