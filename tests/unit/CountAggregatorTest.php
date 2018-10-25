<?php
/**
 * Created by PhpStorm.
 * User: oem
 * Date: 23.10.18
 * Time: 13:58
 */

namespace Test;

use Phore\Datalytics\Core\Aggregator\CountAggregator;
use PHPUnit\Framework\TestCase;


class CountAggregatorTest extends TestCase
{
    public function testAddValueGetAggregatedReset()
    {
        $countAggregator = new CountAggregator();
        $countAggregator->addValue(3);
        $countAggregator->addValue(6);
        $countAggregator->addValue(9);
        $this->assertSame(3,$countAggregator->getAggregated());
        $countAggregator->reset();
        $this->assertSame(0,$countAggregator->getAggregated());
    }
}
