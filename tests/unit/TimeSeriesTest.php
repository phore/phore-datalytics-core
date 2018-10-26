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
        $ts = new TimeSeries(10,15, true);
        $ts->setOutputFormat($this->outputFormat = new ArrayOutputFormat());
        $ts->define("col1", new SumAggregator());
        return $ts;
    }

    protected function _createTs() : TimeSeries
    {
        $ts = new TimeSeries(10,15);
        $ts->setOutputFormat($this->outputFormat = new ArrayOutputFormat());
        $ts->define("col1", new SumAggregator());
        return $ts;
    }

    public function testOutputFlushedWithStartTimestamp()
    {
        $ts = $this->_createTs();

        $ts->push(10, "col1", 1);
        $ts->push(10.5, "col1", 2);

        print_r($this->outputFormat->data);
        //$this->assertEmpty($this->outputFormat->data);

        $ts->push(11, "col1", 4);
        $this->assertArrayHasKey("10", $this->outputFormat->data);

        $ts->close();
        print_r($this->outputFormat->data);
        $this->assertArrayHasKey("11", $this->outputFormat->data);
    }


    public function testFillEmptyFalse()
    {
        /*
        $ts = $this->_createTs();
        $ts->setFillEmpty(false);
        $ts->define("col1",new SumAggregator());
        $ts->push(10, "col1", 1);
        $ts->push(11, "col1", 2);
        $ts->push(12, "col1", 3);
        $ts->close(13);
        //$this->assertEquals(1 , $this->outputFormat->data["10"]["col1"]);
        //$this->assertEquals(2 , $this->outputFormat->data["11"]["col1"]);
        //$this->assertEquals(3 , $this->outputFormat->data["12"]["col1"]);
        //$this->assertEquals( "", $this->outputFormat->data["13"]["col1"]);

        //print_r($this->outputFormat->data["10"]["col1"]);
        print_r($this->outputFormat->data);
        //$this->assertTrue(false);*/
    }

    public function testFillAfter()
    {

    }

    public function testFillBefore()
    {

    }

    public function testFillBetween()
    {

    }
}
