<?php namespace Subscribo\Localization;

use RuntimeException;
use Symfony\Component\Translation\Translator as SymfonyTranslator;
use Symfony\Component\Translation\LoggingTranslator;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Loader\PhpFileLoader;
use Illuminate\Contracts\Cache\Repository;
use Psr\Log\LoggerInterface;
use Subscribo\Support\Arr;
use Subscribo\Localization\Interfaces\LocalizerInterface;

/**
 * Class Localizer
 *
 * @package Subscribo\Localization
 */
class Localizer implements TranslatorInterface, LocalizerInterface
{

    /** @var TranslatorInterface */
    protected $translator;

    /** @var \Illuminate\Contracts\Cache\Repository  */
    protected $cache;

    /** @var \Psr\Log\LoggerInterface  */
    protected $logger;

    /** @var string */
    protected $currentLocale;

    /** @var array  */
    protected $fallbackLocales = array();

    /** @var \Symfony\Component\Translation\MessageSelector  */
    protected $messageSelector;

    protected $defaultDomain = 'app::messages';

    protected $defaultNamespace = 'app';

    protected $domainsByFile = array();

    protected $localesByFile = array();

    protected $registeredResources = array();

    protected $loadedResources = array();

    protected $localeOptions = array();

    protected $namespacePaths = array();



    public function __construct(Repository $cache, LoggerInterface $logger, MessageSelector $messageSelector = null)
    {
        $this->cache = $cache;
        $this->logger = $logger;
        $this->messageSelector = $messageSelector;
    }

    public function initLocale($locale, array $fallbackLocales = array(), array $localeOptions = array())
    {
        $this->setLocale($locale)
            ->setFallbackLocales($locale, $fallbackLocales);
        $this->loadedResources = array();
        $this->setLocaleOptions($locale, $localeOptions);
        return $this;
    }

    public function setLocale($locale)
    {
        $this->currentLocale = $locale;
        return $this;
    }

    public function getLocale()
    {
        return $this->currentLocale;
    }

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

    public function setLocaleOptions($locale, array $options)
    {
        $this->localeOptions[$locale] = $options;
        return $this;
    }

    public function getDomainForFile($fileName)
    {
        return Arr::get($this->domainsByFile, $fileName, $this->defaultDomain);
    }

    public function setDomainForFile($domain, $fileName)
    {
        $this->domainsByFile[$fileName] = $domain;
        return $this;
    }

    public function getLocaleForFile($fileName)
    {
        return Arr::get($this->localesByFile, $fileName, $this->currentLocale);
    }

    public function setLocaleForFile($locale, $fileName)
    {
        $this->localesByFile[$fileName] = $locale;
        return $this;
    }

    public function transForFile($id, array $parameters = array(), $fileName)
    {
        return $this->trans($id, $parameters, $this->getDomainForFile($fileName), $this->getLocaleForFile($fileName));
    }

    public function registerResource($resource, $namespace = true, $simpleDomain = true, $format = true)
    {
        if (true === $namespace) {
            $namespace = $this->defaultNamespace;
        }
        if (true === $simpleDomain) {
            $parts = explode('.', $resource);
            $simpleDomain = reset($parts);
        }
        $domain = $namespace.'::'.$simpleDomain;
        if (true === $format) {
            $extensions = ['php'];
            foreach ($extensions as $extension) {
                $this->registeredResources[$domain][] = [
                    'filename'  => $resource.'.'.$extension,
                    'format'    => null,
                ];
            }
        } else {
            $this->registeredResources[$domain][] = [
                'filename'  => $resource,
                'format'    => $format,
            ];
        }
        return $this;
    }

    public function registerNamespace($namespace, $paths, $resources = array())
    {
        $paths = (array) $paths;
        foreach ($paths as $somePath) {
            $this->namespacePaths[$namespace][] = $somePath;
        }
        $resources = (array) $resources;
        foreach ($resources as $resource) {
            $this->registerResource($resource, $namespace, true, true);
        }
        return $this;
    }


    public function trans($id, array $parameters = array(), $domain = null, $locale = null)
    {
        $locale = is_null($locale) ? $this->getLocale() : $locale;
        $this->loadResources($domain, $locale);
        return $this->getTranslator()->trans($id, $parameters, $domain, $locale);
    }

    public function transChoice($id, $number, array $parameters = array(), $domain = null, $locale = null)
    {
        $locale = is_null($locale) ? $this->getLocale() : $locale;
        $this->loadResources($domain, $locale);
        return $this->getTranslator()->transChoice($id, $number, $parameters, $domain, $locale);
    }

    protected function loadResources($domain, $locale)
    {
        if ( ! empty($this->loadedResources[$domain])) {
            return;
        }
        $filesToCheck = [];
        $locales = $this->getFallbackLocales($locale);
        array_unshift($locales, $locale);
        $paths = $this->getPaths($domain);
        $resources = Arr::get($this->registeredResources, $domain, []);
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
                $this->getTranslator()->addResource($format, $file['filename'], $file['locale'], $domain);
            }
        }
        $this->loadedResources[$domain] = true;
    }

    /**
     * @return LoggingTranslator|SymfonyTranslator|TranslatorInterface
     * @throws \RuntimeException
     */
    protected function getTranslator()
    {
        if ($this->translator) {
            return $this->translator;
        }
        if (empty($this->currentLocale)) {
            throw new RuntimeException('Locale has not been initialized');
        }
        $translator = new SymfonyTranslator($this->currentLocale, $this->messageSelector, null, false);
        $translator->setFallbackLocales($this->getFallbackLocales($this->currentLocale));
        $translator->addLoader('php', new PhpFileLoader());

        $this->translator = ($this->logger) ? new LoggingTranslator($translator, $this->logger) : $translator;
        return $this->translator;
    }

    protected function getPaths($domain)
    {
        $namespace = strstr($domain, '::', true) ?: $this->defaultNamespace;
        return Arr::get($this->namespacePaths, $namespace, array());
    }
}
