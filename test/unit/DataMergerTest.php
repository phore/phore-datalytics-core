<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 12.11.18
 * Time: 16:51
 */

namespace Test;


use Phore\Datalytics\Core\Aggregator\FirstAggregator;
use Phore\Datalytics\Core\DataMerge\DataMerger;
use Phore\Datalytics\Core\OutputFormat\ArrayOutputFormat;
use Phore\Datalytics\Core\TimeSeries;
use PHPUnit\Framework\TestCase;

class DataMergerTest extends TestCase
{


    public function testDataMergeWithMultipleDataSources(): void
    {

        $ts = new TimeSeries(10, 20, false, 1);
        $ts->setOutputFormat($of = new ArrayOutputFormat());
        $ts->define("a", new FirstAggregator());
        $ts->define("b", new FirstAggregator());
        $ms = new DataMerger($ts);

        $c1 = $ms->getInputChannel();
        $c2 = $ms->getInputChannel();

        $c1->push(10, ["a" => 1]);
        $this->assertCount(0, $of->data);

        $c2->push(11, ["b" => 2]);
        $c1->push(11, ["a" => 2]);

        $this->assertCount(1, $of->data);

        $c1->close();
        $c2->close();

        $this->assertCount(2, $of->data);

    }


}
