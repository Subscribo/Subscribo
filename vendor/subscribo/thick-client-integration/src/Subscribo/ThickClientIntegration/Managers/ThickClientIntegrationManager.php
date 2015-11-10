<?php

namespace Subscribo\ThickClientIntegration\Managers;

use Subscribo\ClientIntegrationCommon\Interfaces\ClientIntegrationManagerInterface;
use Illuminate\Container\Container;

/**
 * Class ThickClientIntegrationManager
 * @package Subscribo\ThickClientIntegration
 */
class ThickClientIntegrationManager implements ClientIntegrationManagerInterface
{
    /**
     * @var \Illuminate\Container\Container
     */
    protected $app;

    /**
     * @var \Illuminate\Contracts\Auth\Guard|null
     */
    protected $guard;

    /**
     * @var \Subscribo\Localization\Interfaces\LocalizerInterface|null
     */
    protected $localizer;

    /**
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * @return int|null
     */
    public function getAccountAccessToken()
    {
        $user = $this->getGuard()->user();

        return $user ? $user->getAuthIdentifier() : null;
    }

    /**
     * @return string|null
     */
    public function getLocale()
    {
        return $this->getLocalizer()->getLocale();
    }

    /**
     * @return \Illuminate\Contracts\Auth\Guard
     */
    protected function getGuard()
    {
        if ($this->guard) {

            return $this->guard;
        }
        $this->guard = $this->app->make('Illuminate\\Contracts\\Auth\\Guard');

        return $this->guard;
    }

    /**
     * @return \Subscribo\Localization\Interfaces\LocalizerInterface
     */
    protected function getLocalizer()
    {
        if ($this->localizer) {

            return $this->localizer;
        }
        $this->localizer = $this->app->make('Subscribo\\Localization\\Interfaces\\LocalizerInterface');

        return $this->localizer;
    }
}
