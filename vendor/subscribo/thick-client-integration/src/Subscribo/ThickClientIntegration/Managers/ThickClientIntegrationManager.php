<?php

namespace Subscribo\ThickClientIntegration\Managers;

use RuntimeException;
use Illuminate\Container\Container;
use Webpatser\Uuid\Uuid;
use Subscribo\ClientIntegrationCommon\Interfaces\ClientIntegrationManagerInterface;
use Subscribo\RestCommon\RestCommon;
use Subscribo\Support\Str;

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
     * @var \Subscribo\Api1Connector\Connectors\AccountConnector|null
     */
    protected $accountConnector;

    /**
     * @var \Subscribo\Api1Connector\Connectors\AccountSimplifiedConnector|null
     */
    protected $accountSimplifiedConnector;

    /**
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * @return string
     */
    public static function getClientTokenAttributeName()
    {
        return 'subscribo_client_token';
    }

    /**
     * @return string
     */
    public static function getAccessTokenAttributeName()
    {
        return 'subscribo_access_token';
    }

    /**
     * @return string|null
     * @throws \RuntimeException
     */
    public function getAccountAccessToken()
    {
        $user = $this->getGuard()->user();
        if (empty($user)) {

            return null;
        }
        $accessTokenAttributeName = $this->getAccessTokenAttributeName();
        if ( ! empty($user->$accessTokenAttributeName)) {

            return $user->$accessTokenAttributeName;
        }
        $clientToken = static::ensureClientToken($user);
        if (empty($clientToken)) {

            throw new RuntimeException('Attempt to ensure subscribo client token on user failed');
        }
        $registrationData = static::assembleRegistrationData($user);
        $registrationResult = $this->getAccountSimplifiedConnector()->postRegistration($registrationData);
        $accountAccessToken = $this->getAccountSimplifiedConnector()->extractAccessTokenFromResult($registrationResult);
        if ($accountAccessToken) {
            $user->$accessTokenAttributeName = $accountAccessToken;
            $user->save();
        }

        return $accountAccessToken;
    }

    /**
     * @return string|null
     */
    public function getLocale()
    {
        $localizer = $this->getLocalizer();

        return $localizer ? $localizer->getLocale() : null;
    }

    /**
     * @return int
     */
    protected static function getMaxHashAttempts()
    {
        return 4;
    }

    /**
     * @return Uuid
     */
    protected static function generateHash()
    {
        return Uuid::generate(4);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $user
     * @return string|bool
     */
    protected static function ensureClientToken($user)
    {
        $clientTokenAttributeName = static::getClientTokenAttributeName();
        $clientTokenColumnName = Str::snake($clientTokenAttributeName);

        if ( ! empty($user->$clientTokenAttributeName)) {

            return $user->$clientTokenAttributeName;
        }

        for ($i = 0; $i < static::getMaxHashAttempts(); $i++) {
            $hash = static::generateHash();

            $alreadyUsed = $user->query()->where($clientTokenColumnName, $hash)->first();

            if (empty($alreadyUsed)) {
                $user->$clientTokenAttributeName = $hash;
                $user->save();

                return $user->$clientTokenAttributeName;
            }
        }

        return false;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $user
     * @return array
     */
    protected static function assembleRegistrationData($user)
    {
        $clientTokenAttributeName = static::getClientTokenAttributeName();
        $result = [
            'oauth' => [
                'provider' => RestCommon::OAUTH_PROVIDER_NAME_FOR_SUBSCRIBO_THICK_CLIENT,
                'identifier' => $user->$clientTokenAttributeName,
            ],
        ];
        $name = $user->getAttributeValue('name');
        if ($name) {
            $result['name'] = $name;
        }
        $email = $user->getAttributeValue('email');
        if ($email) {
            $result['email'] = $email;
        }

        return $result;
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
        if ($this->app->bound('Subscribo\\Localization\\Interfaces\\LocalizerInterface')) {
            $this->localizer = $this->app->make('Subscribo\\Localization\\Interfaces\\LocalizerInterface');
        }

        return $this->localizer;
    }

    /**
     * @return \Subscribo\Api1Connector\Connectors\AccountConnector
     */
    protected function getAccountConnector()
    {
        if ($this->accountConnector) {

            return $this->accountConnector;
        }
        $this->accountConnector = $this->app->make('Subscribo\\Api1Connector\\Connectors\\AccountConnector');

        return $this->accountConnector;
    }

    /**
     * @return mixed|null|\Subscribo\Api1Connector\Connectors\AccountSimplifiedConnector
     */
    protected function getAccountSimplifiedConnector()
    {
        if ($this->accountSimplifiedConnector) {

            return $this->accountSimplifiedConnector;
        }
        $this->accountSimplifiedConnector =
            $this->app->make('Subscribo\\Api1Connector\\Connectors\\AccountSimplifiedConnector');

        return $this->accountSimplifiedConnector;
    }
}
