<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 18.10.18
 * Time: 11:35
 */

namespace Test;


use Exception;
use InvalidArgumentException;
use Phore\Datalytics\Core\Aggregator\FirstAggregator;
use Phore\Datalytics\Core\Aggregator\SumAggregator;
use Phore\Datalytics\Core\OutputFormat\ArrayOutputFormat;
use Phore\Datalytics\Core\TimeSeries;
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

    protected function _createTsWithIgnoreErrors() : TimeSeries
    {
        $ts = new TimeSeries(10,14, false, 1, true);
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

    protected function _createShortTs(): TimeSeries
    {
        $ts = new TimeSeries(10,11);
        $ts->setOutputFormat($this->outputFormat = new ArrayOutputFormat());
        $ts->define("col1", new SumAggregator());
        return $ts;
    }

    public function testOutputFlushedWithStartTimestampFillEmptyFalse(): void
    {
        $ts = $this->_createTs();
        $ts->push(10.1, "col1", 1);
        $ts->push(10.5, "col1", 2);
        $this->assertEmpty($this->outputFormat->data);
        $ts->push(11.1, "col1", 4);

        $this->assertCount(1, $this->outputFormat->data);
        $this->assertEquals(10, $this->outputFormat->data[0]["ts"]);
        $ts->close();

        $this->assertCount(2, $this->outputFormat->data);
    }


    public function testFillAfter(): void
    {
        $ts = $this->_createTsWithFillEmpty();
        $ts->push(10, "col1", 1);
        $ts->push(10.5, "col1", 2);
        $ts->push(11, "col1", 4);
        $ts->close();

        $this->assertEquals(3 , $this->outputFormat->data[0]["col1"]);
        $this->assertEquals(4 , $this->outputFormat->data[1]["col1"]);
        $this->assertEquals("" , $this->outputFormat->data[2]["col1"]);
        $this->assertEquals("" , $this->outputFormat->data[3]["col1"]);
    }


    public function testFillBefore(): void
    {
        $ts = $this->_createTsWithFillEmpty();
        $ts->push(11.1, "col1", 1);
        $ts->push(11.5, "col1", 2);
        $ts->push(12.1, "col1", 4);
        $ts->push(12.1, "col1", 5);
        $ts->push(13.1, "col1", 4);
        $ts->close();

        $this->assertEquals(10, $this->outputFormat->data[0]["ts"]);
        $this->assertEquals("" , $this->outputFormat->data[0]["col1"]);
        $this->assertEquals(3 , $this->outputFormat->data[1]["col1"]);
        $this->assertEquals(9 , $this->outputFormat->data[2]["col1"]);
        $this->assertEquals(4 , $this->outputFormat->data[3]["col1"]);
    }

    public function testFillBetween(): void
    {
        $ts = $this->_createTsWithFillEmpty();
        $ts->push(10.1, "col1", 1);
        $ts->push(12.1, "col1", 4);
        $ts->push(13.1, "col1", 4);
        $ts->close();
        $this->assertEquals(1 , $this->outputFormat->data[0]["col1"]);
        $this->assertEquals("" , $this->outputFormat->data[1]["col1"]);
        $this->assertEquals(4 , $this->outputFormat->data[2]["col1"]);
        $this->assertEquals(4 , $this->outputFormat->data[3]["col1"]);
    }

    public function testException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Timestamp not in chronological order");
        $ts = $this->_createTsWithFillEmpty();
        $ts->push(12.1, "col1", 4);
        $ts->push(10.1, "col1", 1);
        $ts->push(13.1, "col1", 4);
    }

    public function testIgnoreErrors(): void
    {
        $ts = $this->_createTsWithIgnoreErrors();
        $ts->push(12.1, "col1", 4);
        $ts->push(10.1, "col1", 1);
        $ts->push(13.1, "col1", 4);
        $ts->close();
        $this->assertCount(2, $this->outputFormat->data);
    }

    public function testTsEqualEndTs(): void
    {
        $ts = $this->_createShortTs();
        $ts->push(10.1, "col1", 4);
        $ts->push(11, "col1", 4);
        $ts->close();
        $this->assertCount(1, $this->outputFormat->data);
    }

    public function testTsEqualEndTsWithFillEmpty(): void
    {
        $ts = $this->_createTsWithFillEmpty();
        $ts->push(10.1, "col1", 4);
        $ts->push(11.1, "col1", 4);
        $ts->push(14.1, "col1", 4);
        $ts->close();
        $this->assertArrayNotHasKey(14, $this->outputFormat->data);
    }

    public function testTsEqualStartTs(): void
    {
        $ts = $this->_createShortTs();
        $ts->push(10.1, "col1", 4);
        $ts->close();
        $this->assertEquals(10, $this->outputFormat->data[0]["ts"]);
    }

    public function testSampleIntervalZeroWillOutputUnsampledData(): void
    {
        $ts = new TimeSeries(10, 11, false, 0);
        $ts->define("a", new FirstAggregator());
        $ts->setOutputFormat($aof = new ArrayOutputFormat());
        $ts->push(10.0, "a", 1);
        $ts->push(10.1, "a", 1);
        $ts->close();
        $this->assertCount(2, $aof->data);
        $this->assertEquals(10, $aof->data[0]["ts"]);
        $this->assertEquals(10.1, $aof->data[1]["ts"]);
    }

    public function testSampleIntervalSmallerOneAndGreaterZero(): void
    {
        $ts = new TimeSeries(10, 11, false, 0.001);
        $ts->define("a", new FirstAggregator());
        $ts->setOutputFormat($aof = new ArrayOutputFormat());
        $ts->push(10.0011, "a", 1);
        $ts->push(10.002, "a", 1);
        $ts->push(10.003, "a", 1);
        $ts->push(10.0499, "a", 1);
        $ts->close();
        $this->assertCount(4, $aof->data);
        $this->assertEquals(10.001, $aof->data[0]["ts"]);
        $this->assertEquals(10.002, $aof->data[1]["ts"]);
        $this->assertEquals(10.003, $aof->data[2]["ts"]);
        $this->assertEquals(10.049, $aof->data[3]["ts"]);
    }

    public function testShiftOne(): void
    {
        $ts = new TimeSeries(1560384010, 1560384012, false, 0.001);
        $ts->define("a", new FirstAggregator());
        $ts->setOutputFormat($aof = new ArrayOutputFormat());
        $ts->push(1560384010.924, "a", 1);
        $ts->push(1560384011.0242, "a", 1);
        $ts->push(1560384011.0242, "a", 1);
        $ts->close();
        $this->assertCount(2, $aof->data);
        $this->assertEquals(1560384010.924, $aof->data[0]["ts"]);
        $this->assertEquals(1560384011.024, $aof->data[1]["ts"]);
    }

    public function testHeaderIsSentWithNoData(): void
    {
        $ts = $this->_createTsWithFillEmpty();
        $ts->close();
        $this->assertTrue($this->outputFormat->isClosed);

        $this->assertEquals(10, $this->outputFormat->data[0]["ts"]);
        $this->assertEquals(13, $this->outputFormat->data[count($this->outputFormat->data) - 1]["ts"]);
    }

    public function testExceptionSignalNotInTimeSeries(): void
    {
        $ts = $this->_createTs();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Signal 'a' not defined in TimeSeries");
        $ts->push(10, "a", 1);
    }

    public function testMultipleTsOnCurrentFrameEnd(): void
    {
        $ts = new TimeSeries(20, 30, false, 0);
        $ts->define("col1", new SumAggregator());
        $ts->define("col2", new SumAggregator());
        $ts->setOutputFormat($aof = new ArrayOutputFormat());
        $ts->push(20.00, "col1", 1);
        $ts->push(20.99999, "col1", 7);
        $ts->push(20.99999, "col2", 9);
        $ts->push(21, "col1", 3);
        $ts->close();
        $this->assertCount(4, $aof->data);
        $this->assertEquals(20, $aof->data[0]["ts"]);
        $this->assertEquals(20.99999, $aof->data[1]["ts"]);
        $this->assertEquals(20.99999, $aof->data[2]["ts"]);
    }

}
