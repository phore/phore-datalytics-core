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

    protected function _createTsWithFillEmpty() : TimeSeries
    {
        $ts = new TimeSeries(10,13, true);
        $ts->setOutputFormat($this->outputFormat = new ArrayOutputFormat());
        $ts->define("col1", new SumAggregator());
        return $ts;
    }

    protected function _createTs() : TimeSeries
    {
        $ts = new TimeSeries(10,13);
        $ts->setOutputFormat($this->outputFormat = new ArrayOutputFormat());
        $ts->define("col1", new SumAggregator());
        return $ts;
    }

    public function testOutputFlushedWithStartTimestampFillEmptyFalse()
    {
        $ts = $this->_createTs();
        $ts->push(10, "col1", 1);
        $ts->push(10.5, "col1", 2);
        $this->assertEmpty($this->outputFormat->data);
        $ts->push(11, "col1", 4);
        $this->assertArrayHasKey("10", $this->outputFormat->data);
        $ts->close();
        $this->assertArrayHasKey("11", $this->outputFormat->data);
    }

    public function testFillAfter()
    {
        $ts = $this->_createTsWithFillEmpty();
        $ts->push(10, "col1", 1);
        $ts->push(10.5, "col1", 2);
        $ts->push(11, "col1", 4);
        $ts->close();
        $this->assertEquals(3 , $this->outputFormat->data["10"]["col1"]);
        $this->assertEquals(4 , $this->outputFormat->data["11"]["col1"]);
        $this->assertEquals("" , $this->outputFormat->data["12"]["col1"]);
        $this->assertEquals("" , $this->outputFormat->data["13"]["col1"]);
    }

    public function testFillBefore()
    {
        $ts = $this->_createTsWithFillEmpty();
        $ts->push(11, "col1", 1);
        $ts->push(11.5, "col1", 2);
        $ts->push(12, "col1", 4);
        $ts->push(12.1, "col1", 5);
        $ts->push(13, "col1", 4);
        $ts->close();
        $this->assertEquals("" , $this->outputFormat->data["10"]["col1"]);
        $this->assertEquals(3 , $this->outputFormat->data["11"]["col1"]);
        $this->assertEquals(9 , $this->outputFormat->data["12"]["col1"]);
        $this->assertEquals(4 , $this->outputFormat->data["13"]["col1"]);
    }

    public function testFillBetween()
    {
        $ts = $this->_createTsWithFillEmpty();
        $ts->push(10, "col1", 1);
        $ts->push(12, "col1", 4);
        $ts->push(13, "col1", 4);
        $ts->close();
        $this->assertEquals(1 , $this->outputFormat->data["10"]["col1"]);
        $this->assertEquals("" , $this->outputFormat->data["11"]["col1"]);
        $this->assertEquals(4 , $this->outputFormat->data["12"]["col1"]);
        $this->assertEquals(4 , $this->outputFormat->data["13"]["col1"]);
    }


    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Timestamp nt in cronological order
     */
    public function testException()
    {
        $ts = $this->_createTsWithFillEmpty();
        $ts->push(12, "col1", 4);
        $ts->push(10, "col1", 1);
        $ts->push(13, "col1", 4);
    }
}
