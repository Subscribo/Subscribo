<?php

namespace Subscribo\TransactionPluginManager\Factories;

use InvalidArgumentException;
use Subscribo\RestCommon\Widget;
use Subscribo\TransactionPluginManager\Factories\AbstractServerRequestFactory;
use Subscribo\Omnipay\Shared\Interfaces\WidgetInterface;

/**
 * Class WidgetFactory
 *
 * @package Subscribo\TransactionPluginManager
 */
class WidgetFactory extends AbstractServerRequestFactory
{
    /**
     * @param Widget|WidgetInterface|string|array $widget
     * @return Widget
     * @throws \InvalidArgumentException
     */
    public function make($widget)
    {
        if ($widget instanceof Widget) {

            return $widget;
        } elseif ($widget instanceof WidgetInterface) {

            return $this->assembleFromString($widget->render());
        } elseif (is_string($widget)) {

            return $this->assembleFromString($widget);
        } elseif (is_array($widget)) {

            return $this->assembleFromArray($widget);
        } else {

            throw new InvalidArgumentException('Invalid widget argument type');
        }
    }

    /**
     * @param array $data
     * @return Widget
     */
    protected function assembleFromArray(array $data)
    {
        return new Widget($this->addDefaultDomainToData($data));
    }

    /**
     * @param string $widgetContent
     * @return Widget
     */
    protected function assembleFromString($widgetContent)
    {
        return $this->assembleFromArray(['content' => $widgetContent]);
    }
}
