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

    protected $validationMessages = array();

    protected $validationAttributes = array();

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
        if ( ! empty($data['validationMessages'])) {
            $this->setValidationMessages($data['validationMessages']);
        }
        if ( ! empty($data['validationAttributes'])) {
            $this->setValidationAttributes($data['validationAttributes']);
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

    /**
     * @return array
     */
    public function getValidationRules()
    {
        $rules = [];
        foreach ($this->questions as $key => $question)
        {
            $rules[$key] = $question->getValidationRules();
        }
        return $rules;
    }

    /**
     * @return array
     */
    public function getValidationMessages()
    {
        $result = $this->validationMessages;
        foreach ($this->questions as $attribute => $question) {
            foreach ($question->getValidationMessages() as $rule => $message) {
                $key = $attribute.'.'.$rule;
                $result[$key] = $message;
            }
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getValidationAttributes()
    {
        $result = $this->validationAttributes;
        foreach ($this->questions as $attribute => $question) {
            if ( ! empty($question->validationAttributeName)) {
                $result[$attribute] = $question->validationAttributeName;
            }
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getValidationCustomValues()
    {
        $result = [];
        foreach ($this->questions as $attribute => $question) {
            foreach ($question->getValidationCustomValues() as $valueName => $value) {
                $result[$attribute][$valueName] = $value;
            }
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getFieldsToRememberOnError()
    {
        $result = [];
        foreach ($this->questions as $attribute => $question) {
            if ($question->getRememberValueOnError()) {
                $result[$attribute] = $attribute;
            }
        }
        return $result;
    }

    /**
     * @param array $validationMessages
     * @return $this
     */
    protected function setValidationMessages(array $validationMessages)
    {
        $this->validationMessages = $validationMessages;
        return $this;
    }

    /**
     * @param array $validationAttributes
     * @return $this
     */
    protected function setValidationAttributes(array $validationAttributes)
    {
        $this->validationAttributes = $validationAttributes;
        return $this;
    }
}
