<?php namespace Subscribo\ApiClientCommon\Connectors;

use Subscribo\ApiClientCommon\AbstractConnector;
use Subscribo\RestCommon\ServerRequest;
use Subscribo\RestCommon\SignatureOptions;
use Subscribo\RestClient\Exceptions\InvalidResponseException;


class ServerRequestConnector extends AbstractConnector
{

    public function postAnswer(ServerRequest $serverRequest, array $data, $signatureOptions = true)
    {
        $signatureOptions = $this->processSignatureOptions($signatureOptions);

        $responseData = $this->restClient->process($serverRequest->endpoint, 'POST', ['answer' => $data], null, null, $signatureOptions, false);

        return $responseData;
    }

    public function getQuestionary($type, array $query = array(), $signatureOptions = true)
    {
        $signatureOptions = $this->processSignatureOptions($signatureOptions);

        $responseData = $this->restClient->process('answer/question/'.$type, 'GET', null, $query, null, $signatureOptions, false);

        if (empty($responseData['result']['endpoint'])) {
            throw new InvalidResponseException();
        }
        return $responseData['result'];
    }

    public function getRedirection($type, array $query = array(), $signatureOptions = true)
    {
        $signatureOptions = $this->processSignatureOptions($signatureOptions);

        $responseData = $this->restClient->process('answer/redirection/'.$type, 'GET', null, $query, null, $signatureOptions, false);

        if (empty($responseData['result']['urlPattern']) and empty($responseData['result']['urlSimple'])) {
            throw new InvalidResponseException();
        }
        return $responseData['result'];
    }

    public function resumeGetRedirection(array $responseData)
    {
        if (empty($responseData['result']['urlPattern']) and empty($responseData['result']['urlSimple'])) {
            throw new InvalidResponseException();
        }
        return $responseData['result'];
    }

}
