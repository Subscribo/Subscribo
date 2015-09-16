<?php namespace Subscribo\ApiClientCommon\ViewComposers;

use RuntimeException;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Subscribo\RestCommon\Questionary;
use Subscribo\Localization\Interfaces\LocalizerInterface;
use Subscribo\Support\Arr;

class QuestionaryComposer
{
    /** @var LocalizerInterface  */
    protected $localizer;

    /** @var Request  */
    protected $request;

    public function __construct(LocalizerInterface $localizer, Request $request)
    {
        $this->localizer = $localizer;
        $this->request = $request;
    }

    public function compose(View $view)
    {
        $localizer = $this->localizer->template('messages', 'apiclientcommon')->setPrefix('template.questionary');
        $questionary = $this->extractQuestionary($view);
        $heading = $questionary->title ?: $localizer->trans('defaultTitle');
        $errorTitle = $localizer->trans('errorTitle');
        $submit = $localizer->trans('submitButton');
        $items = $questionary->getQuestionItems();
        $oldValues = Arr::only($this->request->old(), $questionary->getFieldsToRememberOnError());

        $view->with('heading', $heading);
        $view->with('submit', $submit);
        $view->with('items', $items);
        $view->with('errorTitle', $errorTitle);
        $view->with('oldValues', $oldValues);
        $view->with('totalColumns', 12);
        $view->with('labelColumns', 4);
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
