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
        $this->localizer = $localizer->template('questionary', 'api1');
        $this->localizer->setPrefix('questions');
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
            $question->text = $this->localizer->trans('special.CODE_LOGIN_OR_NEW_ACCOUNT_PASSWORD', $parameters);
        }
        if (Question::CODE_CONFIRM_MERGE_ACCOUNT_YES_OR_NO === $question->code) {
            $parameters = $this->extractParameters(['{confirmingService}', '{requestingService}', '%email%'], $additionalData);
            /// TRANSLATORS: English: Would you like to merge your new account by {requestingService} with your existing account by {confirmingService} (with email %email%)?
            $question->text = $this->localizer->trans('special.CODE_CONFIRM_MERGE_ACCOUNT_YES_OR_NO', $parameters);
        }
        if (Question::CODE_CONFIRM_MERGE_ACCOUNT_PASSWORD === $question->code) {
            $parameters = $this->extractParameters(['{confirmingService}', '%email%'], $additionalData);
            /// TRANSLATORS: English: If you want to merge accounts, please provide a password to your account by {confirmingService} with email %email%
            $question->text = $this->localizer->trans('special.CODE_CONFIRM_MERGE_ACCOUNT_PASSWORD', $parameters);
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
                    'text' => $this->localizer->trans('text.CODE_NEW_CUSTOMER_EMAIL_EMAIL'),
                    'validationRules' => 'required',
                    'validationAttributeName' => $this->localizer->trans('attributeNames.CODE_NEW_CUSTOMER_EMAIL_EMAIL'),
                    'validationMessages' => [
                        'required' => $this->localizer->trans('validationMessages.CODE_NEW_CUSTOMER_EMAIL_EMAIL.required'),
                        'email' => $this->localizer->trans('validationMessages.CODE_NEW_CUSTOMER_EMAIL_EMAIL.email'),
                    ],
                ];
                break;
            case Question::CODE_LOGIN_OR_NEW_ACCOUNT_EMAIL:
                $result = [
                    'type' => Question::TYPE_EMAIL,
                    /// TRANSLATORS: English: You can either provide a new email:
                    'text' => $this->localizer->trans('text.CODE_LOGIN_OR_NEW_ACCOUNT_EMAIL'),
                    'validationRules' => 'required_without:password',
                    'validationAttributeName' => $this->localizer->trans('attributeNames.CODE_LOGIN_OR_NEW_ACCOUNT_EMAIL'),
                    'validationMessages' => [
                        'required_without' => $this->localizer->trans('validationMessages.CODE_LOGIN_OR_NEW_ACCOUNT_EMAIL.required_without'),
                        'email' => $this->localizer->trans('validationMessages.CODE_LOGIN_OR_NEW_ACCOUNT_EMAIL.email'),
                    ],
                ];
                break;
            case Question::CODE_LOGIN_OR_NEW_ACCOUNT_PASSWORD:
                $result = [
                    'type' => Question::TYPE_PASSWORD,
                    /// TRANSLATORS: English: Or provide a password to your existing account:
                    'text' => $this->localizer->trans('text.CODE_LOGIN_OR_NEW_ACCOUNT_PASSWORD'),
                    'validationRules' => 'required_without:email',
                    'validationAttributeName' => $this->localizer->trans('attributeNames.CODE_LOGIN_OR_NEW_ACCOUNT_PASSWORD'),
                    'validationMessages' => $this->localizer->trans('validationMessages.CODE_LOGIN_OR_NEW_ACCOUNT_PASSWORD'),
                ];
                break;
            case Question::CODE_MERGE_OR_NEW_ACCOUNT_SELECT_SERVICE:
                $result = [
                    'type' => Question::TYPE_SELECT,
                    /// TRANSLATORS: English: Would you like to merge your account with one of the following services or create a new account?
                    'text' => $this->localizer->trans('text.CODE_MERGE_OR_NEW_ACCOUNT_SELECT_SERVICE'),
                    'validationRules' => 'required',
                    'validationAttributeName' => $this->localizer->trans('attributeNames.CODE_MERGE_OR_NEW_ACCOUNT_SELECT_SERVICE'),
                    'selectOptions' => [
                        /// TRANSLATORS: English: Please select
                        '' => $this->localizer->trans('select.CODE_MERGE_OR_NEW_ACCOUNT_SELECT_SERVICE.selection_bid'),
                        /// TRANSLATORS: English: Create a new account
                        'new' => $this->localizer->trans('select.CODE_MERGE_OR_NEW_ACCOUNT_SELECT_SERVICE.new_account'),
                    ],
                    'validationMessages' => $this->localizer->trans('validationMessages.CODE_MERGE_OR_NEW_ACCOUNT_SELECT_SERVICE'),
                ];
                break;
            case Question::CODE_CONFIRM_MERGE_ACCOUNT_YES_OR_NO:
                $result = [
                    'type' => Question::TYPE_SELECT,
                    /// TRANSLATORS: English: Would you like to merge your new account with your existing account?
                    'text' => $this->localizer->trans('text.CODE_CONFIRM_MERGE_ACCOUNT_YES_OR_NO'),
                    'validationRules' => 'required',
                    'validationAttributeName' => $this->localizer->trans('attributeNames.CODE_CONFIRM_MERGE_ACCOUNT_YES_OR_NO'),
                    'selectOptions' => [
                        /// TRANSLATORS: English: Please select
                        '' => $this->localizer->trans('select.CODE_CONFIRM_MERGE_ACCOUNT_YES_OR_NO.selection_bid'),
                        'yes' => $this->localizer->trans('select.CODE_CONFIRM_MERGE_ACCOUNT_YES_OR_NO.yes'),
                        'no'  => $this->localizer->trans('select.CODE_CONFIRM_MERGE_ACCOUNT_YES_OR_NO.no'),
                    ],
                    'validationMessages' => $this->localizer->trans('validationMessages.CODE_CONFIRM_MERGE_ACCOUNT_YES_OR_NO'),
                ];
                break;
            case Question::CODE_CONFIRM_MERGE_ACCOUNT_PASSWORD:
                $result = [
                    'type' => Question::TYPE_PASSWORD,
                    /// TRANSLATORS: English: If you want to merge accounts, please provide a password to your current service:
                    'text' => $this->localizer->trans('text.CODE_CONFIRM_MERGE_ACCOUNT_PASSWORD'),
                    'validationRules' => 'required_if:merge,yes',
                    'validationAttributeName' => $this->localizer->trans('attributeNames.CODE_CONFIRM_MERGE_ACCOUNT_PASSWORD'),
                    'validationMessages' => $this->localizer->trans('validationMessages.CODE_CONFIRM_MERGE_ACCOUNT_PASSWORD'),
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
