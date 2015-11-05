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
    public function getAccountId();

    /**
     * @return string|null
     */
    public function getLocale();
}
