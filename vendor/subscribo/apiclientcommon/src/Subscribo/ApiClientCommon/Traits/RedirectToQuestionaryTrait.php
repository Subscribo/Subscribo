<?php namespace Subscribo\ApiClientCommon\Traits;

use Subscribo\RestCommon\Questionary;

trait RedirectToQuestionaryTrait
{
    protected $sessionKeyQuestionary = 'subscribo_apiclientcommon_questionary_object';
    protected $sessionKeyRedirectFromQuestionary = 'subscribo_apiclientcommon_redirect_from_questionary';
    protected $sessionKeyQuestionaryAnswerResult = 'subscribo_apiclientcommon_questionary_answer_result';
    protected $questionaryRedirectUri = 'question';

    protected function redirectToQuestionary(Questionary $questionary, $backUri)
    {
        return redirect($this->questionaryRedirectUri)
            ->with($this->sessionKeyQuestionary, $questionary)
            ->with($this->sessionKeyRedirectFromQuestionary, $backUri);
    }

}