<?php

namespace Subscribo\Omnipay\Shared\Interfaces;

/**
 * Interface WidgetInterface
 *
 * @package Subscribo\OmnipaySubscriboShared
 */
interface WidgetInterface
{
    /**
     * @param array $parameters
     * @return string
     */
    public function render($parameters = []);

    /**
     * @param array $parameters
     * @return bool
     */
    public function isRenderable($parameters = []);

    /**
     * @return string
     */
    public function __toString();

}
