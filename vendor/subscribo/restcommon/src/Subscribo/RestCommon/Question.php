<?php namespace Subscribo\RestCommon;

class Question
{
    const TYPE_SELECT = 'select';

    const TYPE_EMAIL = 'email';

    const TYPE_PASSWORD = 'password';

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

    /** @var array */
    protected $validationRules = array();

    protected $selectOptions = array();

    public function __construct(array $data = array())
    {
        if (is_array($data)) {
            $this->import($data);
        }
    }

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
    }

    public function export()
    {
        $result = [
            'type' => $this->type,
            'code' => $this->code,
            'text' => $this->text,
            'validationRules' => $this->validationRules,
        ];
        if ($this->selectOptions) {
            $result['selectOptions'] = $this->selectOptions;
        }
        return $result;
    }

    public function getValidationRules()
    {
        $rules = is_string($this->validationRules) ? explode('|', $this->validationRules) : $this->validationRules;
        $rules = $this->addValidationRulesBasedOnType($rules);
        return $rules;
    }

    public function getSelectOptions()
    {
        return $this->selectOptions;
    }

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

    protected function setValidationRules($validationRules)
    {
        $this->validationRules = $validationRules;
        return $this;
    }

    protected function setSelectOptions($selectOptions)
    {
        $this->selectOptions = $selectOptions;
        return $this;
    }

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
}
