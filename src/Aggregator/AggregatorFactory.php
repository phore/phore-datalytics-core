<?php
/**
 * Created by PhpStorm.
 * User: oem
 * Date: 24.10.18
 * Time: 13:54
 */

namespace Phore\Datalytics\Core\Aggregator;


class AggregatorFactory
{
    
    private $defaultAggregator;
    
    public function __construct(string $defaultAggregator="first")
    {
        $this->defaultAggregator = $defaultAggregator;
    }


    public function createAggregator(string $aggregatorName = null) : Aggregator
    {
        if ($aggregatorName === null)
            $aggregatorName = $this->defaultAggregator;
        
        switch ($aggregatorName) {
            case "avg":
                return new AvgAggregator();
            case  "count":
                return new CountAggregator();
            case "first":
                return new FirstAggregator();
            case "max":
                return new MaxAggregator();
            case "min":
                return new MinAggregator();
            case "sum":
                return new SumAggregator();
            default:
                throw new \InvalidArgumentException("Unknown aggregator name: $aggregatorName");

        }
    }
}
