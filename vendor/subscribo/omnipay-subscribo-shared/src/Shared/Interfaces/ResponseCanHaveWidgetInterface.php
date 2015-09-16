<?php

namespace Subscribo\Omnipay\Shared\Interfaces;

use Subscribo\Omnipay\Shared\Interfaces\WidgetInterface;

/**
 * Interface ResponseCanHaveWidgetInterface
 *
 * @package Subscribo\OmnipaySubscriboShared
 */
interface ResponseCanHaveWidgetInterface
{
    /**
     * @return bool
     */
    public function haveWidget();

    /**
     * @return WidgetInterface|string|null
     */
    public function getWidget();

}
