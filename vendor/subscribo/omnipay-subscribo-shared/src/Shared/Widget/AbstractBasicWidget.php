<?php

namespace Subscribo\Omnipay\Shared\Widget;

use Subscribo\Omnipay\Shared\Interfaces\WidgetInterface;
use Omnipay\Common\Helper;

/**
 * Abstract Class AbstractBasicWidget
 *
 * @package Subscribo\OmnipaySubscriboShared
 */
abstract class AbstractBasicWidget implements WidgetInterface
{
    protected $parameters = [];

    public function __construct($parameters = [])
    {
        $this->initialize($parameters);
    }

    public function __toString()
    {
        if ($this->isRenderable()) {
            return $this->render();
        }
        return '';
    }

    /**
     * @param array $parameters
     * @return $this
     */
    public function initialize($parameters = [])
    {
        $defaultParameters = [];
        foreach($this->getDefaultParameters() as $key => $value) {
            $defaultParameters[$key] = is_array($value) ? reset($value) : $value;
        }
        $parameters = array_replace($defaultParameters, $parameters);

        return $this->loadParameters($parameters);
    }

    /**
     * @param array $parameters
     * @return $this
     */
    public function loadParameters($parameters = [])
    {
        Helper::initialize($this, $parameters);
        return $this;
    }

    /**
     * @return array
     */
    public function getDefaultParameters()
    {
        return [];
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    protected function getParameter($key)
    {
        if (array_key_exists($key, $this->parameters)) {
            return $this->parameters[$key];
        }
        return null;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    protected function setParameter($key, $value)
    {
        $this->parameters[$key] = $value;
        return $this;
    }
}
