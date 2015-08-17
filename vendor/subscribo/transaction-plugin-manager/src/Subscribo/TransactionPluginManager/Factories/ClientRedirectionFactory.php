<?php

namespace Subscribo\TransactionPluginManager\Factories;

use InvalidArgumentException;
use Subscribo\RestCommon\ClientRedirection;
use Subscribo\TransactionPluginManager\Factories\AbstractServerRequestFactory;

/**
 * Class ClientRedirectionFactory
 *
 * @package Subscribo\TransactionPluginManager
 */
class ClientRedirectionFactory extends AbstractServerRequestFactory
{
    /**
     * @param ClientRedirection|string|array $clientRedirection
     * @return ClientRedirection
     * @throws \InvalidArgumentException
     */
    public function make($clientRedirection)
    {
        if ($clientRedirection instanceof Clientredirection) {

            return $clientRedirection;
        } elseif (is_string($clientRedirection)) {

            return $this->assembleFromString($clientRedirection);
        } elseif (is_array($clientRedirection)) {

            return $this->assembleFromArray($clientRedirection);
        } else {

            throw new InvalidArgumentException('Invalid clientRedirection argument type');
        }
    }

    /**
     * @param array $data
     * @return ClientRedirection
     */
    protected function assembleFromArray(array $data)
    {
        return new ClientRedirection($this->addDefaultDomainToData($data));
    }

    /**
     * @param string $url
     * @return ClientRedirection
     */
    protected function assembleFromString($url)
    {
        $data = [];
        if ((false !== strpos($url, '{hash}'))
            or (false !== strpos($url, '{redirect_back}'))) {
            $data['urlPattern'] = $url;
        } else {
            $data['urlSimple'] = $url;
        }
        if (false !== strpos($url, '{redirect_back}')) {
            $data['remember'] = true;
        } else {
            $data['remember'] = false;
        }

        return $this->assembleFromArray($data);
    }
}
