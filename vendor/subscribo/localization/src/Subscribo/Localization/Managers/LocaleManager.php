<?php namespace Subscribo\Localization\Managers;

use Subscribo\Localization\Interfaces\LocaleManagerInterface;

class LocaleManager implements LocaleManagerInterface
{
    /** @var  string $locale */
    protected $locale;

    /**
     * @param string $locale
     */
    public function __construct($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     * @return void
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }
}
