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
     * @param Questionary|array|string|int $source
     * @param array $extraData
     * @param string $continueMethod
     * @throws \Subscribo\RestCommon\Exceptions\QuestionaryServerRequestHttpException
     */
    protected function askQuestion($source, array $extraData = array(), $continueMethod = 'receiveAnswer')
    {
        $questionary = $this->prepareQuestionary($source, $extraData, $continueMethod);
        $exception = new QuestionaryServerRequestHttpException($questionary);
        throw $exception;
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
