<?php namespace Subscribo\Localization\Managers;

use Symfony\Component\Translation\LoggingTranslator;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Loader\PhpFileLoader;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Illuminate\Contracts\Cache\Repository;
use Psr\Log\LoggerInterface;
use Subscribo\Localization\Translators\Translator;
use Subscribo\Localization\Localizers\Localizer;
use Subscribo\Localization\Interfaces\LocalizerInterface;
use Subscribo\Localization\Interfaces\LocalizationManagerInterface;
use Subscribo\Localization\Interfaces\LocalizationResourcesManagerInterface;
use Subscribo\Localization\Interfaces\LocaleSettingsManagerInterface;
use Subscribo\Localization\Interfaces\LocaleManagerInterface;

/**
 * Class LocalizationManager
 *
 * @package Subscribo\Localization
 */
class LocalizationManager implements LocalizationManagerInterface
{
    /** @var LocalizationResourcesManagerInterface  */
    protected $resourcesManager;

    /** @var LocaleSettingsManagerInterface  */
    protected $localeSettingsManager;

    /** @var LocaleManagerInterface  */
    protected $localeManager;

    /** @var Repository  */
    protected $cache;

    /** @var LoggerInterface  */
    protected $logger;

    /** @var MessageSelector  */
    protected $messageSelector;

    /** @var array  */
    protected $loadedTranslationResources = array();

    /** @var TranslatorInterface[] */
    protected $translators = array();

    public function __construct(LocalizationResourcesManagerInterface $resourcesManager, LocaleSettingsManagerInterface $localeSettingsManager, LocaleManagerInterface $localeManager, Repository $cache, LoggerInterface $logger, MessageSelector $messageSelector = null)
    {
        $this->resourcesManager = $resourcesManager;
        $this->localeSettingsManager = $localeSettingsManager;
        $this->localeManager = $localeManager;
        $this->cache = $cache;
        $this->logger = $logger;
        $this->messageSelector = $messageSelector;
    }

    /**
     * @param string $id
     * @param array $parameters
     * @param string $domain
     * @param string|null $locale
     * @return string
     */
    public function trans($id, array $parameters, $domain, $locale = null)
    {
        if (is_null($locale)) {
            $locale = $this->localeManager->getLocale();
        }
        $this->loadTranslationResources($domain, $locale);
        return $this->getTranslator($locale)->trans($id, $parameters, $domain, $locale);
    }

    /**
     * @param string $id
     * @param int $number
     * @param array $parameters
     * @param string $domain
     * @param string|null $locale
     * @return string
     */
    public function transChoice($id, $number, array $parameters, $domain, $locale = null)
    {
        if (is_null($locale)) {
            $locale = $this->localeManager->getLocale();
        }
        $this->loadTranslationResources($domain, $locale);
        return $this->getTranslator($locale)->transChoice($id, $number, $parameters, $domain, $locale);
    }

    public function localizer($namespace = null, $subdomain = null)
    {
        $locale = $this->localeManager->getLocale();
        return new Localizer($this, $locale, $namespace, $subdomain);
    }

    public function fallbackLocales($locale)
    {
        return $this->localeSettingsManager->getFallbackLocales($locale);
    }

    public function availableLocales()
    {
        return $this->localeSettingsManager->getAvailableLocales();
    }

    /**
     * @param string $domain
     * @param string $locale
     */
    protected function loadTranslationResources($domain, $locale)
    {
        if ( ! empty($this->loadedTranslationResources[$locale][$domain])) {
            return;
        }
        $locales = $this->localeSettingsManager->getFallbackLocales($locale);
        array_unshift($locales, $locale);
        $resources = $this->resourcesManager->getTranslationResources($domain, $locales);
        foreach ($resources as $resource) {
            $this->getTranslator($locale)->addResource($resource['format'], $resource['resource'], $resource['locale'], $domain);
        }
        $this->loadedTranslationResources[$locale][$domain] = true;
    }

    /**
     * @param string $locale
     * @return LoggingTranslator|Translator|TranslatorInterface
     */
    protected function getTranslator($locale)
    {
        if (empty($this->translators[$locale])) {
            $fallbackLocales = $this->localeSettingsManager->getFallbackLocales($locale);
            $this->translators[$locale] = $this->makeTranslator($locale, $fallbackLocales);
        }
        return $this->translators[$locale];
    }

    /**
     * @param string $locale
     * @param array $fallbackLocales
     * @return LoggingTranslator|Translator|TranslatorInterface
     */
    protected function makeTranslator($locale, array $fallbackLocales = array())
    {
        $translator = new Translator($locale, $this->messageSelector, null, false);
        $translator->setFallbackLocales($fallbackLocales);
        $formats = $this->resourcesManager->getSupportedFormats();
        $formats[] = 'array';
        $formats = array_unique($formats);
        foreach ($formats as $format) {
            $loader = $this->makeLoader($format);
            if ($loader) {
                $translator->addLoader($format, $loader);
            }
        }
        $result = ($this->logger) ? new LoggingTranslator($translator, $this->logger) : $translator;
        return $result;
    }

    /**
     * @param string $format
     * @return null|PhpFileLoader|YamlFileLoader
     */
    protected function makeLoader($format)
    {
        switch ($format) {
            case 'php':
                return new PhpFileLoader();
            case 'yml':
                return new YamlFileLoader();
            case 'array':
                return new ArrayLoader();
            default:
                return null;

        }
    }
}
