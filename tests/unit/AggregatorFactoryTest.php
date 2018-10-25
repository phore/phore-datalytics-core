<?php
/**
 * Created by PhpStorm.
 * User: oem
 * Date: 24.10.18
 * Time: 14:11
 */

namespace Test;

use Phore\Datalytics\Core\Aggregator\AggregatorFactory;
use Phore\Datalytics\Core\Aggregator\AvgAggregator;
use Phore\Datalytics\Core\Aggregator\CountAggregator;
use Phore\Datalytics\Core\Aggregator\FirstAggregator;
use Phore\Datalytics\Core\Aggregator\MaxAggregator;
use Phore\Datalytics\Core\Aggregator\MinAggregator;
use Phore\Datalytics\Core\Aggregator\SumAggregator;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class AggregatorFactoryTest extends TestCase
{
    public function testGetAggregator()
    {
        $aggregatorFactory = new AggregatorFactory();
        Assert::assertEquals(new AvgAggregator(), $aggregatorFactory->createAggregator("avg"));
        Assert::assertEquals(new CountAggregator(), $aggregatorFactory->createAggregator("count"));
        Assert::assertEquals(new FirstAggregator(), $aggregatorFactory->createAggregator("first"));
        Assert::assertEquals(new MaxAggregator(), $aggregatorFactory->createAggregator("max"));
        Assert::assertEquals(new MinAggregator(), $aggregatorFactory->createAggregator("min"));
        Assert::assertEquals(new SumAggregator(), $aggregatorFactory->createAggregator("sum"));
        Assert::assertEquals(new AvgAggregator(), $aggregatorFactory->createAggregator());
        Assert::assertEquals(new AvgAggregator(), $aggregatorFactory->createAggregator("abc"));
    }
}
