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
        $data = $this->getData();
        if ( ! array_key_exists('output', $data)) {
            return $default;
        }
        $content = $data['output'];
        if (empty($default)) {
            return $content;
        }
        if (is_array($content)) {
            $result = Arr::mergeNatural($default, $content);
        } else {
            $result = $default;
            $result['content'] = $content;
        }
        return $result;
    }
}
