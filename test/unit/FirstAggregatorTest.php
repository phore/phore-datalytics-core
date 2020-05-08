<?php
/**
 * Created by PhpStorm.
 * User: oem
 * Date: 23.10.18
 * Time: 14:22
 */

namespace Test;

use Phore\Datalytics\Core\Aggregator\FirstAggregator;
use PHPUnit\Framework\TestCase;

class FirstAggregatorTest extends TestCase
{
    public function testAddValueGetAggregatedReset(): void
    {
        $firstAggregator = new FirstAggregator();
        $firstAggregator->addValue(4);
        $firstAggregator->addValue(6);
        $firstAggregator->addValue(9);
        $this->assertSame(4,$firstAggregator->getAggregated());
        $firstAggregator->reset();
        $this->assertNull($firstAggregator->getAggregated());
    }

    public function testFloatReturnValue()
    {
        $ag = new FirstAggregator();
        $ag->addValue(1.5);
        $this->assertEquals(1.5 , $ag->getAggregated());
    }
}
