<?php namespace Subscribo\Api1\Traits;

use Subscribo\ModelCore\Models\ActionInterruption;
use Subscribo\RestCommon\ServerRequest;
use Subscribo\RestCommon\Questionary;
use Subscribo\RestCommon\ClientRedirection;
use Subscribo\RestCommon\Exceptions\QuestionaryServerRequestHttpException;
use Subscribo\RestCommon\Exceptions\ClientRedirectionServerRequestHttpException;
use Subscribo\Api1\Factories\QuestionaryFactory;
use Subscribo\Api1\Factories\ClientRedirectionFactory;
use Subscribo\Api1\Context;

/**
 * Trait ServerRequestEnabledControllerTrait
 *
 * Class using this trait need to have property $context of type Context
 *
 * @package Subscribo\Api1
 */
trait ServerRequestEnabledControllerTrait
{

    /**
     * Wrapper for makeQuestionaryServerRequestHttpException with a shorter name
     *
     * @param Questionary|array|string|int $source
     * @param array $dataToRemember
     * @param string $continueMethod
     * @param array $additionalDataForQuestionary
     * @return QuestionaryServerRequestHttpException
     */
    protected function makeQuestion($source, array $dataToRemember = array(), $continueMethod = 'receiveAnswer', array $additionalDataForQuestionary = array())
    {
        return $this->makeQuestionaryServerRequestHttpException($source, $dataToRemember, $continueMethod, $additionalDataForQuestionary);
    }

    /**
     * @param Questionary|array|string|int $source
     * @param array $dataToRemember
     * @param string $continueMethod
     * @param array $additionalDataForQuestionary
     * @return QuestionaryServerRequestHttpException
     */
    protected function makeQuestionaryServerRequestHttpException($source, array $dataToRemember = array(), $continueMethod = 'receiveAnswer', array $additionalDataForQuestionary = array())
    {
        $questionaryFactory = new QuestionaryFactory($this->context->getLocalizer());
        $questionary = $questionaryFactory->make($source, $additionalDataForQuestionary);
        $this->rememberServerRequest($questionary, 'answer/question/', $dataToRemember, $continueMethod);
        $exception = new QuestionaryServerRequestHttpException($questionary);
        return $exception;
    }

    /**
     * Wrapper for makeClientRedirectionServerRequestHttpException with a shorter name
     *
     * @param ClientRedirection|array|string|int $source
     * @param array $dataToRemember
     * @param string $continueMethod
     * @param array $additionalDataForClientRedirect
     * @return ClientRedirectionServerRequestHttpException
     */
    protected function makeClientRedirect($source, array $dataToRemember = array(), $continueMethod = 'receiveAnswer', array $additionalDataForClientRedirect = array())
    {
        return $this->makeClientRedirectionServerRequestHttpException($source, $dataToRemember, $continueMethod, $additionalDataForClientRedirect);
    }

    /**
     * @param ClientRedirection|array|string|int $source
     * @param array $dataToRemember
     * @param string $continueMethod
     * @param array $additionalDataForClientRedirect
     * @return ClientRedirectionServerRequestHttpException
     */
    protected function makeClientRedirectionServerRequestHttpException($source, array $dataToRemember = array(), $continueMethod = 'receiveAnswer', array $additionalDataForClientRedirect = array())
    {
        $clientRedirection = $this->prepareClientRedirection($source, $dataToRemember, $continueMethod, $additionalDataForClientRedirect);
        $exception = new ClientRedirectionServerRequestHttpException($clientRedirection);
        return $exception;
    }

    /**
     * @param ClientRedirection|array|string|int $source
     * @param array $dataToRemember
     * @param string $continueMethod
     * @param array $additionalDataForClientRedirect
     * @return ClientRedirection
     */
    protected function prepareClientRedirection($source, array $dataToRemember = array(), $continueMethod = 'receiveAnswer', array $additionalDataForClientRedirect = array())
    {
        $clientRedirection = ClientRedirectionFactory::make($source, $additionalDataForClientRedirect);
        $this->rememberServerRequest($clientRedirection, 'answer/redirection/', $dataToRemember, $continueMethod);
        return $clientRedirection;
    }

    /**
     * Generates ActionInterruption DB record and set up hash and endpoint in serverRequest
     *
     * Note: This method modifies its argument object $serverRequest
     *
     * @param ServerRequest $serverRequest this object is modified by this method
     * @param string $endpointBase
     * @param array $extraData
     * @param string $continueMethod
     * @return ActionInterruption
     */
    protected function rememberServerRequest(ServerRequest $serverRequest, $endpointBase, array $extraData = array(), $continueMethod = 'receiveAnswer')
    {
        if (is_null($continueMethod)) {
            $continueMethod = 'receiveAnswer';
        }
        if (false === strpos($continueMethod, '@')) {
            $continueMethod = get_class($this).'@'.$continueMethod;
        }
        $actionInterruption = ActionInterruption::make();
        $serverRequest->hash = $actionInterruption->hash;
        $serverRequest->endpoint = $endpointBase.$serverRequest->hash;
        $actionInterruption->serverRequest = $serverRequest;
        $actionInterruption->extraData = $extraData;
        $actionInterruption->continueMethod = $continueMethod;
        $actionInterruption->accountId = $this->context->getAccountId();
        $actionInterruption->serviceId = $this->context->getServiceId();
        $actionInterruption->type = $serverRequest->getType();
        $actionInterruption->save();
        return $actionInterruption;
    }
}
