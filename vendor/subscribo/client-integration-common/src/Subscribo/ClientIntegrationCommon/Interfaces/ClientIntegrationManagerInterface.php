<?php

namespace Subscribo\ClientIntegrationCommon\Interfaces;

/**
 * Interface ClientIntegrationManagerInterface
 *
 * @package Subscribo\ClientIntegrationCommon
 */
interface ClientIntegrationManagerInterface
{
    /**
     * @return int|null
     */
    public function getAccountAccessToken();

    /**
     * @return string|null
     */
    public function getLocale();
}
