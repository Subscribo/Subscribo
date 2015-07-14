<?php namespace Subscribo\Exception\Exceptions;

use Exception;
use Subscribo\Exception\Interfaces\MarkableExceptionInterface;
use Subscribo\Exception\Traits\MarkableExceptionTrait;

/**
 * Class MarkingException
 *
 * Wrapper, which could be used to mark exceptions, which do not implements MarkableExceptionInterface by themselves
 *
 * @package Subscribo\Exception
 */
class MarkingException extends Exception implements MarkableExceptionInterface {
    use MarkableExceptionTrait;

    public function __construct(Exception $previous, $mark = null)
    {
        $this->setMark($mark);
        parent::__construct($previous->getMessage(), $previous->getCode(), $previous);
    }

    public function getMarkedOriginal()
    {
        return $this->getPrevious();
    }
}
