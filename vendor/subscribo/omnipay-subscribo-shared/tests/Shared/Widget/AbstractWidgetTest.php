<?php

namespace Subscribo\Omnipay\Shared\Widget;

use PHPUnit_Framework_TestCase;
use Subscribo\Omnipay\Shared\Widget\AbstractWidget;

class AbstractWidgetTest extends PHPUnit_Framework_TestCase
{
    public function testEmptyWidget()
    {
        $widget = new ExtendedAbstractWidgetForTesting();

        $this->assertFalse($widget->isRenderable());
        $this->assertSame(['simpleParameter'], $widget->getRequiredParameters());
        $expectedDefaultParameters = [
            'simpleParameter' => ['', 'one', 'two'],
            'someArrayParameter' => [[]],
        ];
        $this->assertSame($expectedDefaultParameters, $widget->getDefaultParameters());
        $parameters = $widget->getParameters();
        $this->assertSame('', $parameters['simpleParameter']);
        $this->assertSame([], $parameters['someArrayParameter']);

        $this->assertSame('', $widget->getSimpleParameter());
        $this->assertSame([], $widget->getSomeArrayParameter());

        $this->assertSame($widget, $widget->setSomeArrayParameter(['some', 'content']));
        $this->assertSame(['some', 'content'], $widget->getSomeArrayParameter());
        $this->assertFalse($widget->isRenderable());
        $this->assertFalse($widget->isRenderable('wrong argument'));
        $this->assertFalse($widget->isRenderable(['simpleParameter' => '']));

        $expectedValue2 = 'Simple parameter is two';
        $parameters = ['simpleParameter' => 'two'];
        $this->assertTrue($widget->isRenderable($parameters));
        $this->assertSame($expectedValue2, $widget->render($parameters));

        $this->assertFalse($widget->isRenderable());
    }


    public function testWidgetFilledBySetters()
    {
        $widget = new ExtendedAbstractWidgetForTesting();

        $this->assertFalse($widget->isRenderable());

        $this->assertSame($widget, $widget->setSimpleParameter('one'));
        $this->assertTrue($widget->isRenderable());
        $this->assertSame('one', $widget->getSimpleParameter());
        $expectedValue = 'Simple parameter is one';
        $this->assertSame($expectedValue, (string) $widget);
        $this->assertSame($expectedValue, $widget->render());

        $this->assertSame($widget, $widget->setSomeArrayParameter(['some', 'content']));
        $this->assertSame(['some', 'content'], $widget->getSomeArrayParameter());
        $this->assertTrue($widget->isRenderable());
        $this->assertSame($expectedValue, (string) $widget);
        $this->assertSame($expectedValue, $widget->render());

        $expectedValue2 = 'Simple parameter is two';
        $parameters = ['simpleParameter' => 'two'];

        $this->assertTrue($widget->isRenderable($parameters));
        $this->assertSame($expectedValue2, $widget->render($parameters));

        $this->assertFalse($widget->isRenderable('wrong argument'));
        $this->assertFalse($widget->isRenderable(['simpleParameter' => '']));

        $this->assertTrue($widget->isRenderable());
        $this->assertTrue($widget->isRenderable(['something' => 'else']));
        $this->assertTrue($widget->isRenderable(['simpleParameter' => 'something else']));
        $this->assertSame($expectedValue, $widget->render());
    }


    public function testWidgetFilledByConstructor()
    {
        $widget = new ExtendedAbstractWidgetForTesting([
            'simpleParameter' => 'one',
            'someArrayParameter' => ['some', 'content'],
        ]);

        $this->assertTrue($widget->isRenderable());
        $this->assertSame('one', $widget->getSimpleParameter());
        $this->assertSame(['some', 'content'], $widget->getSomeArrayParameter());
        $expectedValue = 'Simple parameter is one';
        $this->assertSame($expectedValue, (string) $widget);
        $this->assertSame($expectedValue, $widget->render());

        $expectedValue2 = 'Simple parameter is two';
        $parameters = ['simpleParameter' => 'two'];

        $this->assertTrue($widget->isRenderable($parameters));
        $this->assertSame($expectedValue2, $widget->render($parameters));

        $this->assertFalse($widget->isRenderable('wrong argument'));
        $this->assertFalse($widget->isRenderable(['simpleParameter' => '']));

        $this->assertTrue($widget->isRenderable());
        $this->assertTrue($widget->isRenderable(['something' => 'else']));
        $this->assertTrue($widget->isRenderable(['simpleParameter' => 'something else']));
        $this->assertSame($expectedValue, $widget->render());
    }


    public function testInitialize()
    {
        $widget = new ExtendedAbstractWidgetForTesting([
            'simpleParameter' => 'one',
            'someArrayParameter' => ['some', 'content'],
        ]);

        $this->assertTrue($widget->isRenderable());
        $this->assertSame('one', $widget->getSimpleParameter());
        $this->assertSame(['some', 'content'], $widget->getSomeArrayParameter());
        $expectedValue = 'Simple parameter is one';
        $this->assertSame($expectedValue, (string) $widget);
        $this->assertSame($expectedValue, $widget->render());

        $parameters = [
            'simpleParameter' => 'two',
            'someArrayParameter' => ['different', 'content'],

        ];
        $widget->initialize($parameters);

        $this->assertTrue($widget->isRenderable());
        $this->assertSame('two', $widget->getSimpleParameter());
        $expectedValue2 = 'Simple parameter is two';
        $this->assertSame($expectedValue2, (string) $widget);
        $this->assertSame($expectedValue2, $widget->render());

        $this->assertSame(['different', 'content'], $widget->getSomeArrayParameter());
        $this->assertTrue($widget->isRenderable());
        $this->assertSame($expectedValue2, (string) $widget);
        $this->assertSame($expectedValue2, $widget->render());

        $this->assertFalse($widget->isRenderable('wrong argument'));
        $this->assertFalse($widget->isRenderable(['simpleParameter' => '']));

        $this->assertTrue($widget->isRenderable());
        $this->assertTrue($widget->isRenderable(['something' => 'else']));
        $this->assertTrue($widget->isRenderable(['simpleParameter' => 'something else']));
        $this->assertSame($expectedValue2, $widget->render());
    }


    public function testAlternateRenderingMethod()
    {
        $widget = new ExtendedAbstractWidgetForTesting();
        $this->assertSame('some value', $widget->renderFromArray(['someArrayParameter' => ['some', 'value']]));
        $this->assertSame($widget, $widget->setSomeArrayParameter(['another', 'value']));
        $this->assertSame('another value', $widget->renderFromArray());
        $this->assertSame('some value', $widget->renderFromArray(['someArrayParameter' => ['some', 'value']]));
    }

    /**
     * @expectedException \Subscribo\Omnipay\Shared\Exception\WidgetInvalidRenderingParametersException
     * @expectedExceptionMessage Parameter 'simpleParameter' is required
     */
    public function testExceptionForEmptyWidget()
    {
        $widget = new ExtendedAbstractWidgetForTesting();
        $widget->render();
    }

    /**
     * @expectedException \Subscribo\Omnipay\Shared\Exception\WidgetInvalidRenderingParametersException
     * @expectedExceptionMessage Parameters should be an array
     */
    public function testExceptionWrongParameters()
    {
        $widget = new ExtendedAbstractWidgetForTesting();
        $widget->render('wrong parameter');
    }

    /**
     * @expectedException \Subscribo\Omnipay\Shared\Exception\WidgetInvalidRenderingParametersException
     * @expectedExceptionMessage Parameter 'someArrayParameter' is required
     */
    public function testExceptionForAlternativeRenderingMethod()
    {
        $widget = new ExtendedAbstractWidgetForTesting();
        $widget->renderFromArray();
    }
}


class ExtendedAbstractWidgetForTesting extends AbstractWidget
{
    public function getDefaultParameters()
    {
        return [
            'simpleParameter' => ['', 'one', 'two'],
            'someArrayParameter' => [[]],
        ];
    }


    public function render($parameters = [])
    {
        $parameters = $this->checkParameters($parameters);
        return 'Simple parameter is '.$parameters['simpleParameter'];
    }


    public function renderFromArray($parameters = [])
    {
        $parameters = $this->checkParameters($parameters, ['someArrayParameter']);
        return implode(' ', $parameters['someArrayParameter']);
    }


    public function getRequiredParameters()
    {
        return ['simpleParameter'];
    }


    public function getSimpleParameter()
    {
        return $this->getParameter('simpleParameter');
    }


    public function setSimpleParameter($value)
    {
        return $this->setParameter('simpleParameter', $value);
    }


    public function getSomeArrayParameter()
    {
        return $this->getParameter('someArrayParameter');
    }


    public function setSomeArrayParameter(array $value)
    {
        return $this->setParameter('someArrayParameter', $value);
    }
}
