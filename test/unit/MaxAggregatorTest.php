<?php

namespace Test;

use Phore\Datalytics\Core\Aggregator\MaxAggregator;
use PHPUnit\Framework\TestCase;

class MaxAggregatorTest extends TestCase
{
    public function testAddValuesGetMax(): void
    {
        $maxAggregator = new MaxAggregator();
        $max=100;
        for($i=-100;$i<=$max;$i++){
            $maxAggregator->addValue($i);
        }
        $maxAggregator->addValue("nonNumeric");
        $this->assertSame($max,$maxAggregator->getAggregated());
    }

    public function testReturnNullWhenNothingAggregated(): void
    {
        $maxAggregator = new MaxAggregator();
        $this->assertNull($maxAggregator->getAggregated());
    }
}
