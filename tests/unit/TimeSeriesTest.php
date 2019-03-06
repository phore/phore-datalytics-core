<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 18.10.18
 * Time: 11:35
 */

namespace Test;


use Phore\Datalytics\Core\Aggregator\FirstAggregator;
use Phore\Datalytics\Core\Aggregator\SumAggregator;
use Phore\Datalytics\Core\OutputFormat\ArrayOutputFormat;
use Phore\Datalytics\Core\OutputFormat\OutputFormatFactory;
use Phore\Datalytics\Core\TimeSeries;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class TimeSeriesTest extends TestCase
{

    /**
     * @var ArrayOutputFormat
     */
    public $outputFormat;
    protected function setUp():void
    {
        /**
         * This disables the exception handling to display the stacktrace on the console
         * the same way as it shown on the browser
         */
        parent::setUp();
        $this->setUseErrorHandler(false);
    }
    protected function _createTsWithFillEmpty() : TimeSeries
    {
        $ts = new TimeSeries(10,14, true);
        $ts->setOutputFormat($this->outputFormat = new ArrayOutputFormat());
        $ts->define("col1", new SumAggregator());
        return $ts;
    }

    protected function _createTs() : TimeSeries
    {
        $ts = new TimeSeries(10,14);
        $ts->setOutputFormat($this->outputFormat = new ArrayOutputFormat());
        $ts->define("col1", new SumAggregator());
        return $ts;
    }

    protected function _createShortTs()
    {
        $ts = new TimeSeries(10,11);
        $ts->setOutputFormat($this->outputFormat = new ArrayOutputFormat());
        $ts->define("col1", new SumAggregator());
        return $ts;
    }

    public function testOutputFlushedWithStartTimestampFillEmptyFalse()
    {
        $ts = $this->_createTs();
        $ts->push(10.1, "col1", 1);
        $ts->push(10.5, "col1", 2);
        $this->assertEmpty($this->outputFormat->data);
        $ts->push(11.1, "col1", 4);

        $this->assertEquals(1, count($this->outputFormat->data));
        $ts->close();

        $this->assertEquals(2, count($this->outputFormat->data));
    }


    public function testFillAfter()
    {
        $ts = $this->_createTsWithFillEmpty();
        $ts->push(10, "col1", 1);
        $ts->push(10.5, "col1", 2);
        $ts->push(11, "col1", 4);
        $ts->close();
        print_r ($this->outputFormat->data);

        $this->assertEquals(3 , $this->outputFormat->data[0]["col1"]);
        $this->assertEquals(4 , $this->outputFormat->data[1]["col1"]);
        $this->assertEquals("" , $this->outputFormat->data[2]["col1"]);
        $this->assertEquals("" , $this->outputFormat->data[3]["col1"]);
    }


    public function testFillBefore()
    {
        $ts = $this->_createTsWithFillEmpty();
        $ts->push(11.1, "col1", 1);
        $ts->push(11.5, "col1", 2);
        $ts->push(12.1, "col1", 4);
        $ts->push(12.1, "col1", 5);
        $ts->push(13.1, "col1", 4);
        $ts->close();

        $this->assertEquals("" , $this->outputFormat->data[0]["col1"]);
        $this->assertEquals(3 , $this->outputFormat->data[1]["col1"]);
        $this->assertEquals(9 , $this->outputFormat->data[2]["col1"]);
        $this->assertEquals(4 , $this->outputFormat->data[3]["col1"]);
    }

    public function testFillBetween()
    {
        $ts = $this->_createTsWithFillEmpty();
        $ts->push(10.1, "col1", 1);
        $ts->push(12.1, "col1", 4);
        $ts->push(13.1, "col1", 4);
        $ts->close();
        print_r ($this->outputFormat->data);
        $this->assertEquals(1 , $this->outputFormat->data[0]["col1"]);
        $this->assertEquals("" , $this->outputFormat->data[1]["col1"]);
        $this->assertEquals(4 , $this->outputFormat->data[2]["col1"]);
        $this->assertEquals(4 , $this->outputFormat->data[3]["col1"]);
    }

    public function testException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Timestamp not in chronological order");
        $ts = $this->_createTsWithFillEmpty();
        $ts->push(12.1, "col1", 4);
        $ts->push(10.1, "col1", 1);
        $ts->push(13.1, "col1", 4);
    }

    public function testTsEqualEndTs()
    {
        $ts = $this->_createShortTs();
        $ts->push(10.1, "col1", 4);
        $ts->push(11.1, "col1", 4);
        $ts->close();
        $this->assertEquals(1, count($this->outputFormat->data));
    }

    public function testTsEqualEndTsWithFillEmpty()
    {
        $ts = $this->_createTsWithFillEmpty();
        $ts->push(10.1, "col1", 4);
        $ts->push(11.1, "col1", 4);
        $ts->push(14.1, "col1", 4);
        $ts->close();
        $this->assertArrayNotHasKey(14, $this->outputFormat->data);
    }

    public function testTsEqualStartTs()
    {
        $ts = $this->_createShortTs();
        $ts->push(10.1, "col1", 4);
        $ts->close();
        $this->assertEquals(10, $this->outputFormat->data[0]["ts"]);
    }

    public function testSampleIntervalZeroWillOutputUnsampledData()
    {
        $ts = new TimeSeries(10, 11, false, 0);
        $ts->define("a", new FirstAggregator());
        $ts->setOutputFormat($aof = new ArrayOutputFormat());
        $ts->push(10.0, "a", 1);
        $ts->push(10.1, "a", 1);
        $ts->close();

        $this->assertEquals(2, count ($aof->data));
        $this->assertEquals(10, $aof->data[0]["ts"]);
        $this->assertEquals(10.1, $aof->data[1]["ts"]);
    }

    public function testHeaderIsSentWithNoData()
    {
        $ts = $this->_createTsWithFillEmpty();
        $ts->close();
        $this->assertTrue($this->outputFormat->isClosed);
    }



    public function testTimeSeriesRealIssue()
    {
        $ts = new TimeSeries(1542024000, 1542027600, true, 5);
        $ts->setOutputFormat($of = new ArrayOutputFormat());

        $ts->define("col1", new FirstAggregator());


        $ts->push(1542024008, "col1", null);
        $ts->push(1542025433, "col1", null);
        $ts->close();
        print_r ($of->data[0]);
        print_r ($of->data[count($of->data)-1]);
        echo count($of->data);
    }




}
