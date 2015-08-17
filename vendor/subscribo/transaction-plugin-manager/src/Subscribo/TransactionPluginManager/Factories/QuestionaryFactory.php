<?php

namespace Subscribo\TransactionPluginManager\Factories;

use UnexpectedValueException;
use InvalidArgumentException;
use Subscribo\RestCommon\Questionary;
use Subscribo\RestCommon\Question;
use Subscribo\TransactionPluginManager\Factories\QuestionFactory;
use Subscribo\Localization\Interfaces\LocalizerInterface;
use Subscribo\TransactionPluginManager\Interfaces\QuestionaryFacadeInterface;
use Subscribo\TransactionPluginManager\Factories\AbstractServerRequestFactory;

/**
 * Class QuestionaryFactory
 *
 * @package Subscribo\TransactionPluginManager
 */
class QuestionaryFactory extends AbstractServerRequestFactory
{
    /** @var LocalizerInterface $localizer */
    protected $localizer;

    /**
     * @param LocalizerInterface $localizer
     * @param string|null|bool $defaultDomain
     */
    public function __construct(LocalizerInterface $localizer, $defaultDomain = true)
    {
        $this->localizer = $localizer->duplicate('questionary', 'transaction-plugin-manager');
        parent::__construct($defaultDomain);
    }

    /**
     * @param int|string|array|Questionary|QuestionaryFacadeInterface $questionary
     * @param array $additionalData
     * @return Questionary
     * @throws \InvalidArgumentException
     */
    public function make($questionary, $additionalData = [])
    {
        if ($questionary instanceof Questionary) {

            return $questionary;
        } elseif ($questionary instanceof QuestionaryFacadeInterface) {

            return $questionary->getQuestionaryInstance();
        } elseif (is_int($questionary)) {

            return $this->assembleFromCode($questionary, $additionalData);
        } elseif (is_numeric($questionary)) {

            throw new InvalidArgumentException('A numeric string has been provided as argument. Please provide integer for code-base questionary or string for generic questionary');
        } elseif (is_string($questionary, $additionalData)) {

            return $this->assembleFromString($questionary, $additionalData);
        } elseif (is_array($questionary)) {

            return $this->assembleFromArray($questionary, $additionalData);
        }

        throw new InvalidArgumentException('Invalid questionary argument type');
    }

    /**
     * @param array $data
     * @param array $additionalData
     * @return Questionary
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
        $data = $this->addDefaultDomainToData($data);

        return new Questionary($data);
    }

    /**
     * @param string $questionText
     * @param array $additionalData
     * @return Questionary
     */
    protected function assembleFromString($questionText, array $additionalData = [])
    {
        $questionFactory = new QuestionFactory($this->localizer);
        $questionData = [
            'code' => Question::CODE_GENERIC_QUESTION,
            'type' => Question::TYPE_TEXT,
            'text' => $questionText,
        ];
        $question = $questionFactory->make($questionData, $additionalData);
        $questionary = new Questionary([
            'code' => Questionary::CODE_GENERIC_QUESTIONARY,
            'title' => $this->localizer->trans('questionary.generic.title'),
            'questions' => ['answer' => $question],
        ]);

        return $questionary;
    }

    /**
     * @param int $code
     * @param array $additionalData
     * @return Questionary
     * @throws \UnexpectedValueException
     */
    protected function assembleFromCode($code, array $additionalData = [])
    {
        $data = [];
        $questions = [];
        $questionGroup = null;
        $title = null;
        $required = null;
        switch ($code) {
            case QuestionaryFacadeInterface::CODE_GENERIC_QUESTIONARY:

                throw new UnexpectedValueException ('Generic questionary cannot be assembled via code');
            case QuestionaryFacadeInterface::CODE_DATE:
                $title = $this->localizer->trans('questionary.date.title');
                $questionGroup = [
                    'title' => $this->localizer->trans('questionGroup.date.title'),
                    'questions' => [
                        'day' => Question::CODE_DATE_DAY,
                        'month' => Question::CODE_DATE_MONTH,
                        'year' => Question::CODE_DATE_YEAR,
                    ]
                ];
                break;
            case QuestionaryFacadeInterface::CODE_CUSTOMER_BIRTH_DATE:
                $required = true;
                $title = $this->localizer->trans('questionary.birthDate.title');
                $questionGroup = [
                    'title' => $this->localizer->trans('questionGroup.birthDate.title'),
                    'questions' => [
                        'birth_date_day' => Question::CODE_CUSTOMER_BIRTH_DATE_DAY,
                        'birth_date_month' => Question::CODE_CUSTOMER_BIRTH_DATE_MONTH,
                        'birth_date_year' => Question::CODE_CUSTOMER_BIRTH_DATE_YEAR,
                    ]
                ];
                break;
            case QuestionaryFacadeInterface::CODE_CUSTOMER_NATIONAL_IDENTIFICATION_NUMBER:
                $required = true;
                $title = $this->localizer->trans('questionary.nationalIdentificationNumber.title');
                $questions = [
                    'nin_number' => Question::CODE_CUSTOMER_NATIONAL_IDENTIFICATION_NUMBER_NUMBER,
                ];
                break;
            default:
                throw new UnexpectedValueException('Unknown Questionary code');
        }
        if ($questionGroup) {
            $questionGroup['type'] = Question::TYPE_GROUP;
            $questions['group'] = $questionGroup;
        }
        $data['code'] = $code;
        $data['title'] = $title;
        $data['questions'] = $questions;
        if (isset($required) and ( ! isset($additionalData['required']))) {
            $additionalData['required'] = $required;
        }

        return $this->assembleFromArray($data, $additionalData);
    }
}
