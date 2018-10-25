<?php
/**
 * Created by PhpStorm.
 * User: oem
 * Date: 23.10.18
 * Time: 14:53
 */

namespace Phore\Datalytics\Core\OutputFormat;


use Phore\FileSystem\FileStream;

class CsvEventOutputFormat implements OutputFormat
{

    private $outputHeandler;
    private$delimiter;

    public function __construct(FileStream $res = null, string $delimiter = "\t")
    {
        if ($res === null)
            $res = phore_file("php://output")->fopen("w");
        $this->outputHeandler = $res;
        $this->delimiter = $delimiter;
    }

    public function sendData(float $ts, array $data)
    {
        $arr[0] = $ts;
        foreach ($data as $key => $item) {
            $arr[1] = $key;
            $arr[2] = $item;
            $this->outputHeandler->fputcsv($arr, $this->delimiter);
        }
        return true;
    }

    public function close()
    {
        $this->outputHeandler->fclose();
    }
}