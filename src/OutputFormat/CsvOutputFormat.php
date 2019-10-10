<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 16.10.18
 * Time: 16:06
 */

namespace Phore\Datalytics\Core\OutputFormat;


use Phore\FileSystem\FileStream;

class CsvOutputFormat implements OutputFormat
{
    private $header = [];
    private $headerSend = false;
    private $filename = null;
    private $outputHeandler;
    private $delimiter;
    private $eof = false;

    public function __construct(FileStream $res = null, string $delimiter = "\t", bool $eof = false, bool $skipHeader = false)
    {
        if ($res === null)
            $res = phore_file("php://output")->fopen("w");
        if($skipHeader)
            $this->headerSend = true;
        $this->outputHeandler = $res;
        $this->delimiter = $delimiter;
        $this->eof = $eof;

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

    private function _ensureFooterSend()
    {
        if(!$this->eof) {
            return;
        }
        $headerLength = count($this->header);
        for($i = 0; $i <= $headerLength; $i++){
            $arr[] = "eof";
        }
        $this->outputHeandler->fputcsv($arr, $this->delimiter);
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
            if(! array_key_exists($signalName, $data))
                throw new \InvalidArgumentException("Data missing for SignalName: '$signalName'");
            $arr[] = $data[$signalName];
        }
        if(empty($this->header))
            throw new \InvalidArgumentException("No SignalNames set");

        $this->outputHeandler->fputcsv($arr, $this->delimiter);
        return true;
    }

    public function setFilename(string $filename)
    {
        $this->filename = $filename;
    }

    public function sendHttpHeaders()
    {
        header("Content-type: text/csv; charset=utf-8");
        if($this->filename !== null){
            header("Content-Disposition: attachment; filename=\"{$this->filename}\"");
        }

    }

    public function close()
    {
        $this->_ensureHeaderSend();
        $this->_ensureFooterSend();
        $this->outputHeandler->fclose();
    }
}
