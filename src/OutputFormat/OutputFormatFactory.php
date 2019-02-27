<?php
/**
 * Created by PhpStorm.
 * User: oem
 * Date: 24.10.18
 * Time: 13:55
 */

namespace Phore\Datalytics\Core\OutputFormat;


use Phore\FileSystem\FileStream;

class OutputFormatFactory
{
    public function createOutputFormat(string $formatName = "csv", FileStream $res = null, string $delimiter = "\t") : OutputFormat
    {
        switch ($formatName){
            case "csv":
                return new CsvOutputFormat($res, $delimiter);
            case "csvevt":
                return new CsvEventOutputFormat($res, $delimiter);
            case "json":
                return new JsonOutputFormat($res, $delimiter);
            case "tbf":
                return new TbfOutputFormat($res);
                
            default:
                return new CsvOutputFormat($res, $delimiter);
        }
    }
}
