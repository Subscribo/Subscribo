<?php

namespace Subscribo\Omnipay\Shared\Interfaces;

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
     * @return string|null
     */
    public function getWidget();

}
