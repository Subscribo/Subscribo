<?php namespace Subscribo\Api1\Factories;

use Subscribo\Api1\Exceptions\InvalidArgumentException;
use Subscribo\RestCommon\Question;

/**
 * Class QuestionFactory
 * @package Subscribo\Api1
 */
class QuestionFactory
{
    /**
     * @param Question|array|string|int $source
     * @param array $additionalData
     * @return Question
     * @throws \Subscribo\Api1\Exceptions\InvalidArgumentException
     */
    public static function make($source, array $additionalData = array())
    {
        if ($source instanceof Question) {
            return static::addAdditionalData($source, $additionalData);
        }
        $source = (is_string($source)) ? json_decode($source, true) : $source;
        $source = (is_int($source)) ? static::assembleFromCode($source) : $source;
        if ( ! is_array($source)) {
            throw new InvalidArgumentException('QuestionFactory::make() provided source have incorrect type');
        }
        $question = new Question($source);
        return static::addAdditionalData($question, $additionalData);
    }

    /**
     * @param Question $question
     * @param array $additionalData
     * @return Question
     */
    protected static function addAdditionalData(Question $question, array $additionalData)
    {
        if (empty($additionalData)) {
            return $question;
        }
        if (( ! empty($additionalData['samePoolServices'])) and (Question::CODE_MERGE_OR_NEW_ACCOUNT_SELECT_SERVICE === $question->code)) {
            $question->prependSelectOptions($additionalData['samePoolServices']);
        }
        if (( ! empty($additionalData['existingEmail'])) and (Question::CODE_LOGIN_OR_NEW_ACCOUNT_PASSWORD === $question->code)) {
            $question->text = sprintf("Or provide a password to your existing account (email: %s):", $additionalData['existingEmail']);
        }
        return $question;
    }

    /**
     * @param int $code
     * @return array
     * @throws \Subscribo\Api1\Exceptions\InvalidArgumentException
     */
    protected static function assembleFromCode($code)
    {
        switch ($code) {
            case Question::CODE_NEW_CUSTOMER_EMAIL_EMAIL:
                $result = [
                    'type' => Question::TYPE_EMAIL,
                    'text' => 'Your actual email:',
                    'validationRules' => 'required'
                ];
                break;
            case Question::CODE_LOGIN_OR_NEW_ACCOUNT_EMAIL:
                $result = [
                    'type' => Question::TYPE_EMAIL,
                    'text' => 'You can either provide a new email:',
                    'validationRules' => 'required_without:password'
                ];
                break;
            case Question::CODE_LOGIN_OR_NEW_ACCOUNT_PASSWORD:
                $result = [
                    'type' => Question::TYPE_PASSWORD,
                    'text' => 'Or provide a password to your existing account:',
                    'validationRules' => 'required_without:email'
                ];
                break;
            case Question::CODE_MERGE_OR_NEW_ACCOUNT_SELECT_SERVICE:
                $result = [
                    'type' => Question::TYPE_SELECT,
                    'text' => 'Would you like to merge your account with one of the following services or create a new account?',
                    'validationRules' => 'required',
                    'selectOptions' => [
                        '' => 'Please select',
                        'new' => 'Create a new account',
                    ],
                ];
                break;
            default:
                throw new InvalidArgumentException(sprintf("QuestionFactory::assembleFromCode() unrecognized code '%s'", $code));
        }
        $result['code'] = $code;
        return $result;
    }
}
