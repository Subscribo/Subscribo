<?php

namespace Subscribo\Omnipay\Shared\Widget;

use Subscribo\Omnipay\Shared\Widget\AbstractWidget;

/**
 * Class SimpleWidget
 *
 * @package Subscribo\OmnipaySubscriboShared
 */
class SimpleWidget extends AbstractWidget
{
    public function getRequiredParameters()
    {
        return ['content'];
    }


    public function getDefaultParameters()
    {
        return ['content' => ''];
    }


    public function render($parameters = [])
    {
        $parameters = $this->checkParameters($parameters);
        return $this->processContent($parameters['content'], $parameters);
    }

    /**
     * Method intended for overriding in child class, if needed for content processing
     *
     * @param string $content
     * @param array $parameters
     * @return mixed
     */
    protected function processContent($content, array $parameters = [])
    {
        return $content;
    }

    /**
     * @return string|null
     */
    public function getContent()
    {
        return $this->getParameter('content');
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setContent($value)
    {
        return $this->setParameter('content', $value);
    }
}
