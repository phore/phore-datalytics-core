<?php

namespace Test;

use Phore\Datalytics\Core\Aggregator\MaxAggregator;
use PHPUnit\Framework\TestCase;

class MaxAggregatorTest extends TestCase
{
    public function testAddValuesGetMax()
    {
        $maxAggregator = new MaxAggregator();
        $max=100;
        for($i=-100;$i<=$max;$i++){
            $maxAggregator->addValue($i);
        }
        $maxAggregator->addValue("nonNumeric");
        $this->assertSame($max,$maxAggregator->getAggregated());
    }

    public function testReturnNullWhenNothingAggregated()
    {
        $maxAggregator = new MaxAggregator();
        $this->assertSame(null,$maxAggregator->getAggregated());
    }
}
