<?php
/**
 * Created by PhpStorm.
 * User: oem
 * Date: 23.10.18
 * Time: 13:53
 */

namespace Phore\Datalytics\Core\Aggregator;


class FirstAggregator implements Aggregator
{
    private $firstValue = null;

    public function reset()
    {
        $this->firstValue = null;
    }

    public function addValue($value)
    {
        if($this->firstValue !== null)
            return;
        $this->firstValue = $value;
    }

    public function getAggregated()
    {
        return $this->firstValue;
    }
}