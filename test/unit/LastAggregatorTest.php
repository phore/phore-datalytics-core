<?php

namespace Test;

use Phore\Datalytics\Core\Aggregator\LastAggregator;
use PHPUnit\Framework\TestCase;

class LastAggregatorTest extends TestCase
{
    public function testAddValueGetAggregatedReset(): void
    {
        $lastAggregator = new LastAggregator();
        $lastAggregator->addValue(4);
        $lastAggregator->addValue(6);
        $lastAggregator->addValue(9);
        $this->assertSame(9, $lastAggregator->getAggregated());
        $lastAggregator->reset();
        $this->assertNull($lastAggregator->getAggregated());
    }

    public function testFloatReturnValue()
    {
        $ag = new LastAggregator();
        $ag->addValue(1.5);
        $this->assertEquals(1.5 , $ag->getAggregated());
    }

}
