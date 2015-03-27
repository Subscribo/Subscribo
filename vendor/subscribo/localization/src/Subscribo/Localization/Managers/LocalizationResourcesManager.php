<?php namespace Subscribo\Localization\Managers;

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
    protected $namespacePaths = array();

    /** @var array  */
    protected $supportedExtensions = ['php', 'yml'];


    public function getSupportedFormats()
    {
        return $this->supportedExtensions;
    }


    public function registerResource($resource, $namespace, $subdomain = true, $format = true, $type = self::RESOURCE_TYPE_TRANSLATION)
    {
        if (true === $subdomain) {
            $parts = explode('.', $resource);
            $subdomain = reset($parts);
        }
        $domain = $namespace.'::'.$subdomain;
        if (true === $format) {
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
        $result = [];
        $filesToCheck = [];
        $paths = $this->getPaths($domain);
        $resources = $this->getRegisteredResources($type, $domain);
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
                $item = $file;
                $item['format'] = $file['format'] ?: pathinfo($file['filename'], PATHINFO_EXTENSION);
                $result[] = $item;
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
