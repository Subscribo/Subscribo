<?php

namespace Subscribo\Support;

use PHPUnit_Framework_TestCase;
use Subscribo\Support\Math;

class MathTest extends PHPUnit_Framework_TestCase
{
    public function testBcround()
    {
        $this->assertSame('0', Math::bcround('0', '0'));
        $this->assertSame('4.000', Math::bcround('4', 3, Math::ROUND_HALF_AWAY_FROM_ZERO));
        $this->assertSame('0', Math::bcround(0, 0));
        $this->assertSame('8.5', Math::bcround('8.4999999999999999', 1));
        $this->assertSame('8.50000', Math::bcround('8.4999999999999999', 5));

        $this->assertSame('8', Math::bcround('8.4999999999999999', 0));
        $this->assertSame('9', Math::bcround('8.5', 0));
        $this->assertSame('-8.5', Math::bcround('-8.4999999999999999', 1));
        $this->assertSame('-8.50000000', Math::bcround('-8.4999999999999999', 8));

        $this->assertSame('-8', Math::bcround('-8.4999999999999999', 0));
        $this->assertSame('-9', Math::bcround('-8.5', 0));
        $this->assertSame('148257.2', Math::bcround('148257.231780', 1));
        $this->assertSame('148257.23', Math::bcround('148257.231780', 2));
        $this->assertSame('148257.232', Math::bcround('148257.231780', 3));
        $this->assertSame('148257.2318', Math::bcround('148257.231780', 4));
        $this->assertSame('148257.23178', Math::bcround('148257.231780', 5));
        $this->assertSame('148257.231780', Math::bcround('148257.231780', 6));
        $this->assertSame('148257.2317800', Math::bcround('148257.231780', 7));

        $this->assertSame('148257.2', Math::bcround('148257.200031780', 1));
        $this->assertSame('148257.2', Math::bcround('148257.2499931780', 1));
        $this->assertSame('-148257.2', Math::bcround('-148257.231780', 1));
        $this->assertSame('-148257.23', Math::bcround('-148257.231780', 2));
        $this->assertSame('-148257.232', Math::bcround('-148257.231780', 3));
        $this->assertSame('-148257.2318', Math::bcround('-148257.231780', 4));
        $this->assertSame('-148257.23178', Math::bcround('-148257.231780', 5));
        $this->assertSame('-148257.231780', Math::bcround('-148257.231780', 6));
        $this->assertSame('-148257.2317800', Math::bcround('-148257.231780', 7));
        $this->assertSame('-148257.2', Math::bcround('-148257.200031780', 1));
        $this->assertSame('-148257.2', Math::bcround('-148257.2499931780', 1));

        $this->assertSame('0', Math::bcround('0.4', 0));
        $this->assertSame('-0', Math::bcround('-0.4', 0));
        $this->assertSame('1', Math::bcround('0.5', 0));
        $this->assertSame('-1', Math::bcround('-0.5', 0));

        $this->assertSame('0.048', Math::bcround('0.0475', 3));
        $this->assertSame('0', Math::bcround('0.0475495', 0));
        $this->assertSame('0.0', Math::bcround('0.0475495', 1));
        $this->assertSame('0.05', Math::bcround('0.0475495', 2));
        $this->assertSame('0.048', Math::bcround('0.0475495', 3));
        $this->assertSame('0.0475', Math::bcround('0.0475495', 4));
        $this->assertSame('0.04755', Math::bcround('0.0475495', 5));
        $this->assertSame('0.047550', Math::bcround('0.0475495', 6));
        $this->assertSame('0.0475495', Math::bcround('0.0475495', 7));
        $this->assertSame('0.04754950', Math::bcround('0.0475495', 8));
        $this->assertSame('0.047549500', Math::bcround('0.0475495', 9));

        $this->assertSame('-0.048', Math::bcround('-0.0475', 3));
        $this->assertSame('-0', Math::bcround('-0.0475495', 0));
        $this->assertSame('-0.0', Math::bcround('-0.0475495', 1));
        $this->assertSame('-0.05', Math::bcround('-0.0475495', 2));
        $this->assertSame('-0.048', Math::bcround('-0.0475495', 3));
        $this->assertSame('-0.0475', Math::bcround('-0.0475495', 4));
        $this->assertSame('-0.04755', Math::bcround('-0.0475495', 5));
        $this->assertSame('-0.047550', Math::bcround('-0.0475495', 6));
        $this->assertSame('-0.0475495', Math::bcround('-0.0475495', 7));
        $this->assertSame('-0.04754950', Math::bcround('-0.0475495', 8));
        $this->assertSame('-0.047549500', Math::bcround('-0.0475495', 9));

    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Negative scale
     */
    public function testBcroundNegativeScale()
    {
        Math::bcround('10', -5);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Wrong mode
     */
    public function testBcroundDifferentMode()
    {
        Math::bcround('4', 3, 'nonexistent mode');
    }
}
