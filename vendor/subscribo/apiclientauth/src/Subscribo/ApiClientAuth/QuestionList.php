<?php namespace Subscribo\ApiClientAuth;

class QuestionList
{
    protected $questions = array();

    public function __construct(array $questions = array())
    {
        $this->questions = $questions;
    }

    public function getQuestions()
    {
        return $this->questions;
    }
}
