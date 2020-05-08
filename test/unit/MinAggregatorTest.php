<?php

namespace Test;

use Phore\Datalytics\Core\Aggregator\MinAggregator;
use PHPUnit\Framework\TestCase;

class MinAggregatorTest extends TestCase
{
    public function testAddValuesGetMin(): void
    {
        $minAggregator = new MinAggregator();
        $min=-100;
        for($i=$min;$i<=100;$i++){
            $minAggregator->addValue($i);
        }
        $minAggregator->addValue("nonNumeric");
        $this->assertSame($min, $minAggregator->getAggregated());
    }

    public function testReturnNullWhenNothingAggregated(): void
    {
        $minAggregator = new MinAggregator();
        $this->assertNull($minAggregator->getAggregated());
    }

    public function testFloatReturnValue()
    {
        $ag = new MinAggregator();
        $ag->addValue(1.5);
        $this->assertEquals(1.5 , $ag->getAggregated());
    }
}
