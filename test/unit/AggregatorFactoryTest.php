<?php
/**
 * Created by PhpStorm.
 * User: oem
 * Date: 24.10.18
 * Time: 14:11
 */

namespace Test;

use InvalidArgumentException;
use Phore\Datalytics\Core\Aggregator\AggregatorFactory;
use Phore\Datalytics\Core\Aggregator\AvgAggregator;
use Phore\Datalytics\Core\Aggregator\CountAggregator;
use Phore\Datalytics\Core\Aggregator\FirstAggregator;
use Phore\Datalytics\Core\Aggregator\LastAggregator;
use Phore\Datalytics\Core\Aggregator\MaxAggregator;
use Phore\Datalytics\Core\Aggregator\MinAggregator;
use Phore\Datalytics\Core\Aggregator\SumAggregator;
use PHPUnit\Framework\TestCase;

class AggregatorFactoryTest extends TestCase
{
    public function testGetAggregator(): void
    {
        $aggregatorFactory = new AggregatorFactory();
        $this->assertInstanceOf(AvgAggregator::class,      $aggregatorFactory->createAggregator("avg"));
        $this->assertInstanceOf(CountAggregator::class,    $aggregatorFactory->createAggregator("count"));
        $this->assertInstanceOf(FirstAggregator::class,    $aggregatorFactory->createAggregator("first"));
        $this->assertInstanceOf(MaxAggregator::class,      $aggregatorFactory->createAggregator("max"));
        $this->assertInstanceOf(MinAggregator::class,      $aggregatorFactory->createAggregator("min"));
        $this->assertInstanceOf(SumAggregator::class,      $aggregatorFactory->createAggregator("sum"));
        $this->assertInstanceOf(LastAggregator::class,     $aggregatorFactory->createAggregator("last"));
        $this->assertInstanceOf(FirstAggregator::class,    $aggregatorFactory->createAggregator());
    }

    public function testAggregatorUnknownException(): void
    {
        $aggregatorFactory = new AggregatorFactory();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unknown aggregator name: abc");
        $this->assertInstanceOf(AvgAggregator::class,      $aggregatorFactory->createAggregator("abc"));
    }

}
