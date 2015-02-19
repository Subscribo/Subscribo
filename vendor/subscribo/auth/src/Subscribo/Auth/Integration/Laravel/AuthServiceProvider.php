<?php namespace Subscribo\Auth\Integration\Laravel;

use Subscribo\ServiceProvider\ServiceProvider;

class AuthServiceProvider extends ServiceProvider {

    public function register()
    {
        $this->app->register('Subscribo\\RestCommon\\Integration\\Laravel\\CommonSecretServiceProvider');

        $this->app->singleton('Subscribo\\Auth\\Factories\\UserFactory');
        $this->app->singleton('Subscribo\\Auth\\Interfaces\\StatelessAuthenticatableFactoryInterface', 'Subscribo\\Auth\\Factories\\UserFactory');
        $this->app->singleton('Subscribo\\RestCommon\\Interfaces\\ByTokenIdentifiableFactoryInterface', 'Subscribo\\Auth\\Factories\\UserFactory');

        $this->app->when('Subscribo\\Auth\\Guards\\SimpleGuard')->needs('Symfony\\Component\\HttpFoundation\\Request')->give('Illuminate\\Http\\Request');
        $this->app->when('Subscribo\\Auth\\Guards\\SubscriboGuard')->needs('Symfony\\Component\\HttpFoundation\\Request')->give('Illuminate\\Http\\Request');
        $this->app->when('Subscribo\\Auth\\Guards\\SubscriboBasicGuard')->needs('Symfony\\Component\\HttpFoundation\\Request')->give('Illuminate\\Http\\Request');
        $this->app->when('Subscribo\\Auth\\Guards\\SubscriboDigestGuard')->needs('Symfony\\Component\\HttpFoundation\\Request')->give('Illuminate\\Http\\Request');

        $this->app->singleton('subscribo.auth', 'Subscribo\\Auth\\Guards\\SubscriboGuard');

    //    $this->app->singleton('auth', 'subscribo.auth');
        $this->app->singleton('Subscribo\\Auth\\Interfaces\\StatelessGuardInterface', 'subscribo.auth');
        $this->app->singleton('Subscribo\\Auth\\Interfaces\\ApiGuardInterface', 'subscribo.auth');

    }
}
