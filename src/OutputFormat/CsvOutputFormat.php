<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 16.10.18
 * Time: 16:06
 */

namespace Phore\Datalytics\Core\OutputFormat;


use Phore\FileSystem\FileStream;
use Psr\Http\Message\StreamInterface;

class CsvOutputFormat implements OutputFormat
{
    private $header = [];
    private $headerSend = false;
    private $outputHeandler;
    private$delimiter;

    public function __construct(FileStream $res = null, string $delimiter = "\t")
    {
        if ($res === null)
            $res = phore_file("php://output")->fopen("w");
        $this->outputHeandler = $res;
        $this->delimiter = $delimiter;
    }

    private function _ensureHeaderSend()
    {
        if($this->headerSend === true)
            return;
        $arr = ["ts"];
        foreach ($this->header as $signalName => $alias) {
            $arr[] = $alias;
        }
        $this->outputHeandler->fputcsv($arr,$this->delimiter);
        $this->headerSend = true;
    }

    public function mapName(string $signalName, string $headerAlias = null)
    {
        if($headerAlias === null)
            $headerAlias = $signalName;
        $this->header[$signalName] = $headerAlias;
    }

    public function sendData(float $ts, array $data)
    {

        $this->_ensureHeaderSend();
        $arr = [$ts];
        foreach ($this->header as $signalName => $alias) {
            if(!isset($data[$signalName]))
                throw new \InvalidArgumentException("Data missing for SignalName: '$signalName'");
            $arr[] = $data[$signalName];
        }
        $this->outputHeandler->fputcsv($arr, $this->delimiter);
        return true;
    }

    public function close()
    {
        $this->_ensureHeaderSend();
        $this->outputHeandler->fclose();
    }
}
