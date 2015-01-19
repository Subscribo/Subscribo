<?php namespace Subscribo\Auth\Integration\Laravel;

use Subscribo\ServiceProvider\ServiceProvider;

class AuthServiceProvider extends ServiceProvider {

    public function register()
    {
        $this->app->singleton('Subscribo\\Auth\\Interfaces\\StatelessAuthenticatableFactoryInterface', 'Subscribo\\Auth\\Factories\\UserFactory');
        $this->app->singleton('subscribo.auth', 'Subscribo\\Auth\\Guards\\SimpleGuard');
        $this->app->singleton('auth', 'subscribo.auth');
        $this->app->singleton('Subscribo\\Auth\\Interfaces\\StatelessGuardInterface', 'subscribo.auth');

    }

}