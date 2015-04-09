<?php namespace Subscribo\Localization\Managers;

use Illuminate\Foundation\Application;
use Subscribo\Localization\Interfaces\ApplicationLocaleManagerInterface;
use Subscribo\Localization\Interfaces\LocaleManagerInterface;
use Subscribo\Localization\LocaleTools;


class ApplicationLocaleManager implements ApplicationLocaleManagerInterface
{
    /** @var \Illuminate\Foundation\Application  */
    protected $application;

    /** @var \Subscribo\Localization\Interfaces\LocaleManagerInterface  */
    protected $localeManager;

    public function __construct(LocaleManagerInterface $localeManager, Application $application)
    {
        $this->localeManager = $localeManager;
        $this->application = $application;
    }

    public function getLocale()
    {
        return $this->localeManager->getLocale();
    }

    /**
     * Sets locale for both application wide LocaleManager as well as for Laravel application
     *
     * @param string|bool $locale - true for getting locale from application wide LocaleManager
     * @param string|bool $applicationLocaleFormat false for not setting locale for Laravel application
     * @return void
     */
    public function setLocale($locale = true, $applicationLocaleFormat = self::FORMAT_STANDARD)
    {
        if (true === $locale) {
            $locale = $this->getLocale();
        } else {
            $this->localeManager->setLocale($locale);
        }
        if (empty($applicationLocaleFormat)) {
            return;
        }
        switch ($applicationLocaleFormat) {
            case self::FORMAT_NONE:
                return;
            case self::FORMAT_FULL:
                break;
            case self::FORMAT_STANDARD:
                $locale = LocaleTools::localeTagToStandard($locale);
                break;
            case self::FORMAT_LANGUAGE:
                $locale = LocaleTools::localeTagToLanguage($locale);
                break;
        }
        $this->application->setLocale($locale);
    }

}
