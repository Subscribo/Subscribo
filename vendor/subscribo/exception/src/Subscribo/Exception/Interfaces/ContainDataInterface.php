<?php namespace Subscribo\Exception\Interfaces;

/**
 * Class ContainDataInterface
 *
 * Interface for Exception, which could contain additional data
 *
 * (and possibly also a specific set of data designed to be returned to client)
 *
 * @package Subscribo\Exception
 */
interface ContainDataInterface {

    /**
     * @return array
     */
    public function getData();

    /**
     * Should return something renderable
     *
     * @param array|null $default Default data to add, if corresponding keys are not already provided
     * @return array|null|mixed
     */
    public function getOutputData(array $default = null);

    /**
     * Plain Key data (usually Error data)
     *
     * @return array
     */
    public function getKeyData();

    /**
     * @return string
     */
    public static function getKey();

}
