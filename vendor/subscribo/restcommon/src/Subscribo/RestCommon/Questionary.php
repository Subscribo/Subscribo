<?php namespace Subscribo\RestCommon;

use Subscribo\RestCommon\ServerRequest;
use Subscribo\RestCommon\Question;

class Questionary extends ServerRequest
{
    const TYPE = 'questionary';

    const CODE_NEW_CUSTOMER_EMAIL = 10;
    const CODE_LOGIN_OR_NEW_ACCOUNT = 20;
    const CODE_MERGE_OR_NEW_ACCOUNT = 30;

    /** @var string  */
    public $title;

    /** @var int  */
    public $code = 0;

    /** @var Question[] */
    public $questions = array();

    public $hash;

    public $endpoint;

    public function import(array $data)
    {
        if ( ! empty($data['hash'])) {
            $this->hash = $data['hash'];
        }
        if ( ! empty($data['title'])) {
            $this->title = $data['title'];
        }
        if ( ! empty($data['endpoint'])) {
            $this->endpoint = $data['endpoint'];
        }
        if ( array_key_exists('code', $data)) {
            $this->code = $data['code'];
        }
        if ( ! empty($data['questions'])) {
            $questions = is_array($data['questions']) ? $data['questions'] : ['value' => $data['questions']];
            foreach ($questions as $key => $questionData) {
                $this->questions[$key] = ($questionData instanceof Question) ? $questionData : new Question($questionData);
            }
        }
    }

    public function export()
    {
        $result = [
            'title' => $this->title,
            'code'  => $this->code,
            'hash'  => $this->hash,
            'endpoint' => $this->endpoint,
        ];
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
