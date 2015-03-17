<?php namespace Subscribo\Localization\Interfaces;

use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class LocalizerInterface
 *
 * @package Subscribo\Localization
 */
interface LocalizerInterface extends TranslatorInterface
{
    /**
     * @param string $locale
     * @param array $fallbackLocales
     * @param array $localeOptions
     * @return mixed
     */
    public function initLocale($locale, array $fallbackLocales = array(), array $localeOptions = array());


    /**
     * @param string $resource
     * @param string|bool $namespace
     * @param string|bool $simpleDomain
     * @param string|bool $format
     * @return mixed
     */
    public function registerResource($resource, $namespace = true, $simpleDomain = true, $format = true);

    /**
     * @param string $namespace
     * @param string|array $paths
     * @param string|array $resources
     * @return mixed
     */
    public function registerNamespace($namespace, $paths, $resources = array());

}
