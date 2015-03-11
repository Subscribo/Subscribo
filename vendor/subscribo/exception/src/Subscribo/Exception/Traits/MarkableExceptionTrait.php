<?php namespace Subscribo\Exception\Traits;

/**
 * Trait MarkableExceptionTrait
 *
 * Trait helping Exception classes to implement MarkableExceptionInterface
 *
 * @package Subscribo\Exception
 */
trait MarkableExceptionTrait {

    protected $_mark;

    protected $_markIsUsed = false;

    public function getMark()
    {
        return $this->_mark;
    }

    public function setMark($mark)
    {
        $this->_mark = $mark;
    }

    public function isMarkUsed()
    {
        return $this->_markIsUsed;
    }

    public function useMark()
    {
        $this->_markIsUsed = true;
        return $this->getMark();
    }

    public function getMarkedOriginal()
    {
        return $this;
    }
}
