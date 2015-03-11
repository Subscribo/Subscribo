<?php namespace Subscribo\RestCommon;

use Subscribo\RestCommon\ServerRequest;
use Subscribo\RestCommon\Question;

class Questionary extends ServerRequest
{
    const TYPE = 'questionary';

    const CODE_NEW_CUSTOMER_EMAIL = 10;
    const CODE_LOGIN_OR_NEW_ACCOUNT = 20;
    const CODE_MERGE_OR_NEW_ACCOUNT = 30;
    const CODE_CONFIRM_ACCOUNT_MERGE_PASSWORD = 40;
    const CODE_CONFIRM_ACCOUNT_MERGE_SIMPLE = 50;

    /** @var string  */
    public $title;

    /** @var Question[] */
    public $questions = array();

    public function import(array $data)
    {
        if ( ! empty($data['title'])) {
            $this->title = $data['title'];
        }
        if ( ! empty($data['questions'])) {
            $questions = is_array($data['questions']) ? $data['questions'] : ['value' => $data['questions']];
            foreach ($questions as $key => $questionData) {
                $this->questions[$key] = ($questionData instanceof Question) ? $questionData : new Question($questionData);
            }
        }
        return parent::import($data);
    }

    public function export()
    {
        $result = parent::export();
        $result['title'] = $this->title;
        foreach ($this->questions as $key => $question) {
            $result['questions'][$key] = $question->export();
        }
        return $result;
    }

    public function getQuestionsValidationRules()
    {
        $rules = [];
        foreach ($this->questions as $key => $question)
        {
            $rules[$key] = $question->getValidationRules();
        }
        return $rules;
    }
}
