<?php

namespace Subscribo\Omnipay\Shared\Widget;

use PHPUnit_Framework_TestCase;
use Subscribo\Omnipay\Shared\Widget\SimpleWidget;

class SimpleWidgetTest extends PHPUnit_Framework_TestCase
{
    public function testEmptyWidget()
    {
        $widget = new SimpleWidget();

        $this->assertFalse($widget->isRenderable());
        $this->assertSame(['content'], $widget->getRequiredParameters());
        $expectedDefaultParameters = [
            'content' => ''
        ];
        $this->assertSame($expectedDefaultParameters, $widget->getDefaultParameters());
        $parameters = $widget->getParameters();
        $this->assertSame('', $parameters['content']);

        $this->assertSame('', $widget->getContent());

        $this->assertFalse($widget->isRenderable());
        $this->assertFalse($widget->isRenderable('wrong argument'));
        $this->assertFalse($widget->isRenderable(['content' => '']));

        $expectedValue2 = 'Content is this';
        $parameters = ['content' => 'Content is this'];
        $this->assertTrue($widget->isRenderable($parameters));
        $this->assertSame($expectedValue2, $widget->render($parameters));

        $this->assertFalse($widget->isRenderable());
    }


    public function testWidgetFilledBySetters()
    {
        $widget = new SimpleWidget();

        $this->assertFalse($widget->isRenderable());

        $this->assertSame($widget, $widget->setContent('Content is set'));
        $this->assertTrue($widget->isRenderable());
        $this->assertSame('Content is set', $widget->getContent());
        $expectedValue = 'Content is set';
        $this->assertSame($expectedValue, (string) $widget);
        $this->assertSame($expectedValue, $widget->render());

        $expectedValue2 = 'Content is this';
        $parameters = ['content' => 'Content is this'];

        $this->assertTrue($widget->isRenderable($parameters));
        $this->assertSame($expectedValue2, $widget->render($parameters));

        $this->assertFalse($widget->isRenderable('wrong argument'));
        $this->assertFalse($widget->isRenderable(['content' => '']));

        $this->assertTrue($widget->isRenderable());
        $this->assertTrue($widget->isRenderable(['something' => 'else']));
        $this->assertTrue($widget->isRenderable(['content' => 'something else']));
        $this->assertSame($expectedValue, $widget->render());
    }


    public function testWidgetFilledByConstructor()
    {
        $widget = new SimpleWidget([
            'content' => 'Some content',
        ]);

        $this->assertTrue($widget->isRenderable());
        $this->assertSame('Some content', $widget->getContent());
        $expectedValue = 'Some content';
        $this->assertSame($expectedValue, (string) $widget);
        $this->assertSame($expectedValue, $widget->render());

        $expectedValue2 = 'Content is this';
        $parameters = ['content' => 'Content is this'];

        $this->assertTrue($widget->isRenderable($parameters));
        $this->assertSame($expectedValue2, $widget->render($parameters));

        $this->assertFalse($widget->isRenderable('wrong argument'));
        $this->assertFalse($widget->isRenderable(['content' => '']));

        $this->assertTrue($widget->isRenderable());
        $this->assertTrue($widget->isRenderable(['something' => 'else']));
        $this->assertTrue($widget->isRenderable(['content' => 'something else']));
        $this->assertSame($expectedValue, $widget->render());
    }


    public function testInitialize()
    {
        $widget = new SimpleWidget([
            'content' => 'Some content',
        ]);

        $this->assertTrue($widget->isRenderable());
        $this->assertSame('Some content', $widget->getContent());
        $expectedValue = 'Some content';
        $this->assertSame($expectedValue, (string) $widget);
        $this->assertSame($expectedValue, $widget->render());

        $expectedValue2 = 'Content is this';
        $parameters = ['content' => 'Content is this'];
        $widget->initialize($parameters);

        $this->assertTrue($widget->isRenderable($parameters));
        $this->assertSame($expectedValue2, $widget->render($parameters));

        $this->assertFalse($widget->isRenderable('wrong argument'));
        $this->assertFalse($widget->isRenderable(['content' => '']));

        $this->assertTrue($widget->isRenderable());
        $this->assertTrue($widget->isRenderable(['something' => 'else']));
        $this->assertTrue($widget->isRenderable(['content' => 'something else']));
        $this->assertSame($expectedValue2, $widget->render());
    }

    /**
     * @expectedException \Subscribo\Omnipay\Shared\Exception\WidgetInvalidRenderingParametersException
     * @expectedExceptionMessage Parameter 'content' is required
     */
    public function testExceptionForEmptyWidget()
    {
        $widget = new SimpleWidget();
        $widget->render();
    }

    /**
     * @expectedException \Subscribo\Omnipay\Shared\Exception\WidgetInvalidRenderingParametersException
     * @expectedExceptionMessage Parameters should be an array
     */
    public function testExceptionWrongParameters()
    {
        $widget = new SimpleWidget();
        $widget->render('wrong parameter');
    }
}
