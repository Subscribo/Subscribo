<?php

namespace Subscribo\Omnipay\Shared\Widget;

use Subscribo\Omnipay\Shared\Widget\AbstractBasicWidget;
use Subscribo\Omnipay\Shared\Exception\WidgetInvalidRenderingParametersException;

/**
 * Class AbstractWidget
 *
 * @package Subscribo\OmnipaySubscriboShared
 */
abstract class AbstractWidget extends AbstractBasicWidget
{
    /**
     * Returns (as array values) name of parameters, which are required (and non-empty) for widget rendering
     *
     * @return array
     */
    abstract public function getRequiredParameters();


    public function isRenderable($parameters = [])
    {
        if (is_array($parameters)) {
            $parameters = array_replace($this->getParameters(), $parameters);
        }
        $obstacles = $this->collectRenderingObstacles($parameters);
        return empty($obstacles);
    }

    /**
     * Merges provided parameters with those from objects and checks them
     *
     * @param array $parameters
     * @param bool|array $requirements - required parameter names or true for getting them from getRequiredParameters()
     * @return array
     * @throws \Subscribo\Omnipay\Shared\Exception\WidgetInvalidRenderingParametersException
     */
    protected function checkParameters($parameters, $requirements = true)
    {
        if (is_array($parameters)) {
            $parameters = array_replace($this->getParameters(), $parameters);
        }
        $obstacles = $this->collectRenderingObstacles($parameters, $requirements);
        if ($obstacles) {
            throw new WidgetInvalidRenderingParametersException(reset($obstacles));
        }
        return $parameters;
    }

    /**
     * Returns an array of possible problems for rendering widget or of some widget rendering functionality
     *
     * @param $parameters
     * @param bool|array $requirements - required parameter names or true for getting them from getRequiredParameters()
     * @return array
     */
    protected function collectRenderingObstacles($parameters, $requirements = true)
    {
        if ( ! is_array($parameters)) {
            return ['Parameters should be an array'];
        }
        if (true === $requirements) {
            $requirements = $this->getRequiredParameters();
        }
        $obstacles = [];
        foreach ($requirements as $requiredParameterName) {
            if (empty($parameters[$requiredParameterName])) {
                $obstacles[] = "Parameter '".$requiredParameterName."' is required";
            }
        }
        return $obstacles;
    }
}
