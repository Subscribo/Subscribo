<?php namespace Subscribo\RestClient\Factories;

use Subscribo\Localization\Interfaces\LocalizerInterface;
use Subscribo\RestCommon\SignatureOptions;
use Illuminate\Contracts\Auth\Guard;

class SignatureOptionsFactory
{
    /** @var Guard  */
    protected $auth;

    /** @var  LocalizerInterface */
    protected $localizer;

    public function __construct(Guard $auth, LocalizerInterface $localizer)
    {
        $this->auth = $auth;
        $this->localizer = $localizer;
    }

    /**
     * @param array|bool|SignatureOptions $options true for defaults
     * @return SignatureOptions
     */
    public function generate($options = true)
    {
        if ($options instanceof SignatureOptions) {

            return $options;
        }
        $options = is_array($options) ? $options : array();
        $defaults = [
            'accountId' => true,
            'locale'    => true,
        ];
        $options = array_replace($defaults, $options);
        if (true === $options['accountId']) {
            $user = ($this->auth) ? $this->auth->user() : null;
            $options['accountId'] = $user ? $user->getAuthIdentifier() : false;
        }
        if ((true === $options['locale']) and ($this->localizer)) {
            $options['locale'] = $this->localizer->getLocale();
        }
        $result = new SignatureOptions($options);
        return $result;
    }

}
