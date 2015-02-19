<?php namespace Subscribo\Exception\Interfaces;

/**
 * Class HttpExceptionInterface
 *
 * An Extension to Symfony HttpKernel HttpExceptionInterface, allowing to define a specific status message (Reason Phrase)
 *
 * @package Subscribo\Exception
 */
interface HttpExceptionInterface extends \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface {

    /**
     * @return string
     */
    public function getStatusMessage();
}
