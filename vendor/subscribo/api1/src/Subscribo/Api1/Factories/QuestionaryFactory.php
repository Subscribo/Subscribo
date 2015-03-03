<?php namespace Subscribo\Api1\Factories;

use Subscribo\Api1\Exceptions\InvalidArgumentException;
use Subscribo\RestCommon\Questionary;
use Subscribo\RestCommon\Question;
use Subscribo\Api1\Factories\QuestionFactory;


class QuestionaryFactory
{

    /**
     * @param Questionary|array|string|int $source
     * @param array $additionalData
     * @return Questionary
     * @throws \Subscribo\Api1\Exceptions\InvalidArgumentException
     */
    public static function make($source, array $additionalData = array())
    {
        if ($source instanceof Questionary) {
            return $source;
        }
        $source = is_string($source) ? json_decode($source, true) : $source;
        $source = is_int($source) ? static::assembleFromCode($source) : $source;
        if ( ! is_array($source)) {
            throw new InvalidArgumentException('QuestionaryFactory::make() provided source have incorrect type');
        }
        if (empty($source['questions'])) {
            throw new InvalidArgumentException('QuestionaryFactory::make() provided source does not contain any questions');
        }
        if ( ! is_array($source['questions'])) {
            throw new InvalidArgumentException('QuestionaryFactory::make() questions are not an array');
        }
        $questions = array();
        foreach ($source['questions'] as $key => $questionSource) {
            $questions[$key] = QuestionFactory::make($questionSource, $additionalData);
        }
        $source['questions'] = $questions;
        $questionary = new Questionary($source);
        return $questionary;
    }

    /**
     * @param int $code
     * @return array
     * @throws \Subscribo\Api1\Exceptions\InvalidArgumentException
     */
    protected static function assembleFromCode($code)
    {
        switch ($code) {
            case Questionary::CODE_NEW_CUSTOMER_EMAIL:
                $result = [
                    'title' => 'Please provide your actual email to finish your registration.',
                    'questions' => ['email' => Question::CODE_NEW_CUSTOMER_EMAIL_EMAIL],
                ];
                break;
            case Questionary::CODE_LOGIN_OR_NEW_ACCOUNT:
                $result = [
                    'title' => 'Would you like to login to your existing account or create a new one?',
                    'questions' => [
                        'email' => Question::CODE_LOGIN_OR_NEW_ACCOUNT_EMAIL,
                        'password' => Question::CODE_LOGIN_OR_NEW_ACCOUNT_PASSWORD,
                    ],
                ];
                break;
            case Questionary::CODE_MERGE_OR_NEW_ACCOUNT:
                $result = [
                    'title' => 'Would you like to use your existing account, created for different service, or to create now a new one?',
                    'questions' => ['service' => Question::CODE_MERGE_OR_NEW_ACCOUNT_SELECT_SERVICE],
                ];
                break;
            default:
                throw new InvalidArgumentException(sprintf("QuestionaryFactory::assembleFromCode() unrecognized code '%s'", $code));
        }
        $result['code'] = $code;
        return $result;
    }

}
