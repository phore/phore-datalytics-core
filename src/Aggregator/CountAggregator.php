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
    /**
     * @var int
     */
    private $count = 0;

    /**
     *
     */
    public function reset(): void
    {
        $this->count = 0;
    }

    /**
     * @param $value
     */
    public function addValue($value): void
    {
        $this->count++;
    }

    /**
     * @return int
     */
    public function getAggregated(): int
    {
        return $this->count;
    }
}
