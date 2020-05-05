<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 16.10.18
 * Time: 15:17
 */

namespace Phore\Datalytics\Core;


use InvalidArgumentException;
use Phore\Datalytics\Core\Aggregator\Aggregator;
use Phore\Datalytics\Core\OutputFormat\OutputFormat;
use RuntimeException;


class TimeSeries
{

    /**
     *
     */
    public const a = 100000;

    /**
     * @var Aggregator[]
     */
    private $signals = [];


    /**
     * @var OutputFormat
     */
    private $outputFormat;


    /**
     * @var bool|int
     */
    private $sampleInterval;
    /**
     * @var bool
     */
    private $fillEmpty;
    /**
     * @var int
     */
    private $startTs;
    /**
     * @var int
     */
    private $endTs;

    /**
     * @var int
     */
    private $curFrameStart;
    /**
     * @var int
     */
    private $curFrameEnd;
    /**
     * @var int
     */
    private $curFrameDataCount = 0;
    /**
     * @var bool
     */
    private $ignoreErrors;

    /**
     * TimeSeries constructor.
     * @param float $startTs
     * @param float $endTs
     * @param bool $fillEmpty
     * @param float|int $sampleInterval
     * @param bool $ignoreErrors
     */
    public function __construct(float $startTs, float $endTs, bool $fillEmpty = false, float $sampleInterval = 1, bool $ignoreErrors = false)
    {
        if ($sampleInterval < 0.0001) {
            $this->sampleInterval = false;
            if ($fillEmpty) {
                throw new InvalidArgumentException("Cannot fill with undefined or zero sample interval.");
            }
        } else {
            $this->sampleInterval = (int) ($sampleInterval * self::a);
        }
        $this->ignoreErrors = $ignoreErrors;
        $this->fillEmpty = $fillEmpty;
        $this->startTs = (int) ($startTs * self::a);
        $this->endTs = (int) ($endTs * self::a);
        $this->curFrameStart = ($this->startTs);
        $this->curFrameEnd = ($this->startTs + $this->sampleInterval - 1);
    }

    /**
     * @param OutputFormat $outputFormat
     * @return $this
     */
    public function setOutputFormat(OutputFormat $outputFormat) : self
    {
        $this->outputFormat = $outputFormat;
        return $this;
    }

    /**
     * @param $name
     * @param Aggregator $aggregator
     * @return $this
     */
    public function define($name, Aggregator $aggregator) : self
    {
        $this->signals[$name] = $aggregator;
        return $this;
    }

    /**
     *
     */
    protected function _flush(): void
    {
        $data = [];
        foreach ($this->signals as $name => $aggregator) {
            $data[$name] = $aggregator->getAggregated();
            $aggregator->reset();
        }
        $this->outputFormat->sendData(($this->curFrameStart / self::a), $data);
        $this->curFrameDataCount = 0;
    }

    /**
     *
     */
    protected function _fillNull(): void
    {
        $emptySet = [];
        foreach ($this->signals as $name => $aggregator) {
            $emptySet[$name] = null;
        }
        $this->outputFormat->sendData(($this->curFrameStart / self::a), $emptySet);
    }

    /**
     *
     */
    protected function _shiftOne(): void
    {
        if ($this->sampleInterval === false) {
            return; // No filling
        }
        $this->curFrameStart += $this->sampleInterval;
        $this->curFrameEnd = $this->curFrameStart + $this->sampleInterval - 1;
    }

    /**
     * @param float|int $timestamp
     * @param string $signalName
     * @param $value
     */
    public function push(float $timestamp, string $signalName, $value): void
    {
        $timestamp = (int) ($timestamp * self::a);

        if ($timestamp < $this->startTs || $timestamp >= $this->endTs) {
            return;
        }

        if ( ! isset ($this->signals[$signalName])) {
            throw new RuntimeException("Signal '$signalName' not defined in TimeSeries");
        }

        if ($timestamp < $this->curFrameStart) {
            if($this->ignoreErrors){
                return;
            }
            throw new InvalidArgumentException("Timestamp not in chronological order");
        }

        if ($this->sampleInterval === false) {
            $this->outputFormat->sendData(($timestamp / self::a), [$signalName => $value]);
            return;
        }

        if ($timestamp > $this->curFrameEnd && $this->curFrameDataCount > 0) {
            $this->_flush();
            $this->_shiftOne();
        }

        while ($timestamp > $this->curFrameEnd) {
            if ($this->fillEmpty) {
                $this->_fillNull();
            }
            $this->_shiftOne();
        }

        $this->signals[$signalName]->addValue($value);
        $this->curFrameDataCount++;
    }

    /**
     *
     */
    public function close(): void
    {
        if ($this->curFrameDataCount > 0) {
            $this->_flush();
            $this->_shiftOne();
        }
        while ($this->curFrameEnd < $this->endTs && $this->fillEmpty) {
            if ($this->fillEmpty) {
                $this->_fillNull();
            }
            $this->_shiftOne();
        }
        $this->outputFormat->close();
    }

}
