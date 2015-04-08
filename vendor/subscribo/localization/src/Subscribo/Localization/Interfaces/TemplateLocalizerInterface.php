<?php namespace Subscribo\Localization\Interfaces;

use Subscribo\Localization\Interfaces\LocalizerInterface;

interface TemplateLocalizerInterface extends LocalizerInterface
{
    /**
     * Sets prefix to be prepended to trans() and transChoice() id, when domain is null
     *
     * @param string|null $prefix
     * @param bool $addDot Whether to automatically add trailing dot to non-empty prefix
     * @return $this
     */
    public function setPrefix($prefix = null, $addDot = true);


    /**
     * Sets default parameters to be added to provided parameters to trans() and transChoice() when domain is null
     *
     * @param array $defaultParameters
     * @return $this
     */
    public function setDefaultParameters(array $defaultParameters = array());

    /**
     * Configures localizer instance
     *
     * @param string $subdomain
     * @param string|null $namespace
     * @param string|bool|null $locale
     * @return $this
     */
    public function setup($subdomain, $namespace = null, $locale = null);

}
