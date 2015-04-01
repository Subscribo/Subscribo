<?php namespace Subscribo\Api1\Factories;

use Subscribo\Api1\Exceptions\InvalidArgumentException;
use Subscribo\RestCommon\Question;
use Subscribo\Support\Arr;
use Subscribo\Localization\Interfaces\LocalizerInterface;

/**
 * Class QuestionFactory
 * @package Subscribo\Api1
 */
class QuestionFactory
{
    /** @var \Subscribo\Localization\Interfaces\LocalizerInterface  */
    protected $localizer;

    public function __construct(LocalizerInterface $localizer)
    {
        $this->localizer = $localizer->duplicate('questionary', 'api1');
    }

    /**
     * @param Question|array|string|int $source
     * @param array $additionalData
     * @return Question
     * @throws \Subscribo\Api1\Exceptions\InvalidArgumentException
     */
    public function make($source, array $additionalData = array())
    {
        if ($source instanceof Question) {
            return $this->addAdditionalData($source, $additionalData);
        }
        $source = (is_string($source)) ? json_decode($source, true) : $source;
        $source = (is_int($source)) ? $this->assembleFromCode($source) : $source;
        if ( ! is_array($source)) {
            throw new InvalidArgumentException('QuestionFactory::make() provided source have incorrect type');
        }
        $question = new Question($source);
        return $this->addAdditionalData($question, $additionalData);
    }

    /**
     * @param Question $question
     * @param array $additionalData
     * @return Question
     */
    protected function addAdditionalData(Question $question, array $additionalData)
    {
        $parameters = [];
        if (empty($additionalData)) {
            return $question;
        }
        if (( ! empty($additionalData['samePoolServices'])) and (Question::CODE_MERGE_OR_NEW_ACCOUNT_SELECT_SERVICE === $question->code)) {
            $question->prependSelectOptions($additionalData['samePoolServices']);
        }
        if ((Question::CODE_LOGIN_OR_NEW_ACCOUNT_PASSWORD === $question->code) and ( ! empty($additionalData['%email%']))) {
            $parameters = $this->extractParameters(['%email%'], $additionalData);
            /// TRANSLATORS: English: Or provide a password to your existing account (email: %email%):
            $question->text = $this->localizer->trans('questions.special.CODE_LOGIN_OR_NEW_ACCOUNT_PASSWORD', $parameters);
        }
        if (Question::CODE_CONFIRM_MERGE_ACCOUNT_YES_OR_NO === $question->code) {
            $parameters = $this->extractParameters(['{confirmingService}', '{requestingService}', '%email%'], $additionalData);
            /// TRANSLATORS: English: Would you like to merge your new account by {requestingService} with your existing account by {confirmingService} (with email %email%)?
            $question->text = $this->localizer->trans('questions.special.CODE_CONFIRM_MERGE_ACCOUNT_YES_OR_NO', $parameters);
        }
        if (Question::CODE_CONFIRM_MERGE_ACCOUNT_PASSWORD === $question->code) {
            $parameters = $this->extractParameters(['{confirmingService}', '%email%'], $additionalData);
            /// TRANSLATORS: English: If you want to merge accounts, please provide a password to your account by {confirmingService} with email %email%
            $question->text = $this->localizer->trans('questions.special.CODE_CONFIRM_MERGE_ACCOUNT_PASSWORD', $parameters);
        }
        return $question;
    }

    /**
     * @param int $code
     * @return array
     * @throws \Subscribo\Api1\Exceptions\InvalidArgumentException
     */
    protected function assembleFromCode($code)
    {
        switch ($code) {
            case Question::CODE_NEW_CUSTOMER_EMAIL_EMAIL:
                $result = [
                    'type' => Question::TYPE_EMAIL,
                    /// TRANSLATORS: English: Your actual email:
                    'text' => $this->localizer->trans('questions.text.CODE_NEW_CUSTOMER_EMAIL_EMAIL'),
                    'validationRules' => 'required'
                ];
                break;
            case Question::CODE_LOGIN_OR_NEW_ACCOUNT_EMAIL:
                $result = [
                    'type' => Question::TYPE_EMAIL,
                    /// TRANSLATORS: English: You can either provide a new email:
                    'text' => $this->localizer->trans('questions.text.CODE_LOGIN_OR_NEW_ACCOUNT_EMAIL'),
                    'validationRules' => 'required_without:password'
                ];
                break;
            case Question::CODE_LOGIN_OR_NEW_ACCOUNT_PASSWORD:
                $result = [
                    'type' => Question::TYPE_PASSWORD,
                    /// TRANSLATORS: English: Or provide a password to your existing account:
                    'text' => $this->localizer->trans('questions.text.CODE_LOGIN_OR_NEW_ACCOUNT_PASSWORD'),
                    'validationRules' => 'required_without:email'
                ];
                break;
            case Question::CODE_MERGE_OR_NEW_ACCOUNT_SELECT_SERVICE:
                $result = [
                    'type' => Question::TYPE_SELECT,
                    /// TRANSLATORS: English: Would you like to merge your account with one of the following services or create a new account?
                    'text' => $this->localizer->trans('questions.text.CODE_MERGE_OR_NEW_ACCOUNT_SELECT_SERVICE'),
                    'validationRules' => 'required',
                    'selectOptions' => [
                        /// TRANSLATORS: English: Please select
                        '' => $this->localizer->trans('questions.select.CODE_MERGE_OR_NEW_ACCOUNT_SELECT_SERVICE.selection_bid'),
                        /// TRANSLATORS: English: Create a new account
                        'new' => $this->localizer->trans('questions.select.CODE_MERGE_OR_NEW_ACCOUNT_SELECT_SERVICE.new_account'),

                    ],
                ];
                break;
            case Question::CODE_CONFIRM_MERGE_ACCOUNT_YES_OR_NO:
                $result = [
                    'type' => Question::TYPE_SELECT,
                    /// TRANSLATORS: English: Would you like to merge your new account with your existing account?
                    'text' => $this->localizer->trans('questions.text.CODE_CONFIRM_MERGE_ACCOUNT_YES_OR_NO'),
                    'validationRules' => 'required',
                    'selectOptions' => [
                        /// TRANSLATORS: English: Please select
                        '' => $this->localizer->trans('questions.select.CODE_CONFIRM_MERGE_ACCOUNT_YES_OR_NO.selection_bid'),
                        'yes' => $this->localizer->trans('questions.select.CODE_CONFIRM_MERGE_ACCOUNT_YES_OR_NO.yes'),
                        'no'  => $this->localizer->trans('questions.select.CODE_CONFIRM_MERGE_ACCOUNT_YES_OR_NO.no'),
                    ],
                ];
                break;
            case Question::CODE_CONFIRM_MERGE_ACCOUNT_PASSWORD:
                $result = [
                    'type' => Question::TYPE_PASSWORD,
                    /// TRANSLATORS: English: If you want to merge accounts, please provide a password to your current service:
                    'text' => $this->localizer->trans('questions.text.CODE_CONFIRM_MERGE_ACCOUNT_PASSWORD'),
                    'validationRules' => 'required_if:merge,yes'
                ];
                break;
            default:
                throw new InvalidArgumentException(sprintf("QuestionFactory::assembleFromCode() unrecognized code '%s'", $code));
        }
        $result['code'] = $code;
        return $result;
    }

    protected function extractParameters(array $parameterNames, array $source)
    {
        $result = [];
        foreach ($parameterNames as $key)
        {
            $result[$key] = array_key_exists($key, $source) ? $source[$key] : null;
        }
        return $result;
    }
}
