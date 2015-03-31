<?php namespace Subscribo\Localization\Interfaces;

interface LocalizationResourcesManagerInterface
{
    const RESOURCE_TYPE_TRANSLATION = 'translation';
    const FORMAT_LOCALIZED_ARRAY = 'localized_array';

    /**
     * @param string $resource usually file name with or without extension
     * @param string $namespace
     * @param string|bool $subdomain true for getting subdomain from file name
     * @param string|bool $format true for adding all available extensions
     * @param string $type
     * @return $this
     */
    public function registerResource($resource, $namespace, $subdomain = true, $format = true, $type = self::RESOURCE_TYPE_TRANSLATION);

    /**
     * @param string $namespace
     * @param string|array $paths
     * @param string|array $resources
     * @param string $resourcesType
     * @return $this
     */
    public function registerNamespace($namespace, $paths, $resources = array(), $resourcesType = self::RESOURCE_TYPE_TRANSLATION);

    /**
     * @param string $domain
     * @param array $locales
     * @return array
     */
    public function getTranslationResources($domain, array $locales);

    /**
     * @return array
     */
    public function getSupportedFormats();

}
