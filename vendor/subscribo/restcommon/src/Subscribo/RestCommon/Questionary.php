<?php

namespace Subscribo\RestCommon;

use Subscribo\RestCommon\ServerRequest;
use Subscribo\RestCommon\Interfaces\HasQuestionsInterface;
use Subscribo\RestCommon\Traits\HasQuestionsTrait;

/**
 * Class Questionary
 *
 * @package Subscribo\RestCommon
 */
class Questionary extends ServerRequest implements HasQuestionsInterface
{
    use HasQuestionsTrait;

    const TYPE = 'questionary';

    const CODE_NEW_CUSTOMER_EMAIL = 10;
    const CODE_LOGIN_OR_NEW_ACCOUNT = 20;
    const CODE_MERGE_OR_NEW_ACCOUNT = 30;
    const CODE_CONFIRM_ACCOUNT_MERGE_PASSWORD = 40;
    const CODE_CONFIRM_ACCOUNT_MERGE_SIMPLE = 50;

    const CODE_GENERIC_QUESTIONARY                      = 100;
    const CODE_DATE                                     = 200;
    const CODE_CUSTOMER_BIRTH_DATE                      = 210;
    const CODE_CUSTOMER_NATIONAL_IDENTIFICATION_NUMBER  = 220;

    /** @var string  */
    public $title;

    /** @var array  */
    protected $questionItems = [];

    /** @var array  */
    protected $validationMessages = array();

    /** @var array  */
    protected $validationAttributes = array();

    /**
     * @param array $data
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function import(array $data)
    {
        if ( ! empty($data['title'])) {
            $this->title = $data['title'];
        }
        if ( ! empty($data['questions'])) {
            $this->importQuestionItems($data['questions']);
        }
        if ( ! empty($data['validationMessages'])) {
            $this->setValidationMessages($data['validationMessages']);
        }
        if ( ! empty($data['validationAttributes'])) {
            $this->setValidationAttributes($data['validationAttributes']);
        }

        return parent::import($data);
    }

    /**
     * @return array
     */
    public function export()
    {
        $result = parent::export();
        $questions = [];
        foreach ($this->getQuestionItems() as $key => $item) {
            $questions[$key] = $item->export();
        }
        $result['questions'] = $questions;
        $result['title'] = $this->title;

        return $result;
    }

    /**
     * @return array
     */
    public function getValidationRules()
    {
        $rules = [];
        foreach ($this->getQuestions() as $key => $question) {
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
        foreach ($this->getQuestions() as $attribute => $question) {
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
        foreach ($this->getQuestions() as $attribute => $question) {
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
        foreach ($this->getQuestions() as $attribute => $question) {
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
        foreach ($this->getQuestions() as $attribute => $question) {
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
