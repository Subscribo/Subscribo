<?php namespace Subscribo\RestClient\Factories;

use Subscribo\RestCommon\SignatureOptions;
use Subscribo\ClientIntegrationCommon\Interfaces\ClientIntegrationManagerInterface;

class SignatureOptionsFactory
{
    /** @var \Subscribo\ClientIntegrationCommon\Interfaces\ClientIntegrationManagerInterface  */
    protected $manager;

    /** @var array */
    protected $defaultSignatureOptions = [
        'accountId' => true,
        'locale'    => true,
    ];

    /**
     * @param ClientIntegrationManagerInterface $manager
     */
    public function __construct(ClientIntegrationManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setDefaultSignatureOptions(array $options)
    {
        $this->defaultSignatureOptions = $options;

        return $this;
    }

    /**
     * @return array
     */
    public function getDefaultSignatureOptions()
    {
        return $this->defaultSignatureOptions;
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
        $options = array_replace($this->defaultSignatureOptions, $options);

        if (isset($options['accountId']) and (true === $options['accountId'])) {
            $options['accountId'] = $this->manager->getAccountId();
        }
        if (isset($options['locale']) and (true === $options['locale'])) {
            $options['locale'] = $this->manager->getLocale();
        }

        return new SignatureOptions($options);
    }
}
