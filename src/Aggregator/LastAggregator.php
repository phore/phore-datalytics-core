<?php
/**
 * Created by PhpStorm.
 * User: oem
 * Date: 23.10.18
 * Time: 13:53
 */

namespace Phore\Datalytics\Core\Aggregator;


class LastAggregator implements Aggregator
{
    /**
     * @var null
     */
    private $lastValue;

    /**
     *
     */
    public function reset():void
    {
        $this->lastValue = null;
    }

    /**
     * @param $value
     */
    public function addValue($value):void
    {
        $this->lastValue = $value;
    }

    /**
     * @return mixed|null
     */
    public function getAggregated()
    {
        return $this->lastValue;
    }
}
