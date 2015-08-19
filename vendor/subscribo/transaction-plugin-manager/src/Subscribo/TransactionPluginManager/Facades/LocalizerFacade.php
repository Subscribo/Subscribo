<?php

namespace Subscribo\TransactionPluginManager\Facades;

use Subscribo\TransactionPluginManager\Interfaces\LocalizerFacadeInterface;
use Subscribo\TransactionPluginManager\Traits\TransparentFacadeTrait;
use Subscribo\Localization\Interfaces\LocalizerInterface;

/**
 * Class LocalizerFacade
 *
 * @package Subscribo\TransactionPluginManager
 */
class LocalizerFacade implements LocalizerFacadeInterface
{
    use TransparentFacadeTrait;

    /** @var \Subscribo\Localization\Interfaces\LocalizerInterface  */
    protected $instanceOfObjectBehindFacade;

    /* static calls not supported */
    protected static $classNameOfObjectBehindFacade = null;

    /**
     * @param LocalizerInterface $localizer
     */
    public function __construct(LocalizerInterface $localizer)
    {
        $this->instanceOfObjectBehindFacade = $localizer;
    }

    /**
     * @return \Subscribo\Localization\Interfaces\LocalizerInterface;
     */
    public function getLocalizerInstance()
    {
        return $this->instanceOfObjectBehindFacade;
    }
}
