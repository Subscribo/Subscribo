<?php namespace Subscribo\ApiClientCommon\Connectors;

use Subscribo\ApiClientCommon\AbstractConnector;
use Subscribo\RestCommon\Questionary;

class QuestionaryConnector extends AbstractConnector
{

    public function postAnswer(Questionary $questionary, array $data, array $signatureOptions = null)
    {
        $responseData = $this->restClient->process($questionary->endpoint, 'POST', ['answer' => $data], null, null, $signatureOptions, false);

        return $responseData;
    }

}
