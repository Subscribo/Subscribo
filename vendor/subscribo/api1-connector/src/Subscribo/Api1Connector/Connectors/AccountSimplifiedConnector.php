<?php

namespace Subscribo\Api1Connector\Connectors;

use Subscribo\ApiClientCommon\AbstractConnector;
use Subscribo\RestClient\Exceptions\InvalidResponseException;
use Subscribo\RestCommon\SignatureOptions;

/**
 * Class AccountSimplifiedConnector Reduced functionality connector without user information
 *                                  to be used by RemoteAccountProvider
 *
 * @package Subscribo\Api1Connector
 */
class AccountSimplifiedConnector extends AbstractConnector
{
    /**
     * @param string $accountAccessToken
     * @param SignatureOptions|array|bool $signatureOptions
     * @return array|null
     */
    public function getDetail($accountAccessToken, $signatureOptions = true)
    {
        $signatureOptions = $this->processSignatureOptions($signatureOptions);

        $responseData = $this->restClient->process('account/detail/'.$accountAccessToken, 'GET', null, null, null, $signatureOptions, true);

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
     * @param string $accountAccessToken
     * @param string $rememberMeToken
     * @param SignatureOptions|array|bool $signatureOptions
     * @return array|null
     */
    public function getRemembered($accountAccessToken, $rememberMeToken, $signatureOptions = true)
    {
        $signatureOptions = $this->processSignatureOptions($signatureOptions);

        $responseData = $this->restClient->process('account/remembered/'.$accountAccessToken, 'GET', null, ['token' => $rememberMeToken], null, $signatureOptions, true);

        return $this->assembleResult($responseData, 'found');
    }

    /**
     * @param string $accountAccessToken
     * @param string $rememberMeToken
     * @param SignatureOptions|array|bool $signatureOptions
     * @return array|null
     */
    public function putRemembered($accountAccessToken, $rememberMeToken, $signatureOptions = true)
    {
        $signatureOptions = $this->processSignatureOptions($signatureOptions);

        $responseData = $this->restClient->process('account/remembered/'.$accountAccessToken, 'PUT', ['token' => $rememberMeToken], null, null, $signatureOptions, true);

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
        if (empty($source['result']['account']['access_token'])
          or empty($source['result']['customer']['email'])
          or ( ! isset($source['result']['person']['name']))
          or ( ! isset($source['result']['account']['locale']))
          or ( ! isset($source['result']['account']['remember_locale']))
        ) {
            throw new InvalidResponseException(['response' => $source]);
        }
        $result = [
            'accessToken' => $source['result']['account']['access_token'],
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
        unset($defaultOptions['accountAccessToken']);
        $this->signatureOptionsFactory->setDefaultSignatureOptions($defaultOptions);
    }
}
