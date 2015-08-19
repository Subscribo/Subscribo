<?php

namespace Subscribo\TransactionPluginManager\Interfaces;

/**
 * Interface LocalizerFacadeInterface
 *
 * @package Subscribo\TransactionPluginManager
 */
interface LocalizerFacadeInterface
{
    /**
     * @return \Subscribo\Localization\Interfaces\LocalizerInterface;
     */
    public function getLocalizerInstance();
}
