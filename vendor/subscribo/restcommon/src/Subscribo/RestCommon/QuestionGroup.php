<?php

namespace Subscribo\RestCommon;

use Subscribo\RestCommon\Interfaces\HasQuestionsInterface;
use Subscribo\RestCommon\Traits\HasQuestionsTrait;
use Subscribo\RestCommon\Question;

class QuestionGroup implements HasQuestionsInterface
{
    use HasQuestionsTrait;

    const CODE_DATE               = 100;
    const CODE_BIRTH_DATE         = 101;

    const DISPLAY_INLINE = 'inline';
    const DISPLAY_FIELDSET = 'fieldset';

    public $type = Question::TYPE_GROUP;

    public $display = self::DISPLAY_INLINE;

    public $title;

    public $code;

    protected $questionItems = [];

    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->import($data);
    }

    /**
     * @param array $data
     */
    public function import(array $data)
    {
        if ( ! empty($data['display'])) {
            $this->display = $data['display'];
        }
        if ( ! empty($data['title'])) {
            $this->title = $data['title'];
        }
        if (isset($data['code'])) {
            $this->code = $data['code'];
        }
        if ( ! empty($data['questions'])) {
            $this->importQuestionItems($data['questions']);
        }
    }

    /**
     * @return array
     */
    public function export()
    {
        $questions = [];
        foreach ($this->getQuestionItems() as $key => $item) {
            $questions[$key] = $item->export();
        }
        return [
            'type' => Question::TYPE_GROUP,
            'display' => $this->display,
            'title' => $this->title,
            'code' => $this->code,
            'questions' => $questions,
        ];
    }
}
