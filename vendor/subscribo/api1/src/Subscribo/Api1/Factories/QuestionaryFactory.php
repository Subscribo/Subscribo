<?php namespace Subscribo\Api1\Factories;

use Subscribo\Localization\Interfaces\LocalizerInterface;
use Subscribo\Api1\Exceptions\InvalidArgumentException;
use Subscribo\RestCommon\Questionary;
use Subscribo\RestCommon\Question;
use Subscribo\Api1\Factories\QuestionFactory;

/**
 * Class QuestionaryFactory
 *
 * @package Subscribo\Api1
 */
class QuestionaryFactory
{
    /** @var \Subscribo\Localization\Interfaces\LocalizerInterface  */
    protected $localizer;

    public function __construct(LocalizerInterface $localizer)
    {
        $this->localizer = $localizer->duplicate('questionary', 'api1');
    }

    /**
     * @param Questionary|array|string|int $source
     * @param array $additionalData
     * @return Questionary
     * @throws \Subscribo\Api1\Exceptions\InvalidArgumentException
     */
    public function make($source, array $additionalData = array())
    {
        if ($source instanceof Questionary) {
            return $source;
        }
        $source = is_string($source) ? json_decode($source, true) : $source;
        $source = is_int($source) ? $this->assembleFromCode($source) : $source;
        if ( ! is_array($source)) {
            throw new InvalidArgumentException('QuestionaryFactory::make() provided source have incorrect type');
        }
        if (empty($source['questions'])) {
            throw new InvalidArgumentException('QuestionaryFactory::make() provided source does not contain any questions');
        }
        if ( ! is_array($source['questions'])) {
            throw new InvalidArgumentException('QuestionaryFactory::make() questions are not an array');
        }
        $questionFactory = new QuestionFactory($this->localizer);
        $questions = array();
        foreach ($source['questions'] as $key => $questionSource) {
            $questions[$key] = $questionFactory->make($questionSource, $additionalData);
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
    protected function assembleFromCode($code)
    {
        switch ($code) {
            case Questionary::CODE_NEW_CUSTOMER_EMAIL:
                $result = [
                    /// TRANSLATORS: English: Please provide your actual email to finish your registration.
                    'title' => $this->localizer->trans('questionary.title.CODE_NEW_CUSTOMER_EMAIL'),
                    'questions' => ['email' => Question::CODE_NEW_CUSTOMER_EMAIL_EMAIL],
                ];
                break;
            case Questionary::CODE_LOGIN_OR_NEW_ACCOUNT:
                $result = [
                    /// TRANSLATORS: English: Would you like to login to your existing account or create a new one?
                    'title' => $this->localizer->trans('questionary.title.CODE_LOGIN_OR_NEW_ACCOUNT'),
                    'questions' => [
                        'email' => Question::CODE_LOGIN_OR_NEW_ACCOUNT_EMAIL,
                        'password' => Question::CODE_LOGIN_OR_NEW_ACCOUNT_PASSWORD,
                    ],
                ];
                break;
            case Questionary::CODE_MERGE_OR_NEW_ACCOUNT:
                $result = [
                    /// TRANSLATORS: English: Would you like to use your existing account, created for different service, or to create now a new one?
                    'title' => $this->localizer->trans('questionary.title.CODE_MERGE_OR_NEW_ACCOUNT'),
                    'questions' => ['service' => Question::CODE_MERGE_OR_NEW_ACCOUNT_SELECT_SERVICE],
                ];
                break;
            case Questionary::CODE_CONFIRM_ACCOUNT_MERGE_PASSWORD:
                $result = [
                    /// TRANSLATORS: English: Would you like to merge your accounts?
                    'title' => $this->localizer->trans('questionary.title.CODE_CONFIRM_ACCOUNT_MERGE_PASSWORD'),
                    'questions' => [
                        'merge' => Question::CODE_CONFIRM_MERGE_ACCOUNT_YES_OR_NO,
                        'password' => Question::CODE_CONFIRM_MERGE_ACCOUNT_PASSWORD,
                    ],
                ];
                break;
            case Questionary::CODE_CONFIRM_ACCOUNT_MERGE_SIMPLE:
                $result = [
                    /// TRANSLATORS: English: Would you like to merge your accounts?
                    'title' => $this->localizer->trans('questionary.title.CODE_CONFIRM_ACCOUNT_MERGE_SIMPLE'),
                    'questions' => ['merge' => Question::CODE_CONFIRM_MERGE_ACCOUNT_YES_OR_NO],
                ];
                break;
            default:
                throw new InvalidArgumentException(sprintf("QuestionaryFactory::assembleFromCode() unrecognized code '%s'", $code));
        }
        $result['code'] = $code;
        return $result;
    }

}
