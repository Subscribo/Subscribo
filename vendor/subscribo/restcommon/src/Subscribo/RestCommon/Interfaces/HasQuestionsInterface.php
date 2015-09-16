<?php

namespace Subscribo\RestCommon\Interfaces;

use Subscribo\RestCommon\Question;
/**
 * Class HasQuestionsInterface
 *
 * @package Subscribo\RestCommon
 */
interface HasQuestionsInterface
{
    /**
     * @return Question[]
     */
    public function getQuestions();
}
