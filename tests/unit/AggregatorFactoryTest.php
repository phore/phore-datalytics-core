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
        Assert::isInstanceOf(AvgAggregator::class,      $aggregatorFactory->createAggregator("avg"));
        Assert::isInstanceOf(CountAggregator::class,    $aggregatorFactory->createAggregator("count"));
        Assert::isInstanceOf(FirstAggregator::class,    $aggregatorFactory->createAggregator("first"));
        Assert::isInstanceOf(MaxAggregator::class,      $aggregatorFactory->createAggregator("max"));
        Assert::isInstanceOf(MinAggregator::class,      $aggregatorFactory->createAggregator("min"));
        Assert::isInstanceOf(SumAggregator::class,      $aggregatorFactory->createAggregator("sum"));
        Assert::isInstanceOf(FirstAggregator::class,    $aggregatorFactory->createAggregator());
    }

    public function testAggregatorUnknownException()
    {
        $aggregatorFactory = new AggregatorFactory();
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Unknown aggregator name: abc");
        Assert::isInstanceOf(AvgAggregator::class,      $aggregatorFactory->createAggregator("abc"));
    }

}
