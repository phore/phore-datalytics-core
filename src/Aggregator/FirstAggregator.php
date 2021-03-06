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
    /**
     * @var null
     */
    private $firstValue;

    /**
     *
     */
    public function reset(): void
    {
        $this->firstValue = null;
    }

    /**
     * @param $value
     */
    public function addValue($value): void
    {
        if ($this->firstValue !== null) {
            return;
        }
        $this->firstValue = $value;
    }

    /**
     * @return mixed|null
     */
    public function getAggregated()
    {
        return $this->firstValue;
    }
}
