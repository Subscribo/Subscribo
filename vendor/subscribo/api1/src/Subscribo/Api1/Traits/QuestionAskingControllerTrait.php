<?php namespace Subscribo\Api1\Traits;

use Subscribo\ModelCore\Models\ActionInterruption;
use Subscribo\RestCommon\Questionary;
use Subscribo\RestCommon\Exceptions\QuestionaryServerRequestHttpException;
use Subscribo\Api1\Factories\QuestionaryFactory;
use Subscribo\Api1\Context;

/**
 * Trait QuestionAskingControllerTrait
 *
 * Class using this trait need to have property $context of type Context
 *
 * @package Subscribo\Api1
 */
trait QuestionAskingControllerTrait
{
    /**
     * Throws an exception to request an answer from client
     * Note: return phpDoc is set in order to allow constructions like return $this->askQuestion() clearly denoting end of function processing, but not being marked by IDE as incorrect
     *
     * @param Questionary|array|string|int $source
     * @param array $extraData
     * @param string $continueMethod
     * @return null Actually does not returns anything, always throws exception
     * @throws \Subscribo\RestCommon\Exceptions\QuestionaryServerRequestHttpException
     */
    protected function askQuestion($source, array $extraData = array(), $continueMethod = 'receiveAnswer')
    {
        throw $this->makeQuestionaryServerRequestHttpException($source, $extraData, $continueMethod);
    }

    /**
     * Wrapper for makeQuestionaryServerRequestHttpException with shorter name
     *
     * @param $source
     * @param array $extraData
     * @param string $continueMethod
     * @return QuestionaryServerRequestHttpException
     */
    protected function makeQuestion($source, array $extraData = array(), $continueMethod = 'receiveAnswer')
    {
        return $this->makeQuestionaryServerRequestHttpException($source, $extraData, $continueMethod);
    }

    /**
     * @param Questionary|array|string|int $source
     * @param array $extraData
     * @param string $continueMethod
     * @return QuestionaryServerRequestHttpException
     */
    protected function makeQuestionaryServerRequestHttpException($source, array $extraData = array(), $continueMethod = 'receiveAnswer')
    {
        $questionary = $this->prepareQuestionary($source, $extraData, $continueMethod);
        $exception = new QuestionaryServerRequestHttpException($questionary);
        return $exception;
    }

    /**
     * @param Questionary|array|string|int $source
     * @param array $extraData
     * @param string $continueMethod
     * @return Questionary
     */
    protected function prepareQuestionary($source, array $extraData = array(), $continueMethod = 'receiveAnswer')
    {
        $questionary = QuestionaryFactory::make($source);
        if (is_null($continueMethod)) {
            $continueMethod = 'receiveAnswer';
        }
        if (false === strpos($continueMethod, '@')) {
            $continueMethod = get_class($this).'@'.$continueMethod;
        }
        $actionInterruption = ActionInterruption::make();
        $questionary->hash = $actionInterruption->hash;
        $questionary->endpoint = 'questionary/answer/'.$questionary->hash;
        $actionInterruption->questionary = $questionary;
        $actionInterruption->extraData = $extraData;
        $actionInterruption->continueMethod = $continueMethod;
        $actionInterruption->accountId = $this->context->getAccountId();
        $actionInterruption->serviceId = $this->context->getServiceId();
        $actionInterruption->save();
        return $questionary;
    }
}
