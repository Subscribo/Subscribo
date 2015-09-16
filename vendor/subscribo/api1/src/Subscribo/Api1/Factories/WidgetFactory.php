<?php

namespace Subscribo\Api1\Factories;

use Subscribo\Api1\Exceptions\InvalidArgumentException;
use Subscribo\RestCommon\Widget;

/**
 * Class WidgetFactory
 *
 * @package Subscribo\Api1
 */
class WidgetFactory
{
    /**
     * @param Widget|array|string $source
     * @return Widget
     * @throws \Subscribo\Api1\Exceptions\InvalidArgumentException
     */
    public static function make($source)
    {
        if ($source instanceof Widget) {
            return $source;
        }
        $source = is_string($source) ? json_decode($source, true) : $source;
        if ( ! is_array($source)) {
            throw new InvalidArgumentException('WidgetFactory::make() provided source have incorrect type');
        }

        return new Widget($source);
    }
}
