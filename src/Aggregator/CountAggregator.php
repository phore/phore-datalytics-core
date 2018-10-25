<?php
/**
 * Created by PhpStorm.
 * User: oem
 * Date: 23.10.18
 * Time: 13:53
 */

namespace Phore\Datalytics\Core\Aggregator;


class CountAggregator implements Aggregator
{
    private $count = 0;
    
    public function reset()
    {
        $this->count = 0;
    }

    public function addValue($value)
    {
        $this->count++;
    }

    public function getAggregated()
    {
        return $this->count;   
    }
}