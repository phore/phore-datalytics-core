<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 12.11.18
 * Time: 16:51
 */

namespace Test;


use Phore\Datalytics\Core\Aggregator\FirstAggregator;
use Phore\Datalytics\Core\DataMerge\DataMergeChannel;
use Phore\Datalytics\Core\DataMerge\DataMerger;
use Phore\Datalytics\Core\OutputFormat\ArrayOutputFormat;
use Phore\Datalytics\Core\TimeSeries;
use PHPUnit\Framework\TestCase;

class DataMergerTest extends TestCase
{


    public function testDataMergeWithMultipleDataSources(): void
    {

        $merger = new DataMerger();

        // Add first input channel
        $merger->addInputChannel(new DataMergeChannel(function () {
            static $index = 0;
            $data = [
                0 => [10, ["col1" => "val1"]],
                1 => [12, ["col1" => "val2"]]
            ];
            return $data[$index++] ?? null;
        }));

        // Set second input channel
        $merger->addInputChannel(new DataMergeChannel(function () {
            static $index = 0;
            $data = [
                0 => [9, ["col2" => "val1"]],
                1 => [11, ["col2" => "val2"]],
                2 => [12, ["col2" => "val3"]]
            ];
            return $data[$index++] ?? null;
        }));

        // Set the writer function
        $ret = [];
        $merger->setWriter(function ($data) use (&$ret) {
            $ts = $data[0];
            if ( ! isset ($ret[$ts]))
                $ret[$ts] = [];
            $ret[$ts] = array_merge($ret[$ts], $data[1]);
        });

        // Run the merger
        $merger->run();

        print_r ($ret);

        $expected = [
            9 => ["col2" => "val1"],
            10 => ["col1" => "val1"],
            11 => ["col2" => "val2"],
            12 => ["col1" => "val2", "col2" => "val3"]
        ];
        $this->assertEquals($expected, $ret);
    }


}
