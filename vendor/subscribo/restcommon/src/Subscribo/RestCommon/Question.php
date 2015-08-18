<?php namespace Subscribo\RestCommon;

class Question
{
    const TYPE_GROUP = 'group'; //Not actually a type of question, but reserved for denoting array content being QuestionGroup

    const TYPE_TEXT = 'text';

    const TYPE_EMAIL = 'email';

    const TYPE_PASSWORD = 'password';

    const TYPE_CHECKBOX = 'checkbox';

    const TYPE_SELECT = 'select';

    const TYPE_NUMBER_SELECT = 'number_select';

    const TYPE_RANGE = 'range';

    const TYPE_NUMBER = 'number';

    const TYPE_DATE = 'date';

    const TYPE_DAY = 'day';

    const TYPE_MONTH = 'month';

    const TYPE_YEAR = 'year';

    const CODE_NEW_CUSTOMER_EMAIL_EMAIL = 1010;

    const CODE_LOGIN_OR_NEW_ACCOUNT_EMAIL = 2010;

    const CODE_LOGIN_OR_NEW_ACCOUNT_PASSWORD = 2020;

    const CODE_MERGE_OR_NEW_ACCOUNT_SELECT_SERVICE = 3010;

    const CODE_CONFIRM_MERGE_ACCOUNT_YES_OR_NO = 4010;

    const CODE_CONFIRM_MERGE_ACCOUNT_PASSWORD = 4020;

    const CODE_GENERIC_QUESTION = 10000;

    const CODE_DATE_DAY = 20010;

    const CODE_DATE_MONTH = 20020;

    const CODE_DATE_YEAR = 20030;

    const CODE_CUSTOMER_BIRTH_DATE_DAY = 21010;

    const CODE_CUSTOMER_BIRTH_DATE_MONTH = 21020;

    const CODE_CUSTOMER_BIRTH_DATE_YEAR = 21030;

    const CODE_CUSTOMER_NATIONAL_IDENTIFICATION_NUMBER_NUMBER = 22010;

    /** @var string */
    public $type;

    /** @var int */
    public $code = 0;

    /** @var string|null */
    public $text;

    /** @var int|string */
    public $checkboxValue = 1;

    /** @var  int|string  */
    public $defaultValue;

    /** @var  int|null */
    public $minimumValue;

    /** @var  int|null */
    public $maximumValue;

    /** @var int|null */
    public $incrementStep;

    /** @var  string|null */
    public $validationAttributeName;

    /** @var array */
    protected $validationCustomValues = array();

    /** @var bool */
    public $addValidationCustomValuesFromSelect = true;

    /** @var string|array */
    protected $validationRules = array();

    /** @var array|string */
    protected $validationMessages = array();

    /** @var array */
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
        if (isset($data['text'])) {
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
        if ( array_key_exists('incrementStep', $data)) {
            $this->incrementStep = $data['incrementStep'];
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
        if (is_numeric($this->incrementStep)) {
            $result['incrementStep'] = $this->incrementStep;
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
        switch (strval($this->type)) {
            case static::TYPE_PASSWORD:

                return false;
            case static::TYPE_TEXT:
            case static::TYPE_EMAIL:
            case static::TYPE_CHECKBOX:
            case static::TYPE_SELECT:
            case static::TYPE_NUMBER_SELECT:
            case static::TYPE_RANGE:
            case static::TYPE_NUMBER:
            case static::TYPE_DATE:
            case static::TYPE_DAY:
            case static::TYPE_MONTH:
            case static::TYPE_YEAR:

                return true;
        }

        return false;
    }

    /**
     * @return int|null
     */
    public function getMinimumValue()
    {
        if (isset($this->minimumValue)) {

            return $this->minimumValue;
        }
        switch (strval($this->type)) {
            case static::TYPE_DAY:
            case static::TYPE_MONTH:

                return 1;
        }

        return null;
    }

    /**
     * @return int|null
     */
    public function getMaximumValue()
    {
        if (isset($this->maximumValue)) {

            return $this->maximumValue;
        }
        switch (strval($this->type)) {
            case static::TYPE_DAY:

                return 31;
            case static::TYPE_MONTH:

                return 12;
        }

        return null;
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
        switch (strval($this->type))
        {
            case static::TYPE_EMAIL:
                if (false === array_search('email', $rules, true)) {
                    $rules[] = 'email';
                }
                break;
            case static::TYPE_SELECT:
                $optionKeys = array_keys($this->getSelectOptions());
                $rule = array_filter($optionKeys, 'strlen');
                array_unshift($rule, 'in');
                $rules[] = $rule;
                break;
            case static::TYPE_CHECKBOX:
                $rule = 'in:'.$this->checkboxValue;
                if (false === array_search($rule, $rules, true)) {
                    $rules[] = $rule;
                }
                break;
            case static::TYPE_NUMBER_SELECT:
            case static::TYPE_RANGE:
            case static::TYPE_DAY:
            case static::TYPE_MONTH:
            case static::TYPE_YEAR:
                if (false === array_search('integer', $rules, true)) {
                    $rules[] = 'integer';
                }
                break;
            case static::TYPE_NUMBER:
                if (false === array_search('numeric', $rules, true)) {
                    $rules[] = 'numeric';
                }
                break;
            case static::TYPE_DATE:
                if (false === array_search('date', $rules, true)) {
                    $rules[] = 'date';
                }
                break;
        }

        return $rules;
    }

    /**
     * @param array $rules
     * @return array
     */
    protected function addValidationRulesBasedOnConstraints(array $rules = array())
    {
        $min = $this->getMinimumValue();
        $max = $this->getMaximumValue();
        if (is_numeric($min) and is_numeric($max)) {
            $rules[] = 'between:'.$min.','.$max;
        } elseif (is_numeric($min)) {
            $rules[] = 'min:'.$min;
        } elseif (is_numeric($max)) {
            $rules[] = 'max:'.$max;
        }

        return $rules;
    }
}
