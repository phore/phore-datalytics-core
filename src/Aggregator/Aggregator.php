<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 07.08.18
 * Time: 17:05
 */

namespace Phore\Datalytics\Core\Aggregator;


interface Aggregator
{
    /**
     * @return mixed
     */
    public function reset();

    /**
     * @param $value
     * @return mixed
     */
    public function addValue($value);

    /**
     * @return mixed
     */
    public function getAggregated();
}
