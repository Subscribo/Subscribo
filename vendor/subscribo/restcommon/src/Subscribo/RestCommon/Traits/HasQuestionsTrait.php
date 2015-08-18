<?php

namespace Subscribo\RestCommon\Traits;

use InvalidArgumentException;
use Subscribo\RestCommon\Question;
use Subscribo\RestCommon\QuestionGroup;
use Subscribo\RestCommon\Interfaces\HasQuestionsInterface;

/**
 * Trait HasQuestionsTrait
 *
 * Expecting class using this trait contain array-type property $questionItems
 *
 * @package Subscribo\RestCommon\Traits
 */
trait HasQuestionsTrait
{
    /**
     * @return Question[]
     * @throws \InvalidArgumentException
     */
    public function getQuestions()
    {
        $result = [];
        foreach ($this->questionItems as $key => $item)
        {
            if ($item instanceof Question) {
                $result[$key] = $item;
            } elseif ($item instanceof HasQuestionsInterface) {
                $result = $item->getQuestions() + $result;
            } else {
                throw new InvalidArgumentException('Item is not instance of Question neither implements HasQuestionsInterface');
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getQuestionItems()
    {
        return $this->questionItems;
    }

    /**
     * @param array $data
     * @throws \InvalidArgumentException
     */
    protected function importQuestionItems(array $data)
    {
        foreach($data as $key => $value) {
            $this->questionItems[$key] = $this->makeQuestionItem($value);
        }
    }

    /**
     * @param array|Question|QuestionGroup $data
     * @return Question|QuestionGroup
     * @throws \InvalidArgumentException
     */
    protected function makeQuestionItem($data)
    {
        if ($data instanceof Question) {

            return $data;
        }
        if ($data instanceof QuestionGroup) {

            return $data;
        }
        if ( ! is_array($data)) {
            throw new InvalidArgumentException('Question item should be either Question, QuestionGroup or Array');
        }
        if (isset($data['type']) and (Question::TYPE_GROUP === $data['type'])) {

            return new QuestionGroup($data);
        }

        return new Question($data);
    }
}
