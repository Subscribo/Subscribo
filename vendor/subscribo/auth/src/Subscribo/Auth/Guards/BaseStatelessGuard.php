<?php namespace Subscribo\Auth\Guards;

use Subscribo\Auth\Interfaces\StatelessGuardInterface;
use Subscribo\Auth\Interfaces\StatelessAuthenticatableFactoryInterface;
use Subscribo\Auth\Interfaces\CanBeGuestInterface;
use Illuminate\Contracts\Auth\Authenticatable;

class BaseStatelessGuard implements StatelessGuardInterface {

    /**
     * @var Authenticatable
     */
    protected $user;

    /**
     * @var StatelessAuthenticatableFactoryInterface
     */
    protected $userFactory;

    /**
     * @var bool
     */
    protected $loggedOut;

    public function __construct(StatelessAuthenticatableFactoryInterface $userFactory)
    {
        $this->userFactory = $userFactory;
    }

    public function check()
    {
        return ( ! $this->guest());
    }

    public function guest()
    {
        $user = $this->user();
        if (empty($user)) {
            return true;
        }
        if ($user instanceof CanBeGuestInterface)
        {
            return $user->isGuest();
        }
        return false;
    }

    public function user()
    {
        if ($this->loggedOut) {
            return null;
        }
        return $this->user;
    }

    public function once(array $credentials = array())
    {
        $user = $this->userFactory->retrieveByCredentials($credentials);
        if (empty($user)) {
            return false;
        }
        $valid = $this->userFactory->validateCredentials($user, $credentials);
        if ( ! $valid) {
            return false;
        }
        $this->user = $user;
        $this->loggedOut = false;
        return true;
    }


    public function validate(array $credentials = array())
    {
        $user = $this->userFactory->retrieveByCredentials($credentials);
        if (empty($user)) {
            return false;
        }
        $valid = $this->userFactory->validateCredentials($user, $credentials);
        return $valid;
    }

    public function login(Authenticatable $user, $remember = false)
    {
        $this->user = $user;
        $this->loggedOut = false;
    }

    public function loginUsingId($id, $remember = false)
    {
        $user = $this->userFactory->retrieveById($id);
        if ($user) {
            $this->loggedOut = false;
        }
        $this->user = $user;
        return $user;
    }

    public function logout()
    {
        $this->user = null;
        $this->loggedOut = true;
    }
}
