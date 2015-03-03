<?php namespace Subscribo\RestCommon;

use Subscribo\RestCommon\Interfaces\CommonSecretProviderInterface;
use Illuminate\Contracts\Encryption\Encrypter;

class CommonSecretProvider implements CommonSecretProviderInterface
{

    const DEFAULT_ENCRYPTER = '\\Illuminate\\Encryption\\Encrypter';

    /**
     * @var string|null
     */
    protected $commonSecret;

    /**
     * @var string
     */
    protected $encrypterClassName;

    /**
     * @param string|null $commonSecret
     * @param string|bool $encrypterClassName
     */
    public function __construct($commonSecret, $encrypterClassName = true)
    {
        $this->commonSecret = $commonSecret;
        if (true === $encrypterClassName)
        {
            $encrypterClassName = $this::DEFAULT_ENCRYPTER;
        }
        $this->encrypterClassName = $encrypterClassName;
    }

    /**
     * @return string
     */
    public function getCommonSecret()
    {
        return $this->commonSecret;
    }

    /**
     * @return Encrypter|null|mixed
     */
    public function getCommonSecretEncrypter()
    {
        $commonSecret = $this->getCommonSecret();
        if (empty($commonSecret)) {
            return null;
        }
        $result = $this->wrapWithEncrypter($commonSecret);
        return $result;
    }

    /**
     * @param $key
     * @return Encrypter|mixed
     */
    public function wrapWithEncrypter($key)
    {
        $encrypterClass = $this->encrypterClassName;
        $result = new $encrypterClass($key);
        return $result;
    }

}
