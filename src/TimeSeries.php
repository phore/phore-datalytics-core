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



    private $sampleInterval;
    private $fillEmpty;
    private $startTs;
    private $endTs;

    private $curFrameStart;
    private $curFrameEnd;
    private $curFrameDataCount = 0;

    public function __construct(float $startTs, float $endTs, bool $fillEmpty = false, float $sampleInterval = 1)
    {
        if ($sampleInterval < 0.0001) {
            $this->sampleInterval = false;

            if ($fillEmpty)
                throw new \InvalidArgumentException("Cannot fill with undefined or zero sample interval.");
        } else {
            $this->sampleInterval = $sampleInterval;
        }
        $this->fillEmpty = $fillEmpty;
        $this->startTs = $startTs;
        $this->endTs = $endTs;

        $this->curFrameStart = $startTs;
        $this->curFrameEnd = $startTs + $sampleInterval - 0.00001;
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


    protected function _flush()
    {
        $data = [];
        foreach ($this->signals as $name => $aggregator) {
            $data[$name] = $aggregator->getAggregated();
            $aggregator->reset();
        }

        $this->outputFormat->sendData($this->curFrameStart, $data);
        $this->curFrameDataCount = 0;
    }


    protected function _fillNull()
    {
        $emptySet = [];

        foreach ($this->signals as $name => $aggregator) {
            $emptySet[$name] = null;
        }
        $this->outputFormat->sendData($this->curFrameStart, $emptySet);

    }

    protected function _shiftOne()
    {
        if ($this->sampleInterval === false) {
            return; // No filling
        }
        $this->curFrameStart += $this->sampleInterval;
        $this->curFrameEnd = $this->curFrameStart + $this->sampleInterval - 0.00001;
    }



    public function push(float $timestamp, string $signalName, $value)
    {

        if ($timestamp < $this->startTs || $timestamp > $this->endTs) {
            return;
        }

        if ($this->sampleInterval === false) {
            // Unsampled data
            $this->outputFormat->sendData($timestamp, [$signalName => $value]);
            return;
        }

        if ( ! isset ($this->signals[$signalName]))
            throw new \Exception("Signal '$signalName' not defined");

        if ($timestamp < $this->curFrameStart)
            throw new \InvalidArgumentException("Timestamp not in chronological order");

        if ($timestamp >= $this->curFrameEnd && $this->curFrameDataCount > 0) {
            $this->_flush();
            $this->_shiftOne();
        }

        while ($this->curFrameEnd < $timestamp) {
            if ($this->fillEmpty)
                $this->_fillNull();
            $this->_shiftOne();
        }
        $this->signals[$signalName]->addValue($value);
        $this->curFrameDataCount++;


    }

    public function close()
    {

        if ($this->curFrameDataCount > 0) {
            $this->_flush();
            $this->_shiftOne();
        }
        while ($this->curFrameEnd < $this->endTs && $this->fillEmpty) {
            $this->_shiftOne();
            if ($this->fillEmpty)
                $this->_fillNull();
        }

        $this->outputFormat->close();
    }



}
