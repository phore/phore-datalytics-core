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
    public function createOutputFormat(string $formatName = "csv", string $eof = "false", FileStream $res = null, string $delimiter = "\t") : OutputFormat
    {
        switch ($formatName){
            case "csv":
                return new CsvOutputFormat($res, $delimiter, $eof);
            case "csvevt":
                return new CsvEventOutputFormat($res, $delimiter, $eof);
            case "json":
                return new JsonOutputFormat($res, $delimiter, $eof);
            default:
                return new CsvOutputFormat($res, $delimiter, $eof);
        }
    }
}
