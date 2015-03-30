<?php namespace Subscribo\TranslatableModel\Traits;

use Subscribo\TranslatableModel\Interfaces\LocaleConfigurationInterface;

/**
 * Class TranslatableModelTrait
 *
 * Trait to be used in models to help make them internationalized and localized
 *
 * @package Subscribo\TranslatableModel
 */
trait TranslatableModelTrait
{
    /**
     * @return LocaleConfigurationInterface
     */
    protected function getLocaleConfiguration()
    {
        return app('Subscribo\\TranslatableModel\\Interfaces\\LocaleConfigurationInterface');
    }

}
