<?php

namespace Subscribo\ApiClientAuth\Connectors;

use Subscribo\ApiClientCommon\AbstractConnector;
use Subscribo\RestClient\Exceptions\InvalidResponseException;
use Subscribo\RestCommon\SignatureOptions;

/**
 * Class AccountSimplifiedConnector Reduced functionality connector without user information
 *                                  to be used by RemoteAccountProvider
 *
 * @package Subscribo\ApiClientAuth\Connectors
 */
class AccountSimplifiedConnector extends AbstractConnector
{
    /**
     * @param int $id
     * @param SignatureOptions|array|bool $signatureOptions
     * @return array|null
     */
    public function getDetail($id, $signatureOptions = true)
    {
        $signatureOptions = $this->processSignatureOptions($signatureOptions);

        $responseData = $this->restClient->process('account/detail/'.$id, 'GET', null, null, null, $signatureOptions, true);

        return $this->assembleResult($responseData, 'found');
    }

    /**
     * @param array $credentials
     * @param SignatureOptions|array|bool $signatureOptions
     * @return array|null
     */
    public function postValidation(array $credentials, $signatureOptions = true)
    {
        $signatureOptions = $this->processSignatureOptions($signatureOptions);

        $responseData = $this->restClient->process('account/validation', 'POST', $credentials, null, null, $signatureOptions, false);

        return $this->assembleResult($responseData, 'validated');
    }

    /**
     * @param int $id
     * @param string $token
     * @param SignatureOptions|array|bool $signatureOptions
     * @return array|null
     */
    public function getRemembered($id, $token, $signatureOptions = true)
    {
        $signatureOptions = $this->processSignatureOptions($signatureOptions);

        $responseData = $this->restClient->process('account/remembered/'.$id, 'GET', null, ['token' => $token], null, $signatureOptions, true);

        return $this->assembleResult($responseData, 'found');
    }

    /**
     * @param int $id
     * @param string $token
     * @param SignatureOptions|array|bool $signatureOptions
     * @return array|null
     */
    public function putRemembered($id, $token, $signatureOptions = true)
    {
        $signatureOptions = $this->processSignatureOptions($signatureOptions);

        $responseData = $this->restClient->process('account/remembered/'.$id, 'PUT', ['token' => $token], null, null, $signatureOptions, true);

        return $this->assembleResult($responseData, 'remembered');
    }

    /**
     * @param array $data
     * @param SignatureOptions|array|bool $signatureOptions
     * @return array|null
     */
    public function postRegistration(array $data, $signatureOptions = true)
    {
        return $this->processPostRegistrationRawResponse($this->postRegistrationRaw($data, $signatureOptions));
    }

    /**
     * @param array $data
     * @param SignatureOptions|array|bool $signatureOptions
     * @return array|null
     */
    public function postRegistrationRaw(array $data, $signatureOptions = true)
    {
        $signatureOptions = $this->processSignatureOptions($signatureOptions);

        return $this->restClient->process('account/registration', 'POST', $data, null, null, $signatureOptions, false);
    }

    /**
     * @param array $responseData
     * @return array|null
     */
    public function processPostRegistrationRawResponse(array $responseData)
    {
        return $this->assembleResult($responseData, 'registered');
    }

    /**
     * @param null|array $source
     * @param string|array $keyToCheck
     * @return array|null
     * @throws \Subscribo\RestClient\Exceptions\InvalidResponseException
     */
    public static function assembleResult($source, $keyToCheck = array())
    {
        if (empty($source)) {
            return null;
        }
        if ( ! is_array($source)) {
            throw new InvalidResponseException(['response' => 'Not array']);
        }
        $keysToCheck = is_array($keyToCheck) ? $keyToCheck : array($keyToCheck);
        foreach ($keysToCheck as $key) {
            if (empty($source[$key])) {
                return null;
            }
        }
        if (empty($source['result']['account']['id'])
          or empty($source['result']['customer']['email'])
          or ( ! isset($source['result']['person']['name']))
          or ( ! isset($source['result']['account']['locale']))
          or ( ! isset($source['result']['account']['remember_locale']))
        ) {
            throw new InvalidResponseException(['response' => $source]);
        }
        $result = [
            'id'    => $source['result']['account']['id'],
            'email' => $source['result']['customer']['email'],
            'name'  => $source['result']['person']['name'],
            'locale' => $source['result']['account']['locale'],
            'rememberLocale' =>  $source['result']['account']['remember_locale'],
        ];
        return $result;
    }


    protected function initialize()
    {
        $defaultOptions = $this->signatureOptionsFactory->getDefaultSignatureOptions();
        unset($defaultOptions['accountId']);
        $this->signatureOptionsFactory->setDefaultSignatureOptions($defaultOptions);
    }
}
