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

    /**
     * @var int
     */
    private $sum = 0;
    /**
     * @var int
     */
    private $numValues = 0;

    /**
     *
     */
    public function reset(): void
    {
        $this->sum = 0;
        $this->numValues = 0;
    }

    /**
     * @param $value
     */
    public function addValue($value): void
    {
        if (!is_numeric($value)) {
            return;
        }
        $this->sum += $value;
        $this->numValues++;
    }

    /**
     * @return float|int|mixed|null
     */
    public function getAggregated()
    {
        if ($this->numValues === 0) {
            return null;
        }
        return $this->sum / $this->numValues;
    }
}
