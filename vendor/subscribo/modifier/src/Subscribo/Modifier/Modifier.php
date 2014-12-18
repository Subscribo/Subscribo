<?php namespace Subscribo\Modifier;


/**
 * Class Modifier
 *
 * Inspired among others by Laravel Validator
 *
 * @package Subscribo\Modifier
 */
class Modifier {

    protected $methods = array(
        'non_printable_to_null' => array('self', 'modifyNonPrintableToNull'),
    );


    public function modifyMultiple(array $values, array $ruleSet)
    {
        $result = array();
        foreach ($values as $key => $value) {
            if (array_key_exists($key, $ruleSet)) {
                $result[$key] = $this->modifyOne($value, $ruleSet[$key]);
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }


    public function modifyOne($value, $rules)
    {
        if (empty($rules)) {
            return $value;
        }
        if (is_string($rules)) {
            $rules = explode('|', $rules);
        }
        if ( ! is_array($rules)) {
            throw new \InvalidArgumentException('Second parameter for method Modifier::modifyOne should be an array or a string.');
        }
        foreach ($rules as $rule) {
            $value = $this->applyRule($value, $rule);
        }
        return $value;
    }


    /**
     * @param mixed $value
     * @param string|array $rule
     * @return mixed
     * @throws \InvalidArgumentException
     */
    protected function applyRule($value, $rule)
    {
        if (empty($rule)) {
            return $value;
        }
        list($ruleName, $arguments) = $this->parseRule($rule);

        if ( ! array_key_exists($ruleName, $this->methods)) {
            throw new \InvalidArgumentException("Unknown modification rule '".$ruleName."'");
        }
        array_unshift($arguments, $value);
        return call_user_func_array($this->methods[$ruleName], $arguments);
    }

    protected function parseRule($rule)
    {
        if (is_array($rule)) {
            $ruleName = array_shift($rule);
            return array($ruleName, $rule);
        }
        if ( ! is_string($rule)) {
            throw new \InvalidArgumentException('Modification rule should be defined as an array or as a string.');
        }
        $parts = explode(':', $rule);
        $ruleName = array_shift($parts);
        if (empty($parts)) {
            $parts = '';
        }
        $arguments = $this->parseArgumentString($parts);
        return array($ruleName, $arguments);
    }


    /**
     * @param string $arguments String of comma delimited arguments
     * @return array
     */
    protected function parseArgumentString($arguments)
    {
        return explode(',', $arguments);
    }

    /**
     * If parameter is an empty string or a string, containing only whitespace characters, returns null, otherwise return original value
     *
     * @param mixed|string $value
     * @return mixed|null
     */
    protected function modifyNonPrintableToNull($value)
    {
        if ( ! is_string($value)) {
            return $value;
        }
        if (trim($value)) {
            return $value;
        }
        return null;
    }

}
