<?php namespace Subscribo\Localization\Interfaces;


/**
 * Class LocalizationManagerInterface
 *
 * @package Subscribo\Localization
 */
interface LocalizationManagerInterface
{

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
