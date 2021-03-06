<?php namespace Subscribo\Api1\Controllers;

use Subscribo\Api1\AbstractController;
use Subscribo\Api1\Exceptions\RuntimeException;
use Subscribo\Exception\Exceptions\InvalidIdentifierHttpException;
use Subscribo\Exception\Exceptions\InvalidInputHttpException;
use Subscribo\Exception\Exceptions\InstanceNotFoundHttpException;
use Subscribo\Exception\Exceptions\WrongAccountHttpException;
use Subscribo\Exception\Exceptions\WrongServiceHttpException;
use Subscribo\RestCommon\Questionary;
use Subscribo\RestCommon\ClientRedirection;
use Subscribo\RestCommon\Widget;
use Subscribo\RestCommon\Factories\ServerRequestFactory;
use Subscribo\ModelCore\Models\ActionInterruption;
use Subscribo\Support\Arr;

/**
 * Class AnswerController
 *
 * API v1 Controller class handling requests for ServerRequest and answers to ServerRequests
 *
 * @package Subscribo\Api1
 */
class AnswerController extends AbstractController
{
    /**
     * GET action for getting Questionary by type
     * Currently only 'hash' is supported as type
     *
     * @param string $type
     * @return mixed
     * @throws InvalidIdentifierHttpException
     * @throws WrongAccountHttpException
     * @throws WrongServiceHttpException
     * @throws InstanceNotFoundHttpException
     * @throws RuntimeException
     */
    public function actionGetQuestion($type)
    {
        if ('hash' === $type) {
            return $this->processGetServerRequestByHash('getQuestionByHash');
        }
        throw new InvalidIdentifierHttpException(['type' => $this->localizeError('getQuestion.wrongType')]);
    }

    /**
     * GET Action for getting ClientRedirection by type
     * Currently only 'hash' is supported as type
     *
     * @param string $type
     * @return mixed
     * @throws InvalidIdentifierHttpException
     * @throws WrongAccountHttpException
     * @throws WrongServiceHttpException
     * @throws InstanceNotFoundHttpException
     * @throws RuntimeException
     */
    public function actionGetRedirection($type)
    {
        if ('hash' === $type) {
            return $this->processGetServerRequestByHash('getRedirectionByHash');
        }
        throw new InvalidIdentifierHttpException(['type' => $this->localizeError('getRedirection.wrongType')]);
    }

    /**
     * POST Action handling answer to Questionary ServerRequest
     *
     * @param string $hash
     * @return mixed
     * @throws InvalidInputHttpException
     * @throws WrongAccountHttpException
     * @throws WrongServiceHttpException
     * @throws InstanceNotFoundHttpException
     * @throws RuntimeException
     */
    public function actionPostQuestion($hash)
    {
        $validatedData = $this->validateRequestBody(['answer' => 'required|array']);
        $actionInterruption = $this->retrieveActionInterruption($hash);
        $callback = $this->retrieveCallback($actionInterruption);

        $questionary = new Questionary($actionInterruption->serverRequest);
        $validator = $this->assembleValidator(
            $validatedData['answer'],
            $questionary->getValidationRules(),
            $questionary->getValidationMessages(),
            $questionary->getValidationAttributes(),
            $questionary->getValidationCustomValues()
        );
        if ($validator->fails()) {
            throw new InvalidInputHttpException($validator->errors()->all());
        }
        $validatedAnswer = $validator->valid();

        return call_user_func($callback, $actionInterruption, $validatedAnswer, 'postQuestion', $this->context, $questionary);
    }

    /**
     * POST Action handling answer to ClientRedirection ServerRequest
     *
     * @param string $hash
     * @return mixed
     * @throws WrongAccountHttpException
     * @throws WrongServiceHttpException
     * @throws InstanceNotFoundHttpException
     * @throws RuntimeException
     */
    public function actionPostRedirection($hash)
    {
        $validatedData = $this->validateRequestBody(['answer' => 'required|array']);
        $actionInterruption = $this->retrieveActionInterruption($hash);
        $callback = $this->retrieveCallback($actionInterruption);

        $clientRedirection = new ClientRedirection($actionInterruption->serverRequest);

        return call_user_func($callback, $actionInterruption, $validatedData['answer'], 'postRedirection', $this->context, $clientRedirection);
    }

    /**
     * POST Action handling answer to Widget ServerRequest
     *
     * @param string $hash
     * @return mixed
     * @throws InvalidInputHttpException
     * @throws WrongAccountHttpException
     * @throws WrongServiceHttpException
     * @throws InstanceNotFoundHttpException
     * @throws RuntimeException
     */
    public function actionPostWidget($hash)
    {
        $validatedData = $this->validateRequestBody(['answer' => 'required|array']);

        $answerValidator = $this->assembleValidator($validatedData['answer'], ['request' => 'required|array']);
        if ($answerValidator->fails()) {
            throw new InvalidInputHttpException($answerValidator->errors()->all());
        }
        $validatedAnswer = $answerValidator->valid();
        $rulesForRequest = [
            'hash' => '',
            'method' => 'in:GET,POST',
            'uri' => 'required',
            'scheme' => 'in:http,https',
            'host' => '',
            'port' => 'numeric',
            'path' => '',
            'content' => '',
            'headers' => 'array',
            'query' => 'array',
            'postData' => 'array',
        ];
        $requestValidator = $this->assembleValidator($validatedAnswer['request'], $rulesForRequest);
        if ($requestValidator->fails()) {
            throw new InvalidInputHttpException($requestValidator->errors()->all());
        }
        $actionInterruption = $this->retrieveActionInterruption($hash);
        $callback = $this->retrieveCallback($actionInterruption);
        $widget = new Widget($actionInterruption->serverRequest);

        return call_user_func($callback, $actionInterruption, $validatedAnswer, 'postWidget', $this->context, $widget);
    }

    /**
     * @param string $action
     * @return mixed
     * @throws WrongAccountHttpException
     * @throws WrongServiceHttpException
     * @throws InstanceNotFoundHttpException
     * @throws RuntimeException
     */
    protected function processGetServerRequestByHash($action)
    {
        $queryValidationRules = [
            'hash' => 'required|alpha_num',
            'redirect_back' => 'url',
            'locale' => 'alpha_dash',
        ];
        $validatedData = $this->validateRequestQuery($queryValidationRules);
        $actionInterruption = $this->retrieveActionInterruption($validatedData['hash']);

        $serverRequestData = $actionInterruption->serverRequest;
        $serverRequestType = $actionInterruption->type;
        if ($serverRequestData and $serverRequestType) {
            $serverRequest =  ServerRequestFactory::make(['type' => $serverRequestType, 'data' => $serverRequestData]);
        } else {
            $serverRequest = null;
        }
        $callback = $this->retrieveCallback($actionInterruption);
        return call_user_func($callback, $actionInterruption, $validatedData, $action, $this->context, $serverRequest);
    }


    /**
     * @param string $hash
     * @return ActionInterruption
     * @throws WrongAccountHttpException
     * @throws WrongServiceHttpException
     * @throws InstanceNotFoundHttpException
     */
    protected function retrieveActionInterruption($hash)
    {
        $actionInterruption = ActionInterruption::findByHash($hash);
        if (empty($actionInterruption)) {
            throw new InstanceNotFoundHttpException();
        }
        $serviceId = strval($this->context->getServiceId());
        if (strval($actionInterruption->serviceId) === $serviceId) {
            if ($actionInterruption->accountId
                and (strval($actionInterruption->accountId) !== strval($this->context->getAccount(true)->id))) {
                    throw new WrongAccountHttpException();
            }
        } else {
            $allowedServiceIds = Arr::get($actionInterruption->extraData, 'allowedServiceIds', array());
            if (false === array_search($serviceId, $allowedServiceIds, true)) {
                throw new WrongServiceHttpException();
            }
        }
        return $actionInterruption;
    }

    /**
     * @param ActionInterruption $actionInterruption
     * @return callable
     * @throws RuntimeException
     */
    protected function retrieveCallback(ActionInterruption $actionInterruption)
    {
        $parts = explode('@', $actionInterruption->continueMethod);
        $controller = $this->applicationMake($parts[0]);
        $callback = [$controller, $parts[1]];
        if ( ! is_callable($callback)) {
            throw new RuntimeException(sprintf("Wrong callback '%s' in database", $actionInterruption->continueMethod));
        }
        return $callback;
    }

    private function localizeError($key, $parameters = array())
    {
        $localizer = $this->context->getLocalizer();
        return $localizer->trans('answer.errors.'.$key, $parameters, 'api1::controllers');
    }

}
