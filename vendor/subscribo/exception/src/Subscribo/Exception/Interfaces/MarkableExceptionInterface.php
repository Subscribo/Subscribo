<?php namespace Subscribo\Exception\Interfaces;

use Exception;

/**
 * Class MarkableExceptionInterface
 *
 * Interface defining an ability of an Exception object to carry a specific mark
 *
 * @package Subscribo\Exception
 */
interface MarkableExceptionInterface {

    /**
     * Mark getter
     * @return string|null
     */
    public function getMark();

    /**
     * Mark setter
     * @param string|null $mark
     * @return void
     */
    public function setMark($mark);

    /**
     * True if mark has been used (by calling method useMark()), false otherwise
     * @return bool
     */
    public function isMarkUsed();

    /**
     * Mark getter with object status change (isMarkUsed() should from now on be returning true)
     * @return string|null
     */
    public function useMark();

    /**
     * Return original exception, which on which marking was applied and which was possibly logged.
     * Could be this Exception object, or some other Exception object, to which this object refer
     *
     * @return Exception
     */
    public function getMarkedOriginal();

}