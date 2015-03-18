<?php namespace Subscribo\Localization\Interfaces;


/**
 * Class LocalizationManagerInterface
 *
 * @package Subscribo\Localization
 */
interface LocalizationManagerInterface
{
    const RESOURCE_TYPE_TRANSLATION = 'translation';

    /**
     * @param string $locale
     * @param array $fallbackLocales
     * @param array $localeOptions
     * @return mixed
     */
    public function initLocale($locale, array $fallbackLocales = array(), array $localeOptions = array());


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
     * @return $this
     */
    public function registerNamespace($namespace, $paths, $resources = array());

    /**
     * @param string $id
     * @param array $parameters
     * @param string $domain
     * @param string $locale
     * @return string
     */
    public function trans($id, array $parameters, $domain, $locale);

    /**
     * @param string $id
     * @param int $number
     * @param array $parameters
     * @param string $domain
     * @param string $locale
     * @return string
     */
    public function transChoice($id, $number, array $parameters, $domain, $locale);


}
