<?php namespace Subscribo\Localization;

use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\LoggingTranslator;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Loader\PhpFileLoader;
use Illuminate\Contracts\Cache\Repository;
use Psr\Log\LoggerInterface;
use Subscribo\Localization\Interfaces\LocalizationManagerInterface;

/**
 * Class LocalizationManager
 *
 * @package Subscribo\Localization
 */
class LocalizationManager implements LocalizationManagerInterface
{
    /** @var Repository  */
    protected $cache;

    /** @var LoggerInterface  */
    protected $logger;

    /** @var MessageSelector  */
    protected $messageSelector;

    /** @var array  */
    protected $fallbackLocales = array();

    /** @var array */
    protected $localeOptions = array();

    protected $registeredResources = array();

    protected $loadedResources = array();

    protected $namespacePaths = array();

    /** @var TranslatorInterface[] */
    protected $translators = array();

    public function __construct(Repository $cache, LoggerInterface $logger, MessageSelector $messageSelector = null)
    {
        \Log::notice('Message Selector loaded :'. ($messageSelector ? 'yes' : 'no'));
        $this->cache = $cache;
        $this->logger = $logger;
        $this->messageSelector = $messageSelector;
    }

    public function initLocale($locale, array $fallbackLocales = array(), array $localeOptions = array())
    {
        if (empty($this->translators[$locale]) or ($this->getFallbackLocales($locale) !== $fallbackLocales)) {
            $this->translators[$locale] = $this->makeTranslator($locale, $fallbackLocales);
        }
        $this->setFallbackLocales($locale, $fallbackLocales);
        $this->setLocaleOptions($locale, $localeOptions);
        return $this;
    }

    /**
     * @param string $locale
     * @param array $fallbackLocales
     * @return $this
     */
    public function setFallbackLocales($locale, array $fallbackLocales)
    {
        $this->fallbackLocales[$locale] = $fallbackLocales;
        return $this;
    }

    /**
     * @param string $locale
     * @return array
     */
    public function getFallbackLocales($locale)
    {
        return empty($this->fallbackLocales[$locale]) ? array() : $this->fallbackLocales[$locale];
    }

    /**
     * @param string $locale
     * @param array $options
     * @return $this
     */
    public function setLocaleOptions($locale, array $options)
    {
        $this->localeOptions[$locale] = $options;
        return $this;
    }

    /**
     * @param string $locale
     * @param string|null $optionKey
     * @param null|mixed $default
     * @return array|null|mixed|string
     */
    public function getLocaleOption($locale, $optionKey = null, $default = null)
    {
        if (is_null($optionKey)) {
            return empty($this->localeOptions[$locale]) ? array() : $this->localeOptions[$locale];
        }
        if (array_key_exists($locale, $this->localeOptions)
            and array_key_exists($optionKey, $this->localeOptions[$locale])) {
                return  $this->localeOptions[$locale][$optionKey];
        }
        return $default;
    }

    public function registerResource($resource, $namespace, $subdomain = true, $format = true, $type = self::RESOURCE_TYPE_TRANSLATION)
    {
        if (true === $subdomain) {
            $parts = explode('.', $resource);
            $subdomain = reset($parts);
        }
        $domain = $namespace.'::'.$subdomain;
        if (true === $format) {
            $extensions = ['php'];
            foreach ($extensions as $extension) {
                $this->registeredResources[$type][$domain][] = [
                    'filename'  => $resource.'.'.$extension,
                    'format'    => null,
                ];
            }
        } else {
            $this->registeredResources[$type][$domain][] = [
                'filename'  => $resource,
                'format'    => $format,
            ];
        }
        return $this;
    }

    /**
     * @param string $namespace
     * @param array|string $paths
     * @param array $resources
     * @return $this|mixed
     */
    public function registerNamespace($namespace, $paths, $resources = array())
    {
        $paths = (array) $paths;
        foreach ($paths as $somePath) {
            $this->namespacePaths[$namespace][] = $somePath;
        }
        $resources = (array) $resources;
        foreach ($resources as $resource) {
            $this->registerResource($resource, $namespace);
        }
        return $this;
    }

    /**
     * @param string $id
     * @param array $parameters
     * @param string $domain
     * @param string $locale
     * @return string
     */
    public function trans($id, array $parameters, $domain, $locale)
    {
        $this->loadTranslationResources($domain, $locale);
        return $this->getTranslator($locale)->trans($id, $parameters, $domain, $locale);
    }

    /**
     * @param string $id
     * @param int $number
     * @param array $parameters
     * @param string $domain
     * @param string $locale
     * @return string
     */
    public function transChoice($id, $number, array $parameters, $domain, $locale)
    {
        $this->loadTranslationResources($domain, $locale);
        return $this->getTranslator($locale)->transChoice($id, $number, $parameters, $domain, $locale);
    }

    /**
     * @param string $domain
     * @param string $locale
     */
    protected function loadTranslationResources($domain, $locale)
    {
        if ( ! empty($this->loadedResources[self::RESOURCE_TYPE_TRANSLATION][$locale][$domain])) {
            return;
        }
        $filesToCheck = [];
        $locales = $this->getFallbackLocales($locale);
        array_unshift($locales, $locale);
        $paths = $this->getPaths($domain);
        $resources = $this->getRegisteredResources(self::RESOURCE_TYPE_TRANSLATION, $domain);
        foreach ($locales as $loc) {
            foreach ($paths as $path) {
                foreach ($resources as $resource) {
                    $filesToCheck[] = [
                        'filename'  => rtrim($path, '/').'/'.$loc.'/'.$resource['filename'],
                        'format'    => $resource['format'],
                        'locale'    => $loc,
                    ];
                }
            }
        }
        foreach ($filesToCheck as $file) {
            if (file_exists($file['filename'])) {
                $format = $file['format'] ?: pathinfo($file['filename'], PATHINFO_EXTENSION);
                $this->getTranslator($locale)->addResource($format, $file['filename'], $file['locale'], $domain);
            }
        }
        $this->loadedResources[self::RESOURCE_TYPE_TRANSLATION][$locale][$domain] = true;
    }

    /**
     * @param string $type
     * @param string|null $domain
     * @return array
     */
    protected function getRegisteredResources($type, $domain = null)
    {
        if (is_null($domain)) {
            return empty($this->registeredResources[$type]) ? array() : $this->registeredResources[$type];
        }
        return empty($this->registeredResources[$type][$domain]) ? array() : $this->registeredResources[$type][$domain];
    }

    /**
     * @param $locale
     * @return LoggingTranslator|Translator|TranslatorInterface
     */
    protected function getTranslator($locale)
    {
        if (empty($this->translators[$locale])) {
            $this->initLocale($locale);
        }
        return $this->translators[$locale];
    }

    /**
     * @param string $domain
     * @return array
     */
    protected function getPaths($domain)
    {
        $namespace = strstr($domain, '::', true);
        if (empty($namespace)) {
            return array();
        }
        return empty($this->namespacePaths[$namespace]) ? array() : $this->namespacePaths[$namespace];
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
        $translator->addLoader('php', new PhpFileLoader());
        $result = ($this->logger) ? new LoggingTranslator($translator, $this->logger) : $translator;
        return $result;
    }
}
