<?php namespace Subscribo\Exception\Traits;

use Subscribo\Support\Arr;

/**
 * Class ContainDataTrait
 *
 * Trait helping classes to implement ContainDataInterface
 *
 * Note: you might need to adapt constructor and/or add a setter manually to be able to set $_containedData property
 *
 * @package Subscribo\Exception
 */
trait ContainDataTrait {

    protected $_containedData = array();

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
        $errorData = $this->getErrorData();
        if (empty($errorData)) {
            return $default;
        }
        $content = array('error' => $errorData);
        if (empty($default)) {
            return $content;
        }
        $result = Arr::mergeNatural($default, $content);
        return $result;
    }

    /**
     * @return array
     */
    public function getErrorData()
    {
        $data = $this->getData();
        if (empty($data['error'])) {
            return array();
        }
        if (is_array($data['error'])) {
            return $data['error'];
        }
        return array('content' => $data['error']);
    }
}
