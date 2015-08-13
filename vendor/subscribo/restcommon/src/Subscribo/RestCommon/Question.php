<?php namespace Subscribo\RestCommon;

class Question
{
    const TYPE_GROUP = 'group'; //Not actually a type of question, but reserved for denoting array content being QuestionGroup

    const TYPE_TEXT = 'text';

    const TYPE_EMAIL = 'email';

    const TYPE_PASSWORD = 'password';

    const TYPE_CHECKBOX = 'checkbox';

    const TYPE_SELECT = 'select';

    const TYPE_DAY = 'day';

    const TYPE_MONTH = 'month';

    const TYPE_YEAR = 'year';

    const CODE_NEW_CUSTOMER_EMAIL_EMAIL = 1010;

    const CODE_LOGIN_OR_NEW_ACCOUNT_EMAIL = 2010;

    const CODE_LOGIN_OR_NEW_ACCOUNT_PASSWORD = 2020;

    const CODE_MERGE_OR_NEW_ACCOUNT_SELECT_SERVICE = 3010;

    const CODE_CONFIRM_MERGE_ACCOUNT_YES_OR_NO = 4010;

    const CODE_CONFIRM_MERGE_ACCOUNT_PASSWORD = 4020;


    /** @var string */
    public $type;

    /** @var int */
    public $code = 0;

    /** @var string */
    public $text;

    /** @var int|string */
    public $checkboxValue = 1;

    /** @var  int|string  */
    public $defaultValue;

    /** @var  int|null */
    public $minimumValue;

    /** @var  int|null */
    public $maximumValue;

    /** @var  string|null */
    public $validationAttributeName;

    /** @var array  */
    protected $validationCustomValues = array();

    /** @var bool  */
    public $addValidationCustomValuesFromSelect = true;

    /** @var string|array */
    protected $validationRules = array();

    /** @var array|string  */
    protected $validationMessages = array();

    protected $selectOptions = array();

    /** @var  bool|null */
    protected $rememberValueOnError;

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        if (is_array($data)) {
            $this->import($data);
        }
    }

    /**
     * @param array $data
     */
    public function import(array $data)
    {
        if ( ! empty($data['type'])) {
            $this->type = $data['type'];
        }
        if (array_key_exists('code', $data)) {
            $this->code = $data['code'];
        }
        if ( ! empty($data['text'])) {
            $this->text = $data['text'];
        }
        if ( ! empty($data['validationRules'])) {
            $this->setValidationRules($data['validationRules']);
        }
        if ( ! empty($data['selectOptions'])) {
            $this->setSelectOptions($data['selectOptions']);
        }
        if (isset($data['validationMessages'])) {
            $this->setValidationMessages($data['validationMessages']);
        }
        if ( ! empty($data['validationAttributeName'])) {
            $this->validationAttributeName = $data['validationAttributeName'];
        }
        if ( ! empty($data['validationCustomValues'])) {
            $this->setValidationCustomValues($data['validationCustomValues']);
        }
        if ( array_key_exists('addValidationCustomValuesFromSelect', $data)) {
            $this->addValidationCustomValuesFromSelect = $data['addValidationCustomValuesFromSelect'];
        }
        if ( array_key_exists('rememberValueOnError', $data)) {
            $this->setRememberValueOnError($data['rememberValueOnError']);
        }
        if ( ! empty($data['checkboxValue'])) {
            $this->checkboxValue = $data['checkboxValue'];
        }
        if ( array_key_exists('defaultValue', $data)) {
            $this->defaultValue = $data['defaultValue'];
        }
        if ( array_key_exists('minimumValue', $data)) {
            $this->minimumValue = $data['minimumValue'];
        }
        if ( array_key_exists('maximumValue', $data)) {
            $this->maximumValue = $data['maximumValue'];
        }
    }

    /**
     * @return array
     */
    public function export()
    {
        $result = [
            'type' => $this->type,
            'code' => $this->code,
            'text' => $this->text,
            'validationRules' => $this->validationRules,
            'validationMessages' => $this->validationMessages,
            'validationAttributeName' => $this->validationAttributeName,
            'validationCustomValues' => $this->validationCustomValues,
            'addValidationCustomValuesFromSelect' => $this->addValidationCustomValuesFromSelect,
            'rememberValueOnError' => $this->rememberValueOnError,
            'checkboxValue' => $this->checkboxValue,
            'defaultValue' => $this->defaultValue,
        ];
        if (is_numeric($this->minimumValue)) {
            $result['minimumValue'] = $this->minimumValue;
        }
        if (is_numeric($this->maximumValue)) {
            $result['maximumValue'] = $this->maximumValue;
        }
        if ($this->selectOptions) {
            $result['selectOptions'] = $this->selectOptions;
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getValidationRules()
    {
        $rules = is_string($this->validationRules) ? explode('|', $this->validationRules) : $this->validationRules;
        $rules = $this->addValidationRulesBasedOnType($rules);
        $rules = $this->addValidationRulesBasedOnConstraints($rules);

        return $rules;
    }

    /**
     * @return array
     */
    public function getValidationMessages()
    {
        if (empty($this->validationMessages)) {
            return array();
        }
        if (is_array($this->validationMessages)) {
            return $this->validationMessages;
        }
        $message = (string) $this->validationMessages;
        $result = [];
        $rules = $this->getValidationRules();
        foreach ($rules as $ruleSource) {
            $rule = is_array($ruleSource) ? $ruleSource : explode(':', $ruleSource);
            $ruleName = reset($rule);
            $result[$ruleName] = $message;
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getValidationCustomValues()
    {
        $result = $this->validationCustomValues;
        if ($this->addValidationCustomValuesFromSelect) {
            $result = array_replace($this->selectOptions, $result);
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getSelectOptions()
    {
        return $this->selectOptions;
    }

    /**
     * @param array $additionalSelectOptions
     * @return $this
     */
    public function prependSelectOptions(array $additionalSelectOptions)
    {
        $oldSelectOptions = $this->getSelectOptions();
        $firstValue = reset($oldSelectOptions);
        $firstKey = key($oldSelectOptions);
        $bidPresent = $oldSelectOptions and ( ! strlen($firstKey));

        $newSelectOptions = array();
        if ($bidPresent) {
            $newSelectOptions[$firstKey] = $firstValue;
        }
        foreach ($additionalSelectOptions as $key => $value) {
            $newSelectOptions[$key] = $value;
        }
        foreach ($oldSelectOptions as $key => $value) {
            if (($key === $firstKey) and $bidPresent) {
                continue;
            }
            $newSelectOptions[$key] = $value;
        }
        $this->setSelectOptions($newSelectOptions);

        return $this;
    }

    /**
     * @return bool
     */
    public function getRememberValueOnError()
    {
        if (is_bool($this->rememberValueOnError)) {

            return $this->rememberValueOnError;
        }
        switch ($this->type) {
            case static::TYPE_PASSWORD:
                return false;
            case static::TYPE_EMAIL:
            case static::TYPE_SELECT:
                return true;
        }

        return false;
    }

    /**
     * @param string|array $validationRules
     * @return $this
     */
    protected function setValidationRules($validationRules)
    {
        $this->validationRules = $validationRules;

        return $this;
    }

    /**
     * @param array $selectOptions
     * @return $this
     */
    protected function setSelectOptions(array $selectOptions)
    {
        $this->selectOptions = $selectOptions;

        return $this;
    }

    /**
     * @param array|string $validationMessages
     * @return $this
     */
    protected function setValidationMessages($validationMessages)
    {
        $this->validationMessages = $validationMessages;

        return $this;
    }

    /**
     * @param array $validationCustomValues
     * @return $this
     */
    protected function setValidationCustomValues(array $validationCustomValues)
    {
        $this->validationCustomValues = $validationCustomValues;

        return $this;
    }

    /**
     * @param bool|null $rememberValueOnError
     * @return $this
     */
    protected function setRememberValueOnError($rememberValueOnError)
    {
        $this->rememberValueOnError = $rememberValueOnError;

        return $this;
    }

    /**
     * @param array $rules
     * @return array
     */
    protected function addValidationRulesBasedOnType(array $rules = array())
    {
        $type = $this->type;
        if ($this::TYPE_EMAIL === $type) {
            if (false === array_search('email', $rules, true)) {
                $rules[] = 'email';
            }
        } elseif ($this::TYPE_SELECT === $type) {
            $optionKeys = array_keys($this->getSelectOptions());
            $rule = array_filter($optionKeys, 'strlen');
            array_unshift($rule, 'in');
            $rules[] = $rule;
        }

        return $rules;
    }

    /**
     * @param array $rules
     * @return array
     */
    protected function addValidationRulesBasedOnConstraints(array $rules = array())
    {
        if (is_numeric($this->minimumValue) and is_numeric($this->maximumValue)) {
            $rules[] = 'between:'.$this->minimumValue.','.$this->maximumValue;
        } elseif (is_numeric($this->minimumValue)) {
            $rules[] = 'min:'.$this->minimumValue;
        } elseif (is_numeric($this->maximumValue)) {
            $rules[] = 'max:'.$this->maximumValue;
        }

        return $rules;
    }
}
