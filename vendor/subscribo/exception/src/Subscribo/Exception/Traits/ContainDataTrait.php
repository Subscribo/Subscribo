<?php namespace Subscribo\Exception\Traits;

/**
 * Trait ContainDataTrait
 *
 * Trait helping classes to implement ContainDataInterface
 *
 * Class using this trait need to have $_containedData property, which should be array
 * Note: you might need to adapt constructor and/or add a setter manually to be able to set $_containedData property
 *
 * @package Subscribo\Exception
 */
trait ContainDataTrait {

    /**
     * @return array
     */
    public function getData()
    {
        return $this->_containedData;
    }

    /**
     * @param null|array $default Default data to add, if corresponding keys are not already provided
     * @return null|array
     */
    public function getOutputData(array $default = null)
    {
        $keyName = $this->getKey();
        $keyData = $this->getKeyData();
        if (empty($keyData)) {
            return $default;
        }
        $content = array($keyName => $keyData);
        if (empty($default)) {
            return $content;
        }
        $result = array_merge_recursive($default, $content);
        return $result;
    }

    /**
     * @return array
     */
    public function getKeyData()
    {
        $keyName = $this->getKey();
        $data = $this->getData();
        if (empty($data[$keyName])) {
            return array();
        }
        if (is_array($data[$keyName])) {
            return $data[$keyName];
        }
        return array('content' => $data[$keyName]);
    }

    /**
     * @return string
     */
    public static function getKey()
    {
        return 'error';
    }
}
