<?php

namespace Subscribo\TransactionPluginManager\Factories;

/**
 * Abstract class AbstractServerRequestFactory
 *
 * @package Subscribo\TransactionPluginManager
 */
abstract class AbstractServerRequestFactory
{
    /** @var  string|null $defaultDomain */
    protected $defaultDomain;

    /**
     * @param string|null|bool $defaultDomain
     */
    public function __construct($defaultDomain = true)
    {
        if (true === $defaultDomain) {
            $defaultDomain = 'subscribo/transaction-plugin-manager';
        }
        $this->defaultDomain = $defaultDomain;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function addDefaultDomainToData(array $data)
    {
        if ( ! array_key_exists('domain', $data)) {
            $data['domain'] = $this->defaultDomain;
        }

        return $data;
    }
}
