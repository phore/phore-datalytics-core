<?php
/**
 * Created by PhpStorm.
 * User: matthias
 * Date: 16.10.18
 * Time: 16:06
 */

namespace Phore\Datalytics\Core\OutputFormat;


use InvalidArgumentException;
use Phore\FileSystem\FileStream;

class CsvOutputFormat implements OutputFormat
{
    private $header = [];
    private $headerSend = false;
    private $outputHandler;
    private $delimiter;
    private $eof;

    public function __construct(FileStream $res = null, string $delimiter = "\t", bool $eof = false, bool $skipHeader = false)
    {
        if ($res === null) {
            $res = phore_file('php://output')->fopen('w');
        }
        if ($skipHeader) {
            $this->headerSend = true;
        }
        $this->outputHandler = $res;
        $this->delimiter = $delimiter;
        $this->eof = $eof;

    }

    private function _ensureHeaderSend(): void
    {
        if ($this->headerSend === true) {
            return;
        }
        $arr = ['ts'];
        foreach ($this->header as $signalName => $alias) {
            $arr[] = $alias;
        }

        $this->outputHandler->fputcsv($arr, $this->delimiter);
        $this->headerSend = true;
    }

    private function _ensureFooterSend(): void
    {
        if (!$this->eof) {
            return;
        }
        $headerLength = count($this->header);
        for ($i = 0; $i <= $headerLength; $i++) {
            $arr[] = 'eof';
        }
        $this->outputHandler->fputcsv($arr, $this->delimiter);
    }

    public function mapName(string $signalName, string $headerAlias = null): void
    {
        if ($headerAlias === null) {
            $headerAlias = $signalName;
        }
        $this->header[$signalName] = $headerAlias;
    }

    public function sendData(float $ts, array $data): bool
    {

        $this->_ensureHeaderSend();
        if (empty($this->header)) {
            throw new InvalidArgumentException('No SignalNames set');
        }
        foreach ($this->header as $signalName => $alias) {
            if (!array_key_exists($signalName, $data)) {
                throw new InvalidArgumentException("Data missing for SignalName: '$signalName'");
            }
        }
        array_unshift($data, $ts);


        $this->outputHandler->fputcsv($data, $this->delimiter);
        return true;
    }

    public function sendHttpHeaders(): void
    {
        header('Content-type: text/csv; charset=utf-8');
    }

    public function close(): void
    {
        $this->_ensureHeaderSend();
        $this->_ensureFooterSend();
        $this->outputHandler->fclose();
    }
}
