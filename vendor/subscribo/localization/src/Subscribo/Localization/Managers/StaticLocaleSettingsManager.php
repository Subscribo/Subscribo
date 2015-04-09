<?php namespace Subscribo\Localization\Managers;

use Subscribo\Localization\Interfaces\LocaleSettingsManagerInterface;

class StaticLocaleSettingsManager implements LocaleSettingsManagerInterface
{
    /** @var array  */
    protected $data = array();

    /** @var array  */
    protected $defaultFallbackLocales = array();

    public function __construct(array $data, $defaultFallbackLocales)
    {
        $this->data = $data;
        $this->defaultFallbackLocales = empty($defaultFallbackLocales) ? array() : ((array) $defaultFallbackLocales);
    }

    /**
     * @param string $localeIdentifier
     * @return array
     */
    public function getFallbackLocales($localeIdentifier)
    {
        if ( ! isset($this->data['locales'][$localeIdentifier]['fallbackLocales'])) {
            return $this->defaultFallbackLocales;
        }
        if (empty($this->data['locales'][$localeIdentifier]['fallbackLocales'])) {
            return array();
        }
        return (array) $this->data['locales'][$localeIdentifier]['fallbackLocales'];
    }

    /**
     * @return array
     */
    public function getAvailableLocales()
    {
        if (empty($this->data['locales']) or ! is_array($this->data['locales'])) {
            return array();
        }
        return array_keys($this->data['locales']);
    }
}
