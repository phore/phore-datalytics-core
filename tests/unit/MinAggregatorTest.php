<?php

namespace Test;

use Phore\Datalytics\Core\Aggregator\MaxAggregator;
use Phore\Datalytics\Core\Aggregator\MinAggregator;
use PHPUnit\Framework\TestCase;

class MinAggregatorTest extends TestCase
{
    public function testAddValuesGetMin()
    {
        $minAggregator = new MinAggregator();
        $min=-100;
        for($i=$min;$i<=100;$i++){
            $minAggregator->addValue($i);
        }
        $minAggregator->addValue("nonNumeric");
        $this->assertSame($min, $minAggregator->getAggregated());
    }

    public function testReturnNullWhenNothingAggregated()
    {
        $minAggregator = new MinAggregator();
        $this->assertSame(null, $minAggregator->getAggregated());
    }
}
