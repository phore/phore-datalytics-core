<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 18.10.18
 * Time: 11:35
 */

namespace Test;


use Phore\Datalytics\Core\Aggregator\SumAggregator;
use Phore\Datalytics\Core\OutputFormat\ArrayOutputFormat;
use Phore\Datalytics\Core\TimeSeries;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class TimeSeriesTest extends TestCase
{

    /**
     * @var ArrayOutputFormat
     */
    public $outputFormat;

    protected function _createTs() : TimeSeries
    {
        $ts = new TimeSeries();
        $ts->setOutputFormat($this->outputFormat = new ArrayOutputFormat());
        $ts->define("col1", new SumAggregator());
        $ts->setStartTs(10);
        return $ts;
    }
/*
    public function testOutputFlushedWithStartTimestamp()
    {
        $ts = $this->_createTs();

        $ts->setSampleInterval(1);

        $ts->push(10, "col1", 1);
        $ts->push(10.5, "col2", 2);

        $this->assertEmpty($this->outputFormat->data);

        $ts->push(11, "col1", 4);
        $this->assertArrayHasKey("10", $this->outputFormat->data);
        print_r($this->outputFormat->data);
        $ts->close(15);
    }
*/
   public function testFill()
    {
        $ts = $this->_createTs();
        $ts->setFillEmpty(true);
        $ts->define("col1",new SumAggregator());
        $ts->push(10, "col1", 1);
        $ts->push(11, "col1", 2);
        $ts->push(12, "col1", 3);
        $ts->close(16);
        print_r($this->outputFormat->data);
        $this->assertTrue(false);
    }
}
