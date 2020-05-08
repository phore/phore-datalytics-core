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

    /**
     * @var int
     */
    private $max = -INF;

    /**
     *
     */
    public function reset(): void
    {
        $this->max = -INF;
    }

    /**
     * @param $value
     */
    public function addValue($value): void
    {
        if (is_numeric($value) && $value > $this->max) {
            $this->max = $value;
        }
    }

    /**
     * @return int|null
     */
    public function getAggregated()
    {
        if ($this->max === -INF) {
            return null;
        }
        return $this->max;
    }
}
