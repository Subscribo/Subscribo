<?php namespace Subscribo\Auth\Traits;


trait StatelessToNonStatelessTrait {


    public function attempt(array $credentials = array(), $remember = false, $login = true)
    {
        if ($login) {
            return $this->once($credentials);
        } else {
            return $this->validate($credentials);
        }
    }

    public function basic($field = 'email')
    {
        return $this->onceBasic($field);
    }

    public function onceBasic($field = 'email')
    {
        $user = $this->user();
        return null;
    }

    public function viaRemember()
    {
        return false;
    }
}
