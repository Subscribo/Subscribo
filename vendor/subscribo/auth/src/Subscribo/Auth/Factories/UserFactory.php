<?php namespace Subscribo\Auth\Factories;

use Subscribo\Auth\Interfaces\StatelessAuthenticatableFactoryInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use InvalidArgumentException;
use Hash;
use Model\User;

class UserFactory implements StatelessAuthenticatableFactoryInterface {

    /**
     * @param mixed $id
     * @return \Model\User|Authenticatable
     * @throws \InvalidArgumentException
     */
    public function retrieveById($id)
    {
        if ( ! is_numeric($id)) {
            throw new InvalidArgumentException('Id of an user should be numeric.');
        }
        $id = intval($id);
        $user = User::find($id);
        return $user;
    }

    /**
     * @param array $credentials
     * @return \Model\User|Authenticatable
     * @throws \InvalidArgumentException
     */
    public function retrieveByCredentials(array $credentials)
    {
        $email = isset($credentials['email']) ? $credentials['email'] : null;
        $username = isset($credentials['username']) ? $credentials['username'] : null;
        if (empty($email) and empty($username)) {
            throw new InvalidArgumentException('Both username and email are empty');
        }
        $query = User::query();
        if ($email) {
            $query->where('email', $email);
        } else {
            $query->where('username', $username);
        }
        $user = $query->first();
        return $user;
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        if (empty($credentials['password'])) {
            throw new InvalidArgumentException('Credentials does not contain password or password is empty');
        }
        $userPass = $user->getAuthPassword();
        $result = Hash::check($credentials['password'], $userPass);
        return $result;

    }
}
