<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 07.08.18
 * Time: 17:07
 */

namespace Phore\Datalytics\Core\Aggregator;


class MinAggregator implements Aggregator
{

    /**
     * @var array
     */
    private $values = [];

    /**
     *
     */
    public function reset(): void
    {
        $this->values = [];
    }

    /**
     * @param $value
     */
    public function addValue($value): void
    {
        if (!is_numeric($value)) {
            return;
        }
        $this->values[] = $value;
    }

    /**
     * @return float|int|null
     */
    public function getAggregated()
    {
        if (count($this->values) === 0) {
            return null;
        }
        return min($this->values);
    }
}
