<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 07.08.18
 * Time: 17:07
 */

namespace Phore\Datalytics\Core\Aggregator;


class AvgAggregator implements Aggregator
{

    private $sum = 0;
    private $numValues = 0;

    public function reset()
    {
        $this->sum = 0;
        $this->numValues = 0;
    }

    public function addValue($value)
    {
        if ( ! is_numeric($value))
            return;
        $this->sum += $value;
        $this->numValues++;
    }

    public function getAggregated()
    {
        if ($this->numValues === 0)
            return null;
        return $this->sum / $this->numValues;
    }
}
