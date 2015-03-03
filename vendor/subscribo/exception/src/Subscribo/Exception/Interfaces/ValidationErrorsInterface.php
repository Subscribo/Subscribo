<?php namespace Subscribo\Exception\Interfaces;

/**
 * Class ValidationErrorsInterface
 *
 * @package Subscribo\Exception
 */
interface ValidationErrorsInterface
{
    /**
     * @return array
     */
    public function getValidationErrors();
}
