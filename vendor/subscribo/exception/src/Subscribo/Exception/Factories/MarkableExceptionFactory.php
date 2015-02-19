<?php namespace Subscribo\Exception\Factories;

use Exception;
use Subscribo\Exception\Interfaces\MarkableExceptionInterface;
use Subscribo\Exception\Exceptions\MarkingException;

/**
 * Class MarkableExceptionFactory
 *
 * Factory Class to mark, and if needed to create a class implementing MarkableExceptionInterface
 *
 * @package Subscribo\Exception
 */
class MarkableExceptionFactory {

    /**
     * Return Exception class implementing MarkableExceptionInterface with supposedly unique mark set
     * If provided Exception already implements MarkableExceptionInterface, just sets the mark, if not already set
     *
     * @param Exception $e
     * @param bool|string $mark mark to set (if not already present), true for attempt to assign unique value
     *
     * @return MarkableExceptionInterface|Exception
     */
    public static function mark(Exception $e, $mark = true)
    {
        if ($e instanceof MarkableExceptionInterface) {
            $markFromException = $e->getMark();
            if ($markFromException) {
                $mark = $markFromException;
            }
            $result = $e;
        } else {
            $result = self::assembleException($e);
        }
        if (true === $mark) {
            $mark = self::generateMark();
        }
        $result->setMark($mark);
        return $result;
    }

    /**
     * @param Exception $e
     * @return MarkableExceptionInterface
     */
    protected static function assembleException(Exception $e)
    {
        return new MarkingException($e);
    }

    /**
     * @return string
     */
    protected static function generateMark()
    {
        return md5(microtime().rand());
    }
}
