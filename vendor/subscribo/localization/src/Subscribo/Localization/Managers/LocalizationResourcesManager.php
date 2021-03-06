<?php namespace Subscribo\Localization\Managers;

use InvalidArgumentException;
use Subscribo\Localization\Interfaces\LocalizationResourcesManagerInterface;

/**
 * Class LocalizationResourcesManager
 *
 * @package Subscribo\Localization
 */
class LocalizationResourcesManager implements LocalizationResourcesManagerInterface
{
    /** @var array  */
    protected $registeredResources = array();

    /** @var array  */
    protected $registeredLocalizedArrayResources = array();

    /** @var array  */
    protected $namespacePaths = array();

    /** @var array  */
    protected $supportedExtensions = ['php', 'yml'];


    public function getSupportedFormats()
    {
        return $this->supportedExtensions;
    }


    public function registerResource($resource, $namespace, $subdomain = true, $format = true, $type = self::RESOURCE_TYPE_TRANSLATION)
    {
        if (is_string($resource)) {
            if (true === $subdomain) {
                $parts = explode('.', $resource);
                $subdomain = reset($parts);
            }
        } elseif(is_array($resource)) {
            if (true === $format) {
                $format = self::FORMAT_LOCALIZED_ARRAY;
            }
        }
        $this->checkRegisterResourceArguments($resource, $namespace, $subdomain, $format);
        $domain = $namespace.'::'.$subdomain;
        if ($format === self::FORMAT_LOCALIZED_ARRAY) {
            $this->registeredLocalizedArrayResources[$type][$domain][] = $resource;

        } elseif (true === $format) {
            foreach ($this->supportedExtensions as $extension) {
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

    public function registerNamespace($namespace, $paths, $resources = array(), $resourcesType = self::RESOURCE_TYPE_TRANSLATION)
    {
        $paths = (array) $paths;
        foreach ($paths as $somePath) {
            $this->namespacePaths[$namespace][] = $somePath;
        }
        $resources = (array) $resources;
        foreach ($resources as $resource) {
            $this->registerResource($resource, $namespace, true, true, $resourcesType);
        }
        return $this;
    }

    /**
     * @param string $domain
     * @param array $locales
     * @return array
     */
    public function getTranslationResources($domain, array $locales)
    {
        return $this->getExistingResources(self::RESOURCE_TYPE_TRANSLATION, $domain, $locales);
    }

    /**
     * @param string $type
     * @param string $domain
     * @param array $locales
     * @return array
     */
    public function getExistingResources($type, $domain, array $locales)
    {
        $result = $this->getLocalizedArrayResources($type, $domain, $locales);
        $filesToCheck = [];
        $paths = $this->getPaths($domain);
        $resources = $this->getRegisteredResources($type, $domain);
        foreach ($locales as $loc) {
            foreach ($paths as $path) {
                foreach ($resources as $resource) {
                    $filesToCheck[] = [
                        'resource'  => rtrim($path, '/').'/'.$loc.'/'.$resource['filename'],
                        'format'    => $resource['format'],
                        'locale'    => $loc,
                    ];
                }
            }
        }
        foreach ($filesToCheck as $file) {
            if (file_exists($file['resource'])) {
                $item = $file;
                $item['format'] = $file['format'] ?: pathinfo($file['resource'], PATHINFO_EXTENSION);
                $result[] = $item;
            }
        }
        return $result;
    }

    /**
     * @param string|array $resource
     * @param string $namespace
     * @param string|bool $subdomain
     * @param string|bool $format
     * @throws \InvalidArgumentException
     */
    protected function checkRegisterResourceArguments($resource, $namespace, $subdomain, $format)
    {
        if (is_string($resource)) {
            if ($format === self::FORMAT_LOCALIZED_ARRAY) {
                throw new InvalidArgumentException('LocalizationResourcesManager::registerResource() invalid format for resource as a string');
            }
        } elseif(is_array($resource)) {
            if ( ! is_string($subdomain)) {
                throw new InvalidArgumentException('LocalizationResourcesManager::registerResource() when resource parameter is an array, subdomain should be specified');
            }
            if ($format !== self::FORMAT_LOCALIZED_ARRAY) {
                throw new InvalidArgumentException('LocalizationResourcesManager::registerResource() invalid format for resource as an array');
            }
        } else {
            throw new InvalidArgumentException('LocalizationResourcesManager::registerResource() resource parameter should be a string or an array');
        }
        if ( ! is_string($subdomain)) {
            throw new InvalidArgumentException('LocalizationResourcesManager::registerResource() subdomain not specified');
        }
    }

    /**
     * @param string $type
     * @param string $domain
     * @param array $locales
     * @return array
     */
    protected function getLocalizedArrayResources($type, $domain, array $locales)
    {
        $result = [];
        if (empty($this->registeredLocalizedArrayResources[$type][$domain])) {
            return array();
        }
        foreach ($this->registeredLocalizedArrayResources[$type][$domain] as $resourceSet)
        {
            foreach ($resourceSet as $locale => $content)
            {
                if (false === in_array($locale, $locales, true)) {
                    continue;
                }
                $result[] = [
                    'resource' => $content,
                    'format' => 'array',
                    'locale' => $locale,
                ];
            }
        }
        return $result;
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
}
