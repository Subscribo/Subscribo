<?php namespace Subscribo\Auth\Interfaces;

use Illuminate\Contracts\Auth\Authenticatable;

interface StatelessAuthenticatableFactoryInterface {


    /**
     * @param mixed $id
     * @return Authenticatable
     */
    public function retrieveById($id);

    /**
     * @param array $credentials
     * @return Authenticatable
     */
    public function retrieveByCredentials(array $credentials);


    /**
     * @param Authenticatable $user
     * @param array $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials);


}