<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 16.10.18
 * Time: 15:17
 */

namespace Phore\Datalytics\Core;


use Phore\Datalytics\Core\Aggregator\Aggregator;
use Phore\Datalytics\Core\DataFormat\OutputFormat;

class TimeSeries
{

    /**
     * @var Aggregator[]
     */
    private $signals = [];

    /**
     * @var OutputFormat
     */
    private $outputFormat;

    private $lastFlushTs = null;
    private $sampleInterval = null;

    public function setSampleInterval(float $sampleInterval)
    {
        $this->sampleInterval = $sampleInterval;
    }






    private function _getFlatTs (float $ts)
    {
        return ((int)($ts / $this->sampleInterval)) * $this->sampleInterval;
    }

    private function _flush(float $ts)
    {
        $data = [];
        foreach ($this->signals as $name => $aggregator) {
            $data[$name] = $aggregator->getAggregated();
            $aggregator->reset();
        }
        $this->outputFormat->setData($ts, $data);
    }

    private function _fill(float $ts)
    {
        $data = [];
        foreach (array_keys($this->signals) as $name) {
            // Don't reset aggregator
            $data[$name] = null;
        }
        $this->outputFormat->setData($ts, $data);
    }


    private function _checkMustFill (float $nextTs)
    {
        while ($this->lastFlushTs < $this->_getFlatTs($nextTs + $this->sampleInterval)) {
            $nextTs = $nextTs + $this->sampleInterval;
            $this->_fill($nextTs);
        }
    }

    public function setStartTs (float $startTimestamp)
    {
        $this->lastFlushTs = $this->_getFlatTs($startTimestamp);
    }

    public function push(float $timestamp, string $signalName, $value)
    {
        $flatTs = $this->_getFlatTs($timestamp);
        if($this->lastFlushTs === null) {
            $this->lastFlushTs = $flatTs;
        }

        if($this->lastFlushTs < $flatTs) {
            $this->_checkMustFill($flatTs);
            $this->_flush($flatTs);
            $this->lastFlushTs = $flatTs;
        }

        if ( ! isset ($this->signals[$signalName]))
            return;

        $this->signals[$signalName]->addValue($value);
    }

    public function close(float $endTs)
    {
        $flatTs = $this->_getFlatTs($endTs);

        if($this->lastFlushTs < $flatTs) {
            $this->_checkMustFill($flatTs);
            $this->_flush($flatTs);
            $this->lastFlushTs = $flatTs;
        }
    }



}