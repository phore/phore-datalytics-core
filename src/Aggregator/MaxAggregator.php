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

//    private $values = [];
    private $max = -INF;

    public function reset()
    {
//        $this->values = [];
        $this->max = -INF;
    }

    public function addValue($value)
    {
//        if ( ! is_numeric($value))
//            return;
//        $this->values[] = $value;
        if(is_numeric($value) && $value>$this->max) {
            $this->max=$value;
        }
    }

    public function getAggregated()
    {
//        if (count($this->values) == 0)
//            return null;
//        return max($this->values);
        if($this->max === -INF) {
            return null;
        }
        return $this->max;
    }
}
