<?php

namespace Subscribo\TransactionPluginManager\Factories;

use UnexpectedValueException;
use InvalidArgumentException;
use Subscribo\TransactionPluginManager\Factories\QuestionGroupFactory;
use Subscribo\RestCommon\Question;
use Subscribo\Localization\Interfaces\LocalizerInterface;
use Subscribo\ModelCore\Models\Person;

/**
 * Class QuestionFactory
 *
 * @package Subscribo\TransactionPluginManager
 */
class QuestionFactory
{
    /** @var LocalizerInterface $localizer */
    protected $localizer;

    /**
     * @param LocalizerInterface $localizer
     */
    public function __construct(LocalizerInterface $localizer)
    {
        $this->localizer = $localizer->duplicate('questionary', 'transaction-plugin-manager');
    }

    /**
     * @param Question|int|array $question
     * @return Question|\Subscribo\RestCommon\QuestionGroup
     * @param array $additionalData
     * @return Question|\Subscribo\RestCommon\QuestionGroup
     * @throws \InvalidArgumentException
     */
    public function make($question, array $additionalData = [])
    {
        if ($question instanceof Question) {

            return $question;
        } elseif (is_int($question)) {

            return $this->assembleFromCode($question, $additionalData);
        } elseif (is_array($question)) {

            return $this->assembleFromArray($question, $additionalData);
        }

        throw new InvalidArgumentException('Invalid question argument type');
    }

    /**
     * @param array $data
     * @param array $additionalData
     * @return Question|\Subscribo\RestCommon\QuestionGroup
     */
    protected function assembleFromArray(array $data, array $additionalData = [])
    {
        if (empty($data['type'])) {
            $data['type'] = Question::TYPE_TEXT;
        }
        if (Question::TYPE_GROUP === $data['type']) {
            $questionGroupFactory = new QuestionGroupFactory($this->localizer);

            return $questionGroupFactory->make($data, $additionalData);
        }
        if ( ! empty($additionalData['required'])) {
            $validationRules = empty($data['validationRules']) ? [] : $data['validationRules'];
            $validationRules = is_string($validationRules) ? explode('|', $validationRules) : $validationRules;
            if (false === array_search('required', $validationRules)) {
                array_unshift($validationRules, 'required');
                $data['validationRules'] = $validationRules;
            }
        }

        return new Question($data);
    }

    /**
     * @param $code
     * @param array $additionalData
     * @return Question|\Subscribo\RestCommon\QuestionGroup
     * @throws \UnexpectedValueException
     */
    protected function assembleFromCode($code, array $additionalData = [])
    {
        $data = [];
        $text = null;
        $type = null;
        switch($code) {
            case Question::CODE_GENERIC_QUESTION:

                throw new UnexpectedValueException ('Generic question cannot be assembled via code');
            case Question::CODE_CUSTOMER_BIRTH_DATE_DATE:
                $text = $this->localizer->trans('questions.birthDate.date.text');
                $type = Question::TYPE_DATE;
                $validationRules = [
                    'after:1890-01-01',
                    'before:tomorrow',
                ];
                break;
            case Question::CODE_DATE_DAY:
            case Question::CODE_CUSTOMER_BIRTH_DATE_DAY:
                $text = $this->localizer->trans('questions.date.day.text');
                $type = Question::TYPE_DAY;
                break;
            case Question::CODE_DATE_MONTH:
            case Question::CODE_CUSTOMER_BIRTH_DATE_MONTH:
                $text = $this->localizer->trans('questions.date.month.text');
                $type = Question::TYPE_MONTH;
                break;
            case Question::CODE_DATE_YEAR:
                $text = $this->localizer->trans('questions.date.year.text');
                $type = Question::TYPE_YEAR;
                break;
            case Question::CODE_CUSTOMER_BIRTH_DATE_YEAR:
                $text = $this->localizer->trans('questions.date.year.text');
                $minimum = '1890';
                $maximum = date('Y');
                $type = Question::TYPE_YEAR;
                break;
            case Question::CODE_CUSTOMER_NATIONAL_IDENTIFICATION_NUMBER_NUMBER:
                $text = $this->localizer->trans('questions.nationalIdentificationNumber.number.text');
                $type = Question::TYPE_TEXT;
                break;
            case Question::CODE_CUSTOMER_GENDER_GENDER:
                $text = $this->localizer->trans('questions.gender.gender.text');
                $type = Question::TYPE_RADIO;
                $selectOptions = [
                    Person::GENDER_MAN => $this->localizer->trans('questions.gender.gender.man'),
                    Person::GENDER_WOMAN => $this->localizer->trans('questions.gender.gender.woman'),
                ];
                break;
            default:
                throw new UnexpectedValueException ('Unknown question code');
        }
        $data['code'] = $code;
        $data['type'] = $type;
        if (isset($minimum)) {
            $data['minimumValue'] = $minimum;
        }
        if (isset($maximum)) {
            $data['maximumValue'] = $maximum;
        }
        if (isset($text)) {
            $data['text'] = $text;
            $data['validationAttributeName'] = trim($text, ':');
        }
        if (isset($selectOptions)) {
            $data['selectOptions'] = $selectOptions;
        }
        if (isset($validationRules)) {
            $data['validationRules'] = $validationRules;
        }

        return $this->assembleFromArray($data, $additionalData);
    }
}
