<?php namespace Subscribo\Localization\Localizers;

use RuntimeException;
use Symfony\Component\Translation\TranslatorInterface;
use Subscribo\Localization\Interfaces\LocalizerInterface;
use Subscribo\Localization\Interfaces\LocalizationManagerInterface;
use Subscribo\Localization\LocaleTools;

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

    /**
     * @param LocalizationManagerInterface $manager
     * @param string|null $locale
     * @param string|null $namespace
     * @param string|null $subdomain
     */
    public function __construct(LocalizationManagerInterface $manager, $locale = null, $namespace = 'app', $subdomain = 'messages')
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
     * Setting locale for localizer
     *
     * It is advised to be careful when using this method on objects not created within current scope
     * If you want to change application main locale, you might want to use object implementing LocaleManagerInterface
     *
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

    /**
     * @param string $subdomain
     * @param null $namespace
     * @param null $locale
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
