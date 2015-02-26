<?php namespace Subscribo\Api1\Controllers;

use RuntimeException;
use Subscribo\Api1\AbstractController;
use Subscribo\Exception\Exceptions\InvalidInputHttpException;
use Subscribo\Exception\Exceptions\WrongAccountHttpException;
use Subscribo\RestCommon\Questionary;
use Subscribo\ModelCore\Models\ActionInterruption;
use Subscribo\Exception\Exceptions\InstanceNotFoundHttpException;
use Subscribo\Exception\Exceptions\WrongServiceHttpException;


class QuestionaryController extends AbstractController
{
    public function actionPostAnswer($hash)
    {
        $validatedData = $this->validateRequestBody(['answer' => 'required|array']);
        $actionInterruption = ActionInterruption::findByHash($hash);
        if (empty($actionInterruption)) {
            throw new InstanceNotFoundHttpException();
        }
        if ($actionInterruption->serviceId !== $this->context->getServiceId()) {
            throw new WrongServiceHttpException();
        }
        if ($actionInterruption->accountId and ($actionInterruption->accountId !== $this->context->getAccount(true)->id)) {
            throw new WrongAccountHttpException();
        }
        $parts = explode('@', $actionInterruption->continueMethod);
        $controller = $this->applicationMake($parts[0]);
        $callback = [$controller, $parts[1]];
        if ( ! is_callable($callback)) {
            throw new RuntimeException(sprintf("Wrong callback '%s' in database", $actionInterruption->continueMethod));
        }
        $questionary = new Questionary($actionInterruption->questionary);
        $rules = $questionary->getQuestionsValidationRules();
        $validator = $this->assembleValidator($validatedData['answer'], $rules);
        if ($validator->fails()) {
            throw new InvalidInputHttpException($validator->errors()->all());
        }
        $validatedAnswer = $validator->valid();

        return call_user_func($callback, $actionInterruption, $validatedAnswer, $this->context, $questionary);
    }

}
