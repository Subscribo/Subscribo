<?php namespace Subscribo\RestClient\Factories;

use Subscribo\RestCommon\SignatureOptions;
use Subscribo\Support\Arr;
use Illuminate\Contracts\Auth\Guard;

class SignatureOptionsFactory
{
    /** @var \Illuminate\Contracts\Auth\Guard  */
    protected $auth;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param array|bool $options true for defaults
     * @return SignatureOptions
     */
    public function generate($options = true)
    {
        $options = is_array($options) ? $options : array();
        $defaults = [
            'accountId' => true,
            'lang'      => true,
        ];
        $options = Arr::mergeNatural($defaults, $options);
        if (true === $options['accountId']) {
            $user = ($this->auth) ? $this->auth->user() : null;
            $options['accountId'] = $user ? $user->getAuthIdentifier() : false;
        }
        if (true === $options['lang']) {
            $options['lang'] = 'DE_AT';
        }
        $result = new SignatureOptions($options);
        return $result;
    }

}
