<?php namespace Subscribo\ApiClientCommon\ViewComposers;

use RuntimeException;
use Illuminate\View\View;
use Subscribo\RestCommon\Questionary;

class QuestionaryComposer
{

    public function compose(View $view)
    {
        $questionary = $this->extractQuestionary($view);
        $heading = $questionary->title ?: 'We need some more information';

        $submit = 'Submit';
        $questions = $questionary->questions;
        $errorTitle = 'Please, check your input:';


        $view->with('heading', $heading);
        $view->with('submit', $submit);
        $view->with('questions', $questions);
        $view->with('errorTitle', $errorTitle);
    }

    /**
     * @param View $view
     * @return Questionary
     * @throws \RuntimeException
     */
    private function extractQuestionary(View $view)
    {
        $data = $view->getData();
        if (empty($data['questionary'])) {
            throw new RuntimeException('Questionary object has not been provided to view questionary');
        }
        $questionary = $data['questionary'];
        if ( ! ($questionary instanceof Questionary)) {
            throw new RuntimeException('Questionary provided is not an instance of Questionary');
        }
        return $questionary;
    }

}
