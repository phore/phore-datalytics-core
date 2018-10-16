<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 07.08.18
 * Time: 17:07
 */

namespace Phore\Datalytics\Core\Aggregator;


class MaxAggregator implements Aggregator
{

    private $values = [];

    public function reset()
    {
        $this->values = [];
    }

    public function addValue($value)
    {
        if ( ! is_numeric($value))
            return;
        $this->values[] = $value;
    }

    public function getAggregated()
    {
        if (count($this->values) == 0)
            return null;
        return max($this->values);
    }
}
