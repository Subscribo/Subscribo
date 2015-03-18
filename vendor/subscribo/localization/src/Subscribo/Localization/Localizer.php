<?php namespace Subscribo\Localization;

use RuntimeException;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\TranslatorInterface;
use Subscribo\Localization\Interfaces\LocalizerInterface;
use Subscribo\Localization\Interfaces\LocalizationManagerInterface;

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

    /** @var string */
    protected $locale;

    public function __construct(LocalizationManagerInterface $manager, $locale = null, $namespace = 'app', $subdomain = 'messages')
    {
        $this->manager = $manager;
        $this->setup($subdomain, $namespace, $locale);
    }

    /**
     * @param string $subdomain
     * @param null $namespace
     * @param null $locale
     * @return $this
     */
    public function setup($subdomain, $namespace = null, $locale = null)
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

    /**
     * @param string|null $subdomain
     * @param string|null $namespace
     * @param string|null $locale
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
     * @param string $locale
     * @return $this
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function getLocale()
    {
        return $this->locale;
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
}
