<?php namespace Subscribo\Localization\Localizers;

use Subscribo\Localization\Localizers\Localizer;
use Subscribo\Localization\Interfaces\TemplateLocalizerInterface;

/**
 * Class TemplateLocalizer
 * Template localizer is intended to be used in contexts where its instance is used only in limited scopes, such as templates
 * as using its functionality in broader scopes might lead to unpredictable results
 *
 * @package Subscribo\Localization
 */
class TemplateLocalizer extends Localizer implements TemplateLocalizerInterface
{
    protected $prefix;

    public function trans($id, array $parameters = array(), $domain = null, $locale = null)
    {
        if (is_null($domain) and $this->prefix) {
            $id = $this->prefix.$id;
        }
        return parent::trans($id, $parameters, $domain, $locale);
    }

    public function transChoice($id, $number, array $parameters = array(), $domain = null, $locale = null)
    {
        if (is_null($domain) and $this->prefix) {
            $id = $this->prefix.$id;
        }
        return parent::transChoice($id, $number, $parameters, $domain, $locale);
    }

    /**
     * Sets prefix to be prepended to trans() and transChoice() id, when domain is null
     *
     * @param string|null $prefix
     * @param bool $addDot Whether to automatically add trailing dot to non-empty prefix
     * @return mixed
     */
    public function setPrefix($prefix = null, $addDot = true)
    {
        if ($prefix and $addDot) {
            $prefix = rtrim($prefix, '.').'.';
        }
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * Configures localizer instance
     *
     * @param string $subdomain
     * @param string|null $namespace
     * @param string|bool|null $locale
     * @return $this
     */
    public function setup($subdomain, $namespace = null, $locale = null)
    {
        $this->prefix = null;
        return parent::setup($subdomain, $namespace, $locale);
    }
}
