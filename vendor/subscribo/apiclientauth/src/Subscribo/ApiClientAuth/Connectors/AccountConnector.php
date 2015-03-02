<?php namespace Subscribo\ApiClientAuth\Connectors;

use Subscribo\RestClient\Exceptions\InvalidResponseException;
use Subscribo\ApiClientCommon\AbstractConnector;

class AccountConnector extends AbstractConnector
{

    /**
     * @param int $id
     * @param array|null $signatureOptions
     * @return array|null
     */
    public function getDetail($id, array $signatureOptions = null)
    {
        $responseData = $this->restClient->process('account/detail/'.$id, 'GET', null, null, null, $signatureOptions, true);

        return $this->assembleResult($responseData, 'found');
    }

    /**
     * @param array $credentials
     * @param array|null $signatureOptions
     * @return array|null
     */
    public function postValidation(array $credentials, array $signatureOptions = null)
    {
        $responseData = $this->restClient->process('account/validation', 'POST', $credentials, null, null, $signatureOptions, true);

        return $this->assembleResult($responseData, 'validated');
    }

    /**
     * @param int $id
     * @param string $token
     * @param array|null $signatureOptions
     * @return array|null
     */
    public function getRemembered($id, $token, array $signatureOptions = null)
    {
        $responseData = $this->restClient->process('account/remembered/'.$id, 'GET', null, ['token' => $token], null, $signatureOptions, true);

        return $this->assembleResult($responseData, 'found');
    }

    /**
     * @param int $id
     * @param string $token
     * @param array|null $signatureOptions
     * @return array|null
     */
    public function putRemembered($id, $token, array $signatureOptions = null)
    {
        $responseData = $this->restClient->process('account/remembered/'.$id, 'PUT', ['token' => $token], null, null, $signatureOptions, true);

        return $this->assembleResult($responseData, 'remembered');
    }

    /**
     * @param array $data
     * @param array $signatureOptions
     * @return array|null
     */
    public function postRegistration(array $data, array $signatureOptions = null)
    {
        $responseData = $this->restClient->process('account/registration', 'POST', $data, null, null, $signatureOptions, false);

        return $this->assembleResult($responseData, 'registered');
    }

    public function resumePostRegistration(array $responseData)
    {
        return $this->assembleResult($responseData, 'registered');
    }


    /**
     * @param null|array $source
     * @param string|array $keyToCheck
     * @return array|null
     * @throws \Subscribo\RestClient\Exceptions\InvalidResponseException
     */
    protected function assembleResult($source, $keyToCheck = array())
    {
        if (empty($source)) {
            return null;
        }
        if ( ! is_array($source)) {
            throw new InvalidResponseException();
        }
        $keysToCheck = is_array($keyToCheck) ? $keyToCheck : array($keyToCheck);
        foreach ($keysToCheck as $key) {
            if (empty($source[$key])) {
                throw new InvalidResponseException();
            }
        }
        if (empty($source['result']['account']['id'])
          or empty($source['result']['customer']['email'])
          or ( ! isset($source['result']['person']['name']))) {
            throw new InvalidResponseException();
        }
        $result = [
            'id'    => $source['result']['account']['id'],
            'email' => $source['result']['customer']['email'],
            'name'  => $source['result']['person']['name'],
        ];
        return $result;
    }

}
