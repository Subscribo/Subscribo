<?php

namespace Subscribo\TransactionPluginManager\Factories;

use UnexpectedValueException;
use InvalidArgumentException;
use Subscribo\RestCommon\QuestionGroup;
use Subscribo\Localization\Interfaces\LocalizerInterface;

/**
 * Class QuestionGroupFactory
 *
 * @package Subscribo\TransactionPluginManager
 */
class QuestionGroupFactory
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
     * @param QuestionGroup|array $questionGroup
     * @param array $additionalData
     * @return QuestionGroup
     * @throws \InvalidArgumentException
     */
    public function make($questionGroup, array $additionalData = [])
    {
        if ($questionGroup instanceof QuestionGroup) {

            return $questionGroup;
        } elseif (is_array($questionGroup)) {

            return $this->assembleFromArray($questionGroup, $additionalData);
        }

        throw new InvalidArgumentException('Invalid argument question group type');
    }

    /**
     * @param array $data
     * @param array $additionalData
     * @return QuestionGroup
     * @throws \UnexpectedValueException
     */
    protected function assembleFromArray(array $data, array $additionalData = [])
    {
        if (empty($data['questions']) or ( ! is_array($data['questions']))) {

            throw new UnexpectedValueException('Array key questions missing or invalid');
        }
        $questions = [];
        $questionFactory = new QuestionFactory($this->localizer);
        foreach ($data['questions'] as $key => $question) {
            $questions[$key] = $questionFactory->make($question, $additionalData);
        }
        $data['questions'] = $questions;

        return new QuestionGroup($data);
    }
}
