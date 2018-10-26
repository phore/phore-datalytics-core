<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 16.10.18
 * Time: 15:17
 */

namespace Phore\Datalytics\Core;


use Phore\Datalytics\Core\Aggregator\Aggregator;
use Phore\Datalytics\Core\OutputFormat\OutputFormat;


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
    private $sampleInterval = 1;
    private $fillEmpty = false;

    public function setFillEmpty(bool $fillEmpty)
    {
        $this->fillEmpty = $fillEmpty;
    }

    public function setSampleInterval(float $sampleInterval)
    {
        $this->sampleInterval = $sampleInterval;
    }

    public function setOutputFormat(OutputFormat $outputFormat) : self
    {
        $this->outputFormat = $outputFormat;
        return $this;
    }

    public function setStartTs (float $startTimestamp)
    {
        $this->lastFlushTs = $this->_getFlatTs($startTimestamp);
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

        $this->outputFormat->sendData($ts, $data);
    }

    private function _fill(float $ts)
    {
        if($this->fillEmpty === false)
            return;
        $data = [];
        foreach (array_keys($this->signals) as $name) {
            // Don't reset aggregator
            $data[$name] = null;
        }
        $this->outputFormat->sendData($ts, $data);
    }


    private function _checkMustFill (float $nextTs)
    {
        $fillTs = $this->lastFlushTs + $this->sampleInterval;

        while ($fillTs < $nextTs) {
            $this->_fill($fillTs);
            $this->lastFlushTs = $fillTs;
            $fillTs += $this->sampleInterval;
        }
    }

    public function define($name, Aggregator $aggregator) : self
    {
        $this->signals[$name] = $aggregator;
        return $this;
    }

    public function push(float $timestamp, string $signalName, $value)
    {
        $flatTs = $this->_getFlatTs($timestamp);
        if($this->lastFlushTs === null) {
            $this->lastFlushTs = $flatTs;
        }

        if($this->lastFlushTs < $flatTs) {
            $this->_checkMustFill($flatTs);
            $this->_flush($this->lastFlushTs);
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
