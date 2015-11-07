<?php

namespace Subscribo\ApiClientAuth;

use Subscribo\ApiClientAuth\Exceptions\InvalidArgumentException;
use Illuminate\Contracts\Auth\UserProvider;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Subscribo\Support\Arr;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Events\Dispatcher;
use Subscribo\ApiClientAuth\Connectors\AccountSimplifiedConnector;
use Psr\Log\LoggerInterface;

/**
 * Class RemoteAccountProvider
 * @package Subscribo\ApiClientAuth
 */
class RemoteAccountProvider implements UserProvider
{
    protected $session;

    protected $model;

    protected $sessionKeyName;

    protected $hasher;

    protected $accountConnector;

    protected $logger;

    public function __construct(SessionInterface $session, AccountSimplifiedConnector $accountConnector, Hasher $hasher, Dispatcher $dispatcher, LoggerInterface $logger, $model = '\\Subscribo\\ApiClientAuth\\Account', $sessionKeyName = true)
    {
        if (true === $sessionKeyName) {
            $sessionKeyName = 'account_session_provider_'.md5(get_class($this));
        }

        $this->session = $session;
        $this->model = $model;
        $this->sessionKeyName = $sessionKeyName;
        $this->hasher = $hasher;
        $this->accountConnector = $accountConnector;
        $this->logger = $logger;
        $dispatcher->listen('auth.logout', function ($event) {
            $this->clear();
        });

    }

    public function retrieveById($id)
    {
        if (empty($id)) {
            throw new InvalidArgumentException('Id (account access token) should not be empty');
        }
        $entity = $this->load($id);
        if ($entity) {
            return $entity;
        }
        $data = $this->accountConnector->getDetail($id);

        return $this->saveAsModelIfPossible($data, 'retrieveById');
    }

    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials['password'])) {
            return null;
        }
        if (empty($credentials['email'])) {
            return null;
        }
        $data = $this->accountConnector->postValidation($credentials);

        if (empty($data)) {
            return null;
        }
        $data['password'] = $this->hasher->make($this->assembleHashSource($credentials));

        return $this->saveAsModelIfPossible($data, 'retrieveByCredentials');
    }

    public function updateRememberToken(Authenticatable $account, $token)
    {
        $data = $this->accountConnector->putRemembered($account->getAuthIdentifier(), $token);

        if ($data) {
            $account->setRememberToken($token);
            $this->save($account->getAuthIdentifier(), $account);
        } else {
            $this->logger->notice('RemoteAccountProvider: It was not possible to updateRememberToken');
        }
    }

    public function retrieveByToken($id, $token)
    {
        if (empty($id)) {
            throw new InvalidArgumentException('Id (account access token) should not be empty');
        }

        $data = $this->accountConnector->getRemembered($id, $token);

        return $this->saveAsModelIfPossible($data, 'retrieveByToken');
    }

    public function validateCredentials(Authenticatable $account, array $credentials)
    {
        if (empty($credentials['password'])) {
            return false;
        }
        if (empty($credentials['email'])) {
            return false;
        }
        $hashed = $account->getAuthPassword();
        if (empty($hashed)) {
            return false;
        }
        $result = $this->hasher->check($this->assembleHashSource($credentials), $hashed);
        return $result;
    }

    private function saveAsModelIfPossible(array $data = null, $logMethodName = false)
    {
        /** @var Authenticatable $entity */
        $entity = new $this->model($data);
        $key = $entity->getAuthIdentifier();
        if ($key) {
            $this->save($key, $entity);

            return $entity;
        }
        if ($logMethodName) {
            $this->logger->notice(
                'RemoteAccountProvider: It was not possible to save the model.',
                ['methodName' => $logMethodName]
            );
        }

        return null;
    }

    protected function load($key)
    {
        $data = $this->session->get($this->sessionKeyName, []);
        return Arr::get($data, $key);
    }

    protected function save($key, $value)
    {
        $data = $this->session->get($this->sessionKeyName, []);
        Arr::set($data, $key, $value);
        $this->session->set($this->sessionKeyName, $data);
    }

    public function clear()
    {
        $this->session->set($this->sessionKeyName, []);
    }

    private function assembleHashSource(array $credentials)
    {
        return $credentials['email'].'_already_validated_'.$credentials['password'];
    }
}
