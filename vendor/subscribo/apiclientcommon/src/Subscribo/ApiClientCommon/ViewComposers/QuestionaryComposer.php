<?php namespace Subscribo\ApiClientCommon\ViewComposers;

use RuntimeException;
use Illuminate\View\View;
use Subscribo\RestCommon\Questionary;
use Subscribo\Localization\Interfaces\LocalizerInterface;

class QuestionaryComposer
{
    /** @var LocalizerInterface  */
    protected $localizer;

    public function __construct(LocalizerInterface $localizer)
    {
        $this->localizer = $localizer;
    }

    public function compose(View $view)
    {
        $localizer = $this->localizer->template('messages', 'apiclientcommon')->setPrefix('template.questionary');
        $questionary = $this->extractQuestionary($view);
        $heading = $questionary->title ?: $localizer->trans('defaultTitle');
        $errorTitle = $localizer->trans('errorTitle');
        $submit = $localizer->trans('submitButton');
        $questions = $questionary->questions;

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
