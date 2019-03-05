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

    private $signalBufferLen = 0;

    /**
     * @var OutputFormat
     */
    private $outputFormat;

    private $lastFlushTs = null;
    private $lastPushTs = null;

    private $sampleInterval;
    private $fillEmpty;
    private $startTs;
    private $endTs;

    public function __construct(float $startTs, float $endTs, bool $fillEmpty = false, float $sampleInterval = 1)
    {
        if ($sampleInterval < 0.0001) {
            $this->sampleInterval = false;
            $this->lastPushTs = $this->_getFlatTs($startTs) - 1;

            if ($fillEmpty)
                throw new \InvalidArgumentException("Cannot fill with undefined or zero sample interval.");
        } else {
            $this->sampleInterval = $sampleInterval;
            $this->lastPushTs = $this->_getFlatTs($startTs) - 1;
        }
        $this->fillEmpty = $fillEmpty;
        $this->lastFlushTs = $this->lastPushTs;
        $this->startTs = $startTs;
        $this->endTs = $endTs;
    }


    public function setOutputFormat(OutputFormat $outputFormat) : self
    {
        $this->outputFormat = $outputFormat;
        return $this;
    }

    public function define($name, Aggregator $aggregator) : self
    {
        $this->signals[$name] = $aggregator;
        return $this;
    }

    private function _getFlatTs (float $ts)
    {
        if ($this->sampleInterval === false)
            return $ts;
        return ((int)($ts / $this->sampleInterval)) * $this->sampleInterval;
    }

    private function _flush(float $ts)
    {
        if ($this->signalBufferLen === 0)
            return;

        $data = [];
        foreach ($this->signals as $name => $aggregator) {
            $data[$name] = $aggregator->getAggregated();
            $aggregator->reset();
        }

        $this->outputFormat->sendData($ts, $data);
        $this->signalBufferLen = 0;
    }

    private function _fill(float $ts)
    {
        $data = [];
        foreach (array_keys($this->signals) as $name) {
            // Don't reset aggregator
            $data[$name] = null;
        }
        $this->outputFormat->sendData($ts, $data);
    }

    private function _checkMustFill (float $nextTs)
    {
        if($this->fillEmpty === false)
            return;
        $fillTs = $this->lastFlushTs + $this->sampleInterval;

        while ($fillTs < $nextTs) {
            if ($fillTs >= $this->startTs && $fillTs < $this->endTs)
                $this->_fill($fillTs);
            $this->lastFlushTs = $fillTs;
            $fillTs += $this->sampleInterval;
        }
    }

    public function push(float $timestamp, string $signalName, $value)
    {
        $flatTs = $this->_getFlatTs($timestamp);

        if($flatTs < $this->startTs || $flatTs >= $this->endTs)
            return;

        if($flatTs < $this->lastFlushTs)
            throw new \InvalidArgumentException("Timestamp not in chronological order");

        if($this->lastFlushTs < $flatTs) {
            $this->_flush($this->lastFlushTs);
            $this->_checkMustFill($flatTs);
            $this->lastFlushTs = $flatTs;
        }

        if ( ! isset ($this->signals[$signalName]))
            return;

        $this->signals[$signalName]->addValue($value);
        $this->signalBufferLen++;
        $this->lastPushTs = $this->_getFlatTs($timestamp);

    }

    public function close()
    {
        $this->_flush($this->lastPushTs);

        if ($this->fillEmpty) {
            $this->_checkMustFill($this->endTs);
        }
        $this->outputFormat->close();
    }



}
