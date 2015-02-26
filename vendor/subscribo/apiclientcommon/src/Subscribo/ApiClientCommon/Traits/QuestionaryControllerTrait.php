<?php namespace Subscribo\ApiClientCommon\Traits;

use Illuminate\Session\Store;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Contracts\Auth\Guard;
use Subscribo\RestCommon\Questionary;
use Subscribo\RestCommon\Question;
use Subscribo\RestCommon\AccountIdTransport;
use Subscribo\RestCommon\Exceptions\ServerRequestHttpException;
use Subscribo\Exception\Exceptions\SessionVariableNotFoundHttpException;
use Subscribo\ApiClientCommon\Traits\HandleServerRequestHttpExceptionTrait;
use Subscribo\ApiClientCommon\Connectors\QuestionaryConnector;


trait QuestionaryControllerTrait
{
    use HandleServerRequestHttpExceptionTrait;
    use ValidatesRequests;

    public function getQuestionary(Store $session)
    {
        $questionary = $session->get($this->sessionKeyQuestionary);
        if (empty($questionary)) {
            throw new SessionVariableNotFoundHttpException;
        }
        $session->reflash();
        return view('subscribo::apiclientcommon.questionary')->with('questionary', $questionary);
    }

    public function postQuestionary(Request $request, Store $session, QuestionaryConnector $connector, Guard $auth)
    {
        /** @var Questionary $questionary */
        $questionary = $session->get($this->sessionKeyQuestionary);
        if (empty($questionary)) {
            throw new SessionVariableNotFoundHttpException;
        }
        $session->reflash();
        $rules = $questionary->getQuestionsValidationRules();
        $this->validate($request, $rules);
        $redirectUri = $session->pull($this->sessionKeyRedirectFromQuestionary);
        $questionary = $session->pull($this->sessionKeyQuestionary);
        $data = $request->only(array_keys($rules));

        $user = $auth->user();
        $signatureOptions = $user ? AccountIdTransport::setAccountId($user->getAuthIdentifier()) : array();
        try {
            $response = $connector->postAnswer($questionary, $data, $signatureOptions);
        } catch (ServerRequestHttpException $e) {
            return $this->handleServerRequestHttpException($e, $redirectUri);
        }
        return redirect($redirectUri)->with($this->sessionKeyQuestionaryAnswerResult, $response);
    }

}
