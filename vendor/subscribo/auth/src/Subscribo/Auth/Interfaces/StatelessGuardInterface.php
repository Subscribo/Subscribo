<?php namespace Subscribo\Auth\Interfaces;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Class StatelessGuardInterface
 *
 * Contain Simplified interface for stateless authentication
 *
 * @package Subscribo\Auth
 */
interface StatelessGuardInterface {

    /**
     * @return bool
     */
    public function check();

    /**
     * @return bool
     */
    public function guest();

    /**
     * Returns currently logged in user
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user();

    /**
     * @param array $credentials
     * @return bool
     */
    public function once(array $credentials = array());

    /**
     * @param array $credentials
     * @return bool
     */
    public function validate(array $credentials = array());


    /**
     * @param Authenticatable $user
     * @return void
     */
    public function login(Authenticatable $user);

    /**
     * @param mixed $id
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function loginUsingId($id);

    /**
     * @return void
     */
    public function logout();

}

