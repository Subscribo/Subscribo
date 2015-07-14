<?php namespace Subscribo\ModelBase;

use Subscribo\TranslatableModel\Interfaces\LocaleConfigurationInterface;
use Subscribo\Localization\Interfaces\LocalizerInterface;

class LocalizerLocaleConfiguration implements LocaleConfigurationInterface
{
    /**
     * @var LocalizerInterface
     */
    protected $localizer;

    public function __construct(LocalizerInterface $localizer)
    {
        $this->localizer = $localizer;
    }

    public function getCurrentLocale()
    {
        return $this->localizer->getLocale();
    }

    public function getFallbackLocales($locale = null)
    {
        return $this->localizer->getFallbackLocales($locale);
    }

    public function getAvailableLocales()
    {
        return $this->localizer->getAvailableLocales();
    }
}
