<?php namespace Subscribo\Localization\Localizers;

use RuntimeException;
use Symfony\Component\Translation\TranslatorInterface;
use Subscribo\Localization\Interfaces\LocalizerInterface;
use Subscribo\Localization\Interfaces\LocalizationManagerInterface;
use Subscribo\Localization\LocaleTools;
use Subscribo\Localization\Localizers\TemplateLocalizer;

/**
 * Class Localizer
 *
 * @package Subscribo\Localization
 */
class Localizer implements TranslatorInterface, LocalizerInterface
{
    /** @var LocalizationManagerInterface  */
    protected $manager;

    /** @var  string */
    protected $namespace;

    /** @var  string */
    protected $subdomain;

    /** @var  string */
    protected $domain;

    /** @var string|bool */
    protected $locale = true;

    /**
     * @param LocalizationManagerInterface $manager
     * @param string|null|bool $locale
     * @param string|null $namespace
     * @param string|null $subdomain
     */
    public function __construct(LocalizationManagerInterface $manager, $locale = true, $namespace = 'app', $subdomain = 'messages')
    {
        if (is_null($namespace)) {
            $namespace = 'app';
        }
        if (is_null($subdomain)) {
            $subdomain = 'messages';
        }
        $this->manager = $manager;
        $this->setup($subdomain, $namespace, $locale);
    }

    /**
     * @param string|null $subdomain
     * @param string|null $namespace
     * @param string|bool|null $locale
     * @return static
     */
    public function duplicate($subdomain = null, $namespace = null, $locale = null)
    {
        $subdomain = is_null($subdomain) ? $this->subdomain : $subdomain;
        $namespace = is_null($namespace) ? $this->namespace : $namespace;
        $locale = is_null($locale) ? $this->getLocale() : $locale;
        $result = new static($this->manager, $locale, $namespace, $subdomain);
        return $result;
    }

    /**
     * Creates new TemplateLocalizer
     * @param string|null $subdomain
     * @param string|null $namespace
     * @param string|bool|null $locale
     * @return TemplateLocalizer
     */
    public function template($subdomain = null, $namespace = null, $locale = null)
    {
        $subdomain = is_null($subdomain) ? $this->subdomain : $subdomain;
        $namespace = is_null($namespace) ? $this->namespace : $namespace;
        $locale = is_null($locale) ? $this->getLocale() : $locale;
        $result = new TemplateLocalizer($this->manager, $locale, $namespace, $subdomain);
        return $result;
    }

    /**
     * Setting locale for localizer
     *
     * It is advised to be careful when using this method on objects not created within current scope
     * If you want to change application main locale, you might want to use object implementing LocaleManagerInterface
     *
     * @param string|bool $locale True for transparent locale - pointing to current (global) locale
     * @return $this
     */
    public function setLocale($locale = true)
    {
        $this->locale = $locale;
    }

    public function getLocale()
    {
        if ($this->haveTransparentLocale()) {
            return $this->manager->getCurrentLocale();
        }
        return $this->locale;
    }

    /**
     * Whether this localizer have transparent locale - pointing to current (global) locale
     *
     * @return bool
     */
    public function haveTransparentLocale()
    {
        return (true === $this->locale);
    }

    /**
     * @param string $id
     * @param array $parameters
     * @param string|null $domain
     * @param string|null $locale
     * @return string
     * @throws \RuntimeException
     */
    public function trans($id, array $parameters = array(), $domain = null, $locale = null)
    {
        $domain = is_null($domain) ? $this->domain : $domain;
        $locale = is_null($locale) ? $this->getLocale() : $locale;
        if (empty($locale)) {
            throw new RuntimeException('Localizer::trans() locale has not been provided neither initialized');
        }
        return $this->manager->trans($id, $parameters, $domain, $locale);
    }

    /**
     * @param string $id
     * @param int $number
     * @param array $parameters
     * @param string|null $domain
     * @param string|null $locale
     * @return string
     * @throws \RuntimeException
     */
    public function transChoice($id, $number, array $parameters = array(), $domain = null, $locale = null)
    {
        $domain = is_null($domain) ? $this->domain : $domain;
        $locale = is_null($locale) ? $this->getLocale() : $locale;
        if (empty($locale)) {
            throw new RuntimeException('Localizer::transChoice() locale has not been provided neither initialized');
        }
        return $this->manager->transChoice($id, $number, $parameters, $domain, $locale);
    }

    /**
     * @param string $value
     * @return string
     */
    public function simplify($value)
    {
        $first = substr($value, 0, 1);
        if ('\\' === $first) {
            return stripslashes($value);
        }
        return $value;
    }

    public function getStandardLocale()
    {
        return LocaleTools::localeTagToStandard($this->getLocale());
    }

    public function getBCPLocale()
    {
        return LocaleTools::localeTagToBCP($this->getLocale(), false);
    }

    public function getFallbackLocales($locale = null)
    {
        $locale = is_null($locale) ? $this->getLocale() : $locale;
        return $this->manager->fallbackLocales($locale);
    }

    public function getAvailableLocales()
    {
        return $this->manager->availableLocales();
    }

    /**
     * @param string $subdomain
     * @param string|null $namespace
     * @param string|bool|null $locale
     * @return $this
     */
    protected function setup($subdomain, $namespace = null, $locale = null)
    {
        $this->subdomain = $subdomain;
        if ( ! is_null($namespace)) {
            $this->namespace = $namespace;
        }
        $this->domain = ($this->namespace).'::'.$subdomain;

        if ( ! is_null($locale)) {
            $this->setLocale($locale);
        }
        return $this;
    }
}
