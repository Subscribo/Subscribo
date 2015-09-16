<?php

namespace Subscribo\Omnipay\Shared\Widget;

use PHPUnit_Framework_TestCase;
use Subscribo\Omnipay\Shared\Widget\AbstractBasicWidget;
use Subscribo\Omnipay\Shared\Exception\WidgetInvalidRenderingParametersException;

class AbstractBasicWidgetTest extends PHPUnit_Framework_TestCase
{
    public function testRenderSuccess()
    {
        $widget = new ExtendedAbstractBasicWidgetForTesting();
        $text = 'This is widget';
        $widget->stringToRender = $text;
        $this->assertTrue($widget->isRenderable());
        $this->assertSame($text, $widget->render());
        $this->assertSame($text, (string) $widget);
    }

    public function testRenderNotRenderable()
    {
        $widget = new ExtendedAbstractBasicWidgetForTesting();
        $this->assertFalse($widget->isRenderable());
        $this->assertSame('', (string) $widget);
    }

    public function testRenderParameters()
    {
        $widget = new ExtendedAbstractBasicWidgetForTesting();
        $text = 'This is widget';
        $parameters = ['stringToRender' => $text];
        $this->assertTrue($widget->isRenderable($parameters));
        $this->assertSame($text, $widget->render($parameters));
        $this->assertSame('', (string) $widget);
    }

    /**
     * @expectedException \Subscribo\Omnipay\Shared\Exception\WidgetInvalidRenderingParametersException
     */
    public function testRenderFailure()
    {
        $widget = new ExtendedAbstractBasicWidgetForTesting();
        $widget->render();
    }

    public function testSimpleWidget()
    {
        $widget= new SimpleExtendedAbstractBasicWidgetForTesting(['someOption' => 'someValue']);
        $this->assertSame('Simple widget', (string) $widget);
        $this->assertTrue($widget->isRenderable());
        $this->assertSame('Simple widget', $widget->render());
        $this->assertSame([], $widget->getDefaultParameters());
        $this->assertSame([], $widget->getParameters());
        $this->assertSame($widget, $widget->initialize(['nonexistent' => 'some value']));
        $this->assertSame([], $widget->getParameters());
        $this->assertSame($widget, $widget->loadParameters(['nonexistent2' => 'some value2']));
        $this->assertSame([], $widget->getParameters());
    }

    public function testSettersAndGetters()
    {
        $widget = new WithParametersExtendedAbstractBasicWidgetForTesting();
        $defaults = $widget->getDefaultParameters();
        foreach($defaults as $key => $val) {
            $getter = 'get'.ucfirst($key);
            $setter = 'set'.ucfirst($key);
            $this->assertTrue(method_exists($widget, $getter), "Getter '".$getter."' does not exists for default parameter '".$key."'");
            $this->assertTrue(method_exists($widget, $setter), "Setter '".$setter."' does not exists for default parameter '".$key."'");
            $testValue = uniqid();
            $this->assertSame($widget, $widget->$setter($testValue), "Setter '".$setter."' does not have fluent interface'");
            $this->assertSame($testValue, $widget->$getter(), "Getter '".$getter."' does not return the same value as the one provided for setter '".$setter."'");
        }
    }

    public function testParametersEmptyConstruct()
    {
        $widget = new WithParametersExtendedAbstractBasicWidgetForTesting();
        $expectedDefaults = [
            'selection' => ['one', 'two', 'three'],
            'simpleParameter' => 'Some string',
        ];
        $expectedUnchanged = [
            'selection' => 'one',
            'simpleParameter' => 'Some string',
        ];
        $this->assertSame($expectedDefaults, $widget->getDefaultParameters());
        $this->assertSame($expectedUnchanged, $widget->getParameters());
        $this->assertNull($widget->getNonDefaultParameter());
        $this->assertSame($widget, $widget->setNonDefaultParameter('Some value'));
        $this->assertSame('Some value', $widget->getNonDefaultParameter());
        $expected = $expectedUnchanged;
        $expected['nonDefaultParameter'] = 'Some value';
        $this->assertSame($expectedDefaults, $widget->getDefaultParameters());
        $this->assertSame($expected, $widget->getParameters());
        $this->assertSame($widget, $widget->loadParameters(['simpleParameter' => 'Different string']));
        $expected['simpleParameter'] = 'Different string';
        $this->assertSame($expectedDefaults, $widget->getDefaultParameters());
        $this->assertSame($expected, $widget->getParameters());
        $this->assertSame($widget, $widget->initialize(['selection' => 'two']));
        // initialize resets parameters in defaults to defaults
        $expected = $expectedUnchanged;
        $expected['selection'] = 'two';
        $expected['nonDefaultParameter'] = 'Some value';
        $this->assertSame($expectedDefaults, $widget->getDefaultParameters());
        $this->assertSame($expected, $widget->getParameters());
        $this->assertSame('Some value', $widget->getNonDefaultParameter());
    }

    public function testParametersWithConstruct()
    {
        $parameters = [
            'nonDefaultParameter' => 5,
            'simpleParameter' => '',
        ];
        $widget = new WithParametersExtendedAbstractBasicWidgetForTesting($parameters);
        $expected = [
            'selection' => 'one',
            'simpleParameter' => '',
            'nonDefaultParameter' => 5,
        ];
        $this->assertSame($expected, $widget->getParameters());
        $this->assertSame('', $widget->getSimpleParameter());
        $this->assertSame(5, $widget->getNonDefaultParameter());
        $this->assertSame('one', $widget->getSelection());
        $this->assertSame($widget, $widget->setSimpleParameter(null));
        $this->assertNull($widget->getSimpleParameter());
        $expected['simpleParameter'] = null;
        $this->assertSame($expected, $widget->getParameters());
        $this->assertSame($widget, $widget->setNonDefaultParameter(null));
        $this->assertNull($widget->getNonDefaultParameter());
        $expected['nonDefaultParameter'] = null;
        $this->assertSame($expected, $widget->getParameters());
    }

    public function testSettersAndGettersSimple()
    {
        $widget = new SimpleExtendedAbstractBasicWidgetForTesting();
        $defaults = $widget->getDefaultParameters();
        foreach($defaults as $key => $val) {
            $getter = 'get'.ucfirst($key);
            $setter = 'set'.ucfirst($key);
            $this->assertTrue(method_exists($widget, $getter), "Getter '".$getter."' does not exists for default parameter '".$key."'");
            $this->assertTrue(method_exists($widget, $setter), "Setter '".$setter."' does not exists for default parameter '".$key."'");
            $testValue = uniqid();
            $this->assertSame($widget, $widget->$setter($testValue), "Setter '".$setter."' does not have fluent interface'");
            $this->assertSame($testValue, $widget->$getter(), "Getter '".$getter."' does not return the same value as the one provided for setter '".$setter."'");
        }
    }


}


class ExtendedAbstractBasicWidgetForTesting extends AbstractBasicWidget
{
    public $stringToRender;

    public function isRenderable($parameters = [])
    {
        if (isset($parameters['stringToRender'])) {
            return true;
        }
        return ! is_null($this->stringToRender);
    }

    public function render($parameters = [])
    {
        if (isset($parameters['stringToRender'])) {
            return $parameters['stringToRender'];
        }
        if (is_null($this->stringToRender)) {
            throw new WidgetInvalidRenderingParametersException('stringToRender not provided');
        }
        return $this->stringToRender;
    }
}


class SimpleExtendedAbstractBasicWidgetForTesting extends AbstractBasicWidget
{
    public function isRenderable($parameters = [])
    {
        return true;
    }

    public function render($parameters = [])
    {
        return 'Simple widget';
    }
}


class WithParametersExtendedAbstractBasicWidgetForTesting extends AbstractBasicWidget
{
    public function isRenderable($parameters = [])
    {
        return true;
    }

    public function render($parameters = [])
    {
        return 'Widget with parameters';
    }

    public function getDefaultParameters()
    {
        return [
            'selection' => ['one', 'two', 'three'],
            'simpleParameter' => 'Some string',
        ];
    }

    public function getSelection()
    {
        return $this->getParameter('selection');
    }

    public function setSelection($value)
    {
        return $this->setParameter('selection', $value);
    }

    public function getSimpleParameter()
    {
        return $this->getParameter('simpleParameter');
    }

    public function setSimpleParameter($value)
    {
        return $this->setParameter('simpleParameter', $value);
    }

    public function getNonDefaultParameter()
    {
        return $this->getParameter('nonDefaultParameter');
    }

    public function setNonDefaultParameter($value)
    {
        return $this->setParameter('nonDefaultParameter', $value);
    }
}
