<?php

namespace Subscribo\ApiClientAuth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\GenericUser;

/**
 * Class Account
 * @package Subscribo\ApiClientAuth
 */
class Account extends GenericUser implements Authenticatable
{
    /**
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
        if (empty($attributes['accessToken'])) {
            $this->attributes['accessToken'] = null;
        }
        if (empty($attributes['password'])) {
            $this->attributes['password'] = null;
        }
        if (empty($attributes[$this->getRememberTokenName()])) {
            $this->attributes[$this->getRememberTokenName()] = null;
        }
        if (empty($attributes['name'])) {
            $this->attributes['name'] = null;
        }
        if (empty($attributes['email'])) {
            $this->attributes['email'] = null;
        }
    }

    /**
     * @return string|null
     */
    public function getAuthIdentifier()
    {
        return $this->attributes['accessToken'];
    }
}
