<?php namespace Subscribo\ApiClientAuth\Connectors;

use Subscribo\ApiClientAuth\Exceptions\ValidationException;
use Subscribo\RestClient\Exceptions\ClientErrorHttpException;
use Subscribo\RestClient\RestClient;
use Subscribo\RestClient\Exceptions\InvalidRemoteServerResponseHttpException;
use Subscribo\ApiClientAuth\QuestionList;

class AccountConnector
{
    /**
     * @var \Subscribo\RestClient\RestClient
     */
    protected $restClient;

    public function __construct(RestClient $restClient)
    {
        $this->restClient = $restClient;
    }

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
     * @return array|null|QuestionList
     * @throws \Subscribo\ApiClientAuth\Exceptions\ValidationException
     */
    public function postRegistration(array $data, array $signatureOptions = null)
    {
        try {
            $responseData = $this->restClient->process('account/registration', 'POST', $data, null, null, $signatureOptions, false);
        } catch (ClientErrorHttpException $e) {
            $data = $e->getErrorData();
            $validationErrors = empty($data['validationErrors']) ? ['Registration did not proceeded. Try different email or contact an administrator.'] : $data['validationErrors'];
            throw new ValidationException($validationErrors);
        }
        $account = $this->assembleResult($responseData, 'registered');
        if ($account) {
            return $account;
        }
        return $this->assembleQuestionList($responseData);
    }


    /**
     * @param null|array $source
     * @param string|array $keyToCheck
     * @return array|null
     * @throws \Subscribo\RestClient\Exceptions\InvalidRemoteServerResponseHttpException
     */
    protected function assembleResult($source, $keyToCheck = array())
    {
        if (empty($source)) {
            return null;
        }
        if ( ! is_array($source)) {
            throw new InvalidRemoteServerResponseHttpException();
        }
        $keysToCheck = is_array($keyToCheck) ? $keyToCheck : array($keyToCheck);
        foreach ($keysToCheck as $key) {
            if (empty($source[$key])) {
                return null;
            }
        }
        if (empty($source['result']['account']['id'])
          or empty($source['result']['customer']['email'])
          or ( ! isset($source['result']['person']['name']))) {
            throw new InvalidRemoteServerResponseHttpException();
        }
        $result = [
            'id'    => $source['result']['account']['id'],
            'email' => $source['result']['customer']['email'],
            'name'  => $source['result']['person']['name'],
        ];
        return $result;
    }

    /**
     * @param $source
     * @return null|QuestionList
     * @throws \Subscribo\RestClient\Exceptions\InvalidRemoteServerResponseHttpException
     */
    protected function assembleQuestionList($source)
    {
        if (empty($source)) {
            return null;
        }
        if ( ! is_array($source)) {
            throw new InvalidRemoteServerResponseHttpException();
        }
        if (empty($source['asking'])) {
            return null;
        }
        if (( ! array_key_exists('questions', $source) or ( ! is_array($source['questions'])))) {
            throw new InvalidRemoteServerResponseHttpException();
        }
        return new QuestionList($source['questions']);

    }
}
