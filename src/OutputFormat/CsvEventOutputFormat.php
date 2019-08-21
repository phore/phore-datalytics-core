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

    private $filename ="default_filename";
    private $outputHeandler;
    private $delimiter;
    private $header = [];
    private $eof = "false";

    public function __construct(FileStream $res = null, string $delimiter = "\t", string $eof = "false")
    {
        if ($res === null)
            $res = phore_file("php://output")->fopen("w");
        $this->outputHeandler = $res;
        $this->delimiter = $delimiter;
        $this->eof = $eof;
    }

    private function _ensureFooterSend()
    {
        if($this->eof !== "true"){
            return;
        }
        $this->outputHeandler->fputcsv(array(0 => "eof", 1 => "eof", 2 => "eof"), $this->delimiter);
    }

    public function mapName(string $signalName, string $headerAlias = null)
    {
        if($headerAlias === null)
            $headerAlias = $signalName;
        $this->header[$signalName] = $headerAlias;
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

    public function setFilename(string $filename)
    {
        $this->filename = $filename;
    }

    public function sendHttpHeaders()
    {
        header("Content-type: text/csv; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"{$this->filename}.csv\"");
    }

    public function close()
    {
        $this->_ensureFooterSend();
        $this->outputHeandler->fclose();
    }
}
