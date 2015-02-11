<?php namespace Subscribo\ApiClientAuth;

use Exception;
use Subscribo\ApiClientAuth\Exceptions\InvalidArgumentException;
use Illuminate\Contracts\Auth\UserProvider;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Subscribo\Support\Arr;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Events\Dispatcher;
use Subscribo\RestClient\RestClient;

class RemoteAccountProvider implements UserProvider
{
    protected $session;

    protected $model;

    protected $sessionKeyName;

    protected $hasher;

    protected $restClient;

    public function __construct(SessionInterface $session, RestClient $restClient, Hasher $hasher, Dispatcher $dispatcher, $model = '\\Subscribo\\ApiClientAuth\\Account', $sessionKeyName = true)
    {
        if (true === $sessionKeyName) {
            $sessionKeyName = 'account_session_provider_'.md5(get_class($this));
        }

        $this->session = $session;
        $this->model = $model;
        $this->sessionKeyName = $sessionKeyName;
        $this->hasher = $hasher;
        $this->restClient = $restClient;
        $dispatcher->listen('auth.logout', function ($event) {
            $this->clear();
        });

    }

    public function retrieveById($id)
    {
        if (empty($id)) {
            throw new InvalidArgumentException('Id should not be empty');
        }
        $entity = $this->load($id);
        if ($entity) {
            return $entity;
        }
        try {
            $responseResult = $this->restClient->process('customer/info/'.$id, 'GET');
        } catch (Exception $e) {
            return null;
        }

    }

    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials['password'])) {
            return null;
        }
        if (empty($credentials['email'])) {
            return null;
        }
        try {
            $responseResult = $this->restClient->process('customer/validate', 'GET', $credentials);
        } catch (Exception $e) {
            return null;
        }
        $responseData = json_decode($responseResult->getContent(), true);
        if (empty($responseData['validated']['account']['id'])) {
            return null;
        }
        $result = new $this->model([
            'id' => $responseData['validated']['account']['id'],
            'password' => $this->hasher->make($this->assembleHashSource($credentials)),
            'email' => $responseData['validated']['customer']['email'],
            'name' =>  $responseData['validated']['customer']['email'],
        ]);
        $this->save($responseData['validated']['account']['id'], $result);
        return $result;
    }

    public function updateRememberToken(Authenticatable $account, $token)
    {
        $account->setRememberToken($token);
        $this->save($account->getAuthIdentifier(), $account);
        try {
            $responseResult = $this->restClient->process('customer/remember', 'POST', null, [], json_encode(['id' => $account->getAuthIdentifier(), 'token' => $token]));
        } catch (Exception $e) {
            return null;
        }
    }

    public function retrieveByToken($id, $token)
    {
        try {
            $responseResult = $this->restClient->process('customer/retrieve', 'GET', ['id' =>$id, 'token' => $token]);
        } catch (Exception $e) {
            return null;
        }
        $responseData = json_decode($responseResult->getContent(), true);
        if (empty($responseData['found']['account']['id'])) {
            return null;
        }
        $result = new $this->model([
            'id' => $responseData['found']['account']['id'],
            'email' => $responseData['found']['customer']['email'],
            'name' =>  $responseData['found']['customer']['email'],
        ]);
        $this->save($responseData['found']['account']['id'], $result);
        return $result;
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