<?php namespace Subscribo\RestCommon\Interfaces;

use Illuminate\Contracts\Encryption\Encrypter;

interface CommonSecretProviderInterface
{
    /**
     * @return string|null
     */
    public function getCommonSecret();


    /**
     * @return Encrypter|null
     */
    public function getCommonSecretEncrypter();

    /**
     * @param $key
     * @return Encrypter|mixed
     */
    public function wrapWithEncrypter($key);

}
