<?php

namespace Subscribo\Omnipay\Shared\Exception;

use \InvalidArgumentException;

/**
 * Class WidgetInvalidRenderingParametersException
 *
 * Exception to be thrown by render() or similar methods
 * of objects implementing Subscribo\Omnipay\Shared\Interfaces\WidgetInterface
 * when the object is not in renderable state and necessary parameters have not been provided as argument
 *
 * @package Subscribo\OmnipaySubscriboShared
 */
class WidgetInvalidRenderingParametersException extends InvalidArgumentException {}
